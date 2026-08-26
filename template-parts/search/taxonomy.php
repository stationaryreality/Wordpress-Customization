<?php
/**
 * Template part for displaying matching taxonomy terms in search results.
 *
 * Expected args:
 * - info        array  ['title' => string, 'emoji' => string]
 * - search_term string
 * - taxonomy    string
 *
 * Portal behavior:
 * If a published Portal CPT is assigned to a Topic/Theme term,
 * the raw taxonomy term result is hidden.
 */

$args = isset($args) ? $args : [];

$info        = $args['info'] ?? [];
$search_term = $args['search_term'] ?? '';
$taxonomy    = $args['taxonomy'] ?? '';

if (!$taxonomy || trim($search_term) === '') {
    return;
}

$title = $info['title'] ?? ucfirst($taxonomy);
$emoji = $info['emoji'] ?? '';

/*
 * Find matching terms.
 */
$terms = get_terms([
    'taxonomy'   => $taxonomy,
    'hide_empty' => false,
    'name__like' => $search_term,
]);

if (empty($terms) || is_wp_error($terms)) {
    return;
}

/*
 * Portal override cache.
 *
 * Collect all published Portal posts and their assigned topic/theme term IDs.
 * This runs once per request, even though this template part is included
 * twice: once for Topics and once for Themes.
 */
static $kp_portal_term_ids = null;

if (!is_array($kp_portal_term_ids)) {
    $kp_portal_term_ids = [
        'topic' => [],
        'theme' => [],
    ];

    $portal_ids = get_posts([
        'post_type'              => 'portal',
        'post_status'            => 'publish',
        'numberposts'            => -1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
    ]);

    foreach ($portal_ids as $portal_id) {
        $portal_topics = get_the_terms($portal_id, 'topic');

        if ($portal_topics && !is_wp_error($portal_topics)) {
            foreach ($portal_topics as $portal_term) {
                $kp_portal_term_ids['topic'][] = (int) $portal_term->term_id;
            }
        }

        $portal_themes = get_the_terms($portal_id, 'theme');

        if ($portal_themes && !is_wp_error($portal_themes)) {
            foreach ($portal_themes as $portal_term) {
                $kp_portal_term_ids['theme'][] = (int) $portal_term->term_id;
            }
        }
    }

    $kp_portal_term_ids['topic'] = array_unique($kp_portal_term_ids['topic']);
    $kp_portal_term_ids['theme'] = array_unique($kp_portal_term_ids['theme']);
}

/*
 * Filter out terms that already have a published Portal.
 */
$portal_term_ids = $kp_portal_term_ids[$taxonomy] ?? [];
$visible_terms   = [];

foreach ($terms as $term) {
    if (in_array((int) $term->term_id, $portal_term_ids, true)) {
        continue;
    }

    $term_link = get_term_link($term);

    if (is_wp_error($term_link)) {
        continue;
    }

    $visible_terms[] = [
        'url'         => $term_link,
        'name'        => $term->name,
        'description' => $term->description,
    ];
}

if (empty($visible_terms)) {
    return;
}
?>

<section class="search-tax-section">

    <h2 class="search-tax-title">
        <?php echo esc_html(trim($emoji . ' ' . $title)); ?>

        <?php if ($search_term): ?>
            containing “<?php echo esc_html($search_term); ?>”
        <?php endif; ?>
    </h2>

    <div class="search-tax-list">
        <?php foreach ($visible_terms as $item): ?>
            <a class="search-tax-item" href="<?php echo esc_url($item['url']); ?>">
                <span class="search-tax-name">
                    <?php echo esc_html($item['name']); ?>
                </span>

                <?php if (!empty($item['description'])): ?>
                    <span class="search-tax-description">
                        <?php echo esc_html(wp_trim_words($item['description'], 18, '…')); ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>

</section>