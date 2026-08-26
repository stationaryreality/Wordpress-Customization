<?php
get_header();

$search_term = get_search_query();

// Kept from your existing search template in case this primes/caches metadata elsewhere.
$cpt_sections = get_cpt_metadata();

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

$sections = [];

/*
 * Preserve the existing Portal-first presentation order.
 */
if (isset($search_sections['portal'])) {
    $sections[] = $search_sections['portal'];
}

/*
 * Topics and Themes are now rendered through the unified search taxonomy list.
 *
 * This list is portal-aware:
 * if a published Portal is assigned to a Topic/Theme term, the raw term result is hidden.
 */
$taxonomy_results = [
    [
        'taxonomy' => 'topic',
        'title'    => 'Topics',
        'emoji'    => '🧩',
    ],
    [
        'taxonomy' => 'theme',
        'title'    => 'Themes',
        'emoji'    => '🎨',
    ],
];

foreach ($taxonomy_results as $taxonomy_result) {
    get_template_part(
        'template-parts/search/taxonomy',
        null,
        [
            'info'        => [
                'title' => $taxonomy_result['title'],
                'emoji' => $taxonomy_result['emoji'],
            ],
            'search_term' => $search_term,
            'taxonomy'    => $taxonomy_result['taxonomy'],
        ]
    );
}

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
 */
kp_render_knowledge_sections($sections);

echo '</main>';

get_footer();