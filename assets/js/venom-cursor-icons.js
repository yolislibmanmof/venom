/*! VENOM · Custom Cursor Icons (5 variants) */
(function () {
  'use strict';
  var D = document, W = window;
  var MODES = [
    { id: 'neon-comet', color: '#22d3ee', accent: '#7c5cff' },
    { id: 'pixel-sword', color: '#a3e635', accent: '#4ade80' },
    { id: 'electric-bolt', color: '#fde047', accent: '#38bdf8' },
    { id: 'ink-brush', color: '#f472b6', accent: '#e2e8f0' },
    { id: 'rainbow-ribbon', color: '#f472b6', accent: '#22d3ee' }
  ];
  var cleanup = null;

  function hexRgb(h) {
    h = String(h || '').replace('#', '');
    if (h.length === 3) h = h[0]+h[0]+h[1]+h[1]+h[2]+h[2];
    var n = parseInt(h, 16); return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
  }
  function rgba(rgb, a) { return 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + a + ')'; }
  function rainbow(t) {
    var h = ((t % 1 + 1) % 1) * 6, i = h | 0, f = h - i, q = 1 - f;
    var r, g, b;
    switch (i % 6) { case 0: r=1;g=f;b=0;break; case 1: r=q;g=1;b=0;break; case 2: r=0;g=1;b=f;break;
      case 3: r=0;g=q;b=1;break; case 4: r=f;g=0;b=1;break; default: r=1;g=0;b=q; }
    return [(r*255)|0, (g*255)|0, (b*255)|0];
  }
  function findMode(id) { for (var i = 0; i < MODES.length; i++) if (MODES[i].id === id) return { mode: MODES[i], index: i }; return { mode: MODES[0], index: 0 }; }
  function pointerIsFine() { return W.matchMedia && W.matchMedia('(pointer: fine)').matches; }
  function reducedMotion() { return W.matchMedia && W.matchMedia('(prefers-reduced-motion: reduce)').matches; }

  function injectStyles() {
    if (D.getElementById('venom-cursor-style')) return;
    var s = D.createElement('style'); s.id = 'venom-cursor-style';
    s.textContent = 'body.venom-custom-cursor-active,body.venom-custom-cursor-active a,body.venom-custom-cursor-active button,body.venom-custom-cursor-active [role="button"]{cursor:none!important}';
    D.head.appendChild(s);
  }

  function init() {
    if (typeof cleanup === 'function') { cleanup(); cleanup = null; }
    var body = D.body; if (!body) return;
    if (reducedMotion() || !pointerIsFine()) { body.classList.remove('venom-custom-cursor-active'); return; }
    var modeId = body.getAttribute('data-venom-cursor-icon') || 'default';
    if (modeId === 'default' || modeId === 'none' || modeId === '0') { body.classList.remove('venom-custom-cursor-active'); return; }
    var data = findMode(modeId); var mode = data.mode;

    injectStyles();
    var canvas = D.createElement('canvas');
    canvas.className = 'venom-cursor-icon-canvas';
    canvas.setAttribute('aria-hidden', 'true');
    D.body.appendChild(canvas);
    D.body.classList.add('venom-custom-cursor-active');

    var ctx = canvas.getContext('2d', { alpha: true, desynchronized: true });
    if (!ctx) return;
    var w = 0, h = 0, dpr = 1, mouseX = W.innerWidth / 2, mouseY = W.innerHeight / 2;
    var smoothX = mouseX, smoothY = mouseY, velX = 0, velY = 0, speed = 0, time = 0, angle = 0, visible = true;
    var frameId = 0;

    function resize() {
      w = W.innerWidth; h = W.innerHeight;
      dpr = Math.min(W.devicePixelRatio || 1, 1.35);
      canvas.width = Math.max(1, (w * dpr) | 0);
      canvas.height = Math.max(1, (h * dpr) | 0);
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    function glow(x, y, r, rgb, a) {
      var g = ctx.createRadialGradient(x, y, 0, x, y, r);
      g.addColorStop(0, rgba(rgb, a)); g.addColorStop(1, rgba(rgb, 0));
      ctx.fillStyle = g; ctx.beginPath(); ctx.arc(x, y, r, 0, Math.PI * 2); ctx.fill();
    }

    function currentPalette() {
      var isDark = D.documentElement.getAttribute('data-theme') === 'dark';
      var rgb = hexRgb(mode.color);
      return { rgb: rgb, rgbA: hexRgb(mode.accent), color: mode.color, accent: mode.accent, isDark: isDark };
    }

    function draw() {
      var x = smoothX, y = smoothY;
      var moveAngle = Math.atan2(velY || 0.001, velX || 0.001);
      var pulse = 1 + Math.sin(time * 0.006) * 0.07;
      var pal = currentPalette();
      ctx.save();

      if (mode.id === 'neon-comet') {
        glow(x, y, 28 * pulse, pal.rgb, 0.38);
        ctx.fillStyle = '#fff'; ctx.beginPath(); ctx.arc(x, y, 4.5 * pulse, 0, Math.PI * 2); ctx.fill();
        ctx.strokeStyle = rgba(pal.rgb, 0.9); ctx.lineWidth = 2;
        ctx.beginPath(); ctx.arc(x, y, 10 * pulse, 0, Math.PI * 2); ctx.stroke();
      } else if (mode.id === 'pixel-sword') {
        ctx.translate(x, y); ctx.rotate(moveAngle + Math.PI / 4);
        ctx.fillStyle = '#e2e8f0'; ctx.fillRect(-2, -24, 4, 20);
        ctx.fillStyle = pal.color; ctx.fillRect(-2, -4, 4, 8); ctx.fillRect(-6, 0, 12, 4);
        ctx.fillStyle = '#ca8a04'; ctx.fillRect(-2, 8, 4, 12);
      } else if (mode.id === 'electric-bolt') {
        glow(x, y, 26 * pulse, pal.rgb, 0.35);
        ctx.translate(x, y); ctx.rotate(moveAngle);
        ctx.strokeStyle = pal.color; ctx.lineWidth = 2.2;
        ctx.beginPath(); ctx.moveTo(-14, 0); ctx.lineTo(-8, -5); ctx.lineTo(-3, 4); ctx.lineTo(4, -4); ctx.lineTo(12, 2); ctx.stroke();
        ctx.fillStyle = '#fff'; ctx.beginPath(); ctx.arc(0, 0, 3 * pulse, 0, Math.PI * 2); ctx.fill();
      } else if (mode.id === 'ink-brush') {
        ctx.translate(x, y); ctx.rotate(moveAngle + Math.PI / 2);
        ctx.fillStyle = '#1e1b4b'; ctx.fillRect(-2.5, -2, 5, 18);
        ctx.fillStyle = pal.color; ctx.beginPath();
        ctx.moveTo(-5, -2); ctx.quadraticCurveTo(0, -22 * pulse, 5, -2); ctx.closePath(); ctx.fill();
        ctx.fillStyle = '#f8fafc'; ctx.fillRect(-3.5, 16, 7, 5);
      } else if (mode.id === 'rainbow-ribbon') {
        ctx.translate(x, y);
        for (var i = 0; i < 6; i++) {
          var dotColor = pal.isDark ? rainbow(time * 0.0015 + i / 6) : pal.rgb;
          var dotAngle = angle + i * (Math.PI * 2 / 6);
          ctx.fillStyle = rgba(dotColor, pal.isDark ? 1 : 0.78 - (i * 0.06));
          ctx.beginPath(); ctx.arc(Math.cos(dotAngle) * 7, Math.sin(dotAngle) * 7, 3, 0, Math.PI * 2); ctx.fill();
        }
        ctx.fillStyle = pal.isDark ? '#fff' : pal.color;
        ctx.beginPath(); ctx.arc(0, 0, 4 * pulse, 0, Math.PI * 2); ctx.fill();
      }
      ctx.restore();
    }

    function frame(ts) {
      frameId = 0;
      if (!visible || D.hidden) return;
      time = ts || 0;
      smoothX += (mouseX - smoothX) * 0.78;
      smoothY += (mouseY - smoothY) * 0.78;
      velX *= 0.88; velY *= 0.88;
      speed = Math.hypot(velX, velY);
      angle += 0.035 + speed * 0.0015;
      ctx.clearRect(0, 0, w, h);
      draw();
      frameId = requestAnimationFrame(frame);
    }

    function onMove(e) { velX = e.clientX - mouseX; velY = e.clientY - mouseY; mouseX = e.clientX; mouseY = e.clientY; if (!frameId) frameId = requestAnimationFrame(frame); }
    function setVisible(v) { visible = v; canvas.style.opacity = v ? '1' : '0'; }

    W.addEventListener('resize', resize, { passive: true });
    D.addEventListener('mousemove', onMove, { passive: true });
    D.addEventListener('mouseleave', function () { setVisible(false); }, { passive: true });
    D.addEventListener('mouseenter', function () { setVisible(true); }, { passive: true });
    D.addEventListener('visibilitychange', function () { setVisible(!D.hidden); });
    resize(); frameId = requestAnimationFrame(frame);

    cleanup = function () {
      if (frameId) { cancelAnimationFrame(frameId); frameId = 0; }
      W.removeEventListener('resize', resize);
      D.removeEventListener('mousemove', onMove);
      canvas.remove();
      D.body.classList.remove('venom-custom-cursor-active');
    };
  }

  if (D.readyState === 'loading') D.addEventListener('DOMContentLoaded', init, { once: true }); else init();
  D.addEventListener('venom:cursor-settings-changed', init);
  var rm = W.matchMedia && W.matchMedia('(prefers-reduced-motion: reduce)');
  if (rm && rm.addEventListener) rm.addEventListener('change', init);
  new MutationObserver(function (ms) {
    ms.forEach(function (m) { if (m.attributeName === 'data-venom-cursor-icon') init(); });
  }).observe(D.documentElement, { attributes: true });
})();