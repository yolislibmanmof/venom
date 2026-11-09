<?php
/**
 * VENOM · Biblio List Template (FINAL — Semua Fitur Referensi + VENOM Style)
 * Fitur: List/Grid/Simple modes, Collection Types tags, 3D Tilt grid, Stagger animation
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

function biblio_list_format($dbs, $biblio_detail, $n, $settings = array(), &$return_back = array()) {
    global $sysconf;
    $output = '';

    $title      = stripslashes((string) ($biblio_detail['title'] ?? 'Tanpa Judul'));
    $biblio_id  = (int) ($biblio_detail['biblio_id'] ?? 0);
    $kw         = urlencode((string) ($settings['keywords'] ?? ''));
    $detail_url = 'index.php?p=show_detail&id=' . $biblio_id . ($kw !== '' ? '&keywords=' . $kw : '');
    $cite_url   = 'index.php?p=cite&id=' . $biblio_id . ($kw !== '' ? '&keywords=' . $kw : '');

    $cover_url = venom_cover($biblio_detail['image'] ?? '', $title);
    $avail     = venom_items_availability($dbs, $biblio_id);
    $availability = $avail['available'];

    /* Custom fields (Edition, ISBN, Collation, Series, Call Number) */
    $v_meta = [];
    if (!empty($settings['enable_custom_frontpage']) && !empty($settings['custom_fields']) && is_array($settings['custom_fields'])) {
        foreach ($settings['custom_fields'] as $field => $field_opts) {
            if (isset($field_opts[0]) && $field_opts[0] == 1) {
                $val = trim(strip_tags((string) ($biblio_detail[$field] ?? '')));
                $v_meta[] = [(string) ($field_opts[1] ?? $field), ($val !== '' ? $val : '-')];
            }
        }
    }

    /* Penulis */
    $_authors = $biblio_detail['author'] ?? (class_exists('biblio_list_model') ? biblio_list_model::getAuthors($dbs, $biblio_id, true) : '');
    $_authors_string = '';
    $_authors_plain = '-';
    if ($_authors) {
        $names = [];
        $authors_arr = is_array($_authors) ? $_authors : explode('-', $_authors);
        foreach ($authors_arr as $a) {
            $a = trim((string) $a);
            if ($a === '') continue;
            $names[] = $a;
            $_authors_string .= '<a class="v-author-btn" href="index.php?author=' . urlencode($a) . '&search=Search">' . venom_e($a) . '</a>';
        }
        if ($names) $_authors_plain = implode('; ', $names);
    }

    /* Notes */
    $v_notes = trim(strip_tags((string) getNotes($dbs, $biblio_id)));

    $v_year = trim((string) ($biblio_detail['publish_year'] ?? ''));
    $v_gmd  = trim((string) ($biblio_detail['gmd'] ?? ''));

    /* Collection Types (BARU - dari referensi) */
    $v_colltypes = [];
    $coll_q = $dbs->query('SELECT DISTINCT t.coll_type_name FROM item i LEFT JOIN mst_coll_type t ON t.coll_type_id=i.coll_type_id WHERE i.biblio_id=' . $biblio_id);
    if ($coll_q) {
        while ($row = $coll_q->fetch_assoc()) {
            if (!empty($row['coll_type_name'])) $v_colltypes[] = $row['coll_type_name'];
        }
    }

    /* Status badge untuk cover */
    $cover_badge = ($availability < 1)
        ? '<span class="v-book-badge v-badge-no">' . venom_icon('x', 12) . ' Habis</span>'
        : '<span class="v-book-badge v-badge-ok">' . venom_icon('check', 12) . ' ' . $availability . ' Tersedia</span>';

    /* Bookmark state */
    $is_bookmarked = isset($_SESSION['bookmark']) && in_array($biblio_id, $_SESSION['bookmark']);
    $bookmark_label = $is_bookmarked ? 'Tersimpan' : 'Simpan';
    $bookmark_class = $is_bookmarked ? ' is-bookmarked' : '';

    /* Comment URL */
    $comment_url = (class_exists('utility') && method_exists('utility', 'isMemberLogin') && utility::isMemberLogin())
        ? 'index.php?p=show_detail&id=' . $biblio_id . '#comment'
        : 'index.php?p=member';

    $view_mode = $_POST['view'] ?? $_GET['view'] ?? $_SESSION['LIST_VIEW'] ?? 'list';
    if (!in_array($view_mode, ['simple', 'list', 'grid'], true)) $view_mode = 'list';

    /* Stagger animation delay */
    $delay = min((($n % 8) * 70), 560);

    /* ═══════════════════════════════════════════════════════
       MODE LIST (default)
       Cover(col-2) | Body(col-8) | Availability+Actions(col-2)
       ═══════════════════════════════════════════════════════ */
    if ($view_mode === 'list'):
        $output .= '<article class="v-result-card v-card" style="animation-delay:' . $delay . 'ms">';
        $output .= '<div class="v-result-grid">';

        /* KOLOM KIRI: Cover */
        $output .= '<div class="v-result-cover">';
        $output .= '<a href="' . venom_e($detail_url) . '" title="' . venom_e($title) . '">';
        $output .= '<img loading="lazy" src="' . venom_e($cover_url) . '" alt="cover" decoding="async">';
        $output .= '<span class="v-book-shine"></span>';
        $output .= $cover_badge;
        $output .= '</a></div>';

        /* KOLOM TENGAH: Body */
        $output .= '<div class="v-result-body">';
        $output .= '<h3 class="v-result-title"><a href="' . venom_e($detail_url) . '">' . venom_e(addEllipsis($title, 120)) . '</a></h3>';

        /* Baris aksi cepat: Comment / Bookmark / Share */
        $output .= '<div class="v-result-quick-actions">';
        $output .= '<a class="v-qa-btn" href="' . $comment_url . '">' . venom_icon('chat', 14) . ' Komentar</a>';
        $output .= '<a class="v-qa-btn bookMarkBook' . $bookmark_class . '" href="javascript:void(0)" data-id="' . $biblio_id . '">'
                 . venom_icon('star', 14) . ' <span>' . $bookmark_label . '</span></a>';
        $output .= '<a class="v-qa-btn" href="javascript:void(0)" data-modal-open="vnxShareModal" data-share-url="' . venom_e($detail_url) . '" data-share-title="' . venom_e(addEllipsis($title, 60)) . '">'
                 . venom_icon('share', 14) . ' Bagikan</a>';
        $output .= '</div>';

        /* Chip author */
        if ($_authors_string !== '') {
            $output .= '<div class="v-result-authors">' . $_authors_string . '</div>';
        }

        /* Notes */
        if ($v_notes !== '') {
            $output .= '<p class="v-result-notes">' . venom_e(addEllipsis($v_notes, 240)) . '</p>';
        }

        /* Tags: GMD + Collection Types (BARU - dari referensi) */
        $tags_html = '';
        if ($v_gmd !== '') {
            $tags_html .= '<span class="v-book-tag">' . venom_icon('file', 12) . ' ' . venom_e($v_gmd) . '</span>';
        }
        foreach ($v_colltypes as $ct) {
            $tags_html .= '<span class="v-book-tag v-book-tag-plain">' . venom_icon('folder', 12) . ' ' . venom_e($ct) . '</span>';
        }
        if ($tags_html !== '') {
            $output .= '<div class="v-book-tags">' . $tags_html . '</div>';
        }

        /* Custom fields (DL row) */
        if (!empty($v_meta)) {
            $output .= '<dl class="v-result-meta">';
            foreach ($v_meta as $m) {
                $empty = ($m[1] === '-');
                $output .= '<dt>' . venom_e($m[0]) . '</dt>';
                $output .= '<dd class="' . ($empty ? 'is-empty' : '') . '">' . venom_e($m[1]) . '</dd>';
            }
            $output .= '</dl>';
        }

        $output .= '</div>'; /* close body */

        /* KOLOM KANAN: Availability + Actions */
        $output .= '<div class="v-result-side">';
        $output .= '<div class="v-avail-card ' . ($availability > 0 ? 'ok' : 'no') . '">';
        $output .= '<span class="v-avail-label">Ketersediaan</span>';
        $output .= '<span class="v-avail-value">' . $availability . '</span>';
        $output .= '</div>';
        $output .= '<a class="v-side-btn add-to-chart-button" data-biblio="' . $biblio_id . '" title="Keranjang">'
                 . venom_icon('plus', 16) . ' <span>Keranjang</span></a>';
        $output .= '<a class="v-side-btn openPopUp citationLink" href="' . venom_e($cite_url) . '" target="_blank" title="Sitasi">'
                 . venom_icon('quote', 16) . ' <span>Sitasi</span></a>';
        $output .= '<a class="v-side-btn" href="' . venom_e($detail_url) . '&MARC=true" target="_blank" title="Unduh MARC">'
                 . venom_icon('code', 16) . ' <span>MARC</span></a>';
        $output .= '</div>'; /* close side */

        $output .= '</div>'; /* close grid */
        $output .= '</article>';

    /* ═══════ MODE SIMPLE ═══════ */
    elseif ($view_mode === 'simple'):
        $rows = '';
        foreach ($avail['items'] as $it) {
            $ok = ((int) $it['is_available'] === 1);
            $rows .= '<tr><td>' . venom_e($it['item_code'] ?? '-') . '</td><td>' . venom_e($it['call_number'] ?? '-') . '</td><td>' . venom_e($it['location_name'] ?? '-') . '</td><td>' . ($ok ? '<span class="ok-i">✔ Tersedia</span>' : '<span class="no-i">✘ Dipinjam</span>') . '</td></tr>';
        }
        $output .= '<article class="v-simple-item" style="animation-delay:' . $delay . 'ms">';
        $output .= '<div class="v-simple-main"><a class="v-simple-title" href="' . venom_e($detail_url) . '">' . venom_e(addEllipsis($title, 140)) . '</a><div class="v-simple-author">' . venom_e($_authors_plain) . ($v_year !== '' ? ' · ' . venom_e($v_year) : '') . '</div></div>';
        $output .= '<div class="v-avail-wrap">';
        $output .= '<button type="button" class="v-avail-badge ' . ($availability > 0 ? 'ok' : 'no') . '">' . venom_icon($availability > 0 ? 'check' : 'x', 14) . '<span>' . $availability . '</span></button>';
        if ($rows !== '') {
            $output .= '<div class="v-avail-popover"><table><thead><tr><th>Kode</th><th>No. Panggil</th><th>Lokasi</th><th>Status</th></tr></thead><tbody>' . $rows . '</tbody></table></div>';
        }
        $output .= '</div></article>';

    /* ═══════ MODE GRID (dengan 3D Tilt + Dropdown Menu 3 Dots - dari referensi) ═══════ */
    else:
        $output .= '<article class="v-book" data-tilt data-tilt-max="4" data-tilt-speed="300" data-tilt-glare data-tilt-max-glare="0.12" data-tilt-scale="1.01" style="animation-delay:' . $delay . 'ms">';
        
        /* Dropdown menu 3 dots (BARU - dari referensi) */
        $output .= '<div class="v-book-grid-menu">';
        $output .= '<button type="button" class="v-book-grid-menu-btn" data-dd-toggle="grid-menu-' . $biblio_id . '">';
        $output .= '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/></svg>';
        $output .= '</button>';
        $output .= '<div class="v-book-grid-menu-dropdown" id="grid-menu-' . $biblio_id . '">';
        $output .= '<a class="add-to-chart-button" data-biblio="' . $biblio_id . '" href="javascript:void(0)">' . venom_icon('plus', 14) . ' Keranjang</a>';
        $output .= '<a class="openPopUp citationLink" href="' . venom_e($cite_url) . '" target="_blank">' . venom_icon('quote', 14) . ' Sitasi</a>';
        $output .= '<div class="v-book-grid-menu-divider"></div>';
        $output .= '<a href="' . venom_e($detail_url) . '&MARC=true" target="_blank">' . venom_icon('code', 14) . ' MARC</a>';
        $output .= '</div></div>';

        $output .= '<a class="v-book-cover" href="' . venom_e($detail_url) . '"><img loading="lazy" src="' . venom_e($cover_url) . '" alt="cover" decoding="async"><span class="v-book-shine"></span>' . $cover_badge . '<span class="v-book-hover">' . venom_icon('right', 18) . ' Lihat Detail</span></a>';
        $output .= '<div class="v-book-body">';
        $output .= '<h3 class="v-book-title"><a href="' . venom_e($detail_url) . '">' . venom_e(addEllipsis($title, 80)) . '</a></h3>';
        $output .= '<div class="v-book-author">' . venom_e($_authors_plain) . '</div>';
        $output .= '<div class="v-book-foot"><span class="v-chip v-chip-xs">' . venom_e($v_year !== '' ? $v_year : '—') . '</span>';
        if ($v_gmd !== '') {
            $output .= '<span class="v-chip v-chip-xs v-chip-plain">' . venom_e($v_gmd) . '</span>';
        }
        $output .= '</div>';
        $output .= '</div></article>';
    endif;

    return $output;
}

/* Helper functions (pertahankan dari v2) */
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