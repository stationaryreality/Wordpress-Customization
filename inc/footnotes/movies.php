<?php
// inc/footnotes/movies.php
// ===============================
// Movies Referenced
// ===============================

function fn_movies($chapter_id, $group_titles) {
    ob_start();

    $context = kp_build_reference_context($chapter_id);
    $movies = $context['movie'] ?? [];

    if (!empty($movies)) {
        uasort($movies, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
        $meta = $group_titles['movie'];
        
        echo '<div class="cpt-movie-footnote-group">';
        echo "<h4 class=\"cpt-movie-footnote-title\">";
        echo "<a href=\"{$meta['link']}\">";
        echo "<span>{$meta['emoji']}</span> ";
        echo "<span>{$meta['title']}</span>";
        echo "</a>";
        echo "</h4>";
        echo '<ul class="cpt-movie-footnote-list">';
        
        foreach ($movies as $movie) {
            $title = esc_html(get_the_title($movie));
            $link  = get_permalink($movie);
            $img   = get_field('cover_image', $movie->ID);
            
            $thumb = $img 
                ? "<div class=\"cpt-movie-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" alt=\"{$title}\"></a></div>" 
                : '';
                
            echo "<li class=\"cpt-movie-footnote-item\">";
            echo $thumb;
            echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";
            echo "</li>";
        }
        
        echo '</ul>';
        echo '</div>';
    }

    return ob_get_clean();
}