<?php
$source = get_field('quote_source');
?>

<div class="person-content cpt-quote-content">

    <?php get_template_part('template-parts/navigation/quote'); ?>

    <h1 class="cpt-quote-title"><?php the_title(); ?></h1>

    <div class="cpt-quote-text">
        <?php the_content(); ?>
    </div>

    <?php if ($source): ?>
        <p class="cpt-quote-source">
            Source: <a href="<?php echo esc_url(get_permalink($source->ID)); ?>">
                <?php echo esc_html(get_the_title($source->ID)); ?>
            </a>
        </p>
    <?php endif; ?>

    <?php if (function_exists('kp_render_references')): ?>
        <?php echo kp_render_references(get_the_ID()); ?>
    <?php endif; ?>

    <?php show_featured_in_threads('quotes_referenced'); ?>
    <?php echo fn_taxonomy_bubbles(get_the_ID()); ?>

</div>