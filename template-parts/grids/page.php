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
            
            // Get the excerpt from the database, fallback to the PHP array description
            $excerpt = get_the_excerpt($page->ID);
            if ( empty($excerpt) ) {
                $excerpt = $item['description'];
            }
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
    <?php 
    $raw_excerpt = get_the_excerpt($page->ID);
    if ( empty($raw_excerpt) ) {
        $raw_excerpt = $item['description'];
    }
    // Strip any rogue HTML tags injected by other plugins
    echo esc_html( wp_strip_all_tags($raw_excerpt) ); 
    ?>
</p>

            </div>

        <?php endforeach; ?>

        <?php wp_reset_postdata(); ?>

    </div>

</section>