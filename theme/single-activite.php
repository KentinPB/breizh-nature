<?php
/**
 * Modèle pour afficher le détail d'une activité (Single)
 */
get_header(); ?>

    <main id="main-content" class="site-main single-activite-main" role="main">
        <?php
        while ( have_posts() ) :
            the_post();

            $date       = get_post_meta( get_the_ID(), '_activite_date', true );
            $heure      = get_post_meta( get_the_ID(), '_activite_heure', true );
            $duree      = get_post_meta( get_the_ID(), '_activite_duree', true );
            $lieu       = get_post_meta( get_the_ID(), '_activite_lieu', true );
            $tarif      = get_post_meta( get_the_ID(), '_activite_tarif', true );
            $places_max = get_post_meta( get_the_ID(), '_activite_places', true );
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('single-activite-article'); ?>>

                <header class="entry-header single-activite-header">
                    <h1 class="entry-title single-activite-title"><?php the_title(); ?></h1>

                    <div class="taxonomies-badges">
                        <span class="badge badge-green">🏷️ <?php echo strip_tags( get_the_term_list( get_the_ID(), 'type_activite', '', ', ' ) ); ?></span>
                        <span class="badge badge-yellow">⭐ <?php echo strip_tags( get_the_term_list( get_the_ID(), 'niveau_difficulte', '', ', ' ) ); ?></span>
                    </div>
                </header>

                <div class="single-activite-layout">

                    <!-- Colonne de gauche (Contenu) -->
                    <div class="single-activite-content">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="activite-image">
                                <?php the_post_thumbnail( 'large' ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="entry-content single-activite-copy">
                            <h2>Présentation de la sortie</h2>
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <!-- Colonne de droite (Sidebar Sticky) -->
                    <aside class="single-activite-sidebar">
                        <div class="infos-pratiques info-panel">
                            <h3>Informations pratiques</h3>
                            <ul>
                                <li>📅 <strong>Date :</strong> <?php echo $date ? date( 'd/m/Y', strtotime( $date ) ) : 'Non définie'; ?></li>
                                <li>⏰ <strong>Heure :</strong> <?php echo esc_html( $heure ); ?></li>
                                <li>⏳ <strong>Durée :</strong> <?php echo esc_html( $duree ); ?></li>
                                <li>📍 <strong>Lieu :</strong> <?php echo esc_html( $lieu ); ?></li>
                                <li>👥 <strong>Capacité :</strong> <?php echo esc_html( $places_max ); ?> places max</li>
                                <li>💶 <strong>Tarif :</strong> <?php echo $tarif ? esc_html( $tarif ) . ' €' : 'Gratuit'; ?></li>
                            </ul>
                        </div>

                        <div class="reservation-box info-panel">
                            <h3>Réserver ma place</h3>
                            <?php echo do_shortcode( '[bnr_reservation]' ); ?>
                        </div>
                    </aside>

                </div>
            </article>

        <?php endwhile; ?>
    </main>

<?php get_footer(); ?>