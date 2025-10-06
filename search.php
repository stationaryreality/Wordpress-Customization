<?php
get_header();

$search_term = get_search_query();
$cpt_sections = get_cpt_metadata(); // central CPT metadata

echo '<main class="search-results">';
echo '<h1>Search results for “' . esc_html($search_term) . '”</h1>';

/**
 * Build a map: post_type => [term-matching post IDs...]
 * Exact match (case-insensitive) against term name OR match by slug.
 */
$taxonomies_to_include  = ['theme', 'topic']; // add more if needed
$term_posts_by_type    = [];
$search_term_lower     = mb_strtolower($search_term);
$search_term_slug      = sanitize_title($search_term);

foreach ($taxonomies_to_include as $taxonomy) {
    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ]);
    if (empty($terms) || is_wp_error($terms)) {
        continue;
    }

    foreach ($terms as $term) {
        // case-insensitive exact name OR slug match
        if (mb_strtolower($term->name) === $search_term_lower || $term->slug === $search_term_slug) {
            $posts_in_term = get_posts([
                'post_type'      => array_keys($cpt_sections),
                'tax_query'      => [[
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ]],
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ]);
            foreach ($posts_in_term as $pid) {
                $ptype = get_post_type($pid);
                if ($ptype) {
                    $term_posts_by_type[$ptype][] = intval($pid);
                }
            }
        }
    }
}
// Normalize/unique
foreach ($term_posts_by_type as $ptype => $ids) {
    $term_posts_by_type[$ptype] = array_values(array_unique($ids));
}

/**
 * Helper: Render CPT results (now merges taxonomy-matched posts in a non-destructive way)
 */
function render_cpt_results($type, $info, $search_term, $term_posts_by_type = []) {
    $query_args = [
        'post_type'      => $type,
        's'              => $search_term,
        'posts_per_page' => -1,
        'relevanssi'     => true,
    ];

    $query = new WP_Query($query_args);

    if (function_exists('relevanssi_do_query')) {
        relevanssi_do_query($query);
    }

    // If we have taxonomy-matched IDs for this post_type, append them if they're not already present
    if (!empty($term_posts_by_type[$type])) {
        $existing_ids = [];
        if (!empty($query->posts)) {
            foreach ($query->posts as $p) {
                $existing_ids[] = is_object($p) ? $p->ID : intval($p);
            }
        }
        $new_ids = array_diff($term_posts_by_type[$type], $existing_ids);

        if (!empty($new_ids)) {
            $new_posts = get_posts([
                'post_type'      => $type,
                'post__in'       => $new_ids,
                'posts_per_page' => -1,
                'orderby'        => 'post__in',
            ]);

            // Append to the existing posts array (keeps Relevanssi order first)
            $query->posts = array_merge((array) $query->posts, $new_posts);
            $query->post_count = count($query->posts);
            $query->found_posts = isset($query->found_posts) ? max($query->found_posts, $query->post_count) : $query->post_count;
        }
    }

    $template_path = locate_template("template-parts/search/{$type}.php");
    if ($template_path) {
        get_template_part("template-parts/search/{$type}", null, [
            'query'       => $query,
            'info'        => $info,
            'search_term' => $search_term,
        ]);
    } else {
        get_template_part("template-parts/search/default", null, [
            'query'       => $query,
            'info'        => $info,
            'search_term' => $search_term,
        ]);
    }
}

/**
 * Helper: Render taxonomy results (unchanged)
 */
function render_taxonomy_results($taxonomy, $title, $emoji, $search_term) {
    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'name__like' => $search_term,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return;
    }

    $placeholder_id = 20262; // fallback image
    $acf_field_name = $taxonomy . '_cover_image'; // e.g., theme_cover_image, topic_cover_image

    $grid_items = [];
    foreach ($terms as $term) {
        $image_id = function_exists('get_field') ? get_field($acf_field_name, 'term_' . $term->term_id) : '';
        if (!$image_id) $image_id = $placeholder_id;

        $grid_items[] = [
            'image_id' => intval($image_id),
            'title'    => $term->name,
            'url'      => get_term_link($term),
        ];
    }

    get_template_part('template-parts/theme-grid', null, [
        'items' => $grid_items,
        'title' => $title,
        'emoji' => $emoji,
    ]);
}

// -------------------
// PRIORITY ORDER
// -------------------

// 1. Portal CPT
if (isset($cpt_sections['portal'])) {
    render_cpt_results('portal', $cpt_sections['portal'], $search_term, $term_posts_by_type);
}

// 2. Topics
render_taxonomy_results('topic', 'Topics', '🧩', $search_term);

// 3. Themes
render_taxonomy_results('theme', 'Themes', '🎨', $search_term);

// 4. Remaining CPTs (excluding portal since it was handled already)
foreach ($cpt_sections as $type => $info) {
    if ($type === 'portal') continue;
    render_cpt_results($type, $info, $search_term, $term_posts_by_type);
}

echo '</main>';

get_footer();
