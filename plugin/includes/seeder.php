<?php
// Fichier : includes/seeder.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Sécurité
}

function bnr_injecter_activites_test() {
    if ( get_option( 'bnr_donnees_test_injectees' ) ) {
        return;
    }

    $activites_test = array(
        array(
            'titre'      => 'Randonnée en Forêt de Brocéliande',
            'contenu'    => 'Partez à la découverte des légendes de Merlin l\'enchanteur...',
            'date'       => '2026-09-15',
            'heure'      => '09:00',
            'duree'      => '3h30',
            'lieu'       => 'Paimpont',
            'places'     => 15,
            'tarif'      => 12.50,
            'type'       => 'Randonnée',
            'niveau'     => 'Intermédiaire'
        ),
        array(
            'titre'      => 'Atelier découverte des algues comestibles',
            'contenu'    => 'Apprenez à identifier, récolter et cuisiner les différentes algues...',
            'date'       => '2026-10-02',
            'heure'      => '14:30',
            'duree'      => '2h00',
            'lieu'       => 'Roscoff',
            'places'     => 10,
            'tarif'      => 25.00,
            'type'       => 'Atelier',
            'niveau'     => 'Facile'
        )
        // ... (Vous pouvez remettre les 4 activités ici)
    );

    foreach ( $activites_test as $activite ) {
        $post_data = array(
            'post_title'   => $activite['titre'],
            'post_content' => $activite['contenu'],
            'post_status'  => 'publish',
            'post_type'    => 'activite',
        );
        $post_id = wp_insert_post( $post_data );

        if ( ! is_wp_error( $post_id ) && $post_id > 0 ) {
            update_post_meta( $post_id, '_activite_date', $activite['date'] );
            update_post_meta( $post_id, '_activite_heure', $activite['heure'] );
            update_post_meta( $post_id, '_activite_duree', $activite['duree'] );
            update_post_meta( $post_id, '_activite_lieu', $activite['lieu'] );
            update_post_meta( $post_id, '_activite_places', $activite['places'] );
            update_post_meta( $post_id, '_activite_tarif', $activite['tarif'] );

            wp_set_object_terms( $post_id, $activite['type'], 'type_activite' );
            wp_set_object_terms( $post_id, $activite['niveau'], 'niveau_difficulte' );
        }
    }

    update_option( 'bnr_donnees_test_injectees', true );
}
add_action( 'admin_init', 'bnr_injecter_activites_test' );