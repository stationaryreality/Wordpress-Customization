<?php
/* Template Name: Excerpts Directory */
get_header();

$excerpts_query = new WP_Query([
  'post_type' => 'excerpt',
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC'
]);

get_template_part('template-parts/excerpt', 'list', [
  'query' => $excerpts_query,
  'title' => 'Excerpts'
]);

get_footer();
