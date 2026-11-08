<?php defined('INDEX_AUTH') OR die('Direct access not allowed!');
$vmenu = [
    ['index.php',              venom_t('home', 'Beranda'),        'book'],
    ['index.php?p=news',       venom_t('news', 'Berita'),         'news'],
    ['index.php?p=libinfo',    venom_t('libinfo', 'Profil'),      'layers'],
    ['index.php?p=visitor',    venom_t('visitor', 'Statistik'),   'chart'],
    ['index.php?p=help',       venom_t('help', 'Bantuan'),        'shield'],
];
$vq = isset($_GET['keywords']) ? Venom_Sec::text($_GET['keywords'], 120) : '';
?>
<header class="v-nav" id="vNav">
  <div class="v-wrap v-nav-inner">

    <a class="v-brand" href="index.php">
      <span class="v-brand-mark"><?= venom_icon('book', 22) ?></span>
      <span class="v-brand-text">
        <strong><?= venom_e(venom_library_name()) ?></strong>
        <small><?= venom_e(venom_cfg('brand.tagline', 'OPAC')) ?></small>
      </span>
    </a>

    <nav class="v-menu" id="vMenu" aria-label="Navigasi utama">
      <?php foreach ($vmenu as $m): ?>
        <a class="v-menu-link" href="<?= venom_u($m[0]) ?>"><?= venom_icon($m[2], 16) ?><span><?= venom_e($m[1]) ?></span></a>
      <?php endforeach; ?>
      <a class="v-btn v-btn-primary v-btn-sm v-menu-cta" href="index.php?p=member">
        <?= venom_icon('user', 16) ?><span><?= venom_e(venom_t('member', 'Area Anggota')) ?></span>
      </a>
    </nav>

    <form class="v-navsearch" action="index.php" method="get" role="search">
      <span class="v-navsearch-icon"><?= venom_icon('search', 18) ?></span>
      <input type="search" name="keywords" value="<?= venom_e($vq) ?>" maxlength="120"
             placeholder="<?= venom_e(venom_t('search')) ?>" aria-label="Kata kunci pencarian">
      <button type="submit" name="search" value="Search" aria-label="Cari"><?= venom_icon('right', 18) ?></button>
    </form>

    <div class="v-navtools">
      <button type="button" class="v-icon-btn" data-venom="mode" aria-label="Ganti mode terang/gelap" title="Mode">
        <span class="v-ico-dark"><?= venom_icon('moon', 18) ?></span>
        <span class="v-ico-light"><?= venom_icon('sun', 18) ?></span>
      </button>
      <?php if (venom_on('palette_switcher')): ?>
      <button type="button" class="v-icon-btn" data-venom="palette" aria-label="Ganti palet warna" title="Palet">
        <?= venom_icon('palette', 18) ?>
      </button>
      <?php endif; ?>
      <a class="v-btn v-btn-primary v-btn-sm v-nav-cta" href="index.php?p=member">
        <?= venom_icon('user', 16) ?><span><?= venom_e(venom_t('member', 'Masuk')) ?></span>
      </a>
      <button type="button" class="v-icon-btn v-burger" data-venom="menu" aria-label="Buka menu" aria-expanded="false">
        <?= venom_icon('menu', 20) ?>
      </button>
    </div>

  </div>
</header>