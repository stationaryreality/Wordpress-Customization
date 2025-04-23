<?php
/* Template Name: Lexicon Aggregator */
get_header();
?>

<main class="lexicon-page" style="max-width:800px; margin:2rem auto;">
  <h1>Lexicon:</h1>
<div style="margin-bottom: 2rem;"></div>
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
