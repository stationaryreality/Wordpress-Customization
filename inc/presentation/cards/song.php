<?php
$post_id = $post_id ?? get_the_ID();
$title   = get_the_title($post_id);
$url     = get_permalink($post_id);
$excerpt = get_the_excerpt($post_id);

$artist = get_field('song_artist', $post_id);
$meta   = '';
if ($artist) {
    if (is_array($artist)) { $artist = reset($artist); }
    $meta = get_the_title($artist->ID);
}

$cover = get_field('cover_image', $post_id);
$image = '';
if ($cover) {
    $image = $cover['sizes']['medium'] ?? $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
} elseif (has_post_thumbnail($post_id)) {
    $image = get_the_post_thumbnail_url($post_id, 'medium');
}

return compact('title', 'url', 'icon', 'excerpt', 'image', 'meta', 'type');