<?php
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access this file directly");

if (!function_exists('venomFilterDisabledTinfoOptions')) {
    function venomFilterDisabledTinfoOptions($options) {
        $filtered = [];
        foreach ((array) $options as $key => $def) {
            if (!is_array($def)) continue;
            if (isset($def['disabled']) && $def['disabled']) continue;
            $filtered[$key] = $def;
        }
        return $filtered;
    }
}