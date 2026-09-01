<?php
$current_id = get_the_ID();
$lyric_ids = get_posts([
    'post_type'      => 'lyric',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
]);

$current_index = array_search($current_id, $lyric_ids);
$next_id = $lyric_ids[$current_index + 1] ?? null;
$prev_id = $lyric_ids[$current_index - 1] ?? null;

// Helper function to get the image for a lyric's nav
function get_lyric_nav_image($lyric_id) {
    $song = get_field('song', $lyric_id);
    if ($song) {
        if (is_array($song)) $song = reset($song);
        $song_id = is_object($song) ? $song->ID : $song;
        
        $cover = get_field('cover_image', $song_id);
        if ($cover) {
            if (is_array($cover)) return $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
            if (is_numeric($cover)) return wp_get_attachment_image_url($cover, 'thumbnail');
            return $cover;
        }
        if (has_post_thumbnail($song_id)) return get_the_post_thumbnail_url($song_id, 'thumbnail');
    }
    if (has_post_thumbnail($lyric_id)) return get_the_post_thumbnail_url($lyric_id, 'thumbnail');
    return '';
}

$prev_image = $prev_id ? get_lyric_nav_image($prev_id) : '';
$next_image = $next_id ? get_lyric_nav_image($next_id) : '';
?>

<div class="cpt-lyric-nav-top">
    <div class="cpt-lyric-nav-row">
        <?php if ($prev_id): ?>
            <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-lyric-nav-prev cpt-keyboard-nav-prev">
                <span class="cpt-lyric-nav-label">← Previous Lyric</span>
                <?php if ($prev_image): ?>
                    <img src="<?php echo esc_url($prev_image); ?>" alt="" class="cpt-lyric-nav-thumb">
                <?php endif; ?>
                <span class="cpt-lyric-nav-title"><?php echo get_the_title($prev_id); ?></span>
            </a>
        <?php endif; ?>

        <?php if ($prev_id || $next_id): ?>
            <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
                Use ← ⌨️ → keys
            </span>
        <?php endif; ?>

        <?php if ($next_id): ?>
            <a href="<?php echo get_permalink($next_id); ?>" class="cpt-lyric-nav-next cpt-keyboard-nav-next">
                <span class="cpt-lyric-nav-label">Next Lyric →</span>
                <?php if ($next_image): ?>
                    <img src="<?php echo esc_url($next_image); ?>" alt="" class="cpt-lyric-nav-thumb">
                <?php endif; ?>
                <span class="cpt-lyric-nav-title"><?php echo get_the_title($next_id); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>