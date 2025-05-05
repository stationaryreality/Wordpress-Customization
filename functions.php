<?php

// Enqueue parent and child theme styles
function ct_author_child_enqueue_styles() {
    $parent_style = 'ct-author-style';

    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style('ct-author-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array($parent_style)
    );
}
add_action('wp_enqueue_scripts', 'ct_author_child_enqueue_styles');

// Enable excerpts for Pages
add_post_type_support('page', 'excerpt');

// Redirect default Posts admin screen to show only published posts
add_action('load-edit.php', function () {
    $screen = get_current_screen();
    if ($screen->post_type == 'post' && !isset($_GET['post_status']) && !isset($_GET['all_posts'])) {
        wp_redirect(admin_url('edit.php?post_status=publish&post_type=post'));
        exit;
    }
});

// [Search blocks by anchor across CPTS, future linking tool
function get_blocks_by_anchor($target_anchors = []) {
    $matching_blocks = [];
    $args = [
        'post_type' => ['post'],
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ];
    $query = new WP_Query($args);
    while ($query->have_posts()) {
        $query->the_post();
        $blocks = parse_blocks(get_the_content());
        foreach ($blocks as $block) {
            if (!empty($block['attrs']['anchor']) && in_array($block['attrs']['anchor'], $target_anchors)) {
                $matching_blocks[] = $block;
            }
        }
    }
    wp_reset_postdata();
    return $matching_blocks;
}




// Disable Author Archive pages (redirect to 404)
add_action('template_redirect', function () {
    if (is_author()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include(get_query_template('404'));
        exit;
    }
});

// Remove Google Fonts from Parent Theme
function child_theme_remove_google_fonts() {
    wp_dequeue_style('ct-author-google-fonts'); // Update handle if needed
}
add_action('wp_enqueue_scripts', 'child_theme_remove_google_fonts', 20);

// Load custom local fonts
function child_theme_enqueue_custom_fonts() {
    wp_enqueue_style('custom-fonts', get_stylesheet_directory_uri() . '/fonts/fonts.css');
}
add_action('wp_enqueue_scripts', 'child_theme_enqueue_custom_fonts');


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
// Disable RSS Feeds
// =====================================================
add_action('do_feed', 'disable_feeds', 1);
add_action('do_feed_rdf', 'disable_feeds', 1);
add_action('do_feed_rss', 'disable_feeds', 1);
add_action('do_feed_rss2', 'disable_feeds', 1);
add_action('do_feed_atom', 'disable_feeds', 1);
function disable_feeds() {
    wp_die(__('No feed available, please visit the homepage.'));
}

