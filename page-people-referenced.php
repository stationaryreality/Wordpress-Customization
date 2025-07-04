<?php
/* Template Name: People Referenced */
get_header();
?>

<div class="author-grid">
<?php
$profiles = new WP_Query(array(
  'post_type'      => 'profile',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
));

if ($profiles->have_posts()):
  while ($profiles->have_posts()): $profiles->the_post();
    $portrait = get_field('portrait_image'); // ACF image (returning array)
    $img_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
    ?>
    <div class="book-item">
      <a href="<?php the_permalink(); ?>">
        <?php if ($img_url): ?>
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
      </a>

    </div>
    <?php
  endwhile;
  wp_reset_postdata();
endif;
?>
</div>

<?php get_footer(); ?>
