<?php
/**
 * Template Name: Rap Lyrics
 */
get_header();

$rap_lyrics_query = new WP_Query([
  'post_type'      => 'lyric',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
  'tax_query'      => [
    [
      'taxonomy' => 'song_category',
      'field'    => 'slug',
      'terms'    => ['rap'],
      'operator' => 'IN',
    ],
  ],
]);

get_template_part('template-parts/lyric', 'grid', [
  'query' => $rap_lyrics_query,
  'title' => 'Rap Lyrics',
  'emoji' => '🎤',
]);

get_footer();