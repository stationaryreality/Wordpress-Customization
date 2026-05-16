<?php
// inc/footnotes/cptvideos.php

function fn_cptvideos($chapter_id, $group_titles) {

    $videos = get_field('videos_linked', $chapter_id);

    if (empty($videos) || !is_array($videos)) {
        return '';
    }

    uasort($videos, fn($a, $b) =>
        strcmp(get_the_title($a), get_the_title($b))
    );

    ob_start();

    $meta = $group_titles['video'] ?? [
        'title' => 'Videos',
        'emoji' => '📼',
        'link'  => '/videos/'
    ];

    echo '<div class="referenced-group" style="margin-top:2em;">';

    echo "<h4>
            <a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
                <span style=\"font-size:1.1em;\">{$meta['emoji']}</span>
                <span style=\"text-decoration:underline;\">
                    {$meta['title']}
                </span>
            </a>
          </h4>";

    echo '<div class="mini-video-grid">';

    foreach ($videos as $video_post) {

        $title = esc_html(get_the_title($video_post));
        $link  = get_permalink($video_post);

        $image = get_field('video_screenshot', $video_post->ID);

        $img_url = $image
            ? $image['sizes']['large']
            : get_the_post_thumbnail_url($video_post->ID, 'large');

        if (!$img_url) continue;

        echo "<div class='mini-video-item'>";

        echo "<a href='{$link}' title='{$title}' style='display:block;'>";

        echo "<img
                src='{$img_url}'
                alt='{$title}'
                class='mini-video-thumb'
              >";

        echo "</a>";

        echo "<p class='mini-video-title'>
                {$title}
              </p>";

        echo "</div>";
    }

    echo '</div>';
    echo '</div>';

    return ob_get_clean();
}