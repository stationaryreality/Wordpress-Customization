<?php
/**
 * Organization Grid Template
 *
 * Supports two modes:
 *
 * 1. Query Mode (WP_Query)
 * 2. Normalized Cards Mode ($items)
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$section_title = $args['title'] ?? '';
$emoji        = $args['emoji'] ?? '';
$search_term  = $args['search_term'] ?? '';

$title = $section_title ?: 'Organizations';

// Early bailout if no data source has content
if (
    (!$query instanceof WP_Query || !$query->have_posts())
    && empty($items)
) {
    return;
}
?>

<section class="cited-grid-wrapper cited-grid-wrapper--organizations">
  <h1 class="cited-grid__heading">
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>

  <div class="cited-grid cited-grid--organizations">
    <?php if (!empty($items)): ?>
      <?php foreach ($items as $item): ?>
        <?php
          $bio = $item['meta'] ?? '';
          $img_url = !empty($item['image']) ? $item['image'] : '';
        ?>
        <article class="cited-item">
          <a class="cited-item__link" href="<?php echo esc_url($item['url']); ?>">
            <?php if ($img_url): ?>
              <div class="cited-item__thumb" aria-hidden="true">
                <img src="<?php echo esc_url($img_url); ?>"
                     alt="<?php echo esc_attr($item['title']); ?>"
                     loading="lazy"
                     decoding="async"
                     style="aspect-ratio:1/1; object-fit:cover;" />
              </div>
            <?php endif; ?>

            <h3 class="cited-item__title"><?php echo esc_html($item['title']); ?></h3>
          </a>

          <?php if ($bio): ?>
            <p class="cited-item__meta"><?php echo esc_html(wp_trim_words($bio, 20)); ?></p>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <?php while ($query->have_posts()): $query->the_post(); ?>
        <?php
          $bio    = get_field('org_bio');
          $cover  = get_field('cover_image');
          $img_url = $cover ? $cover['sizes']['thumbnail'] : '';
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('cited-item'); ?>>
          <a class="cited-item__link" href="<?php the_permalink(); ?>">
            <?php if ($img_url): ?>
              <div class="cited-item__thumb" aria-hidden="true">
                <img src="<?php echo esc_url($img_url); ?>"
                     alt="<?php echo esc_attr(get_the_title()); ?>"
                     loading="lazy"
                     decoding="async"
                     style="aspect-ratio:1/1; object-fit:cover;" />
              </div>
            <?php endif; ?>

            <h3 class="cited-item__title"><?php the_title(); ?></h3>
          </a>

          <?php if ($bio): ?>
            <p class="cited-item__meta"><?php echo esc_html(wp_trim_words($bio, 20)); ?></p>
          <?php endif; ?>
        </article>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
</section>