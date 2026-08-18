<?php
/**
 * Modèle pour afficher le détail d'une activité nature
 */
get_header(); ?>

    <main class="site-main site-activite">
        <?php
        // Début de la boucle WordPress
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('activite-detail'); ?>>

                <header class="activite-header">
                    <h1 class="activite-title"><?php the_title(); ?></h1>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="activite-image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                </header>

                <div class="activite-content-wrapper">

                    <!-- Colonne de gauche : La description complète -->
                    <div class="activite-description">
                        <h2>Description de l'activité</h2>
                        <?php the_content(); ?>
                    </div>

                    <!-- Colonne de droite : Les informations pratiques (Méta-données) -->
                    <aside class="activite-infos">
                        <h3>Informations pratiques</h3>
                        <ul>
                            <?php
                            // Récupération des futurs champs personnalisés (post meta)
                            $date = get_post_meta(get_the_ID(), '_activite_date', true);
                            $heure = get_post_meta(get_the_ID(), '_activite_heure', true);
                            $duree = get_post_meta(get_the_ID(), '_activite_duree', true);
                            $lieu = get_post_meta(get_the_ID(), '_activite_lieu', true);
                            $difficulte = get_post_meta(get_the_ID(), '_activite_difficulte', true);
                            $places = get_post_meta(get_the_ID(), '_activite_places', true);
                            $tarif = get_post_meta(get_the_ID(), '_activite_tarif', true);
                            ?>

                            <?php if ($date) : ?>
                                <li><strong>Date :</strong> <?php echo esc_html($date); ?></li><?php endif; ?>
                            <?php if ($heure) : ?>
                                <li><strong>Heure :</strong> <?php echo esc_html($heure); ?></li><?php endif; ?>
                            <?php if ($duree) : ?>
                                <li><strong>Durée :</strong> <?php echo esc_html($duree); ?></li><?php endif; ?>
                            <?php if ($lieu) : ?>
                                <li><strong>Lieu :</strong> <?php echo esc_html($lieu); ?></li><?php endif; ?>
                            <?php if ($difficulte) : ?>
                                <li><strong>Niveau :</strong> <?php echo esc_html($difficulte); ?></li><?php endif; ?>
                            <?php if ($places) : ?>
                                <li><strong>Places max :</strong> <?php echo esc_html($places); ?></li><?php endif; ?>
                            <?php if ($tarif) : ?>
                                <li><strong>Tarif :</strong> <?php echo esc_html($tarif); ?> €</li><?php endif; ?>
                        </ul>

                        <!-- Emplacement pour le futur formulaire de réservation du plugin -->
                        <div class="activite-reservation-box">
                            <?php
                            // C'est ici que notre futur plugin injectera le formulaire !
                            // Pour l'instant, on laisse un espace vide ou un commentaire.
                            echo '<!-- Formulaire de réservation (généré par le plugin) -->';
                            ?>
                        </div>
                    </aside>

                </div>
            </article>
        <?php
        endwhile; // Fin de la boucle
        ?>
    </main>

<?php get_footer(); ?>