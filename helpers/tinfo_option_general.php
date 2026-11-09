<?php
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access this file directly");

return [
    'venom_theme_color' => [
        'dbfield' => 'venom_theme_color',
        'label' => 'Palet Tema',
        'type' => 'dropdown',
        'default' => 'venom',
        'data' => [
            ['venom', 'Venom Violet'],
            ['emerald', 'Emerald Forest'],
            ['crimson', 'Crimson Ember'],
            ['ocean', 'Deep Ocean'],
            ['mono', 'Monochrome Pro'],
            ['rose', 'Rose Dawn'],
            ['sunset', 'Sunset Blaze'],
            ['midnight', 'Midnight Sky'],
            ['cyberpunk', 'Cyberpunk'],
            ['forest', 'Forest Deep'],
            ['custom', 'Kustom'],
        ],
    ],
    'venom_palette_custom' => [
        'dbfield' => 'venom_palette_custom',
        'label' => 'Palet Kustom (TERANG | GELAP)',
        'type' => 'longtext',
        'default' => '#7c3aed;#22d3ee;#a855f7;#0b1020;#1a1a2e;#f8fafc;#64748b | #7c3aed;#22d3ee;#a855f7;#0b1020;#1a1a2e;#f1f5f9;#94a3b8',
        'max' => 500,
    ],
    'venom_mode' => [
        'dbfield' => 'venom_mode',
        'label' => 'Mode Warna',
        'type' => 'dropdown',
        'default' => 'auto',
        'data' => [['auto','Otomatis (ikuti sistem)'],['light','Selalu Terang'],['dark','Selalu Gelap']],
    ],
    'venom_font_family' => [
        'dbfield' => 'venom_font_family',
        'label' => 'Font',
        'type' => 'dropdown',
        'default' => 'system',
        'data' => [
            ['system','System UI'],['inter','Inter'],['poppins','Poppins'],
            ['roboto','Roboto'],['playfair','Playfair Display (serif)'],
        ],
    ],
    'venom_container_width' => [
        'dbfield' => 'venom_container_width',
        'label' => 'Lebar Kontainer (px)',
        'type' => 'text',
        'default' => 1240,
    ],
    'venom_border_radius' => [
        'dbfield' => 'venom_border_radius',
        'label' => 'Radius Sudut (px)',
        'type' => 'text',
        'default' => 18,
    ],
    'venom_glass_effect' => [
        'dbfield' => 'venom_glass_effect',
        'label' => 'Efek Glassmorphism',
        'type' => 'dropdown',
        'default' => 1,
        'data' => [[1,'Aktif'],[0,'Nonaktif']],
    ],
    'venom_motion' => [
        'dbfield' => 'venom_motion',
        'label' => 'Animasi UI',
        'type' => 'dropdown',
        'default' => 1,
        'data' => [[1,'Aktif'],[0,'Nonaktif']],
    ],
    'venom_custom_css' => [
        'dbfield' => 'venom_custom_css',
        'label' => 'Custom CSS Tambahan',
        'type' => 'longtext',
        'default' => '',
        'max' => 4000,
    ],
];