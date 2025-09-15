<?php
// inc/footnotes.php
// ===================================
// Footnotes / Notes & References System (loader)
// ===================================

// Load modular footnote parts
foreach (glob(__DIR__ . '/footnotes/*.php') as $file) {
    require_once $file;
}

/**
 * Top-level shortcode renderer.
 * Calls modular functions (if present) to render each group.
 */
function display_referenced_works() {
    ob_start();
    $chapter_id = get_the_ID();

    echo '<div class="referenced-works">';
    echo '<h3 style="font-weight:bold;margin-top:2em;">Notes & References</h3>';

    // Group title metadata (keeps output identical to previous file)
    $group_titles = [
      'featured_artists'      => ['title' => 'Songs Featured',            'emoji' => '🎤', 'link' => '/artists-featured/'],
      'other_artists'         => ['title' => 'Songs Referenced',          'emoji' => '🎤', 'link' => '/artists-featured/'],
      'songs_referenced'      => ['title' => 'Songs Excerpts',            'emoji' => '🎵', 'link' => '/song-excerpts/'],
      'profile'               => ['title' => 'People Referenced',         'emoji' => '👤', 'link' => '/people-referenced/'],
      'lyric'                 => ['title' => 'Song Excerpts',             'emoji' => '🎵', 'link' => '/song-excerpts/'],
      'quote'                 => ['title' => 'Quote Library',             'emoji' => '💬', 'link' => '/quote-library/'],
      'concept'               => ['title' => 'Lexicon',                   'emoji' => '🔎', 'link' => '/lexicon/'],
      'book'                  => ['title' => 'Books Cited',               'emoji' => '📚', 'link' => '/books-cited/'],
      'movie'                 => ['title' => 'Movies Referenced',         'emoji' => '🎬', 'link' => '/movies-referenced/'],
      'reference'             => ['title' => 'Other References',          'emoji' => '📰', 'link' => '/research-sources/'],
      'theme'                 => ['title' => 'Themes',                    'emoji' => '🎨', 'link' => '/themes/'],
      'organizations'         => ['title' => 'Organizations Referenced',  'emoji' => '🏢', 'link' => '/organizations/'],
      'image'                 => ['title' => 'Images Referenced',         'emoji' => '🖼', 'link' => '/image-gallery/'],
      'excerpt'               => ['title' => 'Excerpts Referenced',       'emoji' => '📖', 'link' => '/excerpt-library/'],
    ];

    // Module functions to call (in display order).
    // If a module file isn't present, it's safely skipped.
    $modules = [
        'fn_featured_artists',
        'fn_other_artists',
        'fn_people',
        'fn_books',
        'fn_movies',
        'fn_quotes',
        'fn_lyrics',
        'fn_excerpts',
        'fn_organizations',
        'fn_references',
        'fn_images',
        'fn_themes',
        'fn_concepts',
        'fn_videos',
    ];

    foreach ($modules as $fn) {
        if (function_exists($fn)) {
            // Each module returns HTML (or empty string)
            echo call_user_func($fn, $chapter_id, $group_titles);
        }
    }

    echo '</div>'; // .referenced-works
    return ob_get_clean();
}

add_shortcode('referenced_works', 'display_referenced_works');
