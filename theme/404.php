<?php
/**
 * Modèle pour afficher la page d'erreur 404 (Introuvable)
 */
get_header(); ?>

    <main id="main-content" class="site-main" role="main" style="max-width: 1000px !important; margin: 80px auto !important; padding: 60px 40px 80px !important; text-align: center; background: #fff; box-shadow: 0 18px 32px rgba(19, 48, 33, 0.05); border-radius: 18px; border-top: 5px solid #2e7d32;">

        <div class="error-404 not-found">
            <header class="page-header" style="margin-bottom: 30px;">
                <h1 class="page-title" style="color: #2e7d32; font-size: 5em; margin: 0; font-weight: 900; line-height: 1;">404</h1>
                <h2 style="color: #1d2a22; font-size: 2.2em; margin-top: 10px;">Oups ! Vous vous êtes égaré en dehors des sentiers balisés.</h2>
            </header>

            <div class="page-content" style="color: #355046; font-size: 1.15em; line-height: 1.8; margin-bottom: 50px; padding: 0 20px;">
                <p>La page que vous recherchez a peut-être été déplacée, supprimée, ou n'a tout simplement jamais existé.</p>
                <p>Pas d'inquiétude, vous pouvez reprendre votre exploration grâce aux liens ci-dessous :</p>
            </div>

            <div class="404-actions" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button" style="background: linear-gradient(135deg, #2e7d32 0%, #3da35d 100%); color: #fff; padding: 14px 28px; text-decoration: none; border-radius: 999px; font-weight: bold; box-shadow: 0 10px 18px rgba(46,125,50,0.18); transition: transform 0.2s ease;">
                    🏠 Retour à l'accueil
                </a>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>" class="button-secondary" style="background: transparent; color: #2e7d32; border: 2px solid rgba(46,125,50,0.45); padding: 14px 28px; text-decoration: none; border-radius: 999px; font-weight: bold; transition: background 0.2s ease;">
                    🥾 Voir nos activités nature
                </a>
            </div>
        </div>

    </main>

<?php get_footer(); ?>