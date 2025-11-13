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
          </a></h4>";

echo '<div class="mini-image-grid" style="
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(90px, 1fr));
    gap:10px;
    margin:0.8em 0 0 1.5em; /* ← subtle indent */
  ">';


    foreach ($items as $img_post) {
        $title = esc_html(get_the_title($img_post));
        $link  = get_permalink($img_post);
        $image = get_field('image_file', $img_post->ID);
        $img_url = $image ? $image['sizes']['medium'] : get_the_post_thumbnail_url($img_post->ID, 'medium');
        $caption = get_field('image_caption', $img_post->ID);

        if (!$img_url) continue;

        echo "<div class='mini-image-item' style='text-align:center;'>
                <a href='{$link}' title='{$title}' style='display:block;'>
                  <img src='{$img_url}' alt='{$title}'
                       style='width:100%;aspect-ratio:1/1;object-fit:cover;
                              border-radius:6px;box-shadow:0 0 4px rgba(0,0,0,0.2);'>
                </a>
                <p style='margin:0.3em 0 0;font-size:0.75em;color:#555;
                          line-height:1.2;'>"
              . esc_html(wp_trim_words($caption ?: $title, 6)) .
             "</p>
              </div>";
    }

    echo '</div></div>';
    return ob_get_clean();
}
