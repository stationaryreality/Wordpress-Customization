<?php
/**
 * Shared Profile List Template
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
$title = $info['title'] ?? $args['title'] ?? 'People Referenced';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

// If a WP_Query was passed, convert it to cards (legacy support)
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('profile', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

// No data to render
if (empty($items)) {
    return;
}
?>

<section class="profile-list container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
  <h1>
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term) : ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>
  <p class="intro-text">People referenced across the archive.</p>

  <div class="profile-items">
    <?php foreach ($items as $item): ?>
      <?php
        $img_url = !empty($item['image']) ? $item['image'] : '';
      ?>
      <div class="profile-entry" style="display:flex; align-items:flex-start; gap:1rem; margin-bottom:2rem; border-bottom:1px solid #ddd; padding-bottom:1rem;">
        <?php if ($img_url): ?>
          <a href="<?php echo esc_url($item['url']); ?>" class="profile-thumb">
            <img src="<?php echo esc_url($img_url); ?>" 
                 alt="<?php echo esc_attr($item['title']); ?>" 
                 style="width:48px; height:48px; border-radius:50%; object-fit:cover;">
          </a>
        <?php endif; ?>

        <div class="profile-text">
          <h2 style="margin-bottom:0.5rem;">
            <a href="<?php echo esc_url($item['url']); ?>">
              <?php echo esc_html($item['title']); ?>
            </a>
          </h2>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>