<?php
/* Template Name: Songs Featured */
get_header();
?>

<div class="song-grid">
<?php
$songs = new WP_Query(array(
  'post_type'      => 'song',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
));

if ($songs->have_posts()):
  while ($songs->have_posts()): $songs->the_post();
    $bio = get_field('song_bio');
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
