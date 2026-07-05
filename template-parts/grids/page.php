<?php
/* Template Part: Homepage Page Grid */

$title = $args['title'] ?? '';
$pages = $args['pages'] ?? [];

if (empty($pages)) {
    return;
}
?>

<section class="homepage-section">
    
    <?php if ($title) : ?>
        <h2 class="page-section-title">
            <?php echo esc_html($title); ?>
        </h2>
    <?php endif; ?>

    <div class="tag-posts-grid">

        <?php foreach ($pages as $item) :

            $page = get_page_by_path($item['slug']);

            if (!$page) {
                continue;
            }

            setup_postdata($page);
        ?>

            <div class="tag-post-item">

                <a href="<?php echo get_permalink($page->ID); ?>" class="tag-post-thumbnail">

                    <?php if (has_post_thumbnail($page->ID)) : ?>

                        <?php echo get_the_post_thumbnail($page->ID, 'medium'); ?>

                    <?php endif; ?>

                </a>

                <a href="<?php echo get_permalink($page->ID); ?>" class="tag-post-title">
                    <?php echo esc_html($item['title']); ?>
                </a>

                <p class="tag-post-excerpt">
                    <?php echo esc_html($item['description']); ?>
                </p>

            </div>

        <?php endforeach; ?>

        <?php wp_reset_postdata(); ?>

    </div>

</section>