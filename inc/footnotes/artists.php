<?php
// inc/footnotes/artists.php
// ===================================
// Shared Artist Footnote Renderer
// ===================================

/**
 * Collect artists from chapter_songs and split them into
 * featured (primary/secondary) and other (supporting).
 */
function fn_collect_chapter_artists($chapter_id) {

    $song_rows = get_field('chapter_songs', $chapter_id) ?: [];
    $context = kp_build_reference_context($chapter_id);
    $referenced_songs = $context['song'] ?? [];

    $artists = [
        'featured' => [],
        'other'    => [],
    ];

    // --- EXISTING LOOP (leave this as is) ---
    foreach ($song_rows as $row) {

        if (empty($row['song']) || !$row['song'] instanceof WP_Post) {
            continue;
        }

        $song_post   = $row['song'];
        $song_title  = get_the_title($song_post);
        $artist_id   = get_field('song_artist', $song_post->ID);
        $artist_post = $artist_id ? get_post($artist_id) : null;
        $role        = $row['role'] ?? 'supporting';

        $group = in_array($role, ['primary', 'secondary'])
            ? 'featured'
            : 'other';

        $artist_key = $artist_post instanceof WP_Post
            ? $artist_post->ID
            : 'unknown';

        $artist_obj = $artist_post instanceof WP_Post
            ? $artist_post
            : (object)[
                'ID' => 'unknown',
                'post_title' => 'Unknown Artist',
            ];

        if (!isset($artists[$group][$artist_key])) {
            $artists[$group][$artist_key] = [
                'post'  => $artist_obj,
                'songs' => [],
            ];
        }

        $artists[$group][$artist_key]['songs'][] = $song_title;
    }
    // --- END OF EXISTING LOOP ---

    // <<< ========== THIS IS WHERE THE NEW CODE GOES ========== >>>
    // Add inherited songs from attached Elements.
    foreach ($referenced_songs as $song_post) {

        if (!$song_post instanceof WP_Post) {
            continue;
        }

        $song_title = get_the_title($song_post);

        $artist_id   = get_field('song_artist', $song_post->ID);
        $artist_post = $artist_id ? get_post($artist_id) : null;

        $artist_key = $artist_post instanceof WP_Post
            ? $artist_post->ID
            : 'unknown';

        $artist_obj = $artist_post instanceof WP_Post
            ? $artist_post
            : (object)[
                'ID' => 'unknown',
                'post_title' => 'Unknown Artist',
            ];

        if (!isset($artists['other'][$artist_key])) {
            $artists['other'][$artist_key] = [
                'post'  => $artist_obj,
                'songs' => [],
            ];
        }

        // Avoid duplicate song titles under the same artist.
        if (!in_array($song_title, $artists['other'][$artist_key]['songs'], true)) {
            $artists['other'][$artist_key]['songs'][] = $song_title;
        }
    }
    // <<< ========== END OF NEW CODE ========== >>>

    return $artists;
}

/**
 * Shared renderer for artist groups.
 */
function fn_render_artist_group($artists, $meta, $alphabetize = false) {

    if (empty($artists)) {
        return '';
    }

    if ($alphabetize) {
        uasort($artists, function($a, $b) {
            return strcmp($a['post']->post_title, $b['post']->post_title);
        });
    }

    ob_start();

    echo '<div class="referenced-group" style="margin-top:2em;">';

    echo "<h4>
            <a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
                <span style=\"font-size:1.1em;\">{$meta['emoji']}</span>
                <span style=\"text-decoration:underline;\">{$meta['title']}</span>
            </a>
          </h4>";

    echo '<ul>';

    foreach ($artists as $entry) {

        $artist = $entry['post'];
        $songs  = $entry['songs'];

        if ($artist->ID !== 'unknown') {

            setup_postdata($artist);

            $img = get_field('portrait_image', $artist->ID);

            $link = get_permalink($artist);

            $title = esc_html(get_the_title($artist));

            $thumb = $img
                ? '<a href="' . $link . '"><img src="' .
                    esc_url($img['sizes']['thumbnail']) .
                    '" style="width:48px;height:48px;border-radius:50%;margin-right:8px;"></a>'
                : '';

        } else {

            $thumb = '';

            $link = '#';

            $title = esc_html($artist->post_title);
        }

        echo '<li style="display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;">';

        echo $thumb;

        echo '<div>';

        echo '<a href="' . esc_url($link) . '"><strong>' . $title . '</strong></a>';

        foreach ($songs as $song) {
            echo '<br><span style="font-size:0.9em;color:#666;">'
                . esc_html($song)
                . '</span>';
        }

        echo '</div>';

        echo '</li>';

        if ($artist->ID !== 'unknown') {
            wp_reset_postdata();
        }
    }

    echo '</ul>';

    echo '</div>';

    return ob_get_clean();
}

/**
 * Featured Artists wrapper.
 */
function fn_featured_artists($chapter_id, $group_titles) {

    $artists = fn_collect_chapter_artists($chapter_id);

    return fn_render_artist_group(
        $artists['featured'],
        $group_titles['featured_artists']
    );
}

/**
 * Other Artists wrapper.
 */
function fn_other_artists($chapter_id, $group_titles) {

    $artists = fn_collect_chapter_artists($chapter_id);

    return fn_render_artist_group(
        $artists['other'],
        $group_titles['other_artists'],
        true
    );
}