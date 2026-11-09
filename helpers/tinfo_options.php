<?php
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access this file directly");

require_once __DIR__ . '/tinfo_options_helper.php';

$theme = $sysconf['template']['theme'] ?? 'venom';
$schema = require __DIR__ . '/tinfo_schema.php';

$venom_options = [];
foreach ($schema as $group => $defs) {
    foreach ($defs as $key => $def) {
        $def['dbfield'] = $key;
        $def['group'] = $group;
        $venom_options[$key] = $def;
    }
}
$sysconf['template']['option'][$theme] = venomFilterDisabledTinfoOptions($venom_options);

require_once __DIR__ . '/tinfo_customizer.php';