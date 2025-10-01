<?php
/* Template Name: All CPT Index (Alphabetical) */
get_header();

// ======= Central CPT Mapping =======
$map = get_cpt_metadata();

// CPTs to show in the key table (in your old manual table)
$key_cpts = [
    'artist',
    'book',
    'concept',
    'excerpt',
    'fragment',   // points to homepage anchor
    'image',
    'lyric',
    'movie',
    'organization',
    'profile',
    'quote',
    'reference',
    'song',
    'theme',
    'topic',
    'chapter',    // points to homepage anchor
];

// ======= Collect counts =======
$type_counts = [];
$total_count = 0;

foreach ($key_cpts as $cpt) {
    if ($cpt === 'theme' || $cpt === 'topic') {
        $count = wp_count_terms($cpt, ['hide_empty' => false]);
    } else {
        $obj = wp_count_posts($cpt);
        $count = isset($obj->publish) ? $obj->publish : 0;
    }
    $type_counts[$cpt] = $count;
    $total_count += $count;
}

// ======= Alphabetize key by title =======
usort($key_cpts, function($a, $b) use ($map) {
    return strcasecmp($map[$a]['title'], $map[$b]['title']);
});

// ======= Build full alphabetical entries list =======

$post_types = [
    'artist',
    'profile',
    'book',
    'concept',
    'movie',
    'quote',
    'lyric',
    'reference',
    'organization',
    'image',
    'song',
    'chapter',
    'excerpt',
    'fragment'
];

$post_args = [
    'post_type'      => $post_types,
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'post_status'    => 'publish'
];

$post_query = new WP_Query($post_args);

// Emoji mapping (for full list)
$icons = [];
foreach ($post_types as $pt) {
    $icons[$pt] = $map[$pt]['emoji'] ?? '❓';
}
$icons['theme'] = $map['theme']['emoji'] ?? '❓';
$icons['topic'] = $map['topic']['emoji'] ?? '❓';

// Collect entries
$entries = [];

if ($post_query->have_posts()) {
    while ($post_query->have_posts()) {
        $post_query->the_post();
        $type  = get_post_type();
        $title = get_the_title();
        $url   = get_permalink();
        $icon  = $icons[$type] ?? '❓';

        $entries[] = [
            'title' => $title,
            'url'   => $url,
            'icon'  => $icon
        ];
    }
    wp_reset_postdata();
}

// Add theme taxonomy terms
$themes = get_terms([
    'taxonomy'   => 'theme',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC'
]);

foreach ($themes as $term) {
    $entries[] = [
        'title' => $term->name,
        'url'   => get_term_link($term),
        'icon'  => $icons['theme']
    ];
}

// Add topic taxonomy terms
$topics = get_terms([
    'taxonomy'   => 'topic',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC'
]);

foreach ($topics as $term) {
    $entries[] = [
        'title' => $term->name,
        'url'   => get_term_link($term),
        'icon'  => $icons['topic']
    ];
}

// Alphabetize the full list
usort($entries, function ($a, $b) {
    return strcasecmp($a['title'], $b['title']);
});
?>

<main class="cpt-index-alphabetical">
    <header class="archive-header">
        <h1 class="post-title">All Entries (Alphabetical Index)</h1>
        <p class="post-meta">
            Total entries: <strong><?php echo $total_count; ?></strong>
        </p>

        <!-- ======= Auto-generated Key Table ======= -->
        <figure class="wp-block-table aligncenter has-regular-font-size">
            <table>
                <tbody>
                    <?php foreach ($key_cpts as $cpt) :
                        $meta = get_cpt_metadata($cpt);
                        if (!$meta) continue;

                        // Fix anchors for homepage
                        if ($cpt === 'chapter') {
                            $meta['link'] = '/#narrative-threads';
                        } elseif ($cpt === 'fragment') {
                            $meta['link'] = '/#narrative-fragments';
                        }
                        ?>
                        <tr>
                            <td><?php echo $meta['emoji']; ?></td>
                            <td class="has-text-align-left" data-align="left">
                                <a href="<?php echo esc_url(home_url($meta['link'])); ?>">
                                    <?php echo esc_html($meta['title']); ?>
                                </a>
                                (<?php echo $type_counts[$cpt]; ?>)
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </figure>
    </header>

    <!-- ======= Full Alphabetical Index ======= -->
    <?php if (!empty($entries)) : ?>
        <ul class="cpt-alpha-list">
            <?php foreach ($entries as $entry) : ?>
                <li>
                    <span class="cpt-icon"><?php echo $entry['icon']; ?></span>
                    <a href="<?php echo esc_url($entry['url']); ?>"><?php echo esc_html($entry['title']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No entries found.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
