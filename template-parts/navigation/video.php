<?php
// content/video-nav.php

$current_id = get_the_ID();

$video_ids = get_posts([
    'post_type'   => 'video',
    'numberposts' => -1,
    'orderby'     => 'title',
    'order'       => 'ASC',
    'fields'      => 'ids',
]);

$current_index = array_search($current_id, $video_ids);

$next_id = $video_ids[$current_index + 1] ?? null;
$prev_id = $video_ids[$current_index - 1] ?? null;
?>

<div class="post-navigation-container video-nav"
     style="
        display:flex;
        justify-content:center;
        gap:60px;
        margin-top:60px;
        flex-wrap:wrap;
     ">

    <?php if ($next_id): ?>

        <div class="previous-post" style="text-align:center; max-width:260px;">

            <h2>Next Video</h2>

            <a href="<?php echo get_permalink($next_id); ?>"
               style="
                    display:inline-block;
                    text-decoration:none;
                    color:inherit;
               ">

                <?php
                $cover = get_field('video_screenshot', $next_id);

                if ($cover) {

                    echo '<img
                            src="' . esc_url($cover['sizes']['medium']) . '"
                            alt="' . esc_attr(get_the_title($next_id)) . '"
                            style="
                                width:220px;
                                aspect-ratio:16/9;
                                object-fit:cover;
                                border-radius:8px;
                                margin-bottom:12px;
                                display:block;
                                margin-left:auto;
                                margin-right:auto;
                            "
                          >';
                }
                ?>

                <h3 style="
                    font-size:1rem;
                    line-height:1.4;
                    margin:0;
                ">
                    <?php echo get_the_title($next_id); ?>
                </h3>

            </a>

        </div>

    <?php endif; ?>

    <?php if ($prev_id): ?>

        <div class="next-post" style="text-align:center; max-width:260px;">

            <h2>Previous Video</h2>

            <a href="<?php echo get_permalink($prev_id); ?>"
               style="
                    display:inline-block;
                    text-decoration:none;
                    color:inherit;
               ">

                <?php
                $cover = get_field('video_screenshot', $prev_id);

                if ($cover) {

                    echo '<img
                            src="' . esc_url($cover['sizes']['medium']) . '"
                            alt="' . esc_attr(get_the_title($prev_id)) . '"
                            style="
                                width:220px;
                                aspect-ratio:16/9;
                                object-fit:cover;
                                border-radius:8px;
                                margin-bottom:12px;
                                display:block;
                                margin-left:auto;
                                margin-right:auto;
                            "
                          >';
                }
                ?>

                <h3 style="
                    font-size:1rem;
                    line-height:1.4;
                    margin:0;
                ">
                    <?php echo get_the_title($prev_id); ?>
                </h3>

            </a>

        </div>

    <?php endif; ?>

</div>