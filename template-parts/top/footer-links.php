<?php $config = $args['config']; ?>

<?php if ( ! empty( $config['full_page'] ) ) : ?>

    <footer class="top-content-footer">
        <a href="<?php echo esc_url( $config['full_page'] ); ?>">
            Explore Full <?php echo esc_html( $config['title'] ); ?>
        </a>
    </footer>

<?php endif; ?>