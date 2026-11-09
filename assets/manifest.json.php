<?php
/**
 * VENOM · PWA Manifest (JSON)
 * Membuat OPAC bisa di-install sebagai app di HP
 */

// 1. Cegah "Direct access not allowed!" jika file ini diakses langsung oleh browser
if (!defined('INDEX_AUTH')) {
    define('INDEX_AUTH', '1');
}

// 2. Bersihkan SEMUA output buffer (spasi, BOM, warning PHP) agar JSON valid
while (ob_get_level()) {
    ob_end_clean();
}

// 3. Load helper (pastikan path-nya benar sesuai struktur folder VENOM)
if (file_exists(__DIR__ . '/../helpers/core.php')) {
    require_once __DIR__ . '/../helpers/core.php';
}

// 4. Bersihkan lagi setelah load helper untuk jaga-jaga
ob_clean();

// 5. Header JSON yang tegas
header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

// 6. Ambil data dengan fallback aman (jika $sysconf belum ter-load)
$name = function_exists('venom_library_name') ? venom_library_name() : 'VENOM Library';
$short = strlen($name) > 20 ? substr($name, 0, 20) : $name;
$base = function_exists('venom_base_url') ? venom_base_url() : '/';

// Icon SVG fallback (huruf V ungu) jika logo belum ada
$icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><rect width="512" height="512" rx="110" fill="#7c3aed"/><text x="256" y="360" font-family="Arial" font-size="280" font-weight="bold" fill="white" text-anchor="middle">V</text></svg>';
$icon_data = 'data:image/svg+xml;base64,' . base64_encode($icon_svg);

$manifest = [
    'name'             => $name,
    'short_name'       => $short,
    'description'      => 'Perpustakaan Digital ' . $name,
    'start_url'        => $base . 'index.php',
    'scope'            => $base,
    'display'          => 'standalone',
    'orientation'      => 'portrait',
    'background_color' => '#080b16',
    'theme_color'      => '#7c3aed',
    'lang'             => 'id',
    'dir'              => 'ltr',
    'categories'       => ['education', 'books'],
    'icons' => [
        ['src' => $icon_data, 'sizes' => '192x192', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
        ['src' => $icon_data, 'sizes' => '512x512', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
    ],
    'shortcuts' => [
        [
            'name'       => 'Cari Koleksi',
            'short_name' => 'Cari',
            'url'        => $base . 'index.php?keywords=&search=Search',
            'icons'      => [['src' => $icon_data, 'sizes' => '96x96']]
        ],
        [
            'name'       => 'Berita Terbaru',
            'short_name' => 'Berita',
            'url'        => $base . 'index.php?p=news',
            'icons'      => [['src' => $icon_data, 'sizes' => '96x96']]
        ]
    ]
];

// 7. Output JSON dan HENTIKAN eksekusi (exit) agar tidak ada tambahan apa pun
echo json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;