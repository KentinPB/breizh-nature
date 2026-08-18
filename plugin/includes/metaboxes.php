<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function bnr_add_activite_metaboxes() {
    add_meta_box( 'bnr_activite_details', 'Informations pratiques de l\'activité', 'bnr_render_activite_metabox', 'activite', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'bnr_add_activite_metaboxes' );

function bnr_render_activite_metabox( $post ) {
    wp_nonce_field( 'bnr_save_activite_meta', 'bnr_activite_nonce' );

    $date       = get_post_meta( $post->ID, '_activite_date', true );
    $heure      = get_post_meta( $post->ID, '_activite_heure', true );
    $duree      = get_post_meta( $post->ID, '_activite_duree', true );
    $lieu       = get_post_meta( $post->ID, '_activite_lieu', true );
    $places     = get_post_meta( $post->ID, '_activite_places', true );
    $tarif      = get_post_meta( $post->ID, '_activite_tarif', true );
    ?>
    <table class="form-table">
        <tr><th><label for="bnr_date">Date</label></th><td><input type="date" id="bnr_date" name="bnr_date" value="<?php echo esc_attr( $date ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="bnr_heure">Heure</label></th><td><input type="time" id="bnr_heure" name="bnr_heure" value="<?php echo esc_attr( $heure ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="bnr_duree">Durée</label></th><td><input type="text" id="bnr_duree" name="bnr_duree" value="<?php echo esc_attr( $duree ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="bnr_lieu">Lieu</label></th><td><input type="text" id="bnr_lieu" name="bnr_lieu" value="<?php echo esc_attr( $lieu ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="bnr_places">Places max</label></th><td><input type="number" id="bnr_places" name="bnr_places" value="<?php echo esc_attr( $places ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="bnr_tarif">Tarif (€)</label></th><td><input type="number" step="0.01" id="bnr_tarif" name="bnr_tarif" value="<?php echo esc_attr( $tarif ); ?>" class="regular-text"></td></tr>
    </table>
    <?php
}

function bnr_save_activite_meta( $post_id ) {
    if ( ! isset( $_POST['bnr_activite_nonce'] ) || ! wp_verify_nonce( $_POST['bnr_activite_nonce'], 'bnr_save_activite_meta' ) ) { return; }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

    $champs = array( '_activite_date' => 'bnr_date', '_activite_heure' => 'bnr_heure', '_activite_duree' => 'bnr_duree', '_activite_lieu' => 'bnr_lieu', '_activite_places' => 'bnr_places', '_activite_tarif' => 'bnr_tarif' );

    foreach ( $champs as $meta_key => $post_key ) {
        if ( isset( $_POST[ $post_key ] ) ) { update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) ); }
    }
}
add_action( 'save_post_activite', 'bnr_save_activite_meta' );