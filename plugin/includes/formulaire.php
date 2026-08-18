<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * 1. SHORTCODE : Formulaire public de réservation [bnr_reservation]
 */
function bnr_render_reservation_form() {
    global $wpdb;
    $post_id    = get_the_ID();
    $table_name = $wpdb->prefix . 'reservations';

    $date_limite = get_post_meta( $post_id, '_activite_date_limite', true );
    $places_max  = intval( get_post_meta( $post_id, '_activite_places', true ) );

    $places_reservees = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT SUM(participants) FROM $table_name WHERE activite_id = %d AND statut = 'Acceptée'",
            $post_id
    ) );

    $places_restantes = $places_max > 0 ? max( 0, $places_max - $places_reservees ) : 0;
    $aujourdhui       = date( 'Y-m-d' );

    ob_start();

    // Messages de statut
    if ( isset( $_GET['reservation'] ) ) {
        if ( $_GET['reservation'] == 'success' ) echo '<div style="padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 15px;">✅ Votre demande a bien été envoyée.</div>';
        elseif ( $_GET['reservation'] == 'complet' ) echo '<div style="padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 15px;">❌ Impossible de réserver : capacité atteinte.</div>';
    }

    // Blocages métier (Date dépassée ou Plus de places)
    if ( ! empty( $date_limite ) && $aujourdhui > $date_limite ) {
        echo '<div style="padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;"><strong>Inscriptions closes :</strong> la date limite est dépassée.</div>';
        return ob_get_clean();
    }
    if ( $places_max > 0 && $places_restantes <= 0 ) {
        echo '<div style="padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;"><strong>Activité complète :</strong> il n\'y a plus de places disponibles.</div>';
        return ob_get_clean();
    }

    // Le formulaire de réservation (Accessible à tous)
    ?>
    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" class="bnr-reservation-form">
        <?php wp_nonce_field( 'bnr_submit_reservation', 'bnr_reservation_nonce' ); ?>
        <input type="hidden" name="action" value="bnr_process_reservation">
        <input type="hidden" name="activite_id" value="<?php echo esc_attr( $post_id ); ?>">

        <p><em>Places restantes : <strong><?php echo esc_html( $places_restantes ); ?></strong></em></p>

        <div style="display: flex; gap: 15px; margin-bottom: 10px;">
            <div style="flex: 1;"><label>Nom *</label><br><input type="text" name="nom" required style="width:100%;"></div>
            <div style="flex: 1;"><label>Prénom *</label><br><input type="text" name="prenom" required style="width:100%;"></div>
        </div>
        <div style="margin-bottom: 10px;">
            <label>Adresse e-mail *</label><br>
            <input type="email" name="email" required style="width:100%;">
        </div>
        <div style="margin-bottom: 10px;">
            <label>Téléphone *</label><br>
            <input type="tel" name="telephone" required style="width:100%;">
        </div>
        <div style="margin-bottom: 10px;">
            <label>Nombre de participants * (max. <?php echo esc_attr( $places_restantes ); ?>)</label><br>
            <input type="number" name="participants" min="1" max="<?php echo esc_attr( $places_restantes ); ?>" value="1" required>
        </div>
        <div style="margin-bottom: 15px;">
            <label>Commentaire</label><br>
            <textarea name="commentaire" rows="3" style="width:100%;"></textarea>
        </div>
        <button type="submit" class="button">Envoyer ma demande</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'bnr_reservation', 'bnr_render_reservation_form' );


/**
 * 2. Traitement du formulaire de réservation
 */
