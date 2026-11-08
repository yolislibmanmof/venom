<?php
/**
 * VENOM · PWA Manifest (JSON)
 * Membuat OPAC bisa di-install sebagai app di HP
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/../helpers/core.php';

header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$name = venom_library_name();
$short = strlen($name) > 20 ? substr($name, 0, 20) : $name;
$base = venom_base_url();

// Icon SVG sebagai data-URI
$icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><rect width="512" height="512" rx="110" fill="#7c3aed"/><text x="256" y="360" font-family="Arial" font-size="280" font-weight="bold" fill="white" text-anchor="middle">V</text></svg>';
$icon_data = 'data:image/svg+xml;base64,' . base64_encode($icon_svg);

$manifest = [
    'name'             => $name,
    'short_name'       => $short,
    'description'      => venom_cfg('brand.description', 'Perpustakaan Digital ' . $name),
    'start_url'        => $base . 'index.php',
    'scope'            => $base,
    'display'          => 'standalone',
    'orientation'      => 'portrait',
    'background_color' => '#080b16',
    'theme_color'      => '#7c3aed',
    'lang'             => venom_lang_code(),
    'dir'              => 'ltr',
    'categories'       => ['education', 'books'],
    'icons' => [
        ['src' => $icon_data, 'sizes' => '192x192', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
        ['src' => $icon_data, 'sizes' => '512x512', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
    ],
    'shortcuts' => [
        [
            'name'  => 'Cari Koleksi',
            'short_name' => 'Cari',
            'url'   => $base . 'index.php?keywords=&search=Search',
            'icons' => [['src' => $icon_data, 'sizes' => '96x96']]
        ],
        [
            'name'  => 'Berita Terbaru',
            'short_name' => 'Berita',
            'url'   => $base . 'index.php?p=news',
            'icons' => [['src' => $icon_data, 'sizes' => '96x96']]
        ]
    ]
];

echo json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);