<?php
/*
Plugin Name: Breizh'Nature Réservations
Description: Extension métier gérant les activités nature et le système de réservation.
Version: 1.0
Author: Votre Nom
*/

// Sécurité : Empêcher l'accès direct au fichier (Bonne pratique requise)
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enregistrement du Custom Post Type "activite"
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
        'rewrite'            => array( 'slug' => 'activite' ),
        'capability_type'    => 'post',
        'has_archive'        => true, // Permet d'avoir une page listant toutes les activités (archive-activite.php)
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-location-alt', // Icône en forme de punaise de carte
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true, // OBLIGATOIRE : active l'éditeur Gutenberg et prépare l'API REST
    );

    register_post_type( 'activite', $args ); // Le nom strict exigé par le cahier des charges
}
add_action( 'init', 'bnr_register_cpt_activite' );