<?php
$post_id = $post_id ?? get_the_ID();
$title   = get_the_title($post_id);
$url     = get_permalink($post_id);

$bio     = get_field('bio', $post_id) ?: get_the_excerpt($post_id);
$excerpt = wp_trim_words($bio, 20);

$portrait = get_field('portrait_image', $post_id);
$image    = '';

if (is_array($portrait)) {
    $image = $portrait['sizes']['thumbnail'] ?? $portrait['url'] ?? '';
} elseif (is_numeric($portrait)) {
    $image = wp_get_attachment_image_url($portrait, 'thumbnail');
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