<?php
/* Template Name: People Referenced */
get_header();

$profiles = new WP_Query([
  'post_type'      => 'profile',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

get_template_part('template-parts/grids/profile', null, [
  'query' => $profiles,
  'title' => 'People Referenced',
  'emoji' => '👤',
]);

get_footer();