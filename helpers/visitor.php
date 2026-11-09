<?php
/**
 * VENOM · Visitor Portal Helpers
 * Utility functions untuk visitor counter
 */
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) {
    die("Direct access not allowed!");
}

/**
 * Get visitor statistics with caching
 */
if (!function_exists('venom_visitor_stats')) {
    function venom_visitor_stats($ttl = 300) {
        $cache_key = 'venom_visitor_stats_' . md5(__DIR__);
        $cache_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cache_key . '.json';
        
        // Check cache
        if (is_file($cache_file) && (time() - filemtime($cache_file)) < $ttl) {
            $data = json_decode(@file_get_contents($cache_file), true);
            if (is_array($data)) return $data;
        }
        
        // Calculate stats
        $stats = [
            'today' => 0,
            'week' => 0,
            'month' => 0,
            'member' => 0,
            'featured' => null,
            'trending' => []
        ];
        
        if (Venom_Core::db()) {
            $db = Venom_Core::db();
            
            // Today's visitors
            $today = venom_row("SELECT COUNT(*) AS c FROM visitor WHERE DATE(visit_date) = CURDATE()");
            $stats['today'] = (int)($today['c'] ?? 0);
            
            // This week
            $week = venom_row("SELECT COUNT(*) AS c FROM visitor WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
            $stats['week'] = (int)($week['c'] ?? 0);
            
            // This month
            $month = venom_row("SELECT COUNT(*) AS c FROM visitor WHERE MONTH(visit_date) = MONTH(CURDATE()) AND YEAR(visit_date) = YEAR(CURDATE())");
            $stats['month'] = (int)($month['c'] ?? 0);
            
            // Total members
            $members = venom_row("SELECT COUNT(*) AS c FROM member");
            $stats['member'] = (int)($members['c'] ?? 0);
            
            // Featured book (newest with cover)
            $featured = venom_row("SELECT biblio_id, title, image FROM biblio WHERE image IS NOT NULL AND image != '' ORDER BY input_date DESC LIMIT 1");
            if ($featured) {
                $stats['featured'] = [
                    'id' => (int)$featured['biblio_id'],
                    'title' => $featured['title'],
                    'image' => $featured['image']
                ];
            }
            
            // Trending books (most loaned)
            $trending = venom_rows("
                SELECT b.biblio_id, b.title, b.image, COUNT(l.loan_id) AS loan_count
                FROM biblio b
                LEFT JOIN loan l ON l.biblio_id = b.biblio_id
                WHERE b.image IS NOT NULL AND b.image != ''
                GROUP BY b.biblio_id
                ORDER BY loan_count DESC, b.input_date DESC
                LIMIT 6
            ");
            $stats['trending'] = $trending ?: [];
        }
        
        // Save cache
        @file_put_contents($cache_file, json_encode($stats), LOCK_EX);
        
        return $stats;
    }
}

/**
 * Get Indonesian greeting based on hour
 */
if (!function_exists('venom_visitor_greeting')) {
    function venom_visitor_greeting() {
        $hour = (int)date('G');
        if ($hour < 4) return 'Selamat malam';
        if ($hour < 10) return 'Selamat pagi';
        if ($hour < 15) return 'Selamat siang';
        if ($hour < 19) return 'Selamat sore';
        return 'Selamat malam';
    }
}

/**
 * Get Indonesian date format
 */
if (!function_exists('venom_visitor_date')) {
    function venom_visitor_date() {
        $day_names = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $month_names = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        return $day_names[(int)date('w')] . ', ' . (int)date('j') . ' ' . 
               ($month_names[(int)date('n')] ?? '') . ' ' . (int)date('Y');
    }
}

/**
 * Get daily quote (deterministic by day of year)
 */
if (!function_exists('venom_visitor_quote')) {
    function venom_visitor_quote() {
        $quotes = [
            'Sebuah ruangan tanpa buku adalah seperti tubuh tanpa jiwa.',
            'Membaca adalah bernapas bagi jiwa.',
            'Hari ini seorang pembaca, besok seorang pemimpin.',
            'Membaca membuka jendela dunia.',
            'Buku adalah teman yang tak pernah mengkhianati.',
            'Ilmu adalah harta, dan membaca adalah kuncinya.',
            'Satu buku, satu pena, satu anak, dapat mengubah dunia.',
            'Buku terbaik membuka pikiran sekaligus hati.'
        ];
        
        return $quotes[(int)date('z') % count($quotes)];
    }
}

/**
 * Get opening hours
 */
if (!function_exists('venom_visitor_hours')) {
    function venom_visitor_hours() {
        return [
            ['Senin – Jumat', '08.00 – 16.00'],
            ['Sabtu', '08.00 – 12.00'],
            ['Minggu / Libur', 'Tutup']
        ];
    }
}

/**
 * Get institution options (default faculties)
 */
if (!function_exists('venom_visitor_institutions')) {
    function venom_visitor_institutions() {
        return [
            ['value' => 'umum', 'label' => 'Umum', 'manual' => false],
            ['value' => 'sma', 'label' => 'SMA/SMK', 'manual' => false],
            ['value' => 'mahasiswa', 'label' => 'Mahasiswa', 'manual' => false],
            ['value' => 'dosen', 'label' => 'Dosen/Guru', 'manual' => false],
            ['value' => 'lainnya', 'label' => 'Lainnya (ketik manual)', 'manual' => true]
        ];
    }
}