<?php
/**
 * Template Part: Element Grid (Grouped by Strand Type)
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

        // Cached term lookup (no N+1)
        $terms = get_the_terms(get_the_ID(), 'strand_type');
        $strand_type = (!is_wp_error($terms) && !empty($terms)) ? $terms[0]->name : 'Unsorted';

        $items[] = [
            'title'       => get_the_title(),
            'url'         => get_permalink(),
            'image'       => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
            'strand_type' => $strand_type,
        ];
    }
    wp_reset_postdata();
}

// No data = bail
if (empty($items)) {
    return;
}

// Group and sort alphabetically, keeping 'Unsorted' at the very end
$grouped = [];
foreach ($items as $item) {
    $type = $item['strand_type'] ?? 'Unsorted';
    if (!isset($grouped[$type])) {
        $grouped[$type] = [];
    }
    $grouped[$type][] = $item;
}

$unsorted = isset($grouped['Unsorted']) ? $grouped['Unsorted'] : [];
unset($grouped['Unsorted']);
ksort($grouped);

if (!empty($unsorted)) {
    $grouped['Unsorted'] = $unsorted;
}
?>

<section class="cpt-element-section">
  <h2>
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      <span>containing “<?php echo esc_html($search_term); ?>”</span>
    <?php endif; ?>
  </h2>

  <?php foreach ($grouped as $group_name => $group_items): ?>
    <div class="cpt-element-group">
      <h3 class="cpt-element-group-title"><?php echo esc_html($group_name); ?></h3>

      <div class="cpt-element-grid">
        <?php foreach ($group_items as $item): ?>
          <div class="cpt-element-item">
            <a href="<?php echo esc_url($item['url']); ?>" class="cpt-element-link">
              <?php if (!empty($item['image'])): ?>
                <img 
                  src="<?php echo esc_url($item['image']); ?>" 
                  alt="<?php echo esc_attr($item['title']); ?>"
                  class="cpt-element-image"
                >
              <?php endif; ?>
            </a>
            <h4 class="cpt-element-title">
              <a href="<?php echo esc_url($item['url']); ?>">
                <?php echo esc_html($item['title']); ?>
              </a>
            </h4>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</section>