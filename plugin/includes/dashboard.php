<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * 1. Déclaration d'un widget personnalisé sur le tableau de bord principal de WordPress
 */
function bnr_add_dashboard_widgets() {
    wp_add_dashboard_widget(
        'bnr_stats_widget',                   // ID du widget
        'Statistiques Breizh\'Nature',        // Titre affiché
        'bnr_render_dashboard_widget'         // Fonction d'affichage
    );
}
add_action( 'wp_dashboard_setup', 'bnr_add_dashboard_widgets' );

/**
 * 2. Calcul et affichage des statistiques requises
 */
function bnr_render_dashboard_widget() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';

    // Statistique 1 : Nombre total d'activités publiées
    $total_activites = wp_count_posts( 'activite' )->publish;
    if ( empty($total_activites) ) $total_activites = 0;

    // Statistique 2 : Nombre d'activités à venir (Date supérieure ou égale à aujourd'hui)
    $args_avenir = array(
        'post_type'   => 'activite',
        'post_status' => 'publish',
        'meta_query'  => array(
            array(
                'key'     => '_activite_date',
                'value'   => date('Y-m-d'),
                'compare' => '>=',
                'type'    => 'DATE'
            )
        )
    );
    $query_avenir = new WP_Query( $args_avenir );
    $activites_avenir = $query_avenir->found_posts;

    // Statistique 3 : Réservations en attente (Requête SQL directe ultra-rapide)[cite: 1]
    $resa_attente = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'En attente'" );

    // Statistique 4 : Réservations acceptées[cite: 1]
    $resa_acceptees = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'Acceptée'" );

    // Affichage HTML du Widget
    ?>
    <div class="bnr-dashboard-stats" style="font-size: 14px;">
        <p><strong>📍 Côté Catalogue :</strong></p>
        <ul style="margin-left: 20px; list-style-type: square;">
            <li>Total des activités créées : <strong><?php echo esc_html( $total_activites ); ?></strong></li>
            <li>Activités à venir : <strong><?php echo esc_html( $activites_avenir ); ?></strong></li>
        </ul>

        <p><strong>📅 Côté Réservations :</strong></p>
        <ul style="margin-left: 20px; list-style-type: square;">
            <li>Demandes en attente : <strong style="color: orange;"><?php echo esc_html( $resa_attente ); ?></strong></li>
            <li>Réservations acceptées : <strong style="color: green;"><?php echo esc_html( $resa_acceptees ); ?></strong></li>
        </ul>

        <hr>
        <a href="<?php echo esc_url( admin_url('edit.php?post_type=activite&page=bnr-reservations') ); ?>" class="button button-primary">Gérer les réservations</a>
    </div>
    <?php
}