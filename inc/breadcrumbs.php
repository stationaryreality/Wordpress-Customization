<?php
// inc/breadcrumbs.php
/**
 * Yoast Breadcrumb Override
 * Uses central CPT mapper for archive labels and custom taxonomies for Themes & Topics
 */

add_filter('wpseo_breadcrumb_links', function($links) {

    $central_cpts = get_cpt_metadata();

    // Loop through each link
    foreach ($links as $key => $link) {

        // Skip the last link (current page)
        if ($key === count($links) - 1) continue;

        // Replace CPT archive labels
        foreach ($central_cpts as $cpt => $info) {
            $archive_url = get_post_type_archive_link($cpt);
            if (!$archive_url) continue;

            if (rtrim($link['url'], '/') === rtrim($archive_url, '/')) {
                $links[$key]['text'] = $info['title']; // mapper title
                $links[$key]['url']  = $archive_url;   // ensure correct URL
            }
        }
    }

    // === Theme Taxonomy ===
    if (is_tax('theme')) {
        $term = get_queried_object();
        $themes_page = get_page_by_path('themes');

        $new_links = [
            ['url' => home_url('/'), 'text' => 'Home']
        ];

        if ($themes_page) {
            $new_links[] = [
                'url'  => get_permalink($themes_page),
                'text' => 'Themes'
            ];
        }

        $new_links[] = ['url' => '', 'text' => $term->name];

        return $new_links;
    }

    // === Topic Taxonomy ===
    if (is_tax('topic')) {
        $term = get_queried_object();
        $topics_page = get_page_by_path('topics');

        $new_links = [
            ['url' => home_url('/'), 'text' => 'Home']
        ];

        if ($topics_page) {
            $new_links[] = [
                'url'  => get_permalink($topics_page),
                'text' => 'Topics'
            ];
        }

        $new_links[] = ['url' => '', 'text' => $term->name];

        return $new_links;
    }

    // === Optional: remove self-link on actual Pages for Themes/Topics ===
    if (is_page('themes')) {
        foreach ($links as &$link) {
            if ($link['text'] === 'Themes') $link['url'] = '';
        }
    }

    if (is_page('topics')) {
        foreach ($links as &$link) {
            if ($link['text'] === 'Topics') $link['url'] = '';
        }
    }

    return $links;
});
