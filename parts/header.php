<?php defined('INDEX_AUTH') OR die('Direct access not allowed!');
$vpalette = (string) venom_cfg('ui.palette', 'venom');
$vmode    = (string) venom_cfg('ui.mode', 'auto');
$vmax     = (int) venom_cfg('ui.container', 1240);
$vradius  = (int) venom_cfg('ui.radius', 18);
$vpals    = (array) venom_cfg('palettes', []);
$vcss     = '';
foreach ($vpals as $k => $c) {
    if (empty($c['p'])) continue;
    $vcss .= '[data-palette="' . venom_e($k) . '"]{--v-p:' . venom_e($c['p'])
           . ';--v-a:' . venom_e(isset($c['a']) ? $c['a'] : $c['p']) . ';}';
}
?>
<!DOCTYPE html>
<html lang="<?= venom_e(venom_lang_code()) ?>" data-palette="<?= venom_e($vpalette) ?>" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="color-scheme" content="light dark">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta name="theme-color" content="#0b1020" media="(prefers-color-scheme: dark)">
<meta name="generator" content="VENOM 1.0.0 for SLiMS 9">
<title><?= venom_e(venom_title()) ?></title>
<script>
/* Pre-paint: cegah kedipan tema (FOUC) */
(function(){try{var m=<?= json_encode($vmode) ?>,s=localStorage.getItem('venom.mode');
var d=(s||(m==='auto'?(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'):m));
document.documentElement.setAttribute('data-theme',d==='dark'?'dark':'light');
var p=localStorage.getItem('venom.palette');if(p)document.documentElement.setAttribute('data-palette',p);
}catch(e){}})();
</script>
<style>:root{--v-max:<?= $vmax ?>px;--v-r:<?= $vradius ?>px}<?= $vcss ?></style>
<link rel="stylesheet" href="<?= venom_e(venom_asset('css/venom-core.css')) ?>">
<?= venom_core_head() ?>
</head>
<body class="v-body<?= venom_on('motion') ? '' : ' v-no-motion' ?>" data-page="<?= venom_e(venom_page()) ?>">
<a class="v-skip" href="#v-main">Lewati ke konten utama</a>
<?php venom_part('background_layers'); ?>
<?php venom_part('_navbar'); ?>
<main id="v-main" class="v-main">