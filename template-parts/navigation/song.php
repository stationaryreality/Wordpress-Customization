<?php
$current_id = get_the_ID();

// 1. Find what song_group(s) this song belongs to
$group_terms = wp_get_post_terms($current_id, 'song_group', ['fields' => 'ids']);

$args = [
    'post_type'      => 'song',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
];

// 2. If it has a group, restrict the query to that group
if (!empty($group_terms)) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'song_group',
            'field'    => 'term_id',
            'terms'    => $group_terms,
        ]
    ];
    $first_term = get_term($group_terms[0], 'song_group');
    $heading_prefix = $first_term ? $first_term->name . ' Song' : 'Song';
} else {
    // Fallback for ungrouped songs
    $heading_prefix = 'Song';
}

$song_ids = get_posts($args);

// 3. Find current position and get next/prev
$current_index = array_search($current_id, $song_ids);
if ($current_index !== false) {
    $next_id = $song_ids[$current_index + 1] ?? null;
    $prev_id = $song_ids[$current_index - 1] ?? null;
} else {
    $next_id = null;
    $prev_id = null;
}
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