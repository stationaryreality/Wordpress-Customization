<?php
// inc/footnotes/excerpts.php
// ===============================
// Excerpts Cited
// ===============================

function fn_excerpts($chapter_id, $group_titles) {

    $context = kp_build_reference_context($chapter_id);
    $items = $context['excerpt'] ?? [];

    if (empty($items)) {
        return '';
    }

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();

    $meta = $group_titles['excerpt'];

    echo '<div class="cpt-excerpt-footnote-group">';

    echo "<h4 class=\"cpt-excerpt-footnote-title\">";
    echo "<a href=\"{$meta['link']}\">";
    echo "<span>{$meta['emoji']}</span> ";
    echo "<span>{$meta['title']}</span>";
    echo "</a>";
    echo "</h4>";
    
    echo '<ul class="cpt-excerpt-footnote-list">';

    foreach ($items as $item) {

        $title = esc_html(get_the_title($item));
        $link  = get_permalink($item);

        $thumb = '';
        $source_text = '';
        $has_migrated_refs = false;   // flag for non‑CPT references

        // --------------------------------------------------
        // ORIGINAL SOURCE CPT LOGIC (unchanged)
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
                $src_title = esc_html(get_the_title($source));
                $thumb = "<div class=\"cpt-excerpt-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$src}\" alt=\"{$src_title}\"></a></div>";
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

            if (have_rows('references', $item->ID)) {
                $has_migrated_refs = true;

                // Get first reference
                $refs = get_field('references', $item->ID);
                $first_ref = $refs[0] ?? null;

                $thumb_src = '';

                // 1. Try custom image
                if ($first_ref && !empty($first_ref['reference_thumbnail'])) {
                    $img = $first_ref['reference_thumbnail'];
                    if (is_array($img)) {
                        $thumb_src = $img['url'];
                    } elseif (is_numeric($img)) {
                        $thumb_src = wp_get_attachment_image_url($img, 'full');
                    }
                }

                // 2. If still empty, use your fallback image (full URL)
                if (empty($thumb_src)) {
                    $thumb_src = wp_get_attachment_image_url(22614, 'full');
                    if (empty($thumb_src)) {
                        $thumb_src = 'https://yourdomain.com/wp-content/uploads/your-default-image.jpg';
                    }
                }

                // 3. Build thumbnail HTML
                if ($thumb_src) {
                    $thumb = "<div class=\"cpt-excerpt-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$thumb_src}\" alt=\"Reference\"></a></div>";
                }
            }
        }
        
        // --- Output list item ---
        echo '<li class="cpt-excerpt-footnote-item">';
        echo $thumb;
        
        echo '<div class="cpt-excerpt-footnote-details">';
        echo "<a href=\"{$link}\">{$title}</a>";

        $excerpt = get_field('excerpt_plain_text', $item->ID);

        if ($excerpt) {
            $excerpt = wp_trim_words($excerpt, 40, '...');
            echo "<div class=\"cpt-excerpt-footnote-text\">{$excerpt}</div>";

            // --- For migrated references, output the universal references block ---
            if ($has_migrated_refs) {
                echo '<div class="cpt-excerpt-footnote-references">';
                echo kp_render_references($item->ID);
                echo '</div>';
            }
        }

        // --- CPT source line (only when $source exists) ---
        if (!empty($source_text)) {
            echo "<p class=\"cpt-excerpt-footnote-source\">{$source_text}</p>";
        }

        echo '</div>'; // end cpt-excerpt-footnote-details
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';

    return ob_get_clean();
}