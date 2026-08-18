<?php
/**
 * Modèle pour afficher la liste des activités (Catalogue avec Filtres)
 */
get_header(); ?>

    <main class="site-main archive-activites">
        <header class="archive-header">
            <h1 class="archive-title">Nos activités nature en Bretagne</h1>
            <p>Trouvez la sortie idéale en utilisant nos filtres ci-dessous.</p>
        </header>

        <!-- 1. Le formulaire de filtrage -->
        <section class="activites-filters">
            <form method="GET" action="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>" class="filtre-form" style="display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap;">

                <!-- Filtre : Type d'activité -->
                <select name="type_activite">
                    <option value="">Tous les types</option>
                    <?php
                    $types = get_terms( array( 'taxonomy' => 'type_activite', 'hide_empty' => true ) );
                    foreach ( $types as $type ) {
                        $selected = ( isset( $_GET['type_activite'] ) && $_GET['type_activite'] === $type->slug ) ? 'selected' : '';
                        echo '<option value="' . esc_attr( $type->slug ) . '" ' . $selected . '>' . esc_html( $type->name ) . '</option>';
                    }
                    ?>
                </select>

                <!-- Filtre : Niveau -->
                <select name="niveau">
                    <option value="">Tous les niveaux</option>
                    <?php
                    $niveaux = get_terms( array( 'taxonomy' => 'niveau_difficulte', 'hide_empty' => true ) );
                    foreach ( $niveaux as $niveau ) {
                        $selected = ( isset( $_GET['niveau'] ) && $_GET['niveau'] === $niveau->slug ) ? 'selected' : '';
                        echo '<option value="' . esc_attr( $niveau->slug ) . '" ' . $selected . '>' . esc_html( $niveau->name ) . '</option>';
                    }
                    ?>
                </select>

                <!-- Filtre : Date (À partir de) -->
                <input type="date" name="date_activite" value="<?php echo isset( $_GET['date_activite'] ) ? esc_attr( $_GET['date_activite'] ) : ''; ?>" title="Activités à partir de cette date">

                <button type="submit" class="button">Rechercher</button>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>" class="button button-secondary">Réinitialiser</a>
            </form>
        </section>

        <!-- 2. Traitement de la requête et affichage des résultats -->
        <div class="activites-grid">
            <?php
            // Préparation des filtres de la requête
            $args = array(
                    'post_type'      => 'activite',
                    'posts_per_page' => 10,
                    'tax_query'      => array( 'relation' => 'AND' ),
                    'meta_query'     => array( 'relation' => 'AND' ),
            );

            // Si l'utilisateur a choisi un type
            if ( ! empty( $_GET['type_activite'] ) ) {
                $args['tax_query'][] = array(
                        'taxonomy' => 'type_activite',
                        'field'    => 'slug',
                        'terms'    => sanitize_text_field( $_GET['type_activite'] ),
                );
            }

            // Si l'utilisateur a choisi un niveau
            if ( ! empty( $_GET['niveau'] ) ) {
                $args['tax_query'][] = array(
                        'taxonomy' => 'niveau_difficulte',
                        'field'    => 'slug',
                        'terms'    => sanitize_text_field( $_GET['niveau'] ),
                );
            }

            // Si l'utilisateur a choisi une date (affiche les activités égales ou ultérieures à la date choisie)
            if ( ! empty( $_GET['date_activite'] ) ) {
                $args['meta_query'][] = array(
                        'key'     => '_activite_date',
                        'value'   => sanitize_text_field( $_GET['date_activite'] ),
                        'compare' => '>=',
                        'type'    => 'DATE'
                );
            }

            // Exécution de la nouvelle requête filtrée
            $query_filtree = new WP_Query( $args );

            if ( $query_filtree->have_posts() ) :
                while ( $query_filtree->have_posts() ) : $query_filtree->the_post();

                    $lieu  = get_post_meta( get_the_ID(), '_activite_lieu', true );
                    $tarif = get_post_meta( get_the_ID(), '_activite_tarif', true );
                    ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('activite-card'); ?> style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                        <h2 class="activite-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                        <div class="activite-meta-preview">
                            <?php
                            // Affichage visuel des taxonomies de l'activité
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

                // Pagination de notre requête personnalisée
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

                wp_reset_postdata(); // Toujours réinitialiser la requête après un WP_Query personnalisé

            else :
                echo '<p>Désolé, aucune activité ne correspond à vos critères de recherche.</p>';
            endif;
            ?>
        </div>
    </main>

<?php get_footer(); ?>