<?php
/**
 * VENOM · Main Layout Wrapper
 * TIDAK melakukan routing. TIDAK memblokir POST.
 * Routing, autentikasi, dan query tetap 100% milik inti SLiMS.
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

// ═══════════════════════════════════════════════════════════════
// FIX CRITICAL: Definisikan sebagai GLOBAL agar bisa dibaca oleh 
// file inti SLiMS (seperti visitor.inc.php)
// ═══════════════════════════════════════════════════════════════
$GLOBALS['main_template_path'] = __DIR__;
$main_template_path = __DIR__;

require_once __DIR__ . '/helpers/core.php';

// Daftarkan semua variabel yang diberikan inti SLiMS ke registry VENOM.
Venom_Core::register(get_defined_vars());

venom_part('header');

// Homepage: seksi VENOM. Halaman lain: konten asli SLiMS.
if (venom_is_home()) {
    venom_part('_home');
}

// Konten inti (pencarian, detail, anggota, berita, visitor, dll).
$venom_core_html = venom_core_content();
if ($venom_core_html !== '') {
    echo '<div class="v-wrap v-core v-page-transition">' . $venom_core_html . '</div>';
}

echo venom_debug_panel();

venom_part('footer');