<?php
$source = get_field('song'); // Book, Reference, etc.
?>

<div class="person-content">
  <h1><?php the_title(); ?></h1>

  <div class="lyric-content">
    <?php the_content(); ?>
  </div>

    <?php if ($source): ?>
    <p class="lyric-source" style="margin-top:1em;">
      Source: <a href="<?php echo esc_url(get_permalink($source->ID)); ?>">
        <?php echo esc_html(get_the_title($source->ID)); ?>
      </a>
    </p>
  <?php endif; ?>

  <?php show_featured_in_threads('lyrics_referenced'); ?>

  <?php echo fn_taxonomy_bubbles(get_the_ID()); ?>

  <?php get_template_part('content/lyric-nav'); ?>
</div>
