<?php
/**
 * Modèle pour afficher la liste des activités (Catalogue)
 */
get_header(); ?>

    <main class="site-main archive-activites">
        <header class="archive-header">
            <h1 class="archive-title">Nos activités nature en Bretagne</h1>
            <p>Découvrez nos randonnées, ateliers et sorties nature.</p>
        </header>

        <div class="activites-grid">
            <?php
            // La boucle WordPress pour les archives
            if (have_posts()) :
                while (have_posts()) : the_post();

                    // Récupération de quelques infos clés à afficher sur la "carte" de l'activité
                    $lieu = get_post_meta(get_the_ID(), '_activite_lieu', true);
                    $tarif = get_post_meta(get_the_ID(), '_activite_tarif', true);
                    ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('activite-card'); ?>>
                        <a href="<?php the_permalink(); ?>" class="activite-link">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="activite-thumbnail">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                            <?php endif; ?>

                            <h2 class="activite-title"><?php the_title(); ?></h2>
                        </a>

                        <div class="activite-summary">
                            <?php the_excerpt(); ?>
                        </div>

                        <div class="activite-meta-preview">
                            <?php if ($lieu) : ?><span>📍 <?php echo esc_html($lieu); ?></span><?php endif; ?>
                            <?php if ($tarif) : ?><span>💶 <?php echo esc_html($tarif); ?> €</span><?php endif; ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="btn-readmore">Voir les détails</a>
                    </article>

                <?php
                endwhile;

                // Système de pagination natif (Un bonus très apprécié par les jurys !)
                the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => 'Précédent',
                        'next_text' => 'Suivant',
                ));

            else :
                echo '<p>Aucune activité n\'est programmée pour le moment.</p>';
            endif;
            ?>
        </div>
    </main>

<?php get_footer(); ?>