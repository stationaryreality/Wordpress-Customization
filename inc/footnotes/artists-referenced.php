<?php

function fn_artists_referenced($chapter_id, $group_titles) {

    $context = kp_build_reference_context($chapter_id);
    $artists = $context['artist'] ?? [];

    if (empty($artists)) {
        return '';
    }

    ob_start();

    $meta = $group_titles['artists_referenced'] ?? null;

    echo '<div class="referenced-group" style="margin-top:2em;">';

    if ($meta) {
        echo "<h4>
                <span style=\"font-size:1.1em;\">{$meta['emoji']}</span>
                <span style=\"text-decoration:underline;\">{$meta['title']}</span>
              </h4>";
    }

    echo '<ul>';

    foreach ($artists as $artist) {

        $title = esc_html(get_the_title($artist));
        $link  = get_permalink($artist);

        $img = get_field('portrait_image', $artist->ID);
        $thumb = $img
            ? '<a href="' . esc_url($link) . '">
                <img src="' . esc_url($img['sizes']['thumbnail']) . '"
                style="width:48px;height:48px;border-radius:50%;margin-right:8px;">
               </a>'
            : '';

        echo '<li style="display:flex;align-items:center;gap:10px;margin-bottom:0.6em;">';
        echo $thumb;
        echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";
        echo '</li>';
    }

    echo '</ul></div>';

    return ob_get_clean();
}