<?php
/**
 * Enregistrement des scripts et styles
 */
function breizh_nature_scripts() {
    // Style principal
    wp_enqueue_style( 'breizh-nature-style', get_stylesheet_uri() );

    // Script AJAX
    wp_enqueue_script( 'breizh-ajax-filter', get_template_directory_uri() . '/assets/js/ajax-filter.js', array(), '1.0', true );

    // Passage des variables vitales de PHP vers notre script JavaScript
    wp_localize_script( 'breizh-ajax-filter', 'breizh_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),           // L'URL du routeur AJAX de WordPress
        'nonce'    => wp_create_nonce( 'breizh_filter_nonce' ) // Sécurité anti-CSRF
    ));
}
add_action( 'wp_enqueue_scripts', 'breizh_nature_scripts' );

/**
 * Traitement AJAX pour le filtrage dynamique des activités
 */
function bnr_ajax_filter_activites() {
    // Vérification stricte du jeton de sécurité[cite: 1]
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
    if ( ! empty( $_POST['date_activite'] ) ) {
        $args['meta_query'][] = array( 'key' => '_activite_date', 'value' => sanitize_text_field( $_POST['date_activite'] ), 'compare' => '>=', 'type' => 'DATE' );
    }

    $query_filtree = new WP_Query( $args );

    if ( $query_filtree->have_posts() ) :
        while ( $query_filtree->have_posts() ) : $query_filtree->the_post();
            $lieu  = get_post_meta( get_the_ID(), '_activite_lieu', true );
            $tarif = get_post_meta( get_the_ID(), '_activite_tarif', true );
            ?>
            <article class="activite-card" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                <h2 class="activite-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="activite-meta-preview">
                    <?php
                    echo '<span>🏷️ ' . strip_tags( get_the_term_list( get_the_ID(), 'type_activite', '', ', ' ) ) . '</span> | ';
                    echo '<span>⭐ ' . strip_tags( get_the_term_list( get_the_ID(), 'niveau_difficulte', '', ', ' ) ) . '</span>';
                    ?>
                    <br>
                    <?php if ( $lieu ) : ?><span>📍 <?php echo esc_html( $lieu ); ?></span><?php endif; ?>
                    <?php if ( $tarif ) : ?><span>💶 <?php echo esc_html( $tarif ); ?> €</span><?php endif; ?>
                </div>
                <div class="activite-summary" style="margin-top: 10px;">
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
    wp_die(); // Obligatoire pour terminer proprement une requête AJAX WordPress
}
// On accroche la fonction pour les utilisateurs connectés ET déconnectés
add_action( 'wp_ajax_filter_activites', 'bnr_ajax_filter_activites' );
add_action( 'wp_ajax_nopriv_filter_activites', 'bnr_ajax_filter_activites' );