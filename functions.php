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


function render_custom_cover_block($atts) {
    $atts = shortcode_atts(['id' => ''], $atts, 'custom_cover');

    $id = $atts['id'];
    if (!$id) return '';

    $post_type = get_post_type($id);
    if (!$post_type) return '';

    // Determine correct ACF field based on post type
    if ($post_type === 'quote') {
        $html = get_field('quote_cover_block_full', $id);
    } elseif ($post_type === 'lyric') {
        $html = get_field('lyric_cover_block_full', $id);
    } else {
        return '';
    }

    return $html ?: '';
}
add_shortcode('custom_cover', 'render_custom_cover_block');


function secondary_song_image_shortcode() {
    if (!function_exists('get_field')) return ''; // safety check

    $chapter_songs = get_field('chapter_songs'); // repeater field
    if (empty($chapter_songs) || !is_array($chapter_songs)) return '';

    $secondary_song = null;

    foreach ($chapter_songs as $row) {
        if (!empty($row['role']) && $row['role'] === 'secondary' && !empty($row['song']) && $row['song'] instanceof WP_Post) {
            $secondary_song = $row['song'];
            break; // stop at the first secondary song
        }
    }

    if (!$secondary_song) return '';

    $song_link = get_permalink($secondary_song);
    $video_img = get_field('video_screenshot', $secondary_song->ID);
    $video_url = $video_img ? $video_img['sizes']['large'] : '';

    if (!$video_url) return '';

    ob_start();
    echo '<div class="secondary-song-image" style="margin:2em 0;text-align:center;">';
    echo '<a href="' . esc_url($song_link) . '">';
    echo '<img src="' . esc_url($video_url) . '" alt="" style="max-width:100%;height:auto;border-radius:8px;">';
    echo '</a>';
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('secondary_song_image', 'secondary_song_image_shortcode');


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


// 2025-8-18
require_once get_stylesheet_directory() . '/inc/breadcrumbs.php';

require_once get_stylesheet_directory() . '/inc/redirects.php';

require_once get_stylesheet_directory() . '/inc/footnotes.php';

require_once get_stylesheet_directory() . '/inc/enqueue.php';
