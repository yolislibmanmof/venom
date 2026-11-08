<?php defined('INDEX_AUTH') OR die('Direct access not allowed!');

/* Menu teks utama (4 saja agar rapi) */
$vmenu = [
  ['index.php',                          venom_t('home', 'Beranda'),  'home'],
  ['index.php?keywords=&search=Search',  venom_t('index', 'Katalog'), 'grid'],
  ['index.php?p=news',                   venom_t('news', 'Berita'),   'news'],
  ['index.php?p=libinfo',                venom_t('libinfo', 'Profil'),'layers'],
];
/* Menu ikon (tooltip) — adopsi pola icon_menus pustaka_ceria */
$vicon = [
  ['index.php?p=visitor',   venom_t('visitor', 'Statistik'),    'chart'],
  ['index.php?p=help',      venom_t('help', 'Bantuan'),         'shield'],
  ['index.php?p=librarian', venom_t('librarian', 'Pustakawan'), 'users'],
];

$vcur_lang = isset($_GET['select_lang']) ? (string) $_GET['select_lang']
           : (isset($_COOKIE['select_lang']) ? (string) $_COOKIE['select_lang']
           : (string) Venom_Core::conf('default_lang', 'id_ID'));
if (!function_exists('venom_lang_url')) {
    function venom_lang_url($code) {
        $q = [];
        foreach ($_GET as $k => $v) { if ($k !== 'select_lang' && is_string($v)) $q[$k] = $v; }
        $q['select_lang'] = $code;
        return 'index.php?' . http_build_query($q);
    }
}
$vlogo = venom_logo_url();
?>
<header class="v-nav" id="vNav">
  <div class="v-wrap v-nav-inner">

    <a class="v-brand" href="index.php" aria-label="Kembali ke beranda">
      <span class="v-brand-mark">
        <?php if ($vlogo !== ''): ?>
          <img src="<?= venom_e($vlogo) ?>" alt="" style="width:100%;height:100%;object-fit:contain;border-radius:inherit">
        <?php else: ?>
          <?= venom_icon('book', 22) ?>
        <?php endif; ?>
      </span>
      <span class="v-brand-text">
        <strong><?= venom_e(venom_library_name()) ?></strong>
        <small><?= venom_e(venom_cfg('brand.tagline', 'OPAC')) ?></small>
      </span>
    </a>

    <nav class="v-menu" id="vMenu" aria-label="Navigasi utama">
      <?php foreach ($vmenu as $m): ?>
        <a class="v-menu-link" href="<?= venom_u($m[0]) ?>"><?= venom_icon($m[2], 16) ?><span><?= venom_e($m[1]) ?></span></a>
      <?php endforeach; ?>
      <?php foreach ($vicon as $m): ?>
        <a class="v-menu-link v-only-icon" href="<?= venom_u($m[0]) ?>" title="<?= venom_e($m[1]) ?>" aria-label="<?= venom_e($m[1]) ?>"><?= venom_icon($m[2], 16) ?><span><?= venom_e($m[1]) ?></span></a>
      <?php endforeach; ?>
      <a class="v-btn v-btn-primary v-btn-sm v-menu-cta" href="index.php?p=member">
        <?= venom_icon('user', 16) ?><span><?= venom_e(venom_t('member', 'Area Anggota')) ?></span>
      </a>
      <span class="v-lang v-lang-mobile" role="group" aria-label="Pilih bahasa">
        <a href="<?= venom_e(venom_lang_url('id_ID')) ?>" class="<?= strpos($vcur_lang, 'id') === 0 ? 'on' : '' ?>">ID</a>
        <a href="<?= venom_e(venom_lang_url('en_US')) ?>" class="<?= strpos($vcur_lang, 'en') === 0 ? 'on' : '' ?>">EN</a>
      </span>
    </nav>

    <div class="v-navtools">
      <span class="v-lang" role="group" aria-label="Pilih bahasa">
        <a href="<?= venom_e(venom_lang_url('id_ID')) ?>" class="<?= strpos($vcur_lang, 'id') === 0 ? 'on' : '' ?>" title="Bahasa Indonesia">ID</a>
        <a href="<?= venom_e(venom_lang_url('en_US')) ?>" class="<?= strpos($vcur_lang, 'en') === 0 ? 'on' : '' ?>" title="English">EN</a>
      </span>
      <button type="button" class="v-icon-btn" data-venom="mode" aria-label="Ganti mode terang/gelap" title="Mode">
        <span class="v-ico-dark"><?= venom_icon('moon', 18) ?></span>
        <span class="v-ico-light"><?= venom_icon('sun', 18) ?></span>
      </button>
      <button type="button" class="v-icon-btn" data-venom="fx-toggle" aria-label="Efek visual & palet warna" title="Efek & Palet">
        <?= venom_icon('palette', 18) ?>
      </button>
      <button type="button" class="v-icon-btn" data-modal-open="vnxAdvancedModal" aria-label="Pencarian lanjutan" title="Cari Lanjutan">
        <?= venom_icon('filter', 18) ?>
      </button>
      <a class="v-btn v-btn-primary v-btn-sm v-nav-cta" href="index.php?p=member">
        <?= venom_icon('user', 16) ?><span><?= venom_e(venom_t('member', 'Masuk')) ?></span>
      </a>
      <button type="button" class="v-icon-btn v-burger" data-venom="menu" aria-label="Buka menu" aria-expanded="false">
        <?= venom_icon('menu', 20) ?>
      </button>
    </div>

  </div>
</header>