function bnr_process_reservation() {
    if ( ! isset( $_POST['bnr_reservation_nonce'] ) || ! wp_verify_nonce( $_POST['bnr_reservation_nonce'], 'bnr_submit_reservation' ) ) {
        wp_die( 'Erreur de sécurité.' );
    }

    $activite_id  = isset( $_POST['activite_id'] ) ? intval( $_POST['activite_id'] ) : 0;
    $nom          = sanitize_text_field( $_POST['nom'] );
    $prenom       = sanitize_text_field( $_POST['prenom'] );
    $email        = sanitize_email( $_POST['email'] );
    $telephone    = sanitize_text_field( $_POST['telephone'] );
    $participants = intval( $_POST['participants'] );
    $commentaire  = sanitize_textarea_field( $_POST['commentaire'] );

    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';

    // Vérification de la disponibilité avant insertion
    $places_reservees = (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(participants) FROM $table_name WHERE activite_id = %d AND statut = 'Acceptée'", $activite_id ) );
    $places_max = intval( get_post_meta( $activite_id, '_activite_places', true ) );

    if ( $places_max > 0 && ( $places_reservees + $participants ) > $places_max ) {
        wp_redirect( add_query_arg( 'reservation', 'complet', wp_get_referer() ) );
        exit;
    }

    $wpdb->insert(
            $table_name,
            array( 'activite_id' => $activite_id, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'telephone' => $telephone, 'participants' => $participants, 'commentaire' => $commentaire, 'statut' => 'En attente' ),
            array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
    );

    wp_redirect( add_query_arg( 'reservation', 'success', wp_get_referer() ) );
    exit;
}
add_action( 'admin_post_nopriv_bnr_process_reservation', 'bnr_process_reservation' );
add_action( 'admin_post_bnr_process_reservation', 'bnr_process_reservation' );


/**
 * 3. SHORTCODE : Portail de suivi public pour les visiteurs [bnr_mes_sorties]
 */
function bnr_render_mes_sorties_form() {
    ob_start();

    // Message si le visiteur vient de se désister
    if ( isset( $_GET['annulation'] ) && $_GET['annulation'] === 'success' ) {
        echo '<div style="padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 20px; border-radius: 4px;">✅ Votre désistement a bien été pris en compte.</div>';
    }

    $email_recherche = isset( $_POST['suivi_email'] ) ? sanitize_email( $_POST['suivi_email'] ) : '';
    ?>
    <div style="background: #f9f9f9; padding: 20px; border: 1px solid #eee; border-radius: 5px; margin-bottom: 30px;">
        <h3 style="margin-top: 0;">Retrouver mes inscriptions</h3>
        <form method="POST" action="">
            <label>Saisissez l'adresse e-mail utilisée lors de votre réservation :</label><br>
            <input type="email" name="suivi_email" value="<?php echo esc_attr( $email_recherche ); ?>" required style="margin-top: 10px; padding: 5px; width: 100%; max-width: 300px;">
            <button type="submit" class="button" style="margin-top: 10px;">Voir mes sorties</button>
        </form>
    </div>
    <?php

    if ( ! empty( $email_recherche ) && is_email( $email_recherche ) ) {
        global $wpdb;
        $table = $wpdb->prefix . 'reservations';

        // On récupère toutes les réservations (sauf celles annulées par le client lui-même)
        $reservations = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $table WHERE email = %s AND statut != 'Annulée' ORDER BY date_creation DESC",
                $email_recherche
        ) );

        if ( empty( $reservations ) ) {
            echo '<p>Aucune inscription active n\'a été trouvée pour l\'adresse <strong>' . esc_html( $email_recherche ) . '</strong>.</p>';
        } else {
            echo '<h3>Vos activités :</h3>';
            echo '<ul style="list-style:none; padding:0;">';

            foreach ( $reservations as $resa ) {
                $date_activite = get_post_meta( $resa->activite_id, '_activite_date', true );
                $titre         = get_the_title( $resa->activite_id );
                $aujourdhui    = date( 'Y-m-d' );

                // Définition de la couleur de mise en valeur selon le statut
                $status_color = '#fd7e14'; // Orange : En attente
                if ( $resa->statut === 'Acceptée' ) $status_color = '#28a745'; // Vert
                if ( $resa->statut === 'Refusée' ) $status_color = '#dc3545'; // Rouge

                // La carte HTML de la sortie (avec bordure latérale dynamique)
                echo '<li style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-left: 5px solid ' . $status_color . '; border-radius: 4px; background: #fff;">';

                // Titre
                echo '<h4 style="margin-top: 0; margin-bottom: 15px;"><a href="' . get_permalink( $resa->activite_id ) . '" style="text-decoration:none; color: #333;">' . esc_html( $titre ) . '</a></h4>';

                // Informations clés bien visibles
                echo '<p style="margin: 5px 0;">📅 <strong>Date de la sortie :</strong> ' . date( 'd/m/Y', strtotime( $date_activite ) ) . '</p>';
                echo '<p style="margin: 5px 0;">👥 <strong>Places réservées :</strong> ' . esc_html( $resa->participants ) . '</p>';
                echo '<p style="margin: 5px 0 15px 0;">📌 <strong>Statut de la demande :</strong> <span style="color: ' . $status_color . '; font-weight: bold; padding: 2px 8px; background: rgba(0,0,0,0.03); border-radius: 3px;">' . esc_html( $resa->statut ) . '</span></p>';

                // LOGIQUE D'AFFICHAGE DU BOUTON D'ANNULATION
                if ( $resa->statut === 'Refusée' ) {
                    // Si refusée, pas de bouton
                    echo '<span style="display:inline-block; color: #dc3545; font-size: 13px; font-weight:bold; padding: 8px; background: #f8d7da; border-radius: 4px;">❌ Cette demande a été refusée par l\'association.</span>';
                } elseif ( $date_activite >= $aujourdhui ) {
                    // Si acceptée ou en attente et que la date n'est pas passée, on affiche le bouton
                    ?>
                    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" style="margin-top: 10px;">
                        <?php wp_nonce_field( 'bnr_cancel_public_action', 'bnr_cancel_public_nonce' ); ?>
                        <input type="hidden" name="action" value="bnr_cancel_public_reservation">
                        <input type="hidden" name="reservation_id" value="<?php echo esc_attr( $resa->id ); ?>">
                        <input type="hidden" name="email" value="<?php echo esc_attr( $email_recherche ); ?>">
                        <button type="submit" style="background: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 3px; cursor: pointer; font-size: 13px;" onclick="return confirm('Êtes-vous sûr de vouloir vous désister pour cette inscription précise ?');">Annuler mon inscription</button>
                    </form>
                    <?php
                } else {
                    // Si la date de l'activité est passée
                    echo '<span style="display:inline-block; color: #6c757d; font-size: 13px; font-weight:bold; padding: 8px; background: #e2e3e5; border-radius: 4px;">🕒 L\'activité est passée.</span>';
                }

                echo '</li>';
            }
            echo '</ul>';
        }
    }
    return ob_get_clean();
}
add_shortcode( 'bnr_mes_sorties', 'bnr_render_mes_sorties_form' );


