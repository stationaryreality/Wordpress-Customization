<?php
/**
 * Template Name: Listening Room (Full Page)
 * Standalone app shell. No get_header(), no wp_head().
 * Inclusion rule: a track appears here if it has ANY song_group term.
 * Sources: song + rap CPTs. Artists: artist + rapper (ACF or taxonomy).
 */

if (function_exists('show_admin_bar')) { show_admin_bar(false); }

/* ───────────────────────── helpers ───────────────────────── */

if (!function_exists('lroom_field')) {
    function lroom_field($selector, $id = false) {
        return function_exists('get_field') ? get_field($selector, $id) : null;
    }
}

if (!function_exists('lroom_sub')) {
    function lroom_sub($s, $start, $len) {
        return function_exists('mb_substr') ? mb_substr($s, $start, $len) : substr($s, $start, $len);
    }
}

if (!function_exists('lroom_lower')) {
    function lroom_lower($s) {
        return function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
    }
}

if (!function_exists('lroom_upper')) {
    function lroom_upper($s) {
        return function_exists('mb_strtoupper') ? mb_strtoupper($s) : strtoupper($s);
    }
}

if (!function_exists('lroom_sortkey')) {
    function lroom_sortkey($s) {
        $s = remove_accents(wp_strip_all_tags((string) $s));
        $s = lroom_lower(trim($s));
        $s = preg_replace('/^(the|a|an)\s+/u', '', $s);
        return trim($s);
    }
}

if (!function_exists('lroom_yt_id')) {
    function lroom_yt_id($url) {
        if (!is_string($url)) return '';
        $url = trim($url);
        if ($url === '') return '';
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) return $url;
        if (preg_match('~(?:youtu\.be/|/embed/|/v/|/shorts/|/live/|[?&]v=)([a-zA-Z0-9_-]{11})~i', $url, $m)) {
            return $m[1];
        }
        return '';
    }
}

if (!function_exists('lroom_cover')) {
    function lroom_cover($field) {
        $out = ['thumb' => '', 'large' => ''];
        if (empty($field)) return $out;

        $id = 0;
        if (is_array($field)) {
            if (!empty($field['ID']))      $id = (int) $field['ID'];
            elseif (!empty($field['url'])) { $out['thumb'] = $field['url']; $out['large'] = $field['url']; return $out; }
        } elseif (is_object($field)) {
            if (!empty($field->ID)) $id = (int) $field->ID;
        } elseif (is_numeric($field)) {
            $id = (int) $field;
        } elseif (is_string($field) && preg_match('~^https?://~i', $field)) {
            $out['thumb'] = $field; $out['large'] = $field; return $out;
        }

        if (!$id) return $out;

        foreach (['thumbnail', 'medium', 'large'] as $size) {
            $src = wp_get_attachment_image_src($id, $size);
            if ($src && !$out['thumb']) $out['thumb'] = $src[0];
        }
        foreach (['medium_large', 'large', 'medium', 'full'] as $size) {
            $src = wp_get_attachment_image_src($id, $size);
            if ($src) { $out['large'] = $src[0]; break; }
        }
        if (!$out['thumb']) $out['thumb'] = $out['large'];
        if (!$out['large']) $out['large'] = $out['thumb'];
        return $out;
    }
}

if (!function_exists('lroom_artists')) {
    function lroom_artists($post_id) {
        $names = [];

        foreach (['artist', 'rapper', 'artists', 'rappers'] as $key) {
            $v = lroom_field($key, $post_id);
            if (empty($v)) continue;
            foreach ((is_array($v) ? $v : [$v]) as $item) {
                if (is_object($item) && isset($item->post_title))      $names[] = $item->post_title;
                elseif (is_array($item) && !empty($item['post_title'])) $names[] = $item['post_title'];
                elseif (is_numeric($item)) { $p = get_post((int) $item); if ($p) $names[] = $p->post_title; }
                elseif (is_string($item) && trim($item) !== '')         $names[] = $item;
            }
            if ($names) break;
        }

        if (!$names) {
            foreach (['artist', 'rapper', 'artists', 'rappers'] as $tax) {
                if (!taxonomy_exists($tax)) continue;
                $terms = get_the_terms($post_id, $tax);
                if ($terms && !is_wp_error($terms)) {
                    foreach ($terms as $t) $names[] = $t->name;
                    break;
                }
            }
        }

        $clean = [];
        foreach ($names as $n) {
            $n = trim(wp_strip_all_tags($n));
            if ($n !== '' && !in_array($n, $clean, true)) $clean[] = $n;
        }
        return $clean;
    }
}

