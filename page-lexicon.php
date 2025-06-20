<?php
/**
 * Template Name: Lexicon Directory
 */

get_header();

$concepts = get_posts([
  'post_type' => 'concept',
  'numberposts' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
]);
?>

<main class="lexicon-directory">
  <section class="container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
    <h1>Lexicon</h1>
    <p class="intro-text">Definitions and concepts referenced across books, profiles, and chapters.</p>

    <div class="concept-list">
      <?php foreach ($concepts as $concept): ?>
        <?php
          $definition = get_field('definition', $concept->ID);
        ?>
        <div class="concept-entry" style="margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
          <h2 style="margin-bottom: 0.5rem;">
            <a href="<?php echo get_permalink($concept); ?>">
              <?php echo esc_html(get_the_title($concept)); ?>
            </a>
          </h2>
          <?php if ($definition): ?>
            <p style="margin: 0;"><?php echo esc_html(wp_trim_words($definition, 30)); ?></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
