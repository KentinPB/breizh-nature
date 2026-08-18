<?php
/*
Plugin Name: Breizh'Nature Réservations
Description: Extension métier gérant les activités nature et le système de réservation.
Version: 1.0
Author: Kentin.PB
*/

// Sécurité : Empêcher l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Définition d'une constante pour simplifier les chemins d'inclusion
define( 'BNR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// 1. Inclusion de tous les fichiers logiques découpés
require_once BNR_PLUGIN_DIR . 'includes/cpt-activite.php';
require_once BNR_PLUGIN_DIR . 'includes/metaboxes.php';
require_once BNR_PLUGIN_DIR . 'includes/base-de-donnees.php';
require_once BNR_PLUGIN_DIR . 'includes/formulaire.php';
require_once BNR_PLUGIN_DIR . 'includes/admin-reservations.php';
require_once BNR_PLUGIN_DIR . 'includes/dashboard.php';
require_once BNR_PLUGIN_DIR . 'includes/roles.php';

// Script d'injection de test (À commenter une fois terminé)
require_once BNR_PLUGIN_DIR . 'includes/seeder.php';

// 2. Hook d'activation (Appelle la fonction située dans base-de-donnees.php)
register_activation_hook( __FILE__, 'bnr_activate_plugin' );