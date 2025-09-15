<?php
// inc/footnotes/images.php

function fn_images($chapter_id, $group_titles) {
    $items = get_field('images_linked', $chapter_id) ?: [];
    if (empty($items)) return '';

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();
    $meta = $group_titles['image'];

    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
            <span style=\"font-size:1.1em;\">{$meta['emoji']}</span> 
            <span style=\"text-decoration:underline;\">{$meta['title']}</span>
          </a></h4><ul>";

    foreach ($items as $img_post) {
        $title = esc_html(get_the_title($img_post));
        $link  = get_permalink($img_post);
        $image = get_field('image_file', $img_post->ID);
        $thumb_url = $image ? $image['sizes']['medium'] : '';

        $thumb = $thumb_url ? "<a href=\"{$link}\"><img src=\"{$thumb_url}\" 
                    alt=\"{$title}\" style=\"width:60px;height:auto;
                    margin-right:10px;\"></a>" : '';

        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;
                    margin-bottom:0.6em;\">{$thumb}
              <div><a href=\"{$link}\"><strong>{$title}</strong></a></div></li>";
    }

    echo '</ul></div>';
    return ob_get_clean();
}
