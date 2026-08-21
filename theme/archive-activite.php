<?php
/**
 * Modèle pour afficher la liste des activités (Catalogue avec Filtres AJAX et Badges d'état)
 */
get_header(); ?>

    <main class="site-main archive-activites">
        <header class="archive-header">
            <h1 class="archive-title">Nos activités nature en Bretagne</h1>
            <p>Trouvez la sortie idéale en utilisant nos filtres ci-dessous.</p>
        </header>

        <section class="activites-filters">
            <form id="activites-filter-form" method="GET" action="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>" class="filtre-form">
                <div class="filter-field filter-select">
                    <label for="filter-type">Type</label>
                    <select id="filter-type" name="type_activite">
                        <option value="">Tous les types</option>
                        <?php
                        $types = get_terms( array( 'taxonomy' => 'type_activite', 'hide_empty' => true ) );
                        foreach ( $types as $type ) {
                            $selected = ( isset( $_GET['type_activite'] ) && $_GET['type_activite'] === $type->slug ) ? 'selected' : '';
                            echo '<option value="' . esc_attr( $type->slug ) . '" ' . $selected . '>' . esc_html( $type->name ) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="filter-field filter-select">
                    <label for="filter-niveau">Niveau</label>
                    <select id="filter-niveau" name="niveau">
                        <option value="">Tous les niveaux</option>
                        <?php
                        $niveaux = get_terms( array( 'taxonomy' => 'niveau_difficulte', 'hide_empty' => true ) );
                        foreach ( $niveaux as $niveau ) {
                            $selected = ( isset( $_GET['niveau'] ) && $_GET['niveau'] === $niveau->slug ) ? 'selected' : '';
                            echo '<option value="' . esc_attr( $niveau->slug ) . '" ' . $selected . '>' . esc_html( $niveau->name ) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="filter-field filter-date">
                    <label for="filter-date">Date</label>
                    <input id="filter-date" type="date" name="date_activite" value="<?php echo isset( $_GET['date_activite'] ) ? esc_attr( $_GET['date_activite'] ) : ''; ?>" title="Activités à partir de cette date">
                </div>

                <label class="filter-checkbox">
                    <input type="checkbox" name="afficher_terminees" value="1" <?php checked( isset( $_GET['afficher_terminees'] ) ); ?>>
                    Afficher les activités terminées
                </label>

                <div class="filter-actions">
                    <button type="submit" class="button">Rechercher</button>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>" class="button button-secondary">Réinitialiser</a>
                </div>
            </form>
        </section>

        <div id="activites-results-container" class="activites-grid">
            <?php
            $args = array(
                    'post_type'      => 'activite',
                    'posts_per_page' => 10,
                    'tax_query'      => array( 'relation' => 'AND' ),
                    'meta_query'     => array( 'relation' => 'AND' ),
            );

            if ( ! empty( $_GET['type_activite'] ) ) {
                $args['tax_query'][] = array( 'taxonomy' => 'type_activite', 'field' => 'slug', 'terms' => sanitize_text_field( $_GET['type_activite'] ) );
            }
            if ( ! empty( $_GET['niveau'] ) ) {
                $args['tax_query'][] = array( 'taxonomy' => 'niveau_difficulte', 'field' => 'slug', 'terms' => sanitize_text_field( $_GET['niveau'] ) );
            }

            $aujourdhui = date('Y-m-d');
            if ( ! empty( $_GET['date_activite'] ) ) {
                $args['meta_query'][] = array( 'key' => '_activite_date', 'value' => sanitize_text_field( $_GET['date_activite'] ), 'compare' => '>=', 'type' => 'DATE' );
            } elseif ( empty( $_GET['afficher_terminees'] ) ) {
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

                    <article id="post-<?php the_ID(); ?>" <?php post_class('activite-card'); ?>>

                        <!-- L'IMAGE ET SON BADGE INTÉGRÉ -->
                        <div class="activite-card-image">

                            <!-- LE BADGE -->
                            <span class="activite-badge" style="background-color: <?php echo esc_attr( $badge_color ); ?>;">
            <?php echo esc_html( $badge_text ); ?>
        </span>

                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium' ); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>" style="display:flex; width:100%; height:100%; align-items:center; justify-content:center; text-decoration:none;">
                                    <span style="font-size: 4rem;">🌿</span>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- LE CONTENU -->
                        <div class="activite-card-content">
                            <h2 class="activite-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

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

                            <div class="activite-summary">
                                <div class="activite-excerpt"><?php the_excerpt(); ?></div>
                                <a href="<?php the_permalink(); ?>" class="btn-readmore">Voir les détails</a>
                            </div>
                        </div>
                    </article>

                <?php
                endwhile;

                $total_pages = $query_filtree->max_num_pages;
                if ($total_pages > 1) {
                    $current_page = max(1, get_query_var('paged'));
                    echo paginate_links(array(
                            'base' => get_pagenum_link(1) . '%_%',
                            'format' => 'page/%#%',
                            'current' => $current_page,
                            'total' => $total_pages,
                    ));
                }
                wp_reset_postdata();

            else :
                echo '<p>Désolé, aucune activité ne correspond à vos critères de recherche.</p>';
            endif;
            ?>
        </div>
    </main>

<?php get_footer(); ?>