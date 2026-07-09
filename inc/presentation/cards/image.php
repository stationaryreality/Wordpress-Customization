<?php
/*
|--------------------------------------------------------------------------
| IMAGES
|--------------------------------------------------------------------------
*/

if ($type === 'image') {

    $excerpt = get_field('image_caption');

    $image_field = get_field('image_file');

    $image = $image_field
        ? $image_field['sizes']['medium']
        : get_the_post_thumbnail_url($post_id, 'medium');
}