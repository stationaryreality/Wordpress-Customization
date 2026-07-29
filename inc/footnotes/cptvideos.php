<?php
// inc/footnotes/cptvideos.php
// ===============================
// Videos Cited
// ===============================

function fn_cptvideos($chapter_id, $group_titles) {
    $context = kp_build_reference_context($chapter_id);
    $videos = $context['video'] ?? [];

    if (empty($videos) || !is_array($videos)) {
        return '';
    }

    uasort($videos, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();

    $meta = $group_titles['video'] ?? [
        'title' => 'Videos',
        'emoji' => '📼',
        'link'  => '/videos/'
    ];

    echo '<div class="cpt-video-footnote-group">';
    
    echo "<h4 class=\"cpt-video-footnote-title\">";
    echo "<a href=\"{$meta['link']}\">";
    echo "<span>{$meta['emoji']}</span> ";
    echo "<span>{$meta['title']}</span>";
    echo "</a>";
    echo "</h4>";

    echo '<div class="cpt-video-footnote-grid">';

    foreach ($videos as $video_post) {
        $title = esc_html(get_the_title($video_post));
        $link  = get_permalink($video_post);

        $image = get_field('video_screenshot', $video_post->ID);
        $img_url = $image ? $image['sizes']['large'] : get_the_post_thumbnail_url($video_post->ID, 'large');

        if (!$img_url) {
            continue;
        }

        echo '<div class="cpt-video-footnote-item">';
        echo "<a href=\"{$link}\" title=\"{$title}\">";
        echo "<img src=\"{$img_url}\" alt=\"{$title}\" class=\"cpt-video-footnote-thumb\">";
        echo "</a>";
        echo "<p class=\"cpt-video-footnote-caption\">{$title}</p>";
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';

    return ob_get_clean();
}