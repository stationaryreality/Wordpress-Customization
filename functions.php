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
        'excerpt',
        'show',
        'game'
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
        'concept'           => ['title' => 'Lexicon',                   'emoji' => '🔎', 'link' => '/lexicon/'],
        'portal'            => ['title' => 'Portal Pages',              'emoji' => '🚪', 'link' => '/portal-pages/'],
        'quote'             => ['title' => 'Quote Library',             'emoji' => '💬', 'link' => '/quote-library/'],
        'excerpt'           => ['title' => 'Excerpts Library',          'emoji' => '📖', 'link' => '/excerpt-library/'],
        'lyric'             => ['title' => 'Song Excerpts',             'emoji' => '🎼', 'link' => '/song-excerpts/'],
        'reference'         => ['title' => 'Research Sources',          'emoji' => '📰', 'link' => '/research-sources/'],
        'song'              => ['title' => 'Songs Featured',            'emoji' => '🎵', 'link' => '/songs-featured/'],
        'image'             => ['title' => 'Images Gallery',            'emoji' => '🖼', 'link' => '/image-gallery/'],
        'organization'      => ['title' => 'Organizations',             'emoji' => '🏢', 'link' => '/organizations/'],
        'book'              => ['title' => 'Books Cited',               'emoji' => '📚', 'link' => '/books-cited/'],
        'movie'             => ['title' => 'Movies Referenced',         'emoji' => '🎬', 'link' => '/movies-referenced/'],
        'artist'            => ['title' => 'Artists Featured',          'emoji' => '🎤', 'link' => '/artists-featured/'],
        'profile'           => ['title' => 'People Referenced',         'emoji' => '👤', 'link' => '/people-referenced/'],
        'theme'             => ['title' => 'Themes',                    'emoji' => '🎨', 'link' => '/themes/'],
        'topic'             => ['title' => 'Topics',                    'emoji' => '🧩', 'link' => '/topics/'],
        'chapter'           => ['title' => 'Narrative Threads',         'emoji' => '🧵', 'link' => '/narrative-threads/'],
        'fragment'          => ['title' => 'Narrative Episodes',        'emoji' => '📜', 'link' => '/narrative-episodes/'],
        'element'           => ['title' => 'Narrative Elements',        'emoji' => '⚛️', 'link' => '/narrative-elements/'],
        'show'              => ['title' => 'TV Shows Referenced',       'emoji' => '📺', 'link' => '/tv-shows-referenced/'],
        'game'              => ['title' => 'Video Games',               'emoji' => '🎮', 'link' => '/video-games/'],
        'video'             => ['title' => 'Video Room',                'emoji' => '📼', 'link' => '/video-room/'],

    ];

    return $cpt_name ? ($all[$cpt_name] ?? null) : $all;
}

/*
|--------------------------------------------------------------------------
| Homepage Navigation Configuration
|--------------------------------------------------------------------------
| Central source of truth for homepage + sidebar navigation
|--------------------------------------------------------------------------
*/

function site_get_navigation_sections() {

    return [

        'Narrative Content' => [
            [
                'title'       => 'Narrative Threads',
                'slug'        => 'narrative-threads',
                'description' => 'Large-scale interconnected narrative structures',
            ],
            [
                'title'       => 'Narrative Episodes',
                'slug'        => 'narrative-episodes',
                'description' => 'Coherent, self-contained units offering insight in compact form',
            ],
            [
                'title'       => 'Narrative Elements',
                'slug'        => 'narrative-elements',
                'description' => 'Characters, concepts, motifs, and recurring structures',
            ],
        ],

                'Site Resources' => [
                                                                                        [
                'title'       => 'Top Content',
                'slug'        => 'top-content',
                'description' => 'Top Excerpts, Quotes, and Lyrics',
            ],
                                                                    [
                'title'       => 'Portal Pages',
                'slug'        => 'portal-pages',
                'description' => 'Curated entry points into major areas of the site',
            ],
            [
                'title'       => 'Newest Content',
                'slug'        => 'newest-content',
                'description' => 'Recently added material across the site',
            ],


                                            [
                'title'       => 'Site Index & Tools',
                'slug'        => 'site-tools',
                'description' => 'Public utility tools and navigation helpers',
            ],
  [
                'title'       => 'dev. Development Site',
                'slug'        => 'developer-notes',
                'description' => 'Development notes and site construction insights',
            ],

                                    [
                'title'       => 'Get Updates',
                'slug'        => 'get-updates',
                'description' => 'Subscribe and receive updates',
            ],
        ],

        'Media & Music' => [
                        [
                'title'       => 'Song Excerpts',
                'slug'        => 'song-excerpts',
                'description' => 'Lyrics and excerpts connected to site themes',
            ],
                        [
                'title'       => 'Image Gallery',
                'slug'        => 'image-gallery',
                'description' => 'Visual material and related imagery',
            ],
                        [
                'title'       => 'Video Room',
                'slug'        => 'video-room',
                'description' => 'Video content and media collections',
            ],
            [
                'title'       => 'Movies Referenced',
                'slug'        => 'movies-referenced',
                'description' => 'Films referenced throughout the site',
            ],
            [
                'title'       => 'TV Shows Referenced',
                'slug'        => 'tv-shows-referenced',
                'description' => 'Television references and related material',
            ],
            [
                'title'       => 'Video Games',
                'slug'        => 'video-games',
                'description' => 'Games referenced within the narrative structure',
            ],
            [
                'title'       => 'Artists Featured',
                'slug'        => 'artists-featured',
                'description' => 'Artists referenced throughout the narrative system',
            ],
            [
                'title'       => 'Songs Featured',
                'slug'        => 'songs-featured',
                'description' => 'Songs tied to narrative and thematic structures',
            ],

            [
                'title'       => 'Rap Pages',
                'slug'        => 'rap-pages',
                'description' => 'Rap artists, songs, and lyrics grouped together',
            ],
        ],

        'Research' => [

            [
                'title'       => 'Excerpt Library',
                'slug'        => 'excerpt-library',
                'description' => 'Curated excerpts and reference material',
            ],
            [
                'title'       => 'Quote Library',
                'slug'        => 'quote-library',
                'description' => 'Standalone quotations and citations',
            ],
                        [
                'title'       => 'Lexicon',
                'slug'        => 'lexicon',
                'description' => 'Definitions and conceptual terminology',
            ],

                                    [
                'title'       => 'People Referenced',
                'slug'        => 'people-referenced',
                'description' => 'People referenced throughout the site',
            ],
                        [
                'title'       => 'Books Cited',
                'slug'        => 'books-cited',
                'description' => 'Books referenced throughout the site',
            ],

            [
                'title'       => 'Organizations',
                'slug'        => 'organizations',
                'description' => 'Referenced groups, institutions, and entities',
            ],

            [
                'title'       => 'Topics',
                'slug'        => 'topics',
                'description' => 'Topical organization across the site',
            ],
            [
                'title'       => 'Themes',
                'slug'        => 'themes',
                'description' => 'Major recurring conceptual themes',
            ],
        ],


    ];
}


