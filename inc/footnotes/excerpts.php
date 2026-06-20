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

        $thumb = '';
        $source_text = '';

        // --------------------------------------------------
        // ORIGINAL SOURCE CPT LOGIC
        // --------------------------------------------------

        $source = get_field('excerpt_source', $item->ID);

        if ($source) {

            $img = get_field('cover_image', $source->ID);

            if ($img) {
                $src = $img['sizes']['thumbnail'];
            } elseif (has_post_thumbnail($source->ID)) {
                $src = get_the_post_thumbnail_url($source->ID, 'thumbnail');
            }

            if (!empty($src)) {
                $thumb = "<a href=\"{$link}\">
                            <img src=\"{$src}\"
                                 style=\"width:48px;height:48px;
                                        object-fit:cover;
                                        border-radius:50%;
                                        margin-right:10px;\">
                          </a>";
            }

            $src_title = esc_html(get_the_title($source));

            $author = get_field('author_profile', $source->ID);

            if (is_array($author)) {
                $author = reset($author);
            }

            $author_name = $author
                ? esc_html(get_the_title($author))
                : '';

$src_link = get_permalink($source);

$source_text = 'Source: <a href="' . esc_url($src_link) . '">' . $src_title . '</a>';

if ($author_name) {

    $author_link = get_permalink($author);

    $source_text .= ' by <a href="' . esc_url($author_link) . '">' . $author_name . '</a>';
}
        }

        // --------------------------------------------------
        // FALLBACK FOR MIGRATED REFERENCES
        // --------------------------------------------------

        else {

            if (have_rows('references', $item->ID)) {

                the_row();

                $label = get_sub_field('reference_label');

                if ($label) {
                    $source_text = 'Source: ' . esc_html($label);
                }

                if ($default_thumb = wp_get_attachment_image_url(20123, 'thumbnail')) {

                    $thumb = "<a href=\"{$link}\">
                                <img src=\"{$default_thumb}\"
                                     style=\"width:48px;height:48px;
                                            object-fit:cover;
                                            border-radius:50%;
                                            margin-right:10px;\">
                              </a>";
                }

                reset_rows();
            }
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

$references = get_field('references', $item->ID);

if (!empty($references)) {

    echo '<div style="
        margin-top:0.6rem;
        margin-left:1rem;
        padding-left:1rem;
        border-left:2px solid #ddd;
        font-size:0.9rem;
    ">';

    echo '<strong>';

    echo count($references) > 1
        ? 'Sources:'
        : 'Source:';

    echo '</strong>';

    foreach ($references as $ref) {

        $label = $ref['reference_label'] ?? '';
        $title = $ref['reference_title'] ?? '';
        $type  = $ref['reference_type'] ?? '';
        $url   = $ref['reference_url'] ?? '';

        echo '<div style="margin-top:0.5rem;">';

        if ($label) {
            echo '<div><strong>' . esc_html($label) . '</strong></div>';
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

        echo '</div>';
    }

    echo '</div>';
}
        }

        if (!empty($source_text)) {

            echo "<p style=\"margin-top:0.4rem;
                           font-size:0.9rem;
                           color:#666;\">{$source_text}</p>";
        }

        echo "</div></li>";
    }

    echo '</ul></div>';

    return ob_get_clean();
}