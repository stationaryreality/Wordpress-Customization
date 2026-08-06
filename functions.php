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

/**
 * Global Excerpt Cleaner
 * Strips rogue HTML and "Continue reading" links from all excerpts globally.
 */
function global_clean_excerpt($excerpt) {
    if (empty($excerpt)) {
        return $excerpt;
    }
    
    // 1. Strip all HTML tags (removes <div>, <a>, <span>, etc.)
    $clean = wp_strip_all_tags($excerpt);
    
    // 2. Remove "Continue reading" and any trailing text/links
    $clean = preg_replace('/\s*Continue reading.*$/i', '', $clean);
    
    // 3. Clean up stray ellipsis or trailing spaces
    return trim(rtrim($clean, ' …'));
}
add_filter('get_the_excerpt', 'global_clean_excerpt', 999);
add_filter('the_excerpt', 'global_clean_excerpt', 999);


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
            [ 'title' => 'Narrative Threads', 'slug' => 'narrative-threads' ],
            [ 'title' => 'Narrative Episodes', 'slug' => 'narrative-episodes' ],
            [ 'title' => 'Narrative Elements', 'slug' => 'narrative-elements' ],
        ],
        'Site Resources' => [
            [ 'title' => 'Site Engineering', 'slug' => 'developer-notes' ],
            [ 'title' => 'Top Content', 'slug' => 'top-content' ],
            [ 'title' => 'Portal Pages', 'slug' => 'portal-pages' ],
            [ 'title' => 'Newest Content', 'slug' => 'newest-content' ],
            [ 'title' => 'Site Index & Tools', 'slug' => 'site-tools' ],
            [ 'title' => 'Get Updates', 'slug' => 'get-updates' ],
        ],
        'Media & Music' => [
            [ 'title' => 'Song Excerpts', 'slug' => 'song-excerpts' ],
            [ 'title' => 'Image Gallery', 'slug' => 'image-gallery' ],
            [ 'title' => 'Video Room', 'slug' => 'video-room' ],
            [ 'title' => 'Movies Referenced', 'slug' => 'movies-referenced' ],
            [ 'title' => 'TV Shows Referenced', 'slug' => 'tv-shows-referenced' ],
            [ 'title' => 'Video Games', 'slug' => 'video-games' ],
            [ 'title' => 'Artists Featured', 'slug' => 'artists-featured' ],
            [ 'title' => 'Songs Featured', 'slug' => 'songs-featured' ],
            [ 'title' => 'Rap Pages', 'slug' => 'rap-pages' ],
        ],
        'Research' => [
            [ 'title' => 'Excerpt Library', 'slug' => 'excerpt-library' ],
            [ 'title' => 'Quote Library', 'slug' => 'quote-library' ],
            [ 'title' => 'Lexicon', 'slug' => 'lexicon' ],
            [ 'title' => 'People Referenced', 'slug' => 'people-referenced' ],
            [ 'title' => 'Books Cited', 'slug' => 'books-cited' ],
            [ 'title' => 'Organizations', 'slug' => 'organizations' ],
            [ 'title' => 'Topics', 'slug' => 'topics' ],
            [ 'title' => 'Themes', 'slug' => 'themes' ],
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
| Usage: [site_sidebar_navigation]
|--------------------------------------------------------------------------
*/
function site_sidebar_navigation_shortcode() {
    $sections = site_get_navigation_sections();
    ob_start();

    foreach ($sections as $section_title => $pages) {
        echo '<div class="sidebar-nav-section">';
        echo '<h2 class="sidebar-nav-heading">' . esc_html($section_title) . '</h2>';
        echo '<div class="sidebar-nav-list">';

        foreach ($pages as $item) {
            $page = get_page_by_path($item['slug'], OBJECT, 'page');

            if (!$page) {
                continue;
            }

            $permalink = get_permalink($page->ID);
            $thumb_url = get_the_post_thumbnail_url($page->ID, 'thumbnail');
            
            // Fetch the REAL page excerpt
            $excerpt = get_the_excerpt($page->ID);

            if ($thumb_url) {
                $image_html = '<img class="sidebar-nav-thumb" src="' . esc_url($thumb_url) . '" alt="' . esc_attr($item['title']) . '">';
            } else {
                $image_html = '<div class="sidebar-nav-thumb sidebar-nav-thumb-fallback">📄</div>';
            }

            echo '<div class="sidebar-nav-item">';
            echo '<a class="sidebar-nav-link" href="' . esc_url($permalink) . '">';
            echo $image_html;
            
            echo '<div class="sidebar-nav-content">';
            echo '<div class="sidebar-nav-title">' . esc_html($item['title']) . '</div>';
            
            // Only show excerpt if it actually exists
            if (!empty($excerpt)) {
                echo '<div class="sidebar-nav-description">' . esc_html($excerpt) . '</div>';
            }
            
            echo '</div>'; // end sidebar-nav-content
            echo '</a>';   // end sidebar-nav-link
            echo '</div>'; // end sidebar-nav-item
        }

        echo '</div>'; // end sidebar-nav-list
        echo '</div>'; // end sidebar-nav-section
    }

    return ob_get_clean();
}
add_shortcode('site_sidebar_navigation', 'site_sidebar_navigation_shortcode');

// 2026-06-23 - limit page-links-to to only pages, can add cpts if needed

add_filter('page-links-to-post-types', function() {
    return ['page'];
});

// 2026-05-09 - did these not work because it used template instead of stylesheet?

//require_once get_template_directory() . '/inc/admin/admin-menu.php';
//require_once get_template_directory() . '/inc/admin/admin-content-operations.php';
//require_once get_template_directory() . '/inc/admin/admin-cpt-menu-order.php';

// 2025-8-18


require_once get_stylesheet_directory() . '/inc/breadcrumbs.php';

require_once get_stylesheet_directory() . '/inc/redirects.php';

require_once get_stylesheet_directory() . '/inc/footnotes.php';

require_once get_stylesheet_directory() . '/inc/enqueue.php';

require_once get_stylesheet_directory() . '/inc/helpers.php';

require_once get_stylesheet_directory() . '/inc/relationships/song-relationships.php';

require_once get_stylesheet_directory() . '/inc/collectors/reference-context.php';

require_once get_stylesheet_directory() . '/inc/collectors/featured-context.php';

require_once get_stylesheet_directory() . '/inc/references.php';

require_once get_stylesheet_directory() . '/inc/presentation/knowledge-sections.php';

require_once get_stylesheet_directory() . '/inc/collectors/knowledge-resolver.php';

require_once get_stylesheet_directory() . '/inc/presentation/knowledge-builder.php';

require_once get_stylesheet_directory() . '/inc/presentation/load-view.php';

require_once get_stylesheet_directory() . '/inc/presentation/cards/build-card.php';

require_once get_stylesheet_directory() . '/inc/collectors/knowledge-collector.php';

require_once get_stylesheet_directory() . '/inc/presentation/knowledge-registry.php';

//disabled - require_once get_stylesheet_directory() . '/inc/concept_relations.php';

// Load shared taxonomy bubbles function
add_action('after_setup_theme', function() {
    $file = get_stylesheet_directory() . '/inc/taxonomy.php';
    if ( file_exists( $file ) ) {
        require_once $file;
    }
});


/**
 * Child theme override.
 *
 * Redirects Author theme content template loading
 * to the organized template-parts/content/ directory.
 */
if (!function_exists('ct_author_get_content_template')) {

    function ct_author_get_content_template()
    {
        // Get bbPress template for all bbPress pages
        if (function_exists('is_bbpress') && is_bbpress()) {
            get_template_part('template-parts/content/bbpress');
            return;
        }

        if (is_home() || is_archive()) {
            get_template_part(
                'template-parts/content/archive',
                get_post_type()
            );
        } else {
            get_template_part(
                'template-parts/content/' . get_post_type()
            );
        }
    }
}


/**
 * Dynamically register Page Templates from the /templates/pages/ folder
 */
add_filter( 'theme_page_templates', function( $templates ) {
    // This is the exact path from your tree output
    $dir = get_stylesheet_directory() . '/templates/pages/';
    
    if ( ! is_dir( $dir ) ) {
        return $templates;
    }

    foreach ( glob( $dir . '*.php' ) as $file ) {
        // Get the relative path (e.g., "templates/pages/artists-featured.php")
        $relative_path = str_replace( get_stylesheet_directory() . '/', '', $file );
        
        // Create a clean label from the filename
        $filename = basename( $file, '.php' );
        $label = str_replace( 'page-', '', $filename );
        $label = str_replace( '-', ' ', $label );
        $label = ucwords( $label );
        
        $templates[ $relative_path ] = $label;
    }

    return $templates;
} );