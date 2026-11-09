<?php
/**
 * VENOM · Theme Viewer (Drawer pratinjau langsung untuk pengunjung)
 * FIX: me-require helper opsi sendiri agar field tidak kosong di OPAC.
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

if (!venom_cfg('features.palette_switcher', true)) return;

/* ✅ PATCH: muat CSS Theme Viewer di OPAC — tanpa ini FAB tidak position:fixed */
echo '<link rel="stylesheet" href="' . venom_e(venom_asset('css/venom-theme-viewer.css')) . '">' . "\n";

/* ✅ FIX KRUSIAL: muat helper opsi di sisi OPAC (sebelumnya tidak pernah dimuat) */
if (!function_exists('venomThemeViewerOptions') && is_file(VENOM_DIR . 'helpers/theme_viewer_tinfo.php')) {
    require_once VENOM_DIR . 'helpers/theme_viewer_tinfo.php';
}

$v_sysconf = (isset($GLOBALS['sysconf']) && is_array($GLOBALS['sysconf'])) ? $GLOBALS['sysconf'] : [];
$v_options = function_exists('venomThemeViewerOptions') ? venomThemeViewerOptions($v_sysconf) : [];

/* Kelompokkan per grup */
$v_groups = [];
foreach ($v_options as $opt) $v_groups[$opt['groupLabel']][$opt['dbfield']] = $opt;

/* Nilai aktif untuk input + JSON */
$v_values = [];
foreach ($v_options as $opt) {
    $db = $opt['dbfield'];
    $v_values[$db] = (string) ($v_sysconf['template'][$db] ?? ($v_sysconf[$db] ?? $opt['value']));
}

/* Palet untuk live preview JS (lengkap 10 palet) */
$v_pal_js = [];
foreach ((array) venom_cfg('palettes', []) as $k => $c) {
    if (!empty($c['p'])) $v_pal_js[$k] = ['p' => $c['p'], 'a' => $c['a'] ?? $c['p']];
}
foreach ([
    'rose' => ['#f43f5e','#fbbf24'], 'sunset' => ['#f97316','#ec4899'],
    'midnight' => ['#6366f1','#a855f7'], 'cyberpunk' => ['#ec4899','#06b6d4'],
    'forest' => ['#166534','#65a30d'], 'crimson' => ['#e11d48','#f59e0b'],
    'ocean' => ['#0284c7','#22d3ee'], 'mono' => ['#475569','#94a3b8'],
] as $k => $c) {
    if (!isset($v_pal_js[$k])) $v_pal_js[$k] = ['p' => $c[0], 'a' => $c[1]];
}

$v_admin_base = (defined('SWB') ? SWB : '');
$v_cfg_json = json_encode([
    'enabled'            => true,
    'adminThemeEndpoint' => $v_admin_base . 'admin/index.php',
    'adminThemeDir'      => VENOM_KEY,
    'palettes'           => $v_pal_js,
    'tinfoValues'        => $v_values,
    'labels' => [
        'checking' => 'Memeriksa sesi admin…',
        'login'    => 'Masuk sebagai admin untuk menyimpan.',
        'saving'   => 'Menyimpan…',
        'saved'    => 'Tersimpan. Memuat ulang…',
        'error'    => 'Gagal menyimpan.',
    ],
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
?>
<button type="button" class="vnx-theme-fab" id="venomThemeFab" aria-label="Kustomisasi tema" title="Kustomisasi Tema">
  <?= venom_icon('palette', 24) ?>
</button>

<div class="vnx-theme-drawer" id="venomThemeDrawer" hidden aria-hidden="true">
  <div class="vnx-theme-drawer-backdrop" data-venom-theme-close></div>
  <div class="vnx-theme-drawer-panel" role="dialog" aria-modal="true" aria-label="Kustomisasi Tema">
    <div class="vnx-theme-head">
      <div>
        <h2 class="vnx-theme-title">Kustomisasi Tema</h2>
        <span class="vnx-theme-sub">Pratinjau langsung • <a href="<?= venom_e($v_admin_base . 'admin/index.php') ?>" target="_blank" rel="noopener">masuk admin</a> untuk menyimpan</span>
      </div>
      <button type="button" class="vnx-theme-close" data-venom-theme-close aria-label="Tutup"><?= venom_icon('x', 18) ?></button>
    </div>

    <div class="vnx-theme-search">
      <input type="search" id="venomThemeSearch" placeholder="Cari pengaturan…" autocomplete="off">
    </div>

    <div class="vnx-theme-body">
      <?php if (!$v_groups): ?>
        <p style="color:var(--v-mut);font-size:.85rem;padding:8px 4px">
          Skema pengaturan tidak ditemukan. Pastikan <code>helpers/tinfo_schema.php</code> tersedia.
        </p>
      <?php endif; ?>
      <?php $gi = 0; foreach ($v_groups as $gLabel => $fields): $gi++; ?>
        <details class="vnx-theme-group"<?= $gi === 1 ? ' open' : '' ?>>
          <summary><span><b class="vnx-theme-gnum"><?= $gi ?></b><?= venom_e($gLabel) ?></span></summary>
          <div class="vnx-theme-group-body">
            <?php foreach ($fields as $opt): $db = $opt['dbfield']; $val = $v_values[$db]; ?>
              <div class="vnx-theme-field" data-venom-item="<?= venom_e(strtolower($db . ' ' . $opt['label'])) ?>">
                <label for="vtf-<?= venom_e($db) ?>"><?= venom_e($opt['label']) ?></label>
                <?php if ($opt['type'] === 'dropdown'): ?>
                  <select id="vtf-<?= venom_e($db) ?>" data-venom-field="<?= venom_e($db) ?>">
                    <?php foreach ($opt['choices'] as $ch): $ck = (string) $ch[0]; $cl = (string) ($ch[1] ?? $ch[0]); ?>
                      <option value="<?= venom_e($ck) ?>"<?= $ck === $val ? ' selected' : '' ?>><?= venom_e($cl) ?></option>
                    <?php endforeach; ?>
                  </select>
                <?php elseif ($opt['type'] === 'longtext'): ?>
                  <textarea id="vtf-<?= venom_e($db) ?>" rows="3" maxlength="<?= (int) $opt['max'] ?>" data-venom-field="<?= venom_e($db) ?>"><?= venom_e($val) ?></textarea>
                <?php else: ?>
                  <input id="vtf-<?= venom_e($db) ?>" type="text" maxlength="<?= (int) $opt['max'] ?>" value="<?= venom_e($val) ?>" data-venom-field="<?= venom_e($db) ?>">
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </details>
      <?php endforeach; ?>
    </div>

    <div class="vnx-theme-foot">
      <span class="vnx-theme-status" id="venomThemeStatus" role="status"></span>
      <span style="flex:1"></span>
      <button type="button" class="vnx-btn" id="venomThemeReset"><?= venom_icon('left', 14) ?> Reset</button>
      <button type="button" class="vnx-btn vnx-btn-primary" id="venomThemeSave"><?= venom_icon('check', 14) ?> Simpan perubahan</button>
    </div>
  </div>
</div>

<script type="application/json" id="venom-theme-viewer-config"><?= $v_cfg_json ?></script>
<script src="<?= venom_e(venom_asset('js/venom-theme-viewer.js')) ?>" defer></script>