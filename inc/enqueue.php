<?php
/**
 * Enqueue styles and fonts for child theme.
 */

// Enqueue parent + child theme styles
function ct_author_child_enqueue_styles() {
    wp_enqueue_style(
        'author-parent-style',
        get_template_directory_uri() . '/style.css'
    );

    wp_enqueue_style(
        'author-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        ['author-parent-style']
    );
}
add_action('wp_enqueue_scripts', 'ct_author_child_enqueue_styles');

// Disable Google Fonts from parent theme
function child_theme_remove_google_fonts() {
    wp_dequeue_style('author-fonts');
    wp_deregister_style('author-fonts');
}
add_action('wp_enqueue_scripts', 'child_theme_remove_google_fonts', 20);

// Enqueue custom fonts
function child_theme_enqueue_custom_fonts() {
    wp_enqueue_style(
        'child-custom-fonts',
        get_stylesheet_directory_uri() . '/fonts/fonts.css',
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'child_theme_enqueue_custom_fonts');
