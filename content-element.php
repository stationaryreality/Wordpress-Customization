<div <?php post_class(); ?>>
    <?php do_action('post_before'); ?>

    <article>

        <header class="post-header">
            <h1 class="post-title"><?php the_title(); ?></h1>
        </header>

        <div class="post-content">
            <?php the_content(); ?>
        </div>

        <?php wp_link_pages([
            'before' => '<p class="singular-pagination">',
            'after'  => '</p>',
        ]); ?>

    </article>

    <?php do_action('post_after'); ?>

    <?php get_template_part('content/element-nav'); ?>

</div>