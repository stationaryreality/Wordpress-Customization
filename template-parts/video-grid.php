<?php
$query = $args['query'];
$title = $args['title'] ?? 'Videos';
$emoji = $args['emoji'] ?? '';

if (!$query->have_posts()) return;
?>

<section class="video-room-section">

    <h2 class="video-room-title">
        <?php if ($emoji) echo $emoji . ' '; ?>
        <?php echo esc_html($title); ?>
    </h2>

    <div class="video-grid-a">

        <?php while ($query->have_posts()) : $query->the_post(); ?>

            <?php
            $screenshot = get_field('video_screenshot');

            $img_url = $screenshot
                ? $screenshot['sizes']['large']
                : get_the_post_thumbnail_url(get_the_ID(), 'large');
            ?>

            <article class="video-card-a">

                <a href="<?php the_permalink(); ?>">

                    <h3 class="video-title-a">
                        <?php the_title(); ?>
                    </h3>

                    <?php if ($img_url): ?>

                        <img
                            src="<?php echo esc_url($img_url); ?>"
                            alt="<?php the_title(); ?>"
                            class="video-thumb-a"
                        >

                    <?php endif; ?>

                </a>

            </article>

        <?php endwhile; ?>

    </div>

</section>

<?php wp_reset_postdata(); ?>