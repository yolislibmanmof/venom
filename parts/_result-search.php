<?php
/**
 * VENOM · Search Result Page (FINAL — scope-safe + JSON filter + full styling)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

/* ═══ Variabel diwarisi dari scope index_template (Opac SLiMS) ═══ */
$engine = $engine ?? Venom_Core::get('engine', $GLOBALS['engine'] ?? null);
$opac   = $opac   ?? Venom_Core::get('opac',   $GLOBALS['opac']   ?? null);
$keywords = (isset($keywords) && is_string($keywords)) ? $keywords : (string) ($_GET['keywords'] ?? '');
$keywords = trim(strip_tags(urldecode($keywords)));
$main_content = $main_content ?? venom_core_content();

$view_mode = $_POST['view'] ?? $_GET['view'] ?? $_SESSION['LIST_VIEW'] ?? 'grid';
if (!in_array($view_mode, ['simple', 'list', 'grid'], true)) $view_mode = 'grid';

/* ═══ Jumlah hasil (fallback: hitung kartu bila engine tak terbaca) ═══ */
$num_rows = (is_object($engine) && method_exists($engine, 'getNumRows')) ? (int) $engine->getNumRows() : 0;
if ($num_rows === 0 && trim(strip_tags((string) $main_content)) !== '') {
    preg_match_all('/class="v-book[ "\]]/', (string) $main_content, $m);
    $num_rows = count($m[0]);
}

/* ═══ Filter sidebar dari engine (tanpa guard $opac yang berlebihan) ═══ */
$filter_html = '';
if (is_object($engine) && method_exists($engine, 'getFilter')) {
    $filter_html = (string) @$engine->getFilter($opac, true);
}

$kw_disp  = (strlen($keywords) > 60) ? substr($keywords, 0, 60) . '…' : $keywords;
$cur_sort = (string) ($_GET['sortby'] ?? 'most-relevant');
$is_empty = trim(strip_tags((string) $main_content)) === '';

if (!function_exists('v_url_merge')) {
    function v_url_merge(array $add) {
        $q = array_merge($_GET, $add);
        unset($q['csrf_token']);
        return $_SERVER['PHP_SELF'] . '?' . http_build_query($q);
    }
}

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
    'grid'   => ['grid', 'Grid', 'Kartu grid modern'],
    'list'   => ['list', 'List', 'Detail dengan cover'],
    'simple' => ['menu', 'Simple', 'Baris kompak'],
];
?>

<!-- TOOLBAR -->
<div class="v-results-toolbar">
    <div class="v-results-summary">
        <span class="v-results-found">Ditemukan <strong><?= venom_num($num_rows) ?></strong></span>
        <?php if ($kw_disp !== ''): ?>
            <span>dari kata kunci:</span>
            <span class="v-keyword-chip" title="<?= venom_e($keywords) ?>"><?= venom_e($kw_disp) ?></span>
        <?php else: ?>
            <span>koleksi</span>
        <?php endif; ?>
    </div>
    <div class="v-results-controls">
        <div class="v-dropdown" data-dropdown="sort">
            <button type="button" class="v-dd-btn" data-dd-toggle>
                <span>Urutkan:</span>
                <strong><?= venom_e($sorts[$cur_sort] ?? 'Paling relevan') ?></strong>
                <?= venom_icon('down', 14) ?>
            </button>
            <div class="v-dd-menu">
                <?php foreach ($sorts as $k => $label): ?>
                    <a href="<?= venom_e(v_url_merge(['sortby' => $k])) ?>" class="v-dd-item<?= $cur_sort === $k ? ' is-active' : '' ?>">
                        <?= venom_e($label) ?>
                        <?php if ($cur_sort === $k): ?><?= venom_icon('check', 14) ?><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="v-dropdown" data-dropdown="view">
            <button type="button" class="v-dd-btn" data-dd-toggle>
                <?= venom_icon($views[$view_mode][0], 14) ?>
                <strong><?= $views[$view_mode][1] ?></strong>
                <?= venom_icon('down', 14) ?>
            </button>
            <div class="v-dd-menu">
                <?php foreach ($views as $vk => $vv): ?>
                    <a href="<?= venom_e(v_url_merge(['view' => $vk])) ?>" class="v-dd-item<?= $view_mode === $vk ? ' is-active' : '' ?>">
                        <?= venom_icon($vv[0], 16) ?>
                        <span><b><?= $vv[1] ?></b><small><?= $vv[2] ?></small></span>
                        <?php if ($view_mode === $vk): ?><?= venom_icon('check', 14) ?><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="button" class="v-filter-toggle-mobile" data-filter-toggle>
            <?= venom_icon('filter', 16) ?> Filter
        </button>
    </div>
