<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function bnr_activate_plugin() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        activite_id bigint(20) NOT NULL,
        nom varchar(100) NOT NULL,
        prenom varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        telephone varchar(20) NOT NULL,
        participants int(5) NOT NULL,
        commentaire text,
        statut varchar(20) DEFAULT 'En attente' NOT NULL,
        date_creation datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
// Note: Le hook "register_activation_hook" est resté dans le fichier principal !