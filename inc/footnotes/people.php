<?php
// inc/footnotes/people.php
// ===============================
// People Referenced
// ===============================

function fn_people($chapter_id, $group_titles) {
    ob_start();

    $people = get_field('people_referenced', $chapter_id) ?: [];
    if (!empty($people)) {
        $meta = $group_titles['profile'];
        echo '<div class="referenced-group" style="margin-top:2em;">';
        echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
        foreach ($people as $person) {
            $title = esc_html(get_the_title($person));
            $link  = get_permalink($person);
            $img   = get_field('portrait_image', $person->ID);
            $thumb = $img ? "<a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:8px;\"></a>" : '';
            echo "<li style=\"display:flex;align-items:center;gap:10px;margin-bottom:0.6em;\">{$thumb}<a href=\"{$link}\"><strong>{$title}</strong></a></li>";
        }
        echo '</ul></div>';
    }

    return ob_get_clean();
}