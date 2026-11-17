<?php $config = isset( $args['config'] ) ? $args['config'] : []; ?>

<p class="top-content-empty">
    No curated entries found<?php echo ! empty( $config['title'] ) ? ' for ' . esc_html( $config['title'] ) : ''; ?>.
</p>