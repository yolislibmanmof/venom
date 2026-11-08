<?php
/**
 * VENOM · Kamus bahasa tema (additif, tidak menimpa $lang SLiMS).
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

$venom_lang = [
    'id' => [
        'home' => 'Beranda', 'search' => 'Cari koleksi, penulis, subjek…', 'search_btn' => 'Cari',
        'member' => 'Area Anggota', 'visitor' => 'Statistik', 'news' => 'Berita',
        'libinfo' => 'Profil', 'help' => 'Bantuan', 'new' => 'Koleksi Terbaru',
        'featured' => 'Koleksi Unggulan', 'topics' => 'Jelajah Topik',
        'titles' => 'Judul Koleksi', 'items' => 'Eksemplar', 'members' => 'Anggota Terdaftar',
        'visits' => 'Kunjungan Hari Ini', 'loans' => 'Sedang Dipinjam',
        'dark' => 'Mode Gelap', 'light' => 'Mode Terang', 'palette' => 'Ganti Warna',
        'top' => 'Kembali ke atas', 'ask' => 'Tanya Pustakawan',
    ],
    'en' => [
        'home' => 'Home', 'search' => 'Search titles, authors, subjects…', 'search_btn' => 'Search',
        'member' => 'Member Area', 'visitor' => 'Statistics', 'news' => 'News',
        'libinfo' => 'About', 'help' => 'Help', 'new' => 'New Arrivals',
        'featured' => 'Featured Collection', 'topics' => 'Browse Topics',
        'titles' => 'Titles', 'items' => 'Copies', 'members' => 'Members',
        'visits' => 'Visits Today', 'loans' => 'On Loan',
        'dark' => 'Dark Mode', 'light' => 'Light Mode', 'palette' => 'Change Palette',
        'top' => 'Back to top', 'ask' => 'Ask a Librarian',
    ],
];
$GLOBALS['venom_lang'] = $venom_lang;