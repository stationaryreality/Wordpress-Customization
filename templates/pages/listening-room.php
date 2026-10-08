<?php
/* Template Name: Listening Room */
get_header();
?>

<main id="primary" class="site-main">

  <div class="lr-layout">

    <!-- ====== MIDDLE: PLAYLIST ====== -->
    <div class="lr-playlist-column">
      <h1 style="font-size:32px; margin:0 0 30px;">The Listening Room</h1>

      <?php
      // Get all song groups
      $groups = get_terms([
        'taxonomy'   => 'song_group',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
      ]);

      // Separate "ungrouped" to render last
      $ungrouped = null;
      foreach ($groups as $key => $group) {
        if ($group->slug === 'ungrouped') {
          $ungrouped = $group;
          unset($groups[$key]);
        }
      }

      // Reusable function to render a song list
      function lr_render_songs($group_slug) {
        $songs = new WP_Query([
          'post_type'      => 'song',
          'posts_per_page' => -1,
          'tax_query'      => [
            'relation' => 'AND',
            ['taxonomy' => 'listening_room', 'field' => 'slug', 'terms' => 'listening-room'],
            ['taxonomy' => 'song_group',     'field' => 'slug', 'terms' => $group_slug],
          ],
          'orderby' => 'title',
          'order'   => 'ASC',
        ]);

        if (!$songs->have_posts()) return;

        while ($songs->have_posts()): $songs->the_post();
          $yt_url  = get_field('youtube_url'); // <-- confirm your ACF field name
          $cover   = get_field('cover_image');
          $img_url = $cover ? $cover['sizes']['thumbnail'] : 'https://via.placeholder.com/96?text=%E2%99%AB';
          $title   = get_the_title();
        ?>
          <button class="lr-track"
                  data-youtube-url="<?php echo esc_url($yt_url); ?>"
                  data-title="<?php echo esc_attr($title); ?>">

            <img class="lr-track-thumb" src="<?php echo esc_url($img_url); ?>" alt="">

            <div class="lr-track-info">
              <p class="lr-track-name"><?php echo esc_html($title); ?></p>
            </div>

            <span class="lr-track-play-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
            </span>
          </button>
        <?php endwhile;
        wp_reset_postdata();
      }

      // Render grouped songs
      foreach ($groups as $group): ?>
        <h3 class="lr-group-title"><?php echo esc_html($group->name); ?></h3>
        <?php lr_render_songs($group->slug); ?>
      <?php endforeach;

      // Render ungrouped last
      if ($ungrouped): ?>
        <h3 class="lr-group-title">Ungrouped</h3>
        <?php lr_render_songs('ungrouped'); ?>
      <?php endif;
      ?>
    </div>

    <!-- ====== RIGHT: STICKY PLAYER ====== -->
    <div class="lr-player-column">
      <div class="lr-player-card">

        <div class="lr-video-wrapper">
          <!-- Placeholder shown before first click -->
          <div class="lr-placeholder" id="lr-placeholder">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
            <span>Select a track to begin</span>
          </div>

          <!-- Hidden iframe, revealed on first click -->
          <div id="lr-video-container" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%;">
            <iframe id="lr-iframe"
                    src=""
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
            </iframe>
          </div>
        </div>

        <!-- Now Playing info -->
        <div class="lr-now-playing">
          <p class="lr-now-playing-label">Now Playing</p>
          <h4 class="lr-now-playing-title" id="lr-np-title">—</h4>
          <p class="lr-now-playing-group" id="lr-np-group"></p>
        </div>

      </div>
    </div>

  </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {

  const iframe      = document.getElementById('lr-iframe');
  const container   = document.getElementById('lr-video-container');
  const placeholder = document.getElementById('lr-placeholder');
  const npTitle     = document.getElementById('lr-np-title');
  const npGroup     = document.getElementById('lr-np-group');
  const tracks      = document.querySelectorAll('.lr-track');

  // Extract YouTube ID from any YouTube URL format
  function getYTID(url) {
    const m = url.match(/(?:youtu\.be\/|v\/|embed\/|watch\?v=)([a-zA-Z0-9_-]{11})/);
    return m ? m[1] : null;
  }

  tracks.forEach(function(track) {
    track.addEventListener('click', function() {
      const url   = this.getAttribute('data-youtube-url');
      const title = this.getAttribute('data-title');
      const group = this.closest('.lr-playlist-column')
                    .querySelector('.lr-group-title');
      const ytId  = getYTID(url);

      if (!ytId) return;

      // Swap the iframe source (privacy-enhanced domain)
      iframe.src = 'https://www.youtube-nocookie.com/embed/' + ytId
                 + '?autoplay=1&rel=0&modestbranding=1';

      // Reveal iframe, hide placeholder
      placeholder.style.display = 'none';
      container.style.display   = 'block';

      // Update Now Playing info
      npTitle.textContent = title;
      npGroup.textContent = group ? group.textContent : '';

      // Highlight active track
      tracks.forEach(t => t.classList.remove('is-active'));
      this.classList.add('is-active');
    });
  });

});
</script>

<?php get_footer(); ?>