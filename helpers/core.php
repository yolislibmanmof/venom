<?php
/**
 * VENOM · Bootstrap, helper, dan data layer.
 * Semua query dibungkus aman: gagal = array kosong, bukan fatal error.
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
/* ── Language switcher: VENOM yang mengelola cookie (kompatibel SLiMS) ── */
if (isset($_GET['select_lang']) && is_string($_GET['select_lang'])
    && preg_match('/^[a-z]{2}(_[A-Z]{2})?$/', $_GET['select_lang'])) {
    $vsl = $_GET['select_lang'];
    $_COOKIE['select_lang'] = $vsl;
    if (isset($GLOBALS['sysconf']) && is_array($GLOBALS['sysconf'])) {
        $GLOBALS['sysconf']['default_lang'] = $vsl;
    }
    if (!headers_sent()) {
        setcookie('select_lang', $vsl, time() + 86400 * 30, '/');
        $vq = $_GET; unset($vq['select_lang']);
        header('Location: index.php' . ($vq ? '?' . http_build_query($vq) : ''));
        exit;
    }
}
// ═══════════════════════════════════════════════════════════════
// FIX CRITICAL: Definisikan GLOBAL variables untuk SLiMS 9.8.0
// HARUS ADA DI SINI agar terbaca oleh visitor.inc.php
// ═══════════════════════════════════════════════════════════════
if (defined('VENOM_LOADED')) return;
define('VENOM_LOADED', true);
define('VENOM_VER', '1.0.0');
define('VENOM_DIR', rtrim(dirname(__DIR__), '/\\') . DIRECTORY_SEPARATOR);
define('VENOM_KEY', basename(dirname(__DIR__)));

require_once VENOM_DIR . 'helpers/compat.php';
require_once VENOM_DIR . 'helpers/security.php';
require_once VENOM_DIR . 'helpers/icons.php';

define('VENOM_URL', venom_base_url() . 'template/' . VENOM_KEY . '/');

$GLOBALS['venom_config'] = require VENOM_DIR . 'helpers/config.php';
$GLOBALS['venom_lang']   = [];
if (is_file(VENOM_DIR . 'lang.php')) { include VENOM_DIR . 'lang.php'; }

require_once VENOM_DIR . 'helpers/ui.php';

Venom_Sec::boot();
Venom_Sec::headers();

/* ── Konfigurasi & aset ───────────────────────────────────────────── */

/**
 * Baca konfigurasi dengan prioritas:
 * 1) Nilai tersimpan Admin/DB ($sysconf['template'][...] atau $sysconf[...])
 * 2) helpers/config.php
 * 3) $default
 */
