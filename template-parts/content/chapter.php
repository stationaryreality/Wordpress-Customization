<div <?php post_class(); ?>>

  <?php get_template_part('template-parts/navigation/chapter'); ?>

    <?php ct_author_featured_image(); ?>

    <article>
        <div class='post-header'>
            <h1 class='post-title'><?php the_title(); ?></h1>
        </div>
<?php 
$primary_song = kp_get_primary_song(get_the_ID());
kp_render_artist_header($primary_song); 
?>

        <?php
        $updated = get_the_modified_time('F j, Y');
        if ($updated) : ?>
            <p class="last-updated">Last updated: <?php echo esc_html($updated); ?></p>
        <?php endif; ?>

        <div class="post-content">
            <?php the_content(); ?>
            <?php wp_link_pages( array(
                'before' => '<p class="singular-pagination">' . esc_html__( 'Pages:', 'author' ),
                'after'  => '</p>',
            ) ); ?>
        </div>

    </article>

</div>
