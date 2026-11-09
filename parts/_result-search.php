<?php
/**
 * VENOM · Search Result Page (v5 — Sidebar Layout ala SLiMS Default + VENOM Style)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/../helpers/core.php';

$engine     = Venom_Core::get('engine', $GLOBALS['engine'] ?? null);
$opac       = Venom_Core::get('opac', $GLOBALS['opac'] ?? null);
$keywords   = Venom_Core::get('keywords', $GLOBALS['keywords'] ?? ($_GET['keywords'] ?? ''));
$pagination = Venom_Core::get('pagination', $GLOBALS['pagination'] ?? '');
$keywords   = is_string($keywords) ? trim($keywords) : '';
$view_mode  = $_POST['view'] ?? $_GET['view'] ?? $_SESSION['LIST_VIEW'] ?? 'list';
if (!in_array($view_mode, ['simple', 'list', 'grid'], true)) $view_mode = 'list';

$main_content = venom_core_content();
$num_rows    = (is_object($engine) && method_exists($engine, 'getNumRows')) ? (int) $engine->getNumRows() : 0;
$filter_html = (is_object($engine) && is_object($opac) && method_exists($engine, 'getFilter')) ? (string) $engine->getFilter($opac, true) : '';
$csrf        = (is_object($opac) && method_exists($opac, 'getCsrf')) ? $opac->getCsrf() : Venom_Sec::token('search');
$kw_disp     = (strlen($keywords) > 60) ? substr($keywords, 0, 60) . '…' : $keywords;
$cur_sort    = (string) ($_GET['sortby'] ?? 'most-relevant');

function v_url_merge(array $add) { return $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, $add)); }

$sorts = [
    'most-relevant'       => 'Paling relevan',
    'recently-added'      => 'Terbaru ditambah',
    'last-update'         => 'Pembaruan terakhir',
    'most-loaned'         => 'Paling banyak dipinjam',
    'publish-year-newest' => 'Tahun terbit (terbaru)',
    'publish-year-oldest' => 'Tahun terbit (terlama)',
    'title-asc'           => 'Judul A-Z',
    'title-desc'          => 'Judul Z-A',
];
$views = [
    'list'   => ['list', 'List', 'Detail dengan cover'],
    'grid'   => ['grid', 'Grid', 'Kartu grid modern'],
    'simple' => ['menu', 'Simple', 'Baris kompak'],
];
?>

<!-- TOOLBAR (inline, di atas hasil) -->
<div class="v-results-toolbar">
    <div class="v-results-summary">
        <span class="v-results-found">Ditemukan <strong><?= venom_num($num_rows) ?></strong></span>
        <?php if ($kw_disp !== ''): ?>
            <span> dari kata kunci:</span>
            <span class="v-keyword-chip" title="<?= venom_e($keywords) ?>"><?= venom_e($kw_disp) ?></span>
        <?php else: ?>
            <span> koleksi</span>
        <?php endif; ?>
    </div>
    <div class="v-results-controls">
        <!-- DROPDOWN SORT -->
        <div class="v-dropdown" data-dropdown="sort">
            <button type="button" class="v-dd-btn" data-dd-toggle>
                <span>Urutkan:</span>
                <strong><?= venom_e($sorts[$cur_sort] ?? 'Paling relevan') ?></strong>
                <?= venom_icon('down', 14) ?>
            </button>
            <div class="v-dd-menu">
                <?php foreach ($sorts as $k => $label): ?>
                    <a href="<?= venom_e(v_url_merge(['sortby' => $k])) ?>"
                       class="v-dd-item<?= $cur_sort === $k ? ' is-active' : '' ?>">
                        <?= venom_e($label) ?>
                        <?php if ($cur_sort === $k): ?><?= venom_icon('check', 14) ?><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- DROPDOWN VIEW -->
        <div class="v-dropdown" data-dropdown="view">
            <button type="button" class="v-dd-btn" data-dd-toggle>
                <?= venom_icon($views[$view_mode][0], 14) ?>
                <strong><?= $views[$view_mode][1] ?></strong>
                <?= venom_icon('down', 14) ?>
            </button>
            <div class="v-dd-menu">
                <?php foreach ($views as $vk => $vv): ?>
                    <a href="<?= venom_e(v_url_merge(['view' => $vk, 'csrf_token' => $csrf])) ?>"
                       class="v-dd-item<?= $view_mode === $vk ? ' is-active' : '' ?>">
                        <?= venom_icon($vv[0], 16) ?>
                        <span><b><?= $vv[1] ?></b><small><?= $vv[2] ?></small></span>
                        <?php if ($view_mode === $vk): ?><?= venom_icon('check', 14) ?><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Toggle sidebar mobile -->
        <button type="button" class="v-filter-toggle-mobile" data-filter-toggle>
            <?= venom_icon('filter', 16) ?> Filter
        </button>
    </div>
</div>

<!-- LAYOUT 2 KOLOM: SIDEBAR + KONTEN -->
<div class="v-results-layout">
    <!-- SIDEBAR FILTER -->
    <aside class="v-results-sidebar" id="vResultsSidebar">
        <div class="v-sidebar-head">
            <h3><?= venom_icon('filter', 18) ?> Saring Hasil</h3>
            <button type="button" class="v-sidebar-close" data-filter-close><?= venom_icon('x', 18) ?></button>
        </div>
        <div class="v-sidebar-body">
            <?php if ($filter_html !== ''): ?>
                <div class="v-filter-raw"><?= $filter_html ?></div>
            <?php else: ?>
                <p style="color:var(--v-mut);font-size:.85rem;padding:12px">Tidak ada filter yang tersedia.</p>
            <?php endif; ?>
        </div>
        <?php if ($filter_html !== ''): ?>
        <div class="v-sidebar-foot">
            <button type="button" class="v-btn v-btn-ghost v-btn-sm" data-venom-reset-filter>
                <?= venom_icon('left', 14) ?> Reset
            </button>
            <button type="button" class="v-btn v-btn-primary v-btn-sm" data-venom-apply-filter>
                <?= venom_icon('check', 14) ?> Terapkan
            </button>
        </div>
        <?php endif; ?>
    </aside>

    <!-- KONTEN HASIL -->
    <section class="v-results-content">
        <?php if ($num_rows === 0 || trim(strip_tags((string) $main_content)) === ''): ?>
            <div class="v-empty">
                <?= venom_icon('search', 48) ?>
                <h3>Tidak Ada Hasil</h3>
                <p>Periksa ejaan, atau longgarkan filter yang aktif.</p>
                <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:16px">
                    <a href="index.php?search=Search" class="v-btn v-btn-primary v-btn-sm">Reset Pencarian</a>
                    <button type="button" class="v-btn v-btn-ghost v-btn-sm" data-modal-open="vnxAdvancedModal">
                        <?= venom_icon('filter', 14) ?> Pencarian Lanjutan
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="<?= $view_mode === 'grid' ? 'v-grid v-grid-books' : ($view_mode === 'list' ? 'v-list-books' : 'v-simple-books') ?>">
                <?= $main_content ?>
            </div>
            <?php if (!empty($pagination)): ?>
                <div class="v-pagination"><div class="paging"><?= $pagination ?></div></div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</div>

<!-- MODAL: Pencarian Lanjutan (tetap dipertahankan) -->
<?php venom_part('_modal-advanced'); ?>

<style>
/* ═══════════════════════════════════════════════════════════════
   VENOM · Results Page — Sidebar Layout (ala SLiMS Default)
   ═══════════════════════════════════════════════════════════════ */

