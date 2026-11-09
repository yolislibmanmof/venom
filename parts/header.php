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
$vfav = venom_favicon_url();
$vlogo = venom_logo_url();
$vog  = $vlogo !== '' ? $vlogo : 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><rect width="512" height="512" rx="112" fill="#7c3aed"/><text x="256" y="336" font-family="Arial" font-size="230" font-weight="bold" fill="#fff" text-anchor="middle">V</text></svg>');
?>
<!DOCTYPE html>
<html lang="<?= venom_e(venom_lang_code()) ?>" data-palette="<?= venom_e($vpalette) ?>" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="color-scheme" content="light dark">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta name="theme-color" content="#0b1020" media="(prefers-color-scheme: dark)">
<meta name="generator" content="VENOM <?= VENOM_VER ?> for SLiMS 9">
<?php if ($vfav !== ''): ?>
<link rel="icon" href="<?= venom_e($vfav) ?>">
<link rel="shortcut icon" href="<?= venom_e($vfav) ?>">
<?php else: ?>
<link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2064%2064'%3E%3Crect%20width='64'%20height='64'%20rx='14'%20fill='%237c3aed'/%3E%3Ctext%20x='32'%20y='45'%20font-family='Arial'%20font-size='34'%20font-weight='bold'%20fill='white'%20text-anchor='middle'%3EV%3C/text%3E%3C/svg%3E">
<?php endif; ?>
<link rel="apple-touch-icon" href="<?= venom_e($vog) ?>">
<title><?= venom_e(venom_title()) ?></title>
<meta name="description" content="<?= venom_e(venom_cfg('brand.description', venom_library_name() . ' — Perpustakaan Digital Modern')) ?>">
<meta property="og:title" content="<?= venom_e(venom_title()) ?>">
<meta property="og:site_name" content="<?= venom_e(venom_library_name()) ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= venom_e($vog) ?>">
<script>
(function(){try{
  var m=<?= json_encode($vmode) ?>,s=localStorage.getItem('venom.mode');
  var d=(s||(m==='auto'?(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'):m));
  document.documentElement.setAttribute('data-theme',d==='dark'?'dark':'light');
  var p=localStorage.getItem('venom.palette');if(p)document.documentElement.setAttribute('data-palette',p);
  var fx=localStorage.getItem('venom.particle');if(fx)document.documentElement.setAttribute('data-particle',fx);
}catch(e){}})();
</script>
<style>:root{--v-max:<?= $vmax ?>px;--v-r:<?= $vradius ?>px}<?= $vcss ?></style>
<link rel="stylesheet" href="<?= venom_e(venom_asset('css/venom-core.css')) ?>">
<link rel="stylesheet" href="<?= venom_e(venom_asset('css/venom-fx.css')) ?>">
<link rel="stylesheet" href="<?= venom_e(venom_asset('css/venom-components.css')) ?>">
<link rel="stylesheet" href="<?= venom_e(venom_asset('css/venom-detail.css')) ?>">
<link rel="stylesheet" href="<?= venom_e(venom_asset('css/venom-member.css')) ?>">
<link rel="manifest" href="<?= venom_e(venom_asset('manifest.json.php')) ?>">
<?php if (isset($_GET['search']) && defined('JWB')): ?>
<link rel="stylesheet" href="<?= JWB ?>ion.rangeSlider/css/ion.rangeSlider.min.css">
<?php endif; ?>

<!-- ═══ PATCH AMAN: Muat CSS Animasi Background & Kursor Custom ═══ -->
<link rel="stylesheet" href="<?= venom_e(venom_asset('css/venom-bg-animations.css')) ?>">
<link rel="stylesheet" href="<?= venom_e(venom_asset('css/venom-cursor.css')) ?>">
<?php 
// Render custom background CSS hanya jika fungsi tersedia (mencegah error fatal)
if (function_exists('venom_render_custom_background_css')) {
    echo venom_render_custom_background_css();
}
?>

<?= venom_core_head() ?>
</head>
<body class="v-body<?= venom_on('motion') ? '' : ' v-no-motion' ?>" data-page="<?= venom_e(venom_page()) ?>">
<a class="v-skip" href="#v-main">Lewati ke konten utama</a>
<?php venom_part('background_layers'); ?>
<?php venom_part('_navbar'); ?>
<main id="v-main" class="v-main">