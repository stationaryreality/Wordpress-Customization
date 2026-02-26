<?php
/* Template Name: Listening Room */
get_header();
?>

<main id="primary" class="site-main listening-room-archive">

  <section class="cpt-section">

    <?php
    // Get all song groups except Ungrouped
    $groups = get_terms([
      'taxonomy'   => 'song_group',
      'hide_empty' => true,
      'orderby'    => 'name',
      'order'      => 'ASC',
      'slug'       => '',
    ]);

    // Separate Ungrouped
    $ungrouped = null;
    foreach ($groups as $key => $group) {
      if ($group->slug === 'ungrouped') {
        $ungrouped = $group;
        unset($groups[$key]);
      }
    }

    // Render grouped songs first
    foreach ($groups as $group):

      $songs = new WP_Query([
        'post_type'      => 'song',
        'posts_per_page' => -1,
        'tax_query'      => [
          'relation' => 'AND',
          [
            'taxonomy' => 'listening_room',
            'field'    => 'slug',
            'terms'    => 'listening-room',
          ],
          [
            'taxonomy' => 'song_group',
            'field'    => 'slug',
            'terms'    => $group->slug,
          ],
        ],
        'orderby' => 'title',
        'order'   => 'ASC',
      ]);

      if ($songs->have_posts()): ?>
        <div class="feature-group">
          <h3 class="feature-level"><?php echo esc_html($group->name); ?></h3>
          <div class="song-grid">
            <?php while ($songs->have_posts()): $songs->the_post();
              $cover   = get_field('cover_image');
              $img_url = $cover ? $cover['sizes']['thumbnail'] : '';
            ?>
              <div class="book-item">
                <a href="<?php the_permalink(); ?>">
                  <?php if ($img_url): ?>
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
                  <?php endif; ?>
                  <h3><?php the_title(); ?></h3>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
        <?php wp_reset_postdata();
      endif;

    endforeach;
    ?>

    <?php
    // Render Ungrouped last
    if ($ungrouped):

      $ungrouped_songs = new WP_Query([
        'post_type'      => 'song',
        'posts_per_page' => -1,
        'tax_query'      => [
          'relation' => 'AND',
          [
            'taxonomy' => 'listening_room',
            'field'    => 'slug',
            'terms'    => 'listening-room',
          ],
          [
            'taxonomy' => 'song_group',
            'field'    => 'slug',
            'terms'    => 'ungrouped',
          ],
        ],
        'orderby' => 'title',
        'order'   => 'ASC',
      ]);

      if ($ungrouped_songs->have_posts()): ?>
        <div class="feature-group ungrouped">
          <h3 class="feature-level">Ungrouped</h3>
          <div class="song-grid">
            <?php while ($ungrouped_songs->have_posts()): $ungrouped_songs->the_post();
              $cover   = get_field('cover_image');
              $img_url = $cover ? $cover['sizes']['thumbnail'] : '';
            ?>
              <div class="book-item">
                <a href="<?php the_permalink(); ?>">
                  <?php if ($img_url): ?>
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
                  <?php endif; ?>
                  <h3><?php the_title(); ?></h3>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
        <?php wp_reset_postdata();
      endif;

    endif;
    ?>

  </section>

</main>

<?php get_footer(); ?>
