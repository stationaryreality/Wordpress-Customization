<?php

if (!function_exists('kp_get_wikipedia_intro')) {

    /**
     * Fetch Wikipedia summary text.
     *
     * Usage:
     * kp_get_wikipedia_intro('Coldplay');
     */
    function kp_get_wikipedia_intro($slug) {

        if (empty($slug)) {
            return false;
        }

        $api_url = 'https://en.wikipedia.org/api/rest_v1/page/summary/' . urlencode($slug);

        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);

        if (empty($body)) {
            return false;
        }

        $data = json_decode($body, true);

        if (
            empty($data) ||
            empty($data['extract'])
        ) {
            return false;
        }

        return esc_html($data['extract']);
    }
}