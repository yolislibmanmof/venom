<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

// Cek apakah ini halaman single news
$news_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($news_id > 0) {
    // Tampilkan single news
    $news = venom_row("SELECT * FROM content WHERE content_id = " . $news_id . " LIMIT 1");
    
    if (empty($news)) {
        echo '<div class="v-wrap v-sec">' . venom_empty('Berita tidak ditemukan.') . '</div>';
        return;
    }
    ?>
    <div class="v-wrap v-sec v-page-transition">
        <nav aria-label="Breadcrumb" style="margin-bottom: 24px; color: var(--v-mut); font-size: 0.9rem;">
            <a href="index.php" class="text-decoration-none" style="color: var(--v-mut);">Beranda</a>
            <span style="margin: 0 8px;">/</span>
            <a href="index.php?p=news" class="text-decoration-none" style="color: var(--v-mut);">Berita</a>
            <span style="margin: 0 8px;">/</span>
            <span style="color: var(--v-tx); font-weight: 600;">Detail</span>
        </nav>

        <article class="v-card v-pad" style="max-width: 800px; margin: 0 auto;">
            <div style="margin-bottom: 24px;">
                <span class="v-chip v-chip-xs" style="margin-bottom: 12px; display: inline-block;">
                    <?= venom_e(!empty($news['input_date']) ? date('d M Y', strtotime($news['input_date'])) : '') ?>
                </span>
                <h1 style="font-size: 2rem; font-weight: 800; margin: 16px 0; line-height: 1.3;">
                    <?= venom_e($news['content_title'] ?? 'Tanpa Judul') ?>
                </h1>
            </div>

            <div style="line-height: 1.8; color: var(--v-tx); font-size: 1.05rem;">
                <?= $news['content_desc'] ?? '' ?>
            </div>

            <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--v-line);">
                <a href="index.php?p=news" class="v-btn v-btn-ghost" style="border: 1px solid var(--v-line);">
                    <?= venom_icon('left', 18) ?> Kembali ke Daftar Berita
                </a>
            </div>
        </article>
    </div>
    <?php
    return;
}

// Tampilkan daftar berita
$news_list = venom_news(20);
?>

<div class="v-wrap v-sec v-page-transition">
    <div style="text-align: center; margin-bottom: 48px;">
        <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 12px 0;">
            Berita & Pengumuman
        </h1>
        <p style="color: var(--v-mut); font-size: 1.1rem; margin: 0; max-width: 600px; margin: 0 auto;">
            Informasi terbaru dari perpustakaan kami
        </p>
    </div>

    <?php if (empty($news_list)): ?>
        <?= venom_empty('Belum ada berita atau pengumuman.') ?>
    <?php else: ?>
        <div class="v-grid v-grid-news">
            <?php foreach ($news_list as $news): 
                $exc = Venom_Sec::text($news['content_desc'] ?? '', 160);
                $date = !empty($news['input_date']) ? date('d M Y', strtotime($news['input_date'])) : '';
            ?>
                <article class="v-card v-pad v-news v-reveal" style="cursor: pointer;" onclick="window.location.href='index.php?p=news&id=<?= (int)$news['content_id'] ?>'">
                    <span class="v-chip v-chip-xs"><?= venom_e($date) ?></span>
                    <h3 style="font-size: 1.15rem; margin: 12px 0; line-height: 1.4;">
                        <?= venom_e($news['content_title'] ?? 'Tanpa Judul') ?>
                    </h3>
                    <p style="color: var(--v-mut); font-size: 0.95rem; line-height: 1.6; margin: 0 0 16px 0;">
                        <?= venom_e($exc) ?>…
                    </p>
                    <span class="v-btn v-btn-ghost v-btn-sm" style="border: 1px solid var(--v-line);">
                        Selengkapnya <?= venom_icon('right', 15) ?>
                    </span>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>