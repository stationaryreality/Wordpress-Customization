<?php
$excerpt_html = get_field('excerpt_cover_block_full'); // optional ACF field
$source = get_field('excerpt_source'); // should return a post object (book, article, etc.)
$profile = get_field('excerpt_profile'); // optional: linked profile CPT
?>

<div class="person-content">
  <h1><?php the_title(); ?></h1>

  <div class="person-bio">
    <?php if ($excerpt_html): ?>
      <div class="excerpt-block excerpt-cover-html">
        <?php echo do_blocks($excerpt_html); ?>
      </div>
    <?php else: ?>
      <div class="excerpt-block">
        <?php the_content(); ?>
      </div>
    <?php endif; ?>

    <?php if ($source): ?>
      <p class="excerpt-source">
        From: <a href="<?php echo esc_url(get_permalink($source->ID)); ?>">
          <?php echo esc_html(get_the_title($source->ID)); ?>
        </a>
      </p>
    <?php endif; ?>

    <?php if ($profile): ?>
      <p class="excerpt-profile">
        Related Profile: <a href="<?php echo esc_url(get_permalink($profile->ID)); ?>">
          <?php echo esc_html(get_the_title($profile->ID)); ?>
        </a>
      </p>
    <?php endif; ?>
  </div>

  <?php get_template_part('content/excerpt-nav'); ?>
</div>
