<?php
/**
 * Modèle générique pour afficher les pages standards (Mentions légales, etc.)
 */
get_header(); ?>

    <main id="main-content" class="site-main" role="main" style="max-width: 800px; margin: 60px auto; padding: 0 20px; background: #fff; box-shadow: 0 0 20px rgba(0,0,0,0.05); border-radius: 8px;">

        <?php
        while ( have_posts() ) :
            the_post();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="padding: 40px;">

                <header class="entry-header" style="margin-bottom: 40px; text-align: center;">
                    <h1 class="entry-title" style="color: #2e7d32; font-size: 2.5em; border-bottom: 2px solid #e8f5e9; padding-bottom: 15px; display: inline-block;">
                        <?php the_title(); ?>
                    </h1>
                </header>

                <div class="entry-content" style="line-height: 1.8; color: #444; font-size: 1.1em;">
                    <?php
                    // Affiche le contenu saisi dans l'éditeur de WordPress (ou le résultat d'un shortcode)
                    the_content();
                    ?>
                </div>

            </article>

        <?php
        endwhile;
        ?>

    </main>

<?php get_footer(); ?>