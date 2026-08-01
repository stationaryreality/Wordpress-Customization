<?php
$current_id = get_the_ID();
$video_ids = get_posts([
    'post_type'      => 'video',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
]);

$current_index = array_search($current_id, $video_ids);
$next_id = $video_ids[$current_index + 1] ?? null;
$prev_id = $video_ids[$current_index - 1] ?? null;
?>

<div class="cpt-video-nav-top">
    <div class="cpt-video-nav-row">
        <?php if ($prev_id): ?>
            <?php $cover = get_field('video_screenshot', $prev_id); $thumb = $cover ? $cover['sizes']['medium'] : ''; ?>
            <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-video-nav-prev cpt-keyboard-nav-prev">
                <span class="cpt-video-nav-label">← Previous Video</span>
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-video-nav-thumb">
                <?php endif; ?>
                <span class="cpt-video-nav-title"><?php echo get_the_title($prev_id); ?></span>
            </a>
        <?php endif; ?>

        <?php if ($prev_id || $next_id): ?>
            <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">Use ← ⌨️ → keys</span>
        <?php endif; ?>

        <?php if ($next_id): ?>
            <?php $cover = get_field('video_screenshot', $next_id); $thumb = $cover ? $cover['sizes']['medium'] : ''; ?>
            <a href="<?php echo get_permalink($next_id); ?>" class="cpt-video-nav-next cpt-keyboard-nav-next">
                <span class="cpt-video-nav-label">Next Video →</span>
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-video-nav-thumb">
                <?php endif; ?>
                <span class="cpt-video-nav-title"><?php echo get_the_title($next_id); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>