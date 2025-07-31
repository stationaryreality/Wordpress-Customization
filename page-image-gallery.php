<?php
/* Template Name: Image Gallery */
get_header();
?>

<div class="image-grid">
<?php
$images = new WP_Query(array(
  'post_type'      => 'image',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
));

if ($images->have_posts()):
  while ($images->have_posts()): $images->the_post();
    $caption = get_field('image_caption');
    $cover = get_field('cover_image');
    $img_url = $cover ? $cover['sizes']['thumbnail'] : '';
    ?>
    <div class="book-item">
      <a href="<?php the_permalink(); ?>">
        <?php if ($img_url): ?>
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" style="aspect-ratio:1/1;object-fit:cover;">
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
      </a>
      <?php if ($caption): ?>
        <p><?php echo esc_html(wp_trim_words($caption, 20)); ?></p>
      <?php endif; ?>
    </div>
    <?php
  endwhile;
  wp_reset_postdata();
endif;
?>
</div>

<?php get_footer(); ?>
