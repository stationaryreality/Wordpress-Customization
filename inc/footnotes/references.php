<?php
// inc/footnotes/references.php

function fn_references($chapter_id, $group_titles) {

    if (!function_exists('kp_render_references')) {
        return '';
    }

    return kp_render_references($chapter_id, true);
}
