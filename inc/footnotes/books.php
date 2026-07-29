<?php
// inc/footnotes/books.php
// ===============================
// Books Cited
// ===============================

function fn_books($chapter_id, $group_titles) {
    ob_start();

    $context = kp_build_reference_context($chapter_id);
    $books = $context['book'] ?? [];

    if (!empty($books)) {
        uasort($books, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
        $meta = $group_titles['book'];
        
        echo '<div class="cpt-book-footnote-group">';
        echo "<h4 class=\"cpt-book-footnote-title\">";
        echo "<a href=\"{$meta['link']}\">";
        echo "<span>{$meta['emoji']}</span> ";
        echo "<span>{$meta['title']}</span>";
        echo "</a>";
        echo "</h4>";
        echo '<ul class="cpt-book-footnote-list">';
        
        foreach ($books as $book) {
            $title = esc_html(get_the_title($book));
            $link  = get_permalink($book);
            $img   = get_field('cover_image', $book->ID);
            
            $thumb = $img 
                ? "<div class=\"cpt-book-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" alt=\"{$title}\"></a></div>" 
                : '';
                
            echo "<li class=\"cpt-book-footnote-item\">";
            echo $thumb;
            echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";
            echo "</li>";
        }
        
        echo '</ul>';
        echo '</div>';
    }

    return ob_get_clean();
}