<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/../helpers/core.php';

// Cek apakah anggota sudah login
if (!isset($_SESSION['m_id']) || !isset($_SESSION['m_name'])) {
    echo '<div class="v-wrap v-sec">';
    echo '<div class="v-card v-pad" style="max-width: 500px; margin: 40px auto; text-align: center;">';
    echo venom_icon('user', 48);
    echo '<h2 style="margin: 16px 0 8px 0;">Area Anggota</h2>';
    echo '<p style="color: var(--v-mut); margin-bottom: 24px;">Silakan masuk untuk mengakses dashboard anggota Anda.</p>';
    echo '<a href="index.php?p=member" class="v-btn v-btn-primary" style="display: inline-flex;">';
    echo venom_icon('right', 18) . ' Masuk ke Akun';
    echo '</a>';
    echo '</div></div>';
    return;
}

$member_id = $_SESSION['m_id'];
$member_name = $_SESSION['m_name'];
$member_email = $_SESSION['m_email'] ?? '';
$member_phone = $_SESSION['m_phone'] ?? '';

// Ambil data anggota dari database (jika tersedia)
$member_data = [];
if (Venom_Core::db()) {
    $member_data = venom_row("SELECT * FROM member WHERE member_id = '" . Venom_Sec::sql($member_id) . "' LIMIT 1");
}

// Ambil riwayat peminjaman
$loans = [];
if (Venom_Core::db()) {
    $loans = venom_rows("SELECT l.*, b.title 
                          FROM loan l 
                          LEFT JOIN biblio b ON b.biblio_id = l.biblio_id 
                          WHERE l.member_id = '" . Venom_Sec::sql($member_id) . "' 
                          ORDER BY l.loan_date DESC LIMIT 10");
}
?>

<div class="v-wrap v-sec v-page-transition">
    <!-- Header -->
    <div style="margin-bottom: 32px;">
        <h1 style="font-size: 2rem; font-weight: 800; margin: 0 0 8px 0;">
            Selamat Datang, <?= venom_e($member_name) ?>
        </h1>
        <p style="color: var(--v-mut); margin: 0; font-size: 1.05rem;">
            Dashboard anggota perpustakaan Anda
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="v-grid v-grid-stats" style="margin-bottom: 40px;">
        <div class="v-stat">
            <div class="v-stat-icon"><?= venom_icon('book', 22) ?></div>
            <strong style="font-size: 1.85rem; font-weight: 800;"><?= count($loans) ?></strong>
            <span class="v-stat-label">Sedang Dipinjam</span>
        </div>
        <div class="v-stat" data-tone="a">
            <div class="v-stat-icon"><?= venom_icon('clock', 22) ?></div>
            <strong style="font-size: 1.85rem; font-weight: 800;">
                <?php 
                $overdue = 0;
                foreach ($loans as $loan) {
                    if (!empty($loan['due_date']) && strtotime($loan['due_date']) < time()) $overdue++;
                }
                echo $overdue;
                ?>
            </strong>
            <span class="v-stat-label">Terlambat</span>
        </div>
        <div class="v-stat">
            <div class="v-stat-icon"><?= venom_icon('check', 22) ?></div>
            <strong style="font-size: 1.85rem; font-weight: 800;">
                <?php echo count($loans) - $overdue; ?>
            </strong>
            <span class="v-stat-label">Tepat Waktu</span>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 32px;">
        
        <!-- Left: Profile Card -->
        <div class="v-card v-pad">
            <h3 style="margin: 0 0 20px 0; font-size: 1.1rem; font-weight: 700;">Profil Saya</h3>
            
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="font-size: 0.8rem; color: var(--v-mut); font-weight: 600; display: block; margin-bottom: 4px;">ID Anggota</label>
                    <div style="font-weight: 600; font-size: 1.05rem;"><?= venom_e($member_id) ?></div>
                </div>
                
                <div>
                    <label style="font-size: 0.8rem; color: var(--v-mut); font-weight: 600; display: block; margin-bottom: 4px;">Nama Lengkap</label>
                    <div style="font-weight: 600;"><?= venom_e($member_name) ?></div>
                </div>
                
                <?php if ($member_email): ?>
                <div>
                    <label style="font-size: 0.8rem; color: var(--v-mut); font-weight: 600; display: block; margin-bottom: 4px;">Email</label>
                    <div style="font-weight: 600;"><?= venom_e($member_email) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($member_phone): ?>
                <div>
                    <label style="font-size: 0.8rem; color: var(--v-mut); font-weight: 600; display: block; margin-bottom: 4px;">Telepon</label>
                    <div style="font-weight: 600;"><?= venom_e($member_phone) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Digital Card Button -->
            <button onclick="showDigitalCard()" class="v-btn v-btn-primary" style="width: 100%; margin-top: 24px; justify-content: center;">
                <?= venom_icon('book', 18) ?> Lihat Kartu Digital
            </button>
        </div>

        <!-- Right: Loan History -->
        <div class="v-card v-pad">
            <h3 style="margin: 0 0 20px 0; font-size: 1.1rem; font-weight: 700;">Riwayat Peminjaman</h3>
            
            <?php if (empty($loans)): ?>
                <div style="text-align: center; padding: 40px 20px; color: var(--v-mut);">
                    <?= venom_icon('book', 32) ?>
                    <p style="margin: 12px 0 0 0;">Belum ada riwayat peminjaman.</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--v-line);">
                                <th style="padding: 12px 16px; text-align: left; color: var(--v-mut); font-weight: 600;">Judul</th>
                                <th style="padding: 12px 16px; text-align: left; color: var(--v-mut); font-weight: 600;">Tgl Pinjam</th>
                                <th style="padding: 12px 16px; text-align: left; color: var(--v-mut); font-weight: 600;">Jatuh Tempo</th>
                                <th style="padding: 12px 16px; text-align: left; color: var(--v-mut); font-weight: 600;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): 
                                $is_overdue = !empty($loan['due_date']) && strtotime($loan['due_date']) < time();
                                $is_returned = !empty($loan['return_date']);
                                $status = $is_returned ? 'Dikembalikan' : ($is_overdue ? 'Terlambat' : 'Dipinjam');
                                $status_color = $is_returned ? '#10b981' : ($is_overdue ? '#ef4444' : '#f59e0b');
                            ?>
                            <tr style="border-bottom: 1px solid var(--v-line);">
                                <td style="padding: 12px 16px; font-weight: 600;"><?= venom_e($loan['title'] ?? '—') ?></td>
                                <td style="padding: 12px 16px; color: var(--v-mut);"><?= venom_e($loan['loan_date'] ?? '—') ?></td>
                                <td style="padding: 12px 16px; color: var(--v-mut);"><?= venom_e($loan['due_date'] ?? '—') ?></td>
                                <td style="padding: 12px 16px;">
                                    <span style="padding: 4px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; background: <?= $status_color ?>20; color: <?= $status_color ?>;">
                                        <?= $status ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Digital Card Modal -->
