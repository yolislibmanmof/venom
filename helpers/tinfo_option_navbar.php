<?php
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access this file directly");

return [
    'venom_navbar_menu' => [
        'dbfield' => 'venom_navbar_menu', 'label' => 'Menu Navigasi (Label | URL | Ikon ; ...)',
        'type' => 'longtext', 'max' => 2000,
        'default' => 'Beranda | index.php | home ; Koleksi | index.php?keywords=&search=Search | book ; Berita | index.php?p=news | news ; Profil | index.php?p=libinfo | layers',
    ],
    'venom_language_visible' => [
        'dbfield' => 'venom_language_visible', 'label' => 'Bahasa Aktif (comma-separated)',
        'type' => 'text', 'default' => 'id_ID,en_US',
    ],
    'venom_mobile_nav_show' => [
        'dbfield' => 'venom_mobile_nav_show', 'label' => 'Navigasi Bawah Mobile',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_back_to_top' => [
        'dbfield' => 'venom_back_to_top', 'label' => 'Tombol Kembali ke Atas',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
];