<?php
/* Template Name: Elements */

get_header(); ?>

<main class="homepage-posts">
  <a id="elements"></a>
  <section>
    <?php
get_template_part('template-parts/grids/element', null, [
        'title' => 'Elements',
      ]);
    ?>
  </section>
</main>

<?php get_footer(); ?>
