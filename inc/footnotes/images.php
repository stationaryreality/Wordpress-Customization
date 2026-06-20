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

    // --- Grid container ---
    echo '<div class="mini-image-grid" style="
        display:grid;
        grid-template-columns:repeat(auto-fill, minmax(90px, 1fr));
        gap:10px;
        margin:0.8em 0 0 1.5em;
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
        $references = get_field('references', $img_post->ID);
        if (!empty($references)) {
            $images_with_sources[] = [
                'title'      => get_the_title($img_post),
                'link'       => get_permalink($img_post),
                'references' => $references,
            ];
        }
    }

    // --- Close grid ---
    echo '</div>';

    // --- Sources section (rendered after the grid) ---
    if (!empty($images_with_sources)) {
        echo '<div style="
            margin:1.5rem 0 0 1.5rem;
            padding-left:1rem;
            border-left:2px solid #ddd;
        ">';

        echo '<strong>';
        echo count($images_with_sources) > 1 ? 'Sources:' : 'Source:';
        echo '</strong>';

        foreach ($images_with_sources as $image_source) {
            echo '<div style="margin-top:1rem;">';

            // Image title as a link
            echo '<div><strong>
                    <a href="' . esc_url($image_source['link']) . '">
                        ' . esc_html($image_source['title']) . '
                    </a>
                  </strong></div>';

            // References for this image
            foreach ($image_source['references'] as $ref) {
                $label = $ref['reference_label'] ?? '';
                $title = $ref['reference_title'] ?? '';
                $type  = $ref['reference_type'] ?? '';
                $url   = $ref['reference_url'] ?? '';

                if ($label) {
                    echo '<div>' . esc_html($label) . '</div>';
                }
                if ($title) {
                    echo '<div>' . esc_html($title) . '</div>';
                }
                if ($type) {
                    echo '<div><em>' . esc_html($type) . '</em></div>';
                }
                if ($url) {
                    echo '<div>
                        <a href="' . esc_url($url) . '"
                           target="_blank"
                           rel="noopener noreferrer">
                           View Source
                        </a>
                    </div>';
                }
            }

            echo '</div>'; // end each image source block
        }

        echo '</div>'; // end sources wrapper
    }

    echo '</div>'; // end referenced-group
    return ob_get_clean();
}