<?php
/**
 * Show Grid Template
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
$title = $info['title'] ?? $args['title'] ?? 'Shows';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

// If a WP_Query was passed, convert it to cards (legacy support)
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('show', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

// No data to render
if (empty($items)) {
    return;
}
?>

<section class="cited-grid-wrapper cited-grid-wrapper--shows">
  <h1 class="cited-grid__heading">
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>

  <div class="cited-grid cited-grid--shows">
    <?php foreach ($items as $item): ?>
      <?php
        $creator = $item['meta'] ?? '';
        $summary = $item['excerpt'] ?? '';
        $img_url = !empty($item['image']) ? $item['image'] : '';
      ?>
      <article class="cited-item">
        <a class="cited-item__link" href="<?php echo esc_url($item['url']); ?>">
          <?php if ($img_url): ?>
            <div class="cited-item__thumb" aria-hidden="true">
              <img src="<?php echo esc_url($img_url); ?>"
                   alt="<?php echo esc_attr($item['title']); ?>"
                   loading="lazy"
                   decoding="async" />
            </div>
          <?php endif; ?>

          <h3 class="cited-item__title"><?php echo esc_html($item['title']); ?></h3>
        </a>

        <?php if ($creator): ?>
          <p class="cited-item__meta"><strong><?php echo esc_html($creator); ?></strong></p>
        <?php endif; ?>

        <?php if ($summary): ?>
          <p class="cited-item__excerpt"><?php echo esc_html(wp_trim_words($summary, 25)); ?></p>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </div>
</section>