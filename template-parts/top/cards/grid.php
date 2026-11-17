<article class="top-grid-card">

    <a class="top-grid-card-image" href="<?php the_permalink(); ?>">

        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'medium_large' ); ?>
        <?php else : ?>
            <div class="top-grid-card-placeholder"></div>
        <?php endif; ?>

    </a>

    <h3 class="top-grid-card-title">
        <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </h3>

</article>