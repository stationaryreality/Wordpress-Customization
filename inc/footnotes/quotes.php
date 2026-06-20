<?php
// inc/footnotes/quotes.php
// ===============================
// Quotes
// ===============================

function fn_quotes($chapter_id, $group_titles) {

    $quotes = get_field('quotes_referenced', $chapter_id) ?: [];
    if (empty($quotes)) return '';

    ob_start();

    $meta = $group_titles['quote'];

    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
            <span style=\"font-size:1.1em;\">{$meta['emoji']}</span>
            <span style=\"text-decoration:underline;\">{$meta['title']}</span>
          </a></h4><ul>";

    foreach ($quotes as $quote) {

        $title   = esc_html(get_the_title($quote));
        $link    = get_permalink($quote);
        $content = get_field('quote_plain_text', $quote->ID) ?: '';

        $thumb = '';
        $source_text = '';

        // --------------------------------------------------
        // ORIGINAL SOURCE CPT LOGIC
        // --------------------------------------------------

        $source = get_field('quote_source', $quote->ID);

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

            $source_text = "Source: {$src_title}";

            if ($author_name) {
                $source_text .= " by {$author_name}";
            }
        }

        // --------------------------------------------------
        // FALLBACK FOR MIGRATED REFERENCES
        // --------------------------------------------------

        else {

            if (have_rows('references', $quote->ID)) {

                the_row();

                $label = get_sub_field('reference_label');

                if ($label) {
                    $source_text = 'Source: ' . esc_html($label);
                }

                $default_thumb = wp_get_attachment_image_url(19766, 'thumbnail');

                if ($default_thumb) {
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

        if ($content) {
            echo "<div style=\"font-size:0.9em;color:#444;margin-top:2px;\">"
                 . esc_html($content) .
                 "</div>";
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