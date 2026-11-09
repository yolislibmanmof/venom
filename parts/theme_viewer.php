<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');

$show = (int) venom_cfg('features.palette_switcher', 1) === 1;
if (!$show) return;

$options = function_exists('venomThemeViewerOptions') ? venomThemeViewerOptions($GLOBALS['sysconf'] ?? []) : [];
$tinfo_values = [];
foreach ($options as $o) {
    $tinfo_values[$o['dbfield']] = $o['value'];
}

$config = [
    'enabled' => true,
    'tinfoValues' => $tinfo_values,
    'tinfoOptions' => $options,
    'palettes' => (array) venom_cfg('palettes', []),
    'adminThemeEndpoint' => (defined('MWB') ? MWB : 'admin/modules/') . 'system/theme.php',
    'adminThemeDir' => 'venom',
    'labels' => [
        'save' => 'Simpan perubahan',
        'saving' => 'Menyimpan...',
        'saved' => 'Tersimpan. Muat ulang OPAC untuk menerapkan.',
        'error' => 'Gagal menyimpan. Periksa sesi admin.',
        'login' => 'Masuk admin untuk menyimpan.',
        'checking' => 'Memeriksa akses...',
    ],
];
?>

<template id="venom-theme-viewer-config"><?= json_encode($config, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?></template>

<link rel="stylesheet" href="<?= venom_e(venom_asset('css/venom-theme-viewer.css')) ?>">

<button type="button" class="vnx-theme-fab" id="venomThemeFab" aria-label="Kustomisasi Tema" title="Kustomisasi Tema">
    <?= venom_icon('palette', 22) ?>
</button>

<div class="vnx-theme-drawer" id="venomThemeDrawer" hidden aria-hidden="true">
    <div class="vnx-theme-drawer-backdrop" data-venom-theme-close></div>
    <div class="vnx-theme-drawer-panel">
        <div class="vnx-theme-head">
            <div>
                <h3 class="vnx-theme-title">Kustomisasi Tema</h3>
                <small class="vnx-theme-sub">Pratinjau langsung • <a href="index.php?p=login" id="venomThemeAdminLink">masuk admin</a> untuk menyimpan</small>
            </div>
            <button type="button" class="vnx-theme-close" data-venom-theme-close><?= venom_icon('x', 20) ?></button>
        </div>

        <div class="vnx-theme-search">
            <input type="search" id="venomThemeSearch" placeholder="Cari pengaturan..." autocomplete="off">
        </div>

        <div class="vnx-theme-body">
            <?php
            $groups = [];
            foreach ($options as $o) {
                $g = $o['group'] ?? 'other';
                if (!isset($groups[$g])) $groups[$g] = ['label' => $o['groupLabel'] ?? $g, 'items' => []];
                $groups[$g]['items'][] = $o;
            }
            foreach ($groups as $gkey => $g): ?>
            <details class="vnx-theme-group"<?= $gkey === 'general' ? ' open' : '' ?>>
                <summary><?= venom_e($g['label']) ?></summary>
                <div class="vnx-theme-group-body">
                    <?php foreach ($g['items'] as $o):
                        $fid = 'venom-tinfo-' . preg_replace('/[^a-z0-9]+/i', '-', $o['dbfield']); ?>
                        <div class="vnx-theme-field" data-venom-item="<?= venom_e($o['label']) ?>">
                            <label for="<?= $fid ?>"><?= venom_e($o['label']) ?></label>
                            <?php if ($o['type'] === 'dropdown' && !empty($o['choices'])): ?>
                                <select id="<?= $fid ?>" data-venom-field="<?= venom_e($o['dbfield']) ?>">
                                    <?php foreach ($o['choices'] as $c): $cv = is_array($c) ? $c[0] : $c; $cl = is_array($c) ? $c[1] : $c; ?>
                                        <option value="<?= venom_e($cv) ?>"<?= (string)$cv === (string)$o['value'] ? ' selected' : '' ?>><?= venom_e($cl) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($o['type'] === 'longtext'): ?>
                                <textarea id="<?= $fid ?>" data-venom-field="<?= venom_e($o['dbfield']) ?>" rows="3"<?= $o['max'] ? ' maxlength="'.$o['max'].'"' : '' ?>><?= venom_e($o['value']) ?></textarea>
                            <?php else: ?>
                                <input id="<?= $fid ?>" type="text" data-venom-field="<?= venom_e($o['dbfield']) ?>" value="<?= venom_e($o['value']) ?>"<?= $o['max'] ? ' maxlength="'.$o['max'].'"' : '' ?>>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </details>
            <?php endforeach; ?>
        </div>

        <div class="vnx-theme-foot">
            <button type="button" class="vnx-btn vnx-btn-ghost" id="venomThemeReset"><?= venom_icon('left', 14) ?> Reset</button>
            <div style="flex:1"></div>
            <span class="vnx-theme-status" id="venomThemeStatus"></span>
            <button type="button" class="vnx-btn vnx-btn-primary" id="venomThemeSave" hidden><?= venom_icon('check', 14) ?> <span>Simpan perubahan</span></button>
        </div>
    </div>
</div>

<script src="<?= venom_e(venom_asset('js/venom-theme-viewer.js')) ?>" defer></script>