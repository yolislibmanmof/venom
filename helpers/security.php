<?php
/**
 * VENOM · Security Layer v2
 * PENTING: tidak lagi memblokir POST global. CSRF hanya untuk form milik VENOM.
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

final class Venom_Sec
{
    const SESSION_KEY = 'venom_sec';

    public static function boot()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = ['tokens' => [], 'hits' => []];
        }
    }

    /** Header keamanan (tidak memasang CSP agar tidak merusak script inti SLiMS). */
    public static function headers()
    {
        if (headers_sent()) return;
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-Frame-Options: SAMEORIGIN');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }

    /** Escape output (XSS). Rekursif untuk array. */
    public static function e($v)
    {
        if (is_array($v)) return array_map([self::class, 'e'], $v);
        if ($v === null || is_bool($v)) return '';
        return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Bersihkan teks input pengguna. */
    public static function text($v, $max = 200)
    {
        $v = trim(strip_tags((string) $v));
        $v = preg_replace('/\s+/u', ' ', $v);
        if (function_exists('mb_substr')) return mb_substr($v, 0, $max, 'UTF-8');
        return substr($v, 0, $max);
    }

    public static function int($v) { return (int) preg_replace('/[^0-9\-]/', '', (string) $v); }

    /** Escape nilai untuk SQL (kompatibel mysqli & wrapper SLiMS). */
    public static function sql($v)
    {
        $db = Venom_Core::db();
        $v  = (string) $v;
        if ($db && method_exists($db, 'escape_string')) return $db->escape_string($v);
        if ($db && method_exists($db, 'real_escape_string')) return $db->real_escape_string($v);
        return addslashes($v);
    }

    /** URL aman: hanya relatif, http, https, mailto, tel. */
    public static function url($u)
    {
        $u = trim((string) $u);
        if ($u === '') return '#';
        if (preg_match('~^(https?://|mailto:|tel:|/|\./|\#|\?|index\.php)~i', $u) && !preg_match('~[\r\n]~', $u)) {
            return self::e($u);
        }
        return '#';
    }

    /** Token CSRF per-aksi (bukan per-halaman). */
    public static function token($action = 'default')
    {
        self::boot();
        if (empty($_SESSION[self::SESSION_KEY]['tokens'][$action])) {
            $_SESSION[self::SESSION_KEY]['tokens'][$action] = bin2hex(random_bytes(24));
        }
        return $_SESSION[self::SESSION_KEY]['tokens'][$action];
    }

    public static function check($action, $token)
    {
        self::boot();
        $saved = isset($_SESSION[self::SESSION_KEY]['tokens'][$action])
            ? $_SESSION[self::SESSION_KEY]['tokens'][$action] : '';
        return ($saved !== '' && is_string($token) && hash_equals($saved, $token));
    }

    /** Rate limit sederhana berbasis session. */
    public static function rate($key, $max = 5, $window = 60)
    {
        self::boot();
        $now  = time();
        $hits =& $_SESSION[self::SESSION_KEY]['hits'];
        if (!isset($hits[$key]) || $hits[$key]['t'] < $now) $hits[$key] = ['t' => $now + $window, 'n' => 0];
        $hits[$key]['n']++;
        return $hits[$key]['n'] <= $max;
    }
}

/* Shortcut global */
function venom_e($v) { return Venom_Sec::e($v); }
function venom_u($v) { return Venom_Sec::url($v); }