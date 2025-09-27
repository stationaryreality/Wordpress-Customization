<?php
/**
 * Template part for displaying search results from the "theme" taxonomy
 *
 * Expects:
 * - $info        (array: title, emoji)
 * - $search_term (string)
 */

if (!isset($info) || !isset($search_term)) {
    return;
}

// Dummy image for now
$dummy_image = wp_get_attachment_image_url(19327, 'thumbnail');

// Use Relevanssi to find posts tagged with themes matching the search term
$query_args = [
    'post_type'      => 'any',
    'posts_per_page' => -1,
    'tax_query'      => [
        [
            'taxonomy' => 'theme',
            'field'    => 'name',
            'terms'    => $search_term,
            'operator' => 'LIKE', // Relevanssi can handle partial matches
        ],
    ],
];

$query = new WP_Query($query_args);

// Collect matching themes from the found posts
$themes = [];
if ($query->have_posts()) :
    while ($query->have_posts()) : $query->the_post();
        $post_themes = wp_get_post_terms(get_the_ID(), 'theme');
        foreach ($post_themes as $theme) {
            // only add if it matches search term (partial)
            if (stripos($theme->name, $search_term) !== false) {
                $themes[$theme->term_id] = $theme;
            }
        }
    endwhile;
    wp_reset_postdata();
endif;

// Convert to array
$themes = array_values($themes);

if (empty($themes)) return;
?>

<section class="search-section search-section-theme container max-w-3xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">
        <?php echo esc_html($info['emoji'] . ' ' . $info['title']); ?>
        <?php if ($search_term) : ?>
            containing “<?php echo esc_html($search_term); ?>”
        <?php endif; ?>
    </h2>

    <div class="theme-list space-y-4">
        <?php foreach ($themes as $theme) : ?>
            <?php $term_link = get_term_link($theme); ?>
            <?php if (is_wp_error($term_link)) continue; ?>
            <div class="theme-entry flex items-center gap-4 border-b pb-2">
                <a href="<?php echo esc_url($term_link); ?>" class="theme-thumb">
                    <img src="<?php echo esc_url($dummy_image); ?>" alt="Theme thumbnail" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
                </a>
                <div class="theme-text">
                    <a href="<?php echo esc_url($term_link); ?>" class="font-medium text-lg">
                        <?php echo esc_html($theme->name); ?>
                    </a>
                    <?php if ($theme->description) : ?>
                        <p class="text-gray-600 text-sm">
                            <?php echo esc_html(wp_trim_words($theme->description, 20, '...')); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
