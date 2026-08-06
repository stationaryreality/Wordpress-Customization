<?php

/**
 * Render a normalized knowledge view.
 *
 * Views are presentation layers and are independent
 * of the context that supplied the knowledge data.
 *
 * Supported views:
 *
 *   knowledge
 *   index
 *   list
 *   atlas
 *
 * $data should contain the normalized knowledge structure
 * produced by kp_build_knowledge().
 */

function kp_render_knowledge_view(
    string $view,
    array $data = []
) {

    $allowed_views = [
        'knowledge',
        'index',
        'list',
        'atlas',
    ];

    if (!in_array($view, $allowed_views, true)) {
        $view = 'index';
    }

    /*
     * Make normalized knowledge data available
     * to the selected view.
     */
    $knowledge_data = $data;

    /*
     * Backward compatibility while the Portal migration
     * is being completed.
     *
     * Existing Portal views currently expect $portal_data.
     * This can be removed once those views are migrated.
     */
    $portal_data = $knowledge_data;

    extract($data, EXTR_SKIP);

    include locate_template(
        "inc/presentation/views/{$view}.php"
    );
}


/**
 * Backward-compatible alias.
 *
 * Existing callers can continue using kp_load_knowledge_view()
 * while contexts are migrated to kp_render_knowledge_view().
 */
function kp_load_knowledge_view(
    $view,
    array $data = []
) {
    return kp_render_knowledge_view($view, $data);
}