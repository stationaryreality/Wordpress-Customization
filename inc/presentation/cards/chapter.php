<?php

$image = has_post_thumbnail($post_id)
    ? get_the_post_thumbnail_url($post_id, 'medium')
    : '';

$excerpt = has_excerpt($post_id)
    ? get_the_excerpt($post_id)
    : wp_trim_words(get_the_content($post_id), 20);

$meta = ''; // optionally add a chapter number or source

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);