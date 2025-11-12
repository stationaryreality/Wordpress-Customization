<?php
/* Template Name: Shows Directory */
get_header();

$shows_query = new WP_Query([
  'post_type'      => 'show',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

get_template_part('template-parts/show', 'grid', [
  'query' => $shows_query,
  'title' => 'TV Shows',
  'emoji' => '📺', // TV emoji suits best
]);

get_footer();
