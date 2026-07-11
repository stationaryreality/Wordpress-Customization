<?php
/**
 * Template Part: Element Grid
 * Styled to visually align with Chapter / Fragment grids
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
$title = $info['title'] ?? $args['title'] ?? 'Elements';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

// If a WP_Query was passed, convert it to cards (legacy support)
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('element', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

// No data to render
if (empty($items)) {
    return;
}
?>

<section class="cpt-section element-grid" style="margin-bottom:4rem;">

  <h2>
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>

    <?php if ($search_term): ?>
      <span style="font-weight:normal;font-size:0.9em;color:#666;">
        containing “<?php echo esc_html($search_term); ?>”
      </span>
    <?php endif; ?>
  </h2>

  <div class="tag-posts-grid">

    <?php foreach ($items as $item): ?>
      <?php
        // Use the normalized image URL (already large/medium as built by card builder)
        $img_url = !empty($item['image']) ? $item['image'] : '';
      ?>
      <div class="tag-post-item">

        <a href="<?php echo esc_url($item['url']); ?>" class="tag-post-thumbnail">
          <?php if ($img_url): ?>
            <img
              src="<?php echo esc_url($img_url); ?>"
              alt="<?php echo esc_attr($item['title']); ?>"
            >
          <?php endif; ?>
        </a>

        <a href="<?php echo esc_url($item['url']); ?>" class="tag-post-title">
          <?php echo esc_html($item['title']); ?>
        </a>

        <?php if (!empty($item['excerpt'])): ?>
          <p class="tag-post-excerpt">
            <?php echo esc_html(wp_trim_words($item['excerpt'], 20)); ?>
          </p>
        <?php endif; ?>

      </div>
    <?php endforeach; ?>

  </div>

</section>