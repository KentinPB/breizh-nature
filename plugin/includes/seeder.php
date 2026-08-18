<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Sécurité
}

/**
 * ====================================================================
 * SCRIPT D'INJECTION D'ACTIVITÉS (FIXTURES)
 * À supprimer ou commenter une fois l'injection réussie !
 * ====================================================================
 */
/*function bnr_injecter_activites_test() {
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
add_action( 'admin_init', 'bnr_injecter_activites_test' );*/

/**
 * ====================================================================
 * SCRIPT D'INJECTION DE DONNÉES DE TEST V2 (SEEDER)
 * À supprimer ou commenter une fois l'injection réussie !
 * ====================================================================
 */
function bnr_injecter_activites_test_v2()
{

    // Sécurité : On vérifie si l'injection V2 a déjà été faite
    if (get_option('bnr_donnees_test_v2_injectees')) {
        return;
    }

    // Notre catalogue de test complet répondant au cahier des charges
    $activites_test = array(
        // 1. Randonnées
        array(
            'titre' => 'Randonnée en Forêt de Brocéliande',
            'contenu' => 'Partez à la découverte des légendes de Merlin l\'enchanteur et de la fée Morgane lors d\'une marche immersive au cœur de la mythique forêt de Brocéliande.',
            'date' => '2026-09-15',
            'heure' => '09:00',
            'duree' => '3h30',
            'lieu' => 'Paimpont',
            'places' => 15,
            'tarif' => 12.50,
            'date_limite' => '2026-09-10', // Nouveau champ intégré !
            'type' => 'Randonnée',
            'niveau' => 'Intermédiaire'
        ),
        // 2. Sorties découverte
        array(
            'titre' => 'Sortie découverte de la Pointe du Raz',
            'contenu' => 'Une marche époustouflante face à l\'océan déchaîné pour découvrir la faune, la flore et la géologie de ce site emblématique.',
            'date' => '2026-10-05',
            'heure' => '10:00',
            'duree' => '4h00',
            'lieu' => 'Plogoff',
            'places' => 12,
            'tarif' => 15.00,
            'date_limite' => '2026-10-01',
            'type' => 'Sortie découverte',
            'niveau' => 'Difficile'
        ),
        // 3. Ateliers pédagogiques
        array(
            'titre' => 'Atelier pédagogique : Les algues comestibles',
            'contenu' => 'Apprenez à identifier, récolter et cuisiner les différentes algues du littoral breton. Dégustation prévue en fin d\'atelier.',
            'date' => '2026-10-20',
            'heure' => '14:30',
            'duree' => '2h00',
            'lieu' => 'Roscoff',
            'places' => 10,
            'tarif' => 25.00,
            'date_limite' => '2026-10-18',
            'type' => 'Atelier pédagogique',
            'niveau' => 'Facile'
        ),
        // 4. Visites de sites naturels
        array(
            'titre' => 'Visite de la réserve ornithologique',
            'contenu' => 'Sortie avec un guide naturaliste pour observer les oiseaux marins dans leur habitat naturel. Prêt de jumelles inclus.',
            'date' => '2026-11-02',
            'heure' => '09:30',
            'duree' => '2h30',
            'lieu' => 'Cap Fréhel',
            'places' => 20,
            'tarif' => 8.00,
            'date_limite' => '2026-10-31',
            'type' => 'Visite de sites naturels',
            'niveau' => 'Facile'
        ),
        // 5. Événements
        array(
            'titre' => 'Grand rassemblement Nature & Patrimoine',
            'contenu' => 'Une journée exceptionnelle réunissant artisans locaux, guides nature et passionnés autour du patrimoine breton.',
            'date' => '2026-11-15',
            'heure' => '10:00',
            'duree' => 'Toute la journée',
            'lieu' => 'Carnac',
            'places' => 50,
            'tarif' => 5.00,
            'date_limite' => '2026-11-10',
            'type' => 'Événement',
            'niveau' => 'Facile'
        ),
        // 6. Animations pour les familles
        array(
            'titre' => 'Chasse au trésor des Corsaires',
            'contenu' => 'Une animation ludique en famille pour découvrir l\'histoire maritime de la Bretagne à travers des énigmes et des épreuves.',
            'date' => '2026-12-01',
            'heure' => '14:00',
            'duree' => '3h00',
            'lieu' => 'Saint-Malo',
            'places' => 30,
            'tarif' => 10.00,
            'date_limite' => '2026-11-28',
            'type' => 'Animation famille',
            'niveau' => 'Facile'
        )
    );

    // Boucle d'insertion des activités
    foreach ($activites_test as $activite) {

        $post_data = array(
            'post_title' => $activite['titre'],
            'post_content' => $activite['contenu'],
            'post_status' => 'publish',
            'post_type' => 'activite',
        );
        $post_id = wp_insert_post($post_data);

        if (!is_wp_error($post_id) && $post_id > 0) {

            // Ajout des méta-données
            update_post_meta($post_id, '_activite_date', $activite['date']);
            update_post_meta($post_id, '_activite_heure', $activite['heure']);
            update_post_meta($post_id, '_activite_duree', $activite['duree']);
            update_post_meta($post_id, '_activite_lieu', $activite['lieu']);
            update_post_meta($post_id, '_activite_places', $activite['places']);
            update_post_meta($post_id, '_activite_tarif', $activite['tarif']);
            update_post_meta($post_id, '_activite_date_limite', $activite['date_limite']); // NOUVEAU !

            // Ajout des taxonomies
            wp_set_object_terms($post_id, $activite['type'], 'type_activite');
            wp_set_object_terms($post_id, $activite['niveau'], 'niveau_difficulte');
        }
    }

    // On mémorise que l'action a été effectuée
    update_option('bnr_donnees_test_v2_injectees', true);
}

add_action('admin_init', 'bnr_injecter_activites_test_v2');