<?php
/**
 * VENOM · Skema opsi tema — SATU sumber kebenaran untuk Admin & OPAC.
 */
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access this file directly");

return [
    'general' => [
        'venom_theme_color' => ['label' => 'Palet Tema', 'type' => 'dropdown', 'default' => 'venom', 'data' => [
            ['venom','Venom Violet'],['emerald','Emerald Forest'],['crimson','Crimson Ember'],['ocean','Deep Ocean'],
            ['mono','Monochrome Pro'],['rose','Rose Dawn'],['sunset','Sunset Blaze'],['midnight','Midnight Sky'],
            ['cyberpunk','Cyberpunk'],['forest','Forest Deep']]],
        'venom_mode' => ['label' => 'Mode Warna', 'type' => 'dropdown', 'default' => 'auto', 'data' => [
            ['auto','Otomatis (ikuti sistem)'],['light','Selalu Terang'],['dark','Selalu Gelap']]],
        'venom_font_family' => ['label' => 'Font', 'type' => 'dropdown', 'default' => 'system', 'data' => [
            ['system','System UI'],['inter','Inter'],['poppins','Poppins'],['roboto','Roboto'],['playfair','Playfair Display']]],
        'venom_container_width' => ['label' => 'Lebar Kontainer (px)', 'type' => 'text', 'default' => '1240'],
        'venom_border_radius' => ['label' => 'Radius Sudut (px)', 'type' => 'text', 'default' => '18'],
        'venom_glass_effect' => ['label' => 'Efek Glassmorphism', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Aktif'],['0','Nonaktif']]],
        'venom_motion' => ['label' => 'Animasi UI', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Aktif'],['0','Nonaktif']]],
        'venom_custom_css' => ['label' => 'Custom CSS Tambahan', 'type' => 'longtext', 'default' => '', 'max' => 4000],
    ],
    'hero' => [
        'venom_hero_show' => ['label' => 'Hero Slider', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_hero_interval' => ['label' => 'Durasi Slide (ms)', 'type' => 'text', 'default' => '6000'],
        'venom_stats_show' => ['label' => 'Statistik Perpustakaan', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_new_arrivals_show' => ['label' => 'Koleksi Terbaru', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_featured_show' => ['label' => 'Koleksi Unggulan', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_topics_show' => ['label' => 'Jelajah Topik', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_news_show' => ['label' => 'Berita', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_new_arrivals_limit' => ['label' => 'Jumlah Koleksi Terbaru', 'type' => 'text', 'default' => '10'],
        'venom_featured_limit' => ['label' => 'Jumlah Koleksi Unggulan', 'type' => 'text', 'default' => '10'],
        'venom_topics_limit' => ['label' => 'Jumlah Topik', 'type' => 'text', 'default' => '14'],
        'venom_news_limit' => ['label' => 'Jumlah Berita', 'type' => 'text', 'default' => '3'],
        'venom_particles' => ['label' => 'Efek Partikel Canvas', 'type' => 'dropdown', 'default' => 'stars', 'data' => [
            ['stars','Bintang'],['snow','Salju'],['bubbles','Gelembung'],['rain','Hujan'],
            ['fireflies','Kunang-kunang'],['confetti','Confetti'],['none','Nonaktif']]],
        'venom_animation_speed' => ['label' => 'Kecepatan Animasi', 'type' => 'dropdown', 'default' => '1', 'data' => [
            ['0.5','Lambat'],['1','Normal'],['2','Cepat'],['3','Sangat Cepat']]],
    ],
    'effects' => [
        'venom_hero_animation' => ['label' => 'Animasi Background Layer', 'type' => 'dropdown', 'default' => 'none',
            'data' => [['none','Nonaktif'],['particles','Floating Glyphs'],['rain','Code Rain'],['grid','Moving Grid'],
                      ['twinkle','Twinkling Stars'],['zen-ripples','Zen Ripples'],['neural-network','Neural Network'],
                      ['starfield-warp','Starfield Warp'],['floating-embers','Floating Embers']]],
        'venom_hero_background_style' => ['label' => 'Gaya Background', 'type' => 'dropdown', 'default' => 'none',
            'data' => [['none','None'],['soft-gradient','Soft Gradient'],['aurora-glow','Aurora Glow'],
                      ['ocean-waves','Ocean Waves'],['aurora-neon','Aurora Neon'],['galaxy-nebula','Galaxy Nebula'],
                      ['quantum-mesh','Quantum Mesh'],['zen-bamboo','Zen Bamboo'],['custom','Custom']]],
        'venom_hero_background_custom' => ['label' => 'Background Kustom (TERANG | GELAP)', 'type' => 'longtext', 'default' => '', 'max' => 500],
        'venom_cursor_icon' => ['label' => 'Ikon Kursor Custom', 'type' => 'dropdown', 'default' => 'default',
            'data' => [['default','Default Browser'],['neon-comet','Neon Comet'],['pixel-sword','Pixel Sword'],
                      ['electric-bolt','Electric Bolt'],['ink-brush','Ink Brush'],['rainbow-ribbon','Rainbow Ribbon']]],
        'venom_cursor_particles' => ['label' => 'Trail Partikel Kursor', 'type' => 'dropdown', 'default' => 'none',
            'data' => [['auto','Auto'],['low','Ringan'],['medium','Sedang'],['high','Optimal'],['none','Nonaktif']]],
    ],
    'navbar' => [
        'venom_navbar_menu' => ['label' => 'Menu Navigasi (Label | URL | ikon ; ...)', 'type' => 'longtext', 'max' => 2000,
            'default' => 'Beranda | index.php | home ; Koleksi | index.php?keywords=&search=Search | book ; Berita | index.php?p=news | news ; Profil | index.php?p=libinfo | layers'],
        'venom_language_visible' => ['label' => 'Bahasa Aktif (pisahkan koma)', 'type' => 'text', 'default' => 'id_ID,en_US'],
        'venom_mobile_nav_show' => ['label' => 'Navigasi Bawah Mobile', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_back_to_top' => ['label' => 'Tombol Kembali ke Atas', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
    ],
    'footer' => [
        'venom_fab_show' => ['label' => 'Tombol Aksi Mengambang', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_palette_switcher_show' => ['label' => 'Drawer Kustomisasi Tema', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_floating_chat_show' => ['label' => 'Chat Mengambang', 'type' => 'dropdown', 'default' => '1', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_live_ticker_show' => ['label' => 'Berita Berjalan', 'type' => 'dropdown', 'default' => '0', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_prayer_times_show' => ['label' => 'Jadwal Sholat', 'type' => 'dropdown', 'default' => '0', 'data' => [['1','Tampil'],['0','Sembunyi']]],
        'venom_prayer_city' => ['label' => 'Kota Jadwal Sholat', 'type' => 'text', 'default' => 'Jakarta'],
        'venom_whatsapp_number' => ['label' => 'Nomor WhatsApp (628xxx)', 'type' => 'text', 'default' => ''],
        'venom_whatsapp_text' => ['label' => 'Teks Awal Chat WhatsApp', 'type' => 'text', 'default' => 'Halo, ada yang bisa kami bantu?'],
        'venom_social_facebook' => ['label' => 'URL Facebook', 'type' => 'text', 'default' => ''],
        'venom_social_instagram' => ['label' => 'URL Instagram', 'type' => 'text', 'default' => ''],
        'venom_social_youtube' => ['label' => 'URL YouTube', 'type' => 'text', 'default' => ''],
        'venom_social_twitter' => ['label' => 'URL Twitter/X', 'type' => 'text', 'default' => ''],
        'venom_contact_email' => ['label' => 'Email Kontak', 'type' => 'text', 'default' => ''],
        'venom_contact_phone' => ['label' => 'Nomor Telepon', 'type' => 'text', 'default' => ''],
        'venom_contact_address' => ['label' => 'Alamat Perpustakaan', 'type' => 'longtext', 'default' => ''],
    ],
];