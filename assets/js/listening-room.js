/* The Listening Room — controller. */
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
    meta: $('lr-meta'), onair: $('lr-onair'), onairText: $('lr-onair-text'), mute: $('lr-mute'),
    groupList: $('lr-grouplist'), groupCount: $('lr-groupcount'),
    trackHeading: $('lr-trackheading'), trackCount: $('lr-trackcount'), trackList: $('lr-tracklist'),
    search: $('lr-search'), searchClear: $('lr-searchclear'), scope: $('lr-scope'), groupSelect: $('lr-groupselect'),
    sort: $('lr-sort'), format: $('lr-format'), exportBtn: $('lr-export'),
    frame: $('lr-frame'), facade: $('lr-facade'), start: $('lr-start'), mountwrap: $('lr-mountwrap'),
    audioBg: $('lr-audio-bg'), audioArt: $('lr-audio-art'), audioT: $('lr-audio-t'), audioA: $('lr-audio-a'),
    fs: $('lr-fs'),
    frameState: $('lr-framestate'), frameStateT: $('lr-framestate-t'), frameStateS: $('lr-framestate-s'),
    frameStateBtn: $('lr-framestate-btn'), frameStateLink: $('lr-framestate-link'),
    nowArt: $('lr-nowart'), nowTitle: $('lr-nowtitle'), nowArtist: $('lr-nowartist'),
    nowGroup: $('lr-nowgroup'), nowLink: $('lr-nowlink'),
    progress: $('lr-progress'), buffer: $('lr-progbuffer'), fill: $('lr-progfill'), knob: $('lr-progknob'),
    tcur: $('lr-tcur'), tdur: $('lr-tdur'),
    shuffle: $('lr-shuffle'), prev: $('lr-prev'), play: $('lr-play'), next: $('lr-next'), repeat: $('lr-repeat'),
    basicNote: $('lr-basicnote'), live: $('lr-live')
  };

  var KEY = 'lr-room-v2';
  var DEF = { sort: 'title', format: 'm3u', view: 'video', muted: false, shuffle: false, repeat: 'off' };
  var SET = Object.assign({}, DEF);
  try {
    var saved = JSON.parse(localStorage.getItem(KEY) || '{}');
    Object.keys(DEF).forEach(function (k) {
      if (saved[k] === undefined || saved[k] === null) return;
      if (typeof DEF[k] === 'boolean' && typeof saved[k] === 'boolean') SET[k] = saved[k];
      else if (typeof DEF[k] === 'string' && typeof saved[k] === 'string') SET[k] = saved[k];
    });
  } catch (e) {}
  if (['title', 'artist', 'newest'].indexOf(SET.sort) < 0) SET.sort = DEF.sort;
  if (['m3u', 'json', 'csv'].indexOf(SET.format) < 0) SET.format = DEF.format;
  if (['video', 'audio'].indexOf(SET.view) < 0) SET.view = DEF.view;
  if (['off', 'all', 'one'].indexOf(SET.repeat) < 0) SET.repeat = DEF.repeat;
  function save() { try { localStorage.setItem(KEY, JSON.stringify(SET)); } catch (e) {} }

  var S = {
    gid: GROUPS.length ? GROUPS[0].id : null, q: '', scope: 'group',
    order: [], shuf: null, shufKey: '', playing: -1, playingGroup: '',
    state: 'idle', basic: false, ready: false, dur: 0, dragging: false
  };

  var player = null, apiState = 'idle', apiTimer = null, tickTimer = null, pending = '';

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
  function fmt(t) { t = Math.max(0, Math.floor(Number(t) || 0)); var m = Math.floor(t / 60), s = t % 60; return m + ':' + (s < 10 ? '0' : '') + s; }
  function groupById(id) { for (var i = 0; i < GROUPS.length; i++) if (GROUPS[i].id === id) return GROUPS[i]; return null; }
  function announce(m) { if (el.live) el.live.textContent = m; }
  function ytUrl(id) { return 'https://www.youtube-nocookie.com/watch?v=' + id; }

  function applySettings() {
    app.dataset.view = SET.view;
    if (el.frame) el.frame.dataset.view = SET.view;
    var segs = document.querySelectorAll('.lr-seg [data-view]');
    Array.prototype.forEach.call(segs, function (b) { b.setAttribute('aria-pressed', b.dataset.view === SET.view ? 'true' : 'false'); });
    if (el.sort) el.sort.value = SET.sort;
    if (el.format) el.format.value = SET.format;
    if (el.mute) {
      el.mute.setAttribute('aria-pressed', SET.muted ? 'true' : 'false');
      el.mute.setAttribute('aria-label', SET.muted ? 'Unmute all sound' : 'Mute all sound');
    }
    if (el.shuffle) el.shuffle.setAttribute('aria-pressed', SET.shuffle ? 'true' : 'false');
    if (el.repeat) { el.repeat.dataset.mode = SET.repeat; el.repeat.setAttribute('aria-label', 'Repeat ' + SET.repeat); }
  }

  function renderGroups() {
    if (!el.groupList) return;
    if (!GROUPS.length) {
      el.groupList.innerHTML = '<div class="lr-empty"><p>No groups yet</p><small>Assign a Song Group to a song or rap track and it appears here.</small></div>';
      if (el.groupSelect) el.groupSelect.innerHTML = '<option>No groups</option>';
      return;
    }
    el.groupList.innerHTML = GROUPS.map(function (g) {
      return '<button type="button" class="lr-gitem" role="tab" id="lr-tab-' + g.id + '" data-gid="' + g.id + '"' +
        ' aria-selected="false" aria-controls="lr-tracklist" tabindex="-1" style="--c:' + esc(g.color) + '"' +
        ' title="' + esc(g.name) + ' · ' + g.count + ' tracks">' +
        '<span class="lr-badge" aria-hidden="true">' + esc(g.badge) + '</span>' +
        '<span class="lr-gname">' + esc(g.name) + '</span>' +
        '<span class="lr-gcount">' + g.count + '</span></button>';
    }).join('');
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

  function computeOrder() {
    var g = groupById(S.gid), pool;
    if (S.q && S.scope === 'all') pool = TRACKS.map(function (_, i) { return i; });
    else pool = g ? g.tracks.slice() : [];
    if (S.q) {
      var tokens = norm(S.q).split(/\s+/).filter(Boolean);
      pool = pool.filter(function (i) {
        var t = TRACKS[i], hay = norm(t.title + ' ' + t.full + ' ' + t.artist + ' ' + t.group);
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
    S.order = pool; S.shufKey = '';
  }

  function artHTML(t, cls) {
    if (t.thumb) return '<span class="' + cls + '"><img src="' + esc(t.thumb) + '" alt="" loading="lazy" decoding="async" width="44" height="44"></span>';
    return '<span class="' + cls + '"><span class="lr-artgen">' + esc((t.title || '?').charAt(0).toUpperCase()) + '</span></span>';
  }

  function renderTracks() {
    if (!el.trackList) return;
    computeOrder(); syncGroupUI();
    if (el.trackCount) el.trackCount.textContent = S.order.length;
    if (el.searchClear) el.searchClear.hidden = !S.q;

    if (!GROUPS.length) {
      el.trackList.innerHTML = '<div class="lr-empty"><p>Nothing to play yet</p><small>Tracks appear once they belong to a Song Group.</small></div>';
      updateTransportEnabled(); return;
    }
    if (!S.order.length) {
      el.trackList.innerHTML = '<div class="lr-empty">' +
        '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.6-3.6"/></svg>' +
        '<p>No matches</p><small>Nothing here matches “' + esc(S.q) + '”.</small>' +
        '<button type="button" class="lr-btn" data-act="clear">Clear search</button></div>';
      updateTransportEnabled(); return;
    }

    var showGroup = (S.scope === 'all' && !!S.q), html = '';
    S.order.forEach(function (i) {
      var t = TRACKS[i], active = (i === S.playing);
      if (!t.yt) {
        html += '<div class="lr-row is-na">' + artHTML(t, 'lr-row-art') +
          '<span class="lr-row-txt"><span class="lr-row-t">' + esc(t.title) + '</span><span class="lr-row-a">' + esc(t.artist) + '</span></span>' +
          '<a class="lr-row-na" href="' + esc(t.url) + '" target="_blank" rel="noopener noreferrer">No video</a></div>';
        return;
      }
      html += '<button type="button" class="lr-row' + (active ? ' is-active' : '') + '" data-i="' + i + '" data-g="' + esc(t.group) + '"' +
        ' aria-current="' + (active ? 'true' : 'false') + '" title="' + esc(t.full) + ' — ' + esc(t.artist) + '"' +
        ' aria-label="Play ' + esc(t.title) + ' by ' + esc(t.artist) + '">' + artHTML(t, 'lr-row-art') +
        '<span class="lr-row-txt"><span class="lr-row-t">' + esc(t.title) + '</span><span class="lr-row-a">' + esc(t.artist) + '</span></span>' +
        (showGroup ? '<span class="lr-row-g">' + esc(t.group) + '</span>' : '') +
        '<span class="lr-eq" aria-hidden="true"><i></i><i></i><i></i></span></button>';
    });
    el.trackList.innerHTML = html;
    updateTransportEnabled();
  }

  function syncRows() {
    if (!el.trackList) return;
    Array.prototype.forEach.call(el.trackList.querySelectorAll('.lr-row[data-i]'), function (r) {
      var on = Number(r.dataset.i) === S.playing;
      r.classList.toggle('is-active', on);
      r.setAttribute('aria-current', on ? 'true' : 'false');
    });
  }

  function selectGroup(gid) {
    if (!groupById(gid)) return;
    S.gid = gid; S.q = '';
    if (el.search) el.search.value = '';
    renderTracks();
    if (el.trackList) el.trackList.scrollTop = 0;
    var g = groupById(gid);
    if (g) announce(g.name + ', ' + g.count + ' tracks');
  }

  function setState(s) {
    S.state = s; app.dataset.state = s;
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
    S.basic = true; S.ready = false;
    if (apiTimer) { clearTimeout(apiTimer); apiTimer = null; }
    if (tickTimer) { clearInterval(tickTimer); tickTimer = null; }
    if (el.basicNote) el.basicNote.hidden = false;
    if (el.mute) { el.mute.disabled = true; el.mute.title = 'Volume control unavailable in basic mode'; }
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
    s.src = 'https://www.youtube.com/iframe_api'; s.async = true;
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
        playerVars: { autoplay: 1, rel: 0, modestbranding: 1, playsinline: 1, iv_load_policy: 3, origin: window.location.origin },
        events: {
          onReady: function () {
            S.ready = true; hideFrameState(); applyVolume();
            if (pending) {
              try {
                var vd = player.getVideoData ? player.getVideoData() : null;
                if (!vd || vd.video_id !== pending) player.loadVideoById(pending); else player.playVideo();
              } catch (e) {}
            }
            startTick(); updateTransportEnabled();
          },
          onStateChange: function (e) { onYtState(e.data); },
          onError: function () {
            setState('error');
            var t = TRACKS[S.playing];
            showFrameState('This video will not play', 'It may be private, removed, or blocked from embedding.', 'Skip to next',
              function () { hideFrameState(); next(false); }, t && t.yt ? ytUrl(t.yt) : null);
          },
          onAutoplayBlocked: function () {
            setState('paused');
            showFrameState('Autoplay was blocked', 'Your browser needs one more click to start the audio.', 'Play now',
              function () { hideFrameState(); try { player.playVideo(); } catch (e) {} }, null);
          }
        }
      });
    } catch (e) { enableBasic(); }
  }

  function onYtState(d) {
    if (d === 1) { setState('playing'); hideFrameState(); }
    else if (d === 2) setState('paused');
    else if (d === 3) setState('loading');
    else if (d === 5) setState('paused');
    else if (d === 0) onEnded();
  }

  function onEnded() {
    if (SET.repeat === 'one') { try { player.seekTo(0, true); player.playVideo(); } catch (e) {} return; }
    var o = playbackOrder(), pos = o.indexOf(S.playing);
    if (SET.repeat === 'off' && pos === o.length - 1) { setState('ended'); return; }
    next(true);
  }

  function ensure(id) {
    pending = id; hideFacade(); hideFrameState();
    if (S.basic) { mountBasic(id); return; }
    if (player && S.ready) { try { player.loadVideoById(id); setState('loading'); return; } catch (e) { enableBasic(); return; } }
    setState('loading');
    if (apiState === 'idle') loadApi();
  }

  function startTick() { if (tickTimer) clearInterval(tickTimer); tickTimer = setInterval(tick, 250); }

  function tick() {
    if (S.basic || !player || !S.ready || S.dragging) return;
    var d = 0, c = 0, b = 0;
    try { d = player.getDuration() || 0; c = player.getCurrentTime() || 0; b = player.getVideoLoadedFraction() || 0; } catch (e) { return; }
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
    try { player.setVolume(100); if (SET.muted) player.mute(); else player.unMute(); } catch (e) {}
  }

  function play(i, groupName) {
    var t = TRACKS[i];
    if (!t || !t.yt) return;
    S.playing = i; S.playingGroup = groupName || t.group;
    ensure(t.yt); updateNowPlaying(); syncRows(); updateTransportEnabled();
    announce('Now playing ' + t.title + ' by ' + t.artist);
  }

  function updateNowPlaying() {
    var t = TRACKS[S.playing];
    if (!t) return;
    if (el.nowTitle) { el.nowTitle.textContent = t.title; el.nowTitle.title = t.full; }
    if (el.nowArtist) el.nowArtist.textContent = t.artist;
    if (el.nowGroup) { el.nowGroup.textContent = S.playingGroup || t.group; el.nowGroup.hidden = false; }
    if (el.nowLink) { el.nowLink.href = t.url; el.nowLink.hidden = false; }
    var big = t.large || t.thumb;
    var gen = '<span class="lr-artgen">' + esc((t.title || '?').charAt(0).toUpperCase()) + '</span>';
    if (el.nowArt) el.nowArt.innerHTML = big ? '<img src="' + esc(big) + '" alt="" decoding="async" width="60" height="60">' : gen;
    if (el.audioArt) el.audioArt.innerHTML = big ? '<img src="' + esc(big) + '" alt="" decoding="async" width="104" height="104">' : gen;
    if (el.audioBg) { if (big) { el.audioBg.src = big; el.audioBg.hidden = false; } else { el.audioBg.hidden = true; } }
    if (el.audioT) el.audioT.textContent = t.title;
    if (el.audioA) el.audioA.textContent = t.artist;
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
    var pos = o.indexOf(S.playing), n;
    if (pos < 0) n = dir > 0 ? 0 : o.length - 1;
    else {
      n = pos + dir;
      if (n >= o.length) { if (SET.repeat === 'off' && auto) { setState('ended'); return; } n = 0; }
      if (n < 0) n = o.length - 1;
    }
    var idx = o[n], t = TRACKS[idx];
    play(idx, t ? t.group : '');
  }
  function next(auto) { step(1, !!auto); }
  function prev() { step(-1, false); }

  function togglePlay() {
    if (S.playing < 0) { var o = playbackOrder(); if (o.length) play(o[0], TRACKS[o[0]].group); return; }
    if (S.basic) { mountBasic(TRACKS[S.playing].yt); return; }
    if (!player || !S.ready) return;
    try { if (S.state === 'playing') player.pauseVideo(); else player.playVideo(); } catch (e) {}
  }

  function updateTransportEnabled() {
    var has = S.order.length > 0 || S.playing >= 0;
    if (el.play) el.play.disabled = !has;
    if (el.prev) el.prev.disabled = !has;
    if (el.next) el.next.disabled = !has;
  }

  function surprise() {
    var playable = GROUPS.filter(function (g) { return g.tracks.some(function (i) { return TRACKS[i].yt; }); });
    if (!playable.length) return;
    var g = playable[Math.floor(Math.random() * playable.length)];
    selectGroup(g.id);
    var pool = S.order.filter(function (i) { return TRACKS[i].yt; });
    if (!pool.length) return;
    var pick = pool[Math.floor(Math.random() * pool.length)];
    play(pick, g.name);
    var row = el.trackList.querySelector('.lr-row[data-i="' + pick + '"]');
    if (row && row.scrollIntoView) row.scrollIntoView({ block: 'nearest' });
  }

  function exportList() {
    var rows = S.order.map(function (i) { return TRACKS[i]; }).filter(function (t) { return !!t.yt; });
    if (!rows.length) { announce('Nothing to export.'); return; }
    var g = groupById(S.gid), name = 'listening-room-' + ((g && g.slug) || 'selection'), body, mime, ext;
    if (SET.format === 'json') {
      body = JSON.stringify({ room: 'The Listening Room', group: g ? g.name : 'Selection', exported: new Date().toISOString(),
        tracks: rows.map(function (t) { return { title: t.title, artist: t.artist, group: t.group, video: ytUrl(t.yt), page: t.url }; }) }, null, 2);
      mime = 'application/json'; ext = 'json';
    } else if (SET.format === 'csv') {
      var q = function (v) { return '"' + String(v).replace(/"/g, '""') + '"'; };
      body = 'Title,Artist,Group,Video,Page\n' + rows.map(function (t) {
        return [q(t.title), q(t.artist), q(t.group), q(ytUrl(t.yt)), q(t.url)].join(',');
      }).join('\n');
      mime = 'text/csv'; ext = 'csv';
    } else {
      body = '#EXTM3U\n' + rows.map(function (t) { return '#EXTINF:-1,' + t.artist + ' - ' + t.title + '\n' + ytUrl(t.yt); }).join('\n') + '\n';
      mime = 'audio/x-mpegurl'; ext = 'm3u8';
    }
    try {
      var url = URL.createObjectURL(new Blob([body], { type: mime + ';charset=utf-8' }));
      var a = document.createElement('a');
      a.href = url; a.download = name + '.' + ext;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
      setTimeout(function () { URL.revokeObjectURL(url); }, 1500);
      announce('Exported ' + rows.length + ' tracks as ' + ext.toUpperCase());
    } catch (e) { announce('Export failed in this browser.'); }
  }

  /* events */
  if (el.groupList) {
    el.groupList.addEventListener('click', function (e) {
      var b = e.target.closest('.lr-gitem'); if (b) selectGroup(Number(b.dataset.gid));
    });
    el.groupList.addEventListener('keydown', function (e) {
      if (['ArrowDown', 'ArrowUp', 'Home', 'End'].indexOf(e.key) < 0) return;
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
      if (e.target.closest('[data-act="clear"]')) { S.q = ''; if (el.search) { el.search.value = ''; el.search.focus(); } renderTracks(); return; }
      var row = e.target.closest('.lr-row[data-i]');
      if (row) play(Number(row.dataset.i), row.dataset.g);
    });
    el.trackList.addEventListener('keydown', function (e) {
      if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') return;
      var rows = Array.prototype.slice.call(el.trackList.querySelectorAll('.lr-row[data-i]'));
      var at = rows.indexOf(document.activeElement);
      if (at < 0) return;
      e.preventDefault();
      rows[e.key === 'ArrowDown' ? Math.min(rows.length - 1, at + 1) : Math.max(0, at - 1)].focus();
    });
  }

  var searchTimer = null;
  if (el.search) {
    el.search.addEventListener('input', function () {
      var v = this.value;
      if (searchTimer) clearTimeout(searchTimer);
      searchTimer = setTimeout(function () { S.q = v.trim(); renderTracks(); }, 90);
    });
    el.search.addEventListener('keydown', function (e) { if (e.key === 'Escape') { this.value = ''; S.q = ''; renderTracks(); } });
  }
  if (el.searchClear) el.searchClear.addEventListener('click', function () { S.q = ''; if (el.search) { el.search.value = ''; el.search.focus(); } renderTracks(); });
  if (el.scope) el.scope.addEventListener('click', function () {
    S.scope = S.scope === 'group' ? 'all' : 'group';
    this.setAttribute('aria-pressed', S.scope === 'all' ? 'true' : 'false');
    this.textContent = S.scope === 'all' ? 'All groups' : 'This group';
    renderTracks();
  });
  if (el.sort) el.sort.addEventListener('change', function () { SET.sort = this.value; save(); renderTracks(); });
  if (el.format) el.format.addEventListener('change', function () { SET.format = this.value; save(); });
  if (el.exportBtn) el.exportBtn.addEventListener('click', exportList);
  if (el.start) el.start.addEventListener('click', surprise);

  Array.prototype.forEach.call(document.querySelectorAll('.lr-seg [data-view]'), function (b) {
    b.addEventListener('click', function () {
      SET.view = this.dataset.view; applySettings(); save();
      announce(SET.view === 'audio' ? 'Audio focus. Player minimized.' : 'Video view.');
    });
  });

  if (el.fs) {
    var d = document;
    if (!d.fullscreenEnabled && !d.webkitFullscreenEnabled) el.fs.hidden = true;
    el.fs.addEventListener('click', function () {
      var f = el.frame; if (!f) return;
      try {
        if (d.fullscreenElement || d.webkitFullscreenElement) { (d.exitFullscreen || d.webkitExitFullscreen).call(d); }
        else { (f.requestFullscreen || f.webkitRequestFullscreen).call(f); }
      } catch (e) {}
    });
  }

  if (el.play) el.play.addEventListener('click', togglePlay);
  if (el.next) el.next.addEventListener('click', function () { next(false); });
  if (el.prev) el.prev.addEventListener('click', prev);
  if (el.shuffle) el.shuffle.addEventListener('click', function () { SET.shuffle = !SET.shuffle; S.shufKey = ''; applySettings(); save(); announce('Shuffle ' + (SET.shuffle ? 'on' : 'off')); });
  if (el.repeat) el.repeat.addEventListener('click', function () { SET.repeat = SET.repeat === 'off' ? 'all' : (SET.repeat === 'all' ? 'one' : 'off'); applySettings(); save(); announce('Repeat ' + SET.repeat); });
  if (el.mute) el.mute.addEventListener('click', function () { SET.muted = !SET.muted; applySettings(); applyVolume(); save(); announce(SET.muted ? 'All sound muted' : 'Sound on'); });

  if (el.progress) {
    var ratioFrom = function (e) { var r = el.progress.getBoundingClientRect(); return r.width ? Math.max(0, Math.min(1, (e.clientX - r.left) / r.width)) : 0; };
    el.progress.addEventListener('pointerdown', function (e) {
      if (S.basic || !player || !S.ready || !S.dur) return;
      S.dragging = true;
      try { el.progress.setPointerCapture(e.pointerId); } catch (err) {}
      setProgress(ratioFrom(e) * 100, null);
    });
    el.progress.addEventListener('pointermove', function (e) {
      if (!S.dragging) return;
      var p = ratioFrom(e) * 100; setProgress(p, null);
      if (el.tcur) el.tcur.textContent = fmt((p / 100) * S.dur);
    });
    el.progress.addEventListener('pointerup', function (e) {
      if (!S.dragging) return;
      S.dragging = false;
      try { el.progress.releasePointerCapture(e.pointerId); } catch (err) {}
      try { player.seekTo(ratioFrom(e) * S.dur, true); } catch (err) {}
    });
    el.progress.addEventListener('pointercancel', function () { S.dragging = false; });
    el.progress.addEventListener('keydown', function (e) {
      if (S.basic || !player || !S.ready) return;
      var d = 0;
      if (e.key === 'ArrowRight') d = 5;
      else if (e.key === 'ArrowLeft') d = -5;
      else if (e.key === 'Home') { e.preventDefault(); try { player.seekTo(0, true); } catch (err) {} return; }
      else return;
      e.preventDefault(); e.stopPropagation();
      try { player.seekTo(Math.max(0, Math.min(S.dur, player.getCurrentTime() + d)), true); } catch (err) {}
    });
  }

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
      case 'v': SET.view = SET.view === 'video' ? 'audio' : 'video'; applySettings(); save(); break;
      case 'ArrowRight':
        if (S.basic || !player || !S.ready) return;
        e.preventDefault(); try { player.seekTo(Math.min(S.dur, player.getCurrentTime() + 5), true); } catch (err) {} break;
      case 'ArrowLeft':
        if (S.basic || !player || !S.ready) return;
        e.preventDefault(); try { player.seekTo(Math.max(0, player.getCurrentTime() - 5), true); } catch (err) {} break;
    }
  });

  window.addEventListener('pagehide', function () {
    if (tickTimer) { clearInterval(tickTimer); tickTimer = null; }
    if (apiTimer) { clearTimeout(apiTimer); apiTimer = null; }
    if (searchTimer) { clearTimeout(searchTimer); searchTimer = null; }
  });

  applySettings();
  renderGroups();
  renderTracks();
  setState('idle');
  setProgress(0, 0);
  if (el.meta && DATA.room) el.meta.textContent = (DATA.room.total || 0) + ' tracks · ' + (DATA.room.groups || 0) + ' groups';
})();