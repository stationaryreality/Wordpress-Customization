<?php
$ids = get_posts([
    'post_type'        => 'chapter',
    'posts_per_page'   => -1,
    'orderby'          => 'menu_order',
    'order'            => 'ASC',
    'suppress_filters' => false,
    'fields'           => 'ids',
]);

$current_id    = get_the_ID();
$current_index = array_search($current_id, $ids);

$prev_id = $ids[$current_index - 1] ?? null;
$next_id = $ids[$current_index + 1] ?? null;
?>

<div class="cpt-chapter-nav-top">
    <div class="cpt-chapter-nav-row">

        <?php if ($prev_id): ?>
            <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-chapter-nav-prev cpt-keyboard-nav-prev">
                <span class="cpt-chapter-nav-label">← Previous Chapter</span>
                <?php if (has_post_thumbnail($prev_id)): ?>
                    <?php echo get_the_post_thumbnail($prev_id, 'medium', ['class' => 'cpt-chapter-nav-thumb']); ?>
                <?php endif; ?>
                <span class="cpt-chapter-nav-title"><?php echo get_the_title($prev_id); ?></span>
            </a>
        <?php endif; ?>

        <?php if ($prev_id || $next_id): ?>
            <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">Use ← ⌨️ → keys</span>
        <?php endif; ?>

        <?php if ($next_id): ?>
            <a href="<?php echo get_permalink($next_id); ?>" class="cpt-chapter-nav-next cpt-keyboard-nav-next">
                <span class="cpt-chapter-nav-label">Next Chapter →</span>
                <?php if (has_post_thumbnail($next_id)): ?>
                    <?php echo get_the_post_thumbnail($next_id, 'medium', ['class' => 'cpt-chapter-nav-thumb']); ?>
                <?php endif; ?>
                <span class="cpt-chapter-nav-title"><?php echo get_the_title($next_id); ?></span>
            </a>
        <?php endif; ?>

    </div>
</div>