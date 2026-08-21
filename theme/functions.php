<?php
/**
 * Enregistrement des scripts et styles
 */
function breizh_nature_scripts() {
    wp_enqueue_style( 'breizh-nature-style', get_stylesheet_uri() );
    wp_enqueue_script( 'breizh-ajax-filter', get_template_directory_uri() . '/asset/js/ajax-filter.js', array(), '1.1', true );

    wp_localize_script( 'breizh-ajax-filter', 'breizh_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'breizh_filter_nonce' )
    ));
}
add_action( 'wp_enqueue_scripts', 'breizh_nature_scripts' );

/**
 * Configuration des fonctionnalités du thème
 */
function breizh_nature_theme_setup() {
    // Prise en charge de la balise <title> dynamique (Essentiel pour le SEO)
    add_theme_support( 'title-tag' );

    // Prise en charge des images mises en avant (Thumbnails)
    add_theme_support( 'post-thumbnails' );

    // Prise en charge du logo personnalisé
    add_theme_support( 'custom-logo', array(
            'height'      => 80,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
    ) );

    // Déclaration de l'emplacement du menu principal
    register_nav_menus( array(
            'menu-principal' => 'Menu Principal',
    ) );
}
add_action( 'after_setup_theme', 'breizh_nature_theme_setup' );

add_action('admin_menu', function () {
    remove_menu_page('edit.php');              // Articles
    remove_menu_page('edit-comments.php');     // Commentaires
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category'); // Catégories
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag'); // Étiquettes
});

/**
 * Traitement AJAX pour le filtrage dynamique des activités
 */
