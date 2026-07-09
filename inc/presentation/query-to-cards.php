<?php

/**
 * Convert a WP_Query into normalized cards.
 */

function kp_query_to_cards(
    WP_Query $query,
    array $map = []
) {

    $cards = [];

    if (!$query->have_posts()) {
        return $cards;
    }

    while ($query->have_posts()) {

        $query->the_post();

        $cards[] = kp_build_card(
            get_post_type(),
            get_the_ID(),
            $map
        );

    }

    wp_reset_postdata();

    return $cards;

}