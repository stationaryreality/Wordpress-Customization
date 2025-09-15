<?php
// inc/footnotes/movies.php
// ===============================
// Movies Referenced
// ===============================

function fn_movies($chapter_id, $group_titles) {
    ob_start();

    $movies = get_field('movies_referenced', $chapter_id) ?: [];
    if (!empty($movies)) {
        uasort($movies, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
        $meta = $group_titles['movie'];
        echo '<div class="referenced-group" style="margin-top:2em;">';
        echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
        foreach ($movies as $movie) {
            $title = esc_html(get_the_title($movie));
            $link  = get_permalink($movie);
            $img   = get_field('cover_image', $movie->ID);
            $thumb = $img ? "<a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:48px;object-fit:cover;margin-right:8px;\"></a>" : '';
            echo "<li style=\"display:flex;align-items:center;gap:10px;margin-bottom:0.6em;\">{$thumb}<a href=\"{$link}\"><strong>{$title}</strong></a></li>";
        }
        echo '</ul></div>';
    }

    return ob_get_clean();
}
