<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';
foreach (['biblio', 'record', 'data', 'row'] as $k) {
    if (isset($$k) && is_array($$k) && isset($$k['biblio_id'])) { echo venom_card_biblio($$k); return; }
}