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


register_taxonomy( 'theme', [ 'chapter', 'quote', 'lyric', 'concept', 'movie', 'book', 'profile', 'artist' ], [
  'label' => 'Themes',
  'public' => true,
  'show_ui' => true,
  'show_in_nav_menus' => true,
  'show_admin_column' => true,
  'hierarchical' => false,
  'show_in_rest' => true, // ← REQUIRED for block editor support
  'rewrite' => ['slug' => 'theme'],
]);


add_action('template_redirect', function () {
    if (is_tax('theme')) {
        $term = get_queried_object();

        // If no term slug, you're on the root `/theme/` archive
        if (empty($term->slug)) {
            wp_redirect(home_url('/themes/'), 301);
            exit;
        }
    }
});

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
        'image'
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

        // Redirect Quotes Archive
        if (strpos($link['url'], '/quotes/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('quote-library'));
            $links[$key]['text'] = 'Quote Library';
        }

        // Redirect References Archive
        if (strpos($link['url'], '/references/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('research-sources'));
            $links[$key]['text'] = 'Research Sources';
        }

        // Redirect Lyrics Archive
        if (strpos($link['url'], '/lyrics/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('song-excerpts'));
            $links[$key]['text'] = 'Song Excerpts';

        }

        // Redirect Organizations Archive
        if (strpos($link['url'], '/organization/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('organizations'));
            $links[$key]['text'] = 'Organizations';
        }

        // Redirect Songs Archive
        if (strpos($link['url'], '/song/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('songs-featured'));
            $links[$key]['text'] = 'Songs Featured';
        }

        // Redirect Images Archive
        if (strpos($link['url'], '/image/') !== false) {
            $links[$key]['url']  = get_permalink(get_page_by_path('image-gallery'));
            $links[$key]['text'] = 'Image Gallery';
        }
    }

    return $links;
});


add_filter('wpseo_breadcrumb_links', function ($links) {
    // Only on individual theme pages
    if (is_tax('theme')) {
        $new_links = [];

        // Manually insert "Home"
        $new_links[] = [
            'url'  => home_url('/'),
            'text' => 'Home'
        ];

        // Manually insert "Themes" page
        $themes_page = get_page_by_path('themes');
        if ($themes_page) {
            $new_links[] = [
                'url'  => get_permalink($themes_page),
                'text' => 'Themes'
            ];
        }

        // Append the term itself
        $term = get_queried_object();
        $new_links[] = [
            'url'  => '',
            'text' => $term->name
        ];

        return $new_links;
    }

    // Optional: clean up breadcrumb on the actual /themes/ page
    if (is_page('themes')) {
        foreach ($links as &$link) {
            if ($link['text'] === 'Themes') {
                $link['url'] = ''; // Remove the link to self
            }
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

add_action('template_redirect', function () {
    if (is_post_type_archive('quote')) {
        wp_redirect(home_url('/quote-library/'), 301);
        exit;
    }
});

add_action('template_redirect', function () {
    if (is_post_type_archive('reference')) {
        wp_redirect(home_url('/research-sources/'), 301);
        exit;
    }
});

add_action('template_redirect', function () {
    if (is_post_type_archive('lyric')) {
        wp_redirect(home_url('/song-excerpts/'), 301);
        exit;
    }
});

add_action('template_redirect', function () {
    if (is_post_type_archive('organization')) {
        wp_redirect(home_url('/organizations/'), 301);
        exit;
    }
});

add_action('template_redirect', function () {
    if (is_post_type_archive('song')) {
        wp_redirect(home_url('/songs-featured/'), 301);
        exit;
    }
});

add_action('template_redirect', function () {
    if (is_post_type_archive('image')) {
        wp_redirect(home_url('/image-gallery/'), 301);
        exit;
    }
});


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



function display_referenced_works() {
  ob_start();
  $chapter_id = get_the_ID();

  echo '<div class="referenced-works">';
  echo '<h3 style="font-weight:bold;margin-top:2em;">Notes & References</h3>';

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

];

// === Songs Referenced (using new chapter_songs repeater) ===
$song_rows = get_field('chapter_songs') ?: [];
$featured  = [];
$other_artists = [];

// Separate by role
foreach ($song_rows as $row) {
    if (empty($row['song']) || !$row['song'] instanceof WP_Post) {
        continue;
    }

    $song_post   = $row['song'];
    $song_title  = get_the_title($song_post);
    $artist_id = get_field('song_artist', $song_post->ID);
    $artist_post = $artist_id ? get_post($artist_id) : null;
    $role        = $row['role'] ?? 'supporting';

    // Fallback for missing artist
    $artist_id   = $artist_post instanceof WP_Post ? $artist_post->ID : 'unknown';
    $artist_obj  = $artist_post instanceof WP_Post ? $artist_post : (object)[
        'ID' => 'unknown',
        'post_title' => 'Unknown Artist'
    ];

    if ($role === 'primary' || $role === 'secondary') {
        if (!isset($featured[$artist_id])) {
            $featured[$artist_id] = [
                'post'  => $artist_obj,
                'songs' => [],
            ];
        }
        $featured[$artist_id]['songs'][] = $song_title;
    } else {
        if (!isset($other_artists[$artist_id])) {
            $other_artists[$artist_id] = [
                'post'  => $artist_obj,
                'songs' => [],
            ];
        }
        $other_artists[$artist_id]['songs'][] = $song_title;
    }
}

// === Output Featured Artists ===
if (!empty($featured)) {
    $meta = $group_titles['featured_artists'];
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";

    foreach ($featured as $entry) {
        $artist = $entry['post'];
        $songs  = $entry['songs'];

        if ($artist->ID !== 'unknown') {
            setup_postdata($artist);
            $img = get_field('portrait_image', $artist->ID);
            $thumb = $img ? "<a href=\"" . get_permalink($artist) . "\"><img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:8px;\"></a>" : '';
            $link  = get_permalink($artist);
            $title = esc_html(get_the_title($artist));
        } else {
            $thumb = '';
            $link  = '#';
            $title = esc_html($artist->post_title);
        }

        echo "<li style=\"display:flex;align-items:center;gap:10px;margin-bottom:0.6em;\">{$thumb}<div><a href=\"{$link}\"><strong>{$title}</strong></a>";
        foreach ($songs as $s) {
            echo "<br><span style=\"font-size:0.9em;color:#666;\">".esc_html($s)."</span>";
        }
        echo "</div></li>";

        if ($artist->ID !== 'unknown') {
            wp_reset_postdata();
        }
    }

    echo '</ul></div>';
}

// === Output Other Artists ===
if (!empty($other_artists)) {
    uasort($other_artists, fn($a, $b) => strcmp($a['post']->post_title, $b['post']->post_title));
    $meta = $group_titles['other_artists'];
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";

    foreach ($other_artists as $entry) {
        $artist = $entry['post'];
        $songs  = $entry['songs'];

        if ($artist->ID !== 'unknown') {
            setup_postdata($artist);
            $img   = get_field('portrait_image', $artist->ID);
            $link  = get_permalink($artist);
            $title = esc_html(get_the_title($artist));
            $thumb = $img ? "<a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:8px;\"></a>" : '';
        } else {
            $thumb = '';
            $link  = '#';
            $title = esc_html($artist->post_title);
        }

        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;\">";
        echo $thumb;
        echo "<div><a href=\"{$link}\"><strong>{$title}</strong></a>";
        foreach ($songs as $song_title) {
            echo "<br><span style=\"font-size:0.9em;color:#666;\">".esc_html($song_title)."</span>";
        }
        echo "</div></li>";

        if ($artist->ID !== 'unknown') {
            wp_reset_postdata();
        }
    }

    echo '</ul></div>';
}


  // === Remaining CPTs ===
  $acf_map = [
    'people_referenced'    => 'profile',
    'books_cited'          => 'book',
    'concepts_referenced'  => 'concept',
    'movies_referenced'    => 'movie',
    'quotes_referenced'    => 'quote',
    'lyrics_referenced'    => 'lyric'
  ];
  $linked_items = [];
  foreach ($acf_map as $acf => $type) {
    $items = get_field($acf) ?: [];
    foreach ($items as $item) {
      if ($item instanceof WP_Post) $linked_items[$type][$item->ID] = $item;
    }
  }
foreach ($linked_items as $type => $items) {
  if (empty($items)) continue;
  if (in_array($type, ['book', 'movie', 'concept'])) {
    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
  }

  $meta = $group_titles[$type];
  echo '<div class="referenced-group" style="margin-top:2em;">';
  echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";

  foreach ($items as $item) {
    $title = esc_html(get_the_title($item));
    $link = get_permalink($item);
    $thumb = '';

    if ($type === 'profile') {
      $img = get_field('portrait_image', $item->ID);
      if ($img) {
        $src = $img['sizes']['thumbnail'];
        $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:10px;\"></a>";
      }
    } elseif (in_array($type, ['concept', 'quote', 'reference'])) {
      if (has_post_thumbnail($item->ID)) {
        $src = get_the_post_thumbnail_url($item->ID, 'thumbnail');
        $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:10px;\"></a>";
      }
    } elseif (in_array($type, ['book', 'movie'])) {
      $img = get_field('cover_image', $item->ID);
      if ($img) {
        $src = $img['sizes']['medium'];
        $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:60px;height:auto;margin-right:10px;\"></a>";
      }
    }

    echo "<li style=\"display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;\">{$thumb}<div><a href=\"{$link}\"><strong>{$title}</strong></a>";

    if ($type === 'concept') {
      $def = get_field('definition', $item->ID);
      if ($def) echo "<div>{$def}</div>";
    } elseif ($type === 'quote') {
      $quote = get_field('quote_text', $item->ID) ?: get_field('quote_html_block', $item->ID);
      if ($quote) echo "<div>{$quote}</div>";
    }

    echo "</div></li>";
  }

  echo '</ul></div>';
}


// === Organizations Referenced ===
$organizations = get_field('organizations_referenced') ?: [];
$meta = $group_titles['organizations'];
if (!empty($organizations)) {
  echo '<div class="referenced-group" style="margin-top:2em;">';
  echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
  uasort($organizations, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
  foreach ($organizations as $org) {
    $title = esc_html(get_the_title($org));
    $link  = get_permalink($org);
    $cover = get_field('cover_image', $org->ID);
    $img   = $cover ? "<a href=\"{$link}\"><img src=\"{$cover['url']}\" alt=\"{$title}\" style=\"width:60px;height:60px;object-fit:cover;margin-right:10px;\"></a>" : '';
    echo "<li style=\"display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;\">{$img}<div><a href=\"{$link}\"><strong>{$title}</strong></a></div></li>";
  }
  echo '</ul></div>';
}


// === Standalone External References ===
$refs = get_field('chapter_references') ?: [];
$meta = $group_titles['reference'];
if (!empty($refs)) {
  echo '<div class="referenced-group" style="margin-top:2em;">';
  echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
  usort($refs, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
  foreach ($refs as $ref) {
    $title = esc_html(get_the_title($ref));
    $url   = get_field('url', $ref->ID);
    $src   = get_field('source_name', $ref->ID);
    $link  = get_permalink($ref->ID);
    $img   = get_the_post_thumbnail_url($ref->ID, 'thumbnail');
    $thumb = $img ? "<a href=\"{$link}\" rel=\"noopener noreferrer\"><img src=\"{$img}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:10px;\"></a>" : '';

    echo "<li style=\"display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;\">{$thumb}<div>";
    echo "<div><a href=\"{$link}\" rel=\"noopener noreferrer\"><strong>{$title}</strong></a></div>";
    if ($src) echo "<div><em>{$src}</em></div>";
    if ($url) echo "<div><a href=\"{$url}\" target=\"_blank\" rel=\"noopener noreferrer\">Link</a></div>";
    echo "</div></li>";
  }
  echo '</ul></div>';
}

// === Images Referenced ===
$images = get_field('images_linked') ?: [];
$meta = $group_titles['image'];
if (!empty($images)) {
  echo '<div class="referenced-group" style="margin-top:2em;">';
  echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
  uasort($images, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
  foreach ($images as $img_post) {
    $title = esc_html(get_the_title($img_post));
    $link  = get_permalink($img_post);
$image = get_field('image_file', $img_post->ID);
$thumb_url = $image ? $image['sizes']['medium'] : '';

    $thumb = $thumb_url ? "<a href=\"{$link}\"><img src=\"{$thumb_url}\" alt=\"{$title}\" style=\"width:60px;height:auto;margin-right:10px;\"></a>" : '';

    echo "<li style=\"display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;\">{$thumb}<div><a href=\"{$link}\"><strong>{$title}</strong></a></div></li>";
  }
  echo '</ul></div>';
}


// === Themes ===
$themes = get_the_terms($chapter_id, 'theme');
if ($themes && !is_wp_error($themes)) {
  $meta = $group_titles['theme'];
  usort($themes, fn($a, $b) => strcmp($a->name, $b->name));
  echo '<div class="referenced-group" style="margin-top:2em;">';
  echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
  foreach ($themes as $theme) {
    $link = get_term_link($theme);
    $title = esc_html($theme->name);
    echo "<li><a href=\"{$link}\"><strong>{$title}</strong></a></li>";
  }
  echo '</ul></div>';
}

// === Music Video Block (Primary Featured Song) ===
$primary_song = get_field('primary_song');
if ($primary_song instanceof WP_Post) {
  $song_link   = get_permalink($primary_song);
  $song_title  = get_the_title($primary_song);
  $video_img   = get_field('video_screenshot', $primary_song->ID);
  $video_url   = $video_img ? $video_img['sizes']['large'] : '';

  echo '<div class="referenced-group" style="margin-top:2em;">';
  echo '<h4><span style="font-size:1.1em;">🎥</span> ' . esc_html($song_title) . '</h4>';

  if ($video_url) {
    echo '<div style="margin-top:10px;">';
    echo '<a href="' . esc_url($song_link) . '">';
    echo '<img src="' . esc_url($video_url) . '" alt="' . esc_attr($song_title) . ' video screenshot" style="max-width:100%;height:auto;border-radius:8px;display:block;margin:0 auto;">';
    echo '</a>';
    echo '</div>';
  }

  echo '</div>';
}


// === Music Video Block (Secondary Featured Song) ===
$chapter_songs = get_field('chapter_songs');
$hide_secondary = get_field('hide_secondary_song_in_footnotes');

$secondary_song = null;

if (!empty($chapter_songs) && is_array($chapter_songs)) {
    foreach ($chapter_songs as $row) {
        if (!empty($row['role']) && $row['role'] === 'secondary' && !empty($row['song']) && $row['song'] instanceof WP_Post) {
            $secondary_song = $row['song'];
            break; // use first secondary song only
        }
    }
}

if ($secondary_song instanceof WP_Post && !$hide_secondary) {
    $song_link  = get_permalink($secondary_song);
    $song_title = get_the_title($secondary_song);
    $video_img  = get_field('video_screenshot', $secondary_song->ID);
    $video_url  = $video_img ? $video_img['sizes']['large'] : '';

    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo '<h4><span style="font-size:1.1em;">🎥</span> ' . esc_html($song_title) . '</h4>';

    if ($video_url) {
        echo '<div style="margin-top:10px;">';
        echo '<a href="' . esc_url($song_link) . '">';
        echo '<img src="' . esc_url($video_url) . '" alt="' . esc_attr($song_title) . ' video screenshot" style="max-width:100%;height:auto;border-radius:8px;display:block;margin:0 auto;">';
        echo '</a>';
        echo '</div>';
    }

    echo '</div>';
}

  echo '</div>'; // .referenced-works
  return ob_get_clean();
}
add_shortcode('referenced_works', 'display_referenced_works');


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
