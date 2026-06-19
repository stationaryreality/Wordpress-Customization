<?php
// inc/footnotes/excerpts.php

function fn_excerpts($chapter_id, $group_titles) {

    $items = get_field('excerpts_referenced', $chapter_id) ?: [];
    if (empty($items)) return '';

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();

    $meta = $group_titles['excerpt'];

    echo '<div class="referenced-group" style="margin-top:2em;">';

    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
            <span style=\"font-size:1.1em;\">{$meta['emoji']}</span>
            <span style=\"text-decoration:underline;\">{$meta['title']}</span>
          </a></h4><ul>";

    foreach ($items as $item) {

        $title = esc_html(get_the_title($item));
        $link  = get_permalink($item);

        // Default thumbnail image
        $default_thumb = wp_get_attachment_image_url(20123, 'thumbnail');

        $thumb = '';

        if ($default_thumb) {
            $thumb = "<a href=\"{$link}\">
                        <img src=\"{$default_thumb}\"
                             style=\"width:48px;height:48px;
                                    object-fit:cover;
                                    border-radius:50%;
                                    margin-right:10px;\">
                      </a>";
        }

        // First source label from references repeater
        $source_label = '';

        if (have_rows('references', $item->ID)) {

            the_row();

            $source_label = get_sub_field('reference_label');

            // Reset repeater pointer just in case
            reset_rows();
        }

        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;
                          margin-bottom:0.6em;\">
                {$thumb}
                <div>";

        echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";

        $excerpt = get_field('excerpt_plain_text', $item->ID);

        if ($excerpt) {
            $excerpt = wp_trim_words($excerpt, 40, '...');
            echo "<div>{$excerpt}</div>";
        }

        if (!empty($source_label)) {

            echo "<p style=\"margin-top:0.4rem;
                           font-size:0.9rem;
                           color:#666;\">
                    Source: " . esc_html($source_label) . "
                  </p>";
        }

        echo "</div></li>";
    }

    echo '</ul></div>';

    return ob_get_clean();
}