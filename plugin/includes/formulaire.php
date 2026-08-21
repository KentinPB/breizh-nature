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
        elseif ( $_GET['reservation'] == 'error' ) echo '<div style="padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 15px;">❌ Erreur lors de l\'envoi.</div>';
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
 * 2. Traitement sécurisé de la réservation
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

    if ( empty( $nom ) || empty( $prenom ) || ! is_email( $email ) ) {
        wp_redirect( add_query_arg( 'reservation', 'error', wp_get_referer() ) ); exit;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';

    $inserted = $wpdb->insert(
            $table_name,
            array( 'activite_id' => $activite_id, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'telephone' => $telephone, 'participants' => $participants, 'commentaire' => $commentaire, 'statut' => 'En attente' ),
            array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
    );

    if ( $inserted ) {
        $titre_activite = get_the_title( $activite_id );
        $subject = "Confirmation de réservation : " . $titre_activite;
        $message = "Bonjour $prenom,\n\nVotre demande pour l'activité '$titre_activite' a bien été enregistrée et est actuellement en attente de validation.\n\nÀ bientôt chez Breizh'Nature !";

        wp_mail( $email, $subject, $message, array('Content-Type: text/plain; charset=UTF-8') );

        wp_redirect( add_query_arg( 'reservation', 'success', wp_get_referer() ) );
    } else {
        wp_redirect( add_query_arg( 'reservation', 'error', wp_get_referer() ) );
    }
    exit;
}
add_action( 'admin_post_nopriv_bnr_process_reservation', 'bnr_process_reservation' );
add_action( 'admin_post_bnr_process_reservation', 'bnr_process_reservation' );

/**
 * 3. SHORTCODE : Formulaire "Gérer mes réservations" (Magic Link)
 */
function bnr_render_gestion_reservations_form() {
    ob_start();

    // Affichage des alertes de succès/erreur de l'annulation
    if ( isset( $_GET['annulation_statut'] ) ) {
        if ( $_GET['annulation_statut'] === 'success' ) {
            echo '<div style="background:#d4edda; color:#155724; padding:20px; margin-bottom:30px; border-radius:8px; border: 1px solid #c3e6cb; text-align:center;">';
            echo '<h3 style="margin-top:0; color:#155724;">✅ Réservation annulée avec succès</h3>';
            echo '<p style="margin-bottom:0;">Un e-mail de confirmation vient de vous être envoyé.</p>';
            echo '</div>';
        } elseif ( $_GET['annulation_statut'] === 'erreur_token' || $_GET['annulation_statut'] === 'erreur_param' ) {
            echo '<div style="background:#f8d7da; color:#721c24; padding:20px; margin-bottom:30px; border-radius:8px; border: 1px solid #f5c6cb; text-align:center;">';
            echo '<h3 style="margin-top:0; color:#721c24;">❌ Action impossible</h3>';
            echo '<p style="margin-bottom:0;">Le lien d\'annulation est invalide, a expiré, ou la réservation est déjà annulée.</p>';
            echo '</div>';
        }
    }

    if ( isset( $_POST['bnr_submit_gestion'] ) && wp_verify_nonce( $_POST['bnr_gestion_nonce'], 'bnr_action_gestion' ) ) {
        $email = sanitize_email( $_POST['email_gestion'] );
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservations';

        // Recherche des réservations "En attente" ou "Acceptée"
        $reservations = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, activite_id, statut FROM $table_name WHERE email = %s AND statut IN ('En attente', 'Acceptée')",
                $email
        ) );

        if ( ! empty( $reservations ) ) {
            $html_liens = '<ul>';
            foreach ( $reservations as $resa ) {
                // GÉNÉRATION SÉCURISÉE DU TOKEN (Expiration 15 minutes)
                $cancel_token = wp_generate_password( 32, false );
                set_transient( '_bnr_cancel_token_' . $resa->id, $cancel_token, 15 * MINUTE_IN_SECONDS );

                $url_annulation = add_query_arg(
                        array( 'action' => 'bnr_cancel_reservation', 'reservation_id' => $resa->id, 'token' => $cancel_token ),
                        admin_url( 'admin-post.php' )
                );

                $titre_activite = get_the_title( $resa->activite_id );
                $html_liens .= '<li><strong>' . esc_html( $titre_activite ) . '</strong> (' . esc_html( $resa->statut ) . ') <br> <a href="' . esc_url( $url_annulation ) . '" style="color:#dc3545; font-weight:bold;">Annuler cette activité</a></li><br>';
            }
            $html_liens .= '</ul>';

            $sujet = "Vos réservations Breizh'Nature - Gestion et Annulation";
            $headers = array('Content-Type: text/html; charset=UTF-8');

            $message_html = "<html><body style='font-family: Arial, sans-serif; color: #333;'>";
            $message_html .= "<h2 style='color:#2e7d32;'>Gérer vos réservations</h2>";
            $message_html .= "<p>Bonjour,</p>";
            $message_html .= "<p>Vous avez demandé à gérer vos réservations sur notre site. Voici vos activités en cours. Cliquez sur le lien rouge pour annuler une réservation spécifique (<strong>Attention, par mesure de sécurité, ces liens ne sont valables que 15 minutes</strong>) :</p>";
            $message_html .= $html_liens;
            $message_html .= "<p>À très bientôt,<br>L'équipe Breizh'Nature.</p>";
            $message_html .= "</body></html>";

            wp_mail( $email, $sujet, $message_html, $headers );

            echo '<div style="background:#d4edda; color:#155724; padding:15px; margin-bottom:15px; border-radius:4px; border: 1px solid #c3e6cb;">Un e-mail contenant vos liens de gestion vient de vous être envoyé. Veuillez vérifier votre boîte de réception (et vos spams).</div>';

        } else {
            echo '<div style="background:#fff3cd; color:#856404; padding:15px; margin-bottom:15px; border-radius:4px;">Si des réservations actives existent pour cet e-mail, un lien de gestion vient de vous être envoyé.</div>';
        }
    }

    ?>
    <div class="bnr-gestion-form" style="max-width: 500px; background: #f9f9f9; padding: 20px; border-radius: 8px;">
        <form method="POST" action="">
            <?php wp_nonce_field( 'bnr_action_gestion', 'bnr_gestion_nonce' ); ?>
            <p>Entrez l'adresse e-mail utilisée lors de vos réservations. Nous vous enverrons un lien sécurisé (valable 15 minutes) pour les gérer ou les annuler.</p>
            <label style="display:block; margin-bottom: 15px;">
                <strong>Votre e-mail :</strong><br>
                <input type="email" name="email_gestion" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </label>
            <button type="submit" name="bnr_submit_gestion" style="background:#2e7d32; color:white; border:none; padding:10px 20px; border-radius:4px; cursor:pointer;">Recevoir mon lien d'accès</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'bnr_gestion_reservations', 'bnr_render_gestion_reservations_form' );

