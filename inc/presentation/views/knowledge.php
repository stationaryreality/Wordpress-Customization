<?php

$knowledge_data = kp_build_portal_sections(
    $query,
    $map,
    $section_order,
    $section_labels,
    [
        'type'    => 'portal',
        'post_id' => get_the_ID(),
    ]
);

kp_load_knowledge_view(
    'knowledge',
    [
        'portal_data' => $knowledge_data,
    ]
);