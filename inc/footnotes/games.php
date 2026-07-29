<?php
// inc/footnotes/games.php
// ===============================
// Games Referenced
// ===============================

function fn_games($chapter_id, $group_titles) {
    ob_start();

    $context = kp_build_reference_context($chapter_id);
    $games = $context['game'] ?? [];

    if (!empty($games)) {
        uasort($games, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
        $meta = $group_titles['game'];
        
        echo '<div class="cpt-game-footnote-group">';
        echo "<h4 class=\"cpt-game-footnote-title\">";
        echo "<a href=\"{$meta['link']}\">";
        echo "<span>{$meta['emoji']}</span> ";
        echo "<span>{$meta['title']}</span>";
        echo "</a>";
        echo "</h4>";
        echo '<ul class="cpt-game-footnote-list">';
        
        foreach ($games as $game) {
            $title = esc_html(get_the_title($game));
            $link  = get_permalink($game);
            $img   = get_field('cover_image', $game->ID);
            
            $thumb = $img 
                ? "<div class=\"cpt-game-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" alt=\"{$title}\"></a></div>" 
                : '';
                
            echo "<li class=\"cpt-game-footnote-item\">";
            echo $thumb;
            echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";
            echo "</li>";
        }
        
        echo '</ul>';
        echo '</div>';
    }

    return ob_get_clean();
}