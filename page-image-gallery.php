<?php
/* Template Name: Image Gallery */
get_header();
?>

<div class="cited-grid cited-grid--images">
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
    $image   = get_field('image_file'); // ACF image array
    // choose a size consistent with books (change 'medium' if you prefer)
$img_url = $image ? $image['url'] : '';
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('cited-item'); ?>>
      <a class="cited-item__link" href="<?php the_permalink(); ?>">
        <?php if ($img_url): ?>
          <div class="cited-item__thumb" aria-hidden="true">
            <img src="<?php echo esc_url($img_url); ?>"
                 alt="<?php echo esc_attr( get_the_title() ); ?>"
                 loading="lazy"
                 decoding="async" />
          </div>
        <?php endif; ?>

        <h3 class="cited-item__title"><?php the_title(); ?></h3>
      </a>

      <?php if ($caption): ?>
        <p class="cited-item__meta"><?php echo esc_html( wp_trim_words( $caption, 20 ) ); ?></p>
      <?php endif; ?>
    </article>
    <?php
  endwhile;
  wp_reset_postdata();
endif;
?>
</div>

<?php get_footer(); ?>
