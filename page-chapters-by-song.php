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

                    // === Find primary song for this chapter
                    $chapter_songs = get_field('chapter_songs', $post->ID);
                    $primary_song = null;
                    if (!empty($chapter_songs) && is_array($chapter_songs)) {
                        foreach ($chapter_songs as $row) {
                            if (!empty($row['role']) && $row['role'] === 'primary' && !empty($row['song'])) {
                                $primary_song = $row['song'];
                                break;
                            }
                        }
                    }

                    // === Get artist from that primary song
                    $primary_artist = null;
                    if ($primary_song instanceof WP_Post) {
                        $artist_field = get_field('song_artist', $primary_song->ID);
                        $primary_artist = $artist_field ? get_post($artist_field) : null;
                    }
                ?>
                    <tr>
                        <td>
                            <?php if ($primary_artist): ?>
                                <a href="<?= esc_url(get_permalink($primary_artist->ID)); ?>">
                                    <?= esc_html(get_the_title($primary_artist->ID)); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($primary_song): ?>
                                <a href="<?= esc_url(get_permalink($primary_song->ID)); ?>">
                                    <?= esc_html(get_the_title($primary_song->ID)); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= esc_url(get_permalink($post->ID)); ?>">
                                <?= esc_html(get_the_title($post->ID)); ?>
                            </a>
                        </td>
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
                    // === Find primary song
                    $chapter_songs = get_field('chapter_songs', $post->ID);
                    $primary_song = null;
                    if (!empty($chapter_songs) && is_array($chapter_songs)) {
                        foreach ($chapter_songs as $row) {
                            if (!empty($row['role']) && $row['role'] === 'primary' && !empty($row['song'])) {
                                $primary_song = $row['song'];
                                break;
                            }
                        }
                    }

                    // === Get artist from that song
                    $primary_artist = null;
                    if ($primary_song instanceof WP_Post) {
                        $artist_field = get_field('song_artist', $primary_song->ID);
                        $primary_artist = $artist_field ? get_post($artist_field) : null;
                    }

                    if ($primary_artist) {
                        $sorted[] = [
                            'post'        => $post,
                            'artist'      => $primary_artist,
                            'artist_name' => get_the_title($primary_artist->ID),
                            'song'        => $primary_song
                        ];
                    }
                }

                // Sort alphabetically by artist name
                usort($sorted, fn($a, $b) => strcmp($a['artist_name'], $b['artist_name']));

                foreach ($sorted as $row):
                ?>
                    <tr>
                        <td>
                            <a href="<?= esc_url(get_permalink($row['artist']->ID)); ?>">
                                <?= esc_html($row['artist_name']); ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($row['song']): ?>
                                <a href="<?= esc_url(get_permalink($row['song']->ID)); ?>">
                                    <?= esc_html(get_the_title($row['song']->ID)); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= esc_url(get_permalink($row['post']->ID)); ?>">
                                <?= esc_html(get_the_title($row['post']->ID)); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div><!-- .page-content -->

</main><!-- #main -->

<?php get_footer(); ?>
