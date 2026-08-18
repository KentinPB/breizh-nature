<?php
/**
 * Modèle pour la page d'accueil personnalisée
 */
get_header(); ?>

    <main id="main-content" class="site-main" role="main">

        <!-- Section Héro (Bannière de présentation) -->
        <section class="hero-section" style="background-color: #e8f5e9; padding: 80px 20px; text-align: center; border-bottom: 5px solid #2e7d32;">
            <h1 class="hero-title" style="color: #2e7d32; font-size: 2.8em; margin-bottom: 20px; text-transform: uppercase;">Bienvenue sur Breizh'Nature</h1>
            <p class="hero-subtitle" style="font-size: 1.3em; max-width: 800px; margin: 0 auto 30px; color: #333;">
                Découvrez le patrimoine naturel de la Bretagne à travers nos randonnées, ateliers pédagogiques et sorties découvertes.
            </p>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>" class="button hero-button" style="background: #2e7d32; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 1.1em; display: inline-block;">
                Explorer nos activités nature
            </a>
        </section>

        <section class="site-purpose">
            <div class="purpose-intro">
                <p class="eyebrow">Une association au service de la nature</p>
                <h2>Pourquoi Breizh'Nature existe</h2>
                <p>
                    Breizh'Nature aide les habitants, les familles et les amoureux de la Bretagne à découvrir,
                    comprendre et protéger le patrimoine naturel de notre région. En proposant des sorties,
                    des ateliers et des moments de sensibilisation, le site donne envie de marcher, d'observer,
                    d'apprendre et d'agir pour la biodiversité.
                </p>
            </div>

            <div class="purpose-grid">
                <article class="purpose-card">
                    <div class="purpose-icon">🌿</div>
                    <h3>Découvrir la nature</h3>
                    <p>Explorer les paysages bretons, identifier les espèces et apprendre à regarder autrement notre environnement.</p>
                </article>

                <article class="purpose-card">
                    <div class="purpose-icon">🤝</div>
                    <h3>Se reconnecter aux autres</h3>
                    <p>Partager des sorties conviviales, rencontrer des passionnés et vivre des expériences collectives enrichissantes.</p>
                </article>

                <article class="purpose-card">
                    <div class="purpose-icon">💚</div>
                    <h3>Agir pour la biodiversité</h3>
                    <p>Comprendre les enjeux écologiques et participer à une démarche concrète de sensibilisation et de protection.</p>
                </article>
            </div>
        </section>

        <section class="how-it-works">
            <div class="how-it-works-header">
                <p class="eyebrow">Comment participer</p>
                <h2>Une simple façon d'entrer dans l'aventure</h2>
            </div>

            <div class="steps-grid">
                <div class="step-item">
                    <span>1</span>
                    <h3>Choisir une sortie</h3>
                    <p>Parcourez les activités proposées selon le type, le niveau et la date.</p>
                </div>
                <div class="step-item">
                    <span>2</span>
                    <h3>Réserver</h3>
                    <p>Inscrivez-vous facilement à une randonnée, un atelier ou une découverte nature.</p>
                </div>
                <div class="step-item">
                    <span>3</span>
                    <h3>Participez</h3>
                    <p>Venez découvrir, apprendre et vivre une expérience positive avec la nature.</p>
                </div>
            </div>
        </section>

        <!-- Section Dernières activités -->
        <section class="latest-activities" style="max-width: 1200px; margin: 60px auto; padding: 0 20px;">
            <h2 style="color: #333; text-align: center; margin-bottom: 50px; font-size: 2em; border-bottom: 2px solid #2e7d32; display: inline-block; padding-bottom: 10px;">Nos prochaines sorties nature</h2>

            <div class="activites-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <?php
                // Requête WP_Query pour récupérer les 3 prochaines activités à venir
                $args = array(
                    'post_type'      => 'activite',
                    'posts_per_page' => 3,
                    'meta_key'       => '_activite_date',
                    'orderby'        => 'meta_value',
                    'order'          => 'ASC',
                    'meta_query'     => array(
                        array(
                            'key'     => '_activite_date',
                            'value'   => date( 'Y-m-d' ),
                            'compare' => '>=',
                            'type'    => 'DATE'
                        )
                    )
                );

                $prochaines_activites = new WP_Query( $args );

                if ( $prochaines_activites->have_posts() ) :
                    while ( $prochaines_activites->have_posts() ) : $prochaines_activites->the_post();

                        $date_debut = get_post_meta( get_the_ID(), '_activite_date', true );
                        $lieu       = get_post_meta( get_the_ID(), '_activite_lieu', true );
                        ?>

                        <article class="activite-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 20px; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <h3 style="margin-top: 0; font-size: 1.4em;">
                                <a href="<?php the_permalink(); ?>" style="color: #2e7d32; text-decoration: none;">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <div style="background: #f1f8f1; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 0.9em;">
                                <?php if ( $date_debut ) : ?>
                                    <span style="display: block; margin-bottom: 5px;">📅 <strong>Date :</strong> <?php echo date( 'd/m/Y', strtotime( $date_debut ) ); ?></span>
                                <?php endif; ?>
                                <?php if ( $lieu ) : ?>
                                    <span style="display: block;">📍 <strong>Lieu :</strong> <?php echo esc_html( $lieu ); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="activite-excerpt" style="color: #555; line-height: 1.5;">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" style="display: inline-block; margin-top: 15px; color: #0073aa; font-weight: bold; text-decoration: none;">Voir les détails →</a>
                        </article>

                    <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p style="text-align: center; color: #666;">Aucune activité prévue pour le moment. Revenez bientôt !</p>';
                endif;
                ?>
            </div>

            <div style="text-align: center; margin-top: 50px;">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>" class="button-secondary" style="color: #333; font-weight: bold; text-decoration: none; border: 2px solid #2e7d32; padding: 10px 20px; border-radius: 4px; transition: all 0.3s;">
                    Voir tout le catalogue d'activités
                </a>
            </div>
        </section>
    </main>

<?php get_footer(); ?>