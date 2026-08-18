<?php
/*
Plugin Name: Breizh'Nature Réservations
Description: Extension métier gérant les activités nature et le système de réservation.
Version: 1.0
Author: Votre Nom
*/

// Sécurité : Empêcher l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Enregistrement du Custom Post Type "activite"
 */
function bnr_register_cpt_activite() {
    $labels = array(
        'name'                  => 'Activités',
        'singular_name'         => 'Activité',
        'menu_name'             => 'Activités',
        'name_admin_bar'        => 'Activité',
        'add_new'               => 'Ajouter une activité',
        'add_new_item'          => 'Ajouter une nouvelle activité',
        'new_item'              => 'Nouvelle activité',
        'edit_item'             => 'Modifier l\'activité',
        'view_item'             => 'Voir l\'activité',
        'all_items'             => 'Toutes les activités',
        'search_items'          => 'Rechercher des activités',
        'not_found'             => 'Aucune activité trouvée.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'activites' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-location-alt',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true,
    );

    register_post_type( 'activite', $args );
}
add_action( 'init', 'bnr_register_cpt_activite' );

/**
 * 2. Enregistrement des Taxonomies personnalisées
 */
function bnr_register_taxonomies() {

    // Taxonomie 1 : Type d'activité (hiérarchique comme des catégories)
    $labels_type = array(
        'name'              => 'Types d\'activité',
        'singular_name'     => 'Type d\'activité',
        'search_items'      => 'Rechercher un type',
        'all_items'         => 'Tous les types',
        'edit_item'         => 'Modifier le type',
        'update_item'       => 'Mettre à jour le type',
        'add_new_item'      => 'Ajouter un nouveau type',
        'new_item_name'     => 'Nouveau type d\'activité',
        'menu_name'         => 'Types d\'activité',
    );

    $args_type = array(
        'hierarchical'      => true,
        'labels'            => $labels_type,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'type-activite' ),
        'show_in_rest'      => true,
    );
    register_taxonomy( 'type_activite', array( 'activite' ), $args_type );

    // Taxonomie 2 : Niveau de difficulté
    $labels_niveau = array(
        'name'              => 'Niveaux',
        'singular_name'     => 'Niveau',
        'search_items'      => 'Rechercher un niveau',
        'all_items'         => 'Tous les niveaux',
        'edit_item'         => 'Modifier le niveau',
        'update_item'       => 'Mettre à jour le niveau',
        'add_new_item'      => 'Ajouter un nouveau niveau',
        'new_item_name'     => 'Nouveau niveau',
        'menu_name'         => 'Niveaux',
    );

    $args_niveau = array(
        'hierarchical'      => true,
        'labels'            => $labels_niveau,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'niveau' ),
        'show_in_rest'      => true,
    );
    register_taxonomy( 'niveau_difficulte', array( 'activite' ), $args_niveau );
}
add_action( 'init', 'bnr_register_taxonomies' );

/**
 * 3. Déclaration de la Meta Box "Informations pratiques"
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
 * Rendu HTML du formulaire des Meta Boxes
 */
function bnr_render_activite_metabox( $post ) {
    // Nonce de sécurité pour vérifier la provenance de la requête
    wp_nonce_field( 'bnr_save_activite_meta', 'bnr_activite_nonce' );

    // Récupération des valeurs existantes
    $date       = get_post_meta( $post->ID, '_activite_date', true );
    $heure      = get_post_meta( $post->ID, '_activite_heure', true );
    $duree      = get_post_meta( $post->ID, '_activite_duree', true );
    $lieu       = get_post_meta( $post->ID, '_activite_lieu', true );
    $places     = get_post_meta( $post->ID, '_activite_places', true );
    $tarif      = get_post_meta( $post->ID, '_activite_tarif', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="bnr_date">Date</label></th>
            <td><input type="date" id="bnr_date" name="bnr_date" value="<?php echo esc_attr( $date ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_heure">Heure</label></th>
            <td><input type="time" id="bnr_heure" name="bnr_heure" value="<?php echo esc_attr( $heure ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_duree">Durée (ex: 2h30)</label></th>
            <td><input type="text" id="bnr_duree" name="bnr_duree" value="<?php echo esc_attr( $duree ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_lieu">Lieu (Ville / Spot)</label></th>
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
    </table>
    <?php
}

/**
 * 4. Sauvegarde et sécurisation des données Meta Box
 */
function bnr_save_activite_meta( $post_id ) {
    // Vérification du nonce de sécurité
    if ( ! isset( $_POST['bnr_activite_nonce'] ) || ! wp_verify_nonce( $_POST['bnr_activite_nonce'], 'bnr_save_activite_meta' ) ) {
        return;
    }

    // Éviter la sauvegarde automatique WordPress
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Vérification des droits utilisateur
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Nettoyage et enregistrement des champs
    $champs = array(
        '_activite_date'   => 'bnr_date',
        '_activite_heure'  => 'bnr_heure',
        '_activite_duree'  => 'bnr_duree',
        '_activite_lieu'   => 'bnr_lieu',
        '_activite_places' => 'bnr_places',
        '_activite_tarif'  => 'bnr_tarif',
    );

    foreach ( $champs as $meta_key => $post_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
        }
    }
}
add_action( 'save_post_activite', 'bnr_save_activite_meta' );