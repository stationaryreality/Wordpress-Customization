<?php
/* Template Name: Elements */

get_header(); ?>

<main class="homepage-posts">
  <a id="strands"></a>
  <section>
    <?php
get_template_part('template-parts/grids/element', null, [
        'title' => 'Strands',
      ]);
    ?>
  </section>
</main>

<?php get_footer(); ?>
