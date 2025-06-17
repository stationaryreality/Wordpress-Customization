<?php
/* Template Name: Artists Featured */
get_header();
?>

<div class="author-grid">
<?php
$artists = new WP_Query(array(
  'post_type'      => 'artist',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
));

if ($artists->have_posts()):
  while ($artists->have_posts()): $artists->the_post();
    $bio      = get_field('bio'); // Optional ACF field
    $portrait = get_field('portrait_image'); // ACF image (returning array)
    $img_url  = $portrait ? $portrait['sizes']['thumbnail'] : '';
    ?>
    <div class="book-item">
      <a href="<?php the_permalink(); ?>">
        <?php if ($img_url): ?>
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
      </a>
      <?php if ($bio): ?>
        <p><?php echo esc_html(wp_trim_words($bio, 20)); ?></p>
      <?php endif; ?>
    </div>
    <?php
  endwhile;
  wp_reset_postdata();
endif;
?>
</div>

<?php get_footer(); ?>
