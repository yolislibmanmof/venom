<?php
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access this file directly");

if (!function_exists('venomTinfoCustomizeAssets')) {
    function venomTinfoCustomizeAssets() {
        global $sysconf;
        $theme_dir = 'venom';
        $template_dir = $sysconf['template']['dir'] ?? 'template';
        $asset_base = (defined('SWB') ? SWB : '') . $template_dir . '/' . $theme_dir . '/assets/';
        $css = htmlspecialchars($asset_base . 'css/venom-theme-viewer.css?v=' . time(), ENT_QUOTES, 'UTF-8');
        $js  = htmlspecialchars($asset_base . 'js/venom-theme-viewer.js?v=' . time(), ENT_QUOTES, 'UTF-8');
        return '<link rel="stylesheet" href="' . $css . '"><script src="' . $js . '" defer></script>';
    }
}