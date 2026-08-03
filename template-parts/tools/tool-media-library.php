<?php
/**
 * Tool: Full Media Library
 * Public visual explorer for attached site images
 */

$paged = max(1, get_query_var('paged') ?: ($_GET['pg'] ?? 1));

$order = isset($_GET['order']) && $_GET['order'] === 'asc'
    ? 'ASC'
    : 'DESC';

$search = isset($_GET['s'])
    ? sanitize_text_field($_GET['s'])
    : '';

$args = [
    'post_type'      => 'attachment',
    'post_status'    => 'inherit',
    'post_mime_type' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ],
    'posts_per_page' => 120,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => $order,

    // ONLY attached images
    'post_parent__not_in' => [0],
];

// Add search if provided
if (!empty($search)) {
    $args['s'] = $search;
}

$media_query = new WP_Query($args);

?>

<section class="admin-tool admin-tool-media-library">

    <header class="tool-header">
        <h2>Full Media Library</h2>

        <p>
            Browse all publicly attached images across the site.
        </p>

        <div class="tool-controls">

            <a href="?tool=media-library&order=desc&s=<?php echo esc_attr($search); ?>"
               class="<?php echo $order === 'DESC' ? 'active' : ''; ?>">
                Newest
            </a>

            <a href="?tool=media-library&order=asc&s=<?php echo esc_attr($search); ?>"
               class="<?php echo $order === 'ASC' ? 'active' : ''; ?>">
                Oldest
            </a>

        </div>

    </header>

    <!-- ====================================================== -->
    <!-- SEARCH BOX -->
    <!-- ====================================================== -->

    <form method="get" class="media-search-form">

        <input type="hidden" name="tool" value="media-library">
        <input type="hidden" name="order" value="<?php echo esc_attr(strtolower($order)); ?>">
        <input type="hidden" name="pg" value="1">

        <input
            type="text"
            name="s"
            value="<?php echo esc_attr($search); ?>"
            placeholder="Search by filename or title..."
            class="media-search-input"
        >

        <button type="submit" class="media-search-button">
            Search
        </button>

        <?php if (!empty($search)): ?>
            <a href="?tool=media-library&order=<?php echo strtolower($order); ?>"
               class="media-search-clear">
                Clear Search
            </a>
        <?php endif; ?>

    </form>

    <?php if ($media_query->have_posts()) : ?>

        <div class="media-library-grid">

            <?php while ($media_query->have_posts()) : $media_query->the_post();

                $attachment_id = get_the_ID();

                $parent_id = wp_get_post_parent_id($attachment_id);

                if (!$parent_id) {
                    continue;
                }

                $parent_post = get_post($parent_id);

                if (!$parent_post || $parent_post->post_status !== 'publish') {
                    continue;
                }

                $thumb = wp_get_attachment_image(
                    $attachment_id,
                    'medium',
                    false,
                    [
                        'loading' => 'lazy',
                    ]
                );

                $full_url = wp_get_attachment_url($attachment_id);

                $filename = basename(get_attached_file($attachment_id));

                $metadata = wp_get_attachment_metadata($attachment_id);

                $width  = $metadata['width'] ?? '';
                $height = $metadata['height'] ?? '';

                ?>

                <article class="media-card">

                    <a href="<?php echo esc_url($full_url); ?>"
                       target="_blank"
                       class="media-thumb">

                        <?php echo $thumb; ?>

                    </a>

                    <div class="media-meta">

                        <div class="media-filename">
                            <?php echo esc_html($filename); ?>
                        </div>

                        <?php if ($width && $height) : ?>

                            <div class="media-dimensions">
                                <?php echo esc_html($width . ' × ' . $height); ?>
                            </div>

                        <?php endif; ?>

                        <div class="media-date">
                            Uploaded:
                            <?php echo esc_html(get_the_date()); ?>
                        </div>

                        <div class="media-parent">

                            Attached to:

                            <a href="<?php echo esc_url(get_permalink($parent_id)); ?>">
                                <?php echo esc_html(get_the_title($parent_id)); ?>
                            </a>

                        </div>

                        <div class="media-parent-type">

                            Type:
                            <?php echo esc_html(get_post_type($parent_id)); ?>

                        </div>

                    </div>

                </article>

            <?php endwhile; ?>

        </div>

        <div class="tool-pagination">

            <?php
            echo paginate_links([
                'total'   => $media_query->max_num_pages,
                'current' => $paged,
                'add_args' => [
                    's' => $search,
                    'order' => strtolower($order),
                ]
            ]);
            ?>

        </div>

        <?php if (!empty($search)): ?>
            <p style="margin-top: 1.5rem; color: #666; font-size: 0.9rem;">
                Showing results for: <strong>"<?php echo esc_html($search); ?>"</strong>
            </p>
        <?php endif; ?>

    <?php else : ?>

        <div class="media-no-results">
            <p>
                <?php if (!empty($search)): ?>
                    No images found matching "<strong><?php echo esc_html($search); ?></strong>".
                <?php else: ?>
                    No attached images found.
                <?php endif; ?>
            </p>
            <?php if (!empty($search)): ?>
                <a href="?tool=media-library">
                    Browse all images
                </a>
            <?php endif; ?>
        </div>

    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</section>