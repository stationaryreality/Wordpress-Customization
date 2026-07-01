<?php
// inc/footnotes/elements.php

function fn_elements($chapter_id, $group_titles) {

    $items = get_field('attached_elements', $chapter_id) ?: [];
    if (empty($items)) return '';

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();

    // --- Header ---
    $meta = $group_titles['element'];

    echo '<div class="referenced-group" style="margin-top:2em;">';

    echo "<h4>
            <a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
                <span style=\"font-size:1.1em;\">{$meta['emoji']}</span>
                <span style=\"text-decoration:underline;\">{$meta['title']}</span>
            </a>
          </h4>";

    // --- Grid ---
    echo '<div class="mini-image-grid" style="
        display:grid;
        grid-template-columns:repeat(auto-fill, minmax(120px, 1fr));
        gap:12px;
        margin:0.8em auto 0;
        max-width:900px;
        justify-content:center;
    ">';

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

        echo "
        <div class='mini-image-item' style='text-align:center;'>

            <a href='{$link}'
               title='{$title}'
               style='display:block;'>

                <img src='{$img_url}'
                     alt='{$title}'
                     style='width:100%;
                            aspect-ratio:1/1;
                            object-fit:cover;
                            border-radius:6px;
                            box-shadow:0 0 4px rgba(0,0,0,0.2);'>

            </a>

            <p style='
                margin:0.3em 0 0;
                font-size:0.75em;
                color:#555;
                line-height:1.2;'>

                {$title}

            </p>

        </div>";
        
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