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

  $icons = [
    'artist'   => '🎹',
    'profile'  => '👤',
    'book'     => '📚',
    'concept'  => '🔎',
    'movie'    => '🎬',
    'quote'    => '💬',
    'lyric'    => '📻',
  ];

  $group_titles = [
    'featured_artists'    => ['title' => 'Artists Featured',     'emoji' => '🎹', 'link' => '/artists-featured'],
    'referenced_artists'  => ['title' => 'Artists Referenced',   'emoji' => '🎹', 'link' => '/artists-referenced'],
    'profile'             => ['title' => 'People Referenced',    'emoji' => '👤', 'link' => '/profiles'],
    'lyric'               => ['title' => 'Song Excerpts',        'emoji' => '📻', 'link' => '/lyrics'],
    'quote'               => ['title' => 'Quote Library',        'emoji' => '💬', 'link' => '/quotes'],
    'concept'             => ['title' => 'Lexicon',              'emoji' => '🔎', 'link' => '/concepts'],
    'book'                => ['title' => 'Books Cited',          'emoji' => '📚', 'link' => '/books'],
    'movie'               => ['title' => 'Movies Referenced',    'emoji' => '🎬', 'link' => '/movies'],
    'manual_songs'        => ['title' => 'Songs Referenced',     'emoji' => '📻'],
  ];

  $featured_artists = [];
  foreach (['primary_artist', 'secondary_artist', 'tertiary_artist'] as $field) {
    $artist = get_field($field, $chapter_id);
    if ($artist instanceof WP_Post) {
      $featured_artists[$artist->ID] = $artist;
    }
  }

  $linked_items = [
    'featured_artists'   => $featured_artists,
    'referenced_artists' => [],
    'profile'            => [],
    'lyric'              => [],
    'quote'              => [],
    'concept'            => [],
    'book'               => [],
    'movie'              => [],
  ];

  $fields = [
    'people_referenced'    => 'profile',
    'books_cited'          => 'book',
    'concepts_referenced'  => 'concept',
    'movies_referenced'    => 'movie',
    'quotes_referenced'    => 'quote',
  ];

  foreach ($fields as $field => $type) {
    $value = get_field($field, $chapter_id);
    if (!empty($value) && is_array($value)) {
      foreach ($value as $item) {
        if ($item instanceof WP_Post) {
          $linked_items[$type][$item->ID] = $item;
        }
      }
    }
  }

  $lyrics = get_field('lyrics_referenced', $chapter_id);
  if (!empty($lyrics)) {
    foreach ($lyrics as $item) {
      if ($item instanceof WP_Post) {
        $linked_items['lyric'][$item->ID] = $item;
      }
    }
  }

  $referenced_songs = get_field('songs_referenced', $chapter_id);
  $manual_songs = [];
  if (!empty($referenced_songs)) {
    foreach ($referenced_songs as $row) {
      $artist = $row['referenced_artist'];
      $song   = $row['referenced_song_title'];
      if ($artist instanceof WP_Post) {
        $linked_items['referenced_artists'][$artist->ID] = $artist;
        $manual_songs[] = [
          'artist' => $artist->post_title,
          'artist_id' => $artist->ID,
          'link' => get_permalink($artist),
          'is_cpt' => true,
          'song' => $song
        ];
      }
    }
  }

  $other_songs = get_field('other_songs_referenced', $chapter_id);
  if (!empty($other_songs)) {
    foreach ($other_songs as $row) {
      $manual_songs[] = [
        'artist' => $row['artist_name'],
        'song'   => $row['song_title'],
        'is_cpt' => false
      ];
    }
  }

  echo '<div class="referenced-works">';
  echo '<h3 style="margin-bottom: 1.5em;"><strong>Referenced Works & People</strong></h3>';

  foreach ($group_titles as $group_key => $meta) {
    $items = ($group_key === 'manual_songs') ? $manual_songs : ($linked_items[$group_key] ?? []);
    if (empty($items)) continue;

    $title = $meta['emoji'] . ' ' . $meta['title'];
    $slug_link = $meta['link'] ?? null;

    echo '<div class="referenced-group" style="margin-top: 2em;">';
    echo $slug_link
      ? "<h4 style=\"margin-bottom: 0.5em;\"><a href=\"{$slug_link}\"><strong>{$title}</strong></a></h4>"
      : "<h4 style=\"margin-bottom: 0.5em;\"><strong>{$title}</strong></h4>";
    echo '<ul>';

    if ($group_key === 'manual_songs') {
      usort($items, fn($a, $b) => strcasecmp($a['artist'], $b['artist']));
      foreach ($items as $song) {
        $artist = esc_html($song['artist']);
        $song_title = esc_html($song['song']);
        if ($song['is_cpt']) {
          echo "<li><strong><a href=\"{$song['link']}\">{$artist}</a></strong> – {$song_title}</li>";
        } else {
          echo "<li><strong>{$artist}</strong> – {$song_title}</li>";
        }
      }
    } else {
      uasort($items, fn($a, $b) => strcasecmp($a->post_title, $b->post_title));
      foreach ($items as $item) {
        $title = get_the_title($item);
        $permalink = get_permalink($item);
        $type = get_post_type($item);
        $thumb = '';

        // ACF image logic
        if (in_array($type, ['artist', 'profile'])) {
          $image = get_field('portrait_image', $item->ID);
          if ($image) {
            $thumb = "<img src=\"{$image['sizes']['thumbnail']}\" alt=\"\" style=\"width:48px;height:48px;border-radius:50%;margin-right:8px;vertical-align:middle;\">";
          }
        } elseif (in_array($type, ['book', 'movie'])) {
          $image = get_field('cover_image', $item->ID);
          if ($image) {
            $thumb = "<img src=\"{$image['sizes']['medium']}\" alt=\"\" style=\"width:60px;height:auto;margin-right:10px;vertical-align:middle;\">";
          }
        } elseif (has_post_thumbnail($item->ID)) {
          $thumb = get_the_post_thumbnail($item->ID, 'thumbnail', ['style' => 'width:48px;height:auto;margin-right:10px;vertical-align:middle;']);
        }

        echo "<li style=\"margin-left: 1em;display: flex;align-items: center;gap:10px;margin-bottom: 0.6em;\">{$thumb}<a href=\"{$permalink}\"><strong>{$title}</strong></a>";

        if ($type === 'concept') {
          $def = get_field('definition', $item->ID);
          if ($def) echo ": <span>{$def}</span>";
        }

        echo "</li>";
      }
    }

    echo '</ul></div>';
  }

  echo '</div>';
  return ob_get_clean();
}
add_shortcode('referenced_works', 'display_referenced_works');
