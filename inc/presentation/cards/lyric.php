<?php

$excerpt = get_field('lyric_plain_text');
$song = get_field('song');

if ($song) {
    $song_title = get_the_title($song->ID);
    $artist = get_field('song_artist', $song->ID);
    if ($artist) {
        if (is_array($artist)) {
            $artist = reset($artist);
        }
        $meta = get_the_title($artist->ID);
    }
    $cover = get_field('cover_image', $song->ID);
    if ($cover && is_array($cover)) {
        $image = $cover['sizes']['medium'] ?? $cover['sizes']['thumbnail'] ?? $cover['url'];
    } elseif (has_post_thumbnail($song->ID)) {
        $image = get_the_post_thumbnail_url($song->ID, 'medium');
    }
}

if (!$image && has_post_thumbnail($post_id)) {
    $image = get_the_post_thumbnail_url($post_id, 'medium');
}

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);