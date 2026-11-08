<?php
/**
 * VENOM · Footer Template (Premium Edition)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

$vsoc   = (array) venom_cfg('social', []);
$vhrs   = (array) venom_cfg('hours', []);
$vmail  = (string) venom_cfg('contact.email', '');
$vaddr  = (string) venom_cfg('contact.address', '');
$vphone = (string) venom_cfg('contact.phone', '');
?>
</main>

<footer class="v-footer">
  <div class="v-wrap v-footer-grid">
    <!-- Brand Column -->
    <div class="v-footer-brand">
      <div class="v-brand">
        <span class="v-brand-mark"><?= venom_icon('book', 22) ?></span>
        <span class="v-brand-text">
          <strong><?= venom_e(venom_library_name()) ?></strong>
          <small><?= venom_e(venom_cfg('brand.tagline', 'OPAC')) ?></small>
        </span>
      </div>
      <p><?= venom_e(venom_cfg('brand.description', 'Melayani dengan hati, menghadirkan pengetahuan tanpa batas melalui perpustakaan digital yang modern, cepat, dan aman.')) ?></p>
      
      <?php if (!empty($vsoc)): ?>
      <div class="v-social">
        <?php foreach ($vsoc as $k => $u): if (!$u) continue; ?>
          <a href="<?= venom_u($u) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= venom_e($k) ?>" title="<?= venom_e(ucfirst($k)) ?>">
            <?= venom_icon($k, 18) ?>
          </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Navigation -->
    <nav class="v-footer-col" aria-label="Tautan Utama">
      <h4>Navigasi</h4>
      <a href="index.php"><?= venom_icon('home', 14) ?>Beranda</a>
      <a href="index.php?p=news"><?= venom_icon('news', 14) ?>Berita</a>
      <a href="index.php?p=libinfo"><?= venom_icon('layers', 14) ?>Profil Perpustakaan</a>
      <a href="index.php?p=visitor"><?= venom_icon('chart', 14) ?>Statistik Pengunjung</a>
      <a href="index.php?p=member"><?= venom_icon('user', 14) ?>Area Anggota</a>
    </nav>

    <!-- Services -->
    <div class="v-footer-col">
      <h4>Layanan</h4>
      <a href="index.php?p=help"><?= venom_icon('shield', 14) ?>Panduan Pengguna</a>
      <a href="index.php?p=suggest"><?= venom_icon('message', 14) ?>Saran Koleksi</a>
      <?php if ($vmail !== ''): ?>
        <a href="mailto:<?= venom_e($vmail) ?>"><?= venom_icon('mail', 14) ?><?= venom_e($vmail) ?></a>
      <?php endif; ?>
      <?php if ($vphone !== ''): ?>
        <a href="tel:<?= venom_e($vphone) ?>"><?= venom_icon('phone', 14) ?><?= venom_e($vphone) ?></a>
      <?php endif; ?>
      <?php if ($vaddr !== ''): ?>
        <p class="v-footer-addr"><?= venom_icon('pin', 14) ?><?= venom_e($vaddr) ?></p>
      <?php endif; ?>
    </div>

    <!-- Hours -->
    <div class="v-footer-col">
      <h4>Jam Layanan</h4>
      <?php if (!empty($vhrs)): foreach ($vhrs as $h): ?>
        <div class="v-hours">
          <span><?= venom_e(isset($h[0]) ? $h[0] : '') ?></span>
          <b><?= venom_e(isset($h[1]) ? $h[1] : '') ?></b>
        </div>
      <?php endforeach; else: ?>
        <div class="v-hours"><span>Senin - Jumat</span><b>08:00 - 16:00</b></div>
        <div class="v-hours"><span>Sabtu</span><b>09:00 - 14:00</b></div>
        <div class="v-hours"><span>Minggu</span><b>Tutup</b></div>
      <?php endif; ?>
      <div class="v-live"><span class="v-pulse"></span>OPAC aktif 24/7</div>
    </div>
  </div>

  <!-- Bottom Bar -->
  <div class="v-wrap v-footer-bottom">
    <p>&copy; <?= date('Y') ?> <?= venom_e(venom_library_name()) ?>. Seluruh hak cipta dilindungi.</p>
    <p>Powered by <strong>SLiMS 9</strong> · Theme <strong class="v-grad-text">VENOM</strong> v<?= VENOM_VER ?></p>
  </div>
</footer>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- FLOATING UI COMPONENTS                                         -->
<!-- ═══════════════════════════════════════════════════════════════ -->

<!-- FX Panel: Ganti Partikel & Palet (Batch 3) -->
<?php if (venom_cfg('features.fx_panel', true)): ?>
  <?php venom_part('fx_panel'); ?>
<?php endif; ?>

<!-- Mobile Bottom Nav -->
<?php if (venom_cfg('features.mobile_bottom_nav', true)): ?>
  <?php venom_part('mobile_bottom_nav'); ?>
<?php endif; ?>

<!-- Floating Actions: Scroll & Palette (KIRI) -->
<?php venom_part('floating_actions'); ?>

<!-- Chat Widget: WhatsApp/Telegram (KANAN) -->
<?php if (venom_cfg('features.floating_chat', true)): ?>
  <?php venom_part('chat_widget'); ?>
<?php endif; ?>

<!-- Modals: Advanced Search, Topic Cloud, Share (Batch 2) -->
<?php venom_part('_modal-advanced'); ?>
<?php venom_part('_modal-topic'); ?>
<?php venom_part('_modal-social-media'); ?>

<!-- Toast Notification -->
<div class="v-toast" id="vToast"></div>

<!-- Scripts -->
<script src="<?= venom_e(venom_asset('js/venom-app.js')) ?>" defer></script>
<script src="<?= venom_e(venom_asset('js/venom-fx.js')) ?>" defer></script>
<?= venom_core_foot() ?>
</body>
</html>