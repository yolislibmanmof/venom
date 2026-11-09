<?php
/**
 * VENOM · Web App Manifest
 * Diakses langsung oleh browser (tanpa bootstrap SLiMS) → TANPA guard INDEX_AUTH.
 */
$script = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '/index.php';
/* path: /app/template/venom/assets/manifest.json.php → root aplikasi = 4x dirname */
$root = dirname(dirname(dirname(dirname($script))));
if ($root === '/' || $root === '.' || $root === '\\') $root = '';
$base = $root . '/';

header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

echo json_encode([
    'name'             => 'VENOM OPAC — Katalog Perpustakaan Digital',
    'short_name'       => 'VENOM OPAC',
    'description'      => 'Katalog perpustakaan digital yang cepat, modern, dan elegan.',
    'start_url'        => $base . 'index.php',
    'scope'            => $base,
    'display'          => 'standalone',
    'background_color' => '#0b1020',
    'theme_color'      => '#7c3aed',
    'icons' => [
        ['src' => $base . 'images/default/image.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
        ['src' => $base . 'webicon.ico', 'sizes' => 'any', 'type' => 'image/x-icon'],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);