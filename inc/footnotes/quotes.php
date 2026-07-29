<?php
// inc/footnotes/quotes.php
// ===============================
// Quotes Referenced
// ===============================

function fn_quotes($chapter_id, $group_titles) {

    $quotes = get_field('quotes_referenced', $chapter_id) ?: [];
    if (empty($quotes)) {
        return '';
    }

    ob_start();

    $meta = $group_titles['quote'];

    echo '<div class="cpt-quote-footnote-group">';
    echo "<h4 class=\"cpt-quote-footnote-title\">";
    echo "<a href=\"{$meta['link']}\">";
    echo "<span>{$meta['emoji']}</span> ";
    echo "<span>{$meta['title']}</span>";
    echo "</a>";
    echo "</h4>";
    echo '<ul class="cpt-quote-footnote-list">';

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
                $src_title = esc_html(get_the_title($source));
                $thumb = "<div class=\"cpt-quote-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$src}\" alt=\"{$src_title}\"></a></div>";
            }

            $author = get_field('author_profile', $source->ID);

            if (is_array($author)) {
                $author = reset($author);
            }

            $author_name = $author
                ? esc_html(get_the_title($author))
                : '';

            $src_link = get_permalink($source);

            $source_text = 'Source: <a href="' . esc_url($src_link) . '">' . esc_html(get_the_title($source)) . '</a>';

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

                // ---- Get the first reference ----
                $refs = get_field('references', $quote->ID);
                $first_ref = $refs[0] ?? null;

                $thumb_src = '';

                // 1. Try custom image from first reference
                if ($first_ref && !empty($first_ref['reference_thumbnail'])) {
                    $img = $first_ref['reference_thumbnail'];
                    // Use full URL – no thumbnail sizes
                    if (is_array($img)) {
                        $thumb_src = $img['url']; // full image
                    } elseif (is_numeric($img)) {
                        $thumb_src = wp_get_attachment_image_url($img, 'full');
                    }
                }

                // 2. If no custom image, use your fallback (full size)
                if (empty($thumb_src)) {
                    // Use the default ID but with 'full' size
                    $thumb_src = wp_get_attachment_image_url(19766, 'full');
                    // If that still fails, hardcode a URL (optional, change if needed)
                    if (empty($thumb_src)) {
                        $thumb_src = 'https://yourdomain.com/wp-content/uploads/default-quote-image.jpg';
                    }
                }

                // 3. Build the thumbnail HTML
                if ($thumb_src) {
                    $thumb = "<div class=\"cpt-quote-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$thumb_src}\" alt=\"Reference\"></a></div>";
                }

                // Do NOT call the_row() or reset_rows() here
            }
        }

        // --- Output list item ---
        echo '<li class="cpt-quote-footnote-item">';
        echo $thumb;
        
        echo '<div class="cpt-quote-footnote-details">';
        echo "<a href=\"{$link}\">{$title}</a>";

        // --- Quote content ---
        if ($content) {
            echo "<div class=\"cpt-quote-footnote-text\">" . esc_html($content) . "</div>";
        }

        // --- For migrated references, output the universal references block ---
        if ($has_migrated_refs) {
            echo '<div class="cpt-quote-footnote-references">';
            // Use the universal renderer – it handles the details toggle and all fields
            echo kp_render_references($quote->ID);
            echo '</div>';
        }

        // --- CPT source line (only when $source exists) ---
        if (!empty($source_text)) {
            echo "<p class=\"cpt-quote-footnote-source\">{$source_text}</p>";
        }

        echo '</div>'; // end cpt-quote-footnote-details
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';

    return ob_get_clean();
}