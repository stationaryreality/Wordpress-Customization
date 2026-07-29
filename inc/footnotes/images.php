<?php
// inc/footnotes/images.php
// ===============================
// Images Cited
// ===============================

function fn_images($chapter_id, $group_titles) {
    
    $context = kp_build_reference_context($chapter_id);
    $items = $context['image'] ?? [];

    if (empty($items)) {
        return '';
    }

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();

    // --- Header ---
    $meta = $group_titles['image'];
    echo '<div class="cpt-image-footnote-group">';
    echo "<h4 class=\"cpt-image-footnote-title\">";
    echo "<a href=\"{$meta['link']}\">";
    echo "<span>{$meta['emoji']}</span> ";
    echo "<span>{$meta['title']}</span>";
    echo "</a>";
    echo "</h4>";

    // --- Grid container (centered) ---
    echo '<div class="cpt-image-footnote-grid">';

    // We'll collect images that have references here
    $images_with_sources = [];

    // --- Loop through images ---
    foreach ($items as $img_post) {
        $title = esc_html(get_the_title($img_post));
        $link  = get_permalink($img_post);
        $image = get_field('image_file', $img_post->ID);
        $img_url = $image ? $image['sizes']['medium'] : get_the_post_thumbnail_url($img_post->ID, 'medium');
        $caption = get_field('image_caption', $img_post->ID);

        if (!$img_url) {
            continue;
        }

        // Output grid item
        echo '<div class="cpt-image-footnote-item">';
        echo "<a href=\"{$link}\" title=\"{$title}\">";
        echo "<img src=\"{$img_url}\" alt=\"{$title}\" class=\"cpt-image-footnote-thumb\">";
        echo "</a>";
        
        $display_caption = esc_html(wp_trim_words($caption ?: $title, 6));
        echo "<p class=\"cpt-image-footnote-caption\">{$display_caption}</p>";
        echo '</div>';

        // --- Check for references and store if any ---
        if (have_rows('references', $img_post->ID)) {
            $images_with_sources[] = $img_post;   // store the post object
        }
    }

    // --- Close grid ---
    echo '</div>';

    echo kp_render_grouped_references($images_with_sources);

    echo '</div>'; // end cpt-image-footnote-group
    return ob_get_clean();
}