<div id="digitalCardModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="v-card" style="max-width: 400px; width: 90%; padding: 32px; position: relative;">
        <button onclick="closeDigitalCard()" style="position: absolute; top: 16px; right: 16px; background: none; border: none; cursor: pointer; color: var(--v-mut);">
            <?= venom_icon('x', 24) ?>
        </button>
        
        <div style="text-align: center; padding: 24px; background: linear-gradient(135deg, var(--v-p), var(--v-a)); border-radius: 16px; color: #fff;">
            <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 8px;">KARTU ANGGOTA DIGITAL</div>
            <div style="font-size: 1.5rem; font-weight: 800; margin-bottom: 16px;"><?= venom_e($member_name) ?></div>
            <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 24px;">ID: <?= venom_e($member_id) ?></div>
            
            <!-- QR Code Placeholder -->
            <div style="background: #fff; padding: 16px; border-radius: 12px; display: inline-block;">
                <div style="width: 120px; height: 120px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.8rem;">
                    QR Code
                </div>
            </div>
            
            <div style="margin-top: 16px; font-size: 0.8rem; opacity: 0.8;">
                <?= venom_e(venom_library_name()) ?>
            </div>
        </div>
    </div>
</div>

<script>
function showDigitalCard() {
    document.getElementById('digitalCardModal').style.display = 'flex';
}

function closeDigitalCard() {
    document.getElementById('digitalCardModal').style.display = 'none';
}

// Close modal on outside click
document.getElementById('digitalCardModal').addEventListener('click', function(e) {
    if (e.target === this) closeDigitalCard();
});
</script>

<style>
@media (max-width: 768px) {
    .v-wrap v-sec > div:last-child {
        grid-template-columns: 1fr !important;
    }
}
</style>