function bnr_ajax_filter_activites() {
    check_ajax_referer( 'breizh_filter_nonce', 'security' );

    $args = array(
            'post_type'      => 'activite',
            'posts_per_page' => 10,
            'tax_query'      => array( 'relation' => 'AND' ),
            'meta_query'     => array( 'relation' => 'AND' ),
    );

    if ( ! empty( $_POST['type_activite'] ) ) {
        $args['tax_query'][] = array( 'taxonomy' => 'type_activite', 'field' => 'slug', 'terms' => sanitize_text_field( $_POST['type_activite'] ) );
    }
    if ( ! empty( $_POST['niveau'] ) ) {
        $args['tax_query'][] = array( 'taxonomy' => 'niveau_difficulte', 'field' => 'slug', 'terms' => sanitize_text_field( $_POST['niveau'] ) );
    }

    // NOUVELLE LOGIQUE DE FILTRAGE PAR DATE ET STATUT EN AJAX
    $aujourdhui = date('Y-m-d');
    if ( ! empty( $_POST['date_activite'] ) ) {
        $args['meta_query'][] = array( 'key' => '_activite_date', 'value' => sanitize_text_field( $_POST['date_activite'] ), 'compare' => '>=', 'type' => 'DATE' );
    } elseif ( empty( $_POST['afficher_terminees'] ) ) {
        // Par défaut (case NON cochée) : on ne montre que les activités à venir
        $args['meta_query'][] = array( 'key' => '_activite_date', 'value' => $aujourdhui, 'compare' => '>=', 'type' => 'DATE' );
    }

    $query_filtree = new WP_Query( $args );

    if ( $query_filtree->have_posts() ) :
        while ( $query_filtree->have_posts() ) : $query_filtree->the_post();

            $lieu        = get_post_meta( get_the_ID(), '_activite_lieu', true );
            $tarif       = get_post_meta( get_the_ID(), '_activite_tarif', true );
            $date_debut  = get_post_meta( get_the_ID(), '_activite_date', true );
            $heure_debut = get_post_meta( get_the_ID(), '_activite_heure', true );
            $date_limite = get_post_meta( get_the_ID(), '_activite_date_limite', true );
            $places_max  = intval( get_post_meta( get_the_ID(), '_activite_places', true ) );

            global $wpdb;
            $table_reservations = $wpdb->prefix . 'reservations';
            $places_reservees   = (int) $wpdb->get_var( $wpdb->prepare(
                    "SELECT SUM(participants) FROM $table_reservations WHERE activite_id = %d AND statut = 'Acceptée'",
                    get_the_ID()
            ) );

            $places_restantes = $places_max > 0 ? max( 0, $places_max - $places_reservees ) : 0;
            $aujourdhui_calc  = date( 'Y-m-d' );

            $badge_text  = 'Ouvert';
            $badge_color = '#28a745';

            if ( ! empty( $date_debut ) && $aujourdhui_calc > $date_debut ) {
                $badge_text  = 'Terminée';
                $badge_color = '#6c757d';
            } elseif ( ! empty( $date_limite ) && $aujourdhui_calc > $date_limite ) {
                $badge_text  = 'Inscriptions closes';
                $badge_color = '#dc3545';
            } elseif ( $places_max > 0 && $places_restantes <= 0 ) {
                $badge_text  = 'Complet';
                $badge_color = '#fd7e14';
            }
            ?>

            <article class="activite-card" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; position: relative;">

                <span style="position: absolute; top: 15px; right: 15px; background-color: <?php echo esc_attr( $badge_color ); ?>; color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; z-index: 1;">
                    <?php echo esc_html( $badge_text ); ?>
                </span>

                <h2 class="activite-title" style="padding-right: 120px;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="activite-meta-preview">
                    <?php
                    echo '<span>🏷️ ' . strip_tags( get_the_term_list( get_the_ID(), 'type_activite', '', ', ' ) ) . '</span> | ';
                    echo '<span>⭐ ' . strip_tags( get_the_term_list( get_the_ID(), 'niveau_difficulte', '', ', ' ) ) . '</span><br>';
                    ?>
                    <?php if ( $lieu ) : ?><span>📍 <?php echo esc_html( $lieu ); ?></span><?php endif; ?>
                    <?php if ( $tarif ) : ?><span>💶 <?php echo esc_html( $tarif ); ?> €</span><?php endif; ?>

                    <br><br>
                    <?php if ( $date_debut ) : ?>
                        <span>📅 <strong>Début :</strong> <?php echo date( 'd/m/Y', strtotime( $date_debut ) ); ?> à <?php echo esc_html( $heure_debut ); ?></span><br>
                    <?php endif; ?>
                    <?php if ( $date_limite ) : ?>
                        <span>⏳ <strong>Inscription avant le :</strong> <?php echo date( 'd/m/Y', strtotime( $date_limite ) ); ?></span><br>
                    <?php endif; ?>
                    <?php if ( $places_max ) : ?>
                        <span>👥 <strong>Places occupées :</strong> <?php echo $places_reservees; ?> / <?php echo esc_html( $places_max ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="activite-summary" style="margin-top: 15px;">
                    <?php the_excerpt(); ?>
                    <a href="<?php the_permalink(); ?>" class="btn-readmore">Voir les détails</a>
                </div>
            </article>
        <?php
        endwhile;
    else :
        echo '<p>Désolé, aucune activité ne correspond à vos critères de recherche.</p>';
    endif;

    wp_reset_postdata();
    wp_die();
}
add_action( 'wp_ajax_filter_activites', 'bnr_ajax_filter_activites' );
add_action( 'wp_ajax_nopriv_filter_activites', 'bnr_ajax_filter_activites' );


if (!defined('ABSPATH')) {
    exit;
}

/**
 * Supprime la version de WordPress des balises meta generator
 */
function bnr_remove_wp_version_meta()
{
    return '';
}

add_filter('the_generator', 'bnr_remove_wp_version_meta');

/**
 * Supprime les numéros de version (?ver=... et ?version=...) des scripts et styles
 */
function bnr_remove_wp_version_strings( $src ) {
    // 1. On supprime le paramètre standard de WordPress (?ver=)
    if ( strpos( $src, 'ver=' ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    // 2. On supprime le paramètre alternatif utilisé par certains plugins (?version=)
    if ( strpos( $src, 'version=' ) ) {
        $src = remove_query_arg( 'version', $src );
    }
    return $src;
}
add_filter( 'style_loader_src', 'bnr_remove_wp_version_strings', 9999 );
add_filter( 'script_loader_src', 'bnr_remove_wp_version_strings', 9999 );

/**
 * =========================================================================
 * SÉCURITÉ : Nettoyage avancé de la balise <head>
 * =========================================================================
 */

function bnr_cleanup_wp_head() {
    // 1. Supprimer les liens vers les flux RSS (Posts et Commentaires)
    remove_action( 'wp_head', 'feed_links', 2 );
    remove_action( 'wp_head', 'feed_links_extra', 3 );

    // 2. Supprimer les liens de découverte (souvent utilisés par les attaquants pour le XML-RPC)
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );

    // 3. Supprimer le "shortlink" (lien court généré par WP)
    remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

    // 4. Masquer les liens de l'API REST dans le <head> (l'API fonctionne toujours, elle est juste invisible dans le code source)
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
}
// On accroche cette fonction à l'initialisation de WordPress
add_action( 'init', 'bnr_cleanup_wp_head' );

/**
 * =========================================================================
 * SÉCURITÉ : Masquer la version de Yoast SEO
 * =========================================================================
 */
// Désactive les commentaires HTML générés par Yoast SEO du type ""
add_filter( 'wpseo_debug_markers', '__return_false' );