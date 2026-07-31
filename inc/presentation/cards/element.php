<?php
// Inherited from kp_build_card(): $title, $url, $icon, $post_id, $type

// Ensure these are defined to prevent PHP warnings
$excerpt = '';
$meta    = '';

// Robust image fetching (prefers ACF image_file, falls back to featured image)
$image_field = get_field('image_file', $post_id) ?: get_post_thumbnail_id($post_id);
$image = '';

if (is_array($image_field)) {
    $image = $image_field['sizes']['medium'] ?? $image_field['url'] ?? '';
} elseif ($image_field) {
    $image = wp_get_attachment_image_url($image_field, 'medium');
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