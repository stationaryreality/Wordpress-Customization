<?php
/* Template Name: Movies Directory */
get_header();

$movies_query = new WP_Query([
  'post_type'      => 'movie',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

get_template_part('template-parts/grids/movie', null, [
  'query' => $movies_query,
  'title' => 'Movies',
  'emoji' => '🎬', // centralized lookup if you’ve got it
]);

get_footer();
