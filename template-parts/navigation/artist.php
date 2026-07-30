<?php
$current_id = get_the_ID();

// Get artist type
$artist_type_terms = wp_get_post_terms($current_id, 'artist_type', ['fields' => 'slugs']);
$artist_type = !empty($artist_type_terms) ? $artist_type_terms[0] : '';

// Prepare base query
$args = [
    'post_type'      => 'artist',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
];

if ($artist_type === 'rapper') {
    $args['tax_query'] = [
        [
            'taxonomy' => 'artist_type',
            'field'    => 'slug',
            'terms'    => 'rapper',
            'operator' => 'IN',
        ],
    ];
    $nav_label = 'Rap Artist';
} else {
    $feature_terms = wp_get_post_terms($current_id, 'feature_level', ['fields' => 'slugs']);
    $feature_slug = !empty($feature_terms) ? $feature_terms[0] : '';

    $args['tax_query'] = [
        'relation' => 'AND',
        [
            'taxonomy' => 'artist_type',
            'field'    => 'slug',
            'terms'    => 'rapper',
            'operator' => 'NOT IN',
        ],
        [
            'taxonomy' => 'feature_level',
            'field'    => 'slug',
            'terms'    => $feature_slug,
            'operator' => 'IN',
        ],
    ];

    $nav_label = ucfirst($feature_slug) . ' Artist';
}

$artist_ids = get_posts($args);
$current_index = array_search($current_id, $artist_ids);
$next_id = $artist_ids[$current_index + 1] ?? null;
$prev_id = $artist_ids[$current_index - 1] ?? null;
?>

<div class="cpt-artist-nav-top">
  <div class="cpt-artist-nav-row">
    <?php if ($next_id): ?>
      <?php
      $portrait = get_field('portrait_image', $next_id);
      $thumb_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
      ?>
      <a href="<?php echo get_permalink($next_id); ?>" class="cpt-artist-nav-next cpt-keyboard-nav-next">
        <span class="cpt-artist-nav-label">Next <?php echo esc_html($nav_label); ?> →</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-artist-nav-thumb">
        <?php endif; ?>
        <span class="cpt-artist-nav-title"><?php echo get_the_title($next_id); ?></span>
      </a>
    <?php endif; ?>

    <?php if ($prev_id || $next_id): ?>
      <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
        Use ← ⌨️ → keys
      </span>
    <?php endif; ?>

    <?php if ($prev_id): ?>
      <?php
      $portrait = get_field('portrait_image', $prev_id);
      $thumb_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
      ?>
      <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-artist-nav-prev cpt-keyboard-nav-prev">
        <span class="cpt-artist-nav-label">← Previous <?php echo esc_html($nav_label); ?></span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-artist-nav-thumb">
        <?php endif; ?>
        <span class="cpt-artist-nav-title"><?php echo get_the_title($prev_id); ?></span>
      </a>
    <?php endif; ?>
  </div>
</div>