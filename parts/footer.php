<?php 
/**
 * VENOM · Footer Template
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

// Define all variables at the top
$vsoc = (array) venom_cfg('social', []);
$vhrs = (array) venom_cfg('hours', []);
$vmail = (string) venom_cfg('contact.email', '');
$vaddr = (string) venom_cfg('contact.address', '');
?>
</main>

<footer class="v-footer">
  <div class="v-wrap v-footer-grid">
    <div class="v-footer-brand">
      <div class="v-brand">
        <span class="v-brand-mark"><?= venom_icon('book', 22) ?></span>
        <span class="v-brand-text"><strong><?= venom_e(venom_library_name()) ?></strong>
        <small><?= venom_e(venom_cfg('brand.tagline', 'OPAC')) ?></small></span>
      </div>
      <p>Melayani dengan hati, menghadirkan pengetahuan tanpa batas melalui perpustakaan digital yang modern, cepat, dan aman.</p>
      <div class="v-social">
        <?php foreach ($vsoc as $k => $u): if (!$u) continue; ?>
          <a href="<?= venom_u($u) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= venom_e($k) ?>">
            <?= venom_icon($k, 18) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <nav class="v-footer-col" aria-label="Tautan">
      <h4>Navigasi</h4>
      <a href="index.php">Beranda</a>
      <a href="index.php?p=news">Berita</a>
      <a href="index.php?p=libinfo">Profil Perpustakaan</a>
      <a href="index.php?p=visitor">Statistik Pengunjung</a>
      <a href="index.php?p=member">Area Anggota</a>
    </nav>

    <div class="v-footer-col">
      <h4>Layanan</h4>
      <a href="index.php?p=help">Panduan Pengguna</a>
      <a href="index.php?p=suggest">Saran Koleksi</a>
      <?php if ($vmail !== ''): ?><a href="mailto:<?= venom_e($vmail) ?>"><?= venom_icon('mail', 15) ?><?= venom_e($vmail) ?></a><?php endif; ?>
      <?php if ($vaddr !== ''): ?><p class="v-footer-addr"><?= venom_icon('pin', 15) ?><?= venom_e($vaddr) ?></p><?php endif; ?>
    </div>

    <div class="v-footer-col">
      <h4>Jam Layanan</h4>
      <?php foreach ($vhrs as $h): ?>
        <div class="v-hours"><span><?= venom_e(isset($h[0]) ? $h[0] : '') ?></span>
        <b><?= venom_e(isset($h[1]) ? $h[1] : '') ?></b></div>
      <?php endforeach; ?>
      <div class="v-live"><span class="v-pulse"></span>OPAC aktif 24/7</div>
    </div>
  </div>

  <div class="v-wrap v-footer-bottom">
    <p>&copy; <?= date('Y') ?> <?= venom_e(venom_library_name()) ?>. Seluruh hak cipta dilindungi.</p>
    <p>Powered by <strong>SLiMS 9</strong> · Theme <strong class="v-grad-text">VENOM</strong> v<?= VENOM_VER ?></p>
  </div>
</footer>

<!-- Mobile Bottom Nav -->
<?php if (venom_cfg('features.mobile_bottom_nav', true)): ?>
    <?php venom_part('mobile_bottom_nav'); ?>
<?php endif; ?>

<!-- Floating Actions - KIRI (Scroll & Palette) -->
<?php venom_part('floating_actions'); ?>

<!-- Chat Widget - KANAN (WhatsApp) -->
<?php if (venom_cfg('features.floating_chat', true)): ?>
    <?php venom_part('chat_widget'); ?>
<?php endif; ?>

<!-- Scripts -->
<script src="<?= venom_e(venom_asset('js/venom-app.js')) ?>" defer></script>
<script src="<?= venom_e(venom_asset('js/venom-effects.js')) ?>" defer></script>
<?= venom_core_foot() ?>
</body>
</html>