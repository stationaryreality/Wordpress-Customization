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
    // Only rappers
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
    // Non-rapper: anything NOT tagged rapper, filtered by feature_level
    $feature_terms = wp_get_post_terms($current_id, 'feature_level', ['fields' => 'slugs']);
    $feature_slug = !empty($feature_terms) ? $feature_terms[0] : '';

    $args['tax_query'] = [
        'relation' => 'AND',
        [
            'taxonomy' => 'artist_type',
            'field'    => 'slug',
            'terms'    => 'rapper',
            'operator' => 'NOT IN', // key change — allows no artist_type term at all
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

// Get IDs
$artist_ids = get_posts($args);

// Find current index
$current_index = array_search($current_id, $artist_ids);
$next_id = $artist_ids[$current_index + 1] ?? null;
$prev_id = $artist_ids[$current_index - 1] ?? null;
?>

<div class="post-navigation-container people-nav" style="display: flex; justify-content: space-between; gap: 20px; margin-top: 40px;">
  <?php if ($next_id): ?>
    <div class="previous-post">
      <h2>Next <?php echo esc_html($nav_label); ?></h2>
      <a href="<?php echo get_permalink($next_id); ?>">
        <?php
        $portrait = get_field('portrait_image', $next_id);
        if ($portrait) {
          echo '<img src="' . esc_url($portrait['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($next_id)) . '" style="width:100px;height:100px;object-fit:cover;border-radius:50%;margin-bottom:10px;">';
        }
        ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="next-post">
      <h2>Previous <?php echo esc_html($nav_label); ?></h2>
      <a href="<?php echo get_permalink($prev_id); ?>">
        <?php
        $portrait = get_field('portrait_image', $prev_id);
        if ($portrait) {
          echo '<img src="' . esc_url($portrait['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($prev_id)) . '" style="width:100px;height:100px;object-fit:cover;border-radius:50%;margin-bottom:10px;">';
        }
        ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>
