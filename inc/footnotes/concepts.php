<?php
// inc/footnotes/concepts.php
// ===============================
// Concepts / Lexicon
// ===============================

function fn_concepts($chapter_id, $group_titles) {
    $items = get_field('concepts_referenced', $chapter_id) ?: [];
    if (empty($items)) return '';

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();
    $meta = $group_titles['concept'];

    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";

    foreach ($items as $item) {
        $title = esc_html(get_the_title($item));
        $link  = get_permalink($item);
        $thumb = '';

        // Concept thumbnail: use featured image if present
        if (has_post_thumbnail($item->ID)) {
            $src = get_the_post_thumbnail_url($item->ID, 'thumbnail');
            $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:10px;\"></a>";
        }

        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;\">{$thumb}<div><a href=\"{$link}\"><strong>{$title}</strong></a>";

        // Definition / extra content for concept
        $def = get_field('definition', $item->ID);
        if ($def) {
            echo "<div style=\"margin-top:0.25rem;\">{$def}</div>";
        }

        echo "</div></li>";
    }

    echo '</ul></div>';
    return ob_get_clean();
}
