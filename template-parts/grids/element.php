<?php
/**
 * Template Part: Element Grid
 * 
 * Parameters:
 * - $query       => WP_Query|null
 * - $items       => array (normalized cards)
 * - $title       => string
 * - $emoji       => string (optional)
 * - $search_term => string (optional)
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$title        = $args['title'] ?? 'Strands';
$emoji        = $args['emoji'] ?? '';
$search_term  = $args['search_term'] ?? '';

// Fallback query if no items or query provided
if (!$query && empty($items)) {
    $query = new WP_Query([
        'post_type'      => 'element',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);
}

// Convert WP_Query to items array
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = [
            'title'   => get_the_title(),
            'url'     => get_permalink(),
            'image'   => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
            // 'excerpt' removed from array since we aren't displaying it
        ];
    }
    wp_reset_postdata();
}

// No data = bail
if (empty($items)) {
    return;
}
?>

<section class="square-grid-section">
  <h2>
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      <span>containing “<?php echo esc_html($search_term); ?>”</span>
    <?php endif; ?>
  </h2>

  <div class="square-grid">
    <?php foreach ($items as $item): ?>
      <div class="square-card">
        <a href="<?php echo esc_url($item['url']); ?>" class="square-card-link">
          <?php if (!empty($item['image'])): ?>
            <img 
              src="<?php echo esc_url($item['image']); ?>" 
              alt="<?php echo esc_attr($item['title']); ?>"
              class="square-image"
            >
          <?php endif; ?>
        </a>
        <h3 class="square-card-title">
          <a href="<?php echo esc_url($item['url']); ?>">
            <?php echo esc_html($item['title']); ?>
          </a>
        </h3>
        <!-- Excerpt block intentionally removed to prevent auto-generated post content from cluttering the grid -->
      </div>
    <?php endforeach; ?>
  </div>
</section>