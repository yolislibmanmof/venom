<?php
/**
 * VENOM · Compatibility Bridge.
 * Membaca variabel/objek inti SLiMS secara aman tanpa mengasumsikan satu versi.
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

final class Venom_Core
{
    public static $vars  = [];
    public static $taken = false;

    public static function register(array $v) { self::$vars = $v; }

    public static function get($key, $default = '')
    {
        if (isset(self::$vars[$key]) && self::$vars[$key] !== '') return self::$vars[$key];
        if (isset($GLOBALS[$key]) && $GLOBALS[$key] !== '')      return $GLOBALS[$key];
        return $default;
    }

    /** Konten utama hasil render inti SLiMS (coba beberapa nama kandidat). */
    public static function content()
    {
        foreach (['main_content', 'content', 'content_str', 'page_content', 'html_content'] as $k) {
            $v = self::get($k, '');
            if (is_string($v) && trim($v) !== '') return $v;
        }
        return '';
    }

    public static function conf($key, $default = null)
    {
        $s = self::get('sysconf', []);
        if (!is_array($s)) $s = [];
        return array_key_exists($key, $s) ? $s[$key] : $default;
    }

    /** Objek database SLiMS (mysqli / wrapper). */
    public static function db()
    {
        $db = self::get('dbs', null);
        return (is_object($db) && method_exists($db, 'query')) ? $db : null;
    }
}

/** Base URL aplikasi (tanpa bergantung konstanta tertentu). */
function venom_base_url()
{
    static $base = null;
    if ($base !== null) return $base;
    if (defined('SWB') && SWB) return $base = rtrim(SWB, '/') . '/';
    $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/index.php';
    $dir    = rtrim(str_replace('\\', '/', dirname($script)), '/');
    return $base = $dir . '/';
}

/** Halaman aktif menurut parameter inti SLiMS. */
function venom_page()
{
    $p = isset($_GET['p']) ? (string) $_GET['p'] : '';
    $p = preg_replace('/[^a-z_]/', '', strtolower($p));
    return $p !== '' ? $p : 'home';
}

function venom_is_search()
{
    foreach (['keywords', 'title', 'author', 'subject', 'isbn', 'query'] as $k) {
        if (isset($_GET[$k]) && trim((string) $_GET[$k]) !== '') return true;
    }
    return false;
}

function venom_is_home()
{
    return venom_page() === 'home' && !venom_is_search();
}

/**
 * Ambil konten inti SEKALI saja. Mencegah duplikasi bila file template
 * juga dipanggil oleh inti SLiMS.
 */
function venom_core_content()
{
    if (Venom_Core::$taken) return '';
    $c = Venom_Core::content();
    if ($c !== '') Venom_Core::$taken = true;
    return $c;
}

/** Blok <head> tambahan dari SLiMS (dedupe). */
function venom_core_head()
{
    $out = [];
    foreach (['metadata', 'header_info', 'html_header'] as $k) {
        $v = Venom_Core::get($k, '');
        if (is_string($v) && trim($v) !== '' && !in_array($v, $out, true)) $out[] = $v;
    }
    return implode("\n", $out);
}

/** Blok script penutup dari SLiMS (dedupe). */
function venom_core_foot()
{
    $out = [];
    foreach (['footer_info', 'html_footer', 'js_content'] as $k) {
        $v = Venom_Core::get($k, '');
        if (is_string($v) && trim($v) !== '' && !in_array($v, $out, true)) $out[] = $v;
    }
    return implode("\n", $out);
}