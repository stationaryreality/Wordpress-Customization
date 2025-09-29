<?php
get_header();

$term = get_queried_object();
if (!$term || is_wp_error($term)) {
    get_footer();
    exit;
}

$cpt_sections = get_cpt_metadata(); // central CPT metadata helper
?>

<main class="search-results site-main max-w-screen-lg mx-auto p-6">
    <h1>
        <?php echo esc_html(ucfirst($term->taxonomy)); ?>: “<?php echo esc_html($term->name); ?>”
    </h1>

    <?php if (term_description($term->term_id, $term->taxonomy)) : ?>
        <div class="mt-4 text-gray-600">
            <?php echo term_description($term->term_id, $term->taxonomy); ?>
        </div>
    <?php endif; ?>

    <?php
    foreach ($cpt_sections as $type => $info) {
        $query_args = [
            'post_type'      => $type,
            'posts_per_page' => -1,
            'tax_query'      => [
                [
                    'taxonomy' => $term->taxonomy, // dynamic
                    'field'    => 'slug',
                    'terms'    => $term->slug,
                ],
            ],
        ];

        $query = new WP_Query($query_args);

        $template_args = [
            'query'       => $query,
            'info'        => $info,
            'search_term' => $term->name,
            'term'        => $term, // generic (works for both themes + topics)
        ];

        $template_path = locate_template("template-parts/search/{$type}.php");
        if ($template_path) {
            get_template_part("template-parts/search/{$type}", null, $template_args);
        } else {
            get_template_part("template-parts/search/default", null, $template_args);
        }

        wp_reset_postdata();
    }
    ?>
</main>

<?php get_footer(); ?>