function venom_cfg($path, $default = null)
{
    /* Peta: path config → kunci opsi Admin (tinfo_schema) */
    static $map = [
        'ui.palette'                  => 'venom_theme_color',
        'ui.mode'                     => 'venom_mode',
        'ui.font'                     => 'venom_font_family',
        'ui.container'                => 'venom_container_width',
        'ui.radius'                   => 'venom_border_radius',
        'ui.glass'                    => 'venom_glass_effect',
        'ui.motion'                   => 'venom_motion',
        'custom_css'                  => 'venom_custom_css',
        'features.hero'               => 'venom_hero_show',
        'hero.interval'               => 'venom_hero_interval',
        'features.stats'              => 'venom_stats_show',
        'features.new_arrivals'       => 'venom_new_arrivals_show',
        'features.featured'           => 'venom_featured_show',
        'features.topics'             => 'venom_topics_show',
        'features.news'               => 'venom_news_show',
        'limits.new'                  => 'venom_new_arrivals_limit',
        'limits.featured'             => 'venom_featured_limit',
        'limits.topics'               => 'venom_topics_limit',
        'limits.news'                 => 'venom_news_limit',
        'particle.default'            => 'venom_particles',
        'hero.animation_speed'        => 'venom_animation_speed',
        'hero.animation'              => 'venom_hero_animation',
        'hero.background_style'       => 'venom_hero_background_style',
        'hero.background_custom'      => 'venom_hero_background_custom',
        'hero.cursor_icon'            => 'venom_cursor_icon',
        'hero.cursor_particles'       => 'venom_cursor_particles',
        'navbar.menu'                 => 'venom_navbar_menu',
        'navbar.languages'            => 'venom_language_visible',
        'features.mobile_bottom_nav'  => 'venom_mobile_nav_show',
        'features.back_to_top'        => 'venom_back_to_top',
        'features.fab'                => 'venom_fab_show',
        'features.palette_switcher'   => 'venom_palette_switcher_show',
        'features.floating_chat'      => 'venom_floating_chat_show',
        'features.live_ticker'        => 'venom_live_ticker_show',
        'features.prayer_times'       => 'venom_prayer_times_show',
        'prayer.city'                 => 'venom_prayer_city',
        'contact.wa'                  => 'venom_whatsapp_number',
        'contact.wa_text'             => 'venom_whatsapp_text',
        'social.facebook'             => 'venom_social_facebook',
        'social.instagram'            => 'venom_social_instagram',
        'social.youtube'              => 'venom_social_youtube',
        'social.twitter'              => 'venom_social_twitter',
        'contact.email'               => 'venom_contact_email',
        'contact.phone'               => 'venom_contact_phone',
        'contact.address'             => 'venom_contact_address',
    ];

    /* 1) Override dari Admin/DB (dukung dua bentuk penyimpanan SLiMS) */
    if (isset($map[$path])) {
        $sys = isset($GLOBALS['sysconf']) && is_array($GLOBALS['sysconf']) ? $GLOBALS['sysconf'] : [];
        $k   = $map[$path];
        $val = null;
        if (isset($sys['template']) && is_array($sys['template']) && array_key_exists($k, $sys['template']) && $sys['template'][$k] !== '' && $sys['template'][$k] !== null) {
            $val = $sys['template'][$k];
        } elseif (array_key_exists($k, $sys) && $sys[$k] !== '' && $sys[$k] !== null) {
            $val = $sys[$k];
        }
        if ($val !== null) return $val;
    }

    /* 2) Fallback ke helpers/config.php */
    $c = isset($GLOBALS['venom_config']) ? $GLOBALS['venom_config'] : [];
    foreach (explode('.', $path) as $seg) {
        if (!is_array($c) || !array_key_exists($seg, $c)) return $default;
        $c = $c[$seg];
    }
    return $c;
}

function venom_on($feature) { return (bool) venom_cfg('features.' . $feature, false); }

function venom_asset($rel)
{
    $rel  = ltrim(str_replace('..', '', $rel), '/');
    $file = VENOM_DIR . 'assets/' . $rel;
    $v    = is_file($file) ? filemtime($file) : VENOM_VER;
    return VENOM_URL . 'assets/' . $rel . '?v=' . $v;
}

/** Include part yang aman — file belum ada? diabaikan, tidak fatal. */
function venom_part($name, array $data = [])
{
    $name = str_replace(['..', '\\', "\0"], '', $name);
    $file = VENOM_DIR . 'parts/' . $name . '.php';
    if (!is_file($file)) return;
    if ($data) extract($data, EXTR_SKIP);
    include $file;
}

function venom_library_name()
{
    $n = Venom_Core::conf('library_name', '');
    return $n !== '' ? $n : venom_cfg('brand.name', 'VENOM');
}

function venom_title()
{
    $t = Venom_Core::get('page_title', '');
    if (is_string($t) && trim($t) !== '') return strip_tags($t);
    return venom_library_name() . ' — ' . venom_cfg('brand.tagline', 'OPAC');
}

function venom_lang_code()
{
    $l = isset($_GET['select_lang']) ? $_GET['select_lang']
       : (isset($_COOKIE['select_lang']) ? $_COOKIE['select_lang']
       : Venom_Core::conf('default_lang', 'id_ID'));
    return substr(preg_replace('/[^a-zA-Z_]/', '', (string) $l), 0, 2) ?: 'id';
}

