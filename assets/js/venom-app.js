/*! VENOM · Core JS — zero dependency */
(function () {
  'use strict';
  var D = document, R = D.documentElement;
  var $ = function (s, c) { return (c || D).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || D).querySelectorAll(s)); };
  var store = {
    get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) {} }
  };

  /* Toast */
  var toastEl, toastTimer;
  function toast(msg) {
    if (!toastEl) toastEl = $('#vToast');
    if (!toastEl) return;
    toastEl.textContent = msg; toastEl.classList.add('is-on');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toastEl.classList.remove('is-on'); }, 2200);
  }
  window.venomToast = toast;

  /* Mode */
  function setMode(m) {
    R.setAttribute('data-theme', m); store.set('venom.mode', m);
    $$('[data-venom="mode"]').forEach(function (b) { b.setAttribute('aria-pressed', m === 'dark'); });
  }
  /* Palette */
  function setPalette(p) {
    R.setAttribute('data-palette', p); store.set('venom.palette', p);
    $$('[data-palette-set]').forEach(function (b) {
      b.classList.toggle('is-on', b.getAttribute('data-palette-set') === p);
    });
  }

  D.addEventListener('DOMContentLoaded', function () {
    setMode(R.getAttribute('data-theme') === 'dark' ? 'dark' : 'light');
    setPalette(R.getAttribute('data-palette') || 'venom');

    /* Delegated clicks */
    D.addEventListener('click', function (e) {
      var t = e.target.closest('[data-venom],[data-palette-set],[data-copy]');
      if (!t) return;
      if (t.hasAttribute('data-palette-set')) { setPalette(t.getAttribute('data-palette-set')); toast('Palet diterapkan'); return; }
      if (t.hasAttribute('data-copy')) {
        var txt = t.getAttribute('data-copy'), sel = $(t.getAttribute('data-copy-target') || '');
        if (sel) txt = sel.innerText;
        if (navigator.clipboard) navigator.clipboard.writeText(txt).then(function () { toast('Disalin ke clipboard'); });
        return;
      }
      var a = t.getAttribute('data-venom');
      if (a === 'mode') { setMode(R.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'); }
      else if (a === 'top') { window.scrollTo({ top: 0, behavior: 'smooth' }); }
      else if (a === 'palette') { var p = $('#vPalette'); if (p) p.hidden = !p.hidden; }
      else if (a === 'palette-close') { var pc = $('#vPalette'); if (pc) pc.hidden = true; }
      else if (a === 'menu') {
        var m = $('#vMenu'), open = m && m.classList.toggle('is-open');
        t.setAttribute('aria-expanded', open ? 'true' : 'false');
      }
    });

    D.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        var p = $('#vPalette'); if (p) p.hidden = true;
        var m = $('#vMenu'); if (m) m.classList.remove('is-open');
      }
      if (e.key === '/' && !/input|textarea|select/i.test(D.activeElement.tagName)) {
        var s = $('.v-search input') || $('.v-navsearch input');
        if (s) { e.preventDefault(); s.focus(); }
      }
    });

    /* Navbar auto-hide */
    var nav = $('#vNav'), last = 0;
    window.addEventListener('scroll', function () {
      var y = window.pageYOffset;
      if (nav) {
        nav.classList.toggle('is-stuck', y > 12);
        nav.classList.toggle('is-hidden', y > 260 && y > last && !($('#vMenu') && $('#vMenu').classList.contains('is-open')));
      }
      var top = $('[data-venom="top"]'); if (top) top.hidden = y < 420;
      last = y;
    }, { passive: true });

    /* Reveal on scroll */
    var rev = $$('.v-reveal');
    if ('IntersectionObserver' in window && rev.length) {
      var io = new IntersectionObserver(function (en) {
        en.forEach(function (x, i) {
          if (x.isIntersecting) {
            setTimeout(function () { x.target.classList.add('is-in'); }, Math.min(i * 55, 330));
            io.unobserve(x.target);
          }
        });
      }, { rootMargin: '0px 0px -8% 0px', threshold: .08 });
      rev.forEach(function (el) { io.observe(el); });
    } else { rev.forEach(function (el) { el.classList.add('is-in'); }); }

    /* Counter animation */
    var cnt = $$('.v-count');
    if ('IntersectionObserver' in window && cnt.length) {
      var io2 = new IntersectionObserver(function (en) {
        en.forEach(function (x) {
          if (!x.isIntersecting) return;
          var el = x.target, to = parseInt(el.getAttribute('data-to'), 10) || 0, t0 = null;
          function step(ts) {
            if (!t0) t0 = ts;
            var p = Math.min((ts - t0) / 1100, 1);
            el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID');
            if (p < 1) requestAnimationFrame(step);
          }
          requestAnimationFrame(step); io2.unobserve(el);
        });
      }, { threshold: .4 });
      cnt.forEach(function (el) { io2.observe(el); });
    }

    /* Tilt (ringan, tanpa library) */
    if (window.matchMedia('(hover:hover)').matches && !D.body.classList.contains('v-no-motion')) {
      $$('[data-tilt]').forEach(function (el) {
        el.addEventListener('mousemove', function (ev) {
          var r = el.getBoundingClientRect();
          var rx = ((ev.clientY - r.top) / r.height - .5) * -7;
          var ry = ((ev.clientX - r.left) / r.width - .5) * 9;
          el.style.transform = 'perspective(1000px) rotateX(' + rx + 'deg) rotateY(' + ry + 'deg) translateY(-5px)';
        });
        el.addEventListener('mouseleave', function () { el.style.transform = ''; });
      });
    }

    /* Hero slider */
    var hero = $('#vHero');
    if (hero) {
      var slides = $$('.v-slide', hero), dots = $$('[data-hero-dot]', hero);
      var idx = 0, timer = null, gap = parseInt(hero.getAttribute('data-interval'), 10) || 6000;
      function go(n) {
        idx = (n + slides.length) % slides.length;
        slides.forEach(function (s, i) { s.classList.toggle('is-active', i === idx); });
        dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
      }
      function play() { if (slides.length > 1) { stop(); timer = setInterval(function () { go(idx + 1); }, gap); } }
      function stop() { if (timer) clearInterval(timer); }
      dots.forEach(function (d, i) { d.addEventListener('click', function () { go(i); play(); }); });
      var pv = $('[data-hero="prev"]', hero), nx = $('[data-hero="next"]', hero);
      if (pv) pv.addEventListener('click', function () { go(idx - 1); play(); });
      if (nx) nx.addEventListener('click', function () { go(idx + 1); play(); });
      hero.addEventListener('mouseenter', stop);
      hero.addEventListener('mouseleave', play);
      var sx = null;
      hero.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; stop(); }, { passive: true });
      hero.addEventListener('touchend', function (e) {
        if (sx === null) return;
        var dx = e.changedTouches[0].clientX - sx;
        if (Math.abs(dx) > 45) go(idx + (dx < 0 ? 1 : -1));
        sx = null; play();
      });
      D.addEventListener('visibilitychange', function () { D.hidden ? stop() : play(); });
      play();
    }

    /* Smooth anchor */
    $$('a[href^="#v-"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var el = $(a.getAttribute('href'));
        if (el) { e.preventDefault(); el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
      });
    });
  });
})();