<?php
/**
 * VENOM · Main Layout Wrapper
 * Routing & Session logic diadaptasi 100% dari pustaka_ceria
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

require_once __DIR__ . '/helpers/core.php';

// Daftarkan semua variabel yang diberikan inti SLiMS ke registry VENOM.
Venom_Core::register(get_defined_vars());

// setup list view (PENTING: Ini yang dibaca oleh biblio_list_template.php)
$req_view = $_POST['view'] ?? $_GET['view'] ?? null;
if ($req_view !== null && in_array((string) $req_view, ['simple', 'list', 'grid'], true)) {
    $_SESSION['LIST_VIEW'] = (string) $req_view;
} elseif (!in_array($_SESSION['LIST_VIEW'] ?? '', ['simple', 'list', 'grid'], true)) {
    $_SESSION['LIST_VIEW'] = 'grid';
}

venom_part('header');

// ----------------------------------------------------------------------------
// load content by URI
// ----------------------------------------------------------------------------
if (isset($_GET['p']) || isset($_GET['search'])) {
    // handle result search
    if (isset($_GET['search'])) {
        include __DIR__ . '/parts/_result-search.php';
    }
    // handle member page
    elseif ($_GET['p'] == 'member') {
        venom_part('_member');
    } 
    // handle other pages (detail, news, libinfo, dll)
    else {
        $venom_core_html = venom_core_content();
        if ($venom_core_html !== '') {
            echo '<div class="v-wrap v-core v-page-transition">' . $venom_core_html . '</div>';
        }
    }
} else {
    // not found query string: load home page
    venom_part('_home');
}

echo venom_debug_panel();
venom_part('footer');