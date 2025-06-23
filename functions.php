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


add_filter('wpseo_breadcrumb_links', function($links) {
    $last_index = count($links) - 1;

    foreach ($links as $key => $link) {
        if ($key === $last_index) continue;

        // Redirect Books Archive
        if (strpos($link['url'], '/books/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('books-cited'));
            $links[$key]['text'] = 'Books Cited';
        }

        // Redirect Artists Archive
        if (strpos($link['url'], '/artists/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('artists-featured'));
            $links[$key]['text'] = 'Artists Featured';
        }

        // Redirect Profiles Archive (formerly authors)
        if (strpos($link['url'], '/profile/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('people-referenced'));
            $links[$key]['text'] = 'People Referenced';
        }

        // Redirect Concept Archive
        if (strpos($link['url'], '/concepts/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('lexicon'));
            $links[$key]['text'] = 'Lexicon';
        }
    
        // Redirect Movie Archive
        if (strpos($link['url'], '/movies/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('movies-referenced'));
            $links[$key]['text'] = 'Movies Referenced';
        }
    }

    return $links;
});


add_action('template_redirect', function () {
    if (is_post_type_archive('book')) {
        wp_redirect(home_url('/books-cited/'), 301);
        exit;
    }
});

add_action('template_redirect', function () {
    if (is_post_type_archive('artist')) {
        wp_redirect(home_url('/artists-featured/'), 301);
        exit;
    }
});

add_action('template_redirect', function () {
    if (is_post_type_archive('profile')) {
        wp_redirect(home_url('/people-referenced/'), 301);
        exit;
    }
});

add_action('template_redirect', function () {
    if (is_post_type_archive('concept')) {
        wp_redirect(home_url('/lexicon/'), 301);
        exit;
    }
});

add_action('template_redirect', function () {
    if (is_post_type_archive('movie')) {
        wp_redirect(home_url('/movies-referenced/'), 301);
        exit;
    }
});

function render_custom_cover_block($atts) {
    $atts = shortcode_atts(['id' => ''], $atts, 'custom_cover');

    if (!$atts['id']) return '';

    $html = get_field('lyric_html_block', $atts['id']);
    return $html ?: '';
}
add_shortcode('custom_cover', 'render_custom_cover_block');

//FootNotes System

function insert_footnote_shortcode($atts) {
  static $seen = [];
  static $counter = 0;

  $id = sanitize_key($atts['id']);
  $text = isset($atts['text']) ? $atts['text'] : null;

  if (!isset($seen[$id])) {
    $seen[$id] = ++$counter;
  }

  $number = $seen[$id];
  $anchor = "fn-$id";
  $ref_anchor = "ref-$id";

  return "<sup id=\"$ref_anchor\"><a href=\"#$anchor\" class=\"footnote-ref\">[$number]</a></sup>";
}
add_shortcode('footnote', 'insert_footnote_shortcode');

function output_footnotes_shortcode() {
  global $post;

  preg_match_all('/\[footnote([^\]]+)\]/', $post->post_content, $matches, PREG_SET_ORDER);
  if (empty($matches)) return '';

  $seen = [];
  $output = '<ol class="footnotes">';
  $counter = 0;

  foreach ($matches as $match) {
    $image = null;
    // Extract id and optional text manually
    preg_match('/id="([^"]+)"/', $match[1], $id_match);
    preg_match('/text="([^"]+)"/', $match[1], $text_match);

    if (empty($id_match[1])) continue;
    $id = sanitize_key($id_match[1]);

    if (isset($seen[$id])) continue;
    $seen[$id] = true;
    $counter++;

    // Decide content
    if (!empty($text_match[1])) {
      $title = '';
      $text = wp_kses_post($text_match[1]);
    } else {
$title = get_the_title($id);
$permalink = get_permalink($id);
$definition = get_field('definition', $id);
$source = get_field('source', $id);

// Format source
if (filter_var($source, FILTER_VALIDATE_URL)) {
  $source = "<a href=\"{$source}\" target=\"_blank\" rel=\"noopener noreferrer\">{$source}</a>";
} else {
  $source = esc_html($source);
}

// Combine definition + source
$text = esc_html($definition);
if ($source) {
  $text .= "<br><em>Source:</em> {$source}";
}


      $image = get_field('cover_image', $id);
      $permalink = get_permalink($id);
    }

    $output .= "<li id=\"fn-$id\"><p>";

    // Insert image if available and CPT


if ($title) {
  $output .= "<strong><a href=\"{$permalink}\" target=\"_blank\" rel=\"noopener noreferrer\">{$title}</a> 🔎</strong> ";
}
$output .= "{$text} <a href=\"#ref-$id\" class=\"backref\">↩︎</a>";

// Image appears below reference, still inside <p>
if (!empty($image)) {
  $output .= "<br><a href=\"{$permalink}\"><img src=\"{$image['url']}\" alt=\"{$title}\" class=\"footnote-cover\" style=\"width:150px; height:auto; margin-top:0.5em; display:block;\" /></a>";
}

$output .= "</p></li>";
  }

    if (!empty($image)) {
      $output .= "<a href=\"{$permalink}\"><img src=\"{$image['url']}\" alt=\"{$title}\" class=\"footnote-cover\" style=\"width:150px; height:auto; margin-bottom:0.5em; display:block;\" /></a>";
    }


  $output .= '</ol>';
  return $output;
}
add_shortcode('footnotes', 'output_footnotes_shortcode');
