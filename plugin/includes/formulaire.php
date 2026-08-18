<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Rendu HTML du formulaire
function bnr_render_reservation_form() {
    ob_start();
    if ( isset( $_GET['reservation'] ) ) {
        if ( $_GET['reservation'] === 'success' ) echo '<p style="color: green; font-weight: bold;">✅ Demande envoyée !</p>';
        elseif ( $_GET['reservation'] === 'error' ) echo '<p style="color: red; font-weight: bold;">❌ Erreur lors de l\'envoi.</p>';
    }
    ?>
    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
        <?php wp_nonce_field( 'bnr_submit_reservation', 'bnr_reservation_nonce' ); ?>
        <input type="hidden" name="action" value="bnr_process_reservation">
        <input type="hidden" name="activite_id" value="<?php echo get_the_ID(); ?>">

        <label>Nom * <input type="text" name="nom" required></label><br>
        <label>Prénom * <input type="text" name="prenom" required></label><br>
        <label>Email * <input type="email" name="email" required></label><br>
        <label>Téléphone * <input type="tel" name="telephone" required></label><br>
        <label>Participants * <input type="number" name="participants" min="1" value="1" required></label><br>
        <label>Commentaire <textarea name="commentaire" rows="4"></textarea></label><br>
        <button type="submit">Envoyer ma demande</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'bnr_reservation', 'bnr_render_reservation_form' );

// Traitement sécurisé
function bnr_process_reservation() {
    if ( ! isset( $_POST['bnr_reservation_nonce'] ) || ! wp_verify_nonce( $_POST['bnr_reservation_nonce'], 'bnr_submit_reservation' ) ) { wp_die( 'Erreur de sécurité.' ); }

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
    $table_name = $wpdb->prefix . 'reservations'; // Attention, on utilise le même nom que lors de la création

    $inserted = $wpdb->insert(
        $table_name,
        array( 'activite_id' => $activite_id, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'telephone' => $telephone, 'participants' => $participants, 'commentaire' => $commentaire, 'statut' => 'En attente' ),
        array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
    );

    wp_redirect( add_query_arg( 'reservation', $inserted ? 'success' : 'error', wp_get_referer() ) );
    exit;
}
add_action( 'admin_post_nopriv_bnr_process_reservation', 'bnr_process_reservation' );
add_action( 'admin_post_bnr_process_reservation', 'bnr_process_reservation' );