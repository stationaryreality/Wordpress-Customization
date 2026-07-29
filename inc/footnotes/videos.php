<?php
// inc/footnotes/videos.php
// ===============================
// Song Videos in Footnotes
// ===============================

function fn_videos($chapter_id, $group_titles) {
    $chapter_songs = get_field('chapter_songs', $chapter_id);
    if (empty($chapter_songs) || !is_array($chapter_songs)) {
        return '';
    }

    ob_start();
    $hide_secondary = get_field('hide_secondary_song_in_footnotes', $chapter_id);

    foreach (['primary', 'secondary'] as $role) {
        if ($role === 'secondary' && $hide_secondary) {
            continue;
        }

        foreach ($chapter_songs as $row) {
            if (!empty($row['role']) && $row['role'] === $role 
                && !empty($row['song']) && $row['song'] instanceof WP_Post) {

                $song       = $row['song'];
                $song_link  = get_permalink($song);
                $song_title = get_the_title($song);
                $video_img  = get_field('video_screenshot', $song->ID);
                $video_url  = $video_img ? $video_img['sizes']['large'] : '';

                echo '<div class="footnote-song-video-group">';
                echo '<h4 class="footnote-song-video-title"><span>🎥</span> ' . esc_html($song_title) . '</h4>';

                if ($video_url) {
                    echo '<div class="footnote-song-video-wrapper">';
                    echo '<a href="' . esc_url($song_link) . '">';
                    echo '<img src="' . esc_url($video_url) . '" alt="' . esc_attr($song_title) . ' video screenshot" class="footnote-song-video-thumb">';
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
    if (empty($chapter_songs) || !is_array($chapter_songs)) {
        return '';
    }

    foreach ($chapter_songs as $row) {
        if (!empty($row['role']) && $row['role'] === 'secondary' 
            && !empty($row['song']) && $row['song'] instanceof WP_Post) {
            
            $song       = $row['song'];
            $song_link  = get_permalink($song);
            $video_img  = get_field('video_screenshot', $song->ID);
            $video_url  = $video_img ? $video_img['sizes']['large'] : '';

            if ($video_url) {
                return '<div class="shortcode-secondary-song-image">
                        <a href="' . esc_url($song_link) . '">
                            <img src="' . esc_url($video_url) . '" alt="" class="footnote-song-video-thumb">
                        </a>
                        </div>';
            }
        }
    }

    return '';
}
add_shortcode('secondary_song_image', 'secondary_song_image_shortcode');