// Narrative Thread Pages Shortcode for Nav
function narrative_threads_list() {
    $output = '<ul>';

    $portals = new WP_Query(array(
        'post_type'      => 'chapter',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC'
    ));

    if ($portals->have_posts()) {
        while ($portals->have_posts()) {
            $portals->the_post();
            $output .= '<li class="post-item stable">';
            $output .= '<a href="' . get_permalink() . '" class="nav-post-title">' . get_the_title() . '</a>';
            $output .= '</li>';
        }
        wp_reset_postdata();
    }

    $output .= '</ul>';
    return $output;
}
add_shortcode('narrative_threads', 'narrative_threads_list');


/*
|--------------------------------------------------------------------------
| Sidebar Navigation Shortcode
|--------------------------------------------------------------------------
| Usage:
| [site_sidebar_navigation]
|--------------------------------------------------------------------------
*/

function site_sidebar_navigation_shortcode() {

    $sections = site_get_navigation_sections();

    ob_start();

    foreach ($sections as $section_title => $pages) {

        echo '<div class="sidebar-nav-section">';

        echo '<h2 class="sidebar-nav-heading">' . esc_html($section_title) . '</h2>';

        foreach ($pages as $item) {

            $page = get_page_by_path($item['slug']);

            if (!$page) {
                continue;
            }

            $image = get_the_post_thumbnail_url($page->ID, 'medium');

            ?>

            <div style="text-align:center; margin-bottom:1.5rem;">

                <a href="<?php echo get_permalink($page->ID); ?>">

                    <h3 style="margin-bottom:0.5em;">
                        <?php echo esc_html($item['title']); ?>
                    </h3>

                    <?php if ($image) : ?>

                        <img
                            src="<?php echo esc_url($image); ?>"
                            alt="<?php echo esc_attr($item['title']); ?>"
                            width="300"
                            class="nav-image"
                        >

                    <?php endif; ?>

                </a>


            </div>

            <?php
        }

        echo '</div>';
    }

    return ob_get_clean();
}
add_shortcode('site_sidebar_navigation', 'site_sidebar_navigation_shortcode');


// Portal Pages Shortcode for Nav - Not sure if even using anymore
function portal_pages_list() {
    $output = '<ul>';

    $portals = new WP_Query(array(
        'post_type'      => 'portal',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC'
    ));

    if ($portals->have_posts()) {
        while ($portals->have_posts()) {
            $portals->the_post();
            $output .= '<li class="post-item stable">';
            $output .= '<a href="' . get_permalink() . '" class="nav-post-title">' . get_the_title() . '</a>';
            $output .= '</li>';
        }
        wp_reset_postdata();
    }

    $output .= '</ul>';
    return $output;
}
add_shortcode('portal_pages', 'portal_pages_list');


// 2026-05-09

//require_once get_template_directory() . '/inc/admin/admin-menu.php';
//require_once get_template_directory() . '/inc/admin/admin-content-operations.php';
//require_once get_template_directory() . '/inc/admin/admin-cpt-menu-order.php';

// 2025-8-18
require_once get_stylesheet_directory() . '/inc/breadcrumbs.php';

require_once get_stylesheet_directory() . '/inc/redirects.php';

require_once get_stylesheet_directory() . '/inc/footnotes.php';

require_once get_stylesheet_directory() . '/inc/enqueue.php';

require_once get_stylesheet_directory() . '/inc/helpers.php';

//disabled - require_once get_stylesheet_directory() . '/inc/concept_relations.php';

// Load shared taxonomy bubbles function
add_action('after_setup_theme', function() {
    $file = get_stylesheet_directory() . '/inc/taxonomy.php';
    if ( file_exists( $file ) ) {
        require_once $file;
    }
});
