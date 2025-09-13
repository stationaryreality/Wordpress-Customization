<?php
// inc/footnotes.php
// ===================================
// Footnotes / Notes & References System
// ===================================

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
  'excerpt'               => ['title' => 'Excerpts Referenced',       'emoji' => '📖', 'link' => '/excerpt-library/'],


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
  'lyrics_referenced'    => 'lyric',
  'excerpts_referenced'  => 'excerpt'
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

    // === Thumbnails per type ===
    if ($type === 'profile') {
      $img = get_field('portrait_image', $item->ID);
      if ($img) {
        $src = $img['sizes']['thumbnail'];
        $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:10px;\"></a>";
      }
    } elseif ($type === 'lyric') {
      // Get linked song's cover_image
      $song = get_field('song', $item->ID);
      if ($song) {
        $img = get_field('cover_image', $song->ID);
        if ($img) {
          $src = $img['sizes']['thumbnail'];
          $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:10px;\"></a>";
        }
      }
    } elseif (in_array($type, ['quote', 'excerpt'])) {
      // Source can be book, movie, or reference
      $source = get_field('quote_source', $item->ID) ?: get_field('excerpt_source', $item->ID);
      if ($source) {
        $img = get_field('cover_image', $source->ID);
        if ($img) {
          $src = $img['sizes']['thumbnail'];
          $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:10px;\"></a>";
        } elseif (has_post_thumbnail($source->ID)) {
          $src = get_the_post_thumbnail_url($source->ID, 'thumbnail');
          $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:10px;\"></a>";
        }
      }
    } elseif (in_array($type, ['book', 'movie'])) {
      $img = get_field('cover_image', $item->ID);
      if ($img) {
        $src = $img['sizes']['thumbnail'];
        $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:10px;\"></a>";
      }
    } elseif ($type === 'concept') {
      if (has_post_thumbnail($item->ID)) {
        $src = get_the_post_thumbnail_url($item->ID, 'thumbnail');
        $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:10px;\"></a>";
      }
    }

    echo "<li style=\"display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;\">{$thumb}<div><a href=\"{$link}\"><strong>{$title}</strong></a>";

    // === Extra content by type ===
    if ($type === 'concept') {
      $def = get_field('definition', $item->ID);
      if ($def) echo "<div>{$def}</div>";
    } elseif ($type === 'quote') {
      $quote = get_field('quote_plain_text', $item->ID) ?: get_field('quote_html_block', $item->ID);
      if ($quote) echo "<div>{$quote}</div>";
      $src_post = get_field('quote_source', $item->ID);
      if ($src_post) {
        $src_title = esc_html(get_the_title($src_post));
        $src_link  = get_permalink($src_post);
        echo "<p style=\"margin-top:0.4rem;font-size:0.9rem;color:#666;\">Source: <a href=\"{$src_link}\">{$src_title}</a></p>";
      }
    } elseif ($type === 'excerpt') {
      $excerpt = get_field('excerpt_plain_text', $item->ID);
      if ($excerpt) {
        $excerpt = wp_trim_words($excerpt, 40, '...');
        echo "<div>{$excerpt}</div>";
      }
      $src_post = get_field('excerpt_source', $item->ID);
      if ($src_post) {
        $src_title = esc_html(get_the_title($src_post));
        $src_link  = get_permalink($src_post);
        echo "<p style=\"margin-top:0.4rem;font-size:0.9rem;color:#666;\">Source: <a href=\"{$src_link}\">{$src_title}</a></p>";
      }
    } elseif ($type === 'lyric') {
      $lyric = get_field('lyric_plain_text', $item->ID);
      if ($lyric) echo "<div>{$lyric}</div>";
      $song = get_field('song', $item->ID);
      if ($song) {
        $src_title = esc_html(get_the_title($song));
        $src_link  = get_permalink($song);
        echo "<p style=\"margin-top:0.4rem;font-size:0.9rem;color:#666;\">Source: <a href=\"{$src_link}\">{$src_title}</a></p>";
      }
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

// === Music Video Block (Primary Featured Song via repeater) ===
$chapter_songs = get_field('chapter_songs');
$primary_song   = null;

if (!empty($chapter_songs) && is_array($chapter_songs)) {
    foreach ($chapter_songs as $row) {
        if (!empty($row['role']) && $row['role'] === 'primary' && !empty($row['song']) && $row['song'] instanceof WP_Post) {
            $primary_song = $row['song'];
            break; // use first primary song
        }
    }
}

if ($primary_song instanceof WP_Post) {
    $song_link  = get_permalink($primary_song);
    $song_title = get_the_title($primary_song);
    $video_img  = get_field('video_screenshot', $primary_song->ID);
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
