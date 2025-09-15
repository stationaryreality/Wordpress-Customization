<?php
// inc/footnotes/featured-artists.php
// ===============================
// Featured Artists section
// ===============================

function fn_featured_artists($chapter_id, $group_titles) {
    ob_start();

    $song_rows = get_field('chapter_songs', $chapter_id) ?: [];
    $featured  = [];

    // Separate featured (primary + secondary)
    foreach ($song_rows as $row) {
        if (empty($row['song']) || !$row['song'] instanceof WP_Post) {
            continue;
        }

        $song_post   = $row['song'];
        $song_title  = get_the_title($song_post);
        $artist_id   = get_field('song_artist', $song_post->ID);
        $artist_post = $artist_id ? get_post($artist_id) : null;
        $role        = $row['role'] ?? 'supporting';

        if (!in_array($role, ['primary', 'secondary'])) {
            continue; // skip non-featured
        }

        // Fallback for missing artist
        $artist_id  = $artist_post instanceof WP_Post ? $artist_post->ID : 'unknown';
        $artist_obj = $artist_post instanceof WP_Post ? $artist_post : (object)[
            'ID' => 'unknown',
            'post_title' => 'Unknown Artist'
        ];

        if (!isset($featured[$artist_id])) {
            $featured[$artist_id] = [
                'post'  => $artist_obj,
                'songs' => [],
            ];
        }
        $featured[$artist_id]['songs'][] = $song_title;
    }

    // Output Featured Artists
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

    return ob_get_clean();
}
