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

<a id="narrative-threads"></a>
<section>
  <?php
    get_template_part('template-parts/chapter', 'grid', [
      'title' => 'Narrative Threads',
    ]);
  ?>

  <hr style="margin: 4em auto; max-width: 80%; border: 0; border-top: 1px solid #ccc;">

  <!-- Narrative Fragments Section -->
<a id="narrative-fragments"></a>
<section>
  <?php
    get_template_part('template-parts/fragment', 'grid', [
      'title' => 'Narrative Fragments',
    ]);
  ?>
</section>

</main>

<?php get_footer(); ?>
