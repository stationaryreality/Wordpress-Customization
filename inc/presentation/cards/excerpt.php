<?php
/*
    |--------------------------------------------------------------------------
    | EXCERPTS
    |--------------------------------------------------------------------------
    */

if ($type === 'excerpt') {

$excerpt = get_field('excerpt_plain_text');

$source = get_field('excerpt_source');

$author_name = '';

if ($source && get_post_type($source->ID) === 'book') {

    $author = get_field('author_profile', $source->ID);

    if ($author) {

        if (is_array($author)) {
            $author = reset($author);
        }

        $author_name = get_the_title($author->ID);
    }
}

$meta = $author_name;

/*
|--------------------------------------------------------------------------
| IMAGE LOGIC
|--------------------------------------------------------------------------
|
| Prefer:
| 1. Source cover_image
| 2. Source featured image
| 3. Excerpt featured image
|
*/

if ($source) {

    $cover = get_field('cover_image', $source->ID);

    if ($cover && is_array($cover)) {

        $image =
            $cover['sizes']['medium']
            ?? $cover['sizes']['thumbnail']
            ?? $cover['url'];

    } elseif (has_post_thumbnail($source->ID)) {

        $image = get_the_post_thumbnail_url($source->ID, 'medium');
    }
}

if (!$image && has_post_thumbnail($post_id)) {

    $image = get_the_post_thumbnail_url($post_id, 'medium');
}
}