<?php

// Ensure meta is defined (parent provides empty default but we'll be safe)
$meta = '';

$artist = get_field('song_artist');
if ($artist) {
    if (is_array($artist)) {
        $artist = reset($artist);
    }
    $meta = get_the_title($artist->ID);
}

$cover = get_field('cover_image');
$image = $cover
    ? $cover['sizes']['medium']
    : get_the_post_thumbnail_url($post_id, 'medium');

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);