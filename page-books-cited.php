<?php
/* Template Name: Books Cited */
get_header(); ?>

<div class="cited-grid">
<?php
$books = new WP_Query(array(
  'post_type' => 'book',
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC'
));

if ($books->have_posts()):
  while ($books->have_posts()): $books->the_post();
    $author = get_field('author');         // ACF field
    $summary = get_field('summary');       // Optional ACF
    $cover = get_field('cover_image');     // ACF image (returning array)
    $img_url = $cover ? $cover['sizes']['medium'] : '';
    ?>
    <div class="cited-item">
      <a href="<?php the_permalink(); ?>">
        <?php if ($img_url): ?>
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
      </a>
      <?php if ($author): ?>
        <p><strong><?php echo esc_html($author); ?></strong></p>
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
