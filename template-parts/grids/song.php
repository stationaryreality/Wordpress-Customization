<?php
/**
 * Grid partial for displaying songs in search or related queries.
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

// Backward compatibility: allow direct title/emoji from older callers
$title = $info['title'] ?? $args['title'] ?? 'Songs';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '🎵';

// If a WP_Query was passed, convert it to cards (legacy support)
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('song', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

// No data to render
if (empty($items)) {
    return;
}
?>

<section style="margin-bottom:4rem;">
  <h2>
    <?php echo esc_html($emoji); ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h2>

  <div class="cited-grid">
    <?php foreach ($items as $item): ?>
      <div class="cited-item">
        <a href="<?php echo esc_url($item['url']); ?>">
          <?php if (!empty($item['image'])): ?>
            <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
          <?php endif; ?>
          <h3><?php echo esc_html($item['title']); ?></h3>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>