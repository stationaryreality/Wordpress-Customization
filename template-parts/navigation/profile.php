<?php
$current_id = get_the_ID();
$people_ids = get_posts([
    'post_type'      => 'profile',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
]);

$current_index = array_search($current_id, $people_ids);
$next_id = $people_ids[$current_index + 1] ?? null;
$prev_id = $people_ids[$current_index - 1] ?? null;
?>

<div class="cpt-profile-nav-top">
    <div class="cpt-profile-nav-row">
        <?php if ($prev_id): ?>
            <?php $portrait = get_field('portrait_image', $prev_id); $thumb = $portrait ? $portrait['sizes']['thumbnail'] : ''; ?>
            <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-profile-nav-prev cpt-keyboard-nav-prev">
                <span class="cpt-profile-nav-label">← Previous Person</span>
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-profile-nav-thumb">
                <?php endif; ?>
                <span class="cpt-profile-nav-title"><?php echo get_the_title($prev_id); ?></span>
            </a>
        <?php endif; ?>

        <?php if ($prev_id || $next_id): ?>
            <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">Use ← ⌨️ → keys</span>
        <?php endif; ?>

        <?php if ($next_id): ?>
            <?php $portrait = get_field('portrait_image', $next_id); $thumb = $portrait ? $portrait['sizes']['thumbnail'] : ''; ?>
            <a href="<?php echo get_permalink($next_id); ?>" class="cpt-profile-nav-next cpt-keyboard-nav-next">
                <span class="cpt-profile-nav-label">Next Person →</span>
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-profile-nav-thumb">
                <?php endif; ?>
                <span class="cpt-profile-nav-title"><?php echo get_the_title($next_id); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>