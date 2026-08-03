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
// 1. PERMANENT CSS LOADER (assets/css/)
// ==========================================
function enqueue_css_files() {
    $base_path = get_stylesheet_directory() . '/assets/css';
    $base_uri  = get_stylesheet_directory_uri() . '/assets/css';
    
    $folders = [
        'global'     => glob("{$base_path}/global/*.css"),
        'cpt'        => glob("{$base_path}/cpt/*.css"),
        'pages'      => glob("{$base_path}/pages/*.css"),
        'components' => glob("{$base_path}/components/*.css"),
        'footnotes'  => glob("{$base_path}/components/footnotes/*.css"),
        'tools'      => glob("{$base_path}/components/tools/*.css"),
        'admin'      => glob("{$base_path}/admin/*.css"),
    ];
    
    foreach ($folders as $folder_name => $files) {
        if (is_array($files)) {
            foreach ($files as $file_path) {
                $file_name = basename($file_path);
                $handle = 'css-' . $folder_name . '-' . basename($file_name, '.css');
                $relative_path = str_replace($base_path . '/', '', $file_path);
                wp_enqueue_style($handle, "{$base_uri}/{$relative_path}", [], filemtime($file_path));
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'enqueue_css_files');


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