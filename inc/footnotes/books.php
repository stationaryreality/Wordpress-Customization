<?php
// inc/footnotes/books.php
// ===============================
// Books Cited
// ===============================

function fn_books($chapter_id, $group_titles) {
    ob_start();

    $books = get_field('books_cited', $chapter_id) ?: [];
    if (!empty($books)) {
        uasort($books, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
        $meta = $group_titles['book'];
        echo '<div class="referenced-group" style="margin-top:2em;">';
        echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";
        foreach ($books as $book) {
            $title = esc_html(get_the_title($book));
            $link  = get_permalink($book);
            $img   = get_field('cover_image', $book->ID);
            $thumb = $img ? "<a href=\"{$link}\"><img src=\"{$img['sizes']['thumbnail']}\" style=\"width:48px;height:64px;object-fit:cover;margin-right:8px;\"></a>" : '';
            echo "<li style=\"display:flex;align-items:center;gap:10px;margin-bottom:0.6em;\">{$thumb}<a href=\"{$link}\"><strong>{$title}</strong></a></li>";
        }
        echo '</ul></div>';
    }

    return ob_get_clean();
}
