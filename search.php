<?php
get_header();

$search_term = get_search_query();
$cpt_sections = get_cpt_metadata(); // central CPT metadata

echo '<main class="search-results">';
echo '<h1>Search results for “' . esc_html($search_term) . '”</h1>';

// --- CPT Sections ---
foreach ($cpt_sections as $type => $info) {
    $query = new WP_Query([
        'post_type'      => $type,
        's'              => $search_term,
        'posts_per_page' => -1,
        'relevanssi'     => true,
    ]);

    if (function_exists('relevanssi_do_query')) {
        relevanssi_do_query($query);
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

// --- Taxonomy Sections (Themes + Topics) ---
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

// Call for both
render_taxonomy_results('theme', 'Themes', '🎨', $search_term);
render_taxonomy_results('topic', 'Topics', '🧩', $search_term);

echo '</main>';

get_footer();
