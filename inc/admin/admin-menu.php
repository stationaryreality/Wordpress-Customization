<?php

add_action('admin_menu', function () {

    add_menu_page(
        'Content Operations',
        'Content Ops',
        'manage_options',
        'content-operations',
        'render_content_operations_page',
        'dashicons-database',
        25
    );

});