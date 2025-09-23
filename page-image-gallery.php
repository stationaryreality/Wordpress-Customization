<?php
/* Template Name: Image Gallery */
get_header();

$images_query = new WP_Query([
  'post_type'      => 'image',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

get_template_part('template-parts/image', 'grid', [
  'query' => $images_query,
  'title' => 'Image Gallery',
  'emoji' => '🖼',
]);

get_footer();
