<?php
$current_id = get_the_ID();

// 1. Find what artist_type(s) this artist belongs to
$type_terms = wp_get_post_terms($current_id, 'artist_type', ['fields' => 'ids']);

$args = [
    'post_type'      => 'artist',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
];

// 2. If they have a type, restrict the query to that type
if (!empty($type_terms)) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'artist_type',
            'field'    => 'term_id',
            'terms'    => $type_terms,
        ]
    ];
    $first_term = get_term($type_terms[0], 'artist_type');
    $nav_label = $first_term ? ucfirst($first_term->name) . ' Artist' : 'Artist';
} else {
    $nav_label = 'Artist';
}

$artist_ids = get_posts($args);

// 3. Find current position and get next/prev
$current_index = array_search($current_id, $artist_ids);
if ($current_index !== false) {
    $next_id = $artist_ids[$current_index + 1] ?? null;
    $prev_id = $artist_ids[$current_index - 1] ?? null;
} else {
    $next_id = null;
    $prev_id = null;
}
?>

<div class="cpt-artist-nav-top">
  <div class="cpt-artist-nav-row">
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

    <?php if ($prev_id || $next_id): ?>
      <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
        Use ← ⌨️ → keys
      </span>
    <?php endif; ?>

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
  </div>
</div>