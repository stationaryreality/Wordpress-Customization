<?php
/**
 * Redirects for CPT archives, breadcrumbs, and taxonomy
 */

// CPT archive redirects
add_action('template_redirect', function () {
    $redirects = [
        'book'         => '/books-cited/',
        'artist'       => '/artists-featured/',
        'profile'      => '/people-referenced/',
        'concept'      => '/lexicon/',
        'movie'        => '/movies-referenced/',
        'quote'        => '/quote-library/',
        'reference'    => '/research-sources/',
        'lyric'        => '/song-excerpts/',
        'organization' => '/organizations/',
        'song'         => '/songs-featured/',
        'image'        => '/image-gallery/',
        'excerpt'      => '/excerpt-library/',
        'chapter'      => '/#narrative-threads',
    ];

    foreach ($redirects as $cpt => $url) {
        if (is_post_type_archive($cpt)) {
            wp_redirect(home_url($url), 301);
            exit;
        }
    }

    // handle paginated chapter URLs (/chapters/page/2, /chapters/page/3, etc.)
    if (is_post_type_archive('chapter') && is_paged()) {
        wp_redirect(home_url('/#chapters'), 301);
        exit;
    }
});

// Taxonomy redirect (theme archive root → /themes/)
add_action('template_redirect', function () {
    if (is_tax('theme')) {
        $term = get_queried_object();

        // If no specific term, redirect the taxonomy root
        if (empty($term->slug)) {
            wp_redirect(home_url('/themes/'), 301);
            exit;
        }
    }
});
