<?php
// inc/footnotes/excerpts.php

function fn_excerpts($chapter_id, $group_titles) {
    $items = get_field('excerpts_referenced', $chapter_id) ?: [];
    if (empty($items)) return '';

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
        if ($source) {
            $img = get_field('cover_image', $source->ID);
            if ($img) {
                $src = $img['sizes']['thumbnail'];
                $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" 
                          style=\"width:48px;height:48px;object-fit:cover;
                          border-radius:50%;margin-right:10px;\"></a>";
            } elseif (has_post_thumbnail($source->ID)) {
                // fallback to featured image of the source CPT
                $src = get_the_post_thumbnail_url($source->ID, 'thumbnail');
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

        if ($source) {
            $src_title = esc_html(get_the_title($source));
            $src_link  = get_permalink($source);
            echo "<p style=\"margin-top:0.4rem;font-size:0.9rem;color:#666;\">
                    Source: <a href=\"{$src_link}\">{$src_title}</a>
                  </p>";
        }

        echo "</div></li>";
    }

    echo '</ul></div>';
    return ob_get_clean();
}
