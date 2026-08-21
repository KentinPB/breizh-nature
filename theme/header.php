<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php wp_head(); // Fonction vitale pour charger les scripts et le SEO de WordPress ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content" style="position:absolute; left:-10000px; top:auto; width:1px; height:1px; overflow:hidden;">Aller au contenu principal</a>

<header class="site-header" role="banner">
    <div class="header-container" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #eaeaea;">

        <div class="site-logo" style="display: flex; align-items: center; gap: 15px;">
            <?php
            // 1. On affiche le logo personnalisé s'il a été défini dans l'administration
            if ( has_custom_logo() ) {
                the_custom_logo();
            }

            // 2. On affiche TOUJOURS le titre du site juste à côté
            ?>
            <h1 style="margin: 0;">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration:none; color:#2e7d32;">
                    <?php bloginfo( 'name' ); ?>
                </a>
            </h1>
        </div>

        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Menu principal">
            <?php
            wp_nav_menu( array(
                    'theme_location' => 'menu-principal',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
            ) );
            ?>
        </nav>

    </div>
</header>

<div id="content" class="site-content">