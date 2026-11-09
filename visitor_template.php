<?php
/**
 * VENOM · Visitor Counter Template (Entry Point)
 * Memilih layout berdasarkan konfigurasi
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

// Load visitor helpers
require_once __DIR__ . '/helpers/visitor.php';

// Layout style: 'kiosk' (default) atau 'split'
$layout_style = $sysconf['template']['visitor_layout'] ?? 'kiosk';

// Include Vue.js dari CDN
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In Pengunjung - <?= venom_e(venom_library_name()) ?></title>
    
    <!-- VENOM CSS -->
    <link rel="stylesheet" href="<?= venom_e(SWB . 'template/venom/assets/css/venom-core.css') ?>">
    <link rel="stylesheet" href="<?= venom_e(SWB . 'template/venom/assets/css/venom-components.css') ?>">
    
    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--v-bg);
            color: var(--v-tx);
        }
    </style>
</head>
<body>
    <?php
    if ($layout_style === 'split') {
        include __DIR__ . '/parts/visitor/visitor_split.php';
    } else {
        include __DIR__ . '/parts/visitor/visitor_kiosk.php';
    }
    
    // Include ticker
    include __DIR__ . '/parts/visitor/visitor_ticker.php';
    ?>
</body>
</html>