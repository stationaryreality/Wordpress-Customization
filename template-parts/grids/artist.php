<?php
/**
 * Artist Grid Template
 *
 * Supports two modes:
 *
 * 1. Query Mode – uses $artist_query (WP_Query)
 * 2. Normalized Cards Mode – uses $items
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$section_title = $args['title'] ?? '';
$emoji        = $args['emoji'] ?? '';
$search_term  = $args['search_term'] ?? '';

// Preserve backward-compatible variable for the original loop
// (the original code uses $artist_query)
$artist_query = $args['artist_query'] ?? $query ?? null;

// Early bailout if no data source has content
if (
    empty($items)
    && (!$artist_query instanceof WP_Query || !$artist_query->have_posts())
) {
    return;
}
?>

<div class="author-grid">
  <?php if (!empty($items)): ?>
    <?php foreach ($items as $item): ?>
      <?php
        $portrait_url = !empty($item['image']) ? $item['image'] : '';
      ?>
      <div class="book-item" style="text-align:center;">
        <a href="<?php echo esc_url($item['url']); ?>">
          <?php if ($portrait_url): ?>
            <img src="<?php echo esc_url($portrait_url); ?>" alt="<?php echo esc_attr($item['title']); ?>" style="border-radius:50%; width:100px; height:100px; object-fit:cover;">
          <?php endif; ?>
          <h3><?php echo esc_html($item['title']); ?></h3>
        </a>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
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
    <?php wp_reset_postdata(); ?>
  <?php endif; ?>
</div>