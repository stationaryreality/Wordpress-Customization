<?php
/* Template Name: Portal Filtered Index */
get_header();

the_post();

// ======= Get portal's linked terms (for all taxonomies) =======
$portal_terms = [];
$taxonomies = get_object_taxonomies(get_post_type());
foreach ($taxonomies as $taxonomy) {
    $terms = wp_get_post_terms(get_the_ID(), $taxonomy, ['fields' => 'slugs']);
    if (!empty($terms) && !is_wp_error($terms)) {
        $portal_terms[$taxonomy] = $terms;
    }
}

if (empty($portal_terms)) {
    echo '<main class="portal-index">';
    echo '<h1>' . esc_html(get_the_title()) . ' Portal</h1>';
    echo '<p>No taxonomy terms found for this portal.</p>';
    echo '</main>';
    get_footer();
    exit;
}

// ======= Central CPT Mapping =======
$map = get_cpt_metadata();

// CPTs to show
$post_types = [
    'artist', 'profile', 'book', 'concept', 'movie', 'quote', 'lyric',
    'reference', 'organization', 'image', 'song', 'chapter', 'excerpt', 'fragment'
];

// ======= Query posts matching any of the portal terms =======
$tax_query = ['relation' => 'OR'];
foreach ($portal_terms as $taxonomy => $slugs) {
    $tax_query[] = [
        'taxonomy' => $taxonomy,
        'field'    => 'slug',
        'terms'    => $slugs,
    ];
}

$args = [
    'post_type'      => $post_types,
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'post_status'    => 'publish',
    'tax_query'      => $tax_query,
];

$query = new WP_Query($args);

// ======= Collect & prepare entries =======
$entries = [];

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $type = get_post_type();

        // Skip portal pages themselves
        if ($type === 'portal') continue;

        $entries[] = [
            'title' => get_the_title(),
            'url'   => get_permalink(),
            'icon'  => $map[$type]['emoji'] ?? '❓',
            'type'  => $type,
        ];
    }
    wp_reset_postdata();
}

// ======= Sort alphabetically by title =======
usort($entries, function ($a, $b) {
    return strcasecmp($a['title'], $b['title']);
});
?>

<main class="portal-index">
    <header class="portal-header">
        <h1><?php the_title(); ?> Portal</h1>
        <?php the_excerpt(); ?>
    </header>

    <?php if (!empty($entries)) : ?>
        <ul class="portal-entry-list">
            <?php foreach ($entries as $entry) : 
                $meta = get_cpt_metadata($entry['type']);
            ?>
                <li>
                    <span class="portal-icon"><?php echo $entry['icon']; ?></span>
                    <a href="<?php echo esc_url($entry['url']); ?>">
                        <?php echo esc_html($entry['title']); ?>
                    </a>
                    <span class="portal-type-label">
                        <?php echo esc_html($meta['title'] ?? ucfirst($entry['type'])); ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No related entries found for this portal.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>