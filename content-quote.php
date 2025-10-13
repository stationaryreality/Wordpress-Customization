<?php
$source = get_field('quote_source'); // Can be a Book or Reference CPT
?>

<div class="person-content" style="text-align:center;">
  <h1><?php the_title(); ?></h1>

  <div class="quote-content" style="margin-top:1em;">
    <?php the_content(); ?>
  </div>

  <?php if ($source): ?>
    <p class="quote-source" style="margin-top:1em;">
      Source: <a href="<?php echo esc_url(get_permalink($source->ID)); ?>">
        <?php echo esc_html(get_the_title($source->ID)); ?>
      </a>
    </p>
  <?php endif; ?>

  <?php show_featured_in_threads('quotes_referenced'); ?>

    <?php echo fn_taxonomy_bubbles(get_the_ID()); ?>

  <?php get_template_part('content/quote-nav'); ?>
</div>
