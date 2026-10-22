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
// SMART CONDITIONAL CSS LOADER
// ==========================================
function smart_conditional_css_loader() {
    if (is_admin()) return; // Never run in admin

    $base_path = get_stylesheet_directory() . '/assets/css';
    $base_uri  = get_stylesheet_directory_uri() . '/assets/css';

    // Helper to enqueue safely
    $enqueue = function($handle, $file) use ($base_path, $base_uri) {
        $uri = str_replace($base_path, $base_uri, $file);
        wp_enqueue_style($handle, $uri, [], filemtime($file));
    };

    // 1. GLOBAL (Always load on standard pages)
    $global_files = glob("{$base_path}/global/*.css");
    if ($global_files) {
        foreach ($global_files as $file) $enqueue('global-' . basename($file, '.css'), $file);
    }

    // 2. SHARED COMPONENTS (Load on all standard pages)
    // Since Listening Room skips wp_head(), it naturally ignores these.
    $shared_folders = ['components', 'components/footnotes', 'components/tools', 'components/portals'];
    foreach ($shared_folders as $folder) {
        $files = glob("{$base_path}/{$folder}/*.css");
        if ($files) {
            foreach ($files as $file) $enqueue('shared-' . basename($file, '.css'), $file);
        }
    }

    // 3. PAGES (Conditional based on page slug matching filename)
    $page_files = glob("{$base_path}/pages/*.css");
    if ($page_files) {
        foreach ($page_files as $file) {
            $slug = basename($file, '.css');
            $load = false;
            
            // Map specific slugs to their WP conditionals
            if ($slug === 'home' && (is_front_page() || is_page('home'))) $load = true;
            elseif ($slug === 'listening-room' && (is_page('listening-room') || is_page_template('template-listening-room-full.php'))) $load = true;
            elseif (is_page($slug)) $load = true; // Fallback for any other page slug
            
            if ($load) $enqueue('page-' . $slug, $file);
        }
    }

    // 4. CPTs (Conditional based on post type matching filename)
    $cpt_files = glob("{$base_path}/cpt/*.css");
    if ($cpt_files) {
        foreach ($cpt_files as $file) {
            $slug = basename($file, '.css');
            if ($slug === 'artist-heads') {
                if (is_singular('artist') || is_post_type_archive('artist')) $enqueue('cpt-' . $slug, $file);
            } else {
                if (is_singular($slug) || is_post_type_archive($slug)) $enqueue('cpt-' . $slug, $file);
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'smart_conditional_css_loader');


// === KEYBOARD NAVIGATION SCRIPT ===
function enqueue_keyboard_navigation() {
    // Only load on single Image or Concept pages
        if (is_singular(['image', 'concept', 'artist', 'book', 'chapter', 'fragment', 'element', 'excerpt', 
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