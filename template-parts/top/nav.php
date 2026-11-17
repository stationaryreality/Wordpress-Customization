<?php
$sections = $args['sections'];
$current  = $args['current'];
?>

<nav class="top-content-nav" aria-label="Top content sections">

    <?php foreach ( $sections as $key => $section ) : ?>

        <a
            href="<?php echo esc_url( add_query_arg( 'view', $key ) ); ?>"
            class="<?php echo $current === $key ? 'active' : ''; ?>"
            <?php echo $current === $key ? 'aria-current="page"' : ''; ?>
        >
            <?php echo esc_html( $section['title'] ); ?>
        </a>

    <?php endforeach; ?>

</nav>