<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * 1. Déclaration de la Meta Box "Informations pratiques"
 */
function bnr_add_activite_metaboxes() {
    add_meta_box(
            'bnr_activite_details',
            'Informations pratiques de l\'activité',
            'bnr_render_activite_metabox',
            'activite',
            'normal',
            'high'
    );
}
add_action( 'add_meta_boxes', 'bnr_add_activite_metaboxes' );

/**
 * 2. Rendu HTML du formulaire dans l'administration
 */
function bnr_render_activite_metabox( $post ) {
    wp_nonce_field( 'bnr_save_activite_meta', 'bnr_activite_nonce' );

    $date        = get_post_meta( $post->ID, '_activite_date', true );
    $heure       = get_post_meta( $post->ID, '_activite_heure', true );
    $duree       = get_post_meta( $post->ID, '_activite_duree', true );
    $lieu        = get_post_meta( $post->ID, '_activite_lieu', true );
    $places      = get_post_meta( $post->ID, '_activite_places', true );
    $tarif       = get_post_meta( $post->ID, '_activite_tarif', true );
    $date_limite = get_post_meta( $post->ID, '_activite_date_limite', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="bnr_date">Date de l'activité</label></th>
            <td><input type="date" id="bnr_date" name="bnr_date" value="<?php echo esc_attr( $date ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_heure">Heure</label></th>
            <td><input type="time" id="bnr_heure" name="bnr_heure" value="<?php echo esc_attr( $heure ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_duree">Durée</label></th>
            <td><input type="text" id="bnr_duree" name="bnr_duree" value="<?php echo esc_attr( $duree ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_lieu">Lieu</label></th>
            <td><input type="text" id="bnr_lieu" name="bnr_lieu" value="<?php echo esc_attr( $lieu ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_places">Nombre max de participants</label></th>
            <td><input type="number" id="bnr_places" name="bnr_places" min="1" value="<?php echo esc_attr( $places ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_tarif">Tarif (€)</label></th>
            <td><input type="number" step="0.01" min="0" id="bnr_tarif" name="bnr_tarif" value="<?php echo esc_attr( $tarif ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_date_limite">Date limite d'inscription</label></th>
            <td><input type="date" id="bnr_date_limite" name="bnr_date_limite" value="<?php echo esc_attr( $date_limite ); ?>" class="regular-text"></td>
        </tr>
    </table>
    <?php
}

/**
 * 3. Sauvegarde des données
 */
function bnr_save_activite_meta( $post_id ) {
    if ( ! isset( $_POST['bnr_activite_nonce'] ) || ! wp_verify_nonce( $_POST['bnr_activite_nonce'], 'bnr_save_activite_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $champs = array(
            '_activite_date'        => 'bnr_date',
            '_activite_heure'       => 'bnr_heure',
            '_activite_duree'       => 'bnr_duree',
            '_activite_lieu'        => 'bnr_lieu',
            '_activite_places'      => 'bnr_places',
            '_activite_tarif'       => 'bnr_tarif',
            '_activite_date_limite' => 'bnr_date_limite'
    );

    foreach ( $champs as $meta_key => $post_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
        }
    }
}
add_action( 'save_post_activite', 'bnr_save_activite_meta' );