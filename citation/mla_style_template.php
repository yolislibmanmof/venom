<?php
/**
 * VENOM · MLA Style Citation (9th Edition)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
if (!defined('VENOM_CITE_ASSETS_LOADED')) { require __DIR__ . '/apa_style_template.php'; }
/* Jika aset sudah dimuat oleh file lain, blok di atas tidak mengeksekusi ulang. */

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

/* ── Format penulis MLA ── */
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

$vnx_out = '';
if ($vnx_au !== '') $vnx_out .= $vnx_e($vnx_au) . '. ';
$vnx_out .= '<em>' . $vnx_e($vnx_title !== '' ? $vnx_title : 'Tanpa judul') . '</em>.';
if ($vnx_ed !== '') $vnx_out .= ' ' . $vnx_e($vnx_ed) . ',';
if ($vnx_pub !== '') $vnx_out .= ' ' . $vnx_e($vnx_pub) . ',';
$vnx_out .= ' ' . $vnx_e($vnx_year !== '' ? $vnx_year : 'n.d.') . '.';
?>
<div class="vnx-cite">
  <div class="vnx-cite-head">
    <span class="vnx-cite-badge">MLA</span>
    <span class="vnx-cite-name">MLA Style · 9th Edition</span>
    <button type="button" class="vnx-cite-copy" title="Salin sitasi MLA" aria-label="Salin sitasi MLA"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></button>
  </div>
  <p class="vnx-cite-body"><?= $vnx_out ?></p>
</div>