<?php
/* Template Name: Narrative Fragments */

get_header(); ?>

<main class="homepage-posts">

<a id="narrative-fragments"></a>
<section>
  <?php
    get_template_part('template-parts/fragment', 'grid', [
      'title' => 'Narrative Episodes',
    ]);
  ?>
</section>

</main>

<?php get_footer(); ?>
