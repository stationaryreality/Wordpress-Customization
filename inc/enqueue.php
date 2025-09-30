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


// Auto-version CSS/JS based on file modification time
function auto_versioned_assets() {

    // Enqueue main stylesheet
    $style_path = get_stylesheet_directory() . '/style.css';
    if (file_exists($style_path)) {
        wp_enqueue_style(
            'theme-style',
            get_stylesheet_uri(),
            array(),
            filemtime($style_path) // Version = last modified time
        );
    }

    // Example: enqueue main JS file
    $script_path = get_stylesheet_directory() . '/js/main.js';
    if (file_exists($script_path)) {
        wp_enqueue_script(
            'theme-main-js',
            get_stylesheet_directory_uri() . '/js/main.js',
            array('jquery'),        // dependencies, adjust as needed
            filemtime($script_path), // Version = last modified time
            true                    // load in footer
        );
    }
}
add_action('wp_enqueue_scripts', 'auto_versioned_assets');
