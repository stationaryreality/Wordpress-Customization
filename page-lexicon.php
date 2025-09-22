<?php
/**
 * Template Name: Lexicon Directory
 */

get_header();

$concepts = new WP_Query([
  'post_type'      => 'concept',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC'
]);

get_template_part('template-parts/concept', 'list', [
  'query' => $concepts,
  'title' => 'Lexicon',
  'emoji' => '🔎',
]);

get_footer();