function venom_t($key, $fallback = '')
{
    $lang = isset($GLOBALS['venom_lang']) ? $GLOBALS['venom_lang'] : [];
    $code = venom_lang_code();
    if (isset($lang[$code][$key])) return $lang[$code][$key];
    if (isset($lang['id'][$key]))  return $lang['id'][$key];
    return $fallback !== '' ? $fallback : $key;
}

function venom_num($n) { return number_format((float) $n, 0, ',', '.'); }

/* ── URL ──────────────────────────────────────────────────────────── */

function venom_detail_url($id) { return 'index.php?p=show_detail&id=' . (int) $id; }
function venom_search_url($q)  { return 'index.php?keywords=' . rawurlencode((string) $q) . '&search=Search'; }

/** Cover buku, fallback SVG inline (tanpa file eksternal). */
function venom_cover($image, $title = '')
{
    $image = basename((string) $image);
    if ($image !== '' && $image !== '.' && $image !== '..') {
        foreach (['images/docs/', 'images/'] as $dir) {
            $fs = (defined('SB') ? SB : '') . $dir . $image;
            if ($fs !== $dir . $image && @is_file($fs)) return venom_base_url() . $dir . rawurlencode($image);
        }
        return venom_base_url() . 'images/docs/' . rawurlencode($image);
    }
    $ini = function_exists('mb_substr') ? mb_substr(trim($title), 0, 1, 'UTF-8') : substr(trim($title), 0, 1);
    $ini = strtoupper($ini !== '' ? $ini : 'V');
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="420">'
         . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
         . '<stop offset="0" stop-color="#7c3aed"/><stop offset="1" stop-color="#22d3ee"/>'
         . '</linearGradient></defs><rect width="300" height="420" fill="url(#g)"/>'
         . '<text x="150" y="235" font-family="sans-serif" font-size="130" font-weight="700" '
         . 'fill="rgba(255,255,255,.9)" text-anchor="middle">' . htmlspecialchars($ini, ENT_QUOTES) . '</text></svg>';
    return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
}

/* ── Database (aman, read-only) ───────────────────────────────────── */

function venom_rows($sql)
{
    $db = Venom_Core::db();
    if (!$db) return [];
    try {
        $r = $db->query($sql);
        if (!$r || !is_object($r)) return [];
        $out = [];
        while ($row = $r->fetch_assoc()) $out[] = $row;
        if (method_exists($r, 'free')) @$r->free();
        return $out;
    } catch (Throwable $e) { return []; }
}

function venom_row($sql) { $r = venom_rows($sql); return $r ? $r[0] : []; }

function venom_val($sql, $default = 0)
{
    $r = venom_row($sql);
    if (!$r) return $default;
    $v = reset($r);
    return ($v === null) ? $default : $v;
}

function venom_has_table($t)
{
    static $cache = [];
    $t = preg_replace('/[^a-z0-9_]/', '', strtolower($t));
    if (isset($cache[$t])) return $cache[$t];
    return $cache[$t] = (bool) venom_rows("SHOW TABLES LIKE '" . Venom_Sec::sql($t) . "'");
}

/** Cache ringan di session (default 5 menit). */
function venom_cached($key, $ttl, callable $fn)
{
    $now = time();
    if (isset($_SESSION['venom_cache'][$key])
        && $_SESSION['venom_cache'][$key]['exp'] > $now) {
        return $_SESSION['venom_cache'][$key]['val'];
    }
    $val = $fn();
    $_SESSION['venom_cache'][$key] = ['exp' => $now + (int) $ttl, 'val' => $val];
    return $val;
}

/* ── Data koleksi ─────────────────────────────────────────────────── */

