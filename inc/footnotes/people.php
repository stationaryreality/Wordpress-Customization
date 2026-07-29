<?php
// inc/footnotes/people.php
// ===============================
// People Referenced
// ===============================

function fn_people($chapter_id, $group_titles) {
    ob_start();

    $context = kp_build_reference_context($chapter_id);
    $people = $context['profile'] ?? [];

    if (!empty($people)) {
        $meta = $group_titles['profile'];
        
        echo '<div class="cpt-people-footnote-group">';
        echo "<h4 class=\"cpt-people-footnote-title\">";
        echo "<a href=\"{$meta['link']}\">";
        echo "<span>{$meta['emoji']}</span> ";
        echo "<span>{$meta['title']}</span>";
        echo "</a>";
        echo "</h4>";
        echo '<ul class="cpt-people-footnote-list">';
        
        foreach ($people as $person) {
            $title = esc_html(get_the_title($person));
            $link  = get_permalink($person);
            $img   = get_field('portrait_image', $person->ID);
            
            $thumb = $img 
                ? "<div class=\"cpt-people-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" alt=\"{$title}\"></a></div>" 
                : '';
                
            echo "<li class=\"cpt-people-footnote-item\">";
            echo $thumb;
            echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";
            echo "</li>";
        }
        
        echo '</ul>';
        echo '</div>';
    }

    return ob_get_clean();
}