<?php
/**
 * VENOM · Search result page (LENGKAP dengan Filter Sidebar)
 * Diadaptasi dari pustaka_ceria dengan styling VENOM
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

$keywords = $_GET['keywords'] ?? '';
$main_content = venom_core_content();
$view = $_SESSION['LIST_VIEW'] ?? 'grid';

$years = [];
$subjects = [];
$db = Venom_Core::db();

if ($db) {
    $years_q = $db->query('SELECT DISTINCT publish_year FROM biblio WHERE publish_year IS NOT NULL AND publish_year != "" ORDER BY publish_year DESC LIMIT 50');
    if ($years_q) {
        while ($row = $years_q->fetch_assoc()) $years[] = $row['publish_year'];
    }
    
    $subjects_q = $db->query('SELECT DISTINCT t.topic FROM mst_topic t INNER JOIN biblio_topic bt ON t.topic_id = bt.topic_id ORDER BY t.topic ASC LIMIT 30');
    if ($subjects_q) {
        while ($row = $subjects_q->fetch_assoc()) $subjects[] = $row['topic'];
    }
}
?>

<div class="v-wrap v-sec v-page-transition">
    <!-- Summary Bar -->
    <div class="v-search-summary">
        <div>
            <h2 class="v-search-title">Hasil Pencarian</h2>
            <?php if ($keywords !== ''): ?>
                <p class="v-search-keyword">Kata kunci: <strong>"<?= venom_e($keywords) ?>"</strong></p>
            <?php endif; ?>
        </div>

        <!-- Toggle View Form -->
        <form method="POST" action="<?= $_SERVER['PHP_SELF'] . '?' . http_build_query(array_filter($_GET, fn($k) => $k !== 'csrf_token', ARRAY_FILTER_USE_KEY)) ?>" class="v-view-toggle">
            <input type="hidden" name="csrf_token" value="<?= Venom_Sec::token('search') ?>"/>
            <?php if ($view === 'list'): ?>
                <input type="hidden" name="view" value="grid"/>
                <button type="submit" class="v-view-btn" title="Grid view"><?= venom_icon('grid', 18) ?></button>
                <button type="button" class="v-view-btn is-active" title="List view (Aktif)"><?= venom_icon('list', 18) ?></button>
            <?php else: ?>
                <button type="button" class="v-view-btn is-active" title="Grid view (Aktif)"><?= venom_icon('grid', 18) ?></button>
                <input type="hidden" name="view" value="list"/>
                <button type="submit" class="v-view-btn" title="List view"><?= venom_icon('list', 18) ?></button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Layout: Sidebar + Results -->
    <div class="v-search-layout">
        <!-- SIDEBAR: Filter Pencarian -->
        <aside class="v-filter-sidebar">
            <h3 class="v-filter-title"><?= venom_icon('filter', 18) ?> Filter Pencarian</h3>

            <!-- Filter Tahun -->
            <?php if (!empty($years)): ?>
            <div class="v-filter-group">
                <h4>Tahun Terbit</h4>
                <select class="v-filter-select" onchange="if(this.value) window.location.href='index.php?publish_year='+this.value+'&keywords=<?= urlencode($keywords) ?>&search=Search'">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= venom_e($year) ?>" <?= (isset($_GET['publish_year']) && $_GET['publish_year'] == $year) ? 'selected' : '' ?>>
                            <?= venom_e($year) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Filter Subjek -->
            <?php if (!empty($subjects)): ?>
            <div class="v-filter-group">
                <h4>Subjek / Topik</h4>
                <div class="v-filter-list">
                    <?php foreach ($subjects as $subj): ?>
                        <?php $is_active = (isset($_GET['subject']) && $_GET['subject'] == $subj); ?>
                        <a href="index.php?subject=<?= urlencode($subj) ?>&keywords=<?= urlencode($keywords) ?>&search=Search" 
                           class="v-filter-link <?= $is_active ? 'is-active' : '' ?>">
                            <?= venom_e($subj) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Reset Filter -->
            <div class="v-filter-reset">
                <a href="index.php?keywords=<?= urlencode($keywords) ?>&search=Search" class="v-btn v-btn-ghost v-btn-sm" style="width:100%; text-align:center;">
                    <?= venom_icon('x', 16) ?> Reset Filter
                </a>
            </div>
        </aside>

        <!-- MAIN: Results -->
        <main class="v-search-main">
            <?php if (trim(strip_tags($main_content)) === ''): ?>
                <?= venom_empty('Tidak ada koleksi yang ditemukan. Coba gunakan kata kunci lain atau fitur pencarian lanjutan.') ?>
            <?php else: ?>
                <div class="<?= $view === 'grid' ? 'v-grid v-grid-books' : 'v-list-books' ?>">
                    <?= $main_content ?>
                </div>

                <!-- Pagination -->
                <?php 
                global $pagination;
                if (!empty($pagination)): ?>
                    <div class="v-pagination">
                        <div class="paging">
                            <?= $pagination ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<style>
/* Search Layout */
.v-search-summary { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px; }
.v-search-title { font-size:1.5rem; font-weight:800; margin:0 0 6px 0; color:var(--v-tx); }
.v-search-keyword { margin:0; color:var(--v-mut); font-size:0.9rem; }
.v-view-toggle { display:flex; gap:4px; background:var(--v-bg2); padding:4px; border-radius:10px; border:1px solid var(--v-line); }
.v-view-btn { padding:8px 12px; border:none; background:transparent; border-radius:8px; cursor:pointer; color:var(--v-mut); transition:all 0.2s; display:flex; align-items:center; justify-content:center; }
.v-view-btn.is-active { background:var(--v-surf); color:var(--v-p); box-shadow:0 2px 8px rgba(0,0,0,0.06); }
.v-view-btn:hover:not(.is-active) { color:var(--v-tx); }

