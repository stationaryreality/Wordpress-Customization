<?php
/* Template Name: Organizations Referenced */
get_header();
?>

<h2 style="margin-bottom:1em;">Organizations</h2>
<div class="song-grid">
<?php
$orgs = new WP_Query([
  'post_type'      => 'organization',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

if ($orgs->have_posts()):
  while ($orgs->have_posts()): $orgs->the_post();
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
