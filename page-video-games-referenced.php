<?php
/* Template Name: Games Directory */
get_header();

$games_query = new WP_Query([
  'post_type'      => 'game',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

get_template_part('template-parts/game', 'grid', [
  'query' => $games_query,
  'title' => 'Video Games',
  'emoji' => '🎮',
]);

get_footer();