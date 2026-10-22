<?php
/* Template Name: Listening Room (Full Page) */

// ── Build song data before HTML output ──
$all_groups = [];

$groups = get_terms([
    'taxonomy'   => 'song_group',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

$ungrouped = null;
foreach ($groups as $key => $group) {
    if ($group->slug === 'ungrouped') {
        $ungrouped = $group;
        unset($groups[$key]);
    }
}

function lr_get_songs($group_slug) {
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

    $results = [];
    if ($songs->have_posts()) {
        while ($songs->have_posts()) {
            $songs->the_post();
            $cover   = get_field('cover_image');
            
            // 1. Clean Title: Remove trailing parentheses like " (Rusted Root)"
            $raw_title = get_the_title();
            $clean_title = preg_replace('/\s*\([^)]+\)\s*$/', '', $raw_title);
            
            // 2. Fetch Artist (Checks ACF Post Object, ACF Text, then Taxonomy fallback)
            $artist_name = 'Unknown Artist';
            $artist_field = get_field('artist'); // Update 'artist' if your ACF field name is different
            if ($artist_field) {
                if (is_object($artist_field)) $artist_name = $artist_field->post_title;
                elseif (is_array($artist_field) && isset($artist_field[0])) $artist_name = is_object($artist_field[0]) ? $artist_field[0]->post_title : $artist_field[0];
                elseif (is_string($artist_field)) $artist_name = $artist_field;
            } else {
                $terms = get_the_terms(get_the_ID(), 'artist'); // Update 'artist' if your taxonomy is different
                if ($terms && !is_wp_error($terms)) $artist_name = $terms[0]->name;
            }

            $results[] = [
                'id'      => get_the_ID(),
                'title'   => $clean_title,
                'artist'  => $artist_name,
                'yt_url'  => get_field('youtube_url'),
                'cover'   => $cover ? $cover['sizes']['thumbnail'] : '',
                'link'    => get_permalink(),
            ];
        }
        wp_reset_postdata();
    }
    return $results;
}

foreach ($groups as $group) {
    $all_groups[] = [
        'name'  => $group->name,
        'songs' => lr_get_songs($group->slug),
    ];
}

if ($ungrouped) {
    $all_groups[] = [
        'name'  => 'Ungrouped',
        'songs' => lr_get_songs('ungrouped'),
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>The Listening Room — <?php bloginfo('name'); ?></title>
<!-- External CSS loaded manually since we skip wp_head() -->
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/pages/listening-room.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- Wrap everything in .lr-app to guarantee 100% CSS isolation -->
<div class="lr-app">

<!-- ═══════ LEFT PANEL: PLAYLISTS ═══════ -->
<div class="lr-left">

  <a href="<?php echo esc_url(home_url('/')); ?>" class="lr-back">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
    Back to Home
  </a>

  <h1 class="lr-page-title">The Listening Room</h1>
  <p class="lr-page-sub">Selected tracks from the archive, chosen for mood, atmosphere, and heavy personal rotation.</p>

  <?php foreach ($all_groups as $group): ?>
    <?php if (empty($group['songs'])) continue; ?>

    <div class="lr-group">
      <h3 class="lr-group-name"><?php echo esc_html($group['name']); ?></h3>

      <?php foreach ($group['songs'] as $song): ?>
        <button class="lr-track"
                data-youtube-url="<?php echo esc_url($song['yt_url']); ?>"
                data-title="<?php echo esc_attr($song['title']); ?>"
                data-artist="<?php echo esc_attr($song['artist']); ?>"
                data-cover="<?php echo esc_url($song['cover']); ?>"
                data-group="<?php echo esc_attr($group['name']); ?>"
                data-link="<?php echo esc_url($song['link']); ?>">

          <img class="lr-track-thumb"
               src="<?php echo esc_url($song['cover'] ?: 'https://via.placeholder.com/88?text=%E2%99%AB'); ?>"
               alt="" loading="lazy">

          <div class="lr-track-info">
              <span class="lr-track-name"><?php echo esc_html($song['title']); ?></span>
              <span class="lr-track-artist"><?php echo esc_html($song['artist']); ?></span>
          </div>

          <span class="lr-track-eq">
            <span></span><span></span><span></span>
          </span>
        </button>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

</div>


<!-- ═══════ RIGHT PANEL: PLAYER ═══════ -->
<div class="lr-right">
  <div class="lr-player-shell">

    <div class="lr-video-frame">
      <!-- Placeholder -->
      <div class="lr-placeholder" id="lr-placeholder">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
        <span>Select a track to begin</span>
      </div>

      <!-- Hidden until first click -->
      <div id="lr-video-container" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%;">
        <iframe id="lr-iframe"
                src=""
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
        </iframe>
      </div>
    </div>

    <!-- Now Playing -->
    <div class="lr-now-playing">
      <img class="lr-np-art" id="lr-np-art" src="" alt="">
      <div class="lr-np-text">
        <p class="lr-np-label">Now Playing</p>
        <h4 class="lr-np-title" id="lr-np-title">—</h4>
        <p class="lr-np-artist" id="lr-np-artist"></p>
        <p class="lr-np-group" id="lr-np-group"></p>
      </div>
      
      <!-- Link to Song CPT -->
      <a href="#" id="lr-np-link" target="_blank" rel="noopener noreferrer" class="lr-np-link" title="View Song Archive Page">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
          <polyline points="15 3 21 3 21 9"></polyline>
          <line x1="10" y1="14" x2="21" y2="3"></line>
        </svg>
      </a>
    </div>

  </div>
</div>

</div> <!-- End .lr-app -->

<script>
document.addEventListener('DOMContentLoaded', function () {

  var iframe      = document.getElementById('lr-iframe');
  var container   = document.getElementById('lr-video-container');
  var placeholder = document.getElementById('lr-placeholder');
  var npTitle     = document.getElementById('lr-np-title');
  var npArtist    = document.getElementById('lr-np-artist');
  var npGroup     = document.getElementById('lr-np-group');
  var npArt       = document.getElementById('lr-np-art');
  var npLink      = document.getElementById('lr-np-link');
  var tracks      = document.querySelectorAll('.lr-track');

  function getYTID(url) {
    var m = url.match(/(?:youtu\.be\/|v\/|embed\/|watch\?v=)([a-zA-Z0-9_-]{11})/);
    return m ? m[1] : null;
  }

  tracks.forEach(function (track) {
    track.addEventListener('click', function () {
      var url    = this.getAttribute('data-youtube-url');
      var title  = this.getAttribute('data-title');
      var artist = this.getAttribute('data-artist');
      var cover  = this.getAttribute('data-cover');
      var group  = this.getAttribute('data-group');
      var link   = this.getAttribute('data-link');
      var ytId   = getYTID(url);

      if (!ytId) return;

      // Swap iframe source
      iframe.src = 'https://www.youtube-nocookie.com/embed/' + ytId
                 + '?autoplay=1&rel=0&modestbranding=1';

      // Reveal video, hide placeholder
      placeholder.style.display = 'none';
      container.style.display   = 'block';

      // Update Now Playing
      npTitle.textContent = title;
      npArtist.textContent = artist;
      npGroup.textContent = group;
      npLink.href = link;

      if (cover) {
        npArt.src = cover;
        npArt.style.display = 'block';
      } else {
        npArt.style.display = 'none';
      }

      // Highlight active track
      tracks.forEach(function (t) { t.classList.remove('is-active'); });
      this.classList.add('is-active');
    });
  });

});
</script>

</body>
</html>