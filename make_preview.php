<?php
$im = imagecreatetruecolor(600, 400);
$bg = imagecolorallocate($im, 11, 16, 32);
$p  = imagecolorallocate($im, 124, 58, 237);
$a  = imagecolorallocate($im, 34, 211, 238);
$w  = imagecolorallocate($im, 255, 255, 255);
imagefilledrectangle($im, 0, 0, 599, 399, $bg);
imagefilledrectangle($im, 40, 70, 560, 130, $p);
imagefilledrectangle($im, 40, 160, 280, 350, $a);
imagefilledrectangle($im, 300, 160, 560, 250, $p);
imagestring($im, 5, 250, 30, 'VENOM OPAC', $w);
imagepng($im, __DIR__ . '/preview.png');
imagedestroy($im);
echo 'preview.png BERHASIL dibuat. Segera HAPUS make_preview.php.';