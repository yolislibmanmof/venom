<?php
/**
 * VENOM · UI Components.
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

function venom_section_head($icon, $title, $sub = '', $link = '', $linkText = 'Lihat semua')
{
    $h  = '<div class="v-sec-head v-reveal"><div class="v-sec-title">';
    $h .= '<span class="v-sec-icon">' . venom_icon($icon, 20) . '</span><div>';
    $h .= '<h2>' . venom_e($title) . '</h2>';
    if ($sub !== '') $h .= '<p>' . venom_e($sub) . '</p>';
    $h .= '</div></div>';
    if ($link !== '') {
        $h .= '<a class="v-btn v-btn-ghost v-btn-sm" href="' . venom_u($link) . '">'
            . venom_e($linkText) . venom_icon('right', 16) . '</a>';
    }
    return $h . '</div>';
}

function venom_card_biblio($row, $badge = '')
{
    $id     = isset($row['biblio_id']) ? (int) $row['biblio_id'] : 0;
    $title  = isset($row['title']) ? $row['title'] : 'Tanpa Judul';
    $author = !empty($row['authors']) ? $row['authors'] : 'Penulis tidak diketahui';
    $year   = !empty($row['publish_year']) ? $row['publish_year'] : '—';
    $url    = venom_detail_url($id);

    $h  = '<article class="v-book v-reveal" data-tilt>';
    $h .= '<a class="v-book-cover" href="' . venom_u($url) . '" aria-label="' . venom_e($title) . '">';
    $h .= '<img src="' . venom_e(venom_cover(isset($row['image']) ? $row['image'] : '', $title)) . '" '
        . 'alt="" loading="lazy" decoding="async" width="300" height="420">';
    $h .= '<span class="v-book-shine"></span>';
    if ($badge !== '') $h .= '<span class="v-book-badge">' . venom_e($badge) . '</span>';
    $h .= '<span class="v-book-hover">' . venom_icon('right', 18) . ' Detail</span>';
    $h .= '</a><div class="v-book-body">';
    $h .= '<h3 class="v-book-title"><a href="' . venom_u($url) . '">' . venom_e($title) . '</a></h3>';
    $h .= '<p class="v-book-author">' . venom_e($author) . '</p>';
    $h .= '<div class="v-book-meta"><span class="v-chip v-chip-xs">' . venom_e($year) . '</span>';
    if (!empty($row['publisher_name'])) {
        $h .= '<span class="v-book-pub">' . venom_e($row['publisher_name']) . '</span>';
    }
    return $h . '</div></div></article>';
}

function venom_stat_card($icon, $value, $label, $tone = 'p')
{
    return '<div class="v-stat v-reveal" data-tone="' . venom_e($tone) . '">'
         . '<span class="v-stat-icon">' . venom_icon($icon, 22) . '</span>'
         . '<strong class="v-count" data-to="' . (int) $value . '">' . venom_num($value) . '</strong>'
         . '<span class="v-stat-label">' . venom_e($label) . '</span></div>';
}

function venom_empty($text = 'Belum ada data untuk ditampilkan.')
{
    return '<div class="v-empty v-reveal">' . venom_icon('layers', 34)
         . '<p>' . venom_e($text) . '</p></div>';
}

function venom_book_grid(array $rows, $badge = '')
{
    if (!$rows) return venom_empty();
    $h = '<div class="v-grid v-grid-books">';
    foreach ($rows as $r) $h .= venom_card_biblio($r, $badge);
    return $h . '</div>';
}