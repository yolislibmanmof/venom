<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

$core_content = venom_core_content();
if ($core_content !== '' && empty($biblio_list)) {
    echo '<div class="v-wrap v-core v-page-transition">' . $core_content . '</div>';
    return;
}
?>

<div class="v-wrap v-sec v-page-transition">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--v-tx);">Hasil Pencarian</h2>
        
        <!-- View Toggle -->
        <div style="display: flex; gap: 4px; background: var(--v-bg2); padding: 4px; border-radius: 10px; border: 1px solid var(--v-line);">
            <button class="v-view-btn is-active" onclick="setView('grid', this)" style="padding: 8px 12px; border: none; background: transparent; border-radius: 8px; cursor: pointer; color: var(--v-mut); transition: all 0.2s;" title="Tampilan Grid">
                <?= venom_icon('grid', 18) ?>
            </button>
            <button class="v-view-btn" onclick="setView('list', this)" style="padding: 8px 12px; border: none; background: transparent; border-radius: 8px; cursor: pointer; color: var(--v-mut); transition: all 0.2s;" title="Tampilan List">
                <?= venom_icon('list', 18) ?>
            </button>
        </div>
    </div>

    <?php if (empty($biblio_list)): ?>
        <?= venom_empty('Tidak ada koleksi yang ditemukan. Coba gunakan kata kunci lain atau fitur pencarian lanjutan.') ?>
    <?php else: ?>
        <div id="biblioContainer" class="v-grid v-grid-books">
            <?php foreach ($biblio_list as $item): 
                $badge = '';
                if (!empty($item['promoted']) && $item['promoted'] == 1) $badge = 'UNGGULAN';
            ?>
                <article class="v-book">
                    <a class="v-book-cover" href="index.php?p=show_detail&id=<?= (int)$item['biblio_id'] ?>">
                        <img src="<?= venom_e(venom_cover($item['image'] ?? '', $item['title'] ?? '')) ?>" 
                             alt="<?= venom_e($item['title'] ?? 'Cover') ?>" loading="lazy" decoding="async">
                        <span class="v-book-shine"></span>
                        <?php if ($badge): ?>
                            <span class="v-book-badge"><?= $badge ?></span>
                        <?php endif; ?>
                        <span class="v-book-hover"><?= venom_icon('right', 18) ?> Lihat Detail</span>
                    </a>
                    <div class="v-book-body">
                        <h3 class="v-book-title">
                            <a href="index.php?p=show_detail&id=<?= (int)$item['biblio_id'] ?>">
                                <?= venom_e($item['title'] ?? 'Tanpa Judul') ?>
                            </a>
                        </h3>
                        <p class="v-book-author"><?= venom_e($item['authors'] ?? $item['author'] ?? 'Penulis tidak diketahui') ?></p>
                        <div class="v-book-meta">
                            <span class="v-chip v-chip-xs"><?= venom_e($item['publish_year'] ?? '—') ?></span>
                            <?php if (!empty($item['publisher_name'])): ?>
                                <span class="v-book-pub"><?= venom_e($item['publisher_name']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (!empty($pagination)): ?>
            <div style="margin-top: 48px; display: flex; justify-content: center;">
                <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; align-items: center;">
                    <?= $pagination ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
/* List View Override Styles */
.v-grid-books.list-view {
    grid-template-columns: 1fr !important;
    gap: 16px !important;
}
.v-grid-books.list-view .v-book {
    flex-direction: row !important;
    max-width: 100%;
}
.v-grid-books.list-view .v-book-cover {
    width: 130px !important;
    min-width: 130px;
    aspect-ratio: auto !important;
    height: 190px;
}
.v-grid-books.list-view .v-book-body {
    padding: 20px 24px;
    justify-content: center;
}
.v-grid-books.list-view .v-book-title {
    font-size: 1.15rem;
    -webkit-line-clamp: 1;
}
.v-view-btn.is-active {
    background: var(--v-surf) !important;
    color: var(--v-p) !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

/* Fix SLiMS default pagination styling to match VENOM */
.paging { display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; }
.paging a, .paging span { 
    padding: 8px 14px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;
    background: var(--v-glass); border: 1px solid var(--v-line); color: var(--v-tx); transition: all 0.2s;
}
.paging a:hover { background: var(--v-p); color: #fff; border-color: var(--v-p); }
.paging .current { background: var(--v-p); color: #fff; border-color: var(--v-p); }
</style>

<script>
function setView(mode, btn) {
    const container = document.getElementById('biblioContainer');
    const buttons = document.querySelectorAll('.v-view-btn');
    
    buttons.forEach(b => b.classList.remove('is-active'));
    btn.classList.add('is-active');
    
    if (mode === 'list') {
        container.classList.add('list-view');
    } else {
        container.classList.remove('list-view');
    }
}
</script>