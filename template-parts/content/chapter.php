<div <?php post_class(); ?>>

    <?php ct_author_featured_image(); ?>

    <article>
        <div class='post-header'>
            <h1 class='post-title'><?php the_title(); ?></h1>
        </div>

        <!-- Artist + Primary Song Info -->
        <?php
        $chapter_songs = get_field('chapter_songs');
        $primary_song = null;

        if (!empty($chapter_songs) && is_array($chapter_songs)) {
            foreach ($chapter_songs as $row) {
                if (!empty($row['role']) && $row['role'] === 'primary' && !empty($row['song']) && $row['song'] instanceof WP_Post) {
                    $primary_song = $row['song'];
                    break;
                }
            }
        }

        if ($primary_song):
            $artist_field = get_field('song_artist', $primary_song->ID);
            $primary_artist = $artist_field ? get_post($artist_field) : null;

            if ($primary_artist instanceof WP_Post):
                $portrait    = get_field('portrait_image', $primary_artist->ID);
                $img_url     = $portrait ? $portrait['sizes']['thumbnail'] : '';
                $artist_name = get_the_title($primary_artist->ID);
                $artist_link = get_permalink($primary_artist->ID);
                ?>
                <div class="artist-meta">
                    <?php if ($img_url): ?>
                        <a href="<?php echo esc_url($artist_link); ?>">
                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($artist_name); ?>" class="artist-thumbnail rounded">
                        </a>
                    <?php endif; ?>

                    <h2 class="artist-name">
                        <a href="<?php echo esc_url($artist_link); ?>" style="text-decoration: underline;">
                            <?php echo esc_html($artist_name); ?>
                        </a>
                    </h2>

                    <?php
                        $song_title = get_the_title($primary_song->ID);
                        $song_link  = get_permalink($primary_song->ID);
                    ?>
                    <div class="song-title">
                        <a href="<?php echo esc_url($song_link); ?>" style="text-decoration: underline;">
                            <?php echo esc_html($song_title); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        $updated = get_the_modified_time('F j, Y');
        if ($updated) : ?>
            <p class="last-updated">Last updated: <?php echo esc_html($updated); ?></p>
        <?php endif; ?>

        <div class="post-content">
            <?php the_content(); ?>
            <?php wp_link_pages( array(
                'before' => '<p class="singular-pagination">' . esc_html__( 'Pages:', 'author' ),
                'after'  => '</p>',
            ) ); ?>
        </div>

    </article>

    <?php get_template_part( 'content/post-nav' ); ?>

</div>
