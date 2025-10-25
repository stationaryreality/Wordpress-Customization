<?php
/**
 * Template Part: Element Grid
 *
 * Expected args:
 * - query: WP_Query object (optional, defaults to all elements)
 * - title: Section title (optional)
 */

$query = $args['query'] ?? null;
$title = $args['title'] ?? 'Elements';

// Default to all elements if none are passed
if (!$query) {
  $query = new WP_Query([
    'post_type'      => 'element',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
  ]);
}

if (!$query->have_posts()) return;
?>

<section style="margin-bottom:4rem;">
  <h2><?php echo esc_html($title); ?></h2>
  <div class="tag-posts-grid">
    <?php while ($query->have_posts()) : $query->the_post(); ?>
      <div class="tag-post-item">
        <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
          <?php if (has_post_thumbnail()) : ?>
            <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>" alt="<?php the_title_attribute(); ?>">
          <?php endif; ?>
        </a>
        <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
        <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>
