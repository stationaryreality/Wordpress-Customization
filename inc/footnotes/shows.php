<?php
// inc/footnotes/shows.php
// ===============================
// Shows Referenced
// ===============================

function fn_shows($chapter_id, $group_titles) {
    ob_start();

    $context = kp_build_reference_context($chapter_id);
    $shows = $context['show'] ?? [];

    if (!empty($shows)) {
        uasort($shows, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
        $meta = $group_titles['show'];
        
        echo '<div class="cpt-show-footnote-group">';
        echo "<h4 class=\"cpt-show-footnote-title\">";
        echo "<a href=\"{$meta['link']}\">";
        echo "<span>{$meta['emoji']}</span> ";
        echo "<span>{$meta['title']}</span>";
        echo "</a>";
        echo "</h4>";
        echo '<ul class="cpt-show-footnote-list">';
        
        foreach ($shows as $show) {
            $title = esc_html(get_the_title($show));
            $link  = get_permalink($show);
            $img   = get_field('cover_image', $show->ID);
            
            $thumb = $img 
                ? "<div class=\"cpt-show-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" alt=\"{$title}\"></a></div>" 
                : '';
                
            echo "<li class=\"cpt-show-footnote-item\">";
            echo $thumb;
            echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";
            echo "</li>";
        }
        
        echo '</ul>';
        echo '</div>';
    }

    return ob_get_clean();
}