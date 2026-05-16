<?php
/* Template Name: Video Room */

get_header();

$videos_query = new WP_Query([
    'post_type'      => 'video',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

get_template_part('template-parts/video', 'grid', [
    'query' => $videos_query,
    'title' => 'Video Room',
    'emoji' => '📼',
]);

get_footer();