<?php
/**
 * VENOM · Prayer Time Widget
 * Menampilkan jadwal sholat dengan API atau fallback
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

// Configuration - Definisikan di level global file
$prayer_city = venom_cfg('prayer.city', 'Jakarta');
$prayer_method = venom_cfg('prayer.method', 11); // 11 = Kemenag RI
$prayer_cache_time = 3600; // 1 hour

/**
 * Get prayer times from API or fallback
 */
function get_prayer_times($city, $method) {
    global $prayer_cache_time;
    
    $cache_key = 'prayer_' . md5($city . '_' . $method . '_' . date('Y-m-d'));
    
    // Try cache first (session-based)
    if (!isset($_SESSION['venom_cache'])) {
        $_SESSION['venom_cache'] = [];
    }
    
    if (isset($_SESSION['venom_cache'][$cache_key]) && 
        $_SESSION['venom_cache'][$cache_key]['exp'] > time()) {
        return $_SESSION['venom_cache'][$cache_key]['data'];
    }
    
    // Try API
    $times = null;
    try {
        $url = 'https://api.aladhan.com/v1/timingsByCity?' . http_build_query([
            'city' => $city,
            'country' => 'Indonesia',
            'method' => $method,
            'adjustment' => 1
        ]);
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['code']) && $data['code'] === 200) {
                $times = $data['data']['timings'];
            }
        }
    } catch (Exception $e) {
        // Silent fail
    }
    
    // Fallback to calculated times if API fails
    if (!$times) {
        $times = calculate_prayer_times_fallback();
    }
    
    // Cache it
    if (isset($_SESSION['venom_cache'])) {
        $_SESSION['venom_cache'][$cache_key] = [
            'exp' => time() + $prayer_cache_time,
            'data' => $times
        ];
    }
    
    return $times;
}

/**
 * Fallback prayer time calculation (simplified)
 * Based on Jakarta timezone with fixed offsets
 */
function calculate_prayer_times_fallback() {
    // Default times for Jakarta (can be adjusted)
    return [
        'Fajr' => '04:45',
        'Sunrise' => '06:00',
        'Dhuhr' => '12:00',
        'Asr' => '15:15',
        'Maghrib' => '18:00',
        'Isha' => '19:15'
    ];
}

/**
 * Get next prayer time
 */
function get_next_prayer($times) {
    $now = time();
    $current_time = date('H:i', $now);
    
    $prayer_order = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
    $prayer_labels = [
        'Fajr' => 'Subuh',
        'Sunrise' => 'Terbit',
        'Dhuhr' => 'Dzuhur',
        'Asr' => 'Ashar',
        'Maghrib' => 'Maghrib',
        'Isha' => 'Isya'
    ];
    
    $next_prayer = null;
    $next_time = null;
    $countdown = 0;
    
    foreach ($prayer_order as $prayer) {
        if (!isset($times[$prayer])) continue;
        
        $prayer_time = $times[$prayer];
        
        if ($prayer_time > $current_time) {
            $next_prayer = $prayer_labels[$prayer] ?? $prayer;
            $next_time = $prayer_time;
            
            // Calculate countdown
            $prayer_timestamp = strtotime($prayer_time);
            $countdown = $prayer_timestamp - $now;
            break;
        }
    }
    
    // If no next prayer today, tomorrow's Fajr
    if (!$next_prayer && isset($times['Fajr'])) {
        $next_prayer = 'Subuh';
        $next_time = $times['Fajr'];
        $countdown = strtotime('tomorrow ' . $next_time) - $now;
    }
    
    return [
        'name' => $next_prayer,
        'time' => $next_time,
        'countdown' => $countdown
    ];
}

/**
 * Format countdown string
 */
function format_countdown($seconds) {
    if ($seconds < 0) $seconds = 0;
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    return sprintf('%02d:%02d', $hours, $minutes);
}

// Get data
$prayer_times = get_prayer_times($prayer_city, $prayer_method);
$next = get_next_prayer($prayer_times);
?>

<div class="v-prayer-widget" id="vPrayerWidget" data-prayer-times='<?= venom_e(json_encode($prayer_times)) ?>'>
    <div class="v-prayer-title">
        <span class="v-prayer-icon"><?= venom_icon('moon', 18) ?></span>
        <span>Jadwal Sholat <small><?= venom_e($prayer_city) ?></small></span>
    </div>
    
    <div class="v-prayer-times">
        <?php 
        $labels = [
            'Fajr' => 'Subuh',
            'Dhuhr' => 'Dzuhur',
            'Asr' => 'Ashar',
            'Maghrib' => 'Maghrib',
            'Isha' => 'Isya'
        ];
        
        foreach ($labels as $key => $label): 
            $is_next = ($next['name'] === $label);
        ?>
        <div class="v-prayer-item<?= $is_next ? ' is-next' : '' ?>" data-prayer="<?= venom_e($key) ?>">
            <span class="v-prayer-name"><?= $label ?></span>
            <span class="v-prayer-time"><?= venom_e($prayer_times[$key] ?? '--:--') ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if ($next['name']): ?>
    <div class="v-prayer-countdown" id="vPrayerCountdown">
        <?= $next['time'] ? 'Menuju ' . $next['name'] . ' dalam ' . format_countdown($next['countdown']) : '' ?>
    </div>
    <?php endif; ?>
</div>

<script>
// Live countdown update
(function() {
    var widget = document.getElementById('vPrayerWidget');
    if (!widget) return;
    
    var times = JSON.parse(widget.getAttribute('data-prayer-times'));
    var countdownEl = document.getElementById('vPrayerCountdown');
    
    function updateCountdown() {
        var now = new Date();
        var currentTime = now.getHours().toString().padStart(2, '0') + ':' + 
                         now.getMinutes().toString().padStart(2, '0');
        
        var prayerOrder = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
        var prayerLabels = {'Fajr':'Subuh','Dhuhr':'Dzuhur','Asr':'Ashar','Maghrib':'Maghrib','Isha':'Isya'};
        
        var nextPrayer = null;
        var nextTime = null;
        var seconds = 0;
        
        for (var i = 0; i < prayerOrder.length; i++) {
            var prayer = prayerOrder[i];
            if (!times[prayer]) continue;
            
            if (times[prayer] > currentTime) {
                nextPrayer = prayerLabels[prayer];
                nextTime = times[prayer];
                
                var parts = nextTime.split(':');
                var prayerDate = new Date();
                prayerDate.setHours(parseInt(parts[0]), parseInt(parts[1]), 0);
                seconds = Math.floor((prayerDate - now) / 1000);
                break;
            }
        }
        
        if (!nextPrayer && times['Fajr']) {
            nextPrayer = 'Subuh';
            nextTime = times['Fajr'];
            var parts = nextTime.split(':');
            var prayerDate = new Date();
            prayerDate.setDate(prayerDate.getDate() + 1);
            prayerDate.setHours(parseInt(parts[0]), parseInt(parts[1]), 0);
            seconds = Math.floor((prayerDate - now) / 1000);
        }
        
        if (countdownEl && nextTime) {
            var hours = Math.floor(seconds / 3600);
            var minutes = Math.floor((seconds % 3600) / 60);
            countdownEl.textContent = 'Menuju ' + nextPrayer + ' dalam ' + 
                                     String(hours).padStart(2,'0') + ':' + 
                                     String(minutes).padStart(2,'0');
        }
    }
    
    updateCountdown();
    setInterval(updateCountdown, 60000); // Update every minute
})();
</script>