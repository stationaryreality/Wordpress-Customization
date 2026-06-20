<?php
// inc/footnotes/images.php

function fn_images($chapter_id, $group_titles) {
    $items = get_field('images_linked', $chapter_id) ?: [];
    if (empty($items)) return '';

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();

    // --- Header ---
    $meta = $group_titles['image'];
    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
            <span style=\"font-size:1.1em;\">{$meta['emoji']}</span> 
            <span style=\"text-decoration:underline;\">{$meta['title']}</span>
          </a></h4>";

    // --- Grid container (centered) ---
    echo '<div class="mini-image-grid" style="
        display:grid;
        grid-template-columns:repeat(auto-fill, minmax(90px, 1fr));
        gap:10px;
        margin:0.8em auto 0;          /* ← center the block */
        max-width:800px;              /* ← optional, adjust as needed */
        justify-content:center;
    ">';

    // We'll collect images that have references here
    $images_with_sources = [];

    // --- Loop through images ---
    foreach ($items as $img_post) {
        $title = esc_html(get_the_title($img_post));
        $link  = get_permalink($img_post);
        $image = get_field('image_file', $img_post->ID);
        $img_url = $image ? $image['sizes']['medium'] : get_the_post_thumbnail_url($img_post->ID, 'medium');
        $caption = get_field('image_caption', $img_post->ID);

        if (!$img_url) continue;

        // Output grid item
        echo "<div class='mini-image-item' style='text-align:center;'>
                <a href='{$link}' title='{$title}' style='display:block;'>
                  <img src='{$img_url}' alt='{$title}'
                       style='width:100%;aspect-ratio:1/1;object-fit:cover;
                              border-radius:6px;box-shadow:0 0 4px rgba(0,0,0,0.2);'>
                </a>
                <p style='margin:0.3em 0 0;font-size:0.75em;color:#555;line-height:1.2;'>"
              . esc_html(wp_trim_words($caption ?: $title, 6)) .
              "</p>
              </div>";

        // --- Check for references and store if any ---
        if (have_rows('references', $img_post->ID)) {
            $images_with_sources[] = $img_post;   // store the post object
        }
    }

    // --- Close grid ---
    echo '</div>';

    // --- Sources section (rendered after the grid) ---
    if (!empty($images_with_sources)) {
        echo '<div style="margin:1.5rem auto 0; max-width:800px; padding-left:1rem; border-left:2px solid #ddd;">';
        echo '<strong>' . (count($images_with_sources) > 1 ? 'Sources:' : 'Source:') . '</strong>';

        foreach ($images_with_sources as $img_post) {
            echo '<div style="margin-top:1rem;">';
            // Image title as a link
            echo '<div><strong><a href="' . esc_url(get_permalink($img_post)) . '">'
                . esc_html(get_the_title($img_post)) . '</a></strong></div>';

            // Use the universal references renderer
            echo kp_render_references($img_post->ID);

            echo '</div>';
        }

        echo '</div>';
    }

    echo '</div>'; // end referenced-group
    return ob_get_clean();
}