.v-search-layout { display:grid; grid-template-columns:280px 1fr; gap:28px; align-items:start; }

/* Sidebar Filter */
.v-filter-sidebar { position:sticky; top:90px; background:var(--v-glass); border:1px solid var(--v-gline); border-radius:var(--v-r); padding:20px; backdrop-filter:blur(14px); box-shadow:var(--v-sh); }
.v-filter-title { margin:0 0 16px 0; font-size:1rem; font-weight:800; color:var(--v-tx); display:flex; align-items:center; gap:8px; }
.v-filter-group { margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid var(--v-line); }
.v-filter-group h4 { margin:0 0 12px 0; font-size:0.85rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--v-mut); }
.v-filter-select { width:100%; padding:10px 12px; border-radius:10px; border:1px solid var(--v-line); background:var(--v-surf2); color:var(--v-tx); font-size:0.88rem; outline:none; cursor:pointer; transition:border-color 0.2s; }
.v-filter-select:focus { border-color:var(--v-p); }
.v-filter-list { display:flex; flex-direction:column; gap:6px; max-height:300px; overflow-y:auto; }
.v-filter-link { font-size:0.85rem; color:var(--v-tx); text-decoration:none; padding:8px 10px; border-radius:8px; transition:all 0.2s; background:var(--v-surf2); }
.v-filter-link:hover, .v-filter-link.is-active { background:var(--v-p); color:#fff; }
.v-filter-reset { margin-top:20px; }

/* List View Override */
.v-list-books { display: flex; flex-direction: column; gap: 16px; }
.v-list-books .v-book { flex-direction: row !important; max-width: 100%; }
.v-list-books .v-book-cover { width: 130px !important; min-width: 130px; aspect-ratio: auto !important; height: 190px; }
.v-list-books .v-book-body { padding: 20px 24px; justify-content: center; flex: 1; }
.v-list-books .v-book-title { font-size: 1.15rem; -webkit-line-clamp: 1; }

/* Pagination */
.v-pagination { margin-top:48px; display:flex; justify-content:center; }
.paging { display:flex; gap:6px; flex-wrap:wrap; justify-content:center; align-items:center; }
.paging a, .paging span { padding: 8px 14px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; background: var(--v-glass); border: 1px solid var(--v-line); color: var(--v-tx); transition: all 0.2s; }
.paging a:hover, .paging .current { background: var(--v-p); color: #fff; border-color: var(--v-p); }

/* Responsive */
@media (max-width: 900px) {
    .v-search-layout { grid-template-columns: 1fr !important; }
    .v-filter-sidebar { position: static !important; margin-bottom: 24px; }
}
</style>