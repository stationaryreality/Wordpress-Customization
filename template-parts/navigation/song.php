<?php
$current_id = get_the_ID();

// Check if this is a rap song
$is_rap = has_term( 'rap', 'song_category', $current_id );

// If rap, get all rap songs (regardless of tier)
if ( $is_rap ) {
    $song_ids = get_posts([
        'post_type' => 'song',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'fields' => 'ids',
        'tax_query' => [
            [
                'taxonomy' => 'song_category',
                'field'    => 'slug',
                'terms'    => 'rap',
            ]
        ],
    ]);

    $heading_prefix = 'Rap Song';

} else {
    // Non-rap: find this song's feature level
    $terms = wp_get_post_terms( $current_id, 'feature_level', ['fields' => 'slugs'] );
    $tier = $terms[0] ?? '';

    // Query only songs in the same tier and NOT rap
    $song_ids = get_posts([
        'post_type' => 'song',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'fields' => 'ids',
        'tax_query' => [
            'relation' => 'AND',
            [
                'taxonomy' => 'feature_level',
                'field'    => 'slug',
                'terms'    => $tier,
            ],
            [
                'taxonomy' => 'song_category',
                'field'    => 'slug',
                'terms'    => 'rap',
                'operator' => 'NOT IN',
            ]
        ],
    ]);

    // Set heading prefix based on tier
    switch ( $tier ) {
        case 'narrative':
            $heading_prefix = 'Narrative Song';
            break;
        case 'featured':
            $heading_prefix = 'Featured Song';
            break;
        case 'referenced':
            $heading_prefix = 'Referenced Song';
            break;
        default:
            $heading_prefix = 'Song';
            break;
    }
}

$current_index = array_search($current_id, $song_ids);
$next_id = $song_ids[$current_index + 1] ?? null;
$prev_id = $song_ids[$current_index - 1] ?? null;
?>

<div class="post-navigation-container song-nav" style="display: flex; justify-content: center; gap: 60px; margin-top: 60px;">
  <?php if ($next_id): ?>
    <div class="previous-post" style="text-align: center;">
      <h2>Next <?php echo esc_html($heading_prefix); ?></h2>
      <a href="<?php echo get_permalink($next_id); ?>" style="display: inline-block;">
        <?php
        $cover = get_field('cover_image', $next_id);
        if ($cover) {
          echo '<img src="' . esc_url($cover['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($next_id)) . '" style="width:100px;height:100px;object-fit:cover;border-radius:0;margin-bottom:10px;display:block;margin:0 auto;">';
        }
        ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="next-post" style="text-align: center;">
      <h2>Previous <?php echo esc_html($heading_prefix); ?></h2>
      <a href="<?php echo get_permalink($prev_id); ?>" style="display: inline-block;">
        <?php
        $cover = get_field('cover_image', $prev_id);
        if ($cover) {
          echo '<img src="' . esc_url($cover['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($prev_id)) . '" style="width:100px;height:100px;object-fit:cover;border-radius:0;margin-bottom:10px;display:block;margin:0 auto;">';
        }
        ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>
