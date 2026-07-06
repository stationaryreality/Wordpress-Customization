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
$sections = [];

foreach ($cpt_sections as $type => $info) {

    $query = new WP_Query([
        'post_type'      => $type,
        'posts_per_page' => -1,
        'tax_query'      => [[
            'taxonomy' => $term->taxonomy,
            'field'    => 'slug',
            'terms'    => $term->slug,
        ]],
    ]);

    $sections[] = [
        'type'        => $type,
        'query'       => $query,
        'info'        => $info,
        'search_term' => $term->name,
        'term'        => $term,
    ];

}

kp_render_knowledge_sections($sections);
    ?>
</main>

<?php get_footer(); ?>
