<?php
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access this file directly");

return [
    'venom_hero_show' => [
        'dbfield' => 'venom_hero_show', 'label' => 'Tampilkan Hero Slider',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_hero_interval' => [
        'dbfield' => 'venom_hero_interval', 'label' => 'Durasi Slide (ms)',
        'type' => 'text', 'default' => 6000,
    ],
    'venom_stats_show' => [
        'dbfield' => 'venom_stats_show', 'label' => 'Statistik Perpustakaan',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_new_arrivals_show' => [
        'dbfield' => 'venom_new_arrivals_show', 'label' => 'Koleksi Terbaru',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_featured_show' => [
        'dbfield' => 'venom_featured_show', 'label' => 'Koleksi Unggulan',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_topics_show' => [
        'dbfield' => 'venom_topics_show', 'label' => 'Jelajah Topik',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_news_show' => [
        'dbfield' => 'venom_news_show', 'label' => 'Berita',
        'type' => 'dropdown', 'default' => 1, 'data' => [[1,'Tampil'],[0,'Sembunyi']],
    ],
    'venom_new_arrivals_limit' => [
        'dbfield' => 'venom_new_arrivals_limit', 'label' => 'Jumlah Koleksi Terbaru',
        'type' => 'text', 'default' => 10,
    ],
    'venom_featured_limit' => [
        'dbfield' => 'venom_featured_limit', 'label' => 'Jumlah Koleksi Unggulan',
        'type' => 'text', 'default' => 10,
    ],
    'venom_topics_limit' => [
        'dbfield' => 'venom_topics_limit', 'label' => 'Jumlah Topik',
        'type' => 'text', 'default' => 14,
    ],
    'venom_news_limit' => [
        'dbfield' => 'venom_news_limit', 'label' => 'Jumlah Berita',
        'type' => 'text', 'default' => 3,
    ],
    'venom_particles' => [
        'dbfield' => 'venom_particles', 'label' => 'Efek Partikel',
        'type' => 'dropdown', 'default' => 'stars',
        'data' => [
            ['stars','Bintang'],['snow','Salju'],['bubbles','Gelembung'],
            ['rain','Hujan'],['fireflies','Kunang-kunang'],['confetti','Confetti'],['none','Nonaktif'],
        ],
    ],
    'venom_animation_speed' => [
        'dbfield' => 'venom_animation_speed', 'label' => 'Kecepatan Animasi',
        'type' => 'dropdown', 'default' => 'normal',
        'data' => [['slow','Lambat'],['normal','Normal'],['fast','Cepat']],
    ],
];