/* Toolbar */
.v-results-toolbar{
  position:relative;z-index:70; /* ✅ FIX: angkat stacking context toolbar di atas kartu hasil */
  display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;
  padding:14px 18px;margin-bottom:22px;
  background:var(--v-glass);border:1px solid var(--v-gline);border-radius:var(--v-r);
  backdrop-filter:blur(16px);box-shadow:var(--v-sh);
}
.v-results-summary{display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:.92rem;color:var(--v-mut)}
.v-results-found strong{color:var(--v-tx);font-size:1.05rem;font-weight:800}
.v-keyword-chip{padding:5px 12px;border-radius:999px;background:color-mix(in srgb,var(--v-p) 12%,transparent);color:var(--v-p);font-weight:700;font-size:.84rem;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.v-results-controls{display:flex;align-items:center;gap:8px;flex-wrap:wrap}

/* Dropdown Inline */
.v-dropdown{position:relative;flex:0 0 auto}
.v-dd-btn{
  display:inline-flex;align-items:center;gap:8px;padding:9px 16px;
  border-radius:10px;border:1px solid var(--v-line);background:var(--v-surf2);
  color:var(--v-tx);font-size:.84rem;cursor:pointer;transition:.2s;
  white-space:nowrap;
}
.v-dd-btn span{color:var(--v-mut);font-weight:500}
.v-dd-btn strong{color:var(--v-tx);font-weight:700}
.v-dd-btn svg{color:var(--v-mut);transition:transform .2s}
.v-dd-btn:hover{border-color:var(--v-p);color:var(--v-p)}
.v-dropdown.is-open .v-dd-btn svg:last-child{transform:rotate(180deg)}
.v-dd-menu{
  position:absolute;top:calc(100% + 6px);right:0;min-width:220px;max-width:320px;
  padding:6px;border-radius:12px;background:var(--v-surf);border:1px solid var(--v-line);
  box-shadow:var(--v-sh2);z-index:999;
  display:none;flex-direction:column;gap:2px;
  animation:vnxModalIn .2s ease;
}
.v-dropdown.is-open .v-dd-menu{display:flex}
.v-dd-item{
  display:flex;justify-content:space-between;align-items:center;gap:10px;
  padding:10px 14px;border-radius:8px;color:var(--v-tx);text-decoration:none;
  font-size:.86rem;font-weight:500;transition:.15s;
}
.v-dd-item span{display:flex;flex-direction:column;gap:2px}
.v-dd-item b{color:var(--v-tx);font-weight:700;font-size:.86rem}
.v-dd-item small{color:var(--v-mut);font-size:.72rem;font-weight:500}
.v-dd-item:hover{background:color-mix(in srgb,var(--v-p) 8%,transparent)}
.v-dd-item.is-active{background:color-mix(in srgb,var(--v-p) 12%,transparent);color:var(--v-p);font-weight:700}
.v-dd-item.is-active b{color:var(--v-p)}

/* Layout 2 Kolom */
.v-results-layout{position:relative;z-index:1;display:grid;grid-template-columns:280px 1fr;gap:28px;align-items:start}
.v-results-sidebar{
  position:sticky;top:90px;max-height:calc(100vh - 110px);overflow:hidden;
  background:var(--v-glass);border:1px solid var(--v-gline);border-radius:var(--v-r);
  backdrop-filter:blur(16px);box-shadow:var(--v-sh);
  display:flex;flex-direction:column;
}
.v-sidebar-head{
  display:flex;justify-content:space-between;align-items:center;gap:10px;
  padding:16px 18px;border-bottom:1px solid var(--v-line);flex:0 0 auto;
}
.v-sidebar-head h3{margin:0;font-size:1rem;font-weight:800;color:var(--v-tx);display:flex;align-items:center;gap:8px}
.v-sidebar-close{
  display:none;place-items:center;width:32px;height:32px;border-radius:8px;
  border:1px solid var(--v-line);background:transparent;color:var(--v-mut);cursor:pointer;
}
.v-sidebar-body{flex:1;overflow-y:auto;padding:4px 18px 18px;overscroll-behavior:contain}
.v-sidebar-foot{
  display:flex;gap:8px;padding:14px 18px;border-top:1px solid var(--v-line);
  background:var(--v-surf);flex:0 0 auto;
}
.v-sidebar-foot .v-btn{flex:1;justify-content:center}
.v-results-content{min-width:0}

/* Filter Raw Styling */
.v-filter-raw h1,.v-filter-raw h2,.v-filter-raw h3,.v-filter-raw h4,.v-filter-raw h5,.v-filter-raw legend{
  display:flex;align-items:center;gap:8px;font-size:.76rem;font-weight:800;
  text-transform:uppercase;letter-spacing:.06em;color:var(--v-tx);
  margin:18px 0 10px;cursor:pointer;
}
.v-filter-raw h1::before,.v-filter-raw h2::before,.v-filter-raw h3::before,
.v-filter-raw h4::before,.v-filter-raw h5::before,.v-filter-raw legend::before{
  content:'';width:7px;height:7px;border-radius:50%;
  background:linear-gradient(120deg,var(--v-p),var(--v-a));
  box-shadow:0 0 8px color-mix(in srgb,var(--v-p) 60%,transparent);
  flex:0 0 auto;
}
.v-filter-raw label{
  position:relative;display:inline-flex;align-items:center;gap:6px;
  margin:0 6px 6px 0;padding:7px 13px;border-radius:999px;
  background:var(--v-surf2);border:1px solid var(--v-line);
  font-size:.78rem;font-weight:600;cursor:pointer;transition:.2s;user-select:none;
}
.v-filter-raw label:hover{border-color:var(--v-p);transform:translateY(-1px)}
.v-filter-raw label.is-on,.v-filter-raw label:has(input:checked){
  background:linear-gradient(120deg,var(--v-p),var(--v-a));color:#fff;
  border-color:transparent;box-shadow:0 4px 12px color-mix(in srgb,var(--v-p) 30%,transparent);
}
.v-filter-raw label.is-on::after,.v-filter-raw label:has(input:checked)::after{
  content:'✓';font-weight:800;font-size:.7rem;margin-left:2px;
}
.v-filter-raw input[type=checkbox],.v-filter-raw input[type=radio]{
  position:absolute;opacity:0;width:1px;height:1px;
}
.v-filter-raw input[type=text],.v-filter-raw input[type=number],.v-filter-raw select{
  width:100%;padding:9px 12px;border-radius:10px;border:1px solid var(--v-line);
  background:var(--v-surf2);color:var(--v-tx);font-size:.84rem;outline:0;margin:4px 0;
}
.v-filter-raw input:focus,.v-filter-raw select:focus{
  border-color:var(--v-p);box-shadow:0 0 0 3px color-mix(in srgb,var(--v-p) 12%,transparent);
}
.v-filter-raw input[type=submit],.v-filter-raw button[type=submit]{display:none !important}
.v-filter-raw a{color:var(--v-p);font-weight:700;font-size:.78rem}

/* Filter toggle mobile (hidden di desktop) */
.v-filter-toggle-mobile{
  display:none;align-items:center;gap:6px;padding:9px 14px;border-radius:10px;
  border:1px solid var(--v-p);background:linear-gradient(120deg,var(--v-p),var(--v-a));
  color:#fff;font-weight:700;font-size:.84rem;cursor:pointer;
}

/* ═══════ Responsive ═══════ */
@media (max-width: 960px) {
  .v-results-layout{grid-template-columns:1fr}
  .v-results-sidebar{
    position:fixed;inset:0 auto 0 0;width:min(340px,88vw);z-index:150;
    max-height:none;border-radius:0 var(--v-r) var(--v-r) 0;
    transform:translateX(-110%);transition:transform .3s cubic-bezier(.4,0,.2,1);
    box-shadow:var(--v-sh2);
  }
  .v-results-sidebar.is-open{transform:translateX(0)}
  .v-sidebar-close{display:grid}
  .v-filter-toggle-mobile{display:inline-flex}
  .v-results-sidebar::before{
    content:'';position:fixed;inset:0;background:rgba(0,0,0,.5);
    z-index:-1;opacity:0;pointer-events:none;transition:opacity .3s;
  }
  .v-results-sidebar.is-open::before{opacity:1;pointer-events:auto}
}
@media (max-width: 640px) {
  .v-results-toolbar{padding:12px 14px}
  .v-dd-btn span{display:none}
  .v-dd-btn strong{font-size:.82rem}
}
</style>

<script>
/* ═══ VENOM · Results Page Controller (Dropdown + Sidebar + Filter Guard) ═══ */
(function () {
  var KW = <?= json_encode($keywords, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var VIEW = <?= json_encode($view_mode) ?>;

  /* ── Inline Dropdown ── */
  document.querySelectorAll('[data-dd-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var dd = btn.closest('.v-dropdown');
      var wasOpen = dd.classList.contains('is-open');
      document.querySelectorAll('.v-dropdown.is-open').forEach(function (x) { x.classList.remove('is-open'); });
      if (!wasOpen) dd.classList.add('is-open');
    });
  });
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.v-dropdown')) {
      document.querySelectorAll('.v-dropdown.is-open').forEach(function (x) { x.classList.remove('is-open'); });
    }
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.v-dropdown.is-open').forEach(function (x) { x.classList.remove('is-open'); });
    }
  });

  /* ── Sidebar toggle mobile ── */
  var sidebar = document.getElementById('vResultsSidebar');
  var toggleBtn = document.querySelector('[data-filter-toggle]');
  var closeBtn = document.querySelector('[data-filter-close]');
  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function () { sidebar.classList.add('is-open'); });
  }
  if (closeBtn && sidebar) {
    closeBtn.addEventListener('click', function () { sidebar.classList.remove('is-open'); });
  }
  document.addEventListener('click', function (e) {
    if (sidebar && sidebar.classList.contains('is-open') &&
        !e.target.closest('.v-results-sidebar') && !e.target.closest('[data-filter-toggle]')) {
      sidebar.classList.remove('is-open');
    }
  });

  /* ── Filter Engine Guard ── */
  function forms() { return Array.prototype.slice.call(document.querySelectorAll('.v-filter-raw form')); }

  function patch() {
    forms().forEach(function (f) {
      function ensure(n, v) {
        if (!v) return;
        var el = f.querySelector('input[name="' + n + '"]');
        if (!el) { el = document.createElement('input'); el.type = 'hidden'; el.name = n; f.appendChild(el); }
        el.value = v;
      }
      ensure('search', 'Search'); ensure('keywords', KW); ensure('view', VIEW);
      if ((f.getAttribute('method') || '').toLowerCase() !== 'get') f.setAttribute('method', 'get');
    });
  }

  function syncPills() {
    document.querySelectorAll('.v-filter-raw input[type=checkbox],.v-filter-raw input[type=radio]').forEach(function (i) {
      var l = i.closest('label') || (i.id ? document.querySelector('label[for="' + i.id + '"]') : null);
      if (l) l.classList.toggle('is-on', i.checked);
    });
  }

  /* Slider tahun mandiri (pertahankan dari v4) */
  function buildYearControl(form) {
    if (form.querySelector('.v-yc')) return;
    if (form.querySelector('.irs')) return;
    var s = form.querySelector('.input-slider');
    var f = form.querySelector('.js-input-from');
    var t = form.querySelector('.js-input-to');
    if (!s || !f || !t) return;
    var min = parseInt(s.getAttribute('data-min'), 10); if (isNaN(min)) return;
    var max = parseInt(s.getAttribute('data-max'), 10); if (isNaN(max) || max <= min) return;

    var cluster = s.parentElement;
    var hasWrap = cluster && cluster !== form && cluster.querySelector('.js-input-from');
    if (hasWrap) cluster.style.display = 'none';
    [s, f, t].forEach(function (i) { if (i) i.style.display = 'none'; });
    form.querySelectorAll('input[type=text]').forEach(function (i) { if (!i.classList.contains('v-keep')) i.style.display = 'none'; });

    var div = document.createElement('div');
    div.className = 'v-yc';
    div.innerHTML =
      '<div class="v-yc-pills">' +
        '<input type="text" class="v-yc-input v-keep v-yc-from" inputmode="numeric" aria-label="Tahun dari">' +
        '<span class="v-yc-to">sampai</span>' +
        '<input type="text" class="v-yc-input v-keep v-yc-toin" inputmode="numeric" aria-label="Tahun sampai">' +
      '</div>' +
      '<div class="v-yc-range">' +
        '<div class="v-yc-track"></div><div class="v-yc-fill"></div>' +
        '<input type="range" class="v-yc-rmin" min="' + min + '" max="' + max + '" step="1">' +
        '<input type="range" class="v-yc-rmax" min="' + min + '" max="' + max + '" step="1">' +
      '</div>' +
      '<div class="v-yc-scale"><span>' + min + '</span><span>' + max + '</span></div>';
    (hasWrap ? cluster : s).parentNode.insertBefore(div, hasWrap ? cluster : s);

    var pF = div.querySelector('.v-yc-from'), pT = div.querySelector('.v-yc-toin');
    var rF = div.querySelector('.v-yc-rmin'), rT = div.querySelector('.v-yc-rmax');
    var fill = div.querySelector('.v-yc-fill');

    function clamp(v) { v = parseInt(v, 10); if (isNaN(v)) v = min; return Math.min(max, Math.max(min, v)); }
    function render() {
      var a = clamp(rF.value), b = clamp(rT.value);
      if (a > b) { var tmp = a; a = b; b = tmp; }
      pF.value = a; pT.value = b; f.value = a; t.value = b;
      var pa = (a - min) / (max - min) * 100, pb = (b - min) / (max - min) * 100;
      fill.style.left = pa + '%'; fill.style.width = (pb - pa) + '%';
    }
    rF.addEventListener('input', function () { if (+rF.value > +rT.value) rF.value = rT.value; render(); });
    rT.addEventListener('input', function () { if (+rT.value < +rF.value) rT.value = rF.value; render(); });
    pF.addEventListener('change', function () { rF.value = clamp(pF.value); render(); });
    pT.addEventListener('change', function () { rT.value = clamp(pT.value); render(); });

    var ia = parseInt(f.value, 10), ib = parseInt(t.value, 10);
    rF.value = (!isNaN(ia) && ia >= min && ia <= max) ? ia : min;
    rT.value = (!isNaN(ib) && ib >= min && ib <= max) ? ib : max;
    render();
  }

  function ensureYear() { forms().forEach(buildYearControl); }

  /* Section collapsible di sidebar (klik heading → toggle konten) */
  function setupCollapsible() {
    document.querySelectorAll('.v-filter-raw h1,.v-filter-raw h2,.v-filter-raw h3,.v-filter-raw h4,.v-filter-raw h5,.v-filter-raw legend').forEach(function (h) {
      if (h.dataset.collapsible) return;
      h.dataset.collapsible = '1';
      h.style.cursor = 'pointer';
      // Tambahkan indicator
      var ind = document.createElement('span');
      ind.className = 'v-sec-ind';
      ind.style.cssText = 'margin-left:auto;transition:transform .2s;color:var(--v-mut)';
      ind.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>';
      h.appendChild(ind);
      // Wrap konten sampai heading berikutnya
      var content = [];
      var next = h.nextElementSibling;
      while (next && !/^H[1-5]|LEGEND$/i.test(next.tagName)) {
        content.push(next);
        next = next.nextElementSibling;
      }
      var wrap = document.createElement('div');
      wrap.className = 'v-sec-body';
      wrap.style.cssText = 'overflow:hidden;transition:max-height .3s ease;max-height:1000px';
      if (content.length) {
        h.parentNode.insertBefore(wrap, content[0]);
        content.forEach(function (el) { wrap.appendChild(el); });
      }
      h.addEventListener('click', function () {
        var open = wrap.style.maxHeight !== '0px';
        wrap.style.maxHeight = open ? '0px' : '1000px';
        ind.style.transform = open ? 'rotate(-90deg)' : 'rotate(0deg)';
      });
    });
  }

  patch(); syncPills(); ensureYear(); setupCollapsible();

  document.addEventListener('change', function (e) {
    if (e.target.closest && e.target.closest('.v-filter-raw')) syncPills();
  }, true);

  document.addEventListener('click', function (e) {
    var ap = e.target.closest('[data-venom-apply-filter]');
    if (ap) {
      e.preventDefault(); patch(); ensureYear();
      var f = forms()[0];
      if (f) {
        var p = new URLSearchParams(new FormData(f));
        new URLSearchParams(location.search).forEach(function (v, k) { if (!p.has(k)) p.set(k, v); });
        p.set('search', 'Search');
        if (KW && !p.has('keywords')) p.set('keywords', KW);
        location.search = p.toString();
      }
      return;
    }
    var rs = e.target.closest('[data-venom-reset-filter]');
    if (rs) { e.preventDefault(); location.href = 'index.php?keywords=' + encodeURIComponent(KW) + '&search=Search&view=' + VIEW; }
  }, true);

  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (f && f.closest && f.closest('.v-filter-raw')) patch();
  }, true);
})();
</script>