<?php

/*
|--------------------------------------------------------------------------
| Helper Function
|--------------------------------------------------------------------------
*/

function get_primary_song_and_artist($post_id) {

    $chapter_songs = get_field('chapter_songs', $post_id);

    $primary_song = null;

    if ($chapter_songs) {

        foreach ($chapter_songs as $row) {

            if (
                $row['role'] === 'primary'
                && !empty($row['song'])
            ) {
                $primary_song = $row['song'];
                break;
            }
        }
    }

    $primary_artist = null;

    if ($primary_song instanceof WP_Post) {

        $artist_field = get_field(
            'song_artist',
            $primary_song->ID
        );

        $primary_artist = $artist_field
            ? get_post($artist_field)
            : null;
    }

    return [
        'song'   => $primary_song,
        'artist' => $primary_artist
    ];
}

?>

<section class="tool-chapters-by-song">

<header class="tool-header">

    <h2>Chapters + Fragments by Song</h2>

    <p>
        Lookup tables connecting chapters and fragments
        to their primary songs and artists.
    </p>

</header>

<!-- ========================================================= -->
<!-- CHAPTERS IN ORDER -->
<!-- ========================================================= -->

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
            'post_type'      => 'chapter',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC'
        ]);

        foreach ($chapters as $post):

            setup_postdata($post);

            $data = get_primary_song_and_artist($post->ID);

            $primary_song   = $data['song'];
            $primary_artist = $data['artist'];

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

<!-- ========================================================= -->
<!-- FRAGMENTS IN ORDER -->
<!-- ========================================================= -->

<h2>Fragments in Order</h2>

<table class="chapter-by-song fragment-order">

    <thead>

        <tr>
            <th>Artist</th>
            <th>Song</th>
            <th>Fragment</th>
        </tr>

    </thead>

    <tbody>

        <?php

        $fragments = get_posts([
            'post_type'      => 'fragment',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC'
        ]);

        foreach ($fragments as $post):

            setup_postdata($post);

            $data = get_primary_song_and_artist($post->ID);

            $primary_song   = $data['song'];
            $primary_artist = $data['artist'];

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

<!-- ========================================================= -->
<!-- CHAPTERS BY ARTIST -->
<!-- ========================================================= -->

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
            'post_type'      => 'chapter',
            'posts_per_page' => -1
        ]);

        $sorted = [];

        foreach ($chapters as $post) {

            $data = get_primary_song_and_artist($post->ID);

            $primary_song   = $data['song'];
            $primary_artist = $data['artist'];

            if ($primary_artist) {

                $sorted[] = [
                    'post'        => $post,
                    'artist'      => $primary_artist,
                    'artist_name' => get_the_title($primary_artist->ID),
                    'song'        => $primary_song
                ];
            }
        }

        usort($sorted, fn($a, $b) =>
            strcmp($a['artist_name'], $b['artist_name'])
        );

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

<!-- ========================================================= -->
<!-- FRAGMENTS BY ARTIST -->
<!-- ========================================================= -->

<h2>Fragments by Artist</h2>

<table class="chapter-by-song artist-order">

    <thead>

        <tr>
            <th>Artist</th>
            <th>Song</th>
            <th>Fragment</th>
        </tr>

    </thead>

    <tbody>

        <?php

        $fragments = get_posts([
            'post_type'      => 'fragment',
            'posts_per_page' => -1
        ]);

        $sorted = [];

        foreach ($fragments as $post) {

            $data = get_primary_song_and_artist($post->ID);

            $primary_song   = $data['song'];
            $primary_artist = $data['artist'];

            if ($primary_artist) {

                $sorted[] = [
                    'post'        => $post,
                    'artist'      => $primary_artist,
                    'artist_name' => get_the_title($primary_artist->ID),
                    'song'        => $primary_song
                ];
            }
        }

        usort($sorted, fn($a, $b) =>
            strcmp($a['artist_name'], $b['artist_name'])
        );

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

</section>