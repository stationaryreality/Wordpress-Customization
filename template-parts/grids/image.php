<?php
/**
 * Template Part: Image Grid (Dual Mode)
 *
 * Supports:
 * 1. $query (WP_Query) – legacy
 * 2. $items (array of normalized cards)
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$title        = $args['title'] ?? 'Images';
$emoji        = $args['emoji'] ?? '';
$search_term  = $args['search_term'] ?? '';

// Fallback query if no items and no query
if (!$query && empty($items)) {
    $query = new WP_Query([
        'post_type'      => 'image',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);
}

// Convert WP_Query to items if needed
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $image_field = get_field('image_file');
        $img_url = $image_field ? $image_field['sizes']['medium'] : get_the_post_thumbnail_url(get_the_ID(), 'medium');
        $items[] = [
            'title'   => get_the_title(),
            'url'     => get_permalink(),
            'image'   => $img_url,
            'caption' => get_field('image_caption'),
        ];
    }
    wp_reset_postdata();
}

if (empty($items)) {
    return;
}
?>

<section class="fresh-gallery-section" style="margin-bottom:4rem;">
  <h2 class="fresh-gallery-title">
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      <span class="fresh-gallery-search-term">
        containing “<?php echo esc_html($search_term); ?>”
      </span>
    <?php endif; ?>
  </h2>

  <div class="fresh-gallery-grid">
    <?php foreach ($items as $item): ?>
      <div class="fresh-gallery-card">
        <a href="<?php echo esc_url($item['url']); ?>" class="fresh-gallery-link">
          <?php if (!empty($item['image'])): ?>
            <img 
              src="<?php echo esc_url($item['image']); ?>" 
              alt="<?php echo esc_attr($item['title']); ?>"
              class="fresh-gallery-image"
            >
          <?php endif; ?>
        </a>
        <h3 class="fresh-gallery-card-title">
          <a href="<?php echo esc_url($item['url']); ?>">
            <?php echo esc_html($item['title']); ?>
          </a>
        </h3>
        <?php if (!empty($item['caption'])): ?>
          <p class="fresh-gallery-caption">
            <?php echo esc_html(wp_trim_words($item['caption'], 20)); ?>
          </p>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>