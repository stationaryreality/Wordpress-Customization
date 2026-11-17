<?php $config = $args['config']; ?>

<header class="top-content-section-header">
    <h2><?php echo esc_html( $config['title'] ); ?></h2>

    <?php if ( ! empty( $config['description'] ) ) : ?>
        <p><?php echo esc_html( $config['description'] ); ?></p>
    <?php endif; ?>
</header>