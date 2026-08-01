<?php
$current_id = get_the_ID();
$is_rap = has_term('rap', 'song_category', $current_id);

if ($is_rap) {
    $song_ids = get_posts([
        'post_type' => 'song',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'fields' => 'ids',
        'tax_query' => [['taxonomy' => 'song_category', 'field' => 'slug', 'terms' => 'rap']]
    ]);
    $heading_prefix = 'Rap Song';
} else {
    $terms = wp_get_post_terms($current_id, 'feature_level', ['fields' => 'slugs']);
    $tier = $terms[0] ?? '';

    $song_ids = get_posts([
        'post_type' => 'song',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'fields' => 'ids',
        'tax_query' => [
            'relation' => 'AND',
            ['taxonomy' => 'feature_level', 'field' => 'slug', 'terms' => $tier],
            ['taxonomy' => 'song_category', 'field' => 'slug', 'terms' => 'rap', 'operator' => 'NOT IN']
        ]
    ]);

    $heading_prefix = match($tier) {
        'narrative' => 'Narrative Song',
        'featured' => 'Featured Song',
        'referenced' => 'Referenced Song',
        default => 'Song'
    };
}

$current_index = array_search($current_id, $song_ids);
$next_id = $song_ids[$current_index + 1] ?? null;
$prev_id = $song_ids[$current_index - 1] ?? null;
?>

<div class="cpt-song-nav-top">
    <div class="cpt-song-nav-row">
        <?php if ($prev_id): ?>
            <?php $cover = get_field('cover_image', $prev_id); $thumb = $cover ? $cover['sizes']['thumbnail'] : ''; ?>
            <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-song-nav-prev cpt-keyboard-nav-prev">
                <span class="cpt-song-nav-label">← Previous <?php echo esc_html($heading_prefix); ?></span>
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-song-nav-thumb">
                <?php endif; ?>
                <span class="cpt-song-nav-title"><?php echo get_the_title($prev_id); ?></span>
            </a>
        <?php endif; ?>

        <?php if ($prev_id || $next_id): ?>
            <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">Use ← ⌨️ → keys</span>
        <?php endif; ?>

        <?php if ($next_id): ?>
            <?php $cover = get_field('cover_image', $next_id); $thumb = $cover ? $cover['sizes']['thumbnail'] : ''; ?>
            <a href="<?php echo get_permalink($next_id); ?>" class="cpt-song-nav-next cpt-keyboard-nav-next">
                <span class="cpt-song-nav-label">Next <?php echo esc_html($heading_prefix); ?> →</span>
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-song-nav-thumb">
                <?php endif; ?>
                <span class="cpt-song-nav-title"><?php echo get_the_title($next_id); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>