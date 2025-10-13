<?php
if ( ! function_exists( 'fn_sort_terms_by_name' ) ) {
    function fn_sort_terms_by_name( $a, $b ) {
        return strcmp( $a->name, $b->name );
    }
}

if ( ! function_exists( 'fn_taxonomy_bubbles' ) ) {
    function fn_taxonomy_bubbles( $post_id = 0 ) {
        if ( ! $post_id ) $post_id = get_the_ID();

        $taxonomies = array(
            'topic' => array( 'title' => 'Topics', 'emoji' => '🏷️', 'link' => '' ),
            'theme' => array( 'title' => 'Themes', 'emoji' => '💭', 'link' => '' ),
        );

        $output = '';

        foreach ( $taxonomies as $taxonomy => $meta ) {
            $terms = get_the_terms( $post_id, $taxonomy );
            if ( ! $terms || is_wp_error( $terms ) ) continue;

            usort( $terms, 'fn_sort_terms_by_name' );

            $output .= '<div class="referenced-group" style="margin-top:2em;">';
            $output .= '<h4>';
            if ( ! empty( $meta['link'] ) ) $output .= '<a href="' . esc_url( $meta['link'] ) . '" style="text-decoration:none;">';
            $output .= '<span style="font-size:1.1em;">' . esc_html( $meta['emoji'] ) . '</span> ';
            $output .= '<span style="text-decoration:underline;">' . esc_html( $meta['title'] ) . '</span>';
            if ( ! empty( $meta['link'] ) ) $output .= '</a>';
            $output .= '</h4>';

            $output .= '<div class="tag-bubbles">';
            foreach ( $terms as $term ) {
                $link  = esc_url( get_term_link( $term ) );
                $title = esc_html( $term->name );
                $output .= '<span class="bubble-wrapper"><a class="tag-bubble" href="' . $link . '">' . $title . '</a></span>';
            }
            $output .= '</div></div>';
        }

        return $output;
    }
}
