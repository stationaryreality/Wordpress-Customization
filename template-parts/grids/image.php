<?php
/**
 * Image Grid Template
 *
 * Supports two modes:
 *
 * 1. Query Mode (WP_Query)
 * 2. Normalized Cards Mode ($items)
 *
 * Standardized contract:
 * - items       => array
 * - query       => WP_Query|null
 * - info        => [ 'title' => '', 'emoji' => '', 'type' => '' ]
 * - search_term => string
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

// Backward compatibility: allow direct title/emoji from older callers
$title = $info['title'] ?? $args['title'] ?? 'Images';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

// Early bailout if no data source has content
if (
    (!$query instanceof WP_Query || !$query->have_posts())
    && empty($items)
) {
    return;
}
?>

<section style="margin-bottom:4rem;">
  <h2>
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      <span style="font-weight:normal;font-size:0.9em;color:#666;">
        containing “<?php echo esc_html($search_term); ?>”
      </span>
    <?php endif; ?>
  </h2>

  <div class="cited-grid">
    <?php if (!empty($items)): ?>
      <?php foreach ($items as $item): ?>
        <?php
          $caption = $item['meta'] ?? '';
          $img_url = !empty($item['image']) ? $item['image'] : '';
        ?>
        <div class="cited-item">
          <a href="<?php echo esc_url($item['url']); ?>">
            <?php if ($img_url): ?>
              <img src="<?php echo esc_url($img_url); ?>"
                   alt="<?php echo esc_attr($item['title']); ?>"
                   style="width:150px; height:150px; object-fit:cover;">
            <?php endif; ?>
            <h3><?php echo esc_html($item['title']); ?></h3>
          </a>
          <?php if ($caption): ?>
            <p style="margin:0.5rem 0 0;font-size:0.9em;color:#555;">
              <?php echo esc_html(wp_trim_words($caption, 20)); ?>
            </p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <?php while ($query->have_posts()): $query->the_post(); ?>
        <?php
          $caption = get_field('image_caption');
          $image   = get_field('image_file');
          // Use ACF size for uniform dimensions; fallback to featured
          $img_url = $image ? $image['sizes']['medium'] : get_the_post_thumbnail_url(get_the_ID(), 'medium');
        ?>
        <div class="cited-item">
          <a href="<?php the_permalink(); ?>">
            <?php if ($img_url): ?>
              <img src="<?php echo esc_url($img_url); ?>"
                   alt="<?php the_title(); ?>"
                   style="width:150px; height:150px; object-fit:cover;">
            <?php endif; ?>
            <h3><?php the_title(); ?></h3>
          </a>
          <?php if ($caption): ?>
            <p style="margin:0.5rem 0 0;font-size:0.9em;color:#555;">
              <?php echo esc_html(wp_trim_words($caption, 20)); ?>
            </p>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
</section>