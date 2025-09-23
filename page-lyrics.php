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
]);

get_template_part('template-parts/lyric', 'grid', [
  'query' => $lyrics_query,
  'title' => 'Lyrics',
  'emoji' => '🎼', // or pull from centralized lookup
]);

get_footer();
