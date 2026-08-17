<?php
get_header();

$search_term = get_search_query();

echo '<main class="search-results">';
echo '<h1>Search results for “' . esc_html($search_term) . '”</h1>';

/**
 * Build normalized search context.
 *
 * Search construction is handled by the Search Context collector.
 * This keeps search logic separate from presentation.
 */
$search_context = kp_build_search_context($search_term);

$search_sections = $search_context['sections'] ?? [];

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

    get_template_part('template-parts/grids/theme', null, [
        'items' => $grid_items,
        'title' => $title,
        'emoji' => $emoji,
    ]);
}

// -------------------
// PRIORITY ORDER
// -------------------

$sections = [];

/*
 * Preserve the existing Portal-first presentation order.
 */
if (isset($search_sections['portal'])) {
    $sections[] = $search_sections['portal'];
}


/*
 * Topics and Themes remain independently rendered for now.
 *
 * This is intentionally unchanged during the Search Context
 * migration. Their eventual unification belongs to the
 * Search / Taxonomy presentation work.
 */
render_taxonomy_results('topic', 'Topics', '🧩', $search_term);

render_taxonomy_results('theme', 'Themes', '🎨', $search_term);


/*
 * Add the remaining normalized search sections.
 */
foreach ($search_sections as $type => $section) {
    if ($type === 'portal') {
        continue;
    }

    $sections[] = $section;
}


/*
 * Existing presentation layer.
 *
 * We are deliberately not changing the renderer yet.
 */
kp_render_knowledge_sections($sections);

echo '</main>';

get_footer();