</div>

<!-- LAYOUT 2 KOLOM -->
<div class="v-results-layout">
    <aside class="v-results-sidebar" id="vResultsSidebar">
        <div class="v-sidebar-head">
            <h3><?= venom_icon('filter', 18) ?> Ditapis dengan</h3>
            <button type="button" class="v-sidebar-close" data-filter-close><?= venom_icon('x', 18) ?></button>
        </div>
        <div class="v-sidebar-body">
            <?php if ($filter_html !== ''): ?>
                <div class="v-filter-raw"><?= $filter_html ?></div>
            <?php else: ?>
                <p style="color:var(--v-mut);font-size:.85rem;padding:12px">Filter tidak tersedia untuk mode ini.</p>
            <?php endif; ?>
        </div>
        <?php if ($filter_html !== ''): ?>
        <div class="v-sidebar-foot">
            <button type="button" class="v-btn v-btn-ghost v-btn-sm" data-venom-reset-filter><?= venom_icon('left', 14) ?> Reset</button>
            <button type="button" class="v-btn v-btn-primary v-btn-sm" data-venom-apply-filter><?= venom_icon('check', 14) ?> Terapkan</button>
        </div>
        <?php endif; ?>
    </aside>

    <section class="v-results-content">
        <?php if ($is_empty): ?>
            <div class="v-empty">
                <?= venom_icon('search', 48) ?>
                <h3>Tidak Ada Hasil</h3>
                <p>Periksa ejaan, atau longgarkan filter yang aktif.</p>
                <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:16px">
                    <a href="index.php?search=Search" class="v-btn v-btn-primary v-btn-sm">Reset Pencarian</a>
                    <button type="button" class="v-btn v-btn-ghost v-btn-sm" data-modal-open="vnxAdvancedModal"><?= venom_icon('filter', 14) ?> Pencarian Lanjutan</button>
                </div>
            </div>
        <?php else: ?>
            <div class="v-results-mode-<?= venom_e($view_mode) ?>">
                <?= $main_content ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php venom_part('_modal-advanced'); ?>

