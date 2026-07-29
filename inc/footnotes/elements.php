<?php
// inc/footnotes/elements.php
// ===============================
// Elements Cited
// ===============================

function fn_elements($chapter_id, $group_titles) {

    $items = get_field('attached_elements', $chapter_id) ?: [];
    if (empty($items)) return '';

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();

    // --- Header ---
    $meta = $group_titles['element'];

    echo '<div class="cpt-element-footnote-group">';

    echo "<h4 class=\"cpt-element-footnote-title\">";
    echo "<a href=\"{$meta['link']}\">";
    echo "<span>{$meta['emoji']}</span> ";
    echo "<span>{$meta['title']}</span>";
    echo "</a>";
    echo "</h4>";

    // --- Grid ---
    echo '<div class="cpt-element-footnote-grid">';

    $elements_with_sources = [];

    foreach ($items as $element) {

        $title = esc_html(get_the_title($element));
        $link  = get_permalink($element);

        // Featured image first
        $img_url = get_the_post_thumbnail_url($element->ID, 'medium');

        // Future-proof:
        // if you later add element_image field,
        // just uncomment this block.
        /*
        $custom_image = get_field('element_image', $element->ID);
        if ($custom_image) {
            $img_url = $custom_image['sizes']['medium'];
        }
        */

        if (!$img_url) {
            continue;
        }

        echo '<div class="cpt-element-footnote-item">';
        echo "<a href=\"{$link}\" title=\"{$title}\">";
        echo "<img src=\"{$img_url}\" alt=\"{$title}\" class=\"cpt-element-footnote-thumb\">";
        echo "</a>";
        echo "<p class=\"cpt-element-footnote-caption\">{$title}</p>";
        echo '</div>';
        
        // Store Elements that contain Sources
        if (have_rows('references', $element->ID)) {
            $elements_with_sources[] = $element;
        }
    }

    echo '</div>';

    echo kp_render_grouped_references($elements_with_sources);

    echo '</div>';

    return ob_get_clean();
}