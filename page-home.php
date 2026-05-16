<?php
/* Template Name: Custom Home */

get_header(); ?>

<main class="homepage-posts">

  <!-- Site Resources Section -->
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

</main>

<?php get_footer(); ?>