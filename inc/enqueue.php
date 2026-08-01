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

// ==========================================
// LEGACY CSS LOADER (Toggleable for Testing)
// ==========================================
// Set to false to disable legacy CSS and test the new unified system.
// Once migration is complete and verified, this entire block can be safely deleted.
define('ENABLE_LEGACY_CSS', true); 

if (defined('ENABLE_LEGACY_CSS') && ENABLE_LEGACY_CSS) {
    $legacy_css_files = [
        'profiles',
        'grids',
        'videos',
        'references',
        'tools',
        'elements',
        'image-grid'
    ];

    foreach ($legacy_css_files as $file) {
        $file_path = get_stylesheet_directory() . "/assets/legacycss/{$file}.css";
        
        // Only enqueue if the file actually exists to prevent errors
        if (file_exists($file_path)) {
            wp_enqueue_style(
                'legacy-' . $file,
                get_stylesheet_directory_uri() . "/assets/legacycss/{$file}.css",
                [],
                filemtime($file_path)
            );
        }
    }
}

// === KEYBOARD NAVIGATION SCRIPT ===
function enqueue_keyboard_navigation() {
    // Only load on single Image or Concept pages
        if (is_singular(['image', 'concept', 'artist', 'book', 'element', 'excerpt', 
        'game', 'movie', 'show', 'lyric', 'organization', 'profile', 'quote', 'song', 'video'])) {
            wp_enqueue_script(
            'keyboard-nav',
            get_stylesheet_directory_uri() . '/assets/js/keyboard-nav.js',
            [],
            filemtime(get_stylesheet_directory() . '/assets/js/keyboard-nav.js'),
            true // Load in footer
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_keyboard_navigation');