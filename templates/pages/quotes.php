<?php
/* Template Name: Quotes Directory */
get_header();

$quotes = new WP_Query([
  'post_type'      => 'quote',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

get_template_part('template-parts/lists/quote', null, [
  'query' => $quotes,
  'title' => 'Quotes',
  'emoji' => '💬',
]);

get_footer();
