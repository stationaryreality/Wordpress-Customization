<?php
/* Template Name: Narrative Threads */

get_header(); ?>

<main id="primary" class="site-main page-narrative-threads">
  
<a id="narrative-threads"></a>
<section>
  <?php
get_template_part('template-parts/grids/chapter', null, [
      'title' => 'Narrative Threads',
    ]);
  ?>
</section>

</main>

<?php get_footer(); ?>