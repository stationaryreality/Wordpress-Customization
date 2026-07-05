<?php
/**
 * Template Name: Lyrics Directory
 */
get_header();

$lyrics_query = new WP_Query([
  'post_type'      => 'lyric',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
  'tax_query'      => [
    [
      'taxonomy' => 'song_category',
      'field'    => 'slug',
      'terms'    => ['rap'],
      'operator' => 'NOT IN',
    ],
  ],
]);

get_template_part('template-parts/grids/lyric', null, [
  'query' => $lyrics_query,
  'title' => 'Lyrics',
  'emoji' => '🎼',
]);

get_footer();