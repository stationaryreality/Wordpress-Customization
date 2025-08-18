<?php
// inc/redirects.php

/**
 * Redirect CPT archive pages to their custom index pages.
 */
add_action('template_redirect', function() {
    if (is_post_type_archive()) {
        $map = [
            'artist'       => '/artists-featured/',
            'song'         => '/songs-featured/',
            'chapter'      => '/chapters-by-song/',
            'movie'        => '/movies-referenced/',
            'book'         => '/books-cited/',
            'organization' => '/organizations-referenced/',
            'profile'      => '/people-referenced/',
            'quote'        => '/quote-library/',
            'reference'    => '/research-sources/',
            'image'        => '/image-gallery/',
            'lyric'        => '/song-excerpts/',
            'concept'      => '/lexicon/',
        ];

        $pt = get_query_var('post_type');
        if (isset($map[$pt])) {
            wp_redirect(home_url($map[$pt]), 301);
            exit;
        }
    }
});
