<?php
/**
 * VENOM · Detail Koleksi Premium (FINAL PATCHED)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

$core_content = venom_core_content();
if ($core_content !== '' && empty($detail)) {
    echo '<div class="v-wrap v-core v-page-transition">' . $core_content . '</div>';
    return;
}
if (empty($detail) || !isset($detail['title'])) {
    echo '<div class="v-wrap v-sec">' . venom_empty('Detail koleksi tidak ditemukan.') . '</div>';
    return;
}

$biblio_id   = (int) ($detail['biblio_id'] ?? 0);
$title       = $detail['title'] ?? 'Tanpa Judul';
$authors_str = $detail['authors'] ?? ($detail['author'] ?? 'Penulis tidak diketahui');
$year        = $detail['publish_year'] ?? '—';
$publisher   = $detail['publisher_name'] ?? '—';
$publish_place = $detail['publish_place'] ?? '—'; // Tambahan untuk Chicago/Turabian
$isbn        = $detail['isbn_issn'] ?? '—';
$call_number = $detail['call_number'] ?? '—';
$collation   = $detail['collation'] ?? '—'; // Deskripsi Fisik (halaman, ukuran)
$notes       = $detail['notes'] ?? '';
$gmd         = $detail['gmd_name'] ?? 'Buku';
$language    = $detail['language_name'] ?? '—';
$topics      = is_array($detail['topics'] ?? null) ? $detail['topics'] : [];
$items       = is_array($detail['items'] ?? null) ? $detail['items'] : [];
$image       = $detail['image'] ?? '';
$cover_url   = venom_cover($image, $title);

$avail_count = 0;
foreach ($items as $it) { if (empty($it['is_loan']) || $it['is_loan'] == 0) $avail_count++; }
$loan_count = count($items) - $avail_count;

// Related books logic (tetap sama)
$related = [];
if ($biblio_id > 0 && !empty($topics)) {
    $topic_ids = []; $db = Venom_Core::db();
    if ($db) {
        foreach ($topics as $t) {
            $r = venom_row("SELECT topic_id FROM mst_topic WHERE topic = '" . Venom_Sec::sql($t['topic']) . "' LIMIT 1");
            if (!empty($r['topic_id'])) $topic_ids[] = (int) $r['topic_id'];
        }
        if (!empty($topic_ids)) {
            $ids = implode(',', $topic_ids);
            $sql = venom_select_biblio() . " JOIN biblio_topic bt ON bt.biblio_id = b.biblio_id WHERE bt.topic_id IN ($ids) AND b.biblio_id != $biblio_id AND b.opac_hide = 0 GROUP BY b.biblio_id ORDER BY COUNT(bt.topic_id) DESC LIMIT 6";
            $related = venom_rows($sql);
        }
    }
}
if (empty($related)) $related = venom_featured(6);

// AMAN DARI XSS: Gunakan JSON_HEX_TAG
$json_flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$e_title  = venom_e($title);
$e_author = venom_e($authors_str);
?>

<div class="v-detail-wrap v-page-transition">
    <nav class="v-breadcrumb" aria-label="Breadcrumb">
        <a href="index.php"><?= venom_icon('home', 14) ?> Beranda</a>
        <span class="v-breadcrumb-sep">/</span>
        <a href="index.php?keywords=&search=Search">Koleksi</a>
        <span class="v-breadcrumb-sep">/</span>
        <span class="v-breadcrumb-current"><?= venom_e(Venom_Sec::text($title, 60)) ?></span>
    </nav>

    <div class="v-detail-main">
        <aside class="v-detail-cover">
            <div class="v-detail-cover-inner" data-tilt>
                <img src="<?= venom_e($cover_url) ?>" alt="<?= $e_title ?>" width="600" height="840">
                <span class="v-detail-cover-shine"></span>
                <span class="v-detail-cover-spine"></span>
                <span class="v-detail-cover-badge" style="<?= $avail_count > 0 ? '' : 'background:linear-gradient(120deg,#ef4444,#f97316)' ?>">
                    <?= $avail_count > 0 ? 'Tersedia' : 'Dipinjam' ?>
                </span>
            </div>
            <div class="v-detail-actions">
                <button class="v-detail-action v-detail-action-primary" data-modal-open="vnxShareModal"><?= venom_icon('share', 16) ?> Bagikan</button>
                <button class="v-detail-action" onclick="window.print()"><?= venom_icon('print', 16) ?> Cetak</button>
            </div>
        </aside>

        <div class="v-detail-info">
            <h1><?= $e_title ?></h1>
            <p class="v-detail-author">oleh <a href="index.php?author=<?= urlencode($authors_str) ?>&search=Search"><?= $e_author ?></a></p>

            <dl class="v-detail-meta">
                <dt>Penerbit</dt><dd class="v-meta-text"><?= venom_e($publisher) ?>, <?= venom_e($year) ?></dd>
                <dt>ISBN/ISSN</dt><dd><?= venom_e($isbn !== '' ? $isbn : '—') ?></dd>
                <dt>No. Panggil</dt><dd class="v-meta-text"><span class="v-detail-callnum"><?= venom_e($call_number !== '' ? $call_number : '—') ?></span></dd>
                <dt>Deskripsi Fisik</dt><dd class="v-meta-text"><?= venom_e($collation) ?></dd>
                <dt>Tipe</dt><dd class="v-meta-text"><?= venom_e($gmd) ?></dd>
                <dt>Bahasa</dt><dd class="v-meta-text"><?= venom_e($language) ?></dd>
                <dt>Ketersediaan</dt>
                <dd class="v-meta-text">
                    <?php if ($avail_count > 0): ?>
                        <span style="color:var(--v-ok);font-weight:700"><?= $avail_count ?> Tersedia</span>
                        <?php if ($loan_count > 0): ?> · <span style="color:var(--v-err)"><?= $loan_count ?> Dipinjam</span><?php endif; ?>
                    <?php else: ?>
                        <span style="color:var(--v-err);font-weight:700">Sedang dipinjam semua</span>
                    <?php endif; ?>
                </dd>
            </dl>

            <?php if (!empty($topics)): ?>
            <div style="margin-bottom:22px">
                <div style="font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--v-mut);margin-bottom:8px">Subjek</div>
                <div class="v-detail-topics">
                    <?php foreach ($topics as $t): ?>
                        <a href="index.php?subject=<?= urlencode($t['topic']) ?>&search=Search" class="v-detail-topic"><?= venom_e($t['topic']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- TABS (Duplikasi Abstrak dihapus, digabung ke Deskripsi) -->
            <div class="v-detail-tabs" role="tablist">
                <button class="v-detail-tab is-active" data-tab="desc" role="tab"><?= venom_icon('book', 16) ?> Deskripsi</button>
                <button class="v-detail-tab" data-tab="items" role="tab"><?= venom_icon('layers', 16) ?> Eksemplar <span class="v-detail-tab-count"><?= count($items) ?></span></button>
                <button class="v-detail-tab" data-tab="cite" role="tab"><?= venom_icon('quote', 16) ?> Sitasi</button>
            </div>

            <div class="v-tab-pane is-active" id="tab-desc" role="tabpanel">
                <?php if (!empty($notes)): ?>
                    <div class="v-notes-box">
                        <h4>Abstrak / Sinopsis</h4>
                        <?= nl2br(venom_e($notes)) ?>
                    </div>
                <?php else: ?>
                    <p style="color:var(--v-mut)">Belum ada deskripsi atau abstrak untuk koleksi ini.</p>
                <?php endif; ?>
            </div>

            <div class="v-tab-pane" id="tab-items" role="tabpanel">
                <?php if (empty($items)): ?>
                    <p style="color:var(--v-mut)">Belum ada data eksemplar.</p>
                <?php else: ?>
                    <table class="v-items-table">
                        <thead><tr><th>Kode</th><th>No. Panggil</th><th>Lokasi</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($items as $it): $is_avail = empty($it['is_loan']) || $it['is_loan'] == 0; ?>
                            <tr>
                                <td class="v-items-code"><?= venom_e($it['item_code'] ?? '—') ?></td>
                                <td class="v-items-code"><?= venom_e($it['call_number'] ?? '—') ?></td>
                                <td><?= venom_e($it['location_name'] ?? 'Umum') ?></td>
                                <td>
                                    <?php if ($is_avail): ?>
                                        <span class="v-status-chip v-status-avail"><span class="v-status-dot"></span>Tersedia</span>
                                    <?php else: ?>
                                        <span class="v-status-chip v-status-loan"><span class="v-status-dot"></span>Dipinjam</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="v-tab-pane" id="tab-cite" role="tabpanel">
                <div class="v-cite-box">
                    <div class="v-cite-ctrl">
                        <label for="vCiteStyle">Gaya sitasi:</label>
                        <select id="vCiteStyle">
                            <option value="apa">APA Style</option>
                            <option value="mla">MLA Style</option>
                            <option value="chicago">Chicago Style</option>
                            <option value="turabian">Turabian</option>
                        </select>
                    </div>
                    <div class="v-cite-output" id="vCiteOut"></div>
                    <button class="v-cite-copy" id="vCiteCopy"><?= venom_icon('copy', 14) ?> Salin</button>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($related)): ?>
    <section class="v-related">
        <div class="v-related-head">
            <h2><?= venom_icon('sparkles', 22) ?> Koleksi Terkait</h2>
            <a href="index.php?keywords=<?= urlencode($title) ?>&search=Search" class="v-btn v-btn-ghost v-btn-sm">Lihat Semua <?= venom_icon('right', 15) ?></a>
        </div>
        <div class="v-related-grid">
            <?php foreach ($related as $rb): ?>
                <?php if (function_exists('venom_card_biblio')): ?>
                    <?= venom_card_biblio($rb, !empty($rb['promoted']) ? 'UNGGULAN' : '') ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
(function(){
    var tabs = document.querySelectorAll('.v-detail-tab');
    var panes = document.querySelectorAll('.v-tab-pane');
    tabs.forEach(function(t){
        t.addEventListener('click', function(){
            tabs.forEach(function(x){x.classList.remove('is-active')});
            panes.forEach(function(x){x.classList.remove('is-active')});
            t.classList.add('is-active');
            var p = document.getElementById('tab-' + t.getAttribute('data-tab'));
            if (p) p.classList.add('is-active');
        });
    });

    // Data Sitasi (Aman dari XSS & Format Chicago/Turabian Diperbaiki)
    var data = {
        title: <?= json_encode($title, $json_flags) ?>,
        author: <?= json_encode($authors_str, $json_flags) ?>,
        year: <?= json_encode($year, $json_flags) ?>,
        publisher: <?= json_encode($publisher, $json_flags) ?>,
        place: <?= json_encode($publish_place, $json_flags) ?>,
        isbn: <?= json_encode($isbn, $json_flags) ?>
    };
    
    var sel = document.getElementById('vCiteStyle');
    var out = document.getElementById('vCiteOut');
    
    function e(s){ return (s+'').replace(/&/g,'&amp;').replace(/</g,'&lt;'); }
    
    function fmt(){
        var st = sel.value;
        var a = e(data.author), t = e(data.title), y = e(data.year), p = e(data.publisher), pl = e(data.place);
        var h = '';
        if (st === 'apa'){
            h = a + ' (' + y + '). <em>' + t + '</em>. ' + p + '.';
        } else if (st === 'mla'){
            h = a + '. <em>' + t + '</em>. ' + p + ', ' + y + '.';
        } else if (st === 'chicago'){
            h = a + '. <em>' + t + '</em>. ' + pl + ': ' + p + ', ' + y + '.';
        } else if (st === 'turabian'){
            h = a + '. <em>' + t + '</em>. ' + pl + ': ' + p + ', ' + y + '.';
        }
        out.innerHTML = h;
    }
    
    sel.addEventListener('change', fmt);
    fmt();

    document.getElementById('vCiteCopy').addEventListener('click', function(){
        var txt = out.innerText;
        if (navigator.clipboard) navigator.clipboard.writeText(txt).then(function(){
            if (typeof venomToast === 'function') venomToast('Sitasi berhasil disalin!');
        });
    });
})();
</script>