<?php
/**
 * VENOM · Biblio List Template (FINAL v2 — 3 view mode + availability akurat)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

function biblio_list_format($dbs, $biblio_detail, $n, $settings = array(), &$return_back = array()) {
    global $sysconf;
    $output = '';

    $title     = stripslashes((string) ($biblio_detail['title'] ?? 'Tanpa Judul'));
    $biblio_id = (int) ($biblio_detail['biblio_id'] ?? 0);
    $kw        = urlencode((string) ($settings['keywords'] ?? ''));
    $detail_url = 'index.php?p=show_detail&id=' . $biblio_id . ($kw !== '' ? '&keywords=' . $kw : '');
    $cite_url   = 'index.php?p=cite&id=' . $biblio_id . ($kw !== '' ? '&keywords=' . $kw : '');

    $cover_url = venom_cover($biblio_detail['image'] ?? '', $title);
    $avail     = venom_items_availability($dbs, $biblio_id);
    $availability = $avail['available'];

    /* Chip metadata dari custom_frontpage_record */
    $v_meta = [];
    if (!empty($settings['enable_custom_frontpage']) && !empty($settings['custom_fields']) && is_array($settings['custom_fields'])) {
        foreach ($settings['custom_fields'] as $field => $field_opts) {
            if (isset($field_opts[0]) && $field_opts[0] == 1) {
                $val = trim(strip_tags((string) ($biblio_detail[$field] ?? '')));
                $v_meta[] = [(string) ($field_opts[1] ?? $field), ($val !== '' ? $val : '-')];
            }
        }
    }

    /* Collection type */
    $v_colls = [];
    $cq = $dbs->query('SELECT DISTINCT t.coll_type_name FROM item i LEFT JOIN mst_coll_type t ON t.coll_type_id=i.coll_type_id WHERE i.biblio_id=' . $biblio_id);
    if ($cq) { while ($cr = $cq->fetch_row()) { if (!empty($cr[0])) $v_colls[] = $cr[0]; } }

    $v_gmd   = trim((string) ($biblio_detail['gmd'] ?? ''));
    $v_notes = trim(strip_tags((string) getNotes($dbs, $biblio_id)));
    $v_year  = trim((string) ($biblio_detail['publish_year'] ?? ''));

    /* Penulis */
    $_authors = $biblio_detail['author'] ?? (class_exists('biblio_list_model') ? biblio_list_model::getAuthors($dbs, $biblio_id, true) : '');
    $_authors_string = ''; $_authors_plain = '-';
    if ($_authors) {
        $names = [];
        $authors_arr = is_array($_authors) ? $_authors : explode('-', $_authors);
        foreach ($authors_arr as $a) {
            $a = trim((string) $a);
            if ($a === '') continue;
            $names[] = $a;
            $_authors_string .= '<a class="v-author-chip" href="index.php?author=' . urlencode($a) . '&search=Search">' . venom_e($a) . '</a>';
        }
        if ($names) $_authors_plain = implode('; ', $names);
    }

    $v_badge = ($availability < 1)
        ? '<span class="v-book-badge v-badge-no">' . venom_icon('x', 12) . ' Habis</span>'
        : '<span class="v-book-badge v-badge-ok">' . venom_icon('check', 12) . ' ' . $availability . '</span>';

    $v_meta_html = '';
    foreach ($v_meta as $m) {
        $empty = ($m[1] === '-');
        $v_meta_html .= '<span class="v-meta-i' . ($empty ? ' empty' : '') . '"><em>' . venom_e($m[0]) . '</em><b>' . venom_e($m[1]) . '</b></span>';
    }

    $v_tags_html = '';
    if ($v_gmd !== '') $v_tags_html .= '<span class="v-tag">' . venom_icon('layers', 12) . venom_e($v_gmd) . '</span>';
    foreach ($v_colls as $c) $v_tags_html .= '<span class="v-tag plain">' . venom_icon('tag', 12) . venom_e($c) . '</span>';

    /* Aksi inti: keranjang + sitasi + MARC */
    $v_actions  = '<div class="v-book-actions">';
    $v_actions .= '<a class="v-act-btn add-to-chart-button" data-biblio="' . $biblio_id . '" title="Keranjang">' . venom_icon('plus', 16) . '</a>';
    $v_actions .= '<a class="v-act-btn openPopUp citationLink" href="' . $cite_url . '" target="_blank" title="Sitasi">' . venom_icon('quote', 16) . '</a>';
    $v_actions .= '<a class="v-act-btn" href="' . $detail_url . '&MARC=true" target="_blank" title="Unduh MARC">' . venom_icon('code', 16) . '</a>';
    $v_actions .= '</div>';

    /* Aksi cepat: komentar + bookmark + salin tautan (data-copy = JS venom-app) */
    $comment_url = (class_exists('utility') && method_exists('utility', 'isMemberLogin') && utility::isMemberLogin())
        ? 'index.php?p=show_detail&id=' . $biblio_id . '#comment'
        : 'index.php?p=member';
    $v_quick  = '<div class="v-quick-actions">';
    $v_quick .= '<a class="v-qa" href="' . $comment_url . '">' . venom_icon('chat', 14) . ' Komentar</a>';
    $v_quick .= '<a class="v-qa bookMarkBook" href="index.php?p=member&sec=bookmark" data-id="' . $biblio_id . '">' . venom_icon('star', 14) . ' Simpan</a>';
    $v_quick .= '<a class="v-qa" href="javascript:void(0)" data-copy="' . venom_e($detail_url) . '">' . venom_icon('share', 14) . ' Salin Tautan</a>';
    $v_quick .= '</div>';

    $view_mode = $_POST['view'] ?? $_GET['view'] ?? $_SESSION['LIST_VIEW'] ?? 'grid';
    if (!in_array($view_mode, ['simple', 'list', 'grid'], true)) $view_mode = 'grid';

    /* ═══════ MODE SIMPLE (adopsi rasamala: baris kompak + popover eksemplar) ═══════ */
    if ($view_mode === 'simple'):
        $rows = '';
        foreach ($avail['items'] as $it) {
            $ok = ((int) $it['is_available'] === 1);
            $rows .= '<tr><td>' . venom_e($it['item_code'] ?? '-') . '</td><td>' . venom_e($it['call_number'] ?? '-') . '</td><td>' . venom_e($it['location_name'] ?? '-') . '</td><td>' . ($ok ? '<span class="ok-i">✔ Tersedia</span>' : '<span class="no-i">✘ Dipinjam</span>') . '</td></tr>';
        }
        $output .= '<article class="v-simple-item">';
        $output .= '<div class="v-simple-main"><a class="v-simple-title" href="' . $detail_url . '">' . venom_e(addEllipsis($title, 140)) . '</a><div class="v-simple-author">' . venom_e($_authors_plain) . ($v_year !== '' ? ' · ' . venom_e($v_year) : '') . '</div></div>';
        $output .= '<div class="v-avail-wrap">';
        $output .= '<button type="button" class="v-avail-badge ' . ($availability > 0 ? 'ok' : 'no') . '" title="' . ($availability > 0 ? 'Tersedia' : 'Tidak tersedia') . '">' . venom_icon($availability > 0 ? 'check' : 'x', 14) . '<span>' . $availability . '</span></button>';
        if ($rows !== '') {
            $output .= '<div class="v-avail-popover"><table><thead><tr><th>Kode</th><th>No. Panggil</th><th>Lokasi</th><th>Status</th></tr></thead><tbody>' . $rows . '</tbody></table></div>';
        }
        $output .= '</div></article>';

    /* ═══════ MODE LIST ═══════ */
    elseif ($view_mode === 'list'):
        $output .= '<article class="v-book v-book-list">';
        $output .= '<a class="v-book-cover" href="' . $detail_url . '"><img loading="lazy" src="' . venom_e($cover_url) . '" alt="cover">' . $v_badge . '</a>';
        $output .= '<div class="v-book-body">';
        $output .= '<h3 class="v-book-title"><a href="' . $detail_url . '">' . venom_e(addEllipsis($title, 120)) . '</a></h3>';
        $output .= '<div class="v-book-author">' . $_authors_string . ($v_year !== '' ? '<span class="v-year"><i>' . venom_icon('clock', 12) . '</i>' . venom_e($v_year) . '</span>' : '') . '</div>';
        $output .= $v_quick;
        if ($v_meta_html !== '') $output .= '<div class="v-book-meta-chips">' . $v_meta_html . '</div>';
        if ($v_notes !== '') $output .= '<p class="v-notes-x">' . venom_e(addEllipsis($v_notes, 220)) . '</p>';
        if ($v_tags_html !== '') $output .= '<div class="v-tags">' . $v_tags_html . '</div>';
        $output .= '</div>';
        $output .= '<div class="v-book-side">';
        $output .= '<div class="v-avail-box ' . ($availability > 0 ? 'ok' : 'no') . '"><span>Ketersediaan</span><b>' . $availability . '/' . $avail['total'] . '</b></div>';
        $output .= $v_actions;
        $output .= '</div></article>';

    /* ═══════ MODE GRID ═══════ */
    else:
        $output .= '<article class="v-book">';
        $output .= '<a class="v-book-cover" href="' . $detail_url . '"><img loading="lazy" src="' . venom_e($cover_url) . '" alt="cover" decoding="async"><span class="v-book-shine"></span>' . $v_badge . '<span class="v-book-hover">' . venom_icon('right', 18) . ' Lihat Detail</span></a>';
        $output .= '<div class="v-book-body">';
        $output .= '<h3 class="v-book-title"><a href="' . $detail_url . '">' . venom_e(addEllipsis($title, 80)) . '</a></h3>';
        $output .= '<div class="v-book-author">' . $_authors_string . '</div>';
        if ($v_tags_html !== '') $output .= '<div class="v-tags">' . $v_tags_html . '</div>';
        $output .= '<div class="v-book-foot"><span class="v-chip v-chip-xs">' . venom_e($v_year !== '' ? $v_year : '—') . '</span>' . $v_actions . '</div>';
        $output .= '</div></article>';
    endif;

    return $output;
}

/* ── Ketersediaan AKURAT (menghormati status no_loan, cache per-request) ── */
if (!function_exists('venom_items_availability')) {
    function venom_items_availability($dbs, $biblio_id) {
        static $cache = [];
        $id = (int) $biblio_id;
        if ($id < 1) return ['items' => [], 'total' => 0, 'available' => 0];
        if (isset($cache[$id])) return $cache[$id];
        $out = ['items' => [], 'total' => 0, 'available' => 0];
        $sql = "SELECT i.item_code, i.call_number, ml.location_name,
                       CASE WHEN IFNULL(mis.no_loan,0)=1 THEN 0
                            WHEN EXISTS(SELECT 1 FROM loan l WHERE l.item_code=i.item_code AND l.is_lent=1 AND l.is_return=0) THEN 0
                            ELSE 1 END AS is_available
                  FROM item i
                  LEFT JOIN mst_location ml ON i.location_id=ml.location_id
                  LEFT JOIN mst_item_status mis ON i.item_status_id=mis.item_status_id
                 WHERE i.biblio_id=" . $id . " ORDER BY i.call_number ASC, i.item_code ASC";
        $q = $dbs->query($sql);
        if ($q) {
            while ($r = $q->fetch_assoc()) {
                $r['is_available'] = (int) $r['is_available'];
                $out['items'][] = $r; $out['total']++;
                if ($r['is_available'] > 0) $out['available']++;
            }
        }
        $cache[$id] = $out;
        return $out;
    }
}

if (!function_exists('getNotes')) {
    function getNotes($dbs, $biblio_id) {
        $query = $dbs->query('SELECT notes FROM biblio WHERE biblio_id = ' . (int)$biblio_id);
        $data = $query ? $query->fetch_row() : null;
        return $data ? addEllipsis((string) $data[0], 400) : '';
    }
}
if (!function_exists('addEllipsis')) {
    function addEllipsis($string, $length, $end = '…') {
        if (strlen($string ?? '') > $length) {
            $length -= strlen($end);
            $string  = mb_substr($string, 0, $length, 'UTF-8');
            $string .= $end;
        }
        return $string;
    }
}
if (!function_exists('getAvailability')) {
    function getAvailability($dbs, $biblio_id, $sysconf) {
        $a = venom_items_availability($dbs, $biblio_id);
        return $a['available'];
    }
}