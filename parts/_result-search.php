<?php
/**
 * VENOM · Search Result Page (FINAL v4 — Accurate Filter Engine + Sticky Toolbar)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/../helpers/core.php';

$engine     = Venom_Core::get('engine', $GLOBALS['engine'] ?? null);
$opac       = Venom_Core::get('opac', $GLOBALS['opac'] ?? null);
$keywords   = Venom_Core::get('keywords', $GLOBALS['keywords'] ?? ($_GET['keywords'] ?? ''));
$pagination = Venom_Core::get('pagination', $GLOBALS['pagination'] ?? '');
$keywords   = is_string($keywords) ? trim($keywords) : '';
$view_mode  = $_POST['view'] ?? $_GET['view'] ?? $_SESSION['LIST_VIEW'] ?? 'grid';
if (!in_array($view_mode, ['simple', 'list', 'grid'], true)) $view_mode = 'grid';

$main_content = venom_core_content();
$num_rows    = (is_object($engine) && method_exists($engine, 'getNumRows')) ? (int) $engine->getNumRows() : 0;
$filter_html = (is_object($engine) && is_object($opac) && method_exists($engine, 'getFilter')) ? (string) $engine->getFilter($opac, true) : '';
$csrf        = (is_object($opac) && method_exists($opac, 'getCsrf')) ? $opac->getCsrf() : Venom_Sec::token('search');
$kw_disp     = (strlen($keywords) > 40) ? substr($keywords, 0, 40) . '…' : $keywords;
$cur_sort    = (string) ($_GET['sortby'] ?? '');

function v_url_merge(array $add) { return $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, $add)); }

$sorts = [
    ['most-relevant', 'Paling relevan'], ['recently-added', 'Terbaru ditambah'],
    ['last-update', 'Pembaruan terakhir'], ['most-loaned', 'Paling banyak dipinjam'],
    ['publish-year-newest', 'Tahun terbit (terbaru)'], ['publish-year-oldest', 'Tahun terbit (terlama)'],
    ['title-asc', 'Judul A-Z'], ['title-desc', 'Judul Z-A'],
];
$views = [
    'simple' => ['menu', 'Simple', 'Baris kompak tanpa cover'],
    'list'   => ['list', 'List', 'Detail dengan cover buku'],
    'grid'   => ['grid', 'Grid', 'Kartu grid modern'],
];
?>
<!-- TOOLBAR (akan dipindah JS menjadi anak langsung <body> agar sticky pasti) -->
<div class="v-toolbar">
    <div class="v-summary-info">
        <span>Ditemukan</span>
        <strong class="v-count-badge"><?= venom_num($num_rows) ?></strong>
        <?php if ($kw_disp !== ''): ?>
            <span>untuk</span><span class="v-keyword-chip" title="<?= venom_e($keywords) ?>"><?= venom_e($kw_disp) ?></span>
        <?php else: ?>
            <span>judul koleksi</span>
        <?php endif; ?>
    </div>
    <div class="v-summary-tools">
        <?php if ($filter_html !== ''): ?>
        <button type="button" class="v-tb-btn" data-modal-open="vnxFilterModal">
            <?= venom_icon('filter', 16) ?> Filter
            <span class="v-filter-count" data-venom-filter-count style="display:none">0</span>
        </button>
        <?php endif; ?>
        <button type="button" class="v-tb-btn" data-modal-open="vnxSortModal"><?= venom_icon('down', 16) ?> Urutkan</button>
        <button type="button" class="v-tb-btn" data-modal-open="vnxViewModal"><?= venom_icon($views[$view_mode][0], 16) ?> <?= $views[$view_mode][1] ?></button>
    </div>
</div>

<section class="v-sec v-search-results">
    <div class="v-wrap">
        <?php if ($num_rows === 0 || trim(strip_tags((string) $main_content)) === ''): ?>
            <div class="v-empty">
                <?= venom_icon('search', 48) ?>
                <h3>Tidak Ada Hasil</h3>
                <p>Periksa ejaan, atau longgarkan filter yang aktif.</p>
                <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
                    <a href="index.php?search=Search" class="v-btn v-btn-primary v-btn-sm">Reset Pencarian</a>
                    <button type="button" class="v-btn v-btn-ghost v-btn-sm" data-modal-open="vnxAdvancedModal"><?= venom_icon('filter', 14) ?> Pencarian Lanjutan</button>
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
    </div>
</section>

<!-- MODAL FILTER -->
<div class="vnx-modal" id="vnxFilterModal" aria-hidden="true">
    <div class="vnx-modal-backdrop" data-close></div>
    <div class="vnx-modal-panel vnx-modal-center v-filter-panel">
        <div class="vnx-modal-head">
            <h3 class="vnx-modal-title"><?= venom_icon('filter', 18) ?> Saring hasil pencarian</h3>
            <button class="vnx-modal-close" data-close><?= venom_icon('x', 20) ?></button>
        </div>
        <div class="vnx-modal-body v-filter-scroll"><div class="v-filter-raw"><?= $filter_html ?></div></div>
        <div class="vnx-modal-foot">
            <button type="button" class="vnx-btn vnx-btn-ghost" data-venom-reset-filter><?= venom_icon('left', 16) ?> Reset</button>
            <button type="button" class="vnx-btn vnx-btn-primary" data-venom-apply-filter><?= venom_icon('check', 16) ?> Terapkan Filter</button>
        </div>
    </div>
</div>

<!-- MODAL SORT -->
<div class="vnx-modal" id="vnxSortModal" aria-hidden="true">
    <div class="vnx-modal-backdrop" data-close></div>
    <div class="vnx-modal-panel vnx-modal-small">
        <div class="vnx-modal-head">
            <h3 class="vnx-modal-title"><?= venom_icon('down', 18) ?> Urutkan hasil</h3>
            <button class="vnx-modal-close" data-close><?= venom_icon('x', 20) ?></button>
        </div>
        <div class="vnx-modal-body" style="padding:10px">
            <?php foreach ($sorts as $s): ?>
                <a href="<?= venom_e(v_url_merge(['sortby' => $s[0]])) ?>" class="v-opt-row<?= $cur_sort === $s[0] ? ' is-active' : '' ?>">
                    <span><?= $s[1] ?></span><?php if ($cur_sort === $s[0]): ?><?= venom_icon('check', 16) ?><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- MODAL VIEW -->
<div class="vnx-modal" id="vnxViewModal" aria-hidden="true">
    <div class="vnx-modal-backdrop" data-close></div>
    <div class="vnx-modal-panel vnx-modal-small">
        <div class="vnx-modal-head">
            <h3 class="vnx-modal-title"><?= venom_icon('grid', 18) ?> Mode tampilan</h3>
            <button class="vnx-modal-close" data-close><?= venom_icon('x', 20) ?></button>
        </div>
        <div class="vnx-modal-body" style="padding:10px">
            <?php foreach ($views as $vk => $vv): ?>
                <a href="<?= venom_e(v_url_merge(['view' => $vk, 'csrf_token' => $csrf])) ?>" class="v-opt-row<?= $view_mode === $vk ? ' is-active' : '' ?>">
                    <span style="display:flex;align-items:center;gap:10px"><?= venom_icon($vv[0], 18) ?><span><b><?= $vv[1] ?></b><small style="display:block;color:var(--v-mut);font-weight:500"><?= $vv[2] ?></small></span></span>
                    <?php if ($view_mode === $vk): ?><?= venom_icon('check', 16) ?><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
/* ═══ VENOM · Filter Engine Guard v6 (slider mandiri, akurasi inti) ═══ */
(function () {
    var KW = <?= json_encode($keywords, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    var VIEW = <?= json_encode($view_mode) ?>;

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

    /* ── Slider tahun mandiri (vanilla, tanpa jQuery) ── */
    function hideYearCluster(form, s) {
        var cluster = s.parentElement;
        var hasWrap = cluster && cluster !== form && cluster.querySelector('.js-input-from');
        if (hasWrap) { cluster.style.display = 'none'; return cluster; }
        [s, form.querySelector('.js-input-from'), form.querySelector('.js-input-to')].forEach(function (i) { if (i) i.style.display = 'none'; });
        form.querySelectorAll('input[type=text]').forEach(function (i) { if (!i.classList.contains('v-keep')) i.style.display = 'none'; });
        Array.prototype.forEach.call(form.childNodes, function (n) {
            if (n.nodeType === 3 && /^\s*to\s*$/i.test(n.textContent)) n.textContent = '';
        });
        return null;
    }

    function buildYearControl(form) {
        if (form.querySelector('.v-yc')) return;
        if (form.querySelector('.irs')) return; /* slider inti hidup → biarkan */
        var s = form.querySelector('.input-slider');
        var f = form.querySelector('.js-input-from');
        var t = form.querySelector('.js-input-to');
        if (!s || !f || !t) return;
        var min = parseInt(s.getAttribute('data-min'), 10); if (isNaN(min)) return;
        var max = parseInt(s.getAttribute('data-max'), 10); if (isNaN(max) || max <= min) return;

        var anchor = hideYearCluster(form, s) || s;
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
              '<input type="range" class="v-yc-rmin" min="' + min + '" max="' + max + '" step="1" aria-label="Geser tahun awal">' +
              '<input type="range" class="v-yc-rmax" min="' + min + '" max="' + max + '" step="1" aria-label="Geser tahun akhir">' +
            '</div>' +
            '<div class="v-yc-scale"><span>' + min + '</span><span>' + max + '</span></div>';
        anchor.parentNode.insertBefore(div, anchor);

        var pF = div.querySelector('.v-yc-from'), pT = div.querySelector('.v-yc-toin');
        var rF = div.querySelector('.v-yc-rmin'), rT = div.querySelector('.v-yc-rmax');
        var fill = div.querySelector('.v-yc-fill');

        function clamp(v) { v = parseInt(v, 10); if (isNaN(v)) v = min; return Math.min(max, Math.max(min, v)); }
        function render() {
            var a = clamp(rF.value), b = clamp(rT.value);
            if (a > b) { var tmp = a; a = b; b = tmp; }
            pF.value = a; pT.value = b;
            f.value = a; t.value = b;               /* ← menulis ke input inti = akurasi */
            var pa = (a - min) / (max - min) * 100, pb = (b - min) / (max - min) * 100;
            fill.style.left = pa + '%'; fill.style.width = (pb - pa) + '%';
            updateBadge();
        }
        rF.addEventListener('input', function () { if (+rF.value > +rT.value) rF.value = rT.value; render(); });
        rT.addEventListener('input', function () { if (+rT.value < +rF.value) rT.value = rF.value; render(); });
        pF.addEventListener('change', function () { rF.value = clamp(pF.value); if (+rF.value > +rT.value) rT.value = rF.value; render(); });
        pT.addEventListener('change', function () { rT.value = clamp(pT.value); if (+rT.value < +rF.value) rF.value = rT.value; render(); });

        var ia = parseInt(f.value, 10), ib = parseInt(t.value, 10);
        rF.value = (!isNaN(ia) && ia >= min && ia <= max) ? ia : min;
        rT.value = (!isNaN(ib) && ib >= min && ib <= max) ? ib : max;
        render();
    }

    function ensureYear() { forms().forEach(buildYearControl); }

    function countActive() {
        var c = document.querySelectorAll('.v-filter-raw input[type=checkbox]:checked,.v-filter-raw input[type=radio]:checked').length;
        var f = document.querySelector('.v-filter-raw .js-input-from');
        var t = document.querySelector('.v-filter-raw .js-input-to');
        var s = document.querySelector('.v-filter-raw .input-slider');
        if (f && t && s && (String(f.value) !== String(s.getAttribute('data-min')) || String(t.value) !== String(s.getAttribute('data-max')))) c++;
        return c;
    }
    function updateBadge() {
        var b = document.querySelector('[data-venom-filter-count]');
        if (!b) return;
        var n = countActive();
        b.textContent = n;
        b.style.display = n > 0 ? 'inline-grid' : 'none';
    }

    /* Toolbar → anak langsung <body> (sticky pasti) */
    var tb = document.querySelector('.v-toolbar');
    var main = document.getElementById('v-main');
    if (tb && main && tb.parentNode !== document.body) {
        document.body.insertBefore(tb, main);
        document.body.classList.add('has-vtoolbar');
    }

    patch(); syncPills(); ensureYear(); updateBadge();

    document.addEventListener('change', function (e) {
        if (e.target.closest && e.target.closest('.v-filter-raw')) { syncPills(); updateBadge(); }
    }, true);

    document.addEventListener('click', function (e) {
        var op = e.target.closest('[data-modal-open="vnxFilterModal"]');
        if (op) {
            setTimeout(function () { patch(); syncPills(); ensureYear(); updateBadge(); }, 80);
            setTimeout(function () { ensureYear(); }, 320);
            return;
        }
        var ap = e.target.closest('[data-venom-apply-filter]');
        if (ap) {
            e.preventDefault(); patch(); ensureYear();
            var f = forms()[0], disabled = [];
            if (f) f.querySelectorAll('input[type=text],input[type=number]').forEach(function (i) {
                if (i.value === '' && !i.classList.contains('js-input-from') && !i.classList.contains('js-input-to')) { i.disabled = true; disabled.push(i); }
            });
            if (typeof window.filter === 'function') {
                window.filter();
            } else if (f) {
                var p = new URLSearchParams(new FormData(f));
                new URLSearchParams(location.search).forEach(function (v, k) { if (!p.has(k)) p.set(k, v); });
                p.set('search', 'Search');
                if (KW && !p.has('keywords')) p.set('keywords', KW);
                location.search = p.toString();
                return;
            }
            disabled.forEach(function (i) { i.disabled = false; });
            var m = document.getElementById('vnxFilterModal'); if (m) m.classList.remove('is-open');
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

<style>
/* Toolbar anak body: lebar penuh terkontrol + sticky pasti */
body>.v-toolbar{width:calc(100% - 40px);max-width:var(--v-max);margin:14px auto 0}
body.has-vtoolbar .v-search-results{padding-top:22px}
.v-toolbar{position:sticky;top:76px;z-index:60;display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;padding:12px 18px;background:var(--v-glass);border:1px solid var(--v-gline);border-radius:var(--v-r);backdrop-filter:blur(16px);box-shadow:var(--v-sh)}
.v-summary-info{display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:.9rem;color:var(--v-mut)}
.v-count-badge{display:inline-grid;place-items:center;min-width:26px;height:26px;padding:0 8px;border-radius:999px;background:linear-gradient(120deg,var(--v-p),var(--v-a));color:#fff;font-size:.82rem;font-weight:800}
.v-keyword-chip{padding:5px 12px;border-radius:999px;background:color-mix(in srgb,var(--v-p) 12%,transparent);color:var(--v-p);font-weight:700;font-size:.84rem}
.v-summary-tools{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.v-tb-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:999px;border:1px solid var(--v-line);background:var(--v-surf2);color:var(--v-tx);font-weight:700;font-size:.84rem;cursor:pointer;transition:.2s}
.v-tb-btn:hover{border-color:var(--v-p);color:var(--v-p);transform:translateY(-1px)}
.v-filter-count{display:none;place-items:center;min-width:20px;height:20px;padding:0 6px;border-radius:999px;background:linear-gradient(120deg,var(--v-p),var(--v-a));color:#fff;font-size:.68rem;font-weight:800}
/* Modal filter: satu scrollbar saja, tombol Apply inti disembunyikan */
.v-filter-panel{overflow:hidden}
.v-filter-scroll{max-height:56vh;overflow-y:auto;overscroll-behavior:contain}
.vnx-modal-foot{display:flex;justify-content:flex-end;gap:10px;padding:14px 22px;border-top:1px solid var(--v-line);background:var(--v-surf)}
.v-filter-raw input[type=submit],.v-filter-raw button[type=submit]{display:none !important}
/* Facet group header dengan titik gradian */
.v-filter-raw h1,.v-filter-raw h2,.v-filter-raw h3,.v-filter-raw h4,.v-filter-raw h5,.v-filter-raw legend{display:flex;align-items:center;gap:8px;font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--v-tx);margin:20px 0 12px}
.v-filter-raw h1::before,.v-filter-raw h2::before,.v-filter-raw h3::before,.v-filter-raw h4::before,.v-filter-raw h5::before,.v-filter-raw legend::before{content:'';width:8px;height:8px;border-radius:50%;background:linear-gradient(120deg,var(--v-p),var(--v-a));box-shadow:0 0 10px color-mix(in srgb,var(--v-p) 60%,transparent);flex:0 0 auto}
.v-filter-raw input[type=text],.v-filter-raw input[type=number],.v-filter-raw select{width:100%;padding:10px 14px;border-radius:12px;border:1px solid var(--v-line);background:var(--v-surf2);color:var(--v-tx);font-size:.9rem;outline:0;margin:4px 0}
.v-filter-raw input:focus,.v-filter-raw select:focus{border-color:var(--v-p);box-shadow:0 0 0 4px color-mix(in srgb,var(--v-p) 12%,transparent)}
/* PILL facet: selectable visual pasti (class .is-on + :has) */
.v-filter-raw label{position:relative;display:inline-flex;align-items:center;gap:7px;margin:0 8px 8px 0;padding:9px 16px;border-radius:999px;background:var(--v-surf2);border:1px solid var(--v-line);font-size:.84rem;font-weight:600;cursor:pointer;transition:.2s;user-select:none}
.v-filter-raw label:hover{border-color:var(--v-p);transform:translateY(-1px)}
.v-filter-raw label.is-on,.v-filter-raw label:has(input:checked){background:linear-gradient(120deg,var(--v-p),var(--v-a));color:#fff;border-color:transparent;box-shadow:0 6px 18px color-mix(in srgb,var(--v-p) 35%,transparent)}
.v-filter-raw label.is-on::after,.v-filter-raw label:has(input:checked)::after{content:'✓';font-weight:800;font-size:.78rem}
.v-filter-raw input[type=checkbox],.v-filter-raw input[type=radio]{position:absolute;opacity:0;width:1px;height:1px}
.v-filter-raw .noUi-connect,.v-filter-raw .irs-bar{background:linear-gradient(120deg,var(--v-p),var(--v-a))}
.v-filter-raw a{color:var(--v-p);font-weight:700}
/* Modal option rows */
.v-opt-row{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:12px 14px;border-radius:12px;color:var(--v-tx);text-decoration:none;font-size:.9rem;font-weight:600;transition:.15s}
.v-opt-row:hover{background:color-mix(in srgb,var(--v-p) 8%,transparent)}
.v-opt-row.is-active{background:color-mix(in srgb,var(--v-p) 12%,transparent);color:var(--v-p)}
/* Komponen kartu (pertahankan dari v3) */
.v-list-books{display:flex;flex-direction:column;gap:16px}
.v-list-books .v-book{flex-direction:row !important;max-width:100%;align-items:stretch}
.v-list-books .v-book-cover{width:130px !important;min-width:130px;aspect-ratio:auto !important;height:190px}
.v-list-books .v-book-body{padding:20px 24px;justify-content:center;flex:1}
.v-author-chip{color:var(--v-p);text-decoration:none;font-weight:600}
.v-book-author .v-year{display:inline-flex;align-items:center;gap:5px;color:var(--v-mut);font-size:.82rem;margin-left:8px}
.v-book-meta-chips{display:flex;flex-wrap:wrap;gap:8px;margin:8px 0}
.v-meta-i{display:inline-flex;flex-direction:column;gap:2px;padding:7px 12px;border-radius:10px;background:var(--v-surf2);border:1px solid var(--v-line);font-size:.72rem;min-width:96px}
.v-meta-i em{font-style:normal;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:var(--v-mut);font-size:.62rem}
.v-meta-i b{color:var(--v-tx);font-weight:600;font-size:.8rem;word-break:break-word}
.v-meta-i.empty{opacity:.55}
.v-notes-x{color:var(--v-mut);font-size:.85rem;line-height:1.6;margin:6px 0}
.v-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
.v-tag{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;font-size:.72rem;font-weight:700;background:color-mix(in srgb,var(--v-p) 10%,transparent);color:var(--v-p)}
.v-tag.plain{background:var(--v-surf2);color:var(--v-mut);border:1px solid var(--v-line)}
.v-book-side{display:flex;flex-direction:column;gap:10px;align-items:stretch;min-width:130px}
.v-avail-box{padding:12px;border-radius:12px;text-align:center;border:1px solid var(--v-line);background:var(--v-surf2)}
.v-avail-box span{display:block;font-size:.64rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--v-mut)}
.v-avail-box b{display:block;font-size:1.4rem;font-weight:800;margin-top:2px}
.v-avail-box.ok b{color:var(--v-ok)} .v-avail-box.no b{color:var(--v-err)}
.v-book-actions{display:flex;flex-direction:column;gap:6px}
.v-act-btn{display:grid;place-items:center;width:40px;height:38px;border-radius:10px;border:1px solid var(--v-line);background:var(--v-glass);color:var(--v-tx);cursor:pointer;transition:.2s;text-decoration:none}
.v-act-btn:hover{border-color:var(--v-p);color:var(--v-p);transform:translateY(-2px)}
.v-book-foot{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:auto}
.v-book-foot .v-book-actions{flex-direction:row}
.v-badge-ok{background:linear-gradient(120deg,var(--v-ok),#34d399);display:inline-flex;align-items:center;gap:5px}
.v-badge-no{background:linear-gradient(120deg,#ef4444,#f97316);display:inline-flex;align-items:center;gap:5px}
.v-quick-actions{display:flex;gap:8px;flex-wrap:wrap;margin:8px 0}
.v-qa{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:999px;border:1px solid var(--v-line);background:var(--v-surf2);color:var(--v-mut);font-size:.74rem;font-weight:700;text-decoration:none;cursor:pointer;transition:.2s}
.v-qa:hover{border-color:var(--v-p);color:var(--v-p)}
.v-simple-books{display:flex;flex-direction:column;gap:8px}
.v-simple-item{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:14px 18px;background:var(--v-glass);border:1px solid var(--v-gline);border-radius:14px;backdrop-filter:blur(10px)}
.v-simple-title{font-weight:700;font-size:.95rem;color:var(--v-tx);text-decoration:none}
.v-simple-title:hover{color:var(--v-p)}
.v-simple-author{color:var(--v-mut);font-size:.8rem;margin-top:2px}
.v-avail-wrap{position:relative;flex:0 0 auto}
.v-avail-badge{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:999px;border:1px solid var(--v-line);background:var(--v-surf2);font-weight:800;cursor:pointer}
.v-avail-badge.ok{color:var(--v-ok)} .v-avail-badge.no{color:var(--v-err)}
.v-avail-popover{display:none;position:absolute;right:0;top:calc(100% + 8px);z-index:60;width:340px;max-width:86vw;padding:14px;border-radius:14px;background:var(--v-surf);border:1px solid var(--v-line);box-shadow:var(--v-sh2)}
.v-avail-wrap:hover .v-avail-popover,.v-avail-wrap:focus-within .v-avail-popover{display:block}
.v-avail-popover table{width:100%;border-collapse:collapse;font-size:.78rem}
.v-avail-popover th{text-align:left;color:var(--v-mut);font-weight:800;text-transform:uppercase;font-size:.64rem;padding:4px 6px}
.v-avail-popover td{padding:6px;border-bottom:1px solid var(--v-line)}
.v-avail-popover .ok-i{color:var(--v-ok)} .v-avail-popover .no-i{color:var(--v-err)}
.v-pagination{margin-top:48px;display:flex;justify-content:center}
.paging{display:flex;gap:6px;flex-wrap:wrap;justify-content:center}
.paging a,.paging span{padding:8px 14px;border-radius:8px;text-decoration:none;font-weight:600;font-size:.9rem;background:var(--v-glass);border:1px solid var(--v-line);color:var(--v-tx)}
.paging a:hover,.paging .current{background:var(--v-p);color:#fff;border-color:var(--v-p)}
/* ── Slider tahun mandiri VENOM (pengganti ionRangeSlider inti) ── */
.v-filter-raw input[type=text]:not(.v-keep){display:none !important}
.v-yc{margin:4px 0 18px}
.v-yc-pills{display:flex;align-items:center;gap:10px}
.v-yc-input{flex:1;text-align:center;padding:10px 12px;border-radius:12px;border:1px solid var(--v-line);background:var(--v-surf2);color:var(--v-tx);font-weight:800;font-size:.95rem;outline:0;transition:.2s}
.v-yc-input:focus{border-color:var(--v-p);box-shadow:0 0 0 4px color-mix(in srgb,var(--v-p) 12%,transparent)}
.v-yc-to{color:var(--v-mut);font-size:.78rem;font-weight:700}
.v-yc-range{position:relative;height:26px;margin-top:14px}
.v-yc-track{position:absolute;left:0;right:0;top:10px;height:6px;border-radius:999px;background:var(--v-bg2)}
.v-yc-fill{position:absolute;top:10px;height:6px;border-radius:999px;background:linear-gradient(90deg,var(--v-p),var(--v-a));box-shadow:0 0 12px color-mix(in srgb,var(--v-p) 45%,transparent)}
.v-yc-range input[type=range]{position:absolute;left:0;top:4px;width:100%;height:18px;margin:0;-webkit-appearance:none;appearance:none;background:transparent;pointer-events:none;outline:0}
.v-yc-range input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;pointer-events:auto;width:18px;height:18px;border-radius:50%;background:var(--v-surf);border:3px solid var(--v-p);box-shadow:0 2px 8px rgba(0,0,0,.35);cursor:grab}
.v-yc-range input[type=range]::-moz-range-thumb{pointer-events:auto;width:13px;height:13px;border-radius:50%;background:var(--v-surf);border:3px solid var(--v-p);cursor:grab}
.v-yc-scale{display:flex;justify-content:space-between;margin-top:6px;font-size:.62rem;color:var(--v-mut);font-variant-numeric:tabular-nums}
@media(max-width:900px){
    body>.v-toolbar{width:calc(100% - 24px)}
    .v-toolbar{top:70px}
    .v-list-books .v-book{flex-direction:column !important}
    .v-list-books .v-book-cover{width:100% !important;height:220px}
    .v-book-side{flex-direction:row;align-items:center;min-width:0}
}
</style>