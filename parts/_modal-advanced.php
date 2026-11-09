<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/../helpers/core.php';
?>
<div class="vnx-modal" id="vnxAdvancedModal" aria-hidden="true">
    <div class="vnx-modal-box">
        <button class="vnx-modal-close" data-close aria-label="Tutup"><?= venom_icon('x', 24) ?></button>
        <h2 class="vnx-modal-title"><span class="v-grad-text">Pencarian Lanjutan</span></h2>
        
        <form action="index.php" method="get" class="v-form v-form-advanced">
            <input type="hidden" name="search" value="Search">
            <div class="v-form-grid">
                <div class="v-field">
                    <label>Judul</label>
                    <input type="text" name="title" placeholder="Kata kunci judul...">
                </div>
                <div class="v-field">
                    <label>Pengarang</label>
                    <input type="text" name="author" placeholder="Nama pengarang...">
                </div>
                <div class="v-field">
                    <label>Subjek / Topik</label>
                    <input type="text" name="subject" placeholder="Topik buku...">
                </div>
                <div class="v-field">
                    <label>ISBN / ISSN</label>
                    <input type="text" name="isbn" placeholder="Nomor ISBN...">
                </div>
                <div class="v-field">
                    <label>Penerbit</label>
                    <input type="text" name="publisher" placeholder="Nama penerbit...">
                </div>
                <div class="v-field">
                    <label>Tahun Terbit</label>
                    <input type="number" name="year" placeholder="Contoh: 2024" min="1900" max="2030">
                </div>
            </div>
            <div class="v-form-actions">
                <button type="reset" class="v-btn v-btn-ghost">Reset</button>
                <button type="submit" class="v-btn v-btn-primary"><?= venom_icon('search', 16) ?> Cari Koleksi</button>
            </div>
        </form>
    </div>
</div>