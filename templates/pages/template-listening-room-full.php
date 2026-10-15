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

// Separate ungrouped
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
            $results[] = [
                'title'   => get_the_title(),
                'yt_url'  => get_field('youtube_url'), // ← confirm ACF field name
                'cover'   => $cover ? $cover['sizes']['thumbnail'] : '',
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
<style>
/* ══════════════════════════════════════════
   THE LISTENING ROOM — FULL PAGE
   ══════════════════════════════════════════ */

*, *::before, *::after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, sans-serif;
  background: #0e0e12;
  color: #e0e0e0;
  overflow: hidden; /* we handle scrolling inside panels */
  height: 100vh;
}


/* ── LAYOUT: Two fixed panels ── */
.lr-left {
  position: fixed;
  top: 0; left: 0; bottom: 0;
  width: 42%;
  overflow-y: auto;
  padding: 48px 36px 80px;
  border-right: 1px solid rgba(255,255,255,0.06);
  scrollbar-width: thin;
  scrollbar-color: #333 transparent;
}

.lr-left::-webkit-scrollbar {
  width: 5px;
}
.lr-left::-webkit-scrollbar-track {
  background: transparent;
}
.lr-left::-webkit-scrollbar-thumb {
  background: #333;
  border-radius: 10px;
}

.lr-right {
  position: fixed;
  top: 0; right: 0; bottom: 0;
  width: 58%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  background: #0a0a0e;
}


/* ── LEFT PANEL: Header ── */
.lr-back {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #666;
  text-decoration: none;
  font-size: 13px;
  letter-spacing: 0.5px;
  margin-bottom: 40px;
  transition: color 0.2s;
}

.lr-back:hover {
  color: #fff;
}

.lr-page-title {
  font-size: 28px;
  font-weight: 700;
  color: #fff;
  margin-bottom: 8px;
  letter-spacing: -0.5px;
}

.lr-page-sub {
  font-size: 14px;
  color: #666;
  margin-bottom: 48px;
  line-height: 1.6;
}


/* ── LEFT PANEL: Groups & Tracks ── */
.lr-group {
  margin-bottom: 36px;
}

.lr-group-name {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2.5px;
  color: #555;
  margin-bottom: 14px;
  padding-bottom: 8px;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}

.lr-track {
  display: flex;
  align-items: center;
  gap: 14px;
  width: 100%;
  padding: 10px 12px;
  border: none;
  border-radius: 8px;
  background: transparent;
  cursor: pointer;
  transition: background 0.15s ease;
  text-align: left;
  font-family: inherit;
}

.lr-track:hover {
  background: rgba(255,255,255,0.05);
}

.lr-track.is-active {
  background: rgba(255,255,255,0.08);
}

.lr-track.is-active .lr-track-name {
  color: #fff;
  font-weight: 600;
}

.lr-track-thumb {
  width: 44px;
  height: 44px;
  border-radius: 6px;
  object-fit: cover;
  flex-shrink: 0;
  background: #1a1a1f;
}

.lr-track-name {
  font-size: 14px;
  color: #aaa;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
}

.lr-track-eq {
  margin-left: auto;
  flex-shrink: 0;
  display: flex;
  align-items: flex-end;
  gap: 2px;
  height: 16px;
  opacity: 0;
  transition: opacity 0.2s;
}

.lr-track:hover .lr-track-eq,
.lr-track.is-active .lr-track-eq {
  opacity: 1;
}

/* Tiny animated equalizer bars */
.lr-track-eq span {
  display: block;
  width: 3px;
  background: #666;
  border-radius: 1px;
  animation: none;
}

.lr-track.is-active .lr-track-eq span {
  background: #fff;
  animation: lr-eq 0.8s ease-in-out infinite alternate;
}

