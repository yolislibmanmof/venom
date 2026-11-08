<?php defined('INDEX_AUTH') OR die('Direct access not allowed!'); ?>
<div class="vnx-modal" id="vnxAdvancedModal">
  <div class="vnx-modal-backdrop" data-close="1"></div>
  <div class="vnx-modal-panel vnx-modal-slide">
    <div class="vnx-modal-head">
      <h2 class="vnx-modal-title">Pencarian Lanjutan</h2>
      <button type="button" class="vnx-modal-close" data-close="1" aria-label="Tutup"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <form class="vnx-modal-body" action="index.php" method="get">
      <input type="hidden" name="search" value="Search">
      <div class="vnx-form-group"><label class="vnx-form-label">Kata Kunci</label><input type="text" name="keywords" class="vnx-form-input" placeholder="Masukkan kata kunci..."></div>
      <div class="vnx-form-group"><label class="vnx-form-label">Judul</label><input type="text" name="title" class="vnx-form-input" placeholder="Cari berdasarkan judul..."></div>
      <div class="vnx-form-group"><label class="vnx-form-label">Penulis</label><input type="text" name="author" class="vnx-form-input" placeholder="Cari berdasarkan penulis..."></div>
      <div class="vnx-form-row">
        <div class="vnx-form-group"><label class="vnx-form-label">Penerbit</label><input type="text" name="publisher" class="vnx-form-input" placeholder="Nama penerbit..."></div>
        <div class="vnx-form-group"><label class="vnx-form-label">Tahun</label><input type="number" name="year" class="vnx-form-input" placeholder="2024" min="1900" max="2100"></div>
      </div>
      <div class="vnx-form-group"><label class="vnx-form-label">Subjek / Topik</label><input type="text" name="subject" class="vnx-form-input" placeholder="Cari berdasarkan subjek..."></div>
      <div class="vnx-form-group"><label class="vnx-form-label">Bahasa</label>
        <select name="language" class="vnx-form-select"><option value="">Semua bahasa</option><option value="ind">Indonesia</option><option value="eng">Inggris</option><option value="ara">Arab</option></select>
      </div>
      <div class="vnx-form-actions">
        <button type="reset" class="vnx-btn vnx-btn-ghost">Reset</button>
        <button type="submit" class="vnx-btn vnx-btn-primary"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>Cari Sekarang</button>
      </div>
    </form>
  </div>
</div>