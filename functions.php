<?php

// Enable excerpts for Pages
add_post_type_support('page', 'excerpt');

// Sort CPTs ABC, except for pages and chapters
add_action('load-edit.php', function () {
    $screen = get_current_screen();

    // CPTs to force alphabetical sorting in admin
    $alphabetical_cpts = array(
        'concept',
        'lyric',
        'song',
        'organization',
        'reference',
        'quote',
        'artist',
        'book',
        'movie',
        'profile',
        'image',
        'excerpt'
    );

    if (in_array($screen->post_type, $alphabetical_cpts)) {
        // If no manual sorting in the query
        if (!isset($_GET['orderby'])) {
            // Force query vars to sort by title ASC
            $_GET['orderby'] = 'title';
            $_GET['order'] = 'ASC';

            // Build redirect URL with forced query vars
            $url = add_query_arg(array(
                'post_type' => $screen->post_type,
                'orderby' => 'title',
                'order' => 'ASC',
            ), admin_url('edit.php'));

            wp_redirect($url);
            exit;
        }
    }
});


// =====================================================
// Disable Comments Site-Wide
// =====================================================
add_action('init', function() {
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);
add_action('admin_menu', function() {
    remove_menu_page('edit-comments.php');
});
add_action('init', function() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});


// =====================================================
// Disable RSS Feeds Properly (Returns 410 Gone)
// =====================================================
add_action('do_feed', 'disable_feeds_properly', 1);
add_action('do_feed_rdf', 'disable_feeds_properly', 1);
add_action('do_feed_rss', 'disable_feeds_properly', 1);
add_action('do_feed_rss2', 'disable_feeds_properly', 1);
add_action('do_feed_atom', 'disable_feeds_properly', 1);
add_action('do_feed_rss2_comments', 'disable_feeds_properly', 1);
add_action('do_feed_atom_comments', 'disable_feeds_properly', 1);

function disable_feeds_properly() {
    wp_die(
        __('No feed available, please visit the homepage.'),
        'Feeds Disabled',
        array('response' => 410) // <-- This sets the proper HTTP status
    );
}


add_filter('relevanssi_content_to_index', 'add_artist_name_to_index', 10, 2);
function add_artist_name_to_index($content, $post) {
    if ($post->post_type === 'chapter') {
        $chapter_songs = get_field('chapter_songs', $post->ID);

        if ($chapter_songs) {
            foreach ($chapter_songs as $row) {
                if (!empty($row['song'])) {
                    $song = $row['song']; // Song CPT object
                    $content .= ' ' . get_the_title($song->ID); // index song title

                    // Only include primary artist? (change condition to include all if you want)
                    if (!empty($row['role']) && $row['role'] === 'primary') {
                        $artist = get_field('song_artist', $song->ID);
                        if ($artist) {
                            $content .= ' ' . get_the_title($artist->ID);
                        }
                    }
                }
            }
        }
    }
    return $content;
}


// Remove the default "Continue reading" junk from excerpts
function my_clean_excerpt_more($more) {
    return '…'; // just ellipsis, or replace with '' for nothing
}
add_filter('excerpt_more', 'my_clean_excerpt_more');

// Optional: limit excerpt length consistently
function my_custom_excerpt_length($length) {
    return 30; // number of words
}
add_filter('excerpt_length', 'my_custom_excerpt_length');


// 2025-08-25 - Redirect to remove old tag disallow in robots.txt
add_action('template_redirect', function() {
    if (is_tag() && !have_posts()) {
        status_header(410);
        nocache_headers();
        include(get_template_directory() . '/410.php'); // optional custom template
        exit;
    }
});


// Emoji & Page Mapper
/**
 * Return CPT metadata: title, emoji, link
 */
function get_cpt_metadata($cpt_name = '') {
    $all = [
        'featured_artists'  => ['title' => 'Songs Featured',            'emoji' => '🎤', 'link' => '/artists-featured/'],
        'other_artists'     => ['title' => 'Songs Referenced',          'emoji' => '🎤', 'link' => '/artists-featured/'],
        'songs_referenced'  => ['title' => 'Songs Excerpts',            'emoji' => '🎵', 'link' => '/song-excerpts/'],
        'artist'            => ['title' => 'Artists Featured',          'emoji' => '🎤', 'link' => '/artists-featured/'],
        'profile'           => ['title' => 'People Referenced',         'emoji' => '👤', 'link' => '/people-referenced/'],
        'lyric'             => ['title' => 'Song Excerpts',             'emoji' => '🎼', 'link' => '/song-excerpts/'],
        'quote'             => ['title' => 'Quote Library',             'emoji' => '💬', 'link' => '/quote-library/'],
        'concept'           => ['title' => 'Lexicon',                   'emoji' => '🔎', 'link' => '/lexicon/'],
        'book'              => ['title' => 'Books Cited',               'emoji' => '📚', 'link' => '/books-cited/'],
        'movie'             => ['title' => 'Movies Referenced',         'emoji' => '🎬', 'link' => '/movies-referenced/'],
        'chapter'           => ['title' => 'Narrative Threads',         'emoji' => '🧵', 'link' => '/narrative-threads/'],
        'fragment'          => ['title' => 'Narrative Fragments',       'emoji' => '📜', 'link' => '/narrative-fragments/'],
        'reference'         => ['title' => 'External References',       'emoji' => '📰', 'link' => '/research-sources/'],
        'theme'             => ['title' => 'Themes',                    'emoji' => '🎨', 'link' => '/themes/'],
        'organization'      => ['title' => 'Organizations Referenced',  'emoji' => '🏢', 'link' => '/organizations/'],
        'image'             => ['title' => 'Images Referenced',         'emoji' => '🖼', 'link' => '/image-gallery/'],
        'song'              => ['title' => 'Songs Featured',            'emoji' => '🎵', 'link' => '/songs-featured/'],
        'excerpt'           => ['title' => 'Excerpts Referenced',       'emoji' => '📖', 'link' => '/excerpt-library/'],
    ];

    if ($cpt_name) {
        return $all[$cpt_name] ?? null;
    }
    return $all;
}


// 2025-8-18
require_once get_stylesheet_directory() . '/inc/breadcrumbs.php';

require_once get_stylesheet_directory() . '/inc/redirects.php';

require_once get_stylesheet_directory() . '/inc/footnotes.php';

require_once get_stylesheet_directory() . '/inc/enqueue.php';
