<?php
/**
 * Template Part: Image Grid (Grouped by Media Type)
 *
 * Supports:
 * 1. $query (WP_Query) – legacy
 * 2. $items (array of normalized cards)
 * 
 * Groups images by media_type taxonomy, sorted alphabetically
 * with "Unsorted" items at the bottom
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
        
        // Get media_type taxonomy terms
        $media_types = wp_get_post_terms(get_the_ID(), 'media_type', ['fields' => 'names']);
        $media_type = !empty($media_types) ? $media_types[0] : 'Unsorted';
        
        $items[] = [
            'title'      => get_the_title(),
            'url'        => get_permalink(),
            'image'      => $img_url,
            'caption'    => get_field('image_caption'),
            'media_type' => $media_type,
        ];
    }
    wp_reset_postdata();
}

if (empty($items)) {
    return;
}

// Group items by media_type
$grouped = [];
foreach ($items as $item) {
    $type = $item['media_type'] ?? 'Unsorted';
    if (!isset($grouped[$type])) {
        $grouped[$type] = [];
    }
    $grouped[$type][] = $item;
}

// Sort groups alphabetically, but keep "Unsorted" at the bottom
$unsorted = isset($grouped['Unsorted']) ? $grouped['Unsorted'] : [];
unset($grouped['Unsorted']);
ksort($grouped); // Sort alphabetically by key

// Add Unsorted back at the end if it exists
if (!empty($unsorted)) {
    $grouped['Unsorted'] = $unsorted;
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

  <?php foreach ($grouped as $group_name => $group_items): ?>
    <div class="img-grid-group">
      <h3 class="img-grid-group-title"><?php echo esc_html($group_name); ?></h3>
      
      <div class="square-grid">
        <?php foreach ($group_items as $item): ?>
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
            <h4 class="square-card-title">
              <a href="<?php echo esc_url($item['url']); ?>">
                <?php echo esc_html($item['title']); ?>
              </a>
            </h4>
            <?php if (!empty($item['caption'])): ?>
              <p class="square-card-caption">
                <?php echo esc_html(wp_trim_words($item['caption'], 20)); ?>
              </p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</section>