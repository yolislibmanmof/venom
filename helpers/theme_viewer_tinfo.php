<?php
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access this file directly");

if (!function_exists('venomThemeViewerOptions')) {
    function venomThemeViewerOptions($sysconf = []) {
        static $cache = null;
        if ($cache !== null) return $cache;
        $schema = require __DIR__ . '/tinfo_schema.php';
        $groupLabels = [
            'general' => 'Identitas Tampilan', 'hero' => 'Beranda & Efek',
            'navbar' => 'Navigasi & Bahasa', 'footer' => 'Footer & Fitur',
        ];
        $options = [];
        foreach ($schema as $group => $defs) {
            foreach ($defs as $key => $def) {
                $db = $key;
                $default = $def['default'] ?? '';
                $val = $sysconf['template'][$db] ?? $default;
                if (is_array($val) || is_object($val)) $val = json_encode($val);
                $options[] = [
                    'dbfield' => $db,
                    'label' => $def['label'] ?? $db,
                    'type' => strtolower($def['type'] ?? 'text'),
                    'value' => (string) $val,
                    'default' => (string) $default,
                    'max' => $def['max'] ?? 250,
                    'choices' => $def['data'] ?? [],
                    'group' => $group,
                    'groupLabel' => $groupLabels[$group] ?? $group,
                ];
            }
        }
        return $cache = $options;
    }
}