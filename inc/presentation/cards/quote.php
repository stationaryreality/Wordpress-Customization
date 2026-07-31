<?php
// These variables are inherited from kp_build_card() scope: 
// $title, $url, $icon, $post_id, $type, $excerpt, $image, $meta

$excerpt = get_field('quote_plain_text', $post_id);
$source  = get_field('quote_source', $post_id);

if ($source) {
    if (is_array($source)) {
        $source = reset($source);
    }
    
    // 1. Build the meta attribution string (Source + Author)
    $source_title = get_the_title($source->ID);
    $meta = 'from ' . esc_html($source_title);
    
    if (get_post_type($source->ID) === 'book') {
        $author = get_field('author_profile', $source->ID);
        if ($author) {
            if (is_array($author)) {
                $author = reset($author);
            }
            $meta .= ' by ' . esc_html(get_the_title($author->ID));
        }
    }

    // 2. Get the image
    $cover = get_field('cover_image', $source->ID);
    if ($cover && is_array($cover)) {
        $image = $cover['sizes']['medium'] ?? $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
    } elseif (has_post_thumbnail($source->ID)) {
        $image = get_the_post_thumbnail_url($source->ID, 'medium');
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