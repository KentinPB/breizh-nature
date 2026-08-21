<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * 1. Enregistrement des routes de l'API REST personnalisée
 */
function bnr_register_rest_routes() {
    // Route 1 : Liste de toutes les activités (/wp-json/breizhnature/v1/activites)
    register_rest_route( 'breizhnature/v1', '/activites', array(
        'methods'             => 'GET',
        'callback'            => 'bnr_rest_get_activites',
        'permission_callback' => '__return_true', // Public (lecture seule)
    ) );

    // Route 2 : Une seule activité par son ID (/wp-json/breizhnature/v1/activites/{id})
    register_rest_route( 'breizhnature/v1', '/activites/(?P<id>\d+)', array(
        'methods'             => 'GET',
        'callback'            => 'bnr_rest_get_single_activite',
        'permission_callback' => '__return_true',
    ) );

    // Route 3 : Liste des catégories / types d'activités (/wp-json/breizhnature/v1/categories)
    register_rest_route( 'breizhnature/v1', '/categories', array(
        'methods'             => 'GET',
        'callback'            => 'bnr_rest_get_categories',
        'permission_callback' => '__return_true',
    ) );
}
add_action( 'rest_api_init', 'bnr_register_rest_routes' );

/**
 * 2. Callback pour récupérer la liste de toutes les activités
 */
function bnr_rest_get_activites( $request ) {
    $args = array(
        'post_type'      => 'activite',
        'posts_per_page' => -1, // Toutes les activités
        'post_status'    => 'publish',
    );

    // Gestion d'un filtre optionnel par type d'activité (taxonomie)
    $type = $request->get_param( 'type' );
    if ( ! empty( $type ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'type_activite',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $type ),
            ),
        );
    }

    $query = new WP_Query( $args );
    $activites_data = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();

            // Limitation des données exposées (sécurité & performance)
            $activites_data[] = array(
                'id'          => $post_id,
                'titre'       => get_the_title(),
                'description' => wp_strip_all_tags( get_the_excerpt() ),
                'lien'        => get_permalink(),
                'date'        => get_post_meta( $post_id, '_activite_date', true ),
                'heure'       => get_post_meta( $post_id, '_activite_heure', true ),
                'lieu'        => get_post_meta( $post_id, '_activite_lieu', true ),
                'tarif'       => get_post_meta( $post_id, '_activite_tarif', true ),
                'places_max'  => get_post_meta( $post_id, '_activite_places', true ),
            );
        }
        wp_reset_postdata();
    }

    return rest_ensure_response( $activites_data );
}

/**
 * 3. Callback pour récupérer une seule activité par son ID
 */
function bnr_rest_get_single_activite( $request ) {
    $post_id = intval( $request['id'] );
    $post    = get_post( $post_id );

    if ( ! $post || 'activite' !== $post->post_type || 'publish' !== $post->post_status ) {
        return new WP_Error(
            'activite_introuvable',
            'Cette activité est introuvable ou n\'existe pas.',
            array( 'status' => 404 )
        );
    }

    $activite_data = array(
        'id'          => $post_id,
        'titre'       => get_the_title( $post_id ),
        'description' => wp_strip_all_tags( $post->post_content ),
        'lien'        => get_permalink( $post_id ),
        'date'        => get_post_meta( $post_id, '_activite_date', true ),
        'heure'       => get_post_meta( $post_id, '_activite_heure', true ),
        'duree'       => get_post_meta( $post_id, '_activite_duree', true ),
        'lieu'        => get_post_meta( $post_id, '_activite_lieu', true ),
        'tarif'       => get_post_meta( $post_id, '_activite_tarif', true ),
        'places_max'  => get_post_meta( $post_id, '_activite_places', true ),
    );

    return rest_ensure_response( $activite_data );
}

/**
 * 4. Callback pour récupérer la liste des catégories (type_activite)
 */
function bnr_rest_get_categories() {
    $terms = get_terms( array(
        'taxonomy'   => 'type_activite',
        'hide_empty' => false,
    ) );

    if ( is_wp_error( $terms ) ) {
        return new WP_Error( 'erreur_categories', 'Impossible de récupérer les catégories.', array( 'status' => 500 ) );
    }

    $categories_data = array();
    foreach ( $terms as $term ) {
        $categories_data[] = array(
            'id'    => $term->term_id,
            'nom'   => $term->name,
            'slug'  => $term->slug,
            'count' => $term->count,
        );
    }

    return rest_ensure_response( $categories_data );
}