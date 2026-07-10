<?php
/**
 * Movie Grid Template
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
$title = $info['title'] ?? $args['title'] ?? 'Movies';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

// Early bailout
if (
    (!$query instanceof WP_Query || !$query->have_posts())
    && empty($items)
) {
    return;
}
?>

<section class="cited-grid-wrapper cited-grid-wrapper--movies">
  <h1 class="cited-grid__heading">
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>

  <div class="cited-grid cited-grid--movies">
    <?php if (!empty($items)): ?>
      <?php foreach ($items as $item): ?>
        <?php
          $director = $item['meta']['director'] ?? '';
          $summary  = $item['excerpt'] ?? '';
          $img_url  = !empty($item['image']) ? $item['image'] : '';
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

          <?php if ($director): ?>
            <p class="cited-item__meta"><strong><?php echo esc_html($director); ?></strong></p>
          <?php endif; ?>

          <?php if ($summary): ?>
            <p class="cited-item__excerpt"><?php echo esc_html(wp_trim_words($summary, 25)); ?></p>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <?php while ($query->have_posts()): $query->the_post(); ?>
        <?php
          $director = get_field('director');
          $summary  = get_field('summary');
          $cover    = get_field('cover_image');
          $img_url  = $cover ? $cover['sizes']['medium'] : '';
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('cited-item'); ?>>
          <a class="cited-item__link" href="<?php the_permalink(); ?>">
            <?php if ($img_url): ?>
              <div class="cited-item__thumb" aria-hidden="true">
                <img src="<?php echo esc_url($img_url); ?>"
                     alt="<?php echo esc_attr(get_the_title()); ?>"
                     loading="lazy"
                     decoding="async" />
              </div>
            <?php endif; ?>

            <h3 class="cited-item__title"><?php the_title(); ?></h3>
          </a>

          <?php if ($director): ?>
            <p class="cited-item__meta"><strong><?php echo esc_html($director); ?></strong></p>
          <?php endif; ?>

          <?php if ($summary): ?>
            <p class="cited-item__excerpt"><?php echo esc_html(wp_trim_words($summary, 25)); ?></p>
          <?php endif; ?>
        </article>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
</section>