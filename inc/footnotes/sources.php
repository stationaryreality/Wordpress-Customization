<?php

function fn_sources($chapter_id, $group_titles) {

    if (!function_exists('kp_render_references')) {
        return '';
    }

    $output  = kp_render_references($chapter_id);

    if (function_exists('kp_render_related_references')) {
        $output .= kp_render_related_references($chapter_id);
    }

    if (empty(trim($output))) {
        return '';
    }

    return '
        <div class="referenced-group" style="margin-top:2em;">
            <h4>🔗 Sources</h4>
            ' . $output . '
        </div>
    ';
}