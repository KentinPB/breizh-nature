<?php
/**
 * Fonctions et définitions du thème Breizh'Nature
 */

if (!function_exists('breizh_nature_setup')) :
    function breizh_nature_setup()
    {
        // Ajoute le support des titres de page dynamiques
        add_theme_support('title-tag');

        // Ajoute le support des images mises en avant (très important pour vos activités)
        add_theme_support('post-thumbnails');

        // Enregistre les zones de menus
        register_nav_menus(array(
            'menu-principal' => esc_html__('Menu Principal', 'breizh-nature'),
        ));

        // Support du logo personnalisé
        add_theme_support('custom-logo', array(
            'height' => 100,
            'width' => 400,
            'flex-width' => true,
            'flex-height' => true,
        ));
    }
endif;
add_action('after_setup_theme', 'breizh_nature_setup');

/**
 * Enregistrement des scripts et styles
 */
function breizh_nature_scripts()
{
    // Chargement du style.css principal
    wp_enqueue_style('breizh-nature-style', get_stylesheet_uri());
}

add_action('wp_enqueue_scripts', 'breizh_nature_scripts');