/**
 * 4. Traitement du Désistement Public (Correction de la Clé Primaire)
 */
function bnr_cancel_public_reservation() {
    if ( ! isset( $_POST['bnr_cancel_public_nonce'] ) || ! wp_verify_nonce( $_POST['bnr_cancel_public_nonce'], 'bnr_cancel_public_action' ) ) {
        wp_die( 'Erreur de sécurité.' );
    }

    // On récupère le bon ID unique de réservation (et non plus l'activite_id)
    $reservation_id = isset( $_POST['reservation_id'] ) ? intval( $_POST['reservation_id'] ) : 0;
    $email          = sanitize_email( $_POST['email'] );

    if ( $reservation_id > 0 && is_email( $email ) ) {
        global $wpdb;
        $table = $wpdb->prefix . 'reservations';

        // On passe le statut à "Annulée" de manière ultra ciblée sur cette ligne SQL précise
        $wpdb->update(
                $table,
                array( 'statut' => 'Annulée' ),
                array( 'id' => $reservation_id, 'email' => $email ), // Double condition de sécurité
                array( '%s' ),
                array( '%d', '%s' )
        );
    }

    // On redirige vers la même page en ajoutant le message de succès
    wp_redirect( add_query_arg( 'annulation', 'success', wp_get_referer() ) );
    exit;
}
add_action( 'admin_post_nopriv_bnr_cancel_public_reservation', 'bnr_cancel_public_reservation' );
add_action( 'admin_post_bnr_cancel_public_reservation', 'bnr_cancel_public_reservation' );


/**
 * 5. SHORTCODE : Tableau de bord des réservations (Front-end) pour les administrateurs
 */
function bnr_render_admin_tracking_frontend() {
    if ( ! current_user_can( 'manage_reservations' ) ) {
        return '<p>Accès strictement réservé aux gestionnaires de l\'association.</p>';
    }

    global $wpdb;
    $table = $wpdb->prefix . 'reservations';
    $reservations = $wpdb->get_results( "SELECT * FROM $table ORDER BY date_creation DESC LIMIT 50" );

    ob_start();
    ?>
    <div class="bnr-admin-tracking">
        <h2>Tableau de bord : Suivi des réservations</h2>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px;">
            <thead>
            <tr style="background: #f1f1f1; text-align: left;">
                <th style="padding: 10px; border: 1px solid #ccc;">Activité</th>
                <th style="padding: 10px; border: 1px solid #ccc;">Inscrit</th>
                <th style="padding: 10px; border: 1px solid #ccc;">Places</th>
                <th style="padding: 10px; border: 1px solid #ccc;">Statut</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ( $reservations as $resa ) : ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc;">
                        <strong><?php echo get_the_title( $resa->activite_id ); ?></strong>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ccc;">
                        <?php echo esc_html( $resa->prenom . ' ' . $resa->nom ); ?><br>
                        <a href="mailto:<?php echo esc_attr( $resa->email ); ?>"><?php echo esc_html( $resa->email ); ?></a><br>
                        <small><?php echo esc_html( $resa->telephone ); ?></small>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ccc; text-align:center;">
                        <?php echo esc_html( $resa->participants ); ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ccc;">
                        <?php
                        $color = '#333';
                        if ( $resa->statut === 'Acceptée' ) $color = '#28a745';
                        if ( $resa->statut === 'Annulée' ) $color = '#dc3545';
                        if ( $resa->statut === 'En attente' ) $color = '#fd7e14';
                        ?>
                        <span style="font-weight:bold; color:<?php echo $color; ?>"><?php echo esc_html( $resa->statut ); ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'bnr_suivi_admin', 'bnr_render_admin_tracking_frontend' );