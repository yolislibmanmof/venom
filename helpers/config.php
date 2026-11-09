<?php
/**
 * VENOM · Konfigurasi tema. Ubah di sini, tidak perlu sentuh kode lain.
 * CATATAN: File ini HARUS murni return array. JANGAN taruh fungsi di sini!
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

return [
    'brand' => [
        'name'    => 'VENOM',
        'tagline' => 'Ultimate Library Experience',
        'logo'    => '',
        'favicon' => '',
    ],

    'ui' => [
        'palette'   => 'venom',
        'mode'      => 'auto',
        'radius'    => 18,
        'glass'     => true,
        'motion'    => true,
        'container' => 1240,
    ],

    'palettes' => [
        'venom'   => ['label' => 'Venom Violet', 'p' => '#7c3aed', 'a' => '#22d3ee'],
        'emerald' => ['label' => 'Emerald Forest', 'p' => '#059669', 'a' => '#a3e635'],
        'crimson' => ['label' => 'Crimson Ember', 'p' => '#e11d48', 'a' => '#f59e0b'],
        'ocean'   => ['label' => 'Deep Ocean', 'p' => '#0284c7', 'a' => '#22d3ee'],
        'mono'    => ['label' => 'Monochrome Pro', 'p' => '#475569', 'a' => '#94a3b8'],
    ],

    'features' => [
        // ─── Fitur Inti ───
        'hero' => true,
        'stats' => true,
        'new_arrivals' => true,
        'featured' => true,
        'topics' => true,
        'news' => true,
        'fab' => true,
        'palette_switcher' => true,

        // ─── Fitur Tahap 2: FX & Interaksi ───
        'cursor_glow' => true,
        'particles' => true,
        'prayer_times' => true,
        'live_ticker' => true,
        'floating_chat' => true,
        'mobile_bottom_nav' => true,

        // ─── Fitur Tahap 3: Eksplorasi Koleksi ───
        'ddc_browse'      => true,
        'category_cards'  => true,
        'ctype_pills'     => true,

        // ─── Opsi Tambahan ───
        'member_cta'      => true,
    ],

    'prayer' => [
        'city'   => 'Jakarta',
        'method' => 11,
    ],

    'hero' => [
        'interval' => 6000,
        'slides' => [
            [
                'kicker' => 'Digital Library 5.0',
                'title'  => 'Jelajahi Semesta Pengetahuan',
                'text'   => 'Ribuan koleksi terkurasi, pencarian cerdas, dan akses 24/7 dalam satu antarmuka yang elegan.',
                'cta'    => 'Mulai Menjelajah',
                'url'    => '#v-koleksi',
            ],
            [
                'kicker' => 'Smart Discovery',
                'title'  => 'Temukan yang Tak Tercari',
                'text'   => 'Filter faset, jelajah topik, dan rekomendasi koleksi unggulan yang dikurasi pustakawan.',
                'cta'    => 'Lihat Koleksi Unggulan',
                'url'    => '#v-unggulan',
            ],
            [
                'kicker' => 'Member Zone',
                'title'  => 'Perpustakaan di Genggaman',
                'text'   => 'Pantau peminjaman, perpanjang mandiri, dan kartu anggota digital — kapan pun, di mana pun.',
                'cta'    => 'Masuk Area Anggota',
                'url'    => 'index.php?p=member',
            ],
        ],
    ],

    'contact' => [
        'wa'       => '6281234567890',
        'telegram' => '',
        'email'    => '',
        'phone'    => '',
        'address'  => '',
    ],

    'social' => [
        'facebook' => '', 'instagram' => '', 'youtube' => '', 'twitter' => '',
    ],

    'hours' => [
        ['Senin – Jumat', '08.00 – 16.00'],
        ['Sabtu', '09.00 – 14.00'],
        ['Minggu / Libur', 'Tutup'],
    ],

    'limits' => ['new' => 10, 'featured' => 10, 'topics' => 14, 'news' => 3],
    'debug'  => false,
];