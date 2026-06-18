<?php
// inc/footnotes/references.php

function fn_references($chapter_id, $group_titles) {
    $items = get_field('chapter_references', $chapter_id) ?: [];
    if (empty($items)) return '';

    usort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();
    $meta = $group_titles['reference'];

    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
            <span style=\"font-size:1.1em;\">{$meta['emoji']}</span> 
            <span style=\"text-decoration:underline;\">{$meta['title']}</span>
          </a></h4><ul>";

    foreach ($items as $ref) {
        $title = esc_html(get_the_title($ref));
        $url   = get_field('url', $ref->ID);
        $src   = get_field('source_name', $ref->ID);
        $link  = get_permalink($ref->ID);
        $img   = get_the_post_thumbnail_url($ref->ID, 'thumbnail');
        $thumb = $img ? "<a href=\"{$link}\" rel=\"noopener noreferrer\">
                    <img src=\"{$img}\" style=\"width:48px;height:48px;
                    border-radius:50%;margin-right:10px;\"></a>" : '';

        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;
                    margin-bottom:0.6em;\">{$thumb}<div>
                <div><a href=\"{$link}\" rel=\"noopener noreferrer\">
                    <strong>{$title}</strong></a></div>";
        if ($src) echo "<div><em>{$src}</em></div>";
        if ($url) echo "<div><a href=\"{$url}\" target=\"_blank\" 
                    rel=\"noopener noreferrer\">Link</a></div>";
        echo "</div></li>";
    }

    echo '</ul></div>';
    return ob_get_clean();
}
