<?php
$query  = $args['query'];
$config = $args['config'];
?>

<div class="top-content-stream top-content-stream--<?php echo esc_attr( $config['key'] ); ?>">

    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <?php top_content_render_card( $config ); ?>
    <?php endwhile; ?>

</div>