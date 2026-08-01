<?php
$source = get_field('song');
?>

<div class="person-content cpt-lyric-content">

    <?php get_template_part('template-parts/navigation/lyric'); ?>

    <h1 class="cpt-lyric-title"><?php the_title(); ?></h1>

    <div class="cpt-lyric-text">
        <?php the_content(); ?>
    </div>

    <?php if ($source): ?>
        <p class="cpt-lyric-source">
            Source: <a href="<?php echo esc_url(get_permalink($source->ID)); ?>">
                <?php echo esc_html(get_the_title($source->ID)); ?>
            </a>
        </p>
    <?php endif; ?>

    <?php show_featured_in_threads('lyrics_referenced'); ?>
    <?php echo fn_taxonomy_bubbles(get_the_ID()); ?>

</div>