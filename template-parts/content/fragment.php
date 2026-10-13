<div <?php post_class('cpt-fragment-content'); ?>>

    <?php get_template_part('template-parts/navigation/fragment'); ?>

    <?php do_action('post_before'); ?>
    <?php ct_author_featured_image(); ?>

    <article>
        <div class='post-header'>
            <h1 class='post-title'><?php the_title(); ?></h1>
        </div>

<?php 
$primary_song = kp_get_primary_song(get_the_ID());
kp_render_artist_header($primary_song); 
?>
        
        <div class="post-content">
            <?php ct_author_output_last_updated_date(); ?>
            <?php the_content(); ?>
            <?php wp_link_pages( array(
                'before' => '<p class="singular-pagination">' . esc_html__( 'Pages:', 'author' ),
                'after'  => '</p>',
            ) ); ?>
        </div>
    </article>

    <?php do_action('post_after'); ?>

</div>