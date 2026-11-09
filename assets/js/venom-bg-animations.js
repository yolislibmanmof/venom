/*! VENOM · Background Animation Engine (7 variants: 3 canvas + 4 CSS) */
(function () {
  'use strict';
  var D = document, W = window;
  var $ = function (s, r) { return (r || D).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || D).querySelectorAll(s)); };
  var ML = W.VenomMotionLifecycle || { subscribe: function () {} };
  var glyphs = 'VENOM0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ@#$%&*';

  function hexRgb(h) {
    h = String(h || '').replace('#', '');
    if (h.length === 3) h = h[0]+h[0]+h[1]+h[1]+h[2]+h[2];
    var n = parseInt(h, 16);
    return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
  }
  function rgba(rgb, a) { return 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + a + ')'; }

  function currentColors() {
    var cs = getComputedStyle(D.documentElement);
    var p = cs.getPropertyValue('--v-p').trim() || '#7c3aed';
    var a = cs.getPropertyValue('--v-a').trim() || '#22d3ee';
    var isDark = D.documentElement.getAttribute('data-theme') === 'dark';
    var rgbP = hexRgb(p), rgbA = hexRgb(a);
    return {
      primary: p, accent: a, rgbP: rgbP, rgbA: rgbA, isDark: isDark,
      palette: [p, rgba(rgbA, 1), rgba(rgbA, .85), rgba(rgbA, .65), rgba(rgbA, .45)]
    };
  }

  function liteMode() {
    var cores = navigator.hardwareConcurrency || 4;
    var mem = navigator.deviceMemory || 4;
    var small = W.matchMedia && W.matchMedia('(max-width: 767px)').matches;
    var save = !!(navigator.connection && navigator.connection.saveData);
    return small || cores <= 4 || mem <= 4 || save;
  }

  function startCanvas(layer, setup) {
    var canvas = D.createElement('canvas');
    canvas.className = 'venom-bg-canvas';
    canvas.setAttribute('aria-hidden', 'true');
    layer.appendChild(canvas);
    var ctx = canvas.getContext('2d', { alpha: true, desynchronized: true });
    if (!ctx) return null;
    var w = 0, h = 0, raf = 0, running = false, lastFrame = 0;
    var lite = liteMode();
    var frameInterval = lite ? (1000 / 30) : 0;
    var stage = setup(ctx, function () { return w; }, function () { return h; });

    function resize() {
      var dpr = Math.min(W.devicePixelRatio || 1, lite ? 1 : 1.35);
      w = Math.max(2, layer.clientWidth || W.innerWidth);
      h = Math.max(2, layer.clientHeight || W.innerHeight);
      canvas.width = Math.floor(w * dpr);
      canvas.height = Math.floor(h * dpr);
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      if (stage && stage.resize) stage.resize(w, h);
    }
    function stop() { running = false; if (raf) { cancelAnimationFrame(raf); raf = 0; } }
    function loop(ts) {
      raf = 0; if (!running) return;
      if (frameInterval && ts - lastFrame < frameInterval) { raf = requestAnimationFrame(loop); return; }
      lastFrame = ts;
      if (stage && stage.frame) stage.frame(ctx, w, h, ts);
      raf = requestAnimationFrame(loop);
    }
    function start() { if (running) return; running = true; raf = requestAnimationFrame(loop); }

    var unsubscribe = ML.subscribe(function (vis) { vis ? start() : stop(); });
    W.addEventListener('resize', resize, { passive: true });
    resize(); start();
    return function () { stop(); W.removeEventListener('resize', resize); if (unsubscribe) unsubscribe(); };
  }

  function cleanup(layer) {
    if (typeof layer._venomCleanup === 'function') { layer._venomCleanup(); layer._venomCleanup = null; }
    while (layer.firstChild) layer.removeChild(layer.firstChild);
    layer.classList.remove('is-static');
  }

  function initAnimation(layer) {
    var anim = layer.getAttribute('data-animation') || 'none';
    var speed = parseFloat(layer.getAttribute('data-speed-multiplier')) || 1;
    if (anim === 'none') { layer.classList.add('is-static'); return; }
    if (ML.prefersReducedMotion()) { layer.classList.add('is-static'); return; }
    if (ML.prefersReducedData()) { layer.classList.add('is-static', 'is-data-saver'); return; }
    cleanup(layer);
    var lite = liteMode();
    var colors = currentColors();
    var rand = function (a, b) { return a + Math.random() * (b - a); };
    var randInt = function (a, b) { return Math.floor(rand(a, b + 1)); };

    /* ── NEURAL NETWORK (canvas) ── */
    if (anim === 'neural-network') {
      var nodes = [];
      layer._venomCleanup = startCanvas(layer, function (ctx, getW, getH) {
        function resize(w, h) {
          var n = lite ? Math.min(28, Math.max(14, Math.floor(w * h / 36000)))
                       : Math.min(46, Math.max(22, Math.floor(w * h / 24000)));
          nodes = Array.from({ length: n }, function () {
            return { x: rand(0, w), y: rand(0, h), vx: rand(-.28, .28), vy: rand(-.28, .28), r: rand(1.6, 3.2), pulse: rand(0, Math.PI * 2) };
          });
        }
        function frame(ctx, w, h) {
          ctx.clearRect(0, 0, w, h);
          var maxDist = lite ? 108 : 136;
          for (var i = 0; i < nodes.length; i++) {
            var n = nodes[i];
            n.x += n.vx; n.y += n.vy;
            if (n.x < 0) { n.x = 0; n.vx *= -1; } else if (n.x > w) { n.x = w; n.vx *= -1; }
            if (n.y < 0) { n.y = 0; n.vy *= -1; } else if (n.y > h) { n.y = h; n.vy *= -1; }
            for (var j = i + 1; j < nodes.length; j++) {
              var o = nodes[j];
              var dist = Math.hypot(n.x - o.x, n.y - o.y);
              if (dist < maxDist) {
                var ratio = 1 - dist / maxDist;
                ctx.strokeStyle = rgba(colors.rgbA, ratio * 0.75);
                ctx.lineWidth = ratio * 1.5 + 0.4;
                ctx.beginPath(); ctx.moveTo(n.x, n.y); ctx.lineTo(o.x, o.y); ctx.stroke();
              }
            }
            n.pulse += 0.04;
            var pr = n.r + Math.sin(n.pulse) * 0.8;
            ctx.fillStyle = rgba(colors.rgbA, 0.85);
            ctx.beginPath(); ctx.arc(n.x, n.y, pr, 0, Math.PI * 2); ctx.fill();
          }
        }
        return { resize: resize, frame: frame };
      });
      return;
    }

    /* ── STARFIELD WARP (canvas) ── */
    if (anim === 'starfield-warp') {
      var stars = [];
      layer._venomCleanup = startCanvas(layer, function (ctx, getW, getH) {
        function resize(w, h) {
          var count = lite ? 80 : 160;
          stars = Array.from({ length: count }, function () {
            return { x: rand(-w / 2, w / 2), y: rand(-h / 2, h / 2), z: rand(1, 1000) };
          });
        }
        function frame(ctx, w, h) {
          ctx.fillStyle = colors.isDark ? 'rgba(0,0,0,.08)' : 'rgba(255,255,255,.08)';
          ctx.fillRect(0, 0, w, h);
          var cx = w / 2, cy = h / 2, speed = 2.4 / speed;
          for (var i = 0; i < stars.length; i++) {
            var s = stars[i];
            s.z -= speed;
            if (s.z <= 0) { s.z = 1000; s.x = rand(-cx, cx); s.y = rand(-cy, cy); }
            var px = (s.x / s.z) * cx + cx, py = (s.y / s.z) * cy + cy;
            if (px < 0 || px > w || py < 0 || py > h) { s.z = 1000; s.x = rand(-cx, cx); s.y = rand(-cy, cy); continue; }
            var size = ((1000 - s.z) / 1000) * 1.8 + 0.2;
            ctx.fillStyle = colors.accent;
            ctx.beginPath(); ctx.arc(px, py, size, 0, Math.PI * 2); ctx.fill();
          }
        }
        return { resize: resize, frame: frame };
      });
      return;
    }

    /* ── ZEN RIPPLES (canvas) ── */
    if (anim === 'zen-ripples') {
      var ripples = [];
      layer._venomCleanup = startCanvas(layer, function (ctx, getW, getH) {
        var maxR = lite ? 180 : 260;
        function resize(w, h) {
          var count = lite ? 5 : 8;
          ripples = Array.from({ length: count }, function (_, i) {
            return { x: rand(.2 * w, .8 * w), y: rand(.2 * h, .8 * h), r: (i / count) * maxR, dr: rand(.4, .8) / speed, alpha: 1 };
          });
        }
        function frame(ctx, w, h) {
          ctx.clearRect(0, 0, w, h);
          ripples.forEach(function (r) {
            r.r += r.dr;
            if (r.r > maxR) { r.r = 0; r.x = rand(.2 * w, .8 * w); r.y = rand(.2 * h, .8 * h); }
            r.alpha = 1 - r.r / maxR;
            ctx.strokeStyle = rgba(colors.rgbA, r.alpha * 0.55);
            ctx.lineWidth = 1.4;
            ctx.beginPath(); ctx.arc(r.x, r.y, r.r, 0, Math.PI * 2); ctx.stroke();
          });
        }
        return { resize: resize, frame: frame };
      });
      return;
    }

    /* ── CSS: Moving Grid ── */
    if (anim === 'grid') {
      var el = D.createElement('div');
      el.className = 'venom-moving-grid';
      el.style.animationDuration = (24 * speed) + 's';
      layer.appendChild(el);
      return;
    }

    /* ── CSS: Twinkling Stars ── */
    if (anim === 'twinkle') {
      var count = lite ? 24 : 64;
      var frag = D.createDocumentFragment();
      for (var i = 0; i < count; i++) {
        var s = D.createElement('span');
        s.className = 'venom-star';
        s.style.backgroundColor = colors.palette[randInt(0, colors.palette.length - 1)];
        s.style.left = rand(1, 99) + '%';
        s.style.top = rand(2, 98) + '%';
        var size = rand(1, 3);
        s.style.width = size + 'px'; s.style.height = size + 'px';
        s.style.animationDuration = (rand(1.2, 4) * speed) + 's';
        s.style.animationDelay = '-' + rand(0, 4) + 's';
        frag.appendChild(s);
      }
      layer.appendChild(frag);
      return;
    }

    /* ── CSS: Floating Glyphs ── */
    if (anim === 'particles' || anim === 'floating-glyphs') {
      var count = lite ? 14 : 32;
      var frag = D.createDocumentFragment();
      for (var i = 0; i < count; i++) {
        var t = D.createElement('span');
        t.className = 'venom-token';
        t.style.color = colors.palette[randInt(0, colors.palette.length - 1)];
        t.style.left = rand(2, 98) + '%';
        t.style.top = rand(5, 90) + '%';
        t.style.setProperty('--tx', rand(-120, 120) + 'px');
        t.style.setProperty('--ty', rand(-120, 120) + 'px');
        t.style.setProperty('--rot', rand(-180, 180) + 'deg');
        t.style.animationDuration = (rand(8, 20) * speed) + 's';
        t.style.animationDelay = '-' + rand(0, 10) + 's';
        t.style.fontSize = randInt(14, 24) + 'px';
        t.textContent = glyphs.charAt(randInt(0, glyphs.length - 1));
        frag.appendChild(t);
      }
      layer.appendChild(frag);
      return;
    }

    /* ── CSS: Floating Embers ── */
    if (anim === 'floating-embers') {
      var count = lite ? 18 : 42;
      var frag = D.createDocumentFragment();
      for (var i = 0; i < count; i++) {
        var e = D.createElement('span');
        e.className = 'venom-ember';
        e.style.backgroundColor = colors.palette[i % colors.palette.length];
        e.style.left = rand(1, 99) + '%';
        e.style.bottom = rand(-40, -10) + 'px';
        e.style.setProperty('--x-drift', rand(-42, 42) + 'px');
        var size = rand(1.8, 5.2);
        e.style.width = size + 'px'; e.style.height = size + 'px';
        e.style.setProperty('--op', rand(0.22, 0.58));
        e.style.animationDuration = (rand(11, 23) * speed) + 's';
        e.style.animationDelay = '-' + rand(0, 18) + 's';
        frag.appendChild(e);
      }
      layer.appendChild(frag);
      return;
    }
  }

  function refresh() {
    $$('.venom-background-animation-layer, .venom-hero-animation-layer').forEach(function (l) {
      if (l.getAttribute('data-animation')) initAnimation(l);
    });
  }

  W.venomInitBgAnimation = initAnimation;
  W.venomRefreshBgAnimation = refresh;

  if (D.readyState === 'loading') D.addEventListener('DOMContentLoaded', refresh, { once: true });
  else refresh();

  // Re-init on palette/theme change
  new MutationObserver(function (ms) {
    ms.forEach(function (m) {
      if (m.attributeName === 'data-palette' || m.attributeName === 'data-theme') refresh();
    });
  }).observe(D.documentElement, { attributes: true });
})();