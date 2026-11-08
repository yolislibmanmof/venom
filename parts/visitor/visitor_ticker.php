<?php
/**
 * VENOM · Live Visitor Ticker
 * Real-time statistics with AJAX refresh
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

// Get initial stats
$stats = venom_stats();
?>

<div class="v-live-ticker" id="vLiveTicker" data-refresh="60">
    <div class="v-ticker-label">
        <span class="v-ticker-dot"></span>
        <span>Live Statistics</span>
    </div>
    
    <div class="v-ticker-track">
        <div class="v-ticker-content" id="vTickerContent">
            <!-- First set -->
            <div class="v-ticker-stat">
                <?= venom_icon('book', 16) ?>
                <span><?= venom_num($stats['titles']) ?></span>
                <span>Judul Koleksi</span>
            </div>
            <div class="v-ticker-stat">
                <?= venom_icon('layers', 16) ?>
                <span><?= venom_num($stats['items']) ?></span>
                <span>Eksemplar</span>
            </div>
            <div class="v-ticker-stat">
                <?= venom_icon('users', 16) ?>
                <span><?= venom_num($stats['members']) ?></span>
                <span>Anggota</span>
            </div>
            <div class="v-ticker-stat">
                <?= venom_icon('chart', 16) ?>
                <span><?= venom_num($stats['visits']) ?></span>
                <span>Kunjungan Hari Ini</span>
            </div>
            <div class="v-ticker-stat">
                <?= venom_icon('clock', 16) ?>
                <span><?= venom_num($stats['loans']) ?></span>
                <span>Sedang Dipinjam</span>
            </div>
            
            <!-- Duplicate for seamless loop -->
            <div class="v-ticker-stat">
                <?= venom_icon('book', 16) ?>
                <span><?= venom_num($stats['titles']) ?></span>
                <span>Judul Koleksi</span>
            </div>
            <div class="v-ticker-stat">
                <?= venom_icon('layers', 16) ?>
                <span><?= venom_num($stats['items']) ?></span>
                <span>Eksemplar</span>
            </div>
            <div class="v-ticker-stat">
                <?= venom_icon('users', 16) ?>
                <span><?= venom_num($stats['members']) ?></span>
                <span>Anggota</span>
            </div>
            <div class="v-ticker-stat">
                <?= venom_icon('chart', 16) ?>
                <span><?= venom_num($stats['visits']) ?></span>
                <span>Kunjungan Hari Ini</span>
            </div>
            <div class="v-ticker-stat">
                <?= venom_icon('clock', 16) ?>
                <span><?= venom_num($stats['loans']) ?></span>
                <span>Sedang Dipinjam</span>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Live ticker updater
 * Fetches new stats via AJAX periodically
 */
(function() {
    var ticker = document.getElementById('vLiveTicker');
    if (!ticker) return;
    
    var refreshInterval = parseInt(ticker.getAttribute('data-refresh')) || 60;
    var content = document.getElementById('vTickerContent');
    
    function fetchStats() {
        // Create a simple AJAX request to get fresh stats
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'index.php?p=visitor&ajax=stats', true);
        xhr.timeout = 5000;
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    updateTicker(data);
                } catch (e) {
                    // Invalid JSON, ignore
                }
            }
        };
        
        xhr.onerror = function() {
            // Silent fail
        };
        
        xhr.send();
    }
    
    function updateTicker(data) {
        if (!content || !data) return;
        
        var stats = [
            {icon: 'book', value: data.titles, label: 'Judul Koleksi'},
            {icon: 'layers', value: data.items, label: 'Eksemplar'},
            {icon: 'users', value: data.members, label: 'Anggota'},
            {icon: 'chart', value: data.visits, label: 'Kunjungan Hari Ini'},
            {icon: 'clock', value: data.loans, label: 'Sedang Dipinjam'}
        ];
        
        // Update first set
        var html = '';
        stats.forEach(function(stat) {
            html += '<div class="v-ticker-stat">' +
                getIcon(stat.icon) +
                '<span class="v-ticker-value">' + stat.value.toLocaleString('id-ID') + '</span>' +
                '<span>' + stat.label + '</span>' +
                '</div>';
        });
        
        // Duplicate for seamless loop
        var htmlDup = html;
        
        content.innerHTML = html + htmlDup;
    }
    
    function getIcon(name) {
        var icons = {
            'book': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5a2 2 0 0 1 2-2h8v18H6a2 2 0 0 1-2-2z"/><path d="M14 3h6v18h-6"/></svg>',
            'layers': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/></svg>',
            'users': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2 21c0-3.5 3-5.5 7-5.5s7 2 7 5.5"/><path d="M17 5.5a3.5 3.5 0 0 1 0 7M18 21c0-2.5-.6-4-2-5"/></svg>',
            'chart': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>',
            'clock': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>'
        };
        return icons[name] || '';
    }
    
    // Auto refresh
    if (refreshInterval > 0) {
        setInterval(fetchStats, refreshInterval * 1000);
    }
})();
</script>