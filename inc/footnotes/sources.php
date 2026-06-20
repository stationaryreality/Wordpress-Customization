<?php

function fn_sources($chapter_id, $group_titles) {

    if (!function_exists('kp_render_references_flat')) {
        return '';
    }

    $output = kp_render_references_flat($chapter_id);

    if (empty(trim($output))) {
        return '';
    }

    ob_start();
    ?>

    <div class="referenced-group" style="margin-top:2em;">

        <h4>🔗 Other Sources</h4>

        <?php echo $output; ?>

    </div>

    <?php

    return ob_get_clean();
}