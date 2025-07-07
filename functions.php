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

// Sort CPTs ABC, except for pages and chapters
add_action('load-edit.php', function () {
    $screen = get_current_screen();

    // CPTs to force alphabetical sorting in admin
    $alphabetical_cpts = array(
        'concept',
        'lyric',
        'quote',
        'artist',
        'book',
        'movie',
        'profile'
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


function render_custom_cover_block($atts) {
    $atts = shortcode_atts(['id' => ''], $atts, 'custom_cover');

    if (!$atts['id']) return '';

    $html = get_field('lyric_html_block', $atts['id']);
    return $html ?: '';
}
add_shortcode('custom_cover', 'render_custom_cover_block');


function display_referenced_works() {
  ob_start();
  $chapter_id = get_the_ID();

  echo '<div class="referenced-works">';

  $group_titles = [
    'featured_artists'      => ['title' => 'Artists Featured',       'emoji' => '🎹', 'link' => '/artists-featured'],
    'other_artists'         => ['title' => 'Other Artists Featured', 'emoji' => '🎹', 'link' => '/artists-featured'],
    'songs_referenced'      => ['title' => 'Songs Referenced',       'emoji' => '📻', 'link' => '/song-excerpts'],
    'profile'               => ['title' => 'People Referenced',      'emoji' => '👤', 'link' => '/people-referenced'],
    'lyric'                 => ['title' => 'Song Excerpts',          'emoji' => '📻', 'link' => '/song-excerpts'],
    'quote'                 => ['title' => 'Quote Library',          'emoji' => '💬', 'link' => '/quotes'],
    'concept'               => ['title' => 'Lexicon',                'emoji' => '🔎', 'link' => '/concepts'],
    'book'                  => ['title' => 'Books Cited',            'emoji' => '📚', 'link' => '/books'],
    'movie'                 => ['title' => 'Movies Referenced',      'emoji' => '🎬', 'link' => '/movies'],
    'reference'             => ['title' => 'External References',    'emoji' => '📰', 'link' => '/references']
  ];

  // === Featured Artists (Primary - Manual Order) ===
  $fields = [
    ['primary_artist', 'primary_song_title'],
    ['secondary_artist', 'secondary_song_title'],
    ['tertiary_artist', 'tertiary_song_title'],
    ['quaternary_artist', 'quaternary_song_title']
  ];
  $featured = [];
  foreach ($fields as [$artist_field, $song_field]) {
    $artist = get_field($artist_field);
    if (!$artist) continue;
    $featured[] = [
      'artist' => $artist,
      'song'   => get_field($song_field)
    ];
  }

  if (!empty($featured)) {
    $meta = $group_titles['featured_artists'];
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
    foreach ($featured as $entry) {
      $artist = $entry['artist'];
      $song = $entry['song'];
      $img = get_field('portrait_image', $artist->ID);
      $thumb = $img ? "<img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:8px;\">" : '';
      $link = get_permalink($artist);
      $title = esc_html(get_the_title($artist));
      echo "<li style=\"display:flex;align-items:center;gap:10px;margin-bottom:0.6em;\">{$thumb}<div><a href=\"{$link}\"><strong>{$title}</strong></a>";
      if ($song) echo "<br><span style=\"font-size:0.9em;color:#666;\">{$song}</span>";
      echo "</div></li>";
    }
    echo '</ul></div>';
  }

  // === Other Artists Featured ===
  $featured_ids = array_column($featured, 'artist', 'ID');
  $cpt_songs = get_field('songs_referenced') ?: [];
  $other_artists = [];
  foreach ($cpt_songs as $row) {
    $artist = $row['referenced_artist'];
    $song   = $row['referenced_song_title'];
    if (!$artist instanceof WP_Post || !$song) continue;
    if (in_array($artist->ID, array_column($featured, 'artist', 'ID'))) continue;
    $other_artists[$artist->ID] = ['post' => $artist, 'song' => $song];
  }
  if (!empty($other_artists)) {
    uasort($other_artists, fn($a, $b) => strcmp($a['post']->post_title, $b['post']->post_title));
    $meta = $group_titles['other_artists'];
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
    foreach ($other_artists as $entry) {
      $artist = $entry['post'];
      $song = $entry['song'];
      $img = get_field('portrait_image', $artist->ID);
      $thumb = $img ? "<img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:8px;\">" : '';
      $link = get_permalink($artist);
      $title = esc_html(get_the_title($artist));
      echo "<li style=\"display:flex;align-items:center;gap:10px;margin-bottom:0.6em;\">{$thumb}<div><a href=\"{$link}\"><strong>{$title}</strong></a><br><span style=\"font-size:0.9em;color:#666;\">{$song}</span></div></li>";
    }
    echo '</ul></div>';
  }

  // === Manual Songs Referenced ===
  $manual_songs = get_field('other_songs_referenced') ?: [];
  if (!empty($manual_songs)) {
    $meta = $group_titles['songs_referenced'];
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
    usort($manual_songs, fn($a, $b) => strcmp($a['artist_name'], $b['artist_name']));
    foreach ($manual_songs as $row) {
      $artist = esc_html($row['artist_name']);
      $song   = esc_html($row['song_title']);
      echo "<li><strong>{$artist}</strong> – {$song}</li>";
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
    $thumb = $img ? "<a href=\"{$link}\"><img src=\"{$img}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:10px;\"></a>" : '';

    echo "<li style=\"display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;\">{$thumb}<div>";
    echo "<div><a href=\"{$link}\"><strong>{$title}</strong></a></div>";
    if ($src) echo "<div><em>{$src}</em></div>";
    if ($url) echo "<div><a href=\"{$url}\" target=\"_blank\" rel=\"noopener noreferrer\">Link</a></div>";
    echo "</div></li>";
  }
  echo '</ul></div>';
}


  echo '</div>'; // .referenced-works
  return ob_get_clean();
}
add_shortcode('referenced_works', 'display_referenced_works');
