<?php
/* Template Name: Reference Directory */
get_header();

$references = new WP_Query([
  'post_type'      => 'reference',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

get_template_part('template-parts/reference', 'list', [
  'query' => $references,
  'title' => 'Research Sources',
  'emoji' => '📚',
]);

get_footer();