function venom_select_biblio()
{
    return "SELECT b.biblio_id, b.title, b.image, b.publish_year, b.isbn_issn, b.call_number,
                   b.input_date, b.promoted, p.publisher_name,
                   (SELECT GROUP_CONCAT(a.author_name ORDER BY ba.level SEPARATOR ', ')
                      FROM biblio_author ba JOIN mst_author a ON a.author_id = ba.author_id
                     WHERE ba.biblio_id = b.biblio_id) AS authors
              FROM biblio b
              LEFT JOIN mst_publisher p ON p.publisher_id = b.publisher_id";
}

function venom_new_arrivals($limit = 10)
{
    $limit = max(1, min(24, (int) $limit));
    return venom_cached('new' . $limit, 300, function () use ($limit) {
        return venom_rows(venom_select_biblio() . " WHERE b.opac_hide = 0
                          ORDER BY b.input_date DESC, b.biblio_id DESC LIMIT " . $limit);
    });
}

function venom_featured($limit = 10)
{
    $limit = max(1, min(24, (int) $limit));
    return venom_cached('feat' . $limit, 300, function () use ($limit) {
        $r = venom_rows(venom_select_biblio() . " WHERE b.opac_hide = 0 AND b.promoted = 1
                         ORDER BY b.last_update DESC LIMIT " . $limit);
        return $r ? $r : venom_new_arrivals($limit);
    });
}

function venom_biblio($id)
{
    $id = (int) $id;
    if ($id < 1) return [];
    $row = venom_row(venom_select_biblio() . " WHERE b.biblio_id = " . $id . " LIMIT 1");
    if (!$row) return [];
    $row['topics'] = venom_rows("SELECT t.topic FROM biblio_topic bt
                                 JOIN mst_topic t ON t.topic_id = bt.topic_id
                                 WHERE bt.biblio_id = " . $id . " ORDER BY bt.level LIMIT 12");
    $row['items']  = venom_rows("SELECT i.item_code, i.call_number, i.item_status_id,
                                        l.location_name
                                   FROM item i LEFT JOIN mst_location l ON l.location_id = i.location_id
                                  WHERE i.biblio_id = " . $id . " LIMIT 60");
    return $row;
}

function venom_topics($limit = 14)
{
    $limit = max(1, min(40, (int) $limit));
    return venom_cached('topics' . $limit, 600, function () use ($limit) {
        return venom_rows("SELECT t.topic, COUNT(bt.biblio_id) AS total
                             FROM biblio_topic bt JOIN mst_topic t ON t.topic_id = bt.topic_id
                         GROUP BY t.topic_id ORDER BY total DESC LIMIT " . $limit);
    });
}

function venom_news($limit = 3)
{
    if (!venom_has_table('content')) return [];
    $limit = max(1, min(12, (int) $limit));
    return venom_cached('news' . $limit, 300, function () use ($limit) {
        return venom_rows("SELECT content_id, content_title, content_desc, input_date
                             FROM content ORDER BY input_date DESC LIMIT " . $limit);
    });
}

function venom_stats()
{
    return venom_cached('stats', 300, function () {
        $s = ['titles' => 0, 'items' => 0, 'members' => 0, 'visits' => 0, 'loans' => 0];
        if (venom_has_table('biblio')) $s['titles']  = (int) venom_val("SELECT COUNT(*) FROM biblio WHERE opac_hide = 0");
        if (venom_has_table('item'))   $s['items']   = (int) venom_val("SELECT COUNT(*) FROM item");
        if (venom_has_table('member')) $s['members'] = (int) venom_val("SELECT COUNT(*) FROM member");
        if (venom_has_table('visitor_count')) {
            $s['visits'] = (int) venom_val("SELECT COUNT(*) FROM visitor_count WHERE DATE(checkin_date) = CURDATE()");
        }
        if (venom_has_table('loan')) {
            $s['loans'] = (int) venom_val("SELECT COUNT(*) FROM loan WHERE is_lent = 1 AND is_return = 0");
        }
        return $s;
    });
}

/* ── Klasifikasi DDC (000-900) — hitung koleksi per kelas dari call number ── */
if (!function_exists('venom_ddc_counts')) {
    function venom_ddc_counts() {
        return venom_cached('ddc_counts', 600, function () {
            $counts = array_fill(0, 10, 0);
            $db = Venom_Core::db();
            if (!$db) return $counts;
            $q = $db->query("SELECT LEFT(call_number,1) AS d, COUNT(*) AS c FROM biblio
                              WHERE call_number IS NOT NULL AND call_number REGEXP '^[0-9]'
                              GROUP BY d");
            if ($q) {
                while ($r = $q->fetch_assoc()) {
                    $d = (int) $r['d'];
                    if ($d >= 0 && $d <= 9) $counts[$d] = (int) $r['c'];
                }
            }
            return $counts;
        });
    }
}

/* ── Definisi 10 kelas DDC (warna + ikon VENOM native) ── */
if (!function_exists('venom_ddc_classes')) {
    function venom_ddc_classes() {
        return [
            ['code' => 0, 'title' => 'Komputer & Umum',        'desc' => 'Informatika, ensiklopedia, jurnalisme', 'icon' => 'code',     'color' => '#2563eb'],
            ['code' => 1, 'title' => 'Filsafat & Psikologi',   'desc' => 'Pengembangan diri, etika, logika',    'icon' => 'sparkles', 'color' => '#7c3aed'],
            ['code' => 2, 'title' => 'Agama',                  'desc' => 'Teologi, kitab suci, sejarah agama',  'icon' => 'moon',     'color' => '#059669'],
            ['code' => 3, 'title' => 'Ilmu Sosial',            'desc' => 'Ekonomi, hukum, pendidikan',          'icon' => 'users',    'color' => '#b45309'],
            ['code' => 4, 'title' => 'Bahasa',                 'desc' => 'Linguistik, kamus, sastra bahasa',    'icon' => 'quote',    'color' => '#e11d48'],
            ['code' => 5, 'title' => 'Sains & Matematika',     'desc' => 'Fisika, biologi, kimia',              'icon' => 'fire',     'color' => '#0d9488'],
            ['code' => 6, 'title' => 'Teknologi',              'desc' => 'Kesehatan, teknik, kewirausahaan',    'icon' => 'layers',   'color' => '#4f46e5'],
            ['code' => 7, 'title' => 'Seni & Olahraga',        'desc' => 'Seni rupa, musik, olahraga',          'icon' => 'palette',  'color' => '#db2777'],
            ['code' => 8, 'title' => 'Sastra',                 'desc' => 'Novel, puisi, drama',                 'icon' => 'book',     'color' => '#ea580c'],
            ['code' => 9, 'title' => 'Sejarah & Geografi',     'desc' => 'Biografi, peta, sejarah dunia',       'icon' => 'pin',      'color' => '#0891b2'],
        ];
    }
}

/* ── 3 Grup Kategori (GMD, Coll Type, Location) ── */
if (!function_exists('venom_category_groups')) {
    function venom_category_groups($limit = 10) {
        return venom_cached('cat_groups_' . $limit, 600, function () use ($limit) {
            $out = [];
            // Grup 1: Type of Material (GMD)
            $rows = venom_rows("SELECT g.gmd_name, COUNT(*) AS c FROM biblio b
                                 INNER JOIN mst_gmd g ON g.gmd_id=b.gmd_id
                                 WHERE b.opac_hide=0
                                 GROUP BY g.gmd_id ORDER BY c DESC LIMIT " . $limit);
            $out[] = ['title' => 'Tipe Material', 'param' => 'gmd', 'icon' => 'layers', 'rows' => $rows, 'label' => 'gmd_name'];

            // Grup 2: Collection Type
            $rows = venom_rows("SELECT t.coll_type_name, COUNT(DISTINCT i.biblio_id) AS c FROM item i
                                 INNER JOIN mst_coll_type t ON t.coll_type_id=i.coll_type_id
                                 INNER JOIN biblio b ON b.biblio_id=i.biblio_id AND b.opac_hide=0
                                 GROUP BY t.coll_type_id ORDER BY c DESC LIMIT " . $limit);
            $out[] = ['title' => 'Jenis Koleksi', 'param' => 'colltype', 'icon' => 'tag', 'rows' => $rows, 'label' => 'coll_type_name'];

            // Grup 3: Location
            $rows = venom_rows("SELECT l.location_name, COUNT(DISTINCT i.biblio_id) AS c FROM item i
                                 INNER JOIN mst_location l ON l.location_id=i.location_id
                                 INNER JOIN biblio b ON b.biblio_id=i.biblio_id AND b.opac_hide=0
                                 GROUP BY l.location_id ORDER BY c DESC LIMIT " . $limit);
            $out[] = ['title' => 'Lokasi', 'param' => 'location', 'icon' => 'pin', 'rows' => $rows, 'label' => 'location_name'];

            return $out;
        });
    }
}

/* ── Popular Collections (berdasarkan jumlah loan, beda dari Featured) ── */
if (!function_exists('venom_popular_collections')) {
    function venom_popular_collections($limit = 6) {
        $limit = max(1, min(24, (int) $limit));
        return venom_cached('popular_' . $limit, 300, function () use ($limit) {
            return venom_rows(venom_select_biblio() . "
                LEFT JOIN (SELECT i.biblio_id, COUNT(*) AS loans FROM loan l
                            INNER JOIN item i ON i.item_code=l.item_code
                            GROUP BY i.biblio_id) AS loan_stats ON loan_stats.biblio_id=b.biblio_id
                WHERE b.opac_hide = 0
                ORDER BY COALESCE(loan_stats.loans,0) DESC, b.last_update DESC
                LIMIT " . $limit);
        });
    }
}

/* ── Collection Type Pills (5 tipe terbesar) ── */
if (!function_exists('venom_collection_type_pills')) {
    function venom_collection_type_pills($limit = 5) {
        return venom_cached('ctype_pills_' . $limit, 600, function () use ($limit) {
            return venom_rows("SELECT t.coll_type_name, COUNT(DISTINCT i.biblio_id) AS c
                                 FROM mst_coll_type t
                                 LEFT JOIN item i ON i.coll_type_id=t.coll_type_id
                                 INNER JOIN biblio b ON b.biblio_id=i.biblio_id AND b.opac_hide=0
                                 GROUP BY t.coll_type_id
                                 ORDER BY c DESC, t.coll_type_name ASC LIMIT " . $limit);
        });
    }
}

/* ── Debug probe ──────────────────────────────────────────────────── */

function venom_debug_panel()
{
    if (!venom_cfg('debug', false) || !isset($_GET['venom_debug'])) return '';
    $keys = array_keys(Venom_Core::$vars);
    sort($keys);
    $html = '<div class="v-wrap"><div class="v-card v-pad v-debug"><h3>VENOM Debug Probe</h3>'
          . '<p><b>DB:</b> ' . (Venom_Core::db() ? 'terhubung ✔' : 'TIDAK ada ✘')
          . ' &nbsp; <b>page:</b> ' . venom_e(venom_page())
          . ' &nbsp; <b>content:</b> ' . (Venom_Core::content() !== '' ? 'ada ✔' : 'kosong ✘') . '</p>'
          . '<p><b>Variabel dari inti:</b><br><code>' . venom_e(implode(', ', $keys)) . '</code></p>'
          . '<p><b>Tabel:</b> biblio ' . (venom_has_table('biblio') ? '✔' : '✘')
          . ' · item ' . (venom_has_table('item') ? '✔' : '✘')
          . ' · visitor_count ' . (venom_has_table('visitor_count') ? '✔' : '✘')
          . ' · content ' . (venom_has_table('content') ? '✔' : '✘') . '</p></div></div>';
    return $html;
}