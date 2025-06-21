<?php
/**
 * Template Name: Lyrics Directory
 */

get_header();

$lyrics = get_posts([
  'post_type' => 'lyric',
  'numberposts' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
]);
?>

<main class="lexicon-directory">
  <section class="container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
    <h1>Lyrics</h1>
    <p class="intro-text">Lyrics referenced across featured chapters and profiles.</p>

    <div class="concept-list">
      <?php foreach ($lyrics as $lyric): ?>
        <?php
          $text = get_field('lyric_text', $lyric->ID);
        ?>
        <div class="concept-entry" style="margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
          <h2 style="margin-bottom: 0.5rem;">
            <a href="<?php echo get_permalink($lyric); ?>">
              <?php echo esc_html(get_the_title($lyric)); ?>
            </a>
          </h2>
          <?php if ($text): ?>
            <p style="margin: 0;"><?php echo esc_html(wp_trim_words($text, 30)); ?></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
