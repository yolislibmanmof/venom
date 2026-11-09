<?php
/**
 * VENOM · Template Info.
 * Sengaja tanpa die() agar tetap terbaca oleh panel Admin maupun OPAC.
 */

// <<< PENAMBAHAN KRUSIAL: Memberitahu inti SLiMS bahwa ini template PHP
$sysconf['template']['base'] = 'php'; 

$venom_meta = [
    'name'        => 'VENOM',
    'title'       => 'VENOM — Ultimate OPAC',
    'version'     => '1.0.0',
    'author'      => 'VENOM Studio',
    'desc'        => 'Template OPAC SLiMS 9 premium: glassmorphism, hero slider, dark mode, palette switcher, statistik live, zero external dependency, hardened output.',
    'description' => 'Template OPAC SLiMS 9 premium: glassmorphism, hero slider, dark mode, palette switcher, statistik live, zero external dependency, hardened output.',
    'preview'     => 'preview.png',
    'preview_image' => 'preview.png',
    'requires'    => 'SLiMS 9 (Bulian)',
    'license'     => 'MIT',
];

// Kompatibilitas dengan berbagai versi penamaan variabel di inti SLiMS
$template_info = $venom_meta;
$theme_info    = $venom_meta;
$tinfo         = $venom_meta;
$template      = $venom_meta;

/* ═══════════════════════════════════════════════════════════════
   PENAMBAHAN: Registrasi skema customizer ke inti SLiMS 9.8
   File-file ini mengisi $sysconf['template']['option']['venom']
   agar dialog "Pengaturan Tema" di admin menampilkan formulir.
   ═══════════════════════════════════════════════════════════════ */
if (defined('INDEX_AUTH') && INDEX_AUTH == 1) {
    require_once __DIR__ . '/helpers/tinfo_options_helper.php';
    require_once __DIR__ . '/helpers/tinfo_defaults.php';
    require_once __DIR__ . '/helpers/tinfo_options.php';
}