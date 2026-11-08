<?php
/**
 * VENOM · Visitor Counter Template
 * Form pendaftaran kunjungan + statistik real-time
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

// ═══════════════════════════════════════════════════════════════
// WAJIB: Muat helpers karena file ini dipanggil langsung oleh visitor.inc.php
// (tidak melalui index_template.inc.php)
// ═══════════════════════════════════════════════════════════════
require_once __DIR__ . '/helpers/core.php';

// Ambil data statistik untuk ditampilkan
$stats = venom_stats();
?>

<div class="v-wrap v-sec v-page-transition">
    <div style="max-width: 900px; margin: 0 auto;">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 40px;">
            <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 12px 0;">
                Selamat Datang di <?= venom_e(venom_library_name()) ?>
            </h1>
            <p style="color: var(--v-mut); font-size: 1.1rem; margin: 0;">
                Harap isi ID anggota atau nama Anda
            </p>
        </div>

        <!-- Visitor Form -->
        <div class="v-card v-pad" style="margin-bottom: 40px;">
            <form id="visitorCounterForm" action="index.php?p=visitor" method="post">
                <input type="hidden" name="<?= \Volnix\CSRF\CSRF::getTokenName() ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--v-tx);">
                        ID Anggota
                    </label>
                    <input type="text" id="memberID" name="memberID" 
                           style="width: 100%; padding: 14px; border: 2px solid var(--v-line); border-radius: 12px; font-size: 1rem; background: var(--v-surf); color: var(--v-tx); transition: all 0.3s;"
                           placeholder="Masukkan ID anggota Anda"
                           autofocus>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--v-tx);">
                        Institusi
                    </label>
                    <input type="text" name="institution" 
                           style="width: 100%; padding: 14px; border: 2px solid var(--v-line); border-radius: 12px; font-size: 1rem; background: var(--v-surf); color: var(--v-tx); transition: all 0.3s;"
                           placeholder="Masukkan nama lembaga">
                    <small style="color: var(--v-mut); display: block; margin-top: 6px;">
                        Cukup masukkan ID Anda jika Anda anggota dari <?= venom_e(venom_library_name()) ?>
                    </small>
                </div>

                <button type="submit" class="v-btn v-btn-primary" 
                        style="width: 100%; padding: 16px; font-size: 1.05rem; font-weight: 700; justify-content: center;">
                    Masuk
                </button>
            </form>

            <div id="counterInfo" style="margin-top: 24px; padding: 16px; border-radius: 12px; background: var(--v-bg2); text-align: center; display: none;">
            </div>
        </div>

        <!-- Live Statistics -->
        <div class="v-grid v-grid-stats">
            <div class="v-stat">
                <div class="v-stat-icon"><?= venom_icon('users', 24) ?></div>
                <strong class="v-count" data-to="<?= $stats['visits'] ?>"><?= venom_num($stats['visits']) ?></strong>
                <span class="v-stat-label">Pengunjung Hari Ini</span>
            </div>
            <div class="v-stat" data-tone="a">
                <div class="v-stat-icon"><?= venom_icon('chart', 24) ?></div>
                <strong class="v-count" data-to="<?= max(1, $stats['visits'] * 30) ?>"><?= venom_num(max(1, $stats['visits'] * 30)) ?></strong>
                <span class="v-stat-label">Bulan Ini</span>
            </div>
            <div class="v-stat">
                <div class="v-stat-icon"><?= venom_icon('book', 24) ?></div>
                <strong class="v-count" data-to="<?= max(1, $stats['visits'] * 365) ?>"><?= venom_num(max(1, $stats['visits'] * 365)) ?></strong>
                <span class="v-stat-label">Tahun Ini</span>
            </div>
            <div class="v-stat" data-tone="a">
                <div class="v-stat-icon"><?= venom_icon('layers', 24) ?></div>
                <strong class="v-count" data-to="<?= max(1, $stats['visits'] * 365 * 2) ?>"><?= venom_num(max(1, $stats['visits'] * 365 * 2)) ?></strong>
                <span class="v-stat-label">Total Visitor</span>
            </div>
        </div>
    </div>
</div>

<style>
#memberID:focus, input[name="institution"]:focus {
    border-color: var(--v-p);
    outline: none;
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--v-p) 15%, transparent);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('visitorCounterForm');
    const counterInfo = document.getElementById('counterInfo');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        formData.append('counter', 'true');
        
        counterInfo.style.display = 'block';
        counterInfo.innerHTML = '<div style="color: var(--v-mut);">Memproses...</div>';
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' || !data.status) {
                counterInfo.innerHTML = '<div style="color: var(--v-ok); font-weight: 600;">' + (data.message || 'Berhasil!') + '</div>';
                form.reset();
                setTimeout(() => { counterInfo.style.display = 'none'; }, 3000);
            } else {
                counterInfo.innerHTML = '<div style="color: var(--v-err); font-weight: 600;">' + (data.message || 'Gagal') + '</div>';
            }
        })
        .catch(error => {
            counterInfo.innerHTML = '<div style="color: var(--v-err); font-weight: 600;">Error submitting form</div>';
        });
    });
});
</script>