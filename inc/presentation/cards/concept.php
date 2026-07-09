<?php
/*
|--------------------------------------------------------------------------
| CONCEPTS
|--------------------------------------------------------------------------
*/

if ($type === 'concept') {

    $excerpt = get_field('definition');

    $image = has_post_thumbnail()
        ? get_the_post_thumbnail_url($post_id, 'medium')
        : '';
}