<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

// 1. Enregistrement du Custom Post Type "activite"
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
        'capability_type'    => 'post', // Utilise les droits de base des articles
        'map_meta_cap'       => true,   // Indispensable pour mapper les droits correctement
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-location-alt',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'show_in_rest'       => true,
    );
    register_post_type( 'activite', $args );
}
add_action( 'init', 'bnr_register_cpt_activite' );

// 2. Enregistrement des Taxonomies personnalisées
function bnr_register_taxonomies() {
    // Taxonomie 1 : Type d'activité
    $args_type = array(
        'hierarchical'      => true,
        'labels'            => array( 'name' => 'Types d\'activité', 'singular_name' => 'Type d\'activité' ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'type-activite' ),
        'show_in_rest'      => true,
        'capabilities'      => array(
            'manage_terms' => 'edit_posts',
            'edit_terms'   => 'edit_posts',
            'delete_terms' => 'edit_posts',
            'assign_terms' => 'edit_posts',
        ),
    );
    register_taxonomy( 'type_activite', array( 'activite' ), $args_type );

    // Taxonomie 2 : Niveau de difficulté
    $args_niveau = array(
        'hierarchical'      => true,
        'labels'            => array( 'name' => 'Niveaux', 'singular_name' => 'Niveau' ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'niveau' ),
        'show_in_rest'      => true,
        'capabilities'      => array(
            'manage_terms' => 'edit_posts',
            'edit_terms'   => 'edit_posts',
            'delete_terms' => 'edit_posts',
            'assign_terms' => 'edit_posts',
        ),
    );
    register_taxonomy( 'niveau_difficulte', array( 'activite' ), $args_niveau );
}
add_action( 'init', 'bnr_register_taxonomies' );