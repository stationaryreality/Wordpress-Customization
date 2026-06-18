<?php

function fn_sources($chapter_id, $group_titles) {

    if (!function_exists('kp_render_references')) {
        return '';
    }

    $output = kp_render_references($chapter_id);

    if (empty($output)) {
        return '';
    }

$output  = kp_render_references($chapter_id);
$output .= kp_render_related_references($chapter_id);

}