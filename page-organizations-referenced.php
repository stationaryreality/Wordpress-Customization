<?php
/* Template Name: Organizations Referenced */
get_header();
?>

<h2 style="margin-bottom:1em;">Featured Organizations</h2>
<div class="song-grid">
<?php
$featured = new WP_Query([
  'post_type'      => 'organization',
  'posts_per_page' => -1,
  'meta_key'       => 'is_featured',
  'meta_value'     => 1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

if ($featured->have_posts()):
  while ($featured->have_posts()): $featured->the_post();
    $bio = get_field('org_bio');
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

<h2 style="margin-top:3em; margin-bottom:1em;">Referenced Organizations</h2>
<div class="song-grid">
<?php
$referenced = new WP_Query([
  'post_type'      => 'organization',
  'posts_per_page' => -1,
  'meta_query' => [
    [
      'key'     => 'is_featured',
      'value'   => 1,
      'compare' => '!=',
    ]
  ],
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

if ($referenced->have_posts()):
  while ($referenced->have_posts()): $referenced->the_post();
    $bio = get_field('org_bio');
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
