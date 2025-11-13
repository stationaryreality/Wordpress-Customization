<?php
// inc/footnotes/organizations.php

function fn_organizations($chapter_id, $group_titles) {
    $items = get_field('organizations_referenced', $chapter_id) ?: [];
    if (empty($items)) return '';

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

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

    ob_start();
    $meta = $group_titles['organization'];

    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
            <span style=\"font-size:1.1em;\">{$meta['emoji']}</span>
            <span style=\"text-decoration:underline;\">{$meta['title']}</span>
          </a></h4><ul>";

    foreach ($items as $org) {
        $title = esc_html(get_the_title($org));
        $link  = get_permalink($org);
        $cover = get_field('cover_image', $org->ID);
        $thumb = $cover ? "<a href=\"{$link}\"><img src=\"{$cover['url']}\" 
                    alt=\"{$title}\" style=\"width:60px;height:60px;
                    object-fit:cover;margin-right:10px;\"></a>" : '';

        // Manual bio (preferred) or fallback to wiki
        $manual_bio = get_field('organization_bio_manual', $org->ID);
        $wiki_slug  = get_field('wikipedia_slug', $org->ID);
        $wiki_bio   = $wiki_slug ? get_wikipedia_intro($wiki_slug) : false;

        $bio_text = $manual_bio ?: $wiki_bio;
        if ($bio_text && !$manual_bio) {
            $bio_text = wp_trim_words($bio_text, 60, '...');
        }

        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;
                    margin-bottom:0.8em;\">{$thumb}
              <div><a href=\"{$link}\"><strong>{$title}</strong></a>";

        if ($bio_text) {
            echo "<div style=\"margin-top:0.3em;font-size:0.95em;color:#555;\">{$bio_text}</div>";
        }

        echo "</div></li>";
    }

    echo '</ul></div>';
    return ob_get_clean();
}
