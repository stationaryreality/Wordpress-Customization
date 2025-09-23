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
                    echo '<div style="margin-top:10px;text-align:center;">';
                    echo '<a href="' . esc_url($song_link) . '">';
                    echo '<img src="' . esc_url($video_url) . '" 
                            alt="' . esc_attr($song_title) . ' video screenshot" 
                            style="max-width:100%;height:auto;border-radius:8px;">';
                    echo '</a>';
                    echo '</div>';
                }

                echo '</div>';
            }
        }
    }

    return ob_get_clean();
}

// Optional: keep the shortcode pointing to the same logic
function secondary_song_image_shortcode($atts = []) {
    $chapter_id = get_the_ID();
    $chapter_songs = get_field('chapter_songs', $chapter_id);
    if (empty($chapter_songs) || !is_array($chapter_songs)) return '';

    foreach ($chapter_songs as $row) {
        if (!empty($row['role']) && $row['role'] === 'secondary' 
            && !empty($row['song']) && $row['song'] instanceof WP_Post) {
            $song       = $row['song'];
            $song_link  = get_permalink($song);
            $video_img  = get_field('video_screenshot', $song->ID);
            $video_url  = $video_img ? $video_img['sizes']['large'] : '';

            if ($video_url) {
                return '<div class="secondary-song-image" style="margin:2em 0;text-align:center;">
                        <a href="' . esc_url($song_link) . '">
                            <img src="' . esc_url($video_url) . '" alt="" 
                                 style="max-width:100%;height:auto;border-radius:8px;">
                        </a>
                        </div>';
            }
        }
    }

    return '';
}
add_shortcode('secondary_song_image', 'secondary_song_image_shortcode');
