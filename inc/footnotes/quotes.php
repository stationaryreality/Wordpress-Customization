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
        $has_migrated_refs = false;   // flag for non‑CPT references

        // --------------------------------------------------
        // ORIGINAL SOURCE CPT LOGIC (unchanged)
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

            $src_link = get_permalink($source);

            $source_text = 'Source: <a href="' . esc_url($src_link) . '">' . $src_title . '</a>';

            if ($author_name) {
                $author_link = get_permalink($author);
                $source_text .= ' by <a href="' . esc_url($author_link) . '">' . $author_name . '</a>';
            }
        }

        // --------------------------------------------------
        // FALLBACK FOR MIGRATED REFERENCES (non‑CPT)
        // --------------------------------------------------

        else {

            // Check if there are any references (without advancing the row pointer)
            if (have_rows('references', $quote->ID)) {
                $has_migrated_refs = true;

                // Set default thumbnail if available
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
                // Do NOT call the_row() or reset_rows() here
            }
        }

        // --- Output list item ---
        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;
                          margin-bottom:0.6em;\">
                {$thumb}
                <div>";

        echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";

        // --- Quote content ---
        if ($content) {
            echo "<div style=\"font-size:0.9em;color:#444;margin-top:2px;\">"
                 . esc_html($content) .
                 "</div>";
        }

        // --- For migrated references, output the universal references block ---
        if ($has_migrated_refs) {
            echo '<div style="
                    margin-top:0.6rem;
                    margin-left:1rem;
                    padding-left:1rem;
                    border-left:2px solid #ddd;
                    font-size:0.9rem;
                ">';
            // Use the universal renderer – it handles the details toggle and all fields
            echo kp_render_references($quote->ID);
            echo '</div>';
        }

        // --- CPT source line (only when $source exists) ---
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