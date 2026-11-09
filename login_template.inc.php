<?php
// Jika halaman yang diminta adalah visitor counter, render secara khusus
if (isset($_GET['p']) && $_GET['p'] === 'visitor') {
    $imagesDisk = \SLiMS\Filesystems\Storage::images();
    include "classic.php";
    include "parts/header.php";
    echo $main_content;
    include __DIR__ . "/assets/js/vegas.js.php"; // Atau sesuaikan dengan JS venom
    echo '</body></html>';
} else {
    // UNTUK SEMUA HALAMAN LAIN (TERMASUK LOGIN MEMBER), 
    // serahkan ke index_template.inc.php agar alur otentikasi SLiMS berjalan normal!
    include "index_template.inc.php";
}