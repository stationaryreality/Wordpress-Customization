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

        $source = get_field('excerpt_source', $item->ID);

        // Build thumbnail (from source cover_image or featured image)
        if ($source) {
            $img = get_field('cover_image', $source->ID);
            if ($img) {
                $src = $img['sizes']['thumbnail'];
            } elseif (has_post_thumbnail($source->ID)) {
                $src = get_the_post_thumbnail_url($source->ID, 'thumbnail');
            }
            if (!empty($src)) {
                $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" 
                          style=\"width:48px;height:48px;object-fit:cover;
                          border-radius:50%;margin-right:10px;\"></a>";
            }
        }

        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;
                    margin-bottom:0.6em;\">{$thumb}<div>
              <a href=\"{$link}\"><strong>{$title}</strong></a>";

        $excerpt = get_field('excerpt_plain_text', $item->ID);
        if ($excerpt) {
            $excerpt = wp_trim_words($excerpt, 40, '...');
            echo "<div>{$excerpt}</div>";
        }

        // Add “Source: [Book Title] by [Author]” (optional author)
        if ($source) {
            $src_title = esc_html(get_the_title($source));
            $src_link  = get_permalink($source);

            // Optional author relationship field (array or single)
            $author = get_field('author_profile', $source->ID);
            if (is_array($author)) $author = reset($author);

            $author_name = $author ? esc_html(get_the_title($author)) : '';
            $author_link = $author ? get_permalink($author) : '';

            echo "<p style=\"margin-top:0.4rem;font-size:0.9rem;color:#666;\">Source: 
                    <a href=\"{$src_link}\">{$src_title}</a>";
            if ($author_name) {
                echo " by <a href=\"{$author_link}\">{$author_name}</a>";
            }
            echo "</p>";
        }

        echo "</div></li>";
    }

    echo '</ul></div>';
    return ob_get_clean();
}
