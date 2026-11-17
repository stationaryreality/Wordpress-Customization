<?php
$caption = function_exists( 'get_field' ) ? get_field( 'image_caption' ) : '';
$image   = function_exists( 'get_field' ) ? get_field( 'image_file' ) : null;

$img_url = '';
if ( is_array( $image ) ) {
    $img_url = $image['sizes']['medium_large'] ?? $image['sizes']['large'] ?? $image['url'] ?? '';
}
if ( ! $img_url ) {
    $img_url = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
}
?>

<article class="top-image-card">

    <a href="<?php the_permalink(); ?>">

        <?php if ( $img_url ) : ?>
            <img
                src="<?php echo esc_url( $img_url ); ?>"
                alt="<?php echo esc_attr( get_the_title() ); ?>"
                loading="lazy"
            >
        <?php endif; ?>

        <h3><?php the_title(); ?></h3>

    </a>

    <?php if ( $caption ) : ?>
        <p><?php echo esc_html( wp_trim_words( $caption, 20 ) ); ?></p>
    <?php endif; ?>

</article>