<style>
/* ═══════════ TOOLBAR & DROPDOWN (tetap) ═══════════ */
.v-results-toolbar{position:relative;z-index:70;display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;padding:14px 18px;margin-bottom:22px;background:var(--v-glass);border:1px solid var(--v-gline);border-radius:var(--v-r);backdrop-filter:blur(16px);box-shadow:var(--v-sh)}
.v-results-summary{display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:.92rem;color:var(--v-mut)}
.v-results-found strong{color:var(--v-tx);font-size:1.05rem;font-weight:800}
.v-keyword-chip{padding:5px 12px;border-radius:999px;background:color-mix(in srgb,var(--v-p) 12%,transparent);color:var(--v-p);font-weight:700;font-size:.84rem;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.v-results-controls{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.v-dropdown{position:relative;flex:0 0 auto}
.v-dd-btn{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:10px;border:1px solid var(--v-line);background:var(--v-surf2);color:var(--v-tx);font-size:.84rem;cursor:pointer;transition:.2s;white-space:nowrap}
.v-dd-btn span{color:var(--v-mut);font-weight:500}
.v-dd-btn strong{color:var(--v-tx);font-weight:700}
.v-dd-btn svg{color:var(--v-mut);transition:transform .2s}
.v-dd-btn:hover{border-color:var(--v-p);color:var(--v-p)}
.v-dropdown.is-open .v-dd-btn svg:last-child{transform:rotate(180deg)}
.v-dd-menu{position:absolute;top:calc(100% + 6px);right:0;min-width:220px;max-width:320px;padding:6px;border-radius:12px;background:var(--v-surf);border:1px solid var(--v-line);box-shadow:var(--v-sh2);z-index:999;display:none;flex-direction:column;gap:2px;animation:vnxModalIn .2s ease}
.v-dropdown.is-open .v-dd-menu{display:flex}
.v-dd-item{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;color:var(--v-tx);text-decoration:none;font-size:.86rem;font-weight:500;transition:.15s}
.v-dd-item span{display:flex;flex-direction:column;gap:2px}
.v-dd-item b{color:var(--v-tx);font-weight:700;font-size:.86rem}
.v-dd-item small{color:var(--v-mut);font-size:.72rem;font-weight:500}
.v-dd-item:hover{background:color-mix(in srgb,var(--v-p) 8%,transparent)}
.v-dd-item.is-active{background:color-mix(in srgb,var(--v-p) 12%,transparent);color:var(--v-p);font-weight:700}
.v-dd-item.is-active b{color:var(--v-p)}

/* ═══════════ LAYOUT & SIDEBAR (tetap) ═══════════ */
.v-results-layout{position:relative;z-index:1;display:grid;grid-template-columns:280px 1fr;gap:28px;align-items:start}
.v-results-sidebar{position:sticky;top:90px;max-height:calc(100vh - 110px);overflow:hidden;background:var(--v-glass);border:1px solid var(--v-gline);border-radius:var(--v-r);backdrop-filter:blur(16px);box-shadow:var(--v-sh);display:flex;flex-direction:column}
.v-sidebar-head{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:16px 18px;border-bottom:1px solid var(--v-line);flex:0 0 auto}
.v-sidebar-head h3{margin:0;font-size:1rem;font-weight:800;color:var(--v-tx);display:flex;align-items:center;gap:8px}
.v-sidebar-close{display:none;place-items:center;width:32px;height:32px;border-radius:8px;border:1px solid var(--v-line);background:transparent;color:var(--v-mut);cursor:pointer}
.v-sidebar-body{flex:1;overflow-y:auto;padding:4px 18px 18px;overscroll-behavior:contain}
.v-sidebar-foot{display:flex;gap:8px;padding:14px 18px;border-top:1px solid var(--v-line);background:var(--v-surf);flex:0 0 auto}
.v-sidebar-foot .v-btn{flex:1;justify-content:center}
.v-results-content{min-width:0}
.v-filter-toggle-mobile{display:none;align-items:center;gap:6px;padding:9px 14px;border-radius:10px;border:1px solid var(--v-p);background:linear-gradient(120deg,var(--v-p),var(--v-a));color:#fff;font-weight:700;font-size:.84rem;cursor:pointer}

/* ═══════════ STYLING FILTER MENTAH SLiMS (BARU — wajib) ═══════════ */
.v-filter-raw ul,.v-filter-raw li{list-style:none;margin:0;padding:0}
.v-filter-raw .list-group-item{padding:14px 4px;border-bottom:1px dashed var(--v-line)}
.v-filter-raw .list-group-item:last-child{border-bottom:0}
.v-filter-raw .d-flex{display:flex}.v-filter-raw .justify-content-between{justify-content:space-between}.v-filter-raw .align-items-center{align-items:center}.v-filter-raw .cursor-pointer{cursor:pointer}.v-filter-raw .text-sm{font-size:.84rem}
.v-filter-raw [data-toggle="collapse"] strong{font-size:.76rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--v-tx);display:flex;align-items:center;gap:8px}
.v-filter-raw [data-toggle="collapse"] strong::before{content:'';width:7px;height:7px;border-radius:50%;background:linear-gradient(120deg,var(--v-p),var(--v-a));box-shadow:0 0 8px color-mix(in srgb,var(--v-p) 60%,transparent);flex:0 0 auto}
.v-filter-raw [data-toggle="collapse"] .dropdown-toggle{width:9px;height:9px;border-right:2px solid var(--v-mut);border-bottom:2px solid var(--v-mut);transform:rotate(45deg);transition:transform .2s;margin-top:-4px;flex:0 0 auto}
.v-filter-raw [data-toggle="collapse"].is-closed .dropdown-toggle{transform:rotate(-45deg);margin-top:2px}
.v-filter-raw .collapse{display:none}
.v-filter-raw .collapse.show{display:block}
.v-filter-raw .collapse .mt-2{padding-top:12px}
.v-filter-raw .form-check{margin:0 0 6px;position:relative}
.v-filter-raw .form-check-input{position:absolute;opacity:0;width:1px;height:1px}
.v-filter-raw .form-check-label{display:inline-flex;align-items:center;gap:6px;margin:0;padding:7px 13px;border-radius:999px;background:var(--v-surf2);border:1px solid var(--v-line);font-size:.78rem;font-weight:600;color:var(--v-tx);cursor:pointer;transition:.2s;user-select:none}
.v-filter-raw .form-check-label:hover{border-color:var(--v-p);transform:translateY(-1px)}
.v-filter-raw .form-check-label.is-on{background:linear-gradient(120deg,var(--v-p),var(--v-a));color:#fff;border-color:transparent;box-shadow:0 4px 12px color-mix(in srgb,var(--v-p) 30%,transparent)}
.v-filter-raw .form-check-label.is-on::after{content:'✓';font-weight:800;font-size:.7rem;margin-left:2px}
.v-filter-raw .row,.v-filter-raw .input-slider,.v-filter-raw #sort{display:none!important}
.v-filter-raw .btn,.v-filter-raw button[type=submit],.v-filter-raw input[type=submit]{display:none!important}
.v-filter-raw a[data-toggle="collapse"]{display:inline-block;margin-top:8px;color:var(--v-p);font-weight:700;font-size:.76rem;text-decoration:none}

/* ═══════════ SLIDER TAHUN VENOM (v-yc) ═══════════ */
.v-yc{margin:6px 0 14px}
.v-yc-pills{display:flex;align-items:center;gap:8px;margin-bottom:12px}
.v-yc-input{width:76px;text-align:center;padding:7px 8px;border-radius:9px;border:1px solid var(--v-line);background:var(--v-surf2);color:var(--v-tx);font-size:.8rem;font-weight:700;outline:0}
.v-yc-input:focus{border-color:var(--v-p)}
.v-yc-to{font-size:.74rem;color:var(--v-mut)}
.v-yc-range{position:relative;height:26px}
.v-yc-track{position:absolute;left:0;right:0;top:11px;height:4px;border-radius:99px;background:var(--v-line)}
.v-yc-fill{position:absolute;top:11px;height:4px;border-radius:99px;background:linear-gradient(90deg,var(--v-p),var(--v-a))}
.v-yc-range input[type=range]{position:absolute;inset:0;width:100%;height:26px;margin:0;background:none;pointer-events:none;-webkit-appearance:none;appearance:none}
.v-yc-range input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;pointer-events:auto;width:16px;height:16px;border-radius:50%;background:#fff;border:3px solid var(--v-p);box-shadow:0 2px 6px rgba(0,0,0,.3);cursor:grab}
.v-yc-range input[type=range]::-moz-range-thumb{pointer-events:auto;width:11px;height:11px;border-radius:50%;background:#fff;border:3px solid var(--v-p);cursor:grab}
.v-yc-scale{display:flex;justify-content:space-between;font-size:.68rem;color:var(--v-mut);margin-top:4px}

/* ═══════════ MODE TAMPILAN HASIL (grid/list/simple) ═══════════ */
.v-mode-grid,.v-results-mode-grid{}.v-results-mode-grid .biblioResult{display:grid;grid-template-columns:repeat(auto-fill,minmax(178px,1fr));gap:18px}
.v-results-mode-list .biblioResult{display:flex;flex-direction:column;gap:16px}
.v-results-mode-list .biblioResult .v-book{flex-direction:row}
.v-results-mode-list .biblioResult .v-book-cover{width:130px;min-width:130px;aspect-ratio:auto;height:190px}
.v-results-mode-list .biblioResult .v-book-body{padding:20px 24px;justify-content:center}
.v-results-mode-list .biblioResult .v-book-title{font-size:1.1rem;-webkit-line-clamp:1}
.v-results-mode-simple .biblioResult{display:flex;flex-direction:column;gap:10px}
.v-results-mode-simple .biblioResult .v-book{flex-direction:row;align-items:center}
.v-results-mode-simple .biblioResult .v-book-cover{width:52px;min-width:52px;aspect-ratio:auto;height:70px;border-radius:10px}
.v-results-mode-simple .biblioResult .v-book-body{padding:10px 14px}
.v-results-mode-simple .biblioResult .v-book-meta,.v-results-mode-simple .biblioResult .v-book-hover{display:none}

/* ═══════════ PAGINATION BAWAAN SLiMS ═══════════ */
.v-results-content .biblioPaging{display:flex;gap:6px;flex-wrap:wrap;justify-content:center;margin:30px 0 6px}
.v-results-content .biblioPaging a,.v-results-content .biblioPaging span,.v-results-content .biblioPaging b{padding:8px 14px;border-radius:9px;text-decoration:none;font-weight:700;font-size:.86rem;background:var(--v-glass);border:1px solid var(--v-line);color:var(--v-tx);transition:.2s}
.v-results-content .biblioPaging a:hover{border-color:var(--v-p);color:var(--v-p);transform:translateY(-1px)}
.v-results-content .biblioPaging .current,.v-results-content .biblioPaging b{background:linear-gradient(120deg,var(--v-p),var(--v-a));color:#fff;border-color:transparent}

/* ═══════════ RESPONSIVE ═══════════ */
@media (max-width: 960px) {
  .v-results-layout{grid-template-columns:1fr}
  .v-results-sidebar{position:fixed;inset:0 auto 0 0;width:min(340px,88vw);z-index:150;max-height:none;border-radius:0 var(--v-r) var(--v-r) 0;transform:translateX(-110%);transition:transform .3s cubic-bezier(.4,0,.2,1);box-shadow:var(--v-sh2)}
  .v-results-sidebar.is-open{transform:translateX(0)}
  .v-sidebar-close{display:grid}
  .v-filter-toggle-mobile{display:inline-flex}
}
@media (max-width: 640px) {
  .v-results-toolbar{padding:12px 14px}
  .v-dd-btn span{display:none}
  .v-dd-btn strong{font-size:.82rem}
}
</style>

<script>
/* ═══ VENOM · Results Controller FINAL (JSON filter seperti core SLiMS) ═══ */
(function () {
  var KW = <?= json_encode($keywords, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var VIEW = <?= json_encode($view_mode) ?>;

  /* Dropdown */
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
    if (!e.target.closest('.v-dropdown')) document.querySelectorAll('.v-dropdown.is-open').forEach(function (x) { x.classList.remove('is-open'); });
  });

  /* Sidebar mobile */
  var sidebar = document.getElementById('vResultsSidebar');
  var toggleBtn = document.querySelector('[data-filter-toggle]');
  var closeBtn = document.querySelector('[data-filter-close]');
  if (toggleBtn && sidebar) toggleBtn.addEventListener('click', function () { sidebar.classList.add('is-open'); });
  if (closeBtn && sidebar) closeBtn.addEventListener('click', function () { sidebar.classList.remove('is-open'); });

  function forms() { return Array.prototype.slice.call(document.querySelectorAll('.v-filter-raw form')); }

  /* Collapse ala Bootstrap (tanpa Bootstrap) */
  document.querySelectorAll('.v-filter-raw [data-toggle="collapse"]').forEach(function (t) {
    t.addEventListener('click', function (e) {
      e.preventDefault(); e.stopPropagation();
      var sel = t.getAttribute('data-target') || t.getAttribute('href');
      if (!sel) return;
      var target = document.querySelector(sel);
      if (!target) return;
      var open = target.classList.toggle('show');
      t.classList.toggle('is-closed', !open);
    });
  });

  /* Pill sync untuk checkbox/radio */
  function syncPills() {
    document.querySelectorAll('.v-filter-raw input[type=checkbox],.v-filter-raw input[type=radio]').forEach(function (i) {
      var l = i.id ? document.querySelector('label[for="' + i.id + '"]') : i.closest('label');
      if (l) l.classList.toggle('is-on', i.checked);
    });
  }

  /* Slider tahun VENOM (menggantikan ion-slider) */
  function buildYearControl(form) {
    if (form.querySelector('.v-yc')) return;
    var s = form.querySelector('.input-slider');
    var f = form.querySelector('.js-input-from');
    var t = form.querySelector('.js-input-to');
    if (!s || !f || !t) return;
    var min = parseInt(s.getAttribute('data-min'), 10); if (isNaN(min)) return;
    var max = parseInt(s.getAttribute('data-max'), 10); if (isNaN(max) || max <= min) return;

    var div = document.createElement('div');
    div.className = 'v-yc';
    div.innerHTML =
      '<div class="v-yc-pills">' +
        '<input type="text" class="v-yc-input v-yc-from" inputmode="numeric" aria-label="Tahun dari">' +
        '<span class="v-yc-to">sampai</span>' +
        '<input type="text" class="v-yc-input v-yc-toin" inputmode="numeric" aria-label="Tahun sampai">' +
      '</div>' +
      '<div class="v-yc-range"><div class="v-yc-track"></div><div class="v-yc-fill"></div>' +
        '<input type="range" class="v-yc-rmin" min="' + min + '" max="' + max + '" step="1">' +
        '<input type="range" class="v-yc-rmax" min="' + min + '" max="' + max + '" step="1"></div>' +
      '<div class="v-yc-scale"><span>' + min + '</span><span>' + max + '</span></div>';
    s.parentNode.insertBefore(div, s);

    var pF = div.querySelector('.v-yc-from'), pT = div.querySelector('.v-yc-toin');
    var rF = div.querySelector('.v-yc-rmin'), rT = div.querySelector('.v-yc-rmax');
    var fill = div.querySelector('.v-yc-fill');

    function clamp(v) { v = parseInt(v, 10); if (isNaN(v)) v = min; return Math.min(max, Math.max(min, v)); }
    function render() {
      var a = clamp(rF.value), b = clamp(rT.value);
      if (a > b) { var tmp = a; a = b; b = tmp; }
      pF.value = a; pT.value = b;
      f.value = a; t.value = b;
      s.value = a + ';' + b;               /* ← WAJIB: isi input bernama "years" */
      var pa = (a - min) / (max - min) * 100, pb = (b - min) / (max - min) * 100;
      fill.style.left = pa + '%'; fill.style.width = (pb - pa) + '%';
    }
    rF.addEventListener('input', function () { if (+rF.value > +rT.value) rF.value = rT.value; render(); });
    rT.addEventListener('input', function () { if (+rT.value < +rF.value) rF.value = rT.value; render(); });
    pF.addEventListener('change', function () { rF.value = clamp(pF.value); render(); });
    pT.addEventListener('change', function () { rT.value = clamp(pT.value); render(); });
    var ia = parseInt(f.value, 10), ib = parseInt(t.value, 10);
    rF.value = (!isNaN(ia) && ia >= min && ia <= max) ? ia : min;
    rT.value = (!isNaN(ib) && ib >= min && ib <= max) ? ib : max;
    render();
  }
  function ensureYear() { forms().forEach(buildYearControl); }

  /* ═══ SERIALIZER → param `filter` JSON (persis seperti core SLiMS) ═══ */
  function serializeForm(form) {
    var out = {};
    form.querySelectorAll('input[name],select[name]').forEach(function (el) {
      var n = el.name; if (!n) return;
      if (el.type === 'checkbox' || el.type === 'radio') { if (el.checked) out[n] = el.value; }
      else if (el.value !== '') { out[n] = el.value; }
    });
    return out;
  }
  function applyFilter(form) {
    if (!form) return;
    var data = serializeForm(form);
    var p = new URLSearchParams(location.search);
    p.delete('filter'); p.delete('csrf_token');
    p.set('search', 'Search');
    p.set('view', VIEW);
    if (Object.keys(data).length) p.set('filter', JSON.stringify(data));
    location.search = p.toString();
  }

  syncPills(); ensureYear();

  document.addEventListener('change', function (e) {
    if (e.target.closest && e.target.closest('.v-filter-raw')) syncPills();
  }, true);
  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (f && f.closest && f.closest('.v-filter-raw')) { e.preventDefault(); applyFilter(f); }
  }, true);
  document.addEventListener('click', function (e) {
    var ap = e.target.closest('[data-venom-apply-filter]');
    if (ap) { e.preventDefault(); ensureYear(); applyFilter(forms()[0]); return; }
    var rs = e.target.closest('[data-venom-reset-filter]');
    if (rs) { e.preventDefault(); location.href = 'index.php?keywords=' + encodeURIComponent(KW) + '&search=Search&view=' + VIEW; }
  }, true);
})();
</script>