<?php
$post_id = $post_id ?? get_the_ID();
$title   = get_the_title($post_id);
$url     = get_permalink($post_id);

$excerpt = get_field('quote_plain_text', $post_id);
$source  = get_field('quote_source', $post_id);

$image   = '';
$meta    = '';

if ($source) {
    if (is_array($source)) {
        $source = reset($source);
    }
    
    $source_title = get_the_title($source->ID);
    $meta = 'from <a href="' . esc_url(get_permalink($source->ID)) . '">' . esc_html($source_title) . '</a>';
    
    if (get_post_type($source->ID) === 'book') {
        $author = get_field('author_profile', $source->ID);
        if ($author) {
            if (is_array($author)) {
                $author = reset($author);
            }
            $meta .= ' by <a href="' . esc_url(get_permalink($author->ID)) . '">' . esc_html(get_the_title($author->ID)) . '</a>';
        }
    }

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