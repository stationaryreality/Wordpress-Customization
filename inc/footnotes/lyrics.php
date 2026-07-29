<?php
// inc/footnotes/lyrics.php
// ===============================
// Lyrics Cited
// ===============================

function fn_lyrics($chapter_id, $group_titles) {
    $context = kp_build_reference_context($chapter_id);
    $items = $context['lyric'] ?? [];

    if (empty($items)) {
        return '';
    }

    uasort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));

    ob_start();
    $meta = $group_titles['lyric'];

    echo '<div class="cpt-lyric-footnote-group">';
    echo "<h4 class=\"cpt-lyric-footnote-title\">";
    echo "<a href=\"{$meta['link']}\">";
    echo "<span>{$meta['emoji']}</span> ";
    echo "<span>{$meta['title']}</span>";
    echo "</a>";
    echo "</h4>";
    echo '<ul class="cpt-lyric-footnote-list">';

    foreach ($items as $item) {
        $title = esc_html(get_the_title($item));
        $link  = get_permalink($item);
        $thumb = '';

        $song = get_field('song', $item->ID);
        if ($song) {
            $img = get_field('cover_image', $song->ID);
            if ($img) {
                $src = $img['sizes']['thumbnail'];
                $src_title = esc_html(get_the_title($song));
                $thumb = "<div class=\"cpt-lyric-footnote-thumb\"><a href=\"{$link}\"><img src=\"{$src}\" alt=\"{$src_title}\"></a></div>";
            }
        }

        echo '<li class="cpt-lyric-footnote-item">';
        echo $thumb;
        
        echo '<div class="cpt-lyric-footnote-details">';
        echo "<a href=\"{$link}\">{$title}</a>";

        $lyric = get_field('lyric_plain_text', $item->ID);
        if ($lyric) {
            echo "<div class=\"cpt-lyric-footnote-text\">{$lyric}</div>";
        }

        if ($song) {
            $src_title = esc_html(get_the_title($song));
            $src_link  = get_permalink($song);

            // Fetch artist
            $artist = get_field('song_artist', $song->ID);
            if (is_array($artist)) {
                $artist = reset($artist);
            }
            $artist_name = $artist ? esc_html(get_the_title($artist)) : '';
            $artist_link = $artist ? get_permalink($artist) : '';

            echo "<p class=\"cpt-lyric-footnote-source\">Source: <a href=\"{$src_link}\">{$src_title}</a>";
            if ($artist_name) {
                echo " by <a href=\"{$artist_link}\">{$artist_name}</a>";
            }
            echo "</p>";
        }

        echo '</div>'; // end cpt-lyric-footnote-details
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';
    
    return ob_get_clean();
}