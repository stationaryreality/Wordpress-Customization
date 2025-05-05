<?php
/* Template Name: Lexicon Aggregator */
get_header();
?>

<?php
if ( has_post_thumbnail() ) {
    echo '<div class="featured-image">';
    the_post_thumbnail('full'); // or 'large', or use a custom size
    echo '</div>';
}
?>
<BR><BR>

<h1 class="lexicon-title">Lexicon:</h1>

<div class="page-content">
    <?php the_content(); ?>
</div>

<main class="lexicon-page" style="max-width:800px; margin:2rem auto;">

  <?php
  $terms = get_terms([
      'taxonomy' => 'post_tag',
      'hide_empty' => false,
  ]);

  // Filter for Concepts
  $concepts = array_filter($terms, function($term) {
      return strpos($term->description, '[Type: Concept]') !== false;
  });

  // Sort alphabetically
  usort($concepts, function($a, $b) {
      return strcmp($a->name, $b->name);
  });

  if (!empty($concepts)) :
      echo '<ul class="lexicon-list">';
      foreach ($concepts as $term) {
          $term_name = $term->name;
          $definition = '—';

          if (preg_match('/\[Term:\s*(.*?)\]/', $term->description, $matches)) {
              $term_name = $matches[1];
          }

          if (preg_match('/\[Definition:\s*(.*?)\]/', $term->description, $matches)) {
              $definition = $matches[1];
          }

          echo '<li style="margin-bottom:1.5rem;">';
          echo '<strong style="font-size:1.2rem;">' . esc_html($term_name) . '</strong><br>';
          echo '<p>' . esc_html($definition) . '</p>';
          echo '</li>';
      }
      echo '</ul>';
  else :
      echo '<p>No concept terms found.</p>';
  endif;
  ?>

</main>

<?php get_footer(); ?>
