<?php
get_header();

if (have_posts()) :
  while (have_posts()) : the_post();

    $author = get_field('author');
    $summary = get_field('summary');
    $cover = get_field('cover_image'); // ACF image (returning array)
    $img_url = $cover ? $cover['sizes']['medium'] : '';
    ?>

    <div class="book-content">
      <?php if ($img_url): ?>
        <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
      <?php endif; ?>

      <h1><?php the_title(); ?></h1>

      <?php if ($author): ?>
        <p><strong><?php echo esc_html($author); ?></strong></p>
      <?php endif; ?>

      <div class="book-description">
        <?php the_content(); ?>
      </div>

      <?php get_template_part('content/book-nav'); ?>
    </div>

  <?php endwhile;
endif;

get_footer();
