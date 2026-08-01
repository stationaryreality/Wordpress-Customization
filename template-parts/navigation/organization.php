<?php
$current_id = get_the_ID();
$org_ids = get_posts([
    'post_type'      => 'organization',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
]);

$current_index = array_search($current_id, $org_ids);
$next_id = $org_ids[$current_index + 1] ?? null;
$prev_id = $org_ids[$current_index - 1] ?? null;
?>

<div class="cpt-organization-nav-top">
    <div class="cpt-organization-nav-row">
        <?php if ($prev_id): ?>
            <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-organization-nav-prev cpt-keyboard-nav-prev">
                <span class="cpt-organization-nav-label">← Previous Organization</span>
                <span class="cpt-organization-nav-title"><?php echo get_the_title($prev_id); ?></span>
            </a>
        <?php endif; ?>

        <?php if ($prev_id || $next_id): ?>
            <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
                Use ← ⌨️ → keys
            </span>
        <?php endif; ?>

        <?php if ($next_id): ?>
            <a href="<?php echo get_permalink($next_id); ?>" class="cpt-organization-nav-next cpt-keyboard-nav-next">
                <span class="cpt-organization-nav-label">Next Organization →</span>
                <span class="cpt-organization-nav-title"><?php echo get_the_title($next_id); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>