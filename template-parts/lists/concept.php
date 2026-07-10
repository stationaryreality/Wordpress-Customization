<?php
/**
 * Template Part: Concept List
 * Unified list style (matches lyrics, quotes, references, profiles).
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
$title = $info['title'] ?? $args['title'] ?? 'Concepts';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

// Early bailout if no data source has content
if (
    (!$query instanceof WP_Query || !$query->have_posts())
    && empty($items)
) {
    return;
}
?>

<section class="concept-list-section container" style="max-width:800px;margin:2rem auto;padding:0 1rem;">
  <h1>
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>
    <p class="intro-text">Definitions and explanations of key terms used throughout the site.</p>

  <div class="concept-list">
    <?php if (!empty($items)): ?>
      <?php foreach ($items as $item): ?>
        <?php
          $thumb_url = !empty($item['image']) ? $item['image'] : '';
          $definition = $item['meta'] ?? '';
        ?>
        <div class="concept-entry" style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:2rem;border-bottom:1px solid #ddd;padding-bottom:1rem;">
          <?php if ($thumb_url): ?>
            <a href="<?php echo esc_url($item['url']); ?>" class="concept-thumb">
              <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($item['title']); ?>"
                   style="width:48px;height:48px;object-fit:cover;border-radius:50%;">
            </a>
          <?php endif; ?>

          <div class="concept-text">
            <h2 style="margin-bottom:0.5rem;">
              <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
            </h2>
            <?php if ($definition): ?>
              <p style="margin:0;"><?php echo esc_html(wp_trim_words($definition, 30, '...')); ?></p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <?php while ($query->have_posts()): $query->the_post(); ?>
        <?php
          $definition = get_field('definition', get_the_ID());
          $thumb_url  = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') : '';
        ?>
        <div class="concept-entry" style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:2rem;border-bottom:1px solid #ddd;padding-bottom:1rem;">
          <?php if ($thumb_url): ?>
            <a href="<?php the_permalink(); ?>" class="concept-thumb">
              <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"
                   style="width:48px;height:48px;object-fit:cover;border-radius:50%;">
            </a>
          <?php endif; ?>

          <div class="concept-text">
            <h2 style="margin-bottom:0.5rem;">
              <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
            <?php if ($definition): ?>
              <p style="margin:0;"><?php echo esc_html(wp_trim_words($definition, 30, '...')); ?></p>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
</section>