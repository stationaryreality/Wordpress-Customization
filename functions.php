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

function display_referenced_works() {
  ob_start();
  $chapter_id = get_the_ID();

  $group_titles = [
    'featured_artists' => ['title' => 'Artists Featured',     'emoji' => '🎹', 'link' => '/artists-featured'],
    'songs_referenced' => ['title' => 'Songs Referenced',     'emoji' => '📻'],
    'profile'          => ['title' => 'People Referenced',    'emoji' => '👤', 'link' => '/profiles'],
    'lyric'            => ['title' => 'Song Excerpts',        'emoji' => '📻', 'link' => '/lyrics'],
    'quote'            => ['title' => 'Quote Library',        'emoji' => '💬', 'link' => '/quotes'],
    'concept'          => ['title' => 'Lexicon',              'emoji' => '🔎', 'link' => '/concepts'],
    'book'             => ['title' => 'Books Cited',          'emoji' => '📚', 'link' => '/books'],
    'movie'            => ['title' => 'Movies Referenced',    'emoji' => '🎬', 'link' => '/movies'],
  ];

  // Collect featured artists (in order)
  $featured_artists = [];
  foreach (['primary_artist', 'secondary_artist', 'tertiary_artist'] as $field) {
    $artist = get_field($field);
    if (is_array($artist)) {
      $artist = reset($artist); // Extract object from Relationship field
    }
    if ($artist instanceof WP_Post) {
      $song_title = '';
      switch ($field) {
        case 'primary_artist':
          $song_title = get_field('primary_song_title');
          break;
        case 'secondary_artist':
          $song_title = get_field('secondary_song_title');
          break;
        case 'tertiary_artist':
          $song_title = get_field('tertiary_song_title');
          break;
      }
      $featured_artists[] = [
        'post' => $artist,
        'song_title' => $song_title
      ];
    }
  }

  // Output: Artists Featured
  if (!empty($featured_artists)) {
    $meta = $group_titles['featured_artists'];
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo '<h4 style="margin-bottom: 0.5em;">' .
         ($meta['link'] ? "<a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a>" :
                          "<span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <strong>{$meta['title']}</strong>") .
         '</h4><ul>';
    foreach ($featured_artists as $entry) {
      $artist = $entry['post'];
      $img = get_field('portrait_image', $artist->ID);
      $thumb = $img ? "<img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:8px;\">" : '';
      $title = esc_html($artist->post_title);
      $link = get_permalink($artist);
      echo "<li style=\"display:flex;align-items:center;margin-bottom:0.6em;gap:10px;\">{$thumb}<div>";
      echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";
      if ($entry['song_title']) {
        echo "<br><span style=\"font-size:0.9em;color:#666;\">{$entry['song_title']}</span>";
      }
      echo "</div></li>";
    }
    echo '</ul></div>';
  }

  // Gather referenced songs: both CPT-based and manual
  $all_songs = ['cpt' => [], 'manual' => []];
  $placeholder_img = wp_get_attachment_image_src(19327, 'thumbnail');
  $placeholder_img = $placeholder_img ? $placeholder_img[0] : '';

  $songs = get_field('songs_referenced');
  if ($songs) {
    foreach ($songs as $row) {
      $artist = $row['referenced_artist'];
      $song   = $row['referenced_song_title'];
      if ($artist instanceof WP_Post && $song) {
        $link = get_permalink($artist->ID);
        $all_songs['cpt'][] = [
          'artist' => $artist->post_title,
          'song'   => $song,
          'link'   => $link,
        ];
      }
    }
  }

  $manual_songs = get_field('other_songs_referenced');
  if ($manual_songs) {
    foreach ($manual_songs as $row) {
      if (!$row['artist_name'] || !$row['song_title']) continue;
      $all_songs['manual'][] = [
        'artist' => $row['artist_name'],
        'song'   => $row['song_title']
      ];
    }
  }

  // Output: Songs Referenced (no images)
  if (!empty($all_songs['cpt']) || !empty($all_songs['manual'])) {
    $meta = $group_titles['songs_referenced'];
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4 style=\"margin-bottom: 0.5em;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <strong>{$meta['title']}</strong></h4>";

    echo '<ul style="margin-bottom: 1em;">';
    foreach ($all_songs['cpt'] as $entry) {
      echo "<li style=\"margin-left:1em;margin-bottom:0.5em;\">";
      echo "<strong>{$entry['artist']}</strong> – {$entry['song']}";
      echo "</li>";
    }
    foreach ($all_songs['manual'] as $entry) {
      echo "<li style=\"margin-left:1em;margin-bottom:0.5em;\">";
      echo "<strong>{$entry['artist']}</strong> – {$entry['song']}";
      echo "</li>";
    }
    echo '</ul></div>';
  }

  // Build list of referenced artists from songs_referenced
  $other_featured_artists = [];
  foreach ($all_songs['cpt'] as $entry) {
    $artist_id = url_to_postid($entry['link']);
    if ($artist_id && !in_array($artist_id, array_map(fn($a) => $a['post']->ID, $featured_artists))) {
      $artist_post = get_post($artist_id);
      if ($artist_post instanceof WP_Post) {
        $other_featured_artists[$artist_id] = $artist_post;
      }
    }
  }

  // Optional: Other Artists Featured
  if (!empty($other_featured_artists)) {
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo '<h4 style="margin-bottom: 0.5em;"><strong>Artists Featured (Other)</strong></h4>';
    echo '<ul>';

    foreach ($other_featured_artists as $artist) {
      $img = get_field('portrait_image', $artist->ID);
      $thumb = $img ? "<img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:8px;\">" : '';
      $title = esc_html($artist->post_title);
      $link = get_permalink($artist);
      echo "<li style=\"display:flex;align-items:center;margin-bottom:0.6em;gap:10px;\">{$thumb}<a href=\"{$link}\"><strong>{$title}</strong></a></li>";
    }

    echo '</ul></div>';
  }

  // Remaining groups
  $linked_items = [
    'profile' => [],
    'lyric'   => [],
    'quote'   => [],
    'concept' => [],
    'book'    => [],
    'movie'   => [],
  ];

  $acf_map = [
    'people_referenced'    => 'profile',
    'books_cited'          => 'book',
    'concepts_referenced'  => 'concept',
    'movies_referenced'    => 'movie',
    'quotes_referenced'    => 'quote',
    'lyrics_referenced'    => 'lyric',
  ];

  foreach ($acf_map as $acf => $type) {
    $items = get_field($acf);
    if (!empty($items)) {
      foreach ($items as $item) {
        if ($item instanceof WP_Post) {
          $linked_items[$type][$item->ID] = $item;
        }
      }
    }
  }

  foreach (['profile', 'lyric', 'quote', 'concept', 'book', 'movie'] as $type) {
    $items = $linked_items[$type];
    if (empty($items)) continue;

    $meta = $group_titles[$type];
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo '<h4 style="margin-bottom: 0.5em;">' .
         ($meta['link'] ? "<a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a>" :
                          "<span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <strong>{$meta['title']}</strong>") .
         '</h4><ul>';

    foreach ($items as $item) {
      $title = get_the_title($item);
      $link = get_permalink($item);
      $thumb = '';
      $style = 'width:60px;height:auto;margin-right:10px;';

      if ($type === 'profile') {
        $img = get_field('portrait_image', $item->ID);
        $thumb = $img ? "<img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;border-radius:50%;margin-right:10px;\">" : '';
      } elseif (in_array($type, ['book', 'movie'])) {
        $img = get_field('cover_image', $item->ID);
        $style = 'width:60px;height:auto;margin-right:10px;';
        $thumb = $img ? "<img src=\"{$img['sizes']['medium']}\" style=\"{$style}\">" : '';
      } elseif (has_post_thumbnail($item->ID)) {
        $thumb = get_the_post_thumbnail($item->ID, 'thumbnail', ['style' => $style]);
      }

      echo "<li style=\"margin-left:1em;display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;\">{$thumb}<div>";
      echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";
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

  echo '</div>';
  return ob_get_clean();
}
add_shortcode('referenced_works', 'display_referenced_works');