<?php
/* Template Name: Authors Cited */
get_header();
?>

<div class="author-grid">
<?php
$authors = new WP_Query(array(
  'post_type'      => 'person',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
  'tax_query'      => array(
    array(
      'taxonomy' => 'person_type',
      'field'    => 'slug',
      'terms'    => 'author',
    ),
  ),
));

if ($authors->have_posts()):
  while ($authors->have_posts()): $authors->the_post();
    $bio      = get_field('bio');           // Optional ACF field
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
```
