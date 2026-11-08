/*! VENOM · Core App Controller (FINAL - slider bug fixed) */
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

  /* Mode / Palette / Particle */
  function setMode(m) { R.setAttribute('data-theme', m); store.set('venom.mode', m); }
  function setPalette(p) {
    R.setAttribute('data-palette', p); store.set('venom.palette', p);
    $$('.vnx-fx-swatch').forEach(function (s) {
      s.classList.toggle('is-active', s.getAttribute('data-palette') === p);
    });
    toast('Palet: ' + p);
  }
  function setParticle(eff) {
    R.setAttribute('data-particle', eff); store.set('venom.particle', eff);
    $$('.vnx-fx-opt[data-particle]').forEach(function (o) {
      o.classList.toggle('is-active', o.getAttribute('data-particle') === eff);
    });
    toast('Efek: ' + eff);
  }

  D.addEventListener('DOMContentLoaded', function () {
    setMode(store.get('venom.mode') || (R.getAttribute('data-theme') === 'dark' ? 'dark' : 'light'));
    setPalette(store.get('venom.palette') || 'venom');
    var particle = store.get('venom.particle') || 'stars';
    R.setAttribute('data-particle', particle);
    $$('.vnx-fx-opt[data-particle]').forEach(function (o) {
      o.classList.toggle('is-active', o.getAttribute('data-particle') === particle);
    });

    /* Delegated clicks: venom actions, palette, particle, copy, modal open/close */
    D.addEventListener('click', function (e) {
      var op = e.target.closest('[data-modal-open]');
      if (op) { var om = D.getElementById(op.getAttribute('data-modal-open')); if (om) om.classList.add('is-open'); return; }
      var cl = e.target.closest('.vnx-modal [data-close]');
      if (cl) { var cm = cl.closest('.vnx-modal'); if (cm) cm.classList.remove('is-open'); return; }

      var t = e.target.closest('[data-venom],[data-palette-set],[data-copy],[data-particle]');
      if (!t) return;
      if (t.hasAttribute('data-palette-set')) { setPalette(t.getAttribute('data-palette-set')); return; }
      if (t.hasAttribute('data-particle')) { setParticle(t.getAttribute('data-particle')); return; }
      if (t.hasAttribute('data-copy')) {
        var txt = t.getAttribute('data-copy'), sel = $(t.getAttribute('data-copy-target') || '');
        if (sel) txt = sel.innerText;
        if (navigator.clipboard) navigator.clipboard.writeText(txt).then(function () { toast('Disalin'); });
        return;
      }
      var a = t.getAttribute('data-venom');
      if (a === 'mode') { setMode(R.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'); }
      else if (a === 'top') { window.scrollTo({ top: 0, behavior: 'smooth' }); }
      else if (a === 'fx-toggle') { var d = $('#vFxD'); if (d) d.classList.toggle('is-open'); }
      else if (a === 'menu') {
        var m = $('#vMenu'), open = m && m.classList.toggle('is-open');
        t.setAttribute('aria-expanded', open ? 'true' : 'false');
      }
    });

    /* Keyboard */
    D.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        var m = $('#vMenu'); if (m) m.classList.remove('is-open');
        var fx = $('#vFxD'); if (fx) fx.classList.remove('is-open');
        $$('.vnx-modal.is-open').forEach(function (x) { x.classList.remove('is-open'); });
      }
      if (e.key === '/' && !/input|textarea|select/i.test(D.activeElement.tagName)) {
        var s = $('.vnx-search-box input') || $('.v-search input');
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

    /* Tilt */
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

    /* ═══════════════════════════════════════════════════
       PHOTO SLIDER (FIXED: tanpa replaceChild, tanpa error)
       ═══════════════════════════════════════════════════ */
    $$('.vnx-slider').forEach(function (slider) {
      var slides = $$('.vnx-slide', slider);
      var dots   = $$('.vnx-slide-dot', slider);
      var prev   = $('.vnx-slide-prev', slider);
      var next   = $('.vnx-slide-next', slider);
      var counter= $('.vnx-slide-counter', slider);
      if (!slides.length) return;

      var idx = 0, timer = null;
      var gap = parseInt(slider.getAttribute('data-interval'), 10) || 6000;

      function updateCounter() { if (counter) counter.textContent = (idx + 1) + ' / ' + slides.length; }

      function go(n) {
        idx = ((n % slides.length) + slides.length) % slides.length;
        slides.forEach(function (s, i) { s.classList.toggle('is-active', i === idx); });
        dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
        var act = dots[idx];
        if (act) { act.classList.remove('is-active'); void act.offsetWidth; act.classList.add('is-active'); }
        updateCounter();
      }
      function play() { if (slides.length > 1) { stop(); timer = setInterval(function () { go(idx + 1); }, gap); } }
      function stop() { if (timer) { clearInterval(timer); timer = null; } }

      dots.forEach(function (d, i) { d.addEventListener('click', function () { go(i); play(); }); });
      if (prev) prev.addEventListener('click', function () { go(idx - 1); play(); });
      if (next) next.addEventListener('click', function () { go(idx + 1); play(); });
      slider.addEventListener('mouseenter', stop);
      slider.addEventListener('mouseleave', play);

      var sx = null;
      slider.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; stop(); }, { passive: true });
      slider.addEventListener('touchend', function (e) {
        if (sx === null) return;
        var dx = e.changedTouches[0].clientX - sx;
        if (Math.abs(dx) > 45) go(idx + (dx < 0 ? 1 : -1));
        sx = null; play();
      });
      slider.setAttribute('tabindex', '0');
      slider.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') { go(idx - 1); play(); }
        else if (e.key === 'ArrowRight') { go(idx + 1); play(); }
      });
      D.addEventListener('visibilitychange', function () { D.hidden ? stop() : play(); });

      go(0); play();
    });

    /* Smooth anchor */
    $$('a[href^="#v-"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var el = $(a.getAttribute('href'));
        if (el) { e.preventDefault(); el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
      });
    });
  });
})();