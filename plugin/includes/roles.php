<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Création du rôle Gestionnaire et assignation des capacités personnalisées
 */
function bnr_register_custom_roles() {

    // 1. On donne d'abord la nouvelle capacité sur-mesure "manage_reservations" à l'Administrateur
    $admin_role = get_role( 'administrator' );
    if ( $admin_role ) {
        $admin_role->add_cap( 'manage_reservations' );
    }

    // 2. Création du rôle "Gestionnaire" avec des droits limités
    // Il pourra créer/modifier des activités et lire les réservations, mais pas casser le site.
    add_role(
        'gestionnaire',
        'Gestionnaire',
        array(
            'read'                   => true,  // Peut lire le tableau de bord
            'edit_posts'             => true,  // Peut créer et modifier des activités
            'publish_posts'          => true,  // Peut publier en ligne
            'edit_published_posts'   => true,  // Peut modifier après publication
            'delete_posts'           => true,  // Peut supprimer une activité
            'upload_files'           => true,  // Indispensable pour ajouter des images mises en avant
            'manage_reservations'    => true,  // Notre capacité sur-mesure pour lire les réservations

            // Sécurité : On NE LUI DONNE PAS 'manage_options', 'switch_themes' ou 'install_plugins'
        )
    );
}
// Le hook 'admin_init' s'assure que les rôles sont vérifiés et créés au chargement de l'administration
add_action( 'admin_init', 'bnr_register_custom_roles' );
add_action('admin_menu', function () {
    // On ne vérifie l'accès que si l'utilisateur est bien "Gestionnaire"
    if ( current_user_can('gestionnaire') && !current_user_can('administrator') ) {

        // Supprimer les menus sensibles
        remove_menu_page('tools.php');        // Outils
        remove_menu_page('options-general.php'); // Réglages
        remove_menu_page('plugins.php');      // Extensions
        remove_menu_page('themes.php');       // Apparence
        remove_menu_page('users.php');        // Utilisateurs

        // Si vous voulez aussi masquer le tableau de bord principal
        // remove_menu_page('index.php');
    }
});