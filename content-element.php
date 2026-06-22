<div <?php post_class(); ?>>
    <?php do_action('post_before'); ?>

    <article>

        <header class="post-header">
            <h1 class="post-title"><?php the_title(); ?></h1>
        </header>

        <div class="post-content">
            <?php the_content(); ?>
        </div>

        <?php

$related = get_field('related_content');

if (!empty($related)) :

    usort($related, fn($a, $b) =>
        strcmp(get_the_title($a), get_the_title($b))
    );
?>

<details style="margin-top:2rem;">

    <summary>
        Related Content (<?php echo count($related); ?>)
    </summary>

    <ul style="
        list-style:none;
        padding-left:0;
        margin-top:1rem;
    ">

        <?php foreach ($related as $item) :

            $type = get_post_type($item);

            $meta = get_cpt_metadata($type);

            $emoji = $meta['emoji'] ?? '•';
        ?>

            <li style="margin-bottom:.4rem;">

                <?php echo esc_html($emoji); ?>

                <a href="<?php echo esc_url(get_permalink($item)); ?>">
                    <?php echo esc_html(get_the_title($item)); ?>
                </a>

            </li>

        <?php endforeach; ?>

    </ul>

</details>

<?php endif; ?>

<?php
echo kp_render_references(get_the_ID());
?>

        <?php wp_link_pages([
            'before' => '<p class="singular-pagination">',
            'after'  => '</p>',
        ]); ?>

    </article>

    <?php do_action('post_after'); ?>

    <?php get_template_part('content/element-nav'); ?>

</div>