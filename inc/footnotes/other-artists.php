<?php
// inc/footnotes/other-artists.php
// ===============================
// Other Artists section
// ===============================

function fn_other_artists($chapter_id, $group_titles) {
    ob_start();

    $song_rows = get_field('chapter_songs', $chapter_id) ?: [];
    $other_artists = [];

    foreach ($song_rows as $row) {
        if (empty($row['song']) || !$row['song'] instanceof WP_Post) {
            continue;
        }

        $song_post   = $row['song'];
        $song_title  = get_the_title($song_post);
        $artist_id   = get_field('song_artist', $song_post->ID);
        $artist_post = $artist_id ? get_post($artist_id) : null;
        $role        = $row['role'] ?? 'supporting';

        if (in_array($role, ['primary', 'secondary'])) {
            continue; // skip featured
        }

        $artist_id  = $artist_post instanceof WP_Post ? $artist_post->ID : 'unknown';
        $artist_obj = $artist_post instanceof WP_Post ? $artist_post : (object)[
            'ID' => 'unknown',
            'post_title' => 'Unknown Artist'
        ];

        if (!isset($other_artists[$artist_id])) {
            $other_artists[$artist_id] = [
                'post'  => $artist_obj,
                'songs' => [],
            ];
        }
        $other_artists[$artist_id]['songs'][] = $song_title;
    }

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
