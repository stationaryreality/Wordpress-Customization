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
                'yt_url'  => get_field('youtube_url'),
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

<!-- External CSS (no wp_head to keep theme CSS out) -->
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/pages/listening-room.css?v=<?php echo filemtime(get_stylesheet_directory() . '/assets/css/pages/listening-room.css'); ?>">

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
      <div class="lr-placeholder" id="lr-placeholder">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
        <span>Select a track to begin</span>
      </div>

      <div id="lr-video-container" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%;">
        <iframe id="lr-iframe"
                src=""
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
        </iframe>
      </div>
    </div>

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

      iframe.src = 'https://www.youtube-nocookie.com/embed/' + ytId
                 + '?autoplay=1&rel=0&modestbranding=1';

      placeholder.style.display = 'none';
      container.style.display   = 'block';

      npTitle.textContent = title;
      npGroup.textContent = group;

      if (cover) {
        npArt.src = cover;
        npArt.style.display = 'block';
      }

      tracks.forEach(function (t) { t.classList.remove('is-active'); });
      this.classList.add('is-active');
    });
  });

});
</script>

</body>
</html>