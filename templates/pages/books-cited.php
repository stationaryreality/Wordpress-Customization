<?php
/* Template Name: Books Cited */
get_header();
?>

<main id="primary" class="site-main page-books-cited">

<?php
get_template_part('template-parts/grids/book', null, [
  'title' => 'Books Cited',
  'emoji' => '📚',
]);
?>
    
</main>

<?php get_footer(); ?>