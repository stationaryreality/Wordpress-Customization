<?php
/**
 * Template Part: Fragment Grid
 *
 * Standardized contract:
 * - items       => array (normalized cards)
 * - query       => WP_Query|null (optional, for legacy callers)
 * - info        => [ 'title' => '', 'emoji' => '', 'type' => '' ]
 * - search_term => string (optional)
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

// Legacy compatibility.
// If no query or items were supplied, behave like the old template.
if (!$query && empty($items)) {
    $query = new WP_Query([
        'post_type'      => 'fragment',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}

// Backward compatibility: allow direct title from older callers
$title = $info['title'] ?? $args['title'] ?? 'Narrative Fragments';

// If a WP_Query was passed, convert it to cards (legacy support)
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('fragment', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

// No data to render
if (empty($items)) {
    return;
}
?>

<section style="margin-bottom:4rem;">
  <h2><?php echo esc_html($title); ?></h2>
  <div class="tag-posts-grid">
    <?php foreach ($items as $item): ?>
      <div class="tag-post-item">
        <a href="<?php echo esc_url($item['url']); ?>" class="tag-post-thumbnail">
          <?php if (!empty($item['image'])): ?>
            <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
          <?php endif; ?>
        </a>
        <a href="<?php echo esc_url($item['url']); ?>" class="tag-post-title">
          <?php echo esc_html($item['title']); ?>
        </a>
        <!-- Excerpt removed for cleaner fragment grid presentation -->
      </div>
    <?php endforeach; ?>
  </div>
</section>