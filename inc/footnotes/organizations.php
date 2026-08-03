<?php
// inc/footnotes/organizations.php
// ===============================
// Organizations Referenced
// ===============================

function fn_organizations($chapter_id, $group_titles) {

    $context = kp_build_reference_context($chapter_id);
    $items = $context['organization'] ?? [];

    if (empty($items)) {
        return '';
    }

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

    echo '<div class="cpt-organization-footnote-group">';
    echo "<h4 class=\"cpt-organization-footnote-title\">";
    echo "<a href=\"{$meta['link']}\">";
    echo "<span>{$meta['emoji']}</span> ";
    echo "<span>{$meta['title']}</span>";
    echo "</a>";
    echo "</h4>";
    echo '<ul class="cpt-organization-footnote-list">';

    foreach ($items as $org) {
        $title = esc_html(get_the_title($org));
        $link  = get_permalink($org);
        $cover = get_field('cover_image', $org->ID);
        
        $thumb = $cover 
            ? "<div class=\"cpt-organization-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$cover['url']}\" alt=\"{$title}\"></a></div>" 
            : '';

// Manual bio (preferred) or fallback to wiki
$manual_bio = get_field('organization_bio_manual', $org->ID);
if ($manual_bio) {
    $manual_bio = esc_html($manual_bio);
}

        $wiki_slug  = get_field('wikipedia_slug', $org->ID);
        $wiki_bio   = $wiki_slug ? get_wikipedia_intro($wiki_slug) : false;

        $bio_text = $manual_bio ?: $wiki_bio;
        if ($bio_text && !$manual_bio) {
            $bio_text = wp_trim_words($bio_text, 60, '...');
        }

        echo '<li class="cpt-organization-footnote-item">';
        echo $thumb;
        
        echo '<div class="cpt-organization-footnote-details">';
        echo "<a href=\"{$link}\">{$title}</a>";

        if ($bio_text) {
            echo "<div class=\"cpt-organization-footnote-bio\">{$bio_text}</div>";
        }

        echo '</div>'; // end cpt-organization-footnote-details
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';
    
    return ob_get_clean();
}