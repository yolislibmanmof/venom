<?php
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access this file directly");

return [
    'venom_fab_show' => [
        'dbfield' => 'venom_fab_show', 'label' => 'Tombol Aksi Mengambang',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_palette_switcher_show' => [
        'dbfield' => 'venom_palette_switcher_show', 'label' => 'Drawer Pratinjau Tema',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_floating_chat_show' => [
        'dbfield' => 'venom_floating_chat_show', 'label' => 'Chat Mengambang',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_live_ticker_show' => [
        'dbfield' => 'venom_live_ticker_show', 'label' => 'Berita Berjalan',
        'type' => 'dropdown', 'default' => 0, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_prayer_times_show' => [
        'dbfield' => 'venom_prayer_times_show', 'label' => 'Jadwal Sholat',
        'type' => 'dropdown', 'default' => 0, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_prayer_city' => [
        'dbfield' => 'venom_prayer_city', 'label' => 'Kota Jadwal Sholat',
        'type' => 'text', 'default' => 'Jakarta',
    ],
    'venom_whatsapp_number' => [
        'dbfield' => 'venom_whatsapp_number', 'label' => 'Nomor WhatsApp (format 628xxx)',
        'type' => 'text', 'default' => '',
    ],
    'venom_whatsapp_text' => [
        'dbfield' => 'venom_whatsapp_text', 'label' => 'Teks Awal Chat WhatsApp',
        'type' => 'text', 'default' => 'Halo, ada yang bisa kami bantu?',
    ],
    'venom_social_facebook' => [
        'dbfield' => 'venom_social_facebook', 'label' => 'URL Facebook',
        'type' => 'text', 'default' => '',
    ],
    'venom_social_instagram' => [
        'dbfield' => 'venom_social_instagram', 'label' => 'URL Instagram',
        'type' => 'text', 'default' => '',
    ],
    'venom_social_youtube' => [
        'dbfield' => 'venom_social_youtube', 'label' => 'URL YouTube',
        'type' => 'text', 'default' => '',
    ],
    'venom_social_twitter' => [
        'dbfield' => 'venom_social_twitter', 'label' => 'URL Twitter/X',
        'type' => 'text', 'default' => '',
    ],
    'venom_contact_email' => [
        'dbfield' => 'venom_contact_email', 'label' => 'Email Kontak',
        'type' => 'text', 'default' => '',
    ],
    'venom_contact_phone' => [
        'dbfield' => 'venom_contact_phone', 'label' => 'Nomor Telepon',
        'type' => 'text', 'default' => '',
    ],
    'venom_contact_address' => [
        'dbfield' => 'venom_contact_address', 'label' => 'Alamat Perpustakaan',
        'type' => 'longtext', 'default' => '',
    ],
];