.lr-track-eq span:nth-child(1) { height: 6px; animation-delay: 0s; }
.lr-track-eq span:nth-child(2) { height: 12px; animation-delay: 0.2s; }
.lr-track-eq span:nth-child(3) { height: 8px; animation-delay: 0.4s; }

@keyframes lr-eq {
  from { transform: scaleY(0.4); }
  to   { transform: scaleY(1); }
}


/* ── RIGHT PANEL: Player ── */
.lr-player-shell {
  width: 100%;
  max-width: 820px;
}

.lr-video-frame {
  position: relative;
  width: 100%;
  padding-top: 56.25%;
  border-radius: 12px;
  overflow: hidden;
  background: #111;
  box-shadow: 0 20px 80px rgba(0,0,0,0.6);
}

.lr-video-frame iframe {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  border: none;
}

/* Placeholder state */
.lr-placeholder {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  color: #444;
  font-size: 15px;
}

.lr-placeholder svg {
  opacity: 0.25;
}

/* Now Playing bar below video */
.lr-now-playing {
  margin-top: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
}

.lr-np-art {
  width: 52px;
  height: 52px;
  border-radius: 8px;
  object-fit: cover;
  background: #1a1a1f;
  display: none;
}

.lr-np-text {
  min-width: 0;
}

.lr-np-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: #555;
  margin-bottom: 3px;
}

.lr-np-title {
  font-size: 17px;
  font-weight: 600;
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.lr-np-group {
  font-size: 13px;
  color: #666;
  margin-top: 2px;
}


/* ── RESPONSIVE: Stack on mobile ── */
@media (max-width: 900px) {
  body {
    overflow: auto;
    height: auto;
  }

  .lr-left {
    position: relative;
    width: 100%;
    border-right: none;
    padding: 32px 20px 40px;
  }

  .lr-right {
    position: relative;
    width: 100%;
    padding: 24px 20px 60px;
  }

  .lr-left {
    order: 2;
  }
  .lr-right {
    order: 1;
  }

  body {
    display: flex;
    flex-direction: column;
  }
}
</style>
</head>
<body>

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
                data-cover="<?php echo esc_url($song['cover']); ?>"
                data-group="<?php echo esc_attr($group['name']); ?>">

          <img class="lr-track-thumb"
               src="<?php echo esc_url($song['cover'] ?: 'https://via.placeholder.com/88?text=%E2%99%AB'); ?>"
               alt="" loading="lazy">

          <span class="lr-track-name"><?php echo esc_html($song['title']); ?></span>

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
        <p class="lr-np-group" id="lr-np-group"></p>
      </div>
    </div>

  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

  var iframe      = document.getElementById('lr-iframe');
  var container   = document.getElementById('lr-video-container');
  var placeholder = document.getElementById('lr-placeholder');
  var npTitle     = document.getElementById('lr-np-title');
  var npGroup     = document.getElementById('lr-np-group');
  var npArt       = document.getElementById('lr-np-art');
  var tracks      = document.querySelectorAll('.lr-track');

  function getYTID(url) {
    var m = url.match(/(?:youtu\.be\/|v\/|embed\/|watch\?v=)([a-zA-Z0-9_-]{11})/);
    return m ? m[1] : null;
  }

  tracks.forEach(function (track) {
    track.addEventListener('click', function () {
      var url   = this.getAttribute('data-youtube-url');
      var title = this.getAttribute('data-title');
      var cover = this.getAttribute('data-cover');
      var group = this.getAttribute('data-group');
      var ytId  = getYTID(url);

      if (!ytId) return;

      // Swap iframe source
      iframe.src = 'https://www.youtube-nocookie.com/embed/' + ytId
                 + '?autoplay=1&rel=0&modestbranding=1';

      // Reveal video, hide placeholder
      placeholder.style.display = 'none';
      container.style.display   = 'block';

      // Update Now Playing
      npTitle.textContent = title;
      npGroup.textContent = group;

      if (cover) {
        npArt.src = cover;
        npArt.style.display = 'block';
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