<?php
// inc/footnotes/videos.php

function fn_videos($chapter_id, $group_titles) {
    $chapter_songs = get_field('chapter_songs', $chapter_id);
    if (empty($chapter_songs) || !is_array($chapter_songs)) return '';

    ob_start();
    $hide_secondary = get_field('hide_secondary_song_in_footnotes', $chapter_id);

    foreach (['primary', 'secondary'] as $role) {
        if ($role === 'secondary' && $hide_secondary) continue;

        foreach ($chapter_songs as $row) {
            if (!empty($row['role']) && $row['role'] === $role 
                && !empty($row['song']) && $row['song'] instanceof WP_Post) {

                $song       = $row['song'];
                $song_link  = get_permalink($song);
                $song_title = get_the_title($song);
                $video_img  = get_field('video_screenshot', $song->ID);
                $video_url  = $video_img ? $video_img['sizes']['large'] : '';

                echo '<div class="referenced-group" style="margin-top:2em;">';
                echo '<h4><span style="font-size:1.1em;">🎥</span> ' 
                     . esc_html($song_title) . '</h4>';

                if ($video_url) {
                    echo '<div style="margin-top:10px;">';
                    echo '<a href="' . esc_url($song_link) . '">';
                    echo '<img src="' . esc_url($video_url) . '" 
                            alt="' . esc_attr($song_title) . ' video screenshot" 
                            style="max-width:100%;height:auto;border-radius:8px;
                            display:block;margin:0 auto;">';
                    echo '</a>';
                    echo '</div>';
                }

                echo '</div>';
            }
        }
    }

    return ob_get_clean();
}
