<?php
/**
 * VENOM · FX Control Panel
 * Floating sidebar untuk ganti efek partikel + palet warna
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

$current = venom_cfg('particle.default', 'stars');
$palettes = [
  'venom'     => ['name' => 'Venom',     'colors' => ['#7c3aed', '#22d3ee']],
  'rose'      => ['name' => 'Rose',      'colors' => ['#f43f5e', '#fbbf24']],
  'emerald'   => ['name' => 'Emerald',   'colors' => ['#10b981', '#3b82f6']],
  'sunset'    => ['name' => 'Sunset',    'colors' => ['#f97316', '#ec4899']],
  'midnight'  => ['name' => 'Midnight',  'colors' => ['#6366f1', '#a855f7']],
  'cyberpunk' => ['name' => 'Cyberpunk', 'colors' => ['#ec4899', '#06b6d4']],
  'forest'    => ['name' => 'Forest',    'colors' => ['#166534', '#65a30d']],
];

$effects = [
  ['key' => 'stars',     'label' => 'Bintang', 'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15 9 22 9 17 14 19 22 12 18 5 22 7 14 2 9 9 9"/></svg>'],
  ['key' => 'snow',      'label' => 'Salju',   'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="2" x2="12" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/><line x1="19.07" y1="4.93" x2="4.93" y2="19.07"/></svg>'],
  ['key' => 'bubbles',   'label' => 'Gelembung','icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8"/><circle cx="8" cy="8" r="2"/></svg>'],
  ['key' => 'rain',      'label' => 'Hujan',   'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 13v8M8 13v8M12 15v8M20 16.58A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25"/></svg>'],
  ['key' => 'fireflies', 'label' => 'Kunang',  'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="4"/></svg>'],
  ['key' => 'confetti',  'label' => 'Confetti','icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><rect x="4" y="4" width="4" height="4"/><rect x="12" y="8" width="4" height="4"/><rect x="8" y="16" width="4" height="4"/><rect x="16" y="14" width="4" height="4"/></svg>'],
  ['key' => 'none',      'label' => 'Nonaktif','icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="1" y1="1" x2="23" y2="23"/><path d="M21 21H3"/></svg>'],
];
?>

<!-- Floating FX Toggle Button -->
<div class="vnx-fx-panel">
  <button type="button" class="vnx-fx-btn" data-venom="fx-toggle" aria-label="Buka panel efek" title="Efek Visual">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="3"/>
      <path d="M12 1v6m0 6v6M4.22 4.22l4.24 4.24m7.07 7.07l4.24 4.24M1 12h6m6 0h6M4.22 19.78l4.24-4.24m7.07-7.07l4.24-4.24"/>
    </svg>
  </button>
</div>

<!-- FX Drawer -->
<div class="vnx-fx-drawer" id="vFxD">
  <div class="vnx-fx-drawer-head">
    <h3 class="vnx-fx-drawer-title">Efek Visual</h3>
    <button type="button" class="vnx-fx-btn" data-venom="fx-toggle" aria-label="Tutup" style="width:32px;height:32px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>

  <div class="vnx-fx-section">
    <div class="vnx-fx-label">Efek Partikel</div>
    <div class="vnx-fx-options">
      <?php foreach ($effects as $e): ?>
        <button type="button" class="vnx-fx-opt<?= $e['key'] === $current ? ' is-active' : '' ?>" data-particle="<?= $e['key'] ?>">
          <?= $e['icon'] ?>
          <span><?= $e['label'] ?></span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="vnx-fx-section">
    <div class="vnx-fx-label">Palet Warna</div>
    <div class="vnx-fx-palettes">
      <?php foreach ($palettes as $key => $p): ?>
        <button type="button" class="vnx-fx-swatch<?= $key === 'venom' ? ' is-active' : '' ?>" data-palette-set="<?= $key ?>" data-palette="<?= $key ?>">
          <span class="vnx-fx-swatch-colors">
            <span style="background:<?= $p['colors'][0] ?>"></span>
            <span style="background:<?= $p['colors'][1] ?>"></span>
          </span>
          <span class="vnx-fx-swatch-name"><?= $p['name'] ?></span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>
</div>