<?php
/**
 * Template Name: Chapters by Song
 */

get_header();
?>

<main id="main" class="site-main">

    <header class="page-header">
        <h1 class="page-title"><?php the_title(); ?></h1>
    </header>

    <div class="page-content">
        <?php the_content(); ?>

        <!-- 🔢 Table 1: Sorted by Chapter (Post Order) -->
        <h2>Chapters in Order</h2>
        <table class="chapter-by-song chapter-order">
            <thead>
                <tr>
                    <th>Artist</th>
                    <th>Song</th>
                    <th>Chapter</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $chapters = get_posts([
                    'post_type' => 'chapter',
                    'posts_per_page' => -1,
                    'orderby' => 'menu_order',
                    'order' => 'ASC'
                ]);

                foreach ($chapters as $post): setup_postdata($post);
                    $artist = get_field('primary_artist', $post->ID);
                    $song   = get_field('primary_song_title', $post->ID);
                ?>
                    <tr>
                        <td>
                            <?php if ($artist): ?>
                                <a href="<?= get_permalink($artist->ID); ?>">
                                    <?= esc_html(get_the_title($artist->ID)); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td><?= esc_html($song); ?></td>
                        <td><a href="<?= get_permalink($post->ID); ?>"><?= get_the_title($post->ID); ?></a></td>
                    </tr>
                <?php endforeach; wp_reset_postdata(); ?>
            </tbody>
        </table>

        <!-- 🔤 Table 2: Sorted by Artist Name -->
        <h2>Chapters by Artist</h2>
        <table class="chapter-by-song artist-order">
            <thead>
                <tr>
                    <th>Artist</th>
                    <th>Song</th>
                    <th>Chapter</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $chapters = get_posts([
                    'post_type' => 'chapter',
                    'posts_per_page' => -1
                ]);

                $sorted = [];

                foreach ($chapters as $post) {
                    $artist = get_field('primary_artist', $post->ID);
                    if ($artist) {
                        $sorted[] = [
                            'post'        => $post,
                            'artist'      => $artist,
                            'artist_name' => get_the_title($artist->ID),
                            'song'        => get_field('primary_song_title', $post->ID)
                        ];
                    }
                }

                usort($sorted, fn($a, $b) => strcmp($a['artist_name'], $b['artist_name']));

                foreach ($sorted as $row):
                ?>
                    <tr>
                        <td>
                            <a href="<?= get_permalink($row['artist']->ID); ?>">
                                <?= esc_html($row['artist_name']); ?>
                            </a>
                        </td>
                        <td><?= esc_html($row['song']); ?></td>
                        <td><a href="<?= get_permalink($row['post']->ID); ?>"><?= get_the_title($row['post']->ID); ?></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div><!-- .page-content -->

</main><!-- #main -->

<?php get_footer(); ?>
