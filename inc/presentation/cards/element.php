<?php
/*
|--------------------------------------------------------------------------
| ELEMENTS
|--------------------------------------------------------------------------
*/

if ($type === 'element') {

    $image_field = get_field('image_file') ?: get_post_thumbnail_id();

    if (is_array($image_field)) {

        $image =
            $image_field['sizes']['medium']
            ?? $image_field['url'];

    } elseif ($image_field) {

        $image = wp_get_attachment_image_url($image_field, 'medium');
    }
}