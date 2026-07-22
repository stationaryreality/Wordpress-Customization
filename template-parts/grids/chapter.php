<?php
/**
 * Chapter Grid Template
 * Reusable grid for displaying Chapter CPTs
 *
 * Supports two modes:
 *
 * 1. Query Mode – uses WP_Query (with optional fallback to all chapters)
 * 2. Normalized Cards Mode – uses $items array
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$section_title = $args['title'] ?? '';
$emoji        = $args['emoji'] ?? '';
$search_term  = $args['search_term'] ?? '';

// If no query is passed and no items, default to all chapters (homepage-style)
if (empty($items) && !$query) {
    $query = new WP_Query([
        'post_type'      => 'chapter',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}

// Early bailout if no data source has content
if (
    empty($items)
    && (!$query instanceof WP_Query || !$query->have_posts())
) {
    return;
}
?>

<section class="cpt-section chapter-grid" style="margin-bottom:4rem;">
  <?php if ($section_title): ?>
    <h2>
      <?php echo esc_html(trim($emoji . ' ' . $section_title)); ?>
      <?php if ($search_term): ?>
        containing “<?php echo esc_html($search_term); ?>”
      <?php endif; ?>
    </h2>
  <?php endif; ?>

  <div class="tag-posts-grid">
    <?php if (!empty($items)): ?>
      <?php foreach ($items as $item): ?>
        <div class="tag-post-item">
          <a href="<?php echo esc_url($item['url']); ?>" class="tag-post-thumbnail">
            <?php if (!empty($item['image'])): ?>
              <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
            <?php endif; ?>
          </a>
          <a href="<?php echo esc_url($item['url']); ?>" class="tag-post-title"><?php echo esc_html($item['title']); ?></a>
          <?php if (!empty($item['excerpt'])): ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <?php while ($query->have_posts()): $query->the_post(); ?>
        <div class="tag-post-item">
          <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
            <?php if (has_post_thumbnail()): ?>
              <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>" alt="<?php the_title_attribute(); ?>">
            <?php endif; ?>
          </a>
          <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
          <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
        </div>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
</section>