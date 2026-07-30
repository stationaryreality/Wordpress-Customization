<?php
$current_id = get_the_ID();
$artist_type_terms = wp_get_post_terms($current_id, 'artist_type', ['fields' => 'slugs']);
$artist_type = !empty($artist_type_terms) ? $artist_type_terms[0] : '';

$args = [
    'post_type' => 'artist',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'fields' => 'ids',
];

if ($artist_type === 'rapper') {
    $args['tax_query'] = [ [ 'taxonomy' => 'artist_type', 'field' => 'slug', 'terms' => 'rapper', 'operator' => 'IN' ] ];
    $nav_label = 'Rap Artist';
} else {
    $feature_terms = wp_get_post_terms($current_id, 'feature_level', ['fields' => 'slugs']);
    $feature_slug = !empty($feature_terms) ? $feature_terms[0] : '';
    $args['tax_query'] = [
        'relation' => 'AND',
        [ 'taxonomy' => 'artist_type', 'field' => 'slug', 'terms' => 'rapper', 'operator' => 'NOT IN' ],
        [ 'taxonomy' => 'feature_level', 'field' => 'slug', 'terms' => $feature_slug, 'operator' => 'IN' ],
    ];
    $nav_label = ucfirst($feature_slug) . ' Artist';
}

$artist_ids = get_posts($args);
$current_index = array_search($current_id, $artist_ids);
$next_id = $artist_ids[$current_index + 1] ?? null;
$prev_id = $artist_ids[$current_index - 1] ?? null;
?>

<div class="cpt-artist-nav">
  <?php if ($next_id): ?>
    <div class="cpt-artist-nav-next">
      <h2>Next <?php echo esc_html($nav_label); ?></h2>
      <a href="<?php echo get_permalink($next_id); ?>">
        <?php
        $portrait = get_field('portrait_image', $next_id);
        if ($portrait) {
          echo '<img src="' . esc_url($portrait['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($next_id)) . '" class="cpt-artist-nav-img">';
        }
        ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="cpt-artist-nav-prev">
      <h2>Previous <?php echo esc_html($nav_label); ?></h2>
      <a href="<?php echo get_permalink($prev_id); ?>">
        <?php
        $portrait = get_field('portrait_image', $prev_id);
        if ($portrait) {
          echo '<img src="' . esc_url($portrait['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($prev_id)) . '" class="cpt-artist-nav-img">';
        }
        ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>