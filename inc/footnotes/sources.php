<?php

function fn_sources($chapter_id, $group_titles) {

    if (!function_exists('kp_render_references')) {
        return '';
    }

    $output = kp_render_references($chapter_id);

    if (empty($output)) {
        return '';
    }

    return '
        <div class="referenced-group" style="margin-top:2em;">
            <h4>🔗 Sources</h4>
            ' . $output . '
        </div>
    ';
}