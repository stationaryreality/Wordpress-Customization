<?php
// inc/footnotes/games.php
// ===============================
// Games Referenced
// ===============================

function fn_games($chapter_id, $group_titles) {
    ob_start();

    $games = get_field('games_referenced', $chapter_id) ?: [];
    if (!empty($games)) {
        uasort($games, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
        $meta = $group_titles['game'];
        echo '<div class="referenced-group" style="margin-top:2em;">';
        echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
        foreach ($games as $game) {
            $title = esc_html(get_the_title($game));
            $link  = get_permalink($game);
            $img   = get_field('cover_image', $game->ID);
            $thumb = $img ? "<a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;object-fit:cover;margin-right:8px;\"></a>" : '';
            echo "<li style=\"display:flex;align-items:center;gap:10px;margin-bottom:0.6em;\">{$thumb}<a href=\"{$link}\"><strong>{$title}</strong></a></li>";
        }
        echo '</ul></div>';
    }

    return ob_get_clean();
}