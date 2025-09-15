<?php
// inc/footnotes/organizations.php

function fn_organizations($chapter_id, $group_titles) {
    $items = get_field('organizations_referenced', $chapter_id) ?: [];
    if (empty($items)) return '';

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();
    $meta = $group_titles['organizations'];

    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
            <span style=\"font-size:1.1em;\">{$meta['emoji']}</span> 
            <span style=\"text-decoration:underline;\">{$meta['title']}</span>
          </a></h4><ul>";

    foreach ($items as $org) {
        $title = esc_html(get_the_title($org));
        $link  = get_permalink($org);
        $cover = get_field('cover_image', $org->ID);
        $img   = $cover ? "<a href=\"{$link}\"><img src=\"{$cover['url']}\" 
                    alt=\"{$title}\" style=\"width:60px;height:60px;
                    object-fit:cover;margin-right:10px;\"></a>" : '';

        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;
                    margin-bottom:0.6em;\">{$img}
              <div><a href=\"{$link}\"><strong>{$title}</strong></a></div></li>";
    }

    echo '</ul></div>';
    return ob_get_clean();
}
