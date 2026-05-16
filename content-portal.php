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
    'reference', 'organization', 'image', 'song', 'chapter',
    'excerpt', 'fragment', 'show', 'game', 'element'
];

// ======= Query posts matching portal terms =======
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

// ======= Collect entries =======
$entries = [];

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();

        $type = get_post_type();

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

// ======= SEMANTIC CPT PRIORITY ORDER =======
$priority_order = [
    'concept'       => 1,
    'quote'         => 2,
    'song'          => 3,
    'excerpt'       => 4,
    'lyric'         => 5,
    'chapter'       => 6,
    'fragment'      => 7,
    'element'       => 8,
    'book'          => 9,
    'reference'     => 10,
    'artist'        => 11,
    'organization'  => 12,
    'profile'       => 13,
    'movie'         => 14,
    'show'          => 15,
    'game'          => 16,
    'image'         => 17,
];

// ======= Sort entries =======
usort($entries, function ($a, $b) use ($priority_order) {

    $a_priority = $priority_order[$a['type']] ?? 999;
    $b_priority = $priority_order[$b['type']] ?? 999;

    if ($a_priority !== $b_priority) {
        return $a_priority <=> $b_priority;
    }

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