<?php
/**
 * VENOM · Detail Koleksi Premium (v3 — Kompatibel variabel FLAT inti SLiMS)
 * Kontrak variabel mengikuti detail_template.php bawaan SLiMS:
 * $biblio_id, $title, $authors(HTML), $image(HTML <img>), $notes, $gmd_name,
 * $availability(HTML), $series_title, $call_number, $publish_place,
 * $publisher_name, $publish_year, $collation, $language_name, $isbn_issn,
 * $classification, $content_type, $media_type, $carrier_type, $edition,
 * $subjects(HTML), $spec_detail_info, $sor, $biblio_custom, $related(HTML), $file_att(HTML)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

/* ═══ 1) NORMALISASI SUMBER DATA (flat → array $detail) ═══ */
if (!isset($detail) || !is_array($detail) || !isset($detail['title'])) {
    if (!isset($title) || trim((string) $title) === '') {
        $core_content = venom_core_content();
        if ($core_content !== '') { echo '<div class="v-wrap v-core v-page-transition">' . $core_content . '</div>'; return; }
        echo '<div class="v-wrap v-sec">' . venom_empty('Detail koleksi tidak ditemukan.') . '</div>';
        return;
    }
    $detail = [
        'biblio_id'         => isset($biblio_id) ? (int) $biblio_id : 0,
        'title'             => $title,
        'authors'           => $authors ?? '',
        'image'             => $image ?? '',
        'notes'             => $notes ?? '',
        'gmd_name'          => $gmd_name ?? '',
        'publish_year'      => $publish_year ?? '',
        'publish_place'     => $publish_place ?? '',
        'publisher_name'    => $publisher_name ?? '',
        'isbn_issn'         => $isbn_issn ?? '',
        'call_number'       => $call_number ?? '',
        'collation'         => $collation ?? '',
        'language_name'     => $language_name ?? '',
        'classification'    => $classification ?? '',
        'series_title'      => $series_title ?? '',
        'edition'           => $edition ?? '',
        'content_type'      => $content_type ?? '',
        'media_type'        => $media_type ?? '',
        'carrier_type'      => $carrier_type ?? '',
        'spec_detail'       => $spec_detail_info ?? '',
        'sor'               => $sor ?? '',
        'subjects_html'     => $subjects ?? '',
        'availability_html' => $availability ?? '',
        'file_att'          => $file_att ?? '',
        'related_html'      => $related ?? '',
        'biblio_custom'     => $biblio_custom ?? [],
    ];
}

/* ═══ 2) HELPER PARSING AMAN ═══ */
if (!function_exists('venom_parse_link_chips')) {
    /** Ubah HTML inti (<a href="?author=...">Nama</a> - Personal Name<br/>) jadi chip bersih. */
    function venom_parse_link_chips($html) {
        $out = [];
        if (preg_match_all('/<a[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', (string) $html, $m, PREG_SET_ORDER)) {
            foreach ($m as $hit) {
                $name = trim(strip_tags($hit[2]));
                if ($name === '') continue;
                $href = $hit[1];
                if (strpos($href, 'http') !== 0 && strpos($href, 'index.php') !== 0 && strpos($href, '?') === 0) {
                    $href = 'index.php' . $href;
                }
                $out[] = ['name' => $name, 'url' => $href];
            }
        }
        return $out;
    }
}
if (!function_exists('venom_resolve_cover')) {
    /** $image inti bisa berupa tag <img .../> ATAU nama file. Kembalikan URL yang benar. */
    function venom_resolve_cover($image_raw, $title) {
        $s = (string) $image_raw;
        if (stripos($s, '<img') !== false) {
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $s, $m)) {
                return html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
            }
            return venom_cover('', $title);
        }
        return venom_cover(trim($s), $title);
    }
}

/* ═══ 3) EKSTRAK NILAI ═══ */
$v_id       = (int) ($detail['biblio_id'] ?? 0);
$v_title    = stripslashes((string) ($detail['title'] ?? 'Tanpa Judul'));
$v_year     = (string) ($detail['publish_year'] ?? '');
$v_publisher= (string) ($detail['publisher_name'] ?? '');
$v_place    = (string) ($detail['publish_place'] ?? '');
$v_isbn     = (string) ($detail['isbn_issn'] ?? '');
$v_callnum  = (string) ($detail['call_number'] ?? '');
$v_collation= (string) ($detail['collation'] ?? '');
$v_notes    = (string) ($detail['notes'] ?? '');
$v_gmd      = (string) ($detail['gmd_name'] ?? ($detail['gmd'] ?? ''));
$v_lang     = (string) ($detail['language_name'] ?? '');
$v_cover    = venom_resolve_cover($detail['image'] ?? '', $v_title);

/* Penulis → chip (tidak lagi escape mentah) */
$v_author_chips = venom_parse_link_chips($detail['authors'] ?? '');
if (!$v_author_chips) {
    $plain = trim(strip_tags(str_replace(['<br />', '<br/>', '<br>'], '; ', (string) ($detail['authors'] ?? ''))));
    $plain = trim(preg_replace('/\s*-\s*(Personal Name|Badan Korporasi|Conference|Nama Orang)\s*/i', ' ', $plain));
    if ($plain !== '') {
        foreach (array_filter(array_map('trim', explode(';', $plain))) as $pn) {
            $v_author_chips[] = ['name' => $pn, 'url' => 'index.php?author=' . rawurlencode($pn) . '&search=Search'];
        }
    }
}
$v_author_plain = implode('; ', array_map(function ($a) { return $a['name']; }, $v_author_chips));

/* ═══ 4) EKSEMPLAR & KETERSEDIAAN (query langsung, akurat) ═══ */
$v_items = is_array($detail['items'] ?? null) ? $detail['items'] : [];
if (!$v_items && $v_id > 0 && Venom_Core::db()) {
    $v_items = venom_rows("SELECT i.item_code, i.call_number, l.location_name,
            CASE WHEN IFNULL(mis.no_loan,0)=1 THEN 1
                 WHEN EXISTS(SELECT 1 FROM loan l2 WHERE l2.item_code=i.item_code AND l2.is_lent=1 AND l2.is_return=0) THEN 1
                 ELSE 0 END AS is_loan
           FROM item i
           LEFT JOIN mst_location l ON i.location_id=l.location_id
           LEFT JOIN mst_item_status mis ON i.item_status_id=mis.item_status_id
          WHERE i.biblio_id=" . $v_id . " ORDER BY i.call_number ASC, i.item_code ASC LIMIT 200");
}
$avail_count = 0;
foreach ($v_items as $it) { if (empty($it['is_loan'])) $avail_count++; }
$loan_count  = count($v_items) - $avail_count;

/* ═══ 5) TOPIK / SUBJEK ═══ */
$v_topics = is_array($detail['topics'] ?? null) ? $detail['topics'] : [];
if (!$v_topics && $v_id > 0 && Venom_Core::db()) {
    $v_topics = venom_rows("SELECT t.topic FROM biblio_topic bt JOIN mst_topic t ON t.topic_id=bt.topic_id
                             WHERE bt.biblio_id=" . $v_id . " ORDER BY bt.level LIMIT 12");
}
$v_subject_chips = [];
foreach ($v_topics as $t) {
    $v_subject_chips[] = ['name' => $t['topic'], 'url' => 'index.php?subject=' . urlencode($t['topic']) . '&search=Search'];
}
if (!$v_subject_chips && !empty($detail['subjects_html'])) {
    $v_subject_chips = venom_parse_link_chips($detail['subjects_html']);
}

/* ═══ 6) METADATA LENGKAP (paritas dengan template default) ═══ */
$v_extra = [];
foreach ([
    'Series Title'      => (string) ($detail['series_title'] ?? ''),
    'Edition'           => (string) ($detail['edition'] ?? ''),
    'Classification'    => (string) ($detail['classification'] ?? ''),
    'Content Type'      => (string) ($detail['content_type'] ?? ''),
    'Media Type'        => (string) ($detail['media_type'] ?? ''),
    'Carrier Type'      => (string) ($detail['carrier_type'] ?? ''),
    'Statement of Responsibility' => (string) ($detail['sor'] ?? ''),
    'Specific Detail Info'        => (string) ($detail['spec_detail'] ?? ''),
] as $lbl => $val) {
    if (trim(strip_tags($val)) !== '' && trim(strip_tags($val)) !== '-') $v_extra[] = [$lbl, $val];
}
$v_custom = is_array($detail['biblio_custom'] ?? null) ? $detail['biblio_custom'] : [];

/* ═══ 7) LAMPIRAN & RELATED (HTML inti, dirender aman) ═══ */
$v_file_att = (string) ($detail['file_att'] ?? '');
$v_file_att_ok = (trim(strip_tags($v_file_att)) !== '' && stripos($v_file_att, 'No Data') === false && stripos($v_file_att, 'Tidak ada') === false);
$v_related_html = (string) ($detail['related_html'] ?? '');

/* Related grid VENOM (fallback) */
$related = [];
if ($v_id > 0 && !empty($v_topics) && Venom_Core::db()) {
    $topic_ids = [];
    foreach ($v_topics as $t) {
        $r = venom_row("SELECT topic_id FROM mst_topic WHERE topic = '" . Venom_Sec::sql($t['topic']) . "' LIMIT 1");
        if (!empty($r['topic_id'])) $topic_ids[] = (int) $r['topic_id'];
    }
    if ($topic_ids) {
        $ids = implode(',', $topic_ids);
        $related = venom_rows(venom_select_biblio() . " JOIN biblio_topic bt ON bt.biblio_id = b.biblio_id
                    WHERE bt.topic_id IN ($ids) AND b.biblio_id != $v_id AND b.opac_hide = 0
                    GROUP BY b.biblio_id ORDER BY COUNT(bt.topic_id) DESC LIMIT 6");
    }
}
if (empty($related)) $related = venom_featured(6);

$json_flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$e_title = venom_e($v_title);
?>

<div class="v-detail-wrap v-page-transition">
    <nav class="v-breadcrumb" aria-label="Breadcrumb">
        <a href="index.php"><?= venom_icon('home', 14) ?> Beranda</a>
        <span class="v-breadcrumb-sep">/</span>
        <a href="index.php?keywords=&search=Search">Koleksi</a>
        <span class="v-breadcrumb-sep">/</span>
        <span class="v-breadcrumb-current"><?= venom_e(Venom_Sec::text($v_title, 60)) ?></span>
    </nav>

    <div class="v-detail-main">
        <aside class="v-detail-cover">
            <div class="v-detail-cover-inner" data-tilt>
                <img src="<?= venom_e($v_cover) ?>" alt="<?= $e_title ?>" width="600" height="840">
                <span class="v-detail-cover-shine"></span>
                <span class="v-detail-cover-spine"></span>
                <span class="v-detail-cover-badge" style="<?= $avail_count > 0 ? '' : 'background:linear-gradient(120deg,#ef4444,#f97316)' ?>">
                    <?= $avail_count > 0 ? $avail_count . ' Tersedia' : (count($v_items) > 0 ? 'Dipinjam' : 'Stok N/A') ?>
                </span>
            </div>
            <div class="v-detail-actions">
                <a class="v-detail-action bookMarkBook" href="javascript:void(0)" data-id="<?= $v_id ?>">
                    <?= venom_icon('star', 16) ?> Simpan
                </a>
                <button class="v-detail-action v-detail-action-primary" data-modal-open="vnxShareModal">
                    <?= venom_icon('share', 16) ?> Bagikan
                </button>
                <button class="v-detail-action" onclick="window.print()"><?= venom_icon('print', 16) ?> Cetak</button>
            </div>
        </aside>

        <div class="v-detail-info">
            <?php if ($v_gmd !== ''): ?>
                <span class="v-chip v-chip-xs" style="margin-bottom:10px"><?= venom_icon('layers', 12) ?> <?= venom_e($v_gmd) ?></span>
            <?php endif; ?>
            <h1><?= $e_title ?></h1>

            <?php if ($v_author_chips): ?>
            <div class="v-detail-authors">
                <span class="v-detail-authors-label">oleh</span>
                <?php foreach ($v_author_chips as $au): ?>
                    <a class="v-author-btn" href="<?= venom_e($au['url']) ?>"><?= venom_e($au['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <dl class="v-detail-meta">
                <dt>Penerbit</dt><dd class="v-meta-text"><?= venom_e($v_publisher !== '' ? $v_publisher : '—') ?><?= $v_year !== '' ? ', ' . venom_e($v_year) : '' ?></dd>
                <dt>ISBN/ISSN</dt><dd><?= venom_e($v_isbn !== '' ? $v_isbn : '—') ?></dd>
                <dt>No. Panggil</dt><dd class="v-meta-text"><span class="v-detail-callnum"><?= venom_e($v_callnum !== '' ? $v_callnum : '—') ?></span></dd>
                <dt>Deskripsi Fisik</dt><dd class="v-meta-text"><?= venom_e($v_collation !== '' ? $v_collation : '—') ?></dd>
                <?php if ($v_lang !== ''): ?><dt>Bahasa</dt><dd class="v-meta-text"><?= venom_e($v_lang) ?></dd><?php endif; ?>
                <dt>Ketersediaan</dt>
                <dd class="v-meta-text">
                    <?php if (count($v_items) === 0 && !empty($detail['availability_html'])): ?>
                        <span style="color:var(--v-mut);font-weight:600">Lihat tab Eksemplar</span>
                    <?php elseif ($avail_count > 0): ?>
                        <span style="color:var(--v-ok);font-weight:700"><?= $avail_count ?> Tersedia</span>
                        <?php if ($loan_count > 0): ?> · <span style="color:var(--v-err)"><?= $loan_count ?> Dipinjam</span><?php endif; ?>
                    <?php elseif ($loan_count > 0): ?>
                        <span style="color:var(--v-err);font-weight:700">Sedang dipinjam semua</span>
                    <?php else: ?>
                        <span style="color:var(--v-mut);font-weight:600">Belum ada eksemplar</span>
                    <?php endif; ?>
                </dd>
            </dl>

            <?php if ($v_subject_chips): ?>
            <div style="margin-bottom:22px">
                <div style="font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--v-mut);margin-bottom:8px">Subjek</div>
                <div class="v-detail-topics">
                    <?php foreach ($v_subject_chips as $sc): ?>
                        <a href="<?= venom_e($sc['url']) ?>" class="v-detail-topic"><?= venom_e($sc['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="v-detail-tabs" role="tablist">
                <button class="v-detail-tab is-active" data-tab="desc" role="tab"><?= venom_icon('book', 16) ?> Deskripsi</button>
                <button class="v-detail-tab" data-tab="items" role="tab"><?= venom_icon('layers', 16) ?> Eksemplar <span class="v-detail-tab-count"><?= count($v_items) ?></span></button>
                <button class="v-detail-tab" data-tab="cite" role="tab"><?= venom_icon('quote', 16) ?> Sitasi</button>
                <?php if (function_exists('showComment')): ?>
                <button class="v-detail-tab" data-tab="comment" role="tab"><?= venom_icon('chat', 16) ?> Komentar</button>
                <?php endif; ?>
            </div>

            <div class="v-tab-pane is-active" id="tab-desc" role="tabpanel">
                <?php if (trim(strip_tags($v_notes)) !== ''): ?>
                    <div class="v-notes-box">
                        <h4>Abstrak / Sinopsis</h4>
                        <?= nl2br(venom_e($v_notes)) ?>
                    </div>
                <?php else: ?>
                    <p style="color:var(--v-mut)">Belum ada deskripsi atau abstrak untuk koleksi ini.</p>
                <?php endif; ?>

                <?php if ($v_extra || $v_custom): ?>
                <div class="v-extra-meta">
                    <div class="v-extra-title">Informasi Lengkap</div>
                    <dl class="v-detail-meta">
                        <?php foreach ($v_extra as $ex): ?>
                            <dt><?= venom_e($ex[0]) ?></dt><dd class="v-meta-text"><?= venom_e($ex[1]) ?></dd>
                        <?php endforeach; ?>
                        <?php foreach ($v_custom as $item): ?>
                            <dt><?= venom_e($item['label'] ?? '') ?></dt><dd class="v-meta-text"><?= venom_e($item['value'] ?? '-') ?></dd>
                        <?php endforeach; ?>
                    </dl>
                </div>
                <?php endif; ?>

                <?php if ($v_file_att_ok): ?>
                <div class="v-extra-meta">
                    <div class="v-extra-title">Lampiran Berkas</div>
                    <div class="v-core-html"><?= $v_file_att ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="v-tab-pane" id="tab-items" role="tabpanel">
                <?php if (!empty($v_items)): ?>
                    <table class="v-items-table">
                        <thead><tr><th>Kode</th><th>No. Panggil</th><th>Lokasi</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($v_items as $it): $is_avail = empty($it['is_loan']); ?>
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
                <?php elseif (!empty($detail['availability_html'])): ?>
                    <div class="v-core-html"><?= $detail['availability_html'] ?></div>
                <?php else: ?>
                    <p style="color:var(--v-mut)">Belum ada data eksemplar.</p>
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

            <?php if (function_exists('showComment')): ?>
            <div class="v-tab-pane" id="tab-comment" role="tabpanel">
                <div id="comment"><?= showComment($v_id) ?></div>
                <?php if (!isset($_SESSION['mid']) && !empty($sysconf['comment']['enable'])): ?>
                    <hr style="border:0;border-top:1px solid var(--v-line);margin:18px 0">
                    <a href="index.php?p=member" class="v-btn v-btn-ghost v-btn-sm">Masuk untuk menulis komentar</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($related) || trim(strip_tags($v_related_html)) !== ''): ?>
    <section class="v-related">
        <div class="v-related-head">
            <h2><?= venom_icon('sparkles', 22) ?> Koleksi Terkait</h2>
            <a href="index.php?keywords=<?= urlencode($v_title) ?>&search=Search" class="v-btn v-btn-ghost v-btn-sm">Lihat Semua <?= venom_icon('right', 15) ?></a>
        </div>
        <?php if (!empty($related)): ?>
        <div class="v-related-grid">
            <?php foreach ($related as $rb): ?>
                <?php if (function_exists('venom_card_biblio')): ?>
                    <?= venom_card_biblio($rb, !empty($rb['promoted']) ? 'UNGGULAN' : '') ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="v-core-html"><?= $v_related_html ?></div>
        <?php endif; ?>
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

    var data = {
        title: <?= json_encode($v_title, $json_flags) ?>,
        author: <?= json_encode($v_author_plain !== '' ? $v_author_plain : 'Tanpa Penulis', $json_flags) ?>,
        year: <?= json_encode($v_year !== '' ? $v_year : 'n.d.', $json_flags) ?>,
        publisher: <?= json_encode($v_publisher !== '' ? $v_publisher : 'Tanpa Penerbit', $json_flags) ?>,
        place: <?= json_encode($v_place !== '' ? $v_place : '', $json_flags) ?>,
        isbn: <?= json_encode($v_isbn, $json_flags) ?>
    };
    var sel = document.getElementById('vCiteStyle');
    var out = document.getElementById('vCiteOut');
    function e(s){ return (s+'').replace(/&/g,'&amp;').replace(/</g,'&lt;'); }
    function fmt(){
        var st = sel.value, a = e(data.author), t = e(data.title), y = e(data.year), p = e(data.publisher), pl = e(data.place), h = '';
        if (st === 'apa')            h = a + ' (' + y + '). <em>' + t + '</em>. ' + p + '.';
        else if (st === 'mla')       h = a + '. <em>' + t + '</em>. ' + p + ', ' + y + '.';
        else if (st === 'chicago')   h = a + '. <em>' + t + '</em>. ' + (pl ? pl + ': ' : '') + p + ', ' + y + '.';
        else                         h = a + '. <em>' + t + '</em>. ' + (pl ? pl + ': ' : '') + p + ', ' + y + '.';
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