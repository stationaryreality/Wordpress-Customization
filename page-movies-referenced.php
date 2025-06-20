<?php
/* Template Name: Movies Referenced */
get_header(); ?>

<div class="cited-grid">
<?php
$movies = new WP_Query(array(
  'post_type' => 'movie',
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC'
));

if ($movies->have_posts()):
  while ($movies->have_posts()): $movies->the_post();
    $director = get_field('director');
    $summary = get_field('summary');
    $cover = get_field('cover_image');
    $img_url = $cover ? $cover['sizes']['medium'] : '';
    ?>
    <div class="cited-item">
      <a href="<?php the_permalink(); ?>">
        <?php if ($img_url): ?>
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
      </a>
      <?php if ($director): ?>
        <p><strong><?php echo esc_html($director); ?></strong></p>
      <?php endif; ?>
      <?php if ($summary): ?>
        <p><?php echo esc_html(wp_trim_words($summary, 25)); ?></p>
      <?php endif; ?>
    </div>
    <?php
  endwhile;
  wp_reset_postdata();
endif;
?>
</div>

<?php get_footer(); ?>
