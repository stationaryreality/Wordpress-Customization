<?php
/**
 * Redirects for CPT archives, taxonomy roots, and consistent labels
 */

$central_cpts = get_cpt_metadata(); // use central mapper

// --- CPT Archive Redirects ---
add_action('template_redirect', function () use ($central_cpts) {
    if (!is_post_type_archive()) return;

    $cpt = get_post_type();
    if (!$cpt || !isset($central_cpts[$cpt])) return;

    $url = $central_cpts[$cpt]['link'] ?? '';
    if ($url) {
        wp_redirect(home_url($url), 301);
        exit;
    }

});

// --- Taxonomy Redirects ---
add_action('template_redirect', function () {
    // Theme taxonomy
    if (is_tax('theme')) {
        $term = get_queried_object();
        if (empty($term->slug)) {
            wp_redirect(home_url('/themes/'), 301);
            exit;
        }
    }

    // Topic taxonomy
    if (is_tax('topic')) {
        $term = get_queried_object();
        if (empty($term->slug)) {
            wp_redirect(home_url('/topics/'), 301);
            exit;
        }
    }
});
