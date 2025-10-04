<?php
// inc/footnotes/lyrics.php

function fn_lyrics($chapter_id, $group_titles) {
    $items = get_field('lyrics_referenced', $chapter_id) ?: [];
    if (empty($items)) return '';

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();
    $meta = $group_titles['lyric'];

    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
            <span style=\"font-size:1.1em;\">{$meta['emoji']}</span> 
            <span style=\"text-decoration:underline;\">{$meta['title']}</span>
          </a></h4><ul>";

    foreach ($items as $item) {
        $title = esc_html(get_the_title($item));
        $link  = get_permalink($item);
        $thumb = '';

        $song = get_field('song', $item->ID);
        if ($song) {
            $img = get_field('cover_image', $song->ID);
            if ($img) {
                $src = $img['sizes']['thumbnail'];
                $thumb = "<a href=\"{$link}\"><img src=\"{$src}\" 
                          style=\"width:48px;height:48px;object-fit:cover;
                          border-radius:50%;margin-right:10px;\"></a>";
            }
        }

        echo "<li style=\"display:flex;align-items:flex-start;gap:10px;
                    margin-bottom:0.6em;\">{$thumb}<div>
              <a href=\"{$link}\"><strong>{$title}</strong></a>";

        $lyric = get_field('lyric_plain_text', $item->ID);
        if ($lyric) echo "<div>{$lyric}</div>";

        if ($song) {
            $src_title = esc_html(get_the_title($song));
            $src_link  = get_permalink($song);
            echo "<p style=\"margin-top:0.4rem;font-size:0.9rem;color:#666;\">
                    Source: <a href=\"{$src_link}\">{$src_title}</a>
                  </p>";
        }

        echo "</div></li>";
    }

    echo '</ul></div>';
    return ob_get_clean();
}
