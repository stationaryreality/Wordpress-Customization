<?php
/**
 * Template part for displaying search results from a taxonomy
 *
 * Expects:
 * - $info        (array: title, emoji)
 * - $search_term (string)
 * - $taxonomy    (string)
 */

if (!isset($info) || !isset($search_term) || !isset($taxonomy)) {
    return;
}

// Dummy image for now
$dummy_image = wp_get_attachment_image_url(19327, 'thumbnail');

// Use Relevanssi to find posts tagged with terms matching the search term
$query_args = [
    'post_type'      => 'any',
    'posts_per_page' => -1,
    'tax_query'      => [
        [
            'taxonomy' => $taxonomy,
            'field'    => 'name',
            'terms'    => $search_term,
            'operator' => 'LIKE',
        ],
    ],
];

$query = new WP_Query($query_args);

$found_terms = [];
if ($query->have_posts()) :
    while ($query->have_posts()) : $query->the_post();
        $post_terms = wp_get_post_terms(get_the_ID(), $taxonomy);
        foreach ($post_terms as $t) {
            if (stripos($t->name, $search_term) !== false) {
                $found_terms[$t->term_id] = $t;
            }
        }
    endwhile;
    wp_reset_postdata();
endif;

$found_terms = array_values($found_terms);
if (empty($found_terms)) return;

$taxonomy_obj = get_taxonomy($taxonomy);
$label = $taxonomy_obj ? $taxonomy_obj->labels->name : ucfirst($taxonomy);
?>

<section class="search-section search-section-<?php echo esc_attr($taxonomy); ?> container max-w-3xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">
        <?php echo esc_html($info['emoji'] . ' ' . $info['title']); ?>
        <?php if ($search_term) : ?>
            containing “<?php echo esc_html($search_term); ?>”
        <?php endif; ?>
    </h2>

    <div class="taxonomy-list space-y-4">
        <?php foreach ($found_terms as $t) : ?>
            <?php $term_link = get_term_link($t); ?>
            <?php if (is_wp_error($term_link)) continue; ?>
            <div class="taxonomy-entry flex items-center gap-4 border-b pb-2">
                <a href="<?php echo esc_url($term_link); ?>" class="taxonomy-thumb">
                    <img src="<?php echo esc_url($dummy_image); ?>" alt="<?php echo esc_attr($label); ?> thumbnail" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
                </a>
                <div class="taxonomy-text">
                    <a href="<?php echo esc_url($term_link); ?>" class="font-medium text-lg">
                        <?php echo esc_html($t->name); ?>
                    </a>
                    <?php if ($t->description) : ?>
                        <p class="text-gray-600 text-sm">
                            <?php echo esc_html(wp_trim_words($t->description, 20, '...')); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
