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