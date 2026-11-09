<?php
/**
 * VENOM · MLA Style Citation (9th Edition)
 * Terintegrasi penuh dengan venom-app.js (data-copy attribute)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

$vnx_src   = (isset($detail) && is_array($detail)) ? $detail : [];
$vnx_e     = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
$vnx_title = (string) ($title ?? ($vnx_src['title'] ?? ''));
$vnx_year  = (string) ($publish_year ?? ($vnx_src['publish_year'] ?? ''));
$vnx_ed    = (string) ($edition ?? ($vnx_src['edition'] ?? ''));
$vnx_pub   = (string) ($publisher_name ?? ($vnx_src['publisher_name'] ?? ''));

$vnx_raw = [];
if (isset($authors) && is_array($authors)) {
    foreach ($authors as $vnx_a) $vnx_raw[] = is_array($vnx_a) ? (string) ($vnx_a['author_name'] ?? '') : (string) $vnx_a;
} elseif (isset($vnx_src['authors']) && is_array($vnx_src['authors'])) {
    foreach ($vnx_src['authors'] as $vnx_a) $vnx_raw[] = is_array($vnx_a) ? (string) ($vnx_a['author_name'] ?? '') : (string) $vnx_a;
}
$vnx_raw = array_values(array_filter(array_map('trim', $vnx_raw), function ($s) { return $s !== ''; }));

$vnx_split = function ($name) {
    $p = preg_split('/\s+/', trim((string) $name));
    if (count($p) < 2) return [trim((string) $name), ''];
    $last = array_pop($p);
    return [$last, implode(' ', $p)];
};

$vnx_n = [];
foreach ($vnx_raw as $vnx_nm) {
    list($vnx_l, $vnx_f) = $vnx_split($vnx_nm);
    $vnx_n[] = [$vnx_l, $vnx_f];
}
$c = count($vnx_n);
if ($c === 0) $vnx_au = '';
elseif ($c === 1) $vnx_au = $vnx_n[0][0] . ($vnx_n[0][1] !== '' ? ', ' . $vnx_n[0][1] : '');
elseif ($c === 2) $vnx_au = $vnx_n[0][0] . ', ' . $vnx_n[0][1] . ', and ' . $vnx_n[1][1] . ' ' . $vnx_n[1][0];
else $vnx_au = $vnx_n[0][0] . ', ' . $vnx_n[0][1] . ', et al.';

$vnx_out_html = '';
if ($vnx_au !== '') $vnx_out_html .= $vnx_e($vnx_au) . '. ';
$vnx_out_html .= '<em>' . $vnx_e($vnx_title !== '' ? $vnx_title : 'Tanpa judul') . '</em>.';
if ($vnx_ed !== '') $vnx_out_html .= ' ' . $vnx_e($vnx_ed) . ',';
if ($vnx_pub !== '') $vnx_out_html .= ' ' . $vnx_e($vnx_pub) . ',';
$vnx_out_html .= ' ' . $vnx_e($vnx_year !== '' ? $vnx_year : 'n.d.') . '.';

// OPTIMASI: Buat versi plain text untuk atribut data-copy agar venom-app.js bisa menyalinnya dengan bersih
$vnx_out_plain = strip_tags($vnx_out_html);
$vnx_out_plain = html_entity_decode($vnx_out_plain, ENT_QUOTES, 'UTF-8');
?>
<div class="vnx-cite v-card v-pad" style="margin-top: 16px;">
  <div class="vnx-cite-head" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <span class="v-chip v-chip-xs">MLA 9th Edition</span>
    <!-- OPTIMASI: data-copy berisi teks bersih, kompatibel langsung dengan venom-app.js -->
    <button type="button" class="v-btn v-btn-ghost v-btn-sm" data-copy="<?= $vnx_e($vnx_out_plain) ?>" title="Salin sitasi MLA" aria-label="Salin sitasi MLA">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
      Salin
    </button>
  </div>
  <p class="vnx-cite-body" style="font-size: 0.95rem; line-height: 1.6; color: var(--v-tx); padding-left: 12px; border-left: 3px solid var(--v-p);"><?= $vnx_out_html ?></p>
</div>