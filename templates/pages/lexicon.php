<?php
/**
 * Template Name: Lexicon Directory
 */
get_header();
?>

<main id="primary" class="site-main page-lexicon">

<?php
$concepts = new WP_Query([
  'post_type'      => 'concept',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC'
]);

get_template_part('template-parts/lists/concept', null, [
  'query' => $concepts,
  'title' => 'Lexicon',
  'emoji' => '🔎',
]);
?>

</main>

<?php get_footer(); ?>