/*! VENOM · Cursor Trail & Particles */
(function () {
  'use strict';
  var D = document, W = window;
  var cleanup = null;

  function hexRgb(h) {
    h = String(h || '').replace('#', '');
    if (h.length === 3) h = h[0]+h[0]+h[1]+h[1]+h[2]+h[2];
    var n = parseInt(h, 16); return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
  }
  function rgba(rgb, a) { return 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + a + ')'; }
  function qualityFrom(setting) {
    var cores = navigator.hardwareConcurrency || 4, mem = navigator.deviceMemory || 4;
    var mobile = /Mobi|Android|iPhone|iPad/i.test(navigator.userAgent);
    var save = !!(navigator.connection && navigator.connection.saveData);
    if (setting === 'low') return 0; if (setting === 'medium') return 1; if (setting === 'high') return 2;
    if (save || mem <= 2 || cores <= 2 || mobile) return 0;
    if (mem <= 4 || cores <= 4) return 1; return 2;
  }

  function init() {
    if (typeof cleanup === 'function') { cleanup(); cleanup = null; }
    var body = D.body; if (!body) return;
    if (W.matchMedia && W.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (!(W.matchMedia && W.matchMedia('(pointer: fine)').matches)) return;
    var setting = body.getAttribute('data-venom-cursor-particles') || 'none';
    if (setting === 'none' || setting === '0') return;
    if (setting === 'auto') {
      var q = qualityFrom('auto');
      setting = q === 0 ? 'low' : q === 1 ? 'medium' : 'high';
    }

    var quality = qualityFrom(setting);
    var caps = [
      { dpr: 1, maxP: 45, maxT: 14, spawn: 0.32, decay: 0.05, fps: 30 },
      { dpr: 1.2, maxP: 85, maxT: 24, spawn: 0.58, decay: 0.04, fps: 45 },
      { dpr: 1.35, maxP: 130, maxT: 36, spawn: 0.82, decay: 0.032, fps: 60 }
    ][quality];

    var canvas = D.createElement('canvas');
    canvas.className = 'venom-cursor-trail-canvas';
    canvas.setAttribute('aria-hidden', 'true');
    D.body.appendChild(canvas);
    var ctx = canvas.getContext('2d', { alpha: true, desynchronized: true });
    if (!ctx) return;

    var w = 0, h = 0, dpr = 1, mouseX = W.innerWidth / 2, mouseY = W.innerHeight / 2;
    var lastX = mouseX, lastY = mouseY, speed = 0, visible = true;
    var trail = [], particles = [], raf = 0, lastFrame = 0;

    function getPalette() {
      var cs = getComputedStyle(D.documentElement);
      var a = cs.getPropertyValue('--v-a').trim() || '#22d3ee';
      return { rgb: hexRgb(a), isDark: D.documentElement.getAttribute('data-theme') === 'dark' };
    }

    function resize() {
      w = W.innerWidth; h = W.innerHeight;
      dpr = Math.min(W.devicePixelRatio || 1, caps.dpr);
      canvas.width = Math.max(1, (w * dpr) | 0);
      canvas.height = Math.max(1, (h * dpr) | 0);
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    function pushTrail(x, y) {
      var last = trail[trail.length - 1];
      var minDist = quality === 0 ? 12 : quality === 1 ? 9 : 7;
      if (last) { var dx = x - last.x, dy = y - last.y; if (dx*dx + dy*dy < minDist*minDist) return; }
      trail.push({ x: x, y: y, life: 1 });
      while (trail.length > caps.maxT) trail.shift();
    }

    function spawn(count, opts) {
      count = Math.max(0, Math.round(count * caps.spawn));
      var room = caps.maxP - particles.length;
      if (room <= 0 || count <= 0) return;
      count = Math.min(count, room);
      var pal = getPalette();
      for (var i = 0; i < count; i++) {
        var ang = opts.angle + (Math.random() - 0.5) * (opts.spread || 1);
        var sp = (opts.sMin || 0.35) + Math.random() * ((opts.sMax || 2.4) - (opts.sMin || 0.35));
        particles.push({
          x: opts.x, y: opts.y,
          vx: Math.cos(ang) * sp, vy: Math.sin(ang) * sp,
          life: 1, decay: opts.decay || 0.03,
          size: (opts.sizeMin || 1.5) + Math.random() * ((opts.sizeMax || 4) - (opts.sizeMin || 1.5)),
          rgb: pal.rgb, rot: Math.random() * Math.PI * 2, vr: (Math.random() - 0.5) * 0.18
        });
      }
    }

    function onMove(e) {
      var dx = e.clientX - lastX, dy = e.clientY - lastY;
      speed = Math.hypot(dx, dy);
      lastX = e.clientX; lastY = e.clientY;
      mouseX = e.clientX; mouseY = e.clientY;
      pushTrail(mouseX, mouseY);
      if (speed < 1) return;
      var ang = Math.atan2(dy, dx) + Math.PI;
      spawn(Math.min(4, speed * 0.12), {
        x: mouseX, y: mouseY, angle: ang, spread: 0.85,
        sMin: 0.25, sMax: 1.7 + speed * 0.04,
        sizeMin: 1.5, sizeMax: 4.8, decay: 0.032
      });
    }

    function onClick(e) {
      spawn(quality === 0 ? 8 : quality === 1 ? 14 : 20, {
        x: e.clientX, y: e.clientY, angle: 0, spread: Math.PI * 2,
        sMin: 1.4, sMax: 5, sizeMin: 2, sizeMax: 5, decay: 0.026
      });
    }

    function drawTrail() {
      if (trail.length < 2) return;
      var pal = getPalette();
      ctx.lineCap = 'round'; ctx.lineJoin = 'round';
      if (quality > 0) {
        ctx.beginPath(); ctx.moveTo(trail[0].x, trail[0].y);
        for (var i = 1; i < trail.length; i++) ctx.lineTo(trail[i].x, trail[i].y);
        ctx.strokeStyle = rgba(pal.rgb, pal.isDark ? 0.12 : 0.14);
        ctx.lineWidth = 8; ctx.stroke();
      }
      ctx.beginPath(); ctx.moveTo(trail[0].x, trail[0].y);
      for (var j = 1; j < trail.length; j++) ctx.lineTo(trail[j].x, trail[j].y);
      ctx.strokeStyle = rgba(pal.rgb, 0.65);
      ctx.lineWidth = 3; ctx.stroke();
    }

    function drawParticles() {
      for (var i = particles.length - 1; i >= 0; i--) {
        var p = particles[i];
        p.x += p.vx; p.y += p.vy; p.vx *= 0.96; p.vy *= 0.96;
        p.rot += p.vr; p.life -= p.decay;
        if (p.life <= 0) { particles[i] = particles[particles.length - 1]; particles.pop(); continue; }
        ctx.save(); ctx.translate(p.x, p.y); ctx.rotate(p.rot);
        ctx.globalAlpha = p.life;
        ctx.fillStyle = rgba(p.rgb, p.life);
        ctx.beginPath(); ctx.arc(0, 0, Math.max(0.8, p.size * 0.45), 0, Math.PI * 2); ctx.fill();
        ctx.restore();
      }
    }

    function frame(ts) {
      raf = 0;
      if (!visible || D.hidden) return;
      if (ts - lastFrame < 1000 / caps.fps) { raf = requestAnimationFrame(frame); return; }
      lastFrame = ts;
      ctx.clearRect(0, 0, w, h);
      drawTrail(); drawParticles();
      for (var i = trail.length - 1; i >= 0; i--) {
        trail[i].life -= caps.decay;
        if (trail[i].life <= 0) trail.splice(i, 1);
      }
      raf = requestAnimationFrame(frame);
    }

    function setVisible(v) {
      visible = v; canvas.style.opacity = v ? '1' : '0';
      if (v && !raf) raf = requestAnimationFrame(frame);
      else if (!v && raf) { cancelAnimationFrame(raf); raf = 0; }
    }

    W.addEventListener('resize', resize, { passive: true });
    D.addEventListener('mousemove', onMove, { passive: true });
    D.addEventListener('click', onClick, { passive: true });
    D.addEventListener('mouseleave', function () { setVisible(false); }, { passive: true });
    D.addEventListener('mouseenter', function () { setVisible(true); }, { passive: true });
    D.addEventListener('visibilitychange', function () { setVisible(!D.hidden); });
    resize(); raf = requestAnimationFrame(frame);

    cleanup = function () {
      if (raf) { cancelAnimationFrame(raf); raf = 0; }
      W.removeEventListener('resize', resize);
      D.removeEventListener('mousemove', onMove);
      D.removeEventListener('click', onClick);
      canvas.remove();
      trail = []; particles = [];
    };
  }

  if (D.readyState === 'loading') D.addEventListener('DOMContentLoaded', init, { once: true }); else init();
  D.addEventListener('venom:cursor-settings-changed', init);
  new MutationObserver(function (ms) {
    ms.forEach(function (m) { if (m.attributeName === 'data-venom-cursor-particles') init(); });
  }).observe(D.documentElement, { attributes: true });
})();