<?php get_header(); ?>

<?php
// La boucle WordPress (The Loop)
if (have_posts()) :
    while (have_posts()) : the_post();
        ?>
        <article>
            <h2><?php the_title(); ?></h2>
            <div><?php the_content(); ?></div>
        </article>
    <?php
    endwhile;
else :
    echo '<p>Aucun contenu trouvé.</p>';
endif;
?>

<?php get_footer(); ?>