if (!function_exists('lroom_is_qualifier')) {
    function lroom_is_qualifier($inner) {
        $l = lroom_lower($inner);
        static $keep = [
            'live','remaster','remix','mix','edit','version','mono','stereo','acoustic',
            'demo','instrumental','radio','single','album','deluxe','anniversary','bonus',
            'original','explicit','clean','feat','ft.','ft ','with','session','take',
            'reprise','extended','vip','dub','bootleg','rework','cover','unplugged',
            'mono','stereo','remixed','restored','soundtrack','theme from'
        ];
        foreach ($keep as $k) { if (strpos($l, $k) !== false) return true; }
        if (preg_match('/^\d{4}$/', trim($inner))) return true;
        return false;
    }
}

if (!function_exists('lroom_title')) {
    function lroom_title($raw) {
        $base   = trim(wp_strip_all_tags($raw));
        $suffix = '';
        for ($i = 0; $i < 4; $i++) {
            if (!preg_match('/^(.*?)\s*\(([^()]*)\)\s*$/u', $base, $m)) break;
            $inner = trim($m[2]);
            if ($inner === '') { $base = trim($m[1]); continue; }
            if (lroom_is_qualifier($inner)) $suffix = ' (' . $inner . ')' . $suffix;
            $base = trim($m[1]);
        }
        $out = trim($base . $suffix);
        return $out !== '' ? $out : trim(wp_strip_all_tags($raw));
    }
}

if (!function_exists('lroom_badge')) {
    function lroom_badge($name, $term_id) {
        $custom = lroom_field('group_badge', 'song_group_' . $term_id);
        if (!$custom) $custom = get_term_meta($term_id, 'group_badge', true);
        if (is_string($custom) && trim($custom) !== '') {
            return lroom_sub(trim(wp_strip_all_tags($custom)), 0, 3);
        }

        $n = trim(wp_strip_all_tags($name));
        $l = lroom_lower($n);

        $words = ['sixties' => '60s', 'seventies' => '70s', 'eighties' => '80s', 'nineties' => '90s', 'noughties' => '00s'];
        foreach ($words as $k => $v) { if (strpos($l, $k) !== false) return $v; }

        if (preg_match('/(?:\b(?:19|20)(\d0)(?:\'?s)?\b|\b(\d0)\'?s\b)/i', $n, $m)) {
            $d = !empty($m[1]) ? $m[1] : (isset($m[2]) ? $m[2] : '');
            if ($d !== '') return $d . 's';
        }

        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $n);
        $parts = preg_split('/\s+/', trim($clean), -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts) return lroom_upper(lroom_sub($n, 0, 3));

        $stop = ['the','and','of','a','an','for','in','on','with','&'];
        $sig  = [];
        foreach ($parts as $w) { if (!in_array(lroom_lower($w), $stop, true)) $sig[] = $w; }
        if (!$sig) $sig = $parts;

        if (count($sig) >= 2) {
            $ini = '';
            foreach (array_slice($sig, 0, 3) as $w) $ini .= lroom_sub($w, 0, 1);
            return lroom_upper($ini);
        }
        return lroom_upper(lroom_sub($sig[0], 0, 3));
    }
}

if (!function_exists('lroom_color')) {
    function lroom_color($name, $term_id) {
        $custom = lroom_field('group_color', 'song_group_' . $term_id);
        if (!$custom) $custom = get_term_meta($term_id, 'group_color', true);
        if (is_string($custom) && preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', trim($custom))) {
            return trim($custom);
        }
        $palette = ['#7C5CFF','#FF4D9D','#FFB020','#2DD4BF','#38BDF8','#A3E635','#FF6B57','#8B93FF'];
        $key     = lroom_lower(remove_accents($name));
        return $palette[abs((int) crc32($key)) % count($palette)];
    }
}

