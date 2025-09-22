<?php
/**
 * Artist Grid Template
 *
 * Accepts:
 * - $artist_query : WP_Query object containing artist posts
 */
if (!isset($artist_query) || !$artist_query->have_posts()) return;
?>

<div class="author-grid">
  <?php while ($artist_query->have_posts()): $artist_query->the_post(); 
    $portrait = get_field('portrait_image');
    $img_url  = $portrait ? $portrait['sizes']['thumbnail'] : '';
  ?>
    <div class="book-item" style="text-align:center;">
      <a href="<?php the_permalink(); ?>">
        <?php if ($img_url): ?>
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" style="border-radius:50%; width:100px; height:100px; object-fit:cover;">
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
      </a>
    </div>
  <?php endwhile; ?>
</div>
<?php wp_reset_postdata(); ?>
