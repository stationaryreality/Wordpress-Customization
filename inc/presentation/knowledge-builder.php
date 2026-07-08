<?php

function kp_build_knowledge(array $sections, array $args = [])
{
    $active_sections = [];
    $total_entries   = 0;

    foreach ($sections as $type => $items) {

        if (empty($items)) {
            continue;
        }

        $active_sections[$type] = count($items);

        $total_entries += count($items);
    }

    return [

        'hero' => $args['hero'] ?? null,

        'sections' => $sections,

        'active_sections' => $active_sections,

        'total_entries' => $total_entries,

        'section_order' =>
            $args['section_order']
            ?? array_keys($sections),

        'section_labels' =>
            $args['section_labels']
            ?? [],

        'map' =>
            $args['map']
            ?? get_cpt_metadata(),

        'context' =>
            $args['context']
            ?? [],

    ];
}