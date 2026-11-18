/* The Listening Room — single-source-of-truth app controller. */
(function () {
  'use strict';

  var app = document.getElementById('lr-app');
  var dataEl = document.getElementById('lr-data');
  if (!app || !dataEl) return;

  var DATA;
  try { DATA = JSON.parse(dataEl.textContent || dataEl.innerHTML); }
  catch (e) { DATA = { tracks: [], groups: [], room: {} }; }

  var TRACKS = DATA.tracks || [];
  var GROUPS = DATA.groups || [];

  var $ = function (id) { return document.getElementById(id); };
  var el = {
    meta: $('lr-meta'), onair: $('lr-onair'), onairText: $('lr-onair-text'),
    mute: $('lr-mute'), volume: $('lr-volume'), volval: $('lr-volval'),
    groupList: $('lr-grouplist'), groupCount: $('lr-groupcount'),
    trackHeading: $('lr-trackheading'), trackCount: $('lr-trackcount'),
    trackList: $('lr-tracklist'), search: $('lr-search'), searchClear: $('lr-searchclear'),
    scope: $('lr-scope'), groupSelect: $('lr-groupselect'),
    facade: $('lr-facade'), mountwrap: $('lr-mountwrap'),
    frameState: $('lr-framestate'), frameStateT: $('lr-framestate-t'),
    frameStateS: $('lr-framestate-s'), frameStateBtn: $('lr-framestate-btn'), frameStateLink: $('lr-framestate-link'),
    nowArt: $('lr-nowart'), nowTitle: $('lr-nowtitle'), nowArtist: $('lr-nowartist'),
    nowGroup: $('lr-nowgroup'), nowLink: $('lr-nowlink'),
    progress: $('lr-progress'), buffer: $('lr-progbuffer'), fill: $('lr-progfill'), knob: $('lr-progknob'),
    tcur: $('lr-tcur'), tdur: $('lr-tdur'),
    shuffle: $('lr-shuffle'), prev: $('lr-prev'), play: $('lr-play'), next: $('lr-next'), repeat: $('lr-repeat'),
    basicNote: $('lr-basicnote'), live: $('lr-live')
  };

  /* ── settings ── */
  var KEY = 'lr-room-v1';
  var DEF = {
    density: 'comfortable', labels: 'badge', sort: 'title', format: 'm3u',
    accent: 268, glow: 70, scale: 100,
    art: true, motion: true, autoplay: true,
    volume: 80, muted: false, shuffle: false, repeat: 'off'
  };
  var SET = Object.assign({}, DEF);
  try {
    var saved = JSON.parse(localStorage.getItem(KEY) || '{}');
    Object.keys(DEF).forEach(function (k) {
      if (saved[k] === undefined || saved[k] === null) return;
      if (typeof DEF[k] === 'boolean' && typeof saved[k] === 'boolean') SET[k] = saved[k];
      else if (typeof DEF[k] === 'number' && isFinite(saved[k])) SET[k] = Number(saved[k]);
      else if (typeof DEF[k] === 'string' && typeof saved[k] === 'string') SET[k] = saved[k];
    });
  } catch (e) {}
  SET.accent = clamp(SET.accent, 0, 360);
  SET.glow = clamp(SET.glow, 0, 100);
  SET.scale = clamp(SET.scale, 90, 125);
  SET.volume = clamp(SET.volume, 0, 100);
  if (['compact', 'comfortable', 'spacious'].indexOf(SET.density) < 0) SET.density = DEF.density;
  if (['badge', 'name', 'badge-only'].indexOf(SET.labels) < 0) SET.labels = DEF.labels;
  if (['title', 'artist', 'newest'].indexOf(SET.sort) < 0) SET.sort = DEF.sort;
  if (['m3u', 'json', 'csv'].indexOf(SET.format) < 0) SET.format = DEF.format;
  if (['off', 'all', 'one'].indexOf(SET.repeat) < 0) SET.repeat = DEF.repeat;

  function clamp(v, a, b) { v = Number(v); if (!isFinite(v)) return a; return Math.min(b, Math.max(a, v)); }
  function save() { try { localStorage.setItem(KEY, JSON.stringify(SET)); } catch (e) {} }

  /* ── state ── */
  var S = {
    gid: GROUPS.length ? GROUPS[0].id : null,
    q: '', scope: 'group',
    order: [], shuf: null, shufKey: '',
    playing: -1, playingGroup: '',
    state: 'idle', basic: false, ready: false, dur: 0, dragging: false
  };

  var player = null, apiState = 'idle', apiTimer = null, tickTimer = null, pending = '';

  /* ── helpers ── */
  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function norm(s) {
    s = String(s || '').toLowerCase();
    try { s = s.normalize('NFD').replace(/[\u0300-\u036f]/g, ''); } catch (e) {}
    return s;
  }
  function hue(s) { var h = 0; s = String(s || ''); for (var i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) >>> 0; return h % 360; }
  function fmt(t) {
    t = Math.max(0, Math.floor(Number(t) || 0));
    var m = Math.floor(t / 60), s = t % 60;
    return m + ':' + (s < 10 ? '0' : '') + s;
  }
  function groupById(id) { for (var i = 0; i < GROUPS.length; i++) if (GROUPS[i].id === id) return GROUPS[i]; return null; }
  function announce(msg) { if (el.live) el.live.textContent = msg; }
  function ytUrl(id) { return 'https://www.youtube-nocookie.com/watch?v=' + id; }

  /* ── settings → DOM ── */
  function applySettings() {
    app.dataset.density = SET.density;
    app.dataset.labels = SET.labels;
    app.dataset.art = SET.art ? 'on' : 'off';
    app.dataset.motion = SET.motion ? 'on' : 'off';
    app.style.setProperty('--accent-h', SET.accent);
    app.style.setProperty('--glow', (SET.glow / 100).toFixed(2));
    app.style.setProperty('--ui', (SET.scale / 100).toFixed(2));

    setVal('ctl-density', SET.density); setVal('ctl-labels', SET.labels);
    setVal('ctl-sort', SET.sort); setVal('ctl-format', SET.format);
    setVal('ctl-accent', SET.accent); setVal('ctl-glow', SET.glow); setVal('ctl-scale', SET.scale);
    setText('ctl-accent-v', SET.accent + '°'); setText('ctl-glow-v', SET.glow + '%'); setText('ctl-scale-v', SET.scale + '%');
    setSwitch('ctl-art', SET.art); setSwitch('ctl-motion', SET.motion); setSwitch('ctl-autoplay', SET.autoplay);

    if (el.volume) el.volume.value = SET.volume;
    setText('lr-volval', SET.muted ? '—' : SET.volume);
    if (el.mute) {
      el.mute.setAttribute('aria-pressed', SET.muted ? 'true' : 'false');
      el.mute.setAttribute('aria-label', SET.muted ? 'Unmute all sound' : 'Mute all sound');
    }
    if (el.shuffle) el.shuffle.setAttribute('aria-pressed', SET.shuffle ? 'true' : 'false');
    if (el.repeat) {
      el.repeat.dataset.mode = SET.repeat;
      el.repeat.setAttribute('aria-label', 'Repeat ' + SET.repeat);
    }
  }
  function setVal(id, v) { var n = $(id); if (n) n.value = v; }
  function setText(id, v) { var n = $(id); if (n) n.textContent = v; }
  function setSwitch(id, on) { var n = $(id); if (n) n.setAttribute('aria-checked', on ? 'true' : 'false'); }

  /* ── groups ── */
  function renderGroups() {
    if (!el.groupList) return;
    if (!GROUPS.length) {
      el.groupList.innerHTML = '<div class="lr-empty"><p>No groups yet</p><small>Assign a Song Group to a song or rap track and it will appear here.</small></div>';
      if (el.groupSelect) el.groupSelect.innerHTML = '<option>No groups</option>';
      return;
    }
    var html = '';
    GROUPS.forEach(function (g, i) {
      html += '<button type="button" class="lr-gitem" role="tab" id="lr-tab-' + g.id + '" data-gid="' + g.id + '"' +
        ' aria-selected="false" aria-controls="lr-tracklist" tabindex="-1" style="--c:' + esc(g.color) + '"' +
        ' title="' + esc(g.name) + ' · ' + g.count + ' tracks">' +
        '<span class="lr-badge" aria-hidden="true">' + esc(g.badge) + '</span>' +
        '<span class="lr-gname">' + esc(g.name) + '</span>' +
        '<span class="lr-gcount">' + g.count + '</span></button>';
    });
    el.groupList.innerHTML = html;

    if (el.groupSelect) {
      el.groupSelect.innerHTML = GROUPS.map(function (g) {
        return '<option value="' + g.id + '">' + esc(g.name) + ' (' + g.count + ')</option>';
      }).join('');
    }
    if (el.groupCount) el.groupCount.textContent = GROUPS.length;
    syncGroupUI();
  }

  function syncGroupUI() {
    var items = el.groupList ? el.groupList.querySelectorAll('.lr-gitem') : [];
    Array.prototype.forEach.call(items, function (b) {
      var on = Number(b.dataset.gid) === S.gid;
      b.setAttribute('aria-selected', on ? 'true' : 'false');
      b.tabIndex = on ? 0 : -1;
    });
    if (el.groupSelect && String(el.groupSelect.value) !== String(S.gid)) el.groupSelect.value = S.gid;
    var g = groupById(S.gid);
    if (el.trackHeading) {
      el.trackHeading.textContent = (S.scope === 'all' && S.q) ? 'All groups' : (g ? g.name : 'Tracks');
      el.trackHeading.title = el.trackHeading.textContent;
    }
    if (el.trackList && g) el.trackList.setAttribute('aria-labelledby', 'lr-tab-' + g.id);
  }

  /* ── track list ── */
  function computeOrder() {
    var g = groupById(S.gid);
    var pool;
    if (S.q && S.scope === 'all') {
      pool = TRACKS.map(function (_, i) { return i; });
    } else {
      pool = g ? g.tracks.slice() : [];
    }
    if (S.q) {
      var tokens = norm(S.q).split(/\s+/).filter(Boolean);
      pool = pool.filter(function (i) {
        var t = TRACKS[i];
        var hay = norm(t.title + ' ' + t.full + ' ' + t.artist + ' ' + t.group);
        for (var k = 0; k < tokens.length; k++) if (hay.indexOf(tokens[k]) < 0) return false;
        return true;
      });
    }
    pool.sort(function (a, b) {
      var A = TRACKS[a], B = TRACKS[b], r;
      if (SET.sort === 'artist') {
        r = A.sortArtist.localeCompare(B.sortArtist, undefined, { numeric: true, sensitivity: 'base' });
        if (r !== 0) return r;
      } else if (SET.sort === 'newest') {
        if (A.added !== B.added) return B.added - A.added;
      }
      return A.sortTitle.localeCompare(B.sortTitle, undefined, { numeric: true, sensitivity: 'base' });
    });
    S.order = pool;
    S.shufKey = '';
  }

  function artHTML(t, cls) {
    if (t.thumb) {
      return '<span class="' + cls + '"><img src="' + esc(t.thumb) + '" alt="" loading="lazy" decoding="async" width="60" height="60"></span>';
    }
    var ini = (t.title || '?').trim().charAt(0).toUpperCase();
    return '<span class="' + cls + '"><span class="lr-artgen" style="--gc:hsl(' + hue(t.title) + ' 80% 70%)">' + esc(ini) + '</span></span>';
  }

  function renderTracks() {
    if (!el.trackList) return;
    computeOrder();
    syncGroupUI();

    if (el.trackCount) el.trackCount.textContent = S.order.length;
    if (el.searchClear) el.searchClear.hidden = !S.q;

    if (!GROUPS.length) {
      el.trackList.innerHTML = '<div class="lr-empty"><p>Nothing to play yet</p><small>Tracks appear here as soon as they belong to a Song Group.</small></div>';
      updateTransportEnabled();
      return;
    }
    if (!S.order.length) {
      el.trackList.innerHTML = '<div class="lr-empty">' +
        '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.6-3.6"/></svg>' +
        '<p>No matches</p><small>Nothing here matches “' + esc(S.q) + '”.</small>' +
        '<button type="button" class="lr-btn" data-act="clear">Clear search</button></div>';
      updateTransportEnabled();
      return;
    }

    var showGroup = (S.scope === 'all' && !!S.q);
    var html = '';
    S.order.forEach(function (i) {
      var t = TRACKS[i];
      var active = (i === S.playing);
      if (!t.yt) {
        html += '<div class="lr-row is-na">' + artHTML(t, 'lr-row-art') +
          '<span class="lr-row-txt"><span class="lr-row-t">' + esc(t.title) + '</span>' +
          '<span class="lr-row-a">' + esc(t.artist) + '</span></span>' +
          '<a class="lr-row-na" href="' + esc(t.url) + '" target="_blank" rel="noopener noreferrer">No video</a></div>';
        return;
      }
      html += '<button type="button" class="lr-row' + (active ? ' is-active' : '') + '" data-i="' + i + '"' +
        ' data-g="' + esc(t.group) + '" aria-current="' + (active ? 'true' : 'false') + '"' +
        ' title="' + esc(t.full) + ' — ' + esc(t.artist) + '"' +
        ' aria-label="Play ' + esc(t.title) + ' by ' + esc(t.artist) + '">' +
        artHTML(t, 'lr-row-art') +
        '<span class="lr-row-txt"><span class="lr-row-t">' + esc(t.title) + '</span>' +
        '<span class="lr-row-a">' + esc(t.artist) + '</span></span>' +
        (showGroup ? '<span class="lr-row-g">' + esc(t.group) + '</span>' : '') +
        '<span class="lr-eq" aria-hidden="true"><i></i><i></i><i></i></span></button>';
    });
    el.trackList.innerHTML = html;
    updateTransportEnabled();
  }

  function syncRows() {
    if (!el.trackList) return;
    var rows = el.trackList.querySelectorAll('.lr-row[data-i]');
    Array.prototype.forEach.call(rows, function (r) {
      var on = Number(r.dataset.i) === S.playing;
      r.classList.toggle('is-active', on);
      r.setAttribute('aria-current', on ? 'true' : 'false');
    });
  }

  function selectGroup(gid, opts) {
    opts = opts || {};
    if (!groupById(gid)) return;
    S.gid = gid;
    if (!opts.keepQuery) { S.q = ''; if (el.search) el.search.value = ''; }
    renderTracks();
    if (el.trackList && opts.scroll !== false) el.trackList.scrollTop = 0;
    var g = groupById(gid);
    if (g) announce(g.name + ', ' + g.count + ' tracks');
  }

  /* ── player ── */
  function setState(s) {
    S.state = s;
    app.dataset.state = s;
    if (el.onair) el.onair.dataset.state = s;
    var label = { idle: 'Standby', loading: 'Loading', playing: 'On air', paused: 'Paused', ended: 'Ended', error: 'Error' }[s] || 'Standby';
    if (el.onairText) el.onairText.textContent = label;
    if (el.play) {
      var p = (s === 'playing');
      el.play.dataset.playing = p ? 'true' : 'false';
      el.play.setAttribute('aria-label', p ? 'Pause' : 'Play');
    }
  }

  function hideFacade() { if (el.facade) el.facade.hidden = true; }
  function hideFrameState() { if (el.frameState) el.frameState.hidden = true; }

  function showFrameState(title, msg, btnLabel, action, link) {
    if (!el.frameState) return;
    el.frameStateT.textContent = title;
    el.frameStateS.textContent = msg;
    el.frameStateBtn.textContent = btnLabel;
    el.frameStateBtn.onclick = action;
    if (link) { el.frameStateLink.href = link; el.frameStateLink.hidden = false; }
    else { el.frameStateLink.hidden = true; }
    el.frameState.hidden = false;
  }

  function enableBasic() {
    if (S.basic) return;
    S.basic = true;
    S.ready = false;
    if (apiTimer) { clearTimeout(apiTimer); apiTimer = null; }
    if (tickTimer) { clearInterval(tickTimer); tickTimer = null; }
    if (el.basicNote) el.basicNote.hidden = false;
    [el.shuffle, el.prev, el.next, el.repeat].forEach(function (b) { if (b) b.disabled = false; });
    if (el.mute) { el.mute.disabled = true; el.mute.title = 'Volume control unavailable in basic mode'; }
    if (el.volume) el.volume.disabled = true;
    if (el.progress) { el.progress.style.pointerEvents = 'none'; el.progress.setAttribute('aria-disabled', 'true'); }
    if (el.tdur) el.tdur.textContent = '—';
    if (pending) mountBasic(pending);
    announce('Basic player mode. Track switching still works.');
  }

  function mountBasic(id) {
    hideFacade(); hideFrameState();
    if (!el.mountwrap) return;
    el.mountwrap.innerHTML = '<iframe src="https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id) +
      '?autoplay=1&rel=0&modestbranding=1&playsinline=1" title="YouTube player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    setState('playing');
  }

  function loadApi() {
    apiState = 'loading';
    window.onYouTubeIframeAPIReady = function () {
      if (S.basic) return;
      apiState = 'ready';
      if (apiTimer) { clearTimeout(apiTimer); apiTimer = null; }
      createPlayer();
    };
    var s = document.createElement('script');
    s.src = 'https://www.youtube.com/iframe_api';
    s.async = true;
    s.onerror = function () { enableBasic(); };
    document.head.appendChild(s);
    apiTimer = setTimeout(function () { if (apiState !== 'ready') enableBasic(); }, 7000);
  }

  function createPlayer() {
    if (S.basic || !window.YT || !window.YT.Player) { enableBasic(); return; }
    hideFacade();
    try {
      player = new window.YT.Player('lr-mount', {
        host: 'https://www.youtube-nocookie.com',
        videoId: pending,
        playerVars: {
          autoplay: 1, rel: 0, modestbranding: 1, playsinline: 1,
          iv_load_policy: 3, origin: window.location.origin
        },
        events: {
          onReady: function () {
            S.ready = true;
            hideFrameState();
            applyVolume();
            if (pending) {
              try {
                var vd = player.getVideoData ? player.getVideoData() : null;
                if (!vd || vd.video_id !== pending) player.loadVideoById(pending);
                else player.playVideo();
              } catch (e) {}
            }
            startTick();
            updateTransportEnabled();
          },
          onStateChange: function (e) { onYtState(e.data); },
          onError: function () {
            setState('error');
            var t = TRACKS[S.playing];
            showFrameState('This video will not play', 'It may be private, removed, or blocked from embedding.', 'Skip to next', function () { hideFrameState(); next(); }, t && t.yt ? ytUrl(t.yt) : null);
          },
          onAutoplayBlocked: function () {
            setState('paused');
            showFrameState('Autoplay was blocked', 'Your browser needs one more click to start the audio.', 'Play now', function () {
              hideFrameState();
              try { player.playVideo(); } catch (e) {}
            }, null);
          }
        }
      });
    } catch (e) { enableBasic(); }
  }

  function onYtState(d) {
    if (d === 1) { setState('playing'); hideFrameState(); }
    else if (d === 2) { setState('paused'); }
    else if (d === 3) { setState('loading'); }
    else if (d === 5) { setState('paused'); }
    else if (d === 0) { onEnded(); }
  }

  function onEnded() {
    if (SET.repeat === 'one') { try { player.seekTo(0, true); player.playVideo(); } catch (e) {} return; }
    if (!SET.autoplay) { setState('ended'); return; }
    var o = playbackOrder();
    var pos = o.indexOf(S.playing);
    if (SET.repeat === 'off' && pos === o.length - 1) { setState('ended'); return; }
    next(true);
  }

  function ensure(id) {
    pending = id;
    hideFacade(); hideFrameState();
    if (S.basic) { mountBasic(id); return; }
    if (player && S.ready) { try { player.loadVideoById(id); setState('loading'); return; } catch (e) { enableBasic(); return; } }
    setState('loading');
    if (apiState === 'idle') loadApi();
  }

  function startTick() {
    if (tickTimer) clearInterval(tickTimer);
    tickTimer = setInterval(tick, 250);
  }

  function tick() {
    if (S.basic || !player || !S.ready || S.dragging) return;
    var d = 0, c = 0, b = 0;
    try {
      d = player.getDuration() || 0;
      c = player.getCurrentTime() || 0;
      b = player.getVideoLoadedFraction() || 0;
    } catch (e) { return; }
    S.dur = d;
    var pct = d > 0 ? Math.min(100, (c / d) * 100) : 0;
    setProgress(pct, b * 100);
    if (el.tcur) el.tcur.textContent = fmt(c);
    if (el.tdur) el.tdur.textContent = d > 0 ? fmt(d) : '0:00';
    if (el.progress) {
      el.progress.setAttribute('aria-valuenow', Math.round(pct));
      el.progress.setAttribute('aria-valuetext', fmt(c) + ' of ' + fmt(d));
    }
  }

  function setProgress(pct, buf) {
    pct = Math.max(0, Math.min(100, pct || 0));
    if (el.fill) el.fill.style.width = pct + '%';
    if (el.knob) el.knob.style.left = pct + '%';
    if (el.buffer && buf != null) el.buffer.style.width = Math.max(0, Math.min(100, buf)) + '%';
  }

  function applyVolume() {
    if (S.basic || !player || !S.ready) return;
    try {
      player.setVolume(SET.volume);
      if (SET.muted) player.mute(); else player.unMute();
    } catch (e) {}
  }

  /* ── playback ── */
  function play(i, groupName) {
    var t = TRACKS[i];
    if (!t || !t.yt) return;
    S.playing = i;
    S.playingGroup = groupName || t.group;
    ensure(t.yt);
    updateNowPlaying();
    syncRows();
    updateTransportEnabled();
    announce('Now playing ' + t.title + ' by ' + t.artist);
  }

  function updateNowPlaying() {
    var t = TRACKS[S.playing];
    if (!t) return;
    if (el.nowTitle) { el.nowTitle.textContent = t.title; el.nowTitle.title = t.full; }
    if (el.nowArtist) el.nowArtist.textContent = t.artist;
    if (el.nowGroup) { el.nowGroup.textContent = S.playingGroup || t.group; el.nowGroup.hidden = false; }
    if (el.nowLink) { el.nowLink.href = t.url; el.nowLink.hidden = false; }
    if (el.nowArt) {
      el.nowArt.innerHTML = t.large || t.thumb
        ? '<img src="' + esc(t.large || t.thumb) + '" alt="" decoding="async" width="60" height="60">'
        : '<span class="lr-artgen" style="--gc:hsl(' + hue(t.title) + ' 80% 70%)">' + esc((t.title || '?').charAt(0).toUpperCase()) + '</span>';
    }
  }

  function orderKey() { return S.order.join(','); }
  function playbackOrder() {
    if (!SET.shuffle) return S.order;
    var k = orderKey();
    if (!S.shuf || S.shufKey !== k) {
      var a = S.order.slice();
      for (var i = a.length - 1; i > 0; i--) { var j = Math.floor(Math.random() * (i + 1)); var tmp = a[i]; a[i] = a[j]; a[j] = tmp; }
      S.shuf = a; S.shufKey = k;
    }
    return S.shuf;
  }

  function step(dir, auto) {
    var o = playbackOrder();
    if (!o.length) return;
    var pos = o.indexOf(S.playing);
    var nextPos;
    if (pos < 0) nextPos = dir > 0 ? 0 : o.length - 1;
    else {
      nextPos = pos + dir;
      if (nextPos >= o.length) { if (SET.repeat === 'off' && auto) { setState('ended'); return; } nextPos = 0; }
      if (nextPos < 0) nextPos = o.length - 1;
    }
    var idx = o[nextPos];
    var t = TRACKS[idx];
    play(idx, t ? t.group : '');
  }
  function next(auto) { step(1, !!auto); }
  function prev() { step(-1, false); }

  function togglePlay() {
    if (S.playing < 0) {
      var o = playbackOrder();
      if (o.length) play(o[0], TRACKS[o[0]].group);
      return;
    }
    if (S.basic) { mountBasic(TRACKS[S.playing].yt); return; }
    if (!player || !S.ready) return;
    try {
      if (S.state === 'playing') player.pauseVideo();
      else player.playVideo();
    } catch (e) {}
  }

  function updateTransportEnabled() {
    var has = S.order.length > 0 || S.playing >= 0;
    if (el.play) el.play.disabled = !has;
    if (el.prev) el.prev.disabled = !has;
    if (el.next) el.next.disabled = !has;
  }

  /* ── export / actions ── */
  function currentExportRows() {
    return S.order.map(function (i) { return TRACKS[i]; }).filter(function (t) { return !!t.yt; });
  }

  function exportList() {
    var rows = currentExportRows();
    if (!rows.length) { announce('Nothing to export.'); return; }
    var g = groupById(S.gid);
    var name = 'listening-room-' + ((g && g.slug) || 'selection');
    var body, mime, ext;

    if (SET.format === 'json') {
      body = JSON.stringify({
        room: 'The Listening Room', group: g ? g.name : 'Selection', exported: new Date().toISOString(),
        tracks: rows.map(function (t) { return { title: t.title, artist: t.artist, group: t.group, video: ytUrl(t.yt), page: t.url }; })
      }, null, 2);
      mime = 'application/json'; ext = 'json';
    } else if (SET.format === 'csv') {
      var q = function (v) { return '"' + String(v).replace(/"/g, '""') + '"'; };
      body = 'Title,Artist,Group,Video,Page\n' + rows.map(function (t) {
        return [q(t.title), q(t.artist), q(t.group), q(ytUrl(t.yt)), q(t.url)].join(',');
      }).join('\n');
      mime = 'text/csv'; ext = 'csv';
    } else {
      body = '#EXTM3U\n' + rows.map(function (t) {
        return '#EXTINF:-1,' + t.artist + ' - ' + t.title + '\n' + ytUrl(t.yt);
      }).join('\n') + '\n';
      mime = 'audio/x-mpegurl'; ext = 'm3u8';
    }

    try {
      var blob = new Blob([body], { type: mime + ';charset=utf-8' });
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url; a.download = name + '.' + ext;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
      setTimeout(function () { URL.revokeObjectURL(url); }, 1500);
      announce('Exported ' + rows.length + ' tracks as ' + ext.toUpperCase());
    } catch (e) { announce('Export failed in this browser.'); }
  }

  function surprise() {
    if (!GROUPS.length) return;
    var playable = GROUPS.filter(function (g) { return g.tracks.some(function (i) { return TRACKS[i].yt; }); });
    if (!playable.length) return;
    var g = playable[Math.floor(Math.random() * playable.length)];
    selectGroup(g.id);
    var pool = S.order.filter(function (i) { return TRACKS[i].yt; });
    if (!pool.length) return;
    var pick = pool[Math.floor(Math.random() * pool.length)];
    play(pick, g.name);
    var row = el.trackList.querySelector('.lr-row[data-i="' + pick + '"]');
    if (row && row.scrollIntoView) row.scrollIntoView({ block: 'nearest', behavior: SET.motion ? 'smooth' : 'auto' });
  }

  function resetSettings() {
    SET = Object.assign({}, DEF);
    applySettings(); save(); applyVolume(); renderTracks();
    announce('Room controls reset.');
  }

  /* ── events ── */
  if (el.groupList) {
    el.groupList.addEventListener('click', function (e) {
      var b = e.target.closest('.lr-gitem');
      if (b) selectGroup(Number(b.dataset.gid));
    });
    el.groupList.addEventListener('keydown', function (e) {
      var keys = ['ArrowDown', 'ArrowUp', 'Home', 'End'];
      if (keys.indexOf(e.key) < 0) return;
      e.preventDefault(); e.stopPropagation();
      var idx = GROUPS.findIndex(function (g) { return g.id === S.gid; });
      if (idx < 0) idx = 0;
      if (e.key === 'ArrowDown') idx = (idx + 1) % GROUPS.length;
      if (e.key === 'ArrowUp') idx = (idx - 1 + GROUPS.length) % GROUPS.length;
      if (e.key === 'Home') idx = 0;
      if (e.key === 'End') idx = GROUPS.length - 1;
      selectGroup(GROUPS[idx].id);
      var btn = el.groupList.querySelector('.lr-gitem[data-gid="' + GROUPS[idx].id + '"]');
      if (btn) { btn.focus(); btn.scrollIntoView({ block: 'nearest' }); }
    });
  }

  if (el.groupSelect) el.groupSelect.addEventListener('change', function () { selectGroup(Number(this.value)); });

  if (el.trackList) {
    el.trackList.addEventListener('click', function (e) {
      var clear = e.target.closest('[data-act="clear"]');
      if (clear) { S.q = ''; if (el.search) { el.search.value = ''; el.search.focus(); } renderTracks(); return; }
      var row = e.target.closest('.lr-row[data-i]');
      if (!row) return;
      play(Number(row.dataset.i), row.dataset.g);
    });
    el.trackList.addEventListener('keydown', function (e) {
      if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') return;
      var rows = Array.prototype.slice.call(el.trackList.querySelectorAll('.lr-row[data-i]'));
      var at = rows.indexOf(document.activeElement);
      if (at < 0) return;
      e.preventDefault();
      var n = e.key === 'ArrowDown' ? Math.min(rows.length - 1, at + 1) : Math.max(0, at - 1);
      rows[n].focus();
    });
  }

  var searchTimer = null;
  if (el.search) {
    el.search.addEventListener('input', function () {
      var v = this.value;
      if (searchTimer) clearTimeout(searchTimer);
      searchTimer = setTimeout(function () { S.q = v.trim(); renderTracks(); }, 90);
    });
    el.search.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { this.value = ''; S.q = ''; renderTracks(); }
    });
  }
  if (el.searchClear) el.searchClear.addEventListener('click', function () {
    S.q = ''; if (el.search) { el.search.value = ''; el.search.focus(); } renderTracks();
  });
  if (el.scope) el.scope.addEventListener('click', function () {
    S.scope = S.scope === 'group' ? 'all' : 'group';
    this.setAttribute('aria-pressed', S.scope === 'all' ? 'true' : 'false');
    this.textContent = S.scope === 'all' ? 'All groups' : 'This group';
    renderTracks();
  });

  if (el.play) el.play.addEventListener('click', togglePlay);
  if (el.next) el.next.addEventListener('click', function () { next(false); });
  if (el.prev) el.prev.addEventListener('click', prev);
  if (el.shuffle) el.shuffle.addEventListener('click', function () {
    SET.shuffle = !SET.shuffle; S.shufKey = ''; applySettings(); save();
    announce('Shuffle ' + (SET.shuffle ? 'on' : 'off'));
  });
  if (el.repeat) el.repeat.addEventListener('click', function () {
    SET.repeat = SET.repeat === 'off' ? 'all' : (SET.repeat === 'all' ? 'one' : 'off');
    applySettings(); save(); announce('Repeat ' + SET.repeat);
  });

  if (el.mute) el.mute.addEventListener('click', function () {
    SET.muted = !SET.muted; applySettings(); applyVolume(); save();
    announce(SET.muted ? 'All sound muted' : 'Sound on');
  });
  if (el.volume) el.volume.addEventListener('input', function () {
    SET.volume = clamp(this.value, 0, 100);
    if (SET.volume > 0 && SET.muted) SET.muted = false;
    applySettings(); applyVolume(); save();
  });

  /* seek */
  if (el.progress) {
    var ratioFrom = function (e) {
      var r = el.progress.getBoundingClientRect();
      return r.width ? Math.max(0, Math.min(1, (e.clientX - r.left) / r.width)) : 0;
    };
    el.progress.addEventListener('pointerdown', function (e) {
      if (S.basic || !player || !S.ready || !S.dur) return;
      S.dragging = true;
      try { el.progress.setPointerCapture(e.pointerId); } catch (err) {}
      setProgress(ratioFrom(e) * 100, null);
    });
    el.progress.addEventListener('pointermove', function (e) {
      if (!S.dragging) return;
      var p = ratioFrom(e) * 100;
      setProgress(p, null);
      if (el.tcur) el.tcur.textContent = fmt((p / 100) * S.dur);
    });
    var endDrag = function (e) {
      if (!S.dragging) return;
      S.dragging = false;
      try { el.progress.releasePointerCapture(e.pointerId); } catch (err) {}
      try { player.seekTo(ratioFrom(e) * S.dur, true); } catch (err) {}
    };
    el.progress.addEventListener('pointerup', endDrag);
    el.progress.addEventListener('pointercancel', function () { S.dragging = false; });
    el.progress.addEventListener('keydown', function (e) {
      if (S.basic || !player || !S.ready) return;
      var d = 0;
      if (e.key === 'ArrowRight') d = 5;
      else if (e.key === 'ArrowLeft') d = -5;
      else if (e.key === 'Home') { try { player.seekTo(0, true); } catch (err) {} e.preventDefault(); return; }
      else return;
      e.preventDefault(); e.stopPropagation();
      try { player.seekTo(Math.max(0, Math.min(S.dur, player.getCurrentTime() + d)), true); } catch (err) {}
    });
  }

  /* controls */
  var binds = [
    ['ctl-density', 'density', 'change'], ['ctl-labels', 'labels', 'change'],
    ['ctl-sort', 'sort', 'change'], ['ctl-format', 'format', 'change']
  ];
  binds.forEach(function (b) {
    var n = $(b[0]); if (!n) return;
    n.addEventListener(b[2], function () {
      SET[b[1]] = this.value; applySettings(); save();
      if (b[1] === 'sort') renderTracks();
    });
  });
  [['ctl-accent', 'accent', '°'], ['ctl-glow', 'glow', '%'], ['ctl-scale', 'scale', '%']].forEach(function (b) {
    var n = $(b[0]); if (!n) return;
    n.addEventListener('input', function () {
      SET[b[1]] = Number(this.value); applySettings(); save();
    });
  });
  [['ctl-art', 'art'], ['ctl-motion', 'motion'], ['ctl-autoplay', 'autoplay']].forEach(function (b) {
    var n = $(b[0]); if (!n) return;
    n.addEventListener('click', function () {
      SET[b[1]] = !SET[b[1]]; applySettings(); save();
      announce(this.previousElementSibling ? this.previousElementSibling.textContent + ' ' + (SET[b[1]] ? 'on' : 'off') : '');
    });
  });
  var resetBtn = $('ctl-reset'); if (resetBtn) resetBtn.addEventListener('click', resetSettings);
  var surpriseBtn = $('ctl-surprise'); if (surpriseBtn) surpriseBtn.addEventListener('click', surprise);
  var exportBtn = $('ctl-export'); if (exportBtn) exportBtn.addEventListener('click', exportList);

  /* keyboard */
  document.addEventListener('keydown', function (e) {
    var t = e.target;
    var typing = t && (t.tagName === 'INPUT' || t.tagName === 'SELECT' || t.tagName === 'TEXTAREA' || t.isContentEditable);
    if (e.metaKey || e.ctrlKey || e.altKey) return;

    if (e.key === '/' && !typing) { e.preventDefault(); if (el.search) el.search.focus(); return; }
    if (typing) return;
    if (t && t.closest && t.closest('#lr-grouplist, #lr-progress')) return;

    switch (e.key) {
      case ' ': case 'k': e.preventDefault(); togglePlay(); break;
      case 'n': next(false); break;
      case 'p': prev(); break;
      case 'm': if (el.mute && !el.mute.disabled) el.mute.click(); break;
      case 's': if (el.shuffle) el.shuffle.click(); break;
      case 'r': if (el.repeat) el.repeat.click(); break;
      case 'ArrowRight':
        if (S.basic || !player || !S.ready) return;
        e.preventDefault();
        try { player.seekTo(Math.min(S.dur, player.getCurrentTime() + 5), true); } catch (err) {}
        break;
      case 'ArrowLeft':
        if (S.basic || !player || !S.ready) return;
        e.preventDefault();
        try { player.seekTo(Math.max(0, player.getCurrentTime() - 5), true); } catch (err) {}
        break;
      case 'ArrowUp':
        if (el.volume && !el.volume.disabled) { e.preventDefault(); SET.volume = clamp(SET.volume + 5, 0, 100); applySettings(); applyVolume(); save(); }
        break;
      case 'ArrowDown':
        if (el.volume && !el.volume.disabled) { e.preventDefault(); SET.volume = clamp(SET.volume - 5, 0, 100); applySettings(); applyVolume(); save(); }
        break;
    }
  });

  window.addEventListener('pagehide', function () {
    if (tickTimer) { clearInterval(tickTimer); tickTimer = null; }
    if (apiTimer) { clearTimeout(apiTimer); apiTimer = null; }
    if (searchTimer) { clearTimeout(searchTimer); searchTimer = null; }
  });

  /* ── boot ── */
  applySettings();
  renderGroups();
  renderTracks();
  setState('idle');
  setProgress(0, 0);
  if (el.meta && DATA.room) el.meta.textContent = (DATA.room.total || 0) + ' tracks · ' + (DATA.room.groups || 0) + ' groups';
  if (!GROUPS.length) { updateTransportEnabled(); }
})();