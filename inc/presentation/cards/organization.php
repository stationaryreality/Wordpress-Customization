<?php
$post_id = $post_id ?? get_the_ID();
$title   = get_the_title($post_id);
$url     = get_permalink($post_id);
$type    = 'organization'; // Defined!

$bio     = get_field('org_bio', $post_id);
$excerpt = $bio ? wp_trim_words($bio, 20) : '';
$icon    = ''; // Defined! (Leave empty or add ACF fetch if you have an icon field)
$meta    = $excerpt; // Defined! (Matches what compact expects)

$image = '';

// BULLETPROOF IMAGE CHECK
$cover = get_field('cover_image', $post_id);
if ($cover) {
    if (is_array($cover)) {
        $image = $cover['sizes']['medium'] ?? $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
    } elseif (is_numeric($cover)) {
        $image = wp_get_attachment_image_url($cover, 'medium');
    } else {
        $image = $cover; // Assume it's already a clean URL string
    }
}

// Fallback to featured image
if (!$image && has_post_thumbnail($post_id)) {
    $image = get_the_post_thumbnail_url($post_id, 'medium');
}

return compact('title', 'url', 'icon', 'excerpt', 'image', 'meta', 'type');