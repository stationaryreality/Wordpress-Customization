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
    echo "<div class=\"cpt-lyric-footnote-text\">" . esc_html($lyric) . "</div>";
}

        if ($song) {
            echo '<p class="cpt-lyric-footnote-source">' . kp_lyric_attribution($item->ID) . '</p>';
        }

        echo '</div>'; // end cpt-lyric-footnote-details
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';
    
    return ob_get_clean();
}