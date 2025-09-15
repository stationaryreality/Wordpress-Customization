<?php
// inc/footnotes/quotes.php
// ===============================
// Quotes
// ===============================

function fn_quotes($chapter_id, $group_titles) {
    ob_start();

    $quotes = get_field('quotes_referenced', $chapter_id) ?: [];
    if (!empty($quotes)) {
        $meta = $group_titles['quote'];
        echo '<div class="referenced-group" style="margin-top:2em;">';
        echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\"><span style=\"font-size:1.1em;\">{$meta['emoji']}</span> <span style=\"text-decoration:underline;\">{$meta['title']}</span></a></h4><ul>";

        foreach ($quotes as $quote) {
            $title   = esc_html(get_the_title($quote));
            $link    = get_permalink($quote);
            $content = get_field('quote_plain_text', $quote->ID) ?: '';

            // ✅ Get thumbnail from source CPT
            $source = get_field('quote_source', $quote->ID);
            $thumb = '';
            if ($source) {
                $img = get_field('cover_image', $source->ID);
                if ($img) {
                    $src = $img['sizes']['thumbnail'];
                    $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:10px;\"></a>";
                } elseif (has_post_thumbnail($source->ID)) {
                    $src = get_the_post_thumbnail_url($source->ID, 'thumbnail');
                    $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" style=\"width:48px;height:48px;object-fit:cover;border-radius:50%;margin-right:10px;\"></a>";
                }
            }

            echo "<li style=\"display:flex;align-items:flex-start;gap:10px;margin-bottom:0.6em;\">{$thumb}<div>";
            echo "<a href=\"{$link}\"><strong>{$title}</strong></a>";
            if ($content) {
                echo "<div style=\"font-size:0.9em;color:#444;margin-top:2px;\">".esc_html($content)."</div>";
            }
            echo "</div></li>";
        }

        echo '</ul></div>';
    }

    return ob_get_clean();
}
