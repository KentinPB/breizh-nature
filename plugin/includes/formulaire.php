<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * 1. Rendu HTML du formulaire avec contrôle des disponibilités
 */
function bnr_render_reservation_form() {
    global $wpdb;
    $post_id    = get_the_ID();
    $table_name = $wpdb->prefix . 'reservations';

    // Récupération des contraintes de l'activité
    $date_limite = get_post_meta( $post_id, '_activite_date_limite', true );
    $places_max  = intval( get_post_meta( $post_id, '_activite_places', true ) );

    // Calcul des places déjà validées
    $places_reservees = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT SUM(participants) FROM $table_name WHERE activite_id = %d AND statut = 'Acceptée'",
            $post_id
    ) );

    $places_restantes = $places_max > 0 ? max( 0, $places_max - $places_reservees ) : 0;
    $aujourdhui       = date( 'Y-m-d' );

    ob_start();

    // Messages de confirmation ou d'erreur
    if ( isset( $_GET['reservation'] ) ) {
        switch ( $_GET['reservation'] ) {
            case 'success':
                echo '<p style="color: green; font-weight: bold;">✅ Votre demande de réservation a bien été envoyée.</p>';
                break;
            case 'complet':
                echo '<p style="color: red; font-weight: bold;">❌ Impossible de réserver : le nombre de places restantes est insuffisant.</p>';
                break;
            case 'expire':
                echo '<p style="color: red; font-weight: bold;">❌ Les inscriptions pour cette activité sont closes.</p>';
                break;
            case 'error':
            default:
                echo '<p style="color: red; font-weight: bold;">❌ Une erreur est survenue lors de l\'envoi de votre demande.</p>';
                break;
        }
    }

    // Blocage 1 : Date limite dépassée
    if ( ! empty( $date_limite ) && $aujourdhui > $date_limite ) {
        echo '<div style="padding: 10px; background: #fee; border: 1px solid #fcc; color: #c00;">';
        echo '<strong>Inscriptions closes :</strong> la date limite du ' . esc_html( date( 'd/m/Y', strtotime( $date_limite ) ) ) . ' est dépassée.';
        echo '</div>';
        return ob_get_clean();
    }

    // Blocage 2 : Activité complète
    if ( $places_max > 0 && $places_restantes <= 0 ) {
        echo '<div style="padding: 10px; background: #fee; border: 1px solid #fcc; color: #c00;">';
        echo '<strong>Complet :</strong> cette activité n\'a plus de places disponibles.';
        echo '</div>';
        return ob_get_clean();
    }

    // Formulaire accessible
    ?>
    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" class="bnr-reservation-form">
        <?php wp_nonce_field( 'bnr_submit_reservation', 'bnr_reservation_nonce' ); ?>
        <input type="hidden" name="action" value="bnr_process_reservation">
        <input type="hidden" name="activite_id" value="<?php echo esc_attr( $post_id ); ?>">

        <p><em>Places restantes : <strong><?php echo esc_html( $places_restantes ); ?></strong></em></p>

        <div>
            <label for="bnr_nom">Nom *</label><br>
            <input type="text" id="bnr_nom" name="nom" required>
        </div>
        <br>
        <div>
            <label for="bnr_prenom">Prénom *</label><br>
            <input type="text" id="bnr_prenom" name="prenom" required>
        </div>
        <br>
        <div>
            <label for="bnr_email">Adresse e-mail *</label><br>
            <input type="email" id="bnr_email" name="email" required>
        </div>
        <br>
        <div>
            <label for="bnr_telephone">Téléphone *</label><br>
            <input type="tel" id="bnr_telephone" name="telephone" required>
        </div>
        <br>
        <div>
            <label for="bnr_participants">Nombre de participants * (max. <?php echo esc_attr( $places_restantes ); ?>)</label><br>
            <input type="number" id="bnr_participants" name="participants" min="1" max="<?php echo esc_attr( $places_restantes ); ?>" value="1" required>
        </div>
        <br>
        <div>
            <label for="bnr_commentaire">Commentaire</label><br>
            <textarea id="bnr_commentaire" name="commentaire" rows="4"></textarea>
        </div>
        <br>
        <button type="submit" class="button">Envoyer ma demande</button>
    </form>
    <?php

    return ob_get_clean();
}
add_shortcode( 'bnr_reservation', 'bnr_render_reservation_form' );


/**
 * 2. Traitement sécurisé et vérification côté serveur
 */
function bnr_process_reservation() {
    // Sécurité CSRF
    if ( ! isset( $_POST['bnr_reservation_nonce'] ) || ! wp_verify_nonce( $_POST['bnr_reservation_nonce'], 'bnr_submit_reservation' ) ) {
        wp_die( 'Erreur de sécurité : jeton invalide.' );
    }

    $activite_id  = isset( $_POST['activite_id'] ) ? intval( $_POST['activite_id'] ) : 0;
    $nom          = isset( $_POST['nom'] ) ? sanitize_text_field( $_POST['nom'] ) : '';
    $prenom       = isset( $_POST['prenom'] ) ? sanitize_text_field( $_POST['prenom'] ) : '';
    $email        = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $telephone    = isset( $_POST['telephone'] ) ? sanitize_text_field( $_POST['telephone'] ) : '';
    $participants = isset( $_POST['participants'] ) ? intval( $_POST['participants'] ) : 1;
    $commentaire  = isset( $_POST['commentaire'] ) ? sanitize_textarea_field( $_POST['commentaire'] ) : '';

    if ( empty( $nom ) || empty( $prenom ) || ! is_email( $email ) || $participants < 1 || ! $activite_id ) {
        wp_redirect( add_query_arg( 'reservation', 'error', wp_get_referer() ) );
        exit;
    }

    // Vérification côté serveur des règles métier (indispensable même si le HTML bloque)
    $date_limite = get_post_meta( $activite_id, '_activite_date_limite', true );
    $places_max  = intval( get_post_meta( $activite_id, '_activite_places', true ) );

    if ( ! empty( $date_limite ) && date( 'Y-m-d' ) > $date_limite ) {
        wp_redirect( add_query_arg( 'reservation', 'expire', wp_get_referer() ) );
        exit;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';

    $places_reservees = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT SUM(participants) FROM $table_name WHERE activite_id = %d AND statut = 'Acceptée'",
            $activite_id
    ) );

    if ( $places_max > 0 && ( $places_reservees + $participants ) > $places_max ) {
        wp_redirect( add_query_arg( 'reservation', 'complet', wp_get_referer() ) );
        exit;
    }

    // Insertion SQL sécurisée
    $inserted = $wpdb->insert(
            $table_name,
            array(
                    'activite_id'  => $activite_id,
                    'nom'          => $nom,
                    'prenom'       => $prenom,
                    'email'        => $email,
                    'telephone'    => $telephone,
                    'participants' => $participants,
                    'commentaire'  => $commentaire,
                    'statut'       => 'En attente'
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
    );

    if ( $inserted ) {
        wp_redirect( add_query_arg( 'reservation', 'success', wp_get_referer() ) );
    } else {
        wp_redirect( add_query_arg( 'reservation', 'error', wp_get_referer() ) );
    }
    exit;
}
add_action( 'admin_post_nopriv_bnr_process_reservation', 'bnr_process_reservation' );
add_action( 'admin_post_bnr_process_reservation', 'bnr_process_reservation' );