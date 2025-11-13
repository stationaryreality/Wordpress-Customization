<?php
// inc/footnotes/people.php
// ===============================
// People Referenced (with bio excerpts)
// ===============================

function fn_people($chapter_id, $group_titles) {
    ob_start();

    $people = get_field('people_referenced', $chapter_id) ?: [];
    if (empty($people)) return '';

    // Local helper: fetch Wikipedia intro
    if (!function_exists('get_wikipedia_intro')) {
        function get_wikipedia_intro($slug) {
            $api_url = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($slug);
            $response = wp_remote_get($api_url, ['timeout' => 3]);
            if (is_wp_error($response)) return false;
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            return !empty($data['extract']) ? esc_html($data['extract']) : false;
        }
    }

    $meta = $group_titles['profile'];
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
            <span style=\"font-size:1.1em;\">{$meta['emoji']}</span>
            <span style=\"text-decoration:underline;\">{$meta['title']}</span>
          </a></h4><ul>";

    foreach ($people as $person) {
        $title     = esc_html(get_the_title($person));
        $link      = get_permalink($person);
        $img       = get_field('portrait_image', $person->ID);
        $thumb     = $img ? "<a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:8px;\"></a>" : '';

        // Prefer manual bio, else Wikipedia summary
        $bio       = trim(strip_tags(get_field('bio', $person->ID)));
        if (!$bio && ($slug = get_field('wikipedia_slug', $person->ID))) {
            $bio = get_wikipedia_intro($slug);
        }

        // Shorten the bio for compact display
        if ($bio) {
            $words = explode(' ', $bio);
            if (count($words) > 50) {
                $bio = implode(' ', array_slice($words, 0, 50)) . '…';
            }
        }

        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;margin-bottom:1em;\">{$thumb}<div>";
        echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";
        if ($bio) {
            echo "<br><span style=\"font-size:0.9em;color:#666;line-height:1.4;\">" . esc_html($bio) . "</span>";
        }
        echo "</div></li>";
    }

    echo '</ul></div>';
    return ob_get_clean();
}
