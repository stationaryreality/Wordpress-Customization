<?php
/* Template Name: Listening Room */
get_header();
?>

<main id="primary" class="site-main listening-room-archive">

  <h1 style="text-align:center; font-size: 48px; margin-bottom: 10px;">The Listening Room</h1>
  <p style="text-align:center; max-width: 600px; margin: 0 auto 60px; color: #666;">Curated soundscapes for reading, reflection, and philosophical inquiry.</p>

  <section class="cpt-section">

    <?php
    // 1. Get all song groups
    $groups = get_terms([
      'taxonomy'   => 'song_group',
      'hide_empty' => true,
      'orderby'    => 'name',
      'order'      => 'ASC',
    ]);

    // Separate Ungrouped
    $ungrouped = null;
    foreach ($groups as $key => $group) {
      if ($group->slug === 'ungrouped') {
        $ungrouped = $group;
        unset($groups[$key]);
      }
    }

    // Helper function to render a grid of songs
    function render_song_grid($query_args) {
      $songs = new WP_Query($query_args);
      if ($songs->have_posts()): ?>
        <div class="song-grid">
          <?php while ($songs->have_posts()): $songs->the_post();
            // Fetch ACF Data
            $yt_url  = get_field('youtube_url'); // <-- UPDATE THIS to your exact ACF field name
            $cover   = get_field('cover_image');
            $img_url = $cover ? $cover['sizes']['thumbnail'] : 'https://via.placeholder.com/150?text=No+Cover';
            $title   = get_the_title();
          ?>
            <!-- The Magic Trigger -->
            <div class="lr-track-card lr-play-trigger" 
                 data-youtube-url="<?php echo esc_url($yt_url); ?>" 
                 data-title="<?php echo esc_attr($title); ?>" 
                 data-cover="<?php echo esc_url($img_url); ?>">
              
              <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>">
              
              <div class="lr-track-meta">
                <h3><?php echo esc_html($title); ?></h3>
                <span><?php echo get_the_date(); // Or Artist name if you have an ACF for that ?></span>
              </div>

              <div class="lr-play-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
              </div>
            </div>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      <?php endif;
    }

    // 2. Render grouped songs
    foreach ($groups as $group): ?>
      <div class="feature-group">
        <h2 class="feature-level"><?php echo esc_html($group->name); ?></h2>
        <?php 
        render_song_grid([
          'post_type'      => 'song',
          'posts_per_page' => -1,
          'tax_query'      => [
            'relation' => 'AND',
            [ 'taxonomy' => 'listening_room', 'field' => 'slug', 'terms' => 'listening-room' ],
            [ 'taxonomy' => 'song_group', 'field' => 'slug', 'terms' => $group->slug ],
          ],
          'orderby' => 'title',
          'order'   => 'ASC',
        ]);
        ?>
      </div>
    <?php endforeach; ?>

    <?php
    // 3. Render Ungrouped last
    if ($ungrouped): ?>
      <div class="feature-group ungrouped">
        <h2 class="feature-level">Ungrouped / Singles</h2>
        <?php 
        render_song_grid([
          'post_type'      => 'song',
          'posts_per_page' => -1,
          'tax_query'      => [
            'relation' => 'AND',
            [ 'taxonomy' => 'listening_room', 'field' => 'slug', 'terms' => 'listening-room' ],
            [ 'taxonomy' => 'song_group', 'field' => 'slug', 'terms' => 'ungrouped' ],
          ],
          'orderby' => 'title',
          'order'   => 'ASC',
        ]);
        ?>
      </div>
    <?php endif; ?>

  </section>
</main>

<?php get_footer(); ?>