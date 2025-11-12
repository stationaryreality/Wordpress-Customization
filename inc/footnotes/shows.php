<?php
// inc/footnotes/shows.php
// ===============================
// Shows Referenced
// ===============================

function fn_shows($chapter_id, $group_titles) {
    ob_start();

    $shows = get_field('shows_referenced', $chapter_id) ?: [];
    if (!empty($shows)) {
        uasort($shows, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
        $meta = $group_titles['show'];
        echo '<div class="referenced-group" style="margin-top:2em;">';
        echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
        foreach ($shows as $show) {
            $title = esc_html(get_the_title($show));
            $link  = get_permalink($show);
            $img   = get_field('cover_image', $show->ID);
            $thumb = $img ? "<a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;object-fit:cover;margin-right:8px;\"></a>" : '';
            echo "<li style=\"display:flex;align-items:center;gap:10px;margin-bottom:0.6em;\">{$thumb}<a href=\"{$link}\"><strong>{$title}</strong></a></li>";
        }
        echo '</ul></div>';
    }

    return ob_get_clean();
}