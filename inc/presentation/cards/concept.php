<?php
$excerpt = get_field('definition', $post_id);
$image   = has_post_thumbnail($post_id) ? get_the_post_thumbnail_url($post_id, 'medium') : '';
$meta    = ''; // Explicitly define as empty string to prevent PHP warnings

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);