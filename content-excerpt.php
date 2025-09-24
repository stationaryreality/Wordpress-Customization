<?php
$source = get_field('excerpt_source'); // Book, Reference, etc.
?>

<div class="person-content" style="text-align:center;">
  <h1><?php the_title(); ?></h1>

  <div class="excerpt-content" style="margin-top:1em;">
    <?php the_content(); ?>
  </div>

  <?php if ($source): ?>
    <p class="excerpt-source" style="margin-top:1em;">
      Source: <a href="<?php echo esc_url(get_permalink($source->ID)); ?>">
        <?php echo esc_html(get_the_title($source->ID)); ?>
      </a>
    </p>
  <?php endif; ?>

      <?php show_featured_in_threads('excerpts_referenced'); ?>

  <?php get_template_part('content/excerpt-nav'); ?>
</div>
