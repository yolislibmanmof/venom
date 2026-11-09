/*! VENOM · Theme Viewer */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  var tpl = $('#venom-theme-viewer-config');
  if (!tpl) return;
  var cfg;
  try { cfg = JSON.parse(tpl.textContent || '{}'); } catch (e) { return; }
  if (!cfg || cfg.enabled === false) return;

  var fab = $('#venomThemeFab'), drawer = $('#venomThemeDrawer');
  var closeBtns = $$('[data-venom-theme-close]');
  var search = $('#venomThemeSearch');
  var saveBtn = $('#venomThemeSave'), statusEl = $('#venomThemeStatus'), resetBtn = $('#venomThemeReset');
  if (!fab || !drawer) return;

  var STORAGE = { draft: 'venom-theme-draft', saved: 'venom-theme-saved', rev: 'venom-theme-rev' };
  var tinfoValues = cfg.tinfoValues || {};

  function setStatus(msg, state) {
    if (!statusEl) return;
    statusEl.textContent = msg || '';
    statusEl.dataset.state = state || '';
  }
  function openDrawer() {
    drawer.hidden = false; drawer.setAttribute('aria-hidden', 'false');
    document.body.classList.add('vnx-theme-drawer-open');
    probeAdmin();
  }
  function closeDrawer() {
    drawer.hidden = true; drawer.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('vnx-theme-drawer-open');
  }

  fab.addEventListener('click', function (e) { e.preventDefault(); drawer.hidden ? openDrawer() : closeDrawer(); });
  closeBtns.forEach(function (b) { b.addEventListener('click', closeDrawer); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !drawer.hidden) closeDrawer(); });

  // Live preview
  function readDraft() {
    var out = {};
    $$('[data-venom-field]', drawer).forEach(function (f) {
      out[f.getAttribute('data-venom-field')] = f.value == null ? '' : String(f.value);
    });
    return out;
  }
  function applyPreview(vals) {
    var body = document.body, root = document.documentElement;
    // CSS variables dari palet
    if (vals.venom_theme_color) {
      var pal = (cfg.palettes || {})[vals.venom_theme_color];
      if (pal && pal.p) {
        root.style.setProperty('--v-p', pal.p);
        root.style.setProperty('--v-a', pal.a || pal.p);
      }
      root.setAttribute('data-palette', vals.venom_theme_color);
    }
    // Mode
    if (vals.venom_mode) {
      var mode = vals.venom_mode;
      if (mode === 'auto') mode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      root.setAttribute('data-theme', mode);
    }
    // Container width & radius
    if (vals.venom_container_width) root.style.setProperty('--v-max', (parseInt(vals.venom_container_width, 10) || 1240) + 'px');
    if (vals.venom_border_radius) root.style.setProperty('--v-r', (parseInt(vals.venom_border_radius, 10) || 18) + 'px');
    // Glass effect
    body.classList.toggle('vnx-no-glass', String(vals.venom_glass_effect) === '0');
    body.classList.toggle('v-no-motion', String(vals.venom_motion) === '0');
    // Font
    if (vals.venom_font_family) body.setAttribute('data-font', vals.venom_font_family);
    // Data attributes untuk section visibility
    Object.keys(vals).forEach(function (k) {
      if (k.indexOf('venom_') !== 0) return;
      var key = 'data-venom-' + k.slice(6).replace(/_/g, '-');
      body.setAttribute(key, String(vals[k]));
    });
    // Sembunyikan section sesuai nilai
    var sectionMap = {
      venom_stats_show: '.v-stats', venom_new_arrivals_show: '[data-section="new-arrivals"]',
      venom_featured_show: '[data-section="featured"]', venom_topics_show: '[data-section="topics"]',
      venom_news_show: '[data-section="news"]'
    };
    Object.keys(sectionMap).forEach(function (k) {
      var sel = sectionMap[k];
      var show = String(vals[k]) !== '0';
      $$(sel).forEach(function (el) { el.hidden = !show; });
    });
    // Custom CSS
    var existing = $('#venom-theme-custom-css');
    if (!existing) { existing = document.createElement('style'); existing.id = 'venom-theme-custom-css'; document.head.appendChild(existing); }
    existing.textContent = String(vals.venom_custom_css || '');

    try { localStorage.setItem(STORAGE.draft, JSON.stringify(vals)); } catch (e) {}
  }

  drawer.addEventListener('input', function (e) {
    if (e.target.matches && e.target.matches('[data-venom-field]')) applyPreview(readDraft());
  });
  drawer.addEventListener('change', function (e) {
    if (e.target.matches && e.target.matches('[data-venom-field]')) applyPreview(readDraft());
  });

  // Search filter
  if (search) {
    search.addEventListener('input', function () {
      var q = search.value.trim().toLowerCase();
      $$('.vnx-theme-field', drawer).forEach(function (f) {
        var item = (f.getAttribute('data-venom-item') || '') + ' ' + (f.textContent || '');
        f.hidden = q !== '' && item.toLowerCase().indexOf(q) === -1;
      });
      $$('.vnx-theme-group', drawer).forEach(function (g) {
        var any = $$('.vnx-theme-field', g).some(function (f) { return !f.hidden; });
        g.hidden = !any;
        if (any && q !== '') g.open = true;
      });
    });
  }

  // Reset
  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      $$('[data-venom-field]', drawer).forEach(function (f) {
        var name = f.getAttribute('data-venom-field');
        f.value = tinfoValues[name] || '';
      });
      applyPreview(readDraft());
      try { localStorage.removeItem(STORAGE.draft); } catch (e) {}
    });
  }

  // Admin probe
  var adminFormData = null;
  function probeAdmin() {
    if (!saveBtn || !cfg.adminThemeEndpoint) return;
    setStatus(cfg.labels.checking || 'Memeriksa...');
    var url = cfg.adminThemeEndpoint + '?customize=public&theme=' + encodeURIComponent(cfg.adminThemeDir || 'venom');
    fetch(url, { credentials: 'include', cache: 'no-store' })
      .then(function (r) { return r.text().then(function (html) { return { ok: r.ok, html: html }; }); })
      .then(function (data) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(data.html, 'text/html');
        var form = doc.querySelector('#mainForm, form[name="mainForm"]');
        var denied = /you are not authorized|session has timed out|not customizable/i.test(data.html);
        if (!data.ok || !form || denied) {
          adminFormData = null;
          saveBtn.hidden = true;
          setStatus(cfg.labels.login || 'Masuk admin untuk menyimpan.');
          return;
        }
        adminFormData = new FormData(form);
        saveBtn.hidden = false;
        setStatus('');
      })
      .catch(function () {
        adminFormData = null; saveBtn.hidden = true;
        setStatus(cfg.labels.login || 'Masuk admin untuk menyimpan.');
      });
  }

  // Save
  if (saveBtn) {
    saveBtn.addEventListener('click', function () {
      if (!adminFormData) { probeAdmin(); return; }
      var vals = readDraft();
      var fd = new FormData();
      adminFormData.forEach(function (v, k) { fd.append(k, v); });
      fd.set('updateData', '1');
      fd.set('themeType', 'public');
      fd.set('themeDir', cfg.adminThemeDir || 'venom');
      Object.keys(vals).forEach(function (k) { if (k) fd.set(k, vals[k]); });
      saveBtn.disabled = true;
      setStatus(cfg.labels.saving || 'Menyimpan...', 'saving');
      fetch(cfg.adminThemeEndpoint, {
        method: 'POST', credentials: 'include', cache: 'no-store',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: new URLSearchParams(fd).toString()
      }).then(function (r) { return r.text(); }).then(function (html) {
        if (!/saved|success/i.test(html) && /error|unauthorized|not customizable/i.test(html)) {
          throw new Error('rejected');
        }
        try { localStorage.setItem(STORAGE.saved, JSON.stringify(vals)); } catch (e) {}
        setStatus(cfg.labels.saved || 'Tersimpan.', 'success');
        setTimeout(function () { window.location.reload(); }, 900);
      }).catch(function () {
        setStatus(cfg.labels.error || 'Gagal menyimpan.', 'error');
        saveBtn.disabled = false;
      });
    });
  }

  // Load saved draft on boot
  try {
    var draft = JSON.parse(localStorage.getItem(STORAGE.draft) || '{}');
    if (draft && typeof draft === 'object') {
      Object.keys(draft).forEach(function (k) {
        var f = drawer.querySelector('[data-venom-field="' + k + '"]');
        if (f) f.value = draft[k];
      });
      applyPreview(draft);
    }
  } catch (e) {}
})();