/**
 * 4. Traitement sécurisé de l'annulation (Validation du Transient)
 */
function bnr_handle_cancel_reservation() {
    $req_id    = isset( $_GET['reservation_id'] ) ? intval( $_GET['reservation_id'] ) : 0;
    $req_token = isset( $_GET['token'] ) ? sanitize_text_field( $_GET['token'] ) : '';

    $url_retour = home_url( '/suivi-des-reservations/' );

    if ( $req_id && $req_token ) {
        // LECTURE DU TOKEN EPHEMERE
        $vrai_token = get_transient( '_bnr_cancel_token_' . $req_id );

        if ( $vrai_token && hash_equals( $vrai_token, $req_token ) ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'reservations';

            $reservation = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $req_id ) );

            $wpdb->update(
                    $table_name,
                    array( 'statut' => 'Annulée' ),
                    array( 'id' => $req_id ),
                    array( '%s' ),
                    array( '%d' )
            );

            // SUPPRESSION DU TOKEN APRES USAGE
            delete_transient( '_bnr_cancel_token_' . $req_id );

            if ( $reservation ) {
                $titre_activite = get_the_title( $reservation->activite_id );
                $subject = "Annulation confirmée : " . $titre_activite;
                $message = "Bonjour " . $reservation->prenom . ",\n\nNous vous confirmons que votre réservation pour l'activité '$titre_activite' a bien été annulée.\n\nEn espérant vous revoir bientôt chez Breizh'Nature !";

                wp_mail( $reservation->email, $subject, $message, array('Content-Type: text/plain; charset=UTF-8') );
            }

            wp_redirect( add_query_arg( 'annulation_statut', 'success', $url_retour ) );
            exit;
        } else {
            wp_redirect( add_query_arg( 'annulation_statut', 'erreur_token', $url_retour ) );
            exit;
        }
    } else {
        wp_redirect( add_query_arg( 'annulation_statut', 'erreur_param', $url_retour ) );
        exit;
    }
}
add_action( 'admin_post_nopriv_bnr_cancel_reservation', 'bnr_handle_cancel_reservation' );
add_action( 'admin_post_bnr_cancel_reservation', 'bnr_handle_cancel_reservation' );