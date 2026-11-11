<?php

function show_featured_in_threads($meta_key, $post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    if (empty($meta_key)) return;

    $context = kp_build_featured_context($meta_key, $post_id);

    if (empty($context['chapters']) && empty($context['fragments']) && empty($context['elements'])) {
        return;
    }

    get_template_part('template-parts/views/featured-in-grid', null, [
        'title'     => 'Featured In',
        'chapters'  => $context['chapters'],
        'fragments' => $context['fragments'],
        'elements'  => $context['elements']
    ]);
}