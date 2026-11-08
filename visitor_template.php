<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

// Cek apakah ini request AJAX untuk update statistik
if (isset($_GET['ajax']) && $_GET['ajax'] === 'stats') {
    header('Content-Type: application/json');
    echo json_encode(venom_stats());
    exit;
}

$stats = venom_stats();
?>

<div class="v-wrap v-sec v-page-transition">
    <div style="text-align: center; margin-bottom: 48px;">
        <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 12px 0;">
            Statistik Perpustakaan
        </h1>
        <p style="color: var(--v-mut); font-size: 1.1rem; margin: 0; max-width: 600px; margin: 0 auto;">
            Data real-time koleksi dan kunjungan perpustakaan kami
        </p>
    </div>

    <!-- Main Stats Grid -->
    <div class="v-grid v-grid-stats" style="margin-bottom: 48px;">
        <div class="v-stat">
            <div class="v-stat-icon"><?= venom_icon('book', 24) ?></div>
            <strong class="v-count" data-to="<?= $stats['titles'] ?>"><?= venom_num($stats['titles']) ?></strong>
            <span class="v-stat-label">Judul Koleksi</span>
        </div>
        <div class="v-stat" data-tone="a">
            <div class="v-stat-icon"><?= venom_icon('layers', 24) ?></div>
            <strong class="v-count" data-to="<?= $stats['items'] ?>"><?= venom_num($stats['items']) ?></strong>
            <span class="v-stat-label">Total Eksemplar</span>
        </div>
        <div class="v-stat">
            <div class="v-stat-icon"><?= venom_icon('users', 24) ?></div>
            <strong class="v-count" data-to="<?= $stats['members'] ?>"><?= venom_num($stats['members']) ?></strong>
            <span class="v-stat-label">Anggota Terdaftar</span>
        </div>
        <div class="v-stat" data-tone="a">
            <div class="v-stat-icon"><?= venom_icon('chart', 24) ?></div>
            <strong class="v-count" data-to="<?= $stats['visits'] ?>"><?= venom_num($stats['visits']) ?></strong>
            <span class="v-stat-label">Kunjungan Hari Ini</span>
        </div>
        <div class="v-stat">
            <div class="v-stat-icon"><?= venom_icon('clock', 24) ?></div>
            <strong class="v-count" data-to="<?= $stats['loans'] ?>"><?= venom_num($stats['loans']) ?></strong>
            <span class="v-stat-label">Sedang Dipinjam</span>
        </div>
    </div>

    <!-- Info Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
        <div class="v-card v-pad">
            <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                <?= venom_icon('chart', 20) ?> Tren Kunjungan
            </h3>
            <p style="color: var(--v-mut); margin: 0; line-height: 1.7;">
                Statistik kunjungan diperbarui secara real-time. Data mencakup pengunjung fisik dan akses digital melalui OPAC.
            </p>
            <div style="margin-top: 20px; padding: 16px; background: var(--v-bg2); border-radius: 12px; text-align: center;">
                <div style="font-size: 0.85rem; color: var(--v-mut); margin-bottom: 4px;">Rata-rata per Hari</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--v-p);">
                    <?= venom_num(max(1, round($stats['visits'] / max(1, date('d'))))) ?>
                </div>
            </div>
        </div>

        <div class="v-card v-pad">
            <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                <?= venom_icon('book', 20) ?> Koleksi Populer
            </h3>
            <p style="color: var(--v-mut); margin: 0; line-height: 1.7;">
                Koleksi yang paling sering dipinjam dan diakses oleh anggota perpustakaan kami.
            </p>
            <div style="margin-top: 20px;">
                <a href="index.php?p=browse" class="v-btn v-btn-ghost" style="width: 100%; justify-content: center; border: 1px solid var(--v-line);">
                    Jelajahi Koleksi <?= venom_icon('right', 16) ?>
                </a>
            </div>
        </div>

        <div class="v-card v-pad">
            <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                <?= venom_icon('users', 20) ?> Keanggotaan
            </h3>
            <p style="color: var(--v-mut); margin: 0; line-height: 1.7;">
                Bergabunglah dengan ribuan anggota kami untuk mengakses koleksi perpustakaan secara digital.
            </p>
            <div style="margin-top: 20px;">
                <a href="index.php?p=member" class="v-btn v-btn-primary" style="width: 100%; justify-content: center;">
                    Daftar Anggota <?= venom_icon('right', 16) ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Counter animation untuk statistik
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.v-count');
    
    const animateCounter = (el) => {
        const target = parseInt(el.getAttribute('data-to')) || 0;
        const duration = 1500;
        const startTime = performance.now();
        
        const update = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(easeOut * target);
            
            el.textContent = current.toLocaleString('id-ID');
            
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                el.textContent = target.toLocaleString('id-ID');
            }
        };
        
        requestAnimationFrame(update);
    };
    
    // Intersection Observer untuk animasi saat scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => observer.observe(counter));
});
</script>