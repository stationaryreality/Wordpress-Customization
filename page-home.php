<?php
/* Template Name: Custom Home */

get_header(); ?>

<main class="homepage-posts">

<?php
get_template_part('template-parts/page', 'grid', [
  'title' => 'Site Resources',
  'excluded_slugs' => [
    'subscription-confirmed',
    'unsubscribe',
    'unsubscribed',
    'manage-subscription',
    'contact',
    'privacy-policy',
  ],
]);
?>

  <hr style="margin: 4em auto; max-width: 80%; border: 0; border-top: 1px solid #ccc;">

  <!-- Narrative Threads Section -->
  <section id="narrative-threads">
  </section>

<?php
get_template_part('template-parts/chapter', 'grid', [
  'title' => 'Narrative Threads',
]);
?>

  <hr style="margin: 4em auto; max-width: 80%; border: 0; border-top: 1px solid #ccc;">

  <!-- Narrative Fragments Section -->
  <section id="narrative-fragments">
  </section>

<?php
get_template_part('template-parts/fragment', 'grid', [
  'title' => 'Narrative Fragments',
]);
?>


</main>

<?php get_footer(); ?>
