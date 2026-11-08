<?php
/**
 * VENOM · Member Dashboard Premium
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/../helpers/core.php';

if (!isset($_SESSION['m_id']) || !isset($_SESSION['m_name'])) {
    echo '<div class="v-mb-wrap">';
    echo '<div class="v-mb-gate">';
    echo '<div class="v-mb-gate-icon">' . venom_icon('user', 32) . '</div>';
    echo '<h2>Area Anggota</h2>';
    echo '<p>Silakan masuk untuk mengakses dashboard anggota Anda.</p>';
    echo '<a href="index.php?p=login" class="v-btn v-btn-primary" style="display:inline-flex">' . venom_icon('right', 18) . ' Masuk ke Akun</a>';
    echo '</div></div>';
    return;
}

$member_id    = $_SESSION['m_id'];
$member_name  = $_SESSION['m_name'];
$member_email = $_SESSION['m_email'] ?? '';
$member_phone = $_SESSION['m_phone'] ?? '';

$member_data = [];
$loans = [];
$loans_active = 0;
$loans_overdue = 0;
$loans_total = 0;

if (Venom_Core::db()) {
    $member_data = venom_row("SELECT * FROM member WHERE member_id = '" . Venom_Sec::sql($member_id) . "' LIMIT 1");
    if (!empty($member_data)) {
        $member_email = $member_email ?: ($member_data['email'] ?? '');
        $member_phone = $member_phone ?: ($member_data['phone'] ?? '');
    }

    $loans = venom_rows("SELECT l.loan_id, l.loan_date, l.due_date, l.return_date, i.item_code, b.title, b.biblio_id
                          FROM loan l
                          LEFT JOIN item i ON i.item_code = l.item_code
                          LEFT JOIN biblio b ON b.biblio_id = l.member_id
                          WHERE l.member_id = '" . Venom_Sec::sql($member_id) . "'
                          ORDER BY l.return_date IS NULL DESC, l.due_date ASC
                          LIMIT 12");

    // Perbaikan JOIN: item.biblio_id
    $loans = venom_rows("SELECT l.loan_id, l.loan_date, l.due_date, l.return_date, l.item_code, b.title, b.biblio_id
                          FROM loan l
                          LEFT JOIN item i ON i.item_code = l.item_code
                          LEFT JOIN biblio b ON b.biblio_id = i.biblio_id
                          WHERE l.member_id = '" . Venom_Sec::sql($member_id) . "'
                          ORDER BY l.return_date IS NULL DESC, l.due_date ASC
                          LIMIT 12");

    foreach ($loans as $l) {
        $loans_total++;
        if (empty($l['return_date'])) {
            $loans_active++;
            if (!empty($l['due_date']) && strtotime($l['due_date']) < time()) $loans_overdue++;
        }
    }
}

$initials = '';
$parts = preg_split('/\s+/', trim($member_name));
foreach (array_slice($parts, 0, 2) as $p) {
    $initials .= function_exists('mb_substr') ? mb_substr($p, 0, 1, 'UTF-8') : substr($p, 0, 1);
}
$initials = strtoupper($initials);

// QR Code generator (simple SVG)
function venom_qr_svg($text, $size = 120) {
    $h = sprintf('%x', crc32($text));
    $bits = str_pad(decbin(hexdec($h)), 32, '0', STR_PAD_LEFT);
    $n = 8;
    $cell = (int) floor($size / $n);
    $svg = '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . ($n*$cell) . ' ' . ($n*$cell) . '" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<rect width="100%" height="100%" fill="#fff"/>';
    for ($y = 0; $y < $n; $y++) {
        for ($x = 0; $x < $n; $x++) {
            $idx = ($y * $n + $x) % 32;
            if ($bits[$idx] === '1') {
                $svg .= '<rect x="' . ($x*$cell) . '" y="' . ($y*$cell) . '" width="' . $cell . '" height="' . $cell . '" fill="#0f172a"/>';
            }
        }
    }
    $svg .= '</svg>';
    return $svg;
}
?>

<div class="v-mb-wrap v-page-transition">
    <div class="v-mb-head">
        <div>
            <h1>Selamat Datang, <?= venom_e($member_name) ?></h1>
            <p>Dashboard anggota perpustakaan Anda</p>
        </div>
        <a href="index.php?p=logout" class="v-btn v-btn-ghost v-btn-sm"><?= venom_icon('right', 16) ?> Keluar</a>
    </div>

    <div class="v-mb-layout">
        <!-- LEFT: Profile + Digital Card -->
        <aside class="v-mb-profile">
            <div class="v-mb-avatar"><?= venom_e($initials) ?></div>
            <h2 class="v-mb-name"><?= venom_e($member_name) ?></h2>
            <p class="v-mb-id">ID: <?= venom_e($member_id) ?></p>

            <div class="v-mb-list">
                <?php if ($member_email): ?>
                <div class="v-mb-row">
                    <?= venom_icon('mail', 16) ?>
                    <div><b>Email</b><span><?= venom_e($member_email) ?></span></div>
                </div>
                <?php endif; ?>
                <?php if ($member_phone): ?>
                <div class="v-mb-row">
                    <?= venom_icon('phone', 16) ?>
                    <div><b>Telepon</b><span><?= venom_e($member_phone) ?></span></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($member_data['member_since'])): ?>
                <div class="v-mb-row">
                    <?= venom_icon('clock', 16) ?>
                    <div><b>Anggota sejak</b><span><?= venom_e(substr($member_data['member_since'], 0, 10)) ?></span></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($member_data['expire_date'])): ?>
                <div class="v-mb-row">
                    <?= venom_icon('shield', 16) ?>
                    <div><b>Berlaku sampai</b><span><?= venom_e(substr($member_data['expire_date'], 0, 10)) ?></span></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="v-mb-digicard">
                <div class="v-mb-digicard-label">Kartu Anggota Digital</div>
                <div class="v-mb-digicard-title"><?= venom_e(venom_library_name()) ?></div>
                <div class="v-mb-digicard-qr"><?= venom_qr_svg($member_id . '|' . $member_name, 110) ?></div>
            </div>
        </aside>

        <!-- RIGHT: Stats + Loans -->
        <div class="v-mb-grid">
            <!-- Stats -->
            <div class="v-mb-stats">
                <div class="v-mb-stat">
                    <div class="v-mb-stat-icon"><?= venom_icon('book', 20) ?></div>
                    <div>
                        <div class="v-mb-stat-num"><?= (int) $loans_active ?></div>
                        <div class="v-mb-stat-lbl">Sedang Dipinjam</div>
                    </div>
                </div>
                <div class="v-mb-stat">
                    <div class="v-mb-stat-icon" style="background:color-mix(in srgb,var(--v-warn) 14%,transparent);color:var(--v-warn)"><?= venom_icon('clock', 20) ?></div>
                    <div>
                        <div class="v-mb-stat-num"><?= (int) $loans_overdue ?></div>
                        <div class="v-mb-stat-lbl">Terlambat</div>
                    </div>
                </div>
                <div class="v-mb-stat">
                    <div class="v-mb-stat-icon" style="background:color-mix(in srgb,var(--v-ok) 14%,transparent);color:var(--v-ok)"><?= venom_icon('check', 20) ?></div>
                    <div>
                        <div class="v-mb-stat-num"><?= (int) ($loans_total - $loans_active) ?></div>
                        <div class="v-mb-stat-lbl">Selesai</div>
                    </div>
                </div>
            </div>

            <!-- Active Loans -->
            <div class="v-mb-card">
                <div class="v-mb-card-head">
                    <?= venom_icon('layers', 20) ?>
                    <h3>Riwayat Peminjaman</h3>
                </div>
                <?php if (empty($loans)): ?>
                    <p style="color:var(--v-mut);text-align:center;padding:20px 0">Belum ada riwayat peminjaman.</p>
                <?php else: ?>
                    <div style="overflow-x:auto">
                        <table class="v-mb-loans">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Kode</th>
                                    <th>Pinjam</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($loans as $l):
                                $is_returned = !empty($l['return_date']);
                                $is_overdue  = !$is_returned && !empty($l['due_date']) && strtotime($l['due_date']) < time();
                                $is_active   = !$is_returned;
                                $chip_class = $is_returned ? 'v-mb-chip-ok' : ($is_overdue ? 'v-mb-chip-err' : 'v-mb-chip-warn');
                                $chip_text  = $is_returned ? 'Dikembalikan' : ($is_overdue ? 'Terlambat' : 'Dipinjam');
                            ?>
                                <tr>
                                    <td>
                                        <p class="v-mb-loan-title"><?= venom_e($l['title'] ?? '—') ?></p>
                                    </td>
                                    <td class="v-items-code"><?= venom_e($l['item_code'] ?? '—') ?></td>
                                    <td><?= venom_e($l['loan_date'] ?? '—') ?></td>
                                    <td><?= venom_e($l['due_date'] ?? '—') ?></td>
                                    <td><span class="v-mb-chip <?= $chip_class ?>"><?= $chip_text ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="v-mb-card">
                <div class="v-mb-card-head">
                    <?= venom_icon('sparkles', 20) ?>
                    <h3>Layanan Cepat</h3>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px">
                    <a href="index.php?keywords=&search=Search" class="v-detail-action"><?= venom_icon('search', 16) ?> Cari Koleksi</a>
                    <a href="index.php?p=news" class="v-detail-action"><?= venom_icon('news', 16) ?> Berita Terbaru</a>
                    <a href="index.php?p=suggest" class="v-detail-action"><?= venom_icon('star', 16) ?> Usulkan Koleksi</a>
                    <a href="index.php?p=help" class="v-detail-action"><?= venom_icon('shield', 16) ?> Panduan</a>
                </div>
            </div>
        </div>
    </div>
</div>