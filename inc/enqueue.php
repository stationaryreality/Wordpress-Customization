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

//css files loader
$css_files = [
    'wordpress-overrides',
    'navigation',
    'profiles',
    'grids',        // ← global grid rules
    'videos',
    'references',
    'tools',
    'misc',
    'elements',     // ← Elements overrides (loaded after grids)
    'image-grid'    // ← Image overrides (loaded last)
];

foreach ($css_files as $file) {
    wp_enqueue_style(
        $file,
        get_stylesheet_directory_uri() . "/assets/css/{$file}.css",
        [],
        filemtime(get_stylesheet_directory() . "/assets/css/{$file}.css")
    );
}








wp_enqueue_style('component-footnotes-artists', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/artists.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/artists.css'));
wp_enqueue_style('component-footnotes-books', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/books.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/books.css'));
wp_enqueue_style('component-footnotes-concepts', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/concepts.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/concepts.css'));

// For the Video CPT footnotes
wp_enqueue_style('component-footnotes-cpt-videos', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/cpt-videos.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/cpt-videos.css'));

// For the Song-attached video footnotes
wp_enqueue_style('component-footnotes-song-videos', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/song-videos.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/song-videos.css'));

wp_enqueue_style('component-footnotes-elements', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/elements.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/elements.css'));


wp_enqueue_style('component-footnotes-excerpts', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/excerpts.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/excerpts.css'));
wp_enqueue_style('component-footnotes-games', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/games.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/games.css'));

wp_enqueue_style('component-footnotes-images', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/images.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/images.css'));
wp_enqueue_style('component-footnotes-lyrics', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/lyrics.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/lyrics.css'));

wp_enqueue_style('component-footnotes-movies', get_stylesheet_directory_uri() . '/assets/css/components/footnotes/movies.css', [], filemtime(get_stylesheet_directory() . '/assets/css/components/footnotes/movies.css'));



