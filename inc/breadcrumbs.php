<?php
// inc/breadcrumbs.php

// === Archive Breadcrumb Remaps ===
add_filter('wpseo_breadcrumb_links', function($links) {
    $last_index = count($links) - 1;

    foreach ($links as $key => $link) {
        if ($key === $last_index) continue;

        $map = [
            '/books/'        => ['books-cited', 'Books Cited'],
            '/artists/'      => ['artists-featured', 'Artists Featured'],
            '/profile/'      => ['people-referenced', 'People Referenced'],
            '/concepts/'     => ['lexicon', 'Lexicon'],
            '/movies/'       => ['movies-referenced', 'Movies Referenced'],
            '/quotes/'       => ['quote-library', 'Quote Library'],
            '/references/'   => ['research-sources', 'Research Sources'],
            '/lyrics/'       => ['song-excerpts', 'Song Excerpts'],
            '/organization/' => ['organizations', 'Organizations'],
            '/song/'         => ['songs-featured', 'Songs Featured'],
            '/image/'        => ['image-gallery', 'Image Gallery'],
            '/excerpts/'     => ['excerpt-library', 'Excerpt Library'],

        ];

        foreach ($map as $needle => [$page, $label]) {
            if (strpos($link['url'], $needle) !== false) {
                $links[$key]['url']  = get_permalink(get_page_by_path($page));
                $links[$key]['text'] = $label;
            }
        }
    }

    return $links;
});


// === Theme Breadcrumb Override ===
add_filter('wpseo_breadcrumb_links', function ($links) {
    // Only on individual theme pages
    if (is_tax('theme')) {
        $new_links = [];

        // Home
        $new_links[] = [
            'url'  => home_url('/'),
            'text' => 'Home'
        ];

        // Themes page
        $themes_page = get_page_by_path('themes');
        if ($themes_page) {
            $new_links[] = [
                'url'  => get_permalink($themes_page),
                'text' => 'Themes'
            ];
        }

        // Current term
        $term = get_queried_object();
        $new_links[] = [
            'url'  => '',
            'text' => $term->name
        ];

        return $new_links;
    }

    // On the actual /themes/ page
    if (is_page('themes')) {
        foreach ($links as &$link) {
            if ($link['text'] === 'Themes') {
                $link['url'] = ''; // Remove self-link
            }
        }
    }

    return $links;
});
