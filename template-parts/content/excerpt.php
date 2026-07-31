<?php
$source = get_field('excerpt_source'); // Book, Movie, Show, etc.
?>

<div class="cpt-excerpt-content">

  <?php get_template_part('template-parts/navigation/excerpt'); ?>

  <h1 class="cpt-excerpt-title"><?php the_title(); ?></h1>

  <div class="cpt-excerpt-text">
    <?php the_content(); ?>
  </div>

  <?php if ($source): ?>
    <p class="cpt-excerpt-source">
      Source:
      <a href="<?php echo esc_url(get_permalink($source->ID)); ?>">
        <?php echo esc_html(get_the_title($source->ID)); ?>
      </a>
    </p>
  <?php endif; ?>

  <?php if (function_exists('kp_render_references')): ?>
    <div class="cpt-excerpt-references">
      <?php echo kp_render_references(get_the_ID()); ?>
    </div>
  <?php endif; ?>

  <?php show_featured_in_threads('excerpts_referenced'); ?>

  <div class="cpt-excerpt-bubbles">
    <?php echo fn_taxonomy_bubbles(get_the_ID()); ?>
  </div>

</div>