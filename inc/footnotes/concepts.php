<?php
// inc/footnotes/concepts.php
// ===============================
// Concepts / Lexicon
// ===============================

function fn_concepts($chapter_id, $group_titles) {
    $context = kp_build_reference_context($chapter_id);
    $items = $context['concept'] ?? [];

    if (empty($items)) {
        return '';
    }

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();
    $meta = $group_titles['concept'];

    echo '<div class="cpt-concept-footnote-group">';
    echo "<h4 class=\"cpt-concept-footnote-title\">";
    echo "<a href=\"{$meta['link']}\">";
    echo "<span>{$meta['emoji']}</span> ";
    echo "<span>{$meta['title']}</span>";
    echo "</a>";
    echo "</h4>";
    echo '<ul class="cpt-concept-footnote-list">';

    foreach ($items as $item) {
        $title = esc_html(get_the_title($item));
        $link  = get_permalink($item);
        $thumb = '';

        // Concept thumbnail: use featured image if present
        if (has_post_thumbnail($item->ID)) {
            $src = get_the_post_thumbnail_url($item->ID, 'thumbnail');
            $thumb = "<div class=\"cpt-concept-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$src}\" alt=\"{$title}\"></a></div>";
        }

        echo '<li class="cpt-concept-footnote-item">';
        echo $thumb;
        
        echo '<div class="cpt-concept-footnote-details">';
        echo "<a href=\"{$link}\" class=\"cpt-concept-footnote-name\"><strong>{$title}</strong></a>";

        // Definition / extra content for concept
        $def = get_field('definition', $item->ID);
        if ($def) {
            echo "<div class=\"cpt-concept-footnote-definition\">{$def}</div>";
        }

        echo '</div>';
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';

    return ob_get_clean();
}