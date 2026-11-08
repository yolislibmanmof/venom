/*! VENOM · FX Engine v2 — Canvas Particle Physics (bergerak nyata) */
(function () {
  'use strict';
  var D = document, R = D.documentElement, W = window;
  var canvas = null, ctx = null, parts = [], mode = null, running = false, raf = 0, last = 0;
  var mouse = { x: -9999, y: -9999, active: false };
  var dpr = 1, vw = 0, vh = 0;
  var COLORS = { p: '#7c3aed', a: '#22d3ee' };
  var REDUCED = W.matchMedia && W.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function rand(a, b) { return a + Math.random() * (b - a); }
  function pick(arr) { return arr[Math.floor(Math.random() * arr.length)]; }
  function hexRgb(h) {
    h = String(h).replace('#', '');
    if (h.length === 3) h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
    var n = parseInt(h, 16);
    return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
  }
  function rgba(hex, al) { var c = hexRgb(hex); return 'rgba(' + c[0] + ',' + c[1] + ',' + c[2] + ',' + al + ')'; }
  function resolve(c) { return c === 'p' ? COLORS.p : c === 'a' ? COLORS.a : c; }

  function readColors() {
    try {
      var cs = getComputedStyle(R);
      var p = cs.getPropertyValue('--v-p').trim(), a = cs.getPropertyValue('--v-a').trim();
      if (p) COLORS.p = p; if (a) COLORS.a = a;
    } catch (e) {}
  }

  /* Konfigurasi per mode: jumlah, ukuran, kecepatan (px/detik) */
  var CONF = {
    stars:     { count: 80,  size: [0.8, 2.6],  speed: [8, 22],    colors: ['a', 'p', '#ffffff'] },
    snow:      { count: 100, size: [1.4, 4.2],  speed: [22, 55],   colors: ['#ffffff'] },
    rain:      { count: 130, size: [0.9, 1.8],  speed: [260, 480], colors: ['a'] },
    bubbles:   { count: 40,  size: [4, 14],     speed: [16, 40],   colors: ['a', 'p'] },
    fireflies: { count: 45,  size: [1.4, 3.2],  speed: [12, 30],   colors: ['a', '#fbbf24'] },
    confetti:  { count: 70,  size: [3, 6],      speed: [45, 100],  colors: ['p', 'a', '#f43f5e', '#fbbf24', '#10b981'] }
  };

  function spawn(m) {
    parts = [];
    var c = CONF[m]; if (!c) return;
    var scale = Math.min(1.5, Math.max(0.45, (vw * vh) / (1280 * 720)));
    var n = Math.round(c.count * scale);
    for (var i = 0; i < n; i++) {
      var p = {
        x: rand(0, vw), y: rand(0, vh),
        r: rand(c.size[0], c.size[1]),
        c: resolve(pick(c.colors)),
        vx: 0, vy: 0,
        ph: rand(0, Math.PI * 2),
        sp: rand(0.5, 1.6),
        rot: rand(0, Math.PI * 2),
        vr: rand(-2.2, 2.2)
      };
      if (m === 'rain')          { p.vy = rand(c.speed[0], c.speed[1]); p.vx = p.vy * 0.14; }
      else if (m === 'snow')     { p.vy = rand(c.speed[0], c.speed[1]); }
      else if (m === 'bubbles')  { p.vy = -rand(c.speed[0], c.speed[1]); }
      else if (m === 'confetti') { p.vy = rand(c.speed[0], c.speed[1]); }
      else if (m === 'stars')    { p.vy = -rand(c.speed[0], c.speed[1]) * 0.5; p.vx = rand(-6, 6); }
      else                       { p.vx = rand(-1, 1) * c.speed[1] * 0.6; p.vy = rand(-1, 1) * c.speed[1] * 0.6; }
      parts.push(p);
    }
  }

  function step(dt, t) {
    for (var i = 0; i < parts.length; i++) {
      var p = parts[i];
      if (mode === 'stars') {
        p.x += p.vx * dt; p.y += p.vy * dt;
        p.x += Math.sin(t * 0.0006 + p.ph) * 0.22;
      } else if (mode === 'snow') {
        p.y += p.vy * dt * p.sp;
        p.x += Math.sin(t * 0.0011 + p.ph) * 0.9;
      } else if (mode === 'rain') {
        p.x += p.vx * dt; p.y += p.vy * dt;
      } else if (mode === 'bubbles') {
        p.y += p.vy * dt * p.sp;
        p.x += Math.sin(t * 0.0013 + p.ph) * 1.1;
      } else if (mode === 'fireflies') {
        p.vx += rand(-16, 16) * dt; p.vy += rand(-16, 16) * dt;
        p.vx *= 0.985; p.vy *= 0.985;
        var s = Math.sqrt(p.vx * p.vx + p.vy * p.vy), mx = 30;
        if (s > mx) { p.vx = p.vx / s * mx; p.vy = p.vy / s * mx; }
        p.x += p.vx * dt * 2.2; p.y += p.vy * dt * 2.2;
      } else if (mode === 'confetti') {
        p.y += p.vy * dt * p.sp;
        p.x += Math.sin(t * 0.002 + p.ph) * 1.4;
        p.rot += p.vr * dt;
      }
      /* Interaksi mouse: partikel menghindar halus */
      if (mouse.active) {
        var dx = p.x - mouse.x, dy = p.y - mouse.y, RR = 120, d2 = dx * dx + dy * dy;
        if (d2 < RR * RR && d2 > 0.01) {
          var d = Math.sqrt(d2), f = (1 - d / RR) * 320 * dt;
          p.x += (dx / d) * f; p.y += (dy / d) * f;
        }
      }
      /* Wrap-around agar tidak pernah habis */
      if (p.y < -40) p.y = vh + 30; if (p.y > vh + 40) p.y = -30;
      if (p.x < -40) p.x = vw + 30; if (p.x > vw + 40) p.x = -30;
    }
  }

  function draw(t) {
    ctx.clearRect(0, 0, vw, vh);
    for (var i = 0; i < parts.length; i++) {
      var p = parts[i];
      if (mode === 'stars') {
        var tw = 0.35 + 0.65 * Math.abs(Math.sin(t * 0.0013 * p.sp + p.ph));
        ctx.globalAlpha = tw; ctx.fillStyle = p.c;
        ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, 6.2832); ctx.fill();
        if (p.r > 1.7) { ctx.globalAlpha = tw * 0.3; ctx.beginPath(); ctx.arc(p.x, p.y, p.r * 2.6, 0, 6.2832); ctx.fill(); }
      } else if (mode === 'snow') {
        ctx.globalAlpha = 0.85; ctx.fillStyle = p.c;
        ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, 6.2832); ctx.fill();
      } else if (mode === 'rain') {
        ctx.globalAlpha = 0.5; ctx.strokeStyle = p.c; ctx.lineWidth = p.r;
        ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(p.x - p.vx * 0.035, p.y - p.vy * 0.035); ctx.stroke();
      } else if (mode === 'bubbles') {
        ctx.globalAlpha = 0.4; ctx.strokeStyle = p.c; ctx.lineWidth = 1.4;
        ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, 6.2832); ctx.stroke();
        ctx.globalAlpha = 0.55; ctx.fillStyle = '#ffffff';
        ctx.beginPath(); ctx.arc(p.x - p.r * 0.35, p.y - p.r * 0.35, p.r * 0.22, 0, 6.2832); ctx.fill();
      } else if (mode === 'fireflies') {
        var g = 0.4 + 0.6 * Math.abs(Math.sin(t * 0.002 * p.sp + p.ph));
        ctx.globalAlpha = g * 0.25; ctx.fillStyle = p.c;
        ctx.beginPath(); ctx.arc(p.x, p.y, p.r * 3.4, 0, 6.2832); ctx.fill();
        ctx.globalAlpha = g; ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, 6.2832); ctx.fill();
      } else if (mode === 'confetti') {
        ctx.globalAlpha = 0.85; ctx.fillStyle = p.c;
        ctx.save(); ctx.translate(p.x, p.y); ctx.rotate(p.rot);
        ctx.fillRect(-p.r / 2, -p.r / 2, p.r, p.r * 1.6);
        ctx.restore();
      }
    }
    ctx.globalAlpha = 1;
  }

  function frame(t) {
    raf = requestAnimationFrame(frame);
    if (D.hidden) { last = t; return; }
    if (!last) last = t;
    var dt = Math.min(0.05, (t - last) / 1000); last = t;
    step(dt, t); draw(t);
  }

  function ensureCanvas() {
    if (canvas) return !!ctx;
    canvas = D.createElement('canvas');
    canvas.id = 'vnxCanvas';
    canvas.setAttribute('aria-hidden', 'true');
    D.body.appendChild(canvas);
    ctx = canvas.getContext('2d');
    resize();
    return !!ctx;
  }
  function resize() {
    dpr = Math.min(2, W.devicePixelRatio || 1);
    vw = W.innerWidth; vh = W.innerHeight;
    canvas.width = Math.round(vw * dpr); canvas.height = Math.round(vh * dpr);
    canvas.style.width = vw + 'px'; canvas.style.height = vh + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  }

  function setMode(m) {
    if (!CONF[m] && m !== 'none') m = 'stars';
    if (m === mode) return;
    mode = m;
    if (!ensureCanvas()) return;
    if (m === 'none') { parts = []; ctx.clearRect(0, 0, vw, vh); running = false; cancelAnimationFrame(raf); return; }
    readColors(); spawn(m);
    if (REDUCED) { draw(0); running = false; return; }
    if (!running) { running = true; last = 0; raf = requestAnimationFrame(frame); }
  }

  /* ── Cursor glow (dipertahankan) ── */
  function initCursorGlow() {
    if ('ontouchstart' in W) return;
    var glow = D.createElement('div');
    glow.className = 'v-cursor-glow'; glow.setAttribute('aria-hidden', 'true');
    D.body.appendChild(glow);
    var cx = 0, cy = 0, tx = 0, ty = 0, on = false;
    D.addEventListener('mousemove', function (e) {
      tx = e.clientX; ty = e.clientY;
      if (!on) { glow.classList.add('is-active'); on = true; }
    }, { passive: true });
    (function loop() {
      cx += (tx - cx) * 0.12; cy += (ty - cy) * 0.12;
      glow.style.left = cx + 'px'; glow.style.top = cy + 'px';
      requestAnimationFrame(loop);
    })();
    D.addEventListener('mouseleave', function () { glow.classList.remove('is-active'); on = false; });
  }

  /* ── Events ── */
  W.addEventListener('mousemove', function (e) { mouse.x = e.clientX; mouse.y = e.clientY; mouse.active = true; }, { passive: true });
  D.addEventListener('mouseleave', function () { mouse.active = false; });
  W.addEventListener('resize', function () { if (canvas) { resize(); if (mode && mode !== 'none') spawn(mode); if (REDUCED) draw(0); } });
  D.addEventListener('visibilitychange', function () {
    if (D.hidden) { cancelAnimationFrame(raf); running = false; }
    else if (mode && mode !== 'none' && !REDUCED) { running = true; last = 0; raf = requestAnimationFrame(frame); }
  });

  new MutationObserver(function (ms) {
    ms.forEach(function (mu) {
      if (mu.attributeName === 'data-particle') setMode(R.getAttribute('data-particle') || 'stars');
      if (mu.attributeName === 'data-palette') { readColors(); if (mode && mode !== 'none') { spawn(mode); if (REDUCED) draw(0); } }
    });
  }).observe(R, { attributes: true });

  function init() {
    if (D.readyState === 'loading') { D.addEventListener('DOMContentLoaded', init); return; }
    initCursorGlow();
    setMode(R.getAttribute('data-particle') || 'stars');
    console.log('✦ VENOM FX Engine v2 (canvas physics) ready');
  }
  init();
})();