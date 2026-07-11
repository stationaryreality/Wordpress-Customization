<?php
/**
 * Template Part: Fresh Element Grid (Conflict‑Free)
 * 
 * Uses completely unique class names to avoid global CSS conflicts.
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
$title        = $args['title'] ?? 'Elements';
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
            'excerpt' => get_the_excerpt(),
        ];
    }
    wp_reset_postdata();
}

// No data = bail
if (empty($items)) {
    return;
}
?>

<!-- ===== FRESH ELEMENT GRID – Unique classes ===== -->
<section class="fresh-elements-section" style="margin-bottom:4rem;">
  
  <h2 class="fresh-elements-title">
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      <span class="fresh-elements-search-term">
        containing “<?php echo esc_html($search_term); ?>”
      </span>
    <?php endif; ?>
  </h2>

  <div class="fresh-elements-grid">
    <?php foreach ($items as $item): ?>
      <div class="fresh-element-card">
        <a href="<?php echo esc_url($item['url']); ?>" class="fresh-element-link">
          <?php if (!empty($item['image'])): ?>
            <img 
              src="<?php echo esc_url($item['image']); ?>" 
              alt="<?php echo esc_attr($item['title']); ?>"
              class="fresh-element-image"
            >
          <?php endif; ?>
        </a>
        <h3 class="fresh-element-card-title">
          <a href="<?php echo esc_url($item['url']); ?>">
            <?php echo esc_html($item['title']); ?>
          </a>
        </h3>
        <?php if (!empty($item['excerpt'])): ?>
          <p class="fresh-element-excerpt">
            <?php echo esc_html(wp_trim_words($item['excerpt'], 20)); ?>
          </p>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

</section>