/* ───────────────────────── data build ───────────────────────── */

$lroom_types = array_values(array_filter(['song', 'rap'], 'post_type_exists'));

$lroom_tracks = [];
$lroom_terms  = [];
$lroom_index  = [];

if ($lroom_types && taxonomy_exists('song_group')) {
    $q = new WP_Query([
        'post_type'           => $lroom_types,
        'post_status'         => 'publish',
        'posts_per_page'      => -1,
        'orderby'             => 'title',
        'order'               => 'ASC',
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
        'tax_query'           => [
            ['taxonomy' => 'song_group', 'operator' => 'EXISTS'],
        ],
    ]);

    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            $pid   = get_the_ID();
            $terms = get_the_terms($pid, 'song_group');
            if (!$terms || is_wp_error($terms)) continue;

            $artists = lroom_artists($pid);
            $raw     = get_the_title();
            $cover   = lroom_cover(lroom_field('cover_image', $pid));

            $track = [
                'id'         => $pid,
                'title'      => lroom_title($raw),
                'full'       => trim(wp_strip_all_tags($raw)),
                'artist'     => $artists ? implode(', ', $artists) : 'Unknown Artist',
                'yt'         => lroom_yt_id(lroom_field('youtube_url', $pid)),
                'thumb'      => $cover['thumb'],
                'large'      => $cover['large'],
                'url'        => get_permalink($pid),
                'sortTitle'  => lroom_sortkey(lroom_title($raw)),
                'sortArtist' => lroom_sortkey($artists ? $artists[0] : 'zzz'),
                'added'      => (int) get_post_time('U', true, $pid),
                'group'      => $terms[0]->name,
            ];

            $i = count($lroom_tracks);
            $lroom_tracks[] = $track;

            foreach ($terms as $t) {
                $lroom_terms[$t->term_id] = $t;
                if (!isset($lroom_index[$t->term_id])) $lroom_index[$t->term_id] = [];
                $lroom_index[$t->term_id][] = $i;
            }
        }
        wp_reset_postdata();
    }
}

$lroom_groups = [];
foreach ($lroom_index as $tid => $idxs) {
    $t = $lroom_terms[$tid];

    usort($idxs, function ($a, $b) use ($lroom_tracks) {
        return strnatcasecmp($lroom_tracks[$a]['sortTitle'], $lroom_tracks[$b]['sortTitle']);
    });

    $lroom_groups[] = [
        'id'     => (int) $tid,
        'name'   => $t->name,
        'slug'   => $t->slug,
        'badge'  => lroom_badge($t->name, $tid),
        'color'  => lroom_color($t->name, $tid),
        'count'  => count($idxs),
        'tracks' => array_values($idxs),
        'sort'   => lroom_sortkey($t->name),
    ];
}

usort($lroom_groups, function ($a, $b) {
    $tail = ['ungrouped', 'misc', 'other', 'others', 'singles'];
    $at = in_array(lroom_lower($a['name']), $tail, true) ? 1 : 0;
    $bt = in_array(lroom_lower($b['name']), $tail, true) ? 1 : 0;
    if ($at !== $bt) return $at - $bt;
    return strnatcasecmp($a['sort'], $b['sort']);
});

foreach ($lroom_groups as &$g) { unset($g['sort']); }
unset($g);

$lroom_payload = [
    'tracks' => $lroom_tracks,
    'groups' => $lroom_groups,
    'room'   => [
        'name'   => 'The Listening Room',
        'total'  => count($lroom_tracks),
        'groups' => count($lroom_groups),
        'site'   => home_url('/'),
    ],
];

$lroom_css = get_stylesheet_directory() . '/assets/css/pages/listening-room.css';
$lroom_js  = get_stylesheet_directory() . '/assets/js/listening-room.js';
$lroom_cv  = file_exists($lroom_css) ? filemtime($lroom_css) : '1';
$lroom_jv  = file_exists($lroom_js) ? filemtime($lroom_js) : '1';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="color-scheme" content="dark">
<meta name="robots" content="noindex,follow">
<title>The Listening Room — <?php bloginfo('name'); ?></title>
<link rel="stylesheet" href="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/css/pages/listening-room.css?v=' . $lroom_cv); ?>">
</head>
<body class="lr-body">

<div class="lr-app" id="lr-app" data-state="idle" data-density="comfortable" data-labels="badge" data-art="on" data-motion="on">

  <div class="lr-bg" aria-hidden="true">
    <span class="lr-aurora lr-aurora--a"></span>
    <span class="lr-aurora lr-aurora--b"></span>
    <span class="lr-aurora lr-aurora--c"></span>
    <span class="lr-grain"></span>
    <span class="lr-vignette"></span>
  </div>

  <!-- ══════════ TOP BAR ══════════ -->
  <header class="lr-top">
    <a class="lr-back" href="<?php echo esc_url(home_url('/')); ?>">
      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
      <span>Site</span>
    </a>

    <div class="lr-brand">
      <span class="lr-mark" aria-hidden="true">
        <svg viewBox="0 0 40 40" width="34" height="34">
          <defs>
            <radialGradient id="lrMarkG" cx="50%" cy="42%" r="60%">
              <stop offset="0%" stop-color="#2A2440"/><stop offset="100%" stop-color="#0B0A12"/>
            </radialGradient>
          </defs>
          <circle cx="20" cy="20" r="19" fill="url(#lrMarkG)" stroke="#3A3160" stroke-width="1"/>
          <circle cx="20" cy="20" r="14.5" fill="none" stroke="#4B3F7A" stroke-width=".7" opacity=".8"/>
          <circle cx="20" cy="20" r="11" fill="none" stroke="#4B3F7A" stroke-width=".7" opacity=".6"/>
          <circle cx="20" cy="20" r="7.5" fill="none" stroke="#4B3F7A" stroke-width=".7" opacity=".45"/>
          <circle cx="20" cy="20" r="5" fill="#FFB020"/>
          <circle cx="20" cy="20" r="1.5" fill="#0B0A12"/>
          <path d="M20 1a19 19 0 0 1 16.5 9.6" fill="none" stroke="#FF4D9D" stroke-width="1.6" stroke-linecap="round" opacity=".85"/>
        </svg>
      </span>
      <span class="lr-brandtxt">
        <strong>The Listening Room</strong>
        <span class="lr-meta" id="lr-meta"><?php echo (int) $lroom_payload['room']['total']; ?> tracks · <?php echo (int) $lroom_payload['room']['groups']; ?> groups</span>
      </span>
    </div>

    <div class="lr-onair" id="lr-onair" data-state="idle">
      <span class="lr-onair-dot" aria-hidden="true"></span>
      <span id="lr-onair-text">Standby</span>
    </div>

    <div class="lr-master">
      <button type="button" class="lr-iconbtn" id="lr-mute" aria-pressed="false" aria-label="Mute all sound" title="Mute all sound (M)">
        <svg class="i-loud" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M15.5 8.5a5 5 0 0 1 0 7"/><path d="M18.5 5.5a9 9 0 0 1 0 13"/></svg>
        <svg class="i-mute" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M22 9l-6 6"/><path d="M16 9l6 6"/></svg>
      </button>
      <input class="lr-range lr-range--vol" id="lr-volume" type="range" min="0" max="100" step="1" value="80" aria-label="Master volume">
      <span class="lr-masterval" id="lr-volval">80</span>
    </div>
  </header>

  <!-- ══════════ COLUMN 1 · GROUPS ══════════ -->
  <nav class="lr-col lr-groups" aria-label="Track groups">
    <div class="lr-colhead">
      <h2 class="lr-coltitle">Groups</h2>
      <span class="lr-colmeta" id="lr-groupcount"><?php echo (int) $lroom_payload['room']['groups']; ?></span>
    </div>
    <div class="lr-scroll lr-grouplist" id="lr-grouplist" role="tablist" aria-orientation="vertical" aria-label="Groups"></div>
    <div class="lr-colfoot">
      <span class="lr-kbd">↑</span><span class="lr-kbd">↓</span> browse
    </div>
  </nav>

  <!-- ══════════ COLUMN 2 · TRACKS ══════════ -->
  <section class="lr-col lr-tracks" aria-label="Tracks in group">
    <div class="lr-colhead">
      <h2 class="lr-coltitle" id="lr-trackheading">Tracks</h2>
      <span class="lr-colmeta" id="lr-trackcount">0</span>
    </div>

    <div class="lr-tools">
      <div class="lr-searchwrap">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.6-3.6"/></svg>
        <input id="lr-search" type="search" placeholder="Search title or artist…" autocomplete="off" spellcheck="false" aria-label="Search tracks">
        <button type="button" id="lr-searchclear" class="lr-clear" hidden aria-label="Clear search">
          <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <button type="button" class="lr-chip" id="lr-scope" aria-pressed="false" title="Toggle search scope">This group</button>
      <select class="lr-select lr-onlymobile" id="lr-groupselect" aria-label="Choose a group"></select>
    </div>

    <div class="lr-scroll lr-tracklist" id="lr-tracklist" role="tabpanel" tabindex="-1"></div>
  </section>

  <!-- ══════════ COLUMN 3 · STAGE ══════════ -->
  <section class="lr-col lr-stage" aria-label="Player and room controls">
    <div class="lr-scroll lr-stageinner">

      <div class="lr-frame" id="lr-frame">
        <div class="lr-facade" id="lr-facade">
          <span class="lr-vinyl" aria-hidden="true">
            <svg viewBox="0 0 120 120" width="132" height="132">
              <defs>
                <radialGradient id="lrVin" cx="50%" cy="40%" r="65%">
                  <stop offset="0%" stop-color="#241F3A"/><stop offset="100%" stop-color="#0A0910"/>
                </radialGradient>
                <linearGradient id="lrSheen" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#FF4D9D" stop-opacity=".55"/>
                  <stop offset="55%" stop-color="#7C5CFF" stop-opacity="0"/>
                  <stop offset="100%" stop-color="#38BDF8" stop-opacity=".45"/>
                </linearGradient>
              </defs>
              <circle cx="60" cy="60" r="58" fill="url(#lrVin)" stroke="#3A3160" stroke-width="1"/>
              <g fill="none" stroke="#4B3F7A" stroke-width=".8" opacity=".7">
                <circle cx="60" cy="60" r="50"/><circle cx="60" cy="60" r="44"/><circle cx="60" cy="60" r="38"/>
                <circle cx="60" cy="60" r="32"/><circle cx="60" cy="60" r="26"/>
              </g>
              <circle cx="60" cy="60" r="58" fill="url(#lrSheen)"/>
              <circle cx="60" cy="60" r="17" fill="#FFB020"/>
              <circle cx="60" cy="60" r="17" fill="none" stroke="#7A4E00" stroke-width="1"/>
              <circle cx="60" cy="60" r="3.4" fill="#0A0910"/>
            </svg>
          </span>
          <p class="lr-facade-t">Nothing is playing yet</p>
          <p class="lr-facade-s">Pick a group, then choose a track. The player loads only when you press play — no trackers until then.</p>
        </div>

        <div class="lr-mountwrap" id="lr-mountwrap"><div id="lr-mount"></div></div>

        <div class="lr-framestate" id="lr-framestate" hidden>
          <p class="lr-framestate-t" id="lr-framestate-t">Playback needs a nudge</p>
          <p class="lr-framestate-s" id="lr-framestate-s">Your browser blocked autoplay.</p>
          <div class="lr-framestate-a">
            <button type="button" class="lr-btn lr-btn--accent" id="lr-framestate-btn">Play</button>
            <a class="lr-btn" id="lr-framestate-link" href="#" target="_blank" rel="noopener noreferrer">Open on YouTube</a>
          </div>
        </div>

        <span class="lr-frameglow" aria-hidden="true"></span>
      </div>

      <div class="lr-now" id="lr-now">
        <span class="lr-nowart" id="lr-nowart" aria-hidden="true">
          <span class="lr-artgen">♪</span>
        </span>
        <span class="lr-nowtxt">
          <span class="lr-nowlabel">Now playing</span>
          <h3 class="lr-nowtitle" id="lr-nowtitle">Select a track</h3>
          <p class="lr-nowartist" id="lr-nowartist">The room is quiet</p>
          <span class="lr-nowtags"><span class="lr-tag" id="lr-nowgroup" hidden></span></span>
        </span>
        <a class="lr-nowlink" id="lr-nowlink" href="#" target="_blank" rel="noopener noreferrer" hidden>
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14L21 3"/></svg>
          <span>Song page</span>
        </a>
      </div>

      <div class="lr-transport">
        <div class="lr-progress" id="lr-progress" role="slider" tabindex="0" aria-label="Seek" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-valuetext="0:00">
          <span class="lr-progbuffer" id="lr-progbuffer"></span>
          <span class="lr-progfill" id="lr-progfill"></span>
          <span class="lr-progknob" id="lr-progknob"></span>
        </div>
        <div class="lr-times"><span id="lr-tcur">0:00</span><span id="lr-tdur">0:00</span></div>

        <div class="lr-tbuttons">
          <button type="button" class="lr-iconbtn" id="lr-shuffle" aria-pressed="false" aria-label="Shuffle" title="Shuffle (S)">
            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 3h5v5"/><path d="M4 20L21 3"/><path d="M21 16v5h-5"/><path d="M15 15l6 6"/><path d="M4 4l5 5"/></svg>
          </button>
          <button type="button" class="lr-iconbtn" id="lr-prev" aria-label="Previous track" title="Previous (P)">
            <svg viewBox="0 0 24 24" width="19" height="19" fill="currentColor" aria-hidden="true"><path d="M6 5h2.2v14H6zM19 5v14l-10-7z"/></svg>
          </button>
          <button type="button" class="lr-playbtn" id="lr-play" data-playing="false" aria-label="Play" title="Play / pause (Space)">
            <svg class="i-play" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
            <svg class="i-pause" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" aria-hidden="true"><path d="M7 5h3.4v14H7zM13.6 5H17v14h-3.4z"/></svg>
          </button>
          <button type="button" class="lr-iconbtn" id="lr-next" aria-label="Next track" title="Next (N)">
            <svg viewBox="0 0 24 24" width="19" height="19" fill="currentColor" aria-hidden="true"><path d="M15.8 5H18v14h-2.2zM5 5v14l10-7z"/></svg>
          </button>
          <button type="button" class="lr-iconbtn" id="lr-repeat" data-mode="off" aria-label="Repeat off" title="Repeat (R)">
            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 2l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 22l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
            <span class="lr-repeatone">1</span>
          </button>
          <span class="lr-basicnote" id="lr-basicnote" hidden>Basic player</span>
        </div>
      </div>

      <div class="lr-controls">
        <div class="lr-controlshead">
          <h2>Room controls</h2>
          <button type="button" class="lr-minibtn" id="ctl-reset">Reset</button>
        </div>

        <div class="lr-ctlgrid">
          <label class="lr-ctl" for="ctl-density">
            <span class="lr-ctl-l">Row density</span>
            <select class="lr-select" id="ctl-density">
              <option value="compact">Compact</option>
              <option value="comfortable">Comfortable</option>
              <option value="spacious">Spacious</option>
            </select>
          </label>

          <label class="lr-ctl" for="ctl-labels">
            <span class="lr-ctl-l">Group labels</span>
            <select class="lr-select" id="ctl-labels">
              <option value="badge">Badge + name</option>
              <option value="name">Names only</option>
              <option value="badge-only">Badges only</option>
            </select>
          </label>

          <label class="lr-ctl" for="ctl-sort">
            <span class="lr-ctl-l">Sort tracks</span>
            <select class="lr-select" id="ctl-sort">
              <option value="title">Title A–Z</option>
              <option value="artist">Artist A–Z</option>
              <option value="newest">Recently added</option>
            </select>
          </label>

          <label class="lr-ctl" for="ctl-accent">
            <span class="lr-ctl-l">Accent hue <b class="lr-ctl-v" id="ctl-accent-v">268°</b></span>
            <input class="lr-range" id="ctl-accent" type="range" min="0" max="360" step="1" value="268">
          </label>

          <label class="lr-ctl" for="ctl-glow">
            <span class="lr-ctl-l">Room glow <b class="lr-ctl-v" id="ctl-glow-v">70%</b></span>
            <input class="lr-range" id="ctl-glow" type="range" min="0" max="100" step="1" value="70">
          </label>

          <label class="lr-ctl" for="ctl-scale">
            <span class="lr-ctl-l">Text size <b class="lr-ctl-v" id="ctl-scale-v">100%</b></span>
            <input class="lr-range" id="ctl-scale" type="range" min="90" max="125" step="1" value="100">
          </label>

          <div class="lr-ctl lr-ctl--switch">
            <span class="lr-ctl-l">List artwork</span>
            <button type="button" class="lr-switch" id="ctl-art" role="switch" aria-checked="true"><span></span></button>
          </div>

          <div class="lr-ctl lr-ctl--switch">
            <span class="lr-ctl-l">Motion</span>
            <button type="button" class="lr-switch" id="ctl-motion" role="switch" aria-checked="true"><span></span></button>
          </div>

          <div class="lr-ctl lr-ctl--switch">
            <span class="lr-ctl-l">Auto-advance</span>
            <button type="button" class="lr-switch" id="ctl-autoplay" role="switch" aria-checked="true"><span></span></button>
          </div>
        </div>

        <div class="lr-ctlactions">
          <button type="button" class="lr-btn lr-btn--accent" id="ctl-surprise">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l1.9 4.6L18.5 9l-4.6 1.9L12 15.5l-1.9-4.6L5.5 9l4.6-1.4z"/><path d="M18 16l.9 2.1L21 19l-2.1.9L18 22l-.9-2.1L15 19l2.1-.9z"/></svg>
            Surprise me
          </button>
          <label class="lr-sr" for="ctl-format">Export format</label>
          <select class="lr-select lr-select--sm" id="ctl-format">
            <option value="m3u">M3U</option>
            <option value="json">JSON</option>
            <option value="csv">CSV</option>
          </select>
          <button type="button" class="lr-btn" id="ctl-export">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M4 21h16"/></svg>
            Export
          </button>
        </div>

        <p class="lr-keyhint"><span class="lr-kbd">Space</span> play · <span class="lr-kbd">N</span>/<span class="lr-kbd">P</span> track · <span class="lr-kbd">M</span> mute · <span class="lr-kbd">/</span> search · <span class="lr-kbd">←</span><span class="lr-kbd">→</span> seek</p>
      </div>

    </div>
  </section>
</div>

<p class="lr-sr" role="status" aria-live="polite" id="lr-live"></p>

<noscript>
  <div class="lr-noscript">
    <h1>The Listening Room</h1>
    <p>Playback needs JavaScript. Here is the full archive as plain links.</p>
    <?php if (empty($lroom_groups)) : ?>
      <p>No tracks have been assigned to a group yet.</p>
    <?php else : ?>
      <?php foreach ($lroom_groups as $g) : ?>
        <section>
          <h2><?php echo esc_html($g['name']); ?> <small>(<?php echo (int) $g['count']; ?>)</small></h2>
          <ul>
            <?php foreach ($g['tracks'] as $i) : $t = $lroom_tracks[$i]; ?>
              <li>
                <a href="<?php echo esc_url($t['url']); ?>"><?php echo esc_html($t['title']); ?></a>
                — <?php echo esc_html($t['artist']); ?>
                <?php if ($t['yt']) : ?>
                  · <a href="<?php echo esc_url('https://www.youtube-nocookie.com/watch?v=' . $t['yt']); ?>" rel="noopener noreferrer">video</a>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</noscript>

<script type="application/json" id="lr-data"><?php echo wp_json_encode($lroom_payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?></script>
<script src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/js/listening-room.js?v=' . $lroom_jv); ?>" defer></script>
</body>
</html>