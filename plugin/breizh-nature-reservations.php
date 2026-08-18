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

/**
 * 3. Déclaration de la Meta Box "Informations pratiques"
 */
function bnr_add_activite_metaboxes() {
    add_meta_box(
        'bnr_activite_details',
        'Informations pratiques de l\'activité',
        'bnr_render_activite_metabox',
        'activite',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'bnr_add_activite_metaboxes' );

/**
 * Rendu HTML du formulaire des Meta Boxes
 */
function bnr_render_activite_metabox( $post ) {
    // Nonce de sécurité pour vérifier la provenance de la requête
    wp_nonce_field( 'bnr_save_activite_meta', 'bnr_activite_nonce' );

    // Récupération des valeurs existantes
    $date       = get_post_meta( $post->ID, '_activite_date', true );
    $heure      = get_post_meta( $post->ID, '_activite_heure', true );
    $duree      = get_post_meta( $post->ID, '_activite_duree', true );
    $lieu       = get_post_meta( $post->ID, '_activite_lieu', true );
    $places     = get_post_meta( $post->ID, '_activite_places', true );
    $tarif      = get_post_meta( $post->ID, '_activite_tarif', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="bnr_date">Date</label></th>
            <td><input type="date" id="bnr_date" name="bnr_date" value="<?php echo esc_attr( $date ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_heure">Heure</label></th>
            <td><input type="time" id="bnr_heure" name="bnr_heure" value="<?php echo esc_attr( $heure ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_duree">Durée (ex: 2h30)</label></th>
            <td><input type="text" id="bnr_duree" name="bnr_duree" value="<?php echo esc_attr( $duree ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_lieu">Lieu (Ville / Spot)</label></th>
            <td><input type="text" id="bnr_lieu" name="bnr_lieu" value="<?php echo esc_attr( $lieu ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_places">Nombre max de participants</label></th>
            <td><input type="number" id="bnr_places" name="bnr_places" min="1" value="<?php echo esc_attr( $places ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="bnr_tarif">Tarif (€)</label></th>
            <td><input type="number" step="0.01" min="0" id="bnr_tarif" name="bnr_tarif" value="<?php echo esc_attr( $tarif ); ?>" class="regular-text"></td>
        </tr>
    </table>
    <?php
}

/**
 * 4. Sauvegarde et sécurisation des données Meta Box
 */
function bnr_save_activite_meta( $post_id ) {
    // Vérification du nonce de sécurité
    if ( ! isset( $_POST['bnr_activite_nonce'] ) || ! wp_verify_nonce( $_POST['bnr_activite_nonce'], 'bnr_save_activite_meta' ) ) {
        return;
    }

    // Éviter la sauvegarde automatique WordPress
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Vérification des droits utilisateur
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Nettoyage et enregistrement des champs
    $champs = array(
        '_activite_date'   => 'bnr_date',
        '_activite_heure'  => 'bnr_heure',
        '_activite_duree'  => 'bnr_duree',
        '_activite_lieu'   => 'bnr_lieu',
        '_activite_places' => 'bnr_places',
        '_activite_tarif'  => 'bnr_tarif',
    );

    foreach ( $champs as $meta_key => $post_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
        }
    }
}
add_action( 'save_post_activite', 'bnr_save_activite_meta' );

/**
 * 5. Création de la table de base de données à l'activation du plugin
 */
function bnr_activate_plugin() {
    global $wpdb;

    // Nom de la table avec le préfixe de WordPress (ex: wp_bnr_reservations)
    $table_name = $wpdb->prefix . 'reservations';
    $charset_collate = $wpdb->get_charset_collate();

    // Requête SQL pour créer la table selon le cahier des charges
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

    // dbDelta nécessite ce fichier natif
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Exécution sécurisée
    dbDelta( $sql );
}
// Le hook d'activation de WordPress
register_activation_hook( __FILE__, 'bnr_activate_plugin' );

/**
 * 6. Création du formulaire HTML (Shortcode)
 */
function bnr_render_reservation_form() {
    ob_start(); // Début de la temporisation de l'affichage

    // Affichage des messages de confirmation ou d'erreur
    if ( isset( $_GET['reservation'] ) ) {
        if ( $_GET['reservation'] === 'success' ) {
            echo '<p style="color: green; font-weight: bold;">✅ Votre demande de réservation a bien été envoyée. Nous vous recontacterons vite !</p>';
        } elseif ( $_GET['reservation'] === 'error' ) {
            echo '<p style="color: red; font-weight: bold;">❌ Une erreur est survenue. Veuillez vérifier votre saisie.</p>';
        }
    }

    // Le formulaire HTML avec les champs exigés par le cahier des charges
    ?>
    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" class="bnr-form">

        <!-- Sécurité : Le nonce protège contre les failles CSRF -->
        <?php wp_nonce_field( 'bnr_submit_reservation', 'bnr_reservation_nonce' ); ?>

        <!-- Action cachée pour dire à WordPress quelle fonction PHP appeler -->
        <input type="hidden" name="action" value="bnr_process_reservation">
        <input type="hidden" name="activite_id" value="<?php echo get_the_ID(); ?>">

        <div>
            <label for="nom">Nom *</label><br>
            <input type="text" id="nom" name="nom" required>
        </div>
        <br>
        <div>
            <label for="prenom">Prénom *</label><br>
            <input type="text" id="prenom" name="prenom" required>
        </div>
        <br>
        <div>
            <label for="email">Adresse e-mail *</label><br>
            <input type="email" id="email" name="email" required>
        </div>
        <br>
        <div>
            <label for="telephone">Téléphone *</label><br>
            <input type="tel" id="telephone" name="telephone" required>
        </div>
        <br>
        <div>
            <label for="participants">Nombre de participants *</label><br>
            <input type="number" id="participants" name="participants" min="1" value="1" required>
        </div>
        <br>
        <div>
            <label for="commentaire">Commentaire</label><br>
            <textarea id="commentaire" name="commentaire" rows="4"></textarea>
        </div>
        <br>
        <button type="submit">Envoyer ma demande</button>
    </form>
    <?php

    return ob_get_clean(); // Retourne le HTML généré
}
// On déclare ce formulaire comme un "shortcode" utilisable partout avec [bnr_reservation]
add_shortcode( 'bnr_reservation', 'bnr_render_reservation_form' );


/**
 * 7. Traitement et nettoyage des données envoyées par le formulaire
 */
function bnr_process_reservation() {
    // Étape 1 : Sécurité - Vérification du nonce (faille CSRF)
    if ( ! isset( $_POST['bnr_reservation_nonce'] ) || ! wp_verify_nonce( $_POST['bnr_reservation_nonce'], 'bnr_submit_reservation' ) ) {
        wp_die( 'Erreur de sécurité (Nonce invalide).' );
    }

    // Étape 2 : Nettoyage strict des données (Sanitization pour éviter les failles XSS/SQL)
    $activite_id  = isset( $_POST['activite_id'] ) ? intval( $_POST['activite_id'] ) : 0;
    $nom          = isset( $_POST['nom'] ) ? sanitize_text_field( $_POST['nom'] ) : '';
    $prenom       = isset( $_POST['prenom'] ) ? sanitize_text_field( $_POST['prenom'] ) : '';
    $email        = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $telephone    = isset( $_POST['telephone'] ) ? sanitize_text_field( $_POST['telephone'] ) : '';
    $participants = isset( $_POST['participants'] ) ? intval( $_POST['participants'] ) : 1;
    $commentaire  = isset( $_POST['commentaire'] ) ? sanitize_textarea_field( $_POST['commentaire'] ) : '';

    // Étape 3 : Vérification finale avant insertion
    if ( empty( $nom ) || empty( $prenom ) || ! is_email( $email ) ) {
        // Redirection avec erreur
        wp_redirect( add_query_arg( 'reservation', 'error', wp_get_referer() ) );
        exit;
    }

    // Étape 4 : Insertion sécurisée (requête préparée) dans la table SQL
    global $wpdb;
    $table_name = $wpdb->prefix . 'bnr_reservations';

    $inserted = $wpdb->insert(
            $table_name,
            array(
                    'activite_id'  => $activite_id,
                    'nom'          => $nom,
                    'prenom'       => $prenom,
                    'email'        => $email,
                    'telephone'    => $telephone,
                    'participants' => $participants,
                    'commentaire'  => $commentaire,
                    'statut'       => 'En attente'
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' ) // Type de données (d=entier, s=chaîne)
    );

    // Étape 5 : Redirection
    if ( $inserted ) {
        wp_redirect( add_query_arg( 'reservation', 'success', wp_get_referer() ) );
    } else {
        wp_redirect( add_query_arg( 'reservation', 'error', wp_get_referer() ) );
    }
    exit;
}
// Déclaration des hooks natifs pour intercepter le formulaire
add_action( 'admin_post_nopriv_bnr_process_reservation', 'bnr_process_reservation' ); // Pour les visiteurs non connectés
add_action( 'admin_post_bnr_process_reservation', 'bnr_process_reservation' ); // Pour les utilisateurs connectés