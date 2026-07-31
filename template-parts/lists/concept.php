<?php
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'Concepts';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

if ( (!$query instanceof WP_Query || !$query->have_posts()) && empty($items) ) {
    return;
}
?>

<section class="cpt-concept-list-section">
  <h1>
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>
  <p class="intro-text">Definitions and explanations of key terms used throughout the site.</p>

  <div class="cpt-concept-list">
    <?php if (!empty($items)): ?>
      <?php foreach ($items as $item): ?>
        <?php
          $thumb_url = !empty($item['image']) ? $item['image'] : '';
          $definition = !empty($item['excerpt']) ? $item['excerpt'] : '';
        ?>
        <div class="cpt-concept-entry">
          <?php if ($thumb_url): ?>
            <a href="<?php echo esc_url($item['url']); ?>" class="cpt-concept-thumb">
              <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($item['title']); ?>">
            </a>
          <?php endif; ?>

          <div class="cpt-concept-text">
            <h2><a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a></h2>
            <?php if ($definition): ?>
              <p class="cpt-concept-definition"><?php echo esc_html(wp_trim_words($definition, 30, '...')); ?></p>
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
        <div class="cpt-concept-entry">
          <?php if ($thumb_url): ?>
            <a href="<?php the_permalink(); ?>" class="cpt-concept-thumb">
              <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
            </a>
          <?php endif; ?>

          <div class="cpt-concept-text">
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <?php if ($definition): ?>
              <p class="cpt-concept-definition"><?php echo esc_html(wp_trim_words($definition, 30, '...')); ?></p>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
</section>