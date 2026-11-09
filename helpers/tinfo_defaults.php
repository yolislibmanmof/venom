<?php
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access this file directly");

/* Prefix 'venom_' untuk field khusus VENOM (anti-bentrok antar tema) */
$sysconf['template']['base'] = 'php';
$sysconf['template']['responsive'] = true;

/* Brand & Identitas */
$sysconf['template']['venom_theme_color'] = 'venom';
$sysconf['template']['venom_palette_custom'] = '#7c3aed;#22d3ee;#a855f7;#0b1020;#1a1a2e;#f8fafc;#64748b | #7c3aed;#22d3ee;#a855f7;#0b1020;#1a1a2e;#f1f5f9;#94a3b8';
$sysconf['template']['venom_font_family'] = 'system';
$sysconf['template']['venom_mode'] = 'auto';
$sysconf['template']['venom_container_width'] = 1240;
$sysconf['template']['venom_border_radius'] = 18;

/* Background & FX */
$sysconf['template']['venom_background_animation'] = 'none';
$sysconf['template']['venom_animation_speed'] = 'normal';
$sysconf['template']['venom_particles'] = 'stars';
$sysconf['template']['venom_motion'] = 1;
$sysconf['template']['venom_glass_effect'] = 1;

/* Hero & Homepage */
$sysconf['template']['venom_hero_show'] = 1;
$sysconf['template']['venom_hero_interval'] = 6000;
$sysconf['template']['venom_stats_show'] = 1;
$sysconf['template']['venom_new_arrivals_show'] = 1;
$sysconf['template']['venom_featured_show'] = 1;
$sysconf['template']['venom_topics_show'] = 1;
$sysconf['template']['venom_news_show'] = 1;
$sysconf['template']['venom_new_arrivals_limit'] = 10;
$sysconf['template']['venom_featured_limit'] = 10;
$sysconf['template']['venom_topics_limit'] = 14;
$sysconf['template']['venom_news_limit'] = 3;

/* Navigation & Footer */
$sysconf['template']['venom_navbar_menu'] = 'Beranda | index.php | home ; Koleksi | index.php?keywords=&search=Search | book ; Berita | index.php?p=news | news ; Profil | index.php?p=libinfo | layers';
$sysconf['template']['venom_language_visible'] = 'id_ID,en_US';
$sysconf['template']['venom_mobile_nav_show'] = 1;
$sysconf['template']['venom_back_to_top'] = 1;

/* Fitur Tambahan */
$sysconf['template']['venom_fab_show'] = 1;
$sysconf['template']['venom_palette_switcher_show'] = 1;
$sysconf['template']['venom_floating_chat_show'] = 1;
$sysconf['template']['venom_live_ticker_show'] = 0;
$sysconf['template']['venom_prayer_times_show'] = 0;
$sysconf['template']['venom_prayer_city'] = 'Jakarta';
$sysconf['template']['venom_prayer_method'] = 11;
$sysconf['template']['venom_whatsapp_number'] = '';
$sysconf['template']['venom_whatsapp_text'] = 'Halo, ada yang bisa kami bantu?';

/* Sosial media */
$sysconf['template']['venom_social_facebook'] = '';
$sysconf['template']['venom_social_instagram'] = '';
$sysconf['template']['venom_social_youtube'] = '';
$sysconf['template']['venom_social_twitter'] = '';

/* Kontak */
$sysconf['template']['venom_contact_email'] = '';
$sysconf['template']['venom_contact_phone'] = '';
$sysconf['template']['venom_contact_address'] = '';

/* Custom CSS */
$sysconf['template']['venom_custom_css'] = '';

/* Classic (bawaan SLiMS, tetap dipertahankan) */
$sysconf['template']['classic_popular_collection'] = 1;
$sysconf['template']['classic_popular_collection_item'] = 6;
$sysconf['template']['classic_new_collection'] = 1;
$sysconf['template']['classic_new_collection_item'] = 6;
$sysconf['template']['classic_top_reader'] = 1;
$sysconf['template']['classic_homepage_section_order'] = 'topic;news;popular;new-collection;top-reader';
$sysconf['template']['classic_search_size'] = 'medium';
$sysconf['template']['classic_footer_show'] = 1;
$sysconf['template']['classic_member_area'] = 1;
$sysconf['template']['classic_color_toggle'] = 'auto_show';