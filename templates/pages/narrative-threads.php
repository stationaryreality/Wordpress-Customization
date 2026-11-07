<?php
/* Template Name: Narrative Threads */

get_header(); ?>

<main id="primary" class="site-main page-narrative-threads">
  
  <?php
  // The template part itself already outputs the <section> and handles the spacing
  get_template_part('template-parts/grids/chapter', null, [
      'title' => 'Narrative Threads',
  ]);
  ?>

</main>

<?php get_footer(); ?>