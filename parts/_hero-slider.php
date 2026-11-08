<?php
/**
 * VENOM · Hero Photo Slider (Ken Burns) — CSP-SAFE + AUTO-DETECT
 *
 * CARA GANTI FOTO (pilih salah satu):
 *  1) OTOMATIS: taruh file bernama slide1.jpg, slide2.jpg, slide3.jpg, ...
 *     di folder: template/venom/assets/images/   (jpg/jpeg/png/webp/gif)
 *     Slider akan memuat semuanya sesuai urutan alami (slide1 → slide2 → ...).
 *  2) MANUAL: isi 'slider.photos' di helpers/config.php, contoh:
 *     'photos' => [ ['image' => 'assets/images/slide1.jpg'],
 *                   ['image' => 'images/banner-perpus.jpg'] ],   // relatif root SLiMS juga boleh
 *  3) TEKS CAPTION: opsional via 'slider.captions' di config (per slide, sesuai urutan).
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

/* ── Seni gradasi fallback (data-URI, diizinkan CSP) ── */
if (!function_exists('venom_slide_art')) {
    function venom_slide_art($c1, $c2) {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="1600" height="900">'
             . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
             . '<stop offset="0" stop-color="' . $c1 . '"/><stop offset="1" stop-color="' . $c2 . '"/></linearGradient>'
             . '<radialGradient id="r" cx="0.78" cy="0.22" r="0.95">'
             . '<stop offset="0" stop-color="#ffffff" stop-opacity="0.30"/><stop offset="1" stop-color="#ffffff" stop-opacity="0"/></radialGradient></defs>'
             . '<rect width="1600" height="900" fill="url(#g)"/><rect width="1600" height="900" fill="url(#r)"/>'
             . '<g fill="none" stroke="#ffffff" stroke-opacity="0.16" stroke-width="2">'
             . '<circle cx="1240" cy="430" r="270"/><circle cx="1240" cy="430" r="200"/><circle cx="1240" cy="430" r="130"/>'
             . '<path d="M0 720 Q 400 640 800 720 T 1600 720"/><path d="M0 780 Q 400 700 800 780 T 1600 780"/></g>'
             . '<g fill="#ffffff" fill-opacity="0.18">'
             . '<rect x="1150" y="330" width="120" height="170" rx="10" transform="rotate(-8 1210 415)"/>'
             . '<rect x="1230" y="345" width="120" height="170" rx="10" transform="rotate(6 1290 430)"/>'
             . '<circle cx="180" cy="170" r="6"/><circle cx="330" cy="110" r="4"/><circle cx="470" cy="200" r="5"/></g>'
             . '</svg>';
        return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
    }
}

/* ── Resolver sumber gambar: lokal dulu, eksternal diblokir CSP → fallback art ── */
if (!function_exists('venom_slide_src')) {
    function venom_slide_src($img, $i) {
        $img = trim((string) $img);
        $pairs = [['#7c3aed','#22d3ee'], ['#0284c7','#a855f7'], ['#059669','#22d3ee'], ['#e11d48','#f59e0b']];
        $p = $pairs[$i % count($pairs)];

        if ($img === '') return venom_slide_art($p[0], $p[1]);

        /* data-URI: aman CSP */
        if (strpos($img, 'data:') === 0) return $img;

        /* Aset tema: assets/images/... */
        if (strpos($img, 'assets/') === 0) {
            $rel = substr($img, 7);
            if (is_file(VENOM_DIR . 'assets' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel))) {
                return venom_asset($rel);
            }
            return venom_slide_art($p[0], $p[1]);
        }

        /* Relatif root SLiMS: images/... */
        if (strpos($img, '/') !== 0 && strpos($img, 'http') !== 0) {
            if (defined('SB') && is_file(SB . $img)) return venom_base_url() . $img;
            return venom_slide_art($p[0], $p[1]);
        }

        /* Absolut path server */
        if (strpos($img, '/') === 0) {
            if (defined('SB') && is_file(SB . ltrim($img, '/'))) return venom_base_url() . ltrim($img, '/');
            return $img;
        }

        /* URL http: izinkan hanya host sendiri / static.slims.web.id (lolos CSP) */
        $host = parse_url($img, PHP_URL_HOST);
        $self = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if ($host && ($host === $self || $host === 'static.slims.web.id')) return $img;
        return venom_slide_art($p[0], $p[1]);
    }
}

/* ── Kumpulkan slide: config > auto-scan assets/images/slide* ── */
if (!function_exists('venom_slide_collect')) {
    function venom_slide_collect() {
        $slides = (array) venom_cfg('slider.photos', []);

        if (empty($slides)) {
            $dir = VENOM_DIR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
            $found = [];
            if (is_dir($dir)) {
                foreach (scandir($dir) as $f) {
                    if ($f === '.' || $f === '..') continue;
                    if (preg_match('/^slide.*\.(jpe?g|png|webp|gif)$/i', $f)) $found[] = 'assets/images/' . $f;
                }
                usort($found, 'strnatcasecmp');
            }
            foreach ($found as $rel) $slides[] = ['image' => $rel];
        }

        /* Caption default (digilir bila slide tidak punya teks sendiri) */
        $default_captions = [
            ['kicker' => 'Koleksi Baru',   'title' => 'Jelajahi Dunia Melalui Buku',
             'desc'  => 'Ribuan judul buku pilihan telah menanti untuk memperkaya wawasan dan imajinasi Anda.',
             'cta'   => 'Jelajahi Sekarang', 'link' => 'index.php?keywords=&search=Search'],
            ['kicker' => 'Layanan Digital','title' => 'Perpustakaan di Ujung Jari',
             'desc'  => 'Akses koleksi digital kapan saja, di mana saja. Baca, pinjam, dan pelajari dengan mudah.',
             'cta'   => 'Mulai Baca', 'link' => 'index.php?p=member'],
            ['kicker' => 'Komunitas',      'title' => 'Bergabung dengan Pembaca',
             'desc'  => 'Jadi bagian dari komunitas pembaca aktif. Diskusi, ulasan, dan rekomendasi buku terbaik.',
             'cta'   => 'Daftar Anggota', 'link' => 'index.php?p=member'],
        ];
        $captions = (array) venom_cfg('slider.captions', []);
        if (empty($captions)) $captions = $default_captions;

        foreach ($slides as $i => $s) {
            if (!is_array($s)) $s = ['image' => (string) $s];
            if (empty($s['title'])) {
                $c = $captions[count($captions) ? ($i % count($captions)) : 0];
                $s = array_merge((array) $c, $s);
            }
            $slides[$i] = $s;
        }

        /* Jamin minimal 1 slide */
        if (empty($slides)) $slides = [array_merge($default_captions[0], ['image' => ''])];
        return $slides;
    }
}

$slides   = venom_slide_collect();
$interval = (int) venom_cfg('slider.interval', 6000);
?>

<section class="vnx-slider" data-interval="<?= $interval ?>" id="vnxHeroSlider" aria-label="Galeri foto unggulan">
  <?php foreach ($slides as $i => $s): ?>
    <div class="vnx-slide<?= $i === 0 ? ' is-active' : '' ?>">
      <img class="vnx-slide-img" src="<?= venom_e(venom_slide_src($s['image'] ?? '', $i)) ?>" alt="<?= venom_e($s['title'] ?? '') ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
      <div class="vnx-slide-content">
        <?php if (!empty($s['kicker'])): ?><span class="vnx-slide-kicker">★ <?= venom_e($s['kicker']) ?></span><?php endif; ?>
        <h2 class="vnx-slide-title"><?= venom_e($s['title'] ?? '') ?></h2>
        <?php if (!empty($s['desc'])): ?><p class="vnx-slide-desc"><?= venom_e($s['desc']) ?></p><?php endif; ?>
        <div class="vnx-slide-actions">
          <?php if (!empty($s['link'])): ?>
            <a href="<?= venom_u($s['link']) ?>" class="vnx-slide-btn"><?= venom_e($s['cta'] ?? 'Lihat') ?>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
          <?php endif; ?>
          <a href="index.php?p=news" class="vnx-slide-btn vnx-slide-btn-ghost">Berita Terbaru</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (count($slides) > 1): ?>
    <button type="button" class="vnx-slide-nav vnx-slide-prev" aria-label="Slide sebelumnya"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></button>
    <button type="button" class="vnx-slide-nav vnx-slide-next" aria-label="Slide berikutnya"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button>
    <div class="vnx-slide-dots">
      <?php foreach ($slides as $i => $s): ?><button type="button" class="vnx-slide-dot<?= $i === 0 ? ' is-active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button><?php endforeach; ?>
    </div>
    <div class="vnx-slide-counter">1 / <?= count($slides) ?></div>
  <?php endif; ?>
</section>