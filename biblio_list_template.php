<?php
/**
 * VENOM · Biblio List Template
 * Format bibliographic item list for OPAC display
 * Diadaptasi dari pustaka_ceria untuk kompatibilitas logika 100%
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

function biblio_list_format($dbs, $biblio_detail, $n, $settings = array(), &$return_back = array()) {
    global $sysconf;
    $output = '';

    $title     = $biblio_detail['title'] ?? 'Tanpa Judul';
    $biblio_id = (int) ($biblio_detail['biblio_id'] ?? 0);
    $detail_url = 'index.php?p=show_detail&id=' . $biblio_id;
    $cite_url   = 'index.php?p=cite&id=' . $biblio_id;

    $cover_url = venom_cover($biblio_detail['image'] ?? '', $title);
    $availability = getAvailability($dbs, $biblio_id, $sysconf);
    $is_promoted = !empty($biblio_detail['promoted']) || !empty($biblio_detail['labels']);

    $_authors = $biblio_detail['author'] ?? 'Penulis tidak diketahui';
    $_authors_string = '';
    if ($_authors) {
        $authors_arr = is_array($_authors) ? $_authors : explode('-', $_authors);
        foreach ($authors_arr as $a) {
            $a = trim($a);
            if ($a !== '') {
                $_authors_string .= '<a href="index.php?author=' . urlencode($a) . '&search=Search" style="color:var(--v-p);text-decoration:none;">' . venom_e($a) . '</a> ';
            }
        }
    }

    $view_mode = $_POST['view'] ?? $_SESSION['LIST_VIEW'] ?? 'grid';

    if ($view_mode === 'list'):
        $year = trim($biblio_detail['publish_year'] ?? '');
        $publisher = trim($biblio_detail['publisher'] ?? '');
        
        $output .= '<article class="v-book v-book-list">';
        $output .= '<a class="v-book-cover" href="' . $detail_url . '">';
        $output .= '<img loading="lazy" src="' . venom_e($cover_url) . '" alt="cover">';
        if ($availability < 1) {
            $output .= '<span class="v-book-badge" style="background:linear-gradient(120deg,#ef4444,#f97316)">Habis</span>';
        } elseif ($is_promoted) {
            $output .= '<span class="v-book-badge">UNGGULAN</span>';
        }
        $output .= '</a>';
        
        $output .= '<div class="v-book-body">';
        $output .= '<h3 class="v-book-title"><a href="' . $detail_url . '">' . venom_e(addEllipsis($title, 120)) . '</a></h3>';
        $output .= '<p class="v-book-author">' . $_authors_string . ($year !== '' ? '<span style="color:var(--v-mut);font-size:0.85rem;">(' . venom_e($year) . ')</span>' : '') . '</p>';
        
        $output .= '<div class="v-book-meta">';
        if ($publisher !== '') $output .= '<span class="v-book-pub">' . venom_e($publisher) . '</span>';
        $avail_class = ($availability > 0) ? 'background:color-mix(in srgb,var(--v-ok) 15%,transparent);color:var(--v-ok)' : 'background:color-mix(in srgb,var(--v-err) 15%,transparent);color:var(--v-err)';
        $avail_text = ($availability > 0) ? 'Tersedia: ' . $availability : 'Dipinjam Semua';
        $output .= '<span class="v-chip v-chip-xs" style="margin-left:auto; ' . $avail_class . '">' . $avail_text . '</span>';
        $output .= '</div></div></article>';

    else:
        $year = trim($biblio_detail['publish_year'] ?? '—');
        $publisher = trim($biblio_detail['publisher'] ?? '');

        $output .= '<article class="v-book">';
        $output .= '<a class="v-book-cover" href="' . $detail_url . '">';
        $output .= '<img loading="lazy" src="' . venom_e($cover_url) . '" alt="cover" decoding="async">';
        $output .= '<span class="v-book-shine"></span>';
        if ($availability < 1) {
            $output .= '<span class="v-book-badge" style="background:linear-gradient(120deg,#ef4444,#f97316)">Habis</span>';
        } elseif ($is_promoted) {
            $output .= '<span class="v-book-badge">UNGGULAN</span>';
        }
        $output .= '<span class="v-book-hover">' . venom_icon('right', 18) . ' Lihat Detail</span>';
        $output .= '</a>';
        
        $output .= '<div class="v-book-body">';
        $output .= '<h3 class="v-book-title"><a href="' . $detail_url . '">' . venom_e(addEllipsis($title, 80)) . '</a></h3>';
        $output .= '<p class="v-book-author">' . $_authors_string . '</p>';
        $output .= '<div class="v-book-meta">';
        $output .= '<span class="v-chip v-chip-xs">' . venom_e($year) . '</span>';
        if ($publisher !== '') {
            $output .= '<span class="v-book-pub">' . venom_e($publisher) . '</span>';
        }
        $output .= '</div></div></article>';
    endif;

    return $output;
}

// --- HELPER FUNCTIONS (Dibungkus function_exists agar aman dari Fatal Error) ---
if (!function_exists('getNotes')) {
    function getNotes($dbs, $biblio_id) {
        $query = $dbs->query('SELECT notes FROM biblio WHERE biblio_id = ' . (int)$biblio_id);
        $data = $query ? $query->fetch_row() : null;
        return $data ? addEllipsis($data[0], 400) : '';
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
        $_item_q = $dbs->query('SELECT COUNT(*) FROM item WHERE biblio_id=' . (int)$biblio_id);
        $_item_c = $_item_q ? $_item_q->fetch_row() : [0];
        
        $_borrowed_q = $dbs->query('SELECT COUNT(*) FROM loan AS l INNER JOIN item AS i ON l.item_code=i.item_code WHERE l.is_lent=1 AND l.is_return=0 AND i.biblio_id=' . (int)$biblio_id);
        $_borrowed_c = $_borrowed_q ? $_borrowed_q->fetch_row() : [0];
        
        return ($_item_c[0] ?? 0) - ($_borrowed_c[0] ?? 0);
    }
}