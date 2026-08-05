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

// Helper function to get the image for an organization's nav
function get_organization_nav_image($org_id) {
    $cover = get_field('cover_image', $org_id);
    if ($cover) {
        if (is_array($cover)) return $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
        if (is_numeric($cover)) return wp_get_attachment_image_url($cover, 'thumbnail');
        return $cover;
    }
    if (has_post_thumbnail($org_id)) return get_the_post_thumbnail_url($org_id, 'thumbnail');
    return '';
}

$prev_image = $prev_id ? get_organization_nav_image($prev_id) : '';
$next_image = $next_id ? get_organization_nav_image($next_id) : '';
?>

<div class="cpt-organization-nav-top">
    <div class="cpt-organization-nav-row">
        <?php if ($prev_id): ?>
            <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-organization-nav-prev cpt-keyboard-nav-prev">
                <span class="cpt-organization-nav-label">← Previous Organization</span>
                <?php if ($prev_image): ?>
                    <img src="<?php echo esc_url($prev_image); ?>" alt="" class="cpt-organization-nav-thumb">
                <?php endif; ?>
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
                <?php if ($next_image): ?>
                    <img src="<?php echo esc_url($next_image); ?>" alt="" class="cpt-organization-nav-thumb">
                <?php endif; ?>
                <span class="cpt-organization-nav-title"><?php echo get_the_title($next_id); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>