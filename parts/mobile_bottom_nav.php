<?php
/**
 * VENOM · Mobile Bottom Navigation
 * Enhanced mobile UX
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

$current_page = venom_page();

$nav_items = [
    ['index.php', 'Beranda', 'book', 'home'],
    ['index.php?p=browse', 'Jelajah', 'grid', 'browse'],
    ['index.php?p=news', 'Berita', 'news', 'news'],
    ['index.php?p=member', 'Akun', 'user', 'member'],
    ['index.php?p=visitor', 'Statistik', 'chart', 'visitor'],
];
?>

<nav class="v-mobile-nav" id="vMobileNav" aria-label="Navigasi mobile">
    <div class="v-mobile-nav-inner">
        <?php foreach ($nav_items as $item): 
            list($url, $label, $icon, $page) = $item;
            $is_active = ($current_page === $page) || ($page === 'home' && $current_page === 'home');
        ?>
        <a href="<?= venom_u($url) ?>" 
           class="v-mobile-nav-item<?= $is_active ? ' is-active' : '' ?>"
           aria-current="<?= $is_active ? 'page' : 'false' ?>">
            <?= venom_icon($icon, 22) ?>
            <span><?= venom_e($label) ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</nav>