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
 * SCRIPT D'INJECTION DE COMPTES UTILISATEURS (FIXTURES)
 * À supprimer ou commenter une fois l'injection réussie !
 * ====================================================================
 */
/*function bnr_injecter_comptes_test() {

    // Notre catalogue de comptes de test
    $comptes_test = array(
        array(
            'user_login' => 'jean_gestionnaire',
            'user_pass'  => 'BreizhNature2026!', // Mot de passe commun pour vos tests
            'user_email' => 'jean.gestionnaire@breizhnature.local',
            'first_name' => 'Jean',
            'last_name'  => 'Dupont',
            'role'       => 'gestionnaire' // Le profil avec accès limité que nous avons créé
        ),
        array(
            'user_login' => 'marie_gestionnaire',
            'user_pass'  => 'BreizhNature2026!',
            'user_email' => 'marie.gestionnaire@breizhnature.local',
            'first_name' => 'Marie',
            'last_name'  => 'Martin',
            'role'       => 'gestionnaire'
        )
    );

    // Boucle d'insertion
    foreach ( $comptes_test as $compte ) {

        // Sécurité : On vérifie que l'email ou l'identifiant n'existe pas déjà
        if ( ! get_user_by( 'email', $compte['user_email'] ) && ! get_user_by( 'login', $compte['user_login'] ) ) {

            // Création de l'utilisateur
            $user_id = wp_insert_user( $compte );

            // Gestion visuelle d'une éventuelle erreur (optionnel mais utile en développement)
            if ( is_wp_error( $user_id ) ) {
                error_log( 'Erreur création fixture utilisateur : ' . $user_id->get_error_message() );
            }
        }
    }
}
// Le hook 'admin_init' lance le script dès que vous ouvrez l'administration
add_action( 'admin_init', 'bnr_injecter_comptes_test' );*/