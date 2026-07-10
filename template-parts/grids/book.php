<?php
/**
 * Book Grid Template
 *
 * Supports two modes:
 *
 * 1. Search Mode
 *    - receives a WP_Query
 *
 * 2. Knowledge Mode
 *    - receives normalized card arrays
 */

$query = $args['query'] ?? null;
$items = $args['items'] ?? [];

$section_title = $args['title'] ?? '';
$emoji         = $args['emoji'] ?? '';
$search_term   = $args['search_term'] ?? '';

/*
|--------------------------------------------------------------------------
| Nothing to render
|--------------------------------------------------------------------------
*/

if (
    (!$query instanceof WP_Query || !$query->have_posts())
    && empty($items)
) {
    return;
}
?>

<section class="cpt-section book-grid" style="margin-bottom:4rem;">

<?php if ($section_title): ?>

<h2>

<?php echo esc_html(trim($emoji . ' ' . $section_title)); ?>

<?php if ($search_term): ?>

containing “<?php echo esc_html($search_term); ?>”

<?php endif; ?>

</h2>

<?php endif; ?>

<div class="cited-grid">

<?php if (!empty($items)): ?>

    <?php foreach ($items as $item): ?>

        <div class="cited-item">

            <a href="<?php echo esc_url($item['url']); ?>">

                <?php if (!empty($item['image'])): ?>

                    <img
                        src="<?php echo esc_url($item['image']); ?>"
                        alt="<?php echo esc_attr($item['title']); ?>">

                <?php endif; ?>

                <h3><?php echo esc_html($item['title']); ?></h3>

            </a>

            <?php if (!empty($item['meta'])): ?>

                <p><strong><?php echo esc_html($item['meta']); ?></strong></p>

            <?php endif; ?>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <?php while ($query->have_posts()): $query->the_post();

        $author = get_field('author');

        $cover = get_field('cover_image');

        $img_url = $cover
            ? $cover['sizes']['medium']
            : '';

    ?>

        <div class="cited-item">

            <a href="<?php the_permalink(); ?>">

                <?php if ($img_url): ?>

                    <img
                        src="<?php echo esc_url($img_url); ?>"
                        alt="<?php the_title_attribute(); ?>">

                <?php endif; ?>

                <h3><?php the_title(); ?></h3>

            </a>

            <?php if ($author): ?>

                <p><strong><?php echo esc_html($author); ?></strong></p>

            <?php endif; ?>

        </div>

    <?php endwhile; ?>

    <?php wp_reset_postdata(); ?>

<?php endif; ?>

</div>

</section>