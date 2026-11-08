<?php defined('INDEX_AUTH') OR die('Direct access not allowed!');
$vslides = (array) venom_cfg('hero.slides', []);
$vstats  = venom_stats();
?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 1. HERO SECTION -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('hero') && $vslides): ?>
<section class="v-hero" id="vHero" data-interval="<?= (int) venom_cfg('hero.interval', 6000) ?>">
  <div class="v-wrap v-hero-inner">

    <div class="v-hero-copy">
      <div class="v-slides">
        <?php foreach ($vslides as $i => $s): ?>
        <div class="v-slide<?= $i === 0 ? ' is-active' : '' ?>" data-slide="<?= (int) $i ?>">
          <?php if (!empty($s['kicker'])): ?>
            <span class="v-kicker"><?= venom_icon('sparkles', 15) ?><?= venom_e($s['kicker']) ?></span>
          <?php endif; ?>
          <h1 class="v-hero-title"><?= venom_e(isset($s['title']) ? $s['title'] : '') ?></h1>
          <p class="v-hero-text"><?= venom_e(isset($s['text']) ? $s['text'] : '') ?></p>
          <?php if (!empty($s['cta'])): ?>
            <a class="v-btn v-btn-primary v-btn-lg" href="<?= venom_u($s['url']) ?>">
              <?= venom_e($s['cta']) ?><?= venom_icon('right', 18) ?>
            </a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <form class="v-search" action="index.php" method="get" role="search">
        <span class="v-search-icon"><?= venom_icon('search', 20) ?></span>
        <input type="search" name="keywords" maxlength="120" autocomplete="off"
               placeholder="<?= venom_e(venom_t('search')) ?>" aria-label="Kata kunci">
        <button type="submit" name="search" value="Search" class="v-btn v-btn-primary">
          <?= venom_e(venom_t('search_btn', 'Cari')) ?>
        </button>
      </form>

      <div class="v-quick">
        <span>Populer:</span>
        <?php foreach (array_slice(venom_topics(6), 0, 6) as $t): ?>
          <a class="v-chip" href="<?= venom_u(venom_search_url($t['topic'])) ?>"><?= venom_e($t['topic']) ?></a>
        <?php endforeach; ?>
      </div>

      <?php if (count($vslides) > 1): ?>
      <div class="v-hero-ctrl">
        <button type="button" class="v-icon-btn" data-hero="prev" aria-label="Slide sebelumnya"><?= venom_icon('left', 18) ?></button>
        <div class="v-dots">
          <?php foreach ($vslides as $i => $s): ?>
            <button type="button" class="v-dot<?= $i === 0 ? ' is-active' : '' ?>" data-hero-dot="<?= (int) $i ?>"
                    aria-label="Slide <?= $i + 1 ?>"></button>
          <?php endforeach; ?>
        </div>
        <button type="button" class="v-icon-btn" data-hero="next" aria-label="Slide berikutnya"><?= venom_icon('right', 18) ?></button>
      </div>
      <?php endif; ?>
    </div>

    <div class="v-hero-art">
      <div class="v-stack" data-tilt>
        <?php foreach (array_slice(venom_featured(3), 0, 3) as $i => $b): ?>
          <a class="v-stack-item v-stack-<?= (int) $i ?>" href="<?= venom_u(venom_detail_url($b['biblio_id'])) ?>">
            <img src="<?= venom_e(venom_cover($b['image'], $b['title'])) ?>" alt="<?= venom_e($b['title']) ?>"
                 loading="lazy" decoding="async" width="300" height="420">
          </a>
        <?php endforeach; ?>
        <span class="v-stack-glow"></span>
      </div>
    </div>

  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 2. TICKER LAMA (Tetap dipertahankan sesuai kode asli Anda) -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('ticker')): ?>
<div class="v-ticker" aria-hidden="true">
  <div class="v-ticker-track">
    <?php for ($r = 0; $r < 2; $r++): ?>
      <span class="v-ticker-group">
        <em><?= venom_icon('book', 15) ?><?= venom_num($vstats['titles']) ?> Judul</em>
        <em><?= venom_icon('layers', 15) ?><?= venom_num($vstats['items']) ?> Eksemplar</em>
        <em><?= venom_icon('users', 15) ?><?= venom_num($vstats['members']) ?> Anggota</em>
        <em><?= venom_icon('chart', 15) ?><?= venom_num($vstats['visits']) ?> Kunjungan Hari Ini</em>
        <em><?= venom_icon('clock', 15) ?><?= venom_num($vstats['loans']) ?> Sedang Dipinjam</em>
        <em><?= venom_icon('shield', 15) ?>Aman &amp; Terlindungi</em>
      </span>
    <?php endfor; ?>
  </div>
</div>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 3. TAHAP 2: WIDGET JADWAL SHOLAT & LIVE VISITOR TICKER (BARU) -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_cfg('features.prayer_times', true)): ?>
    <?php venom_part('waktu_sholat'); ?>
<?php endif; ?>

<?php if (venom_cfg('features.live_ticker', true)): ?>
    <?php venom_part('visitor/visitor_ticker'); ?>
<?php endif; ?>
<!-- ═══════════════════════════════════════════════════════════════ -->

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 4. STATISTIK SECTION -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('stats')): ?>
<section class="v-wrap v-sec">
  <div class="v-grid v-grid-stats">
    <?= venom_stat_card('book',  $vstats['titles'],  venom_t('titles', 'Judul Koleksi'), 'p') ?>
    <?= venom_stat_card('layers',$vstats['items'],   venom_t('items', 'Eksemplar'), 'a') ?>
    <?= venom_stat_card('users', $vstats['members'], venom_t('members', 'Anggota'), 'p') ?>
    <?= venom_stat_card('chart', $vstats['visits'],  venom_t('visits', 'Kunjungan'), 'a') ?>
    <?= venom_stat_card('clock', $vstats['loans'],   venom_t('loans', 'Dipinjam'), 'p') ?>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 5. KOLEKSI TERBARU (NEW ARRIVALS) -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('new_arrivals')): ?>
<section class="v-wrap v-sec" id="v-koleksi">
  <?= venom_section_head('fire', venom_t('new', 'Koleksi Terbaru'),
        'Tambahan koleksi paling baru di perpustakaan kami', 'index.php?p=news', 'Info koleksi') ?>
  <?= venom_book_grid(venom_new_arrivals(venom_cfg('limits.new', 10)), 'BARU') ?>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 6. KOLEKSI UNGGULAN (FEATURED) -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('featured')): ?>
<section class="v-wrap v-sec" id="v-unggulan">
  <?= venom_section_head('star', venom_t('featured', 'Koleksi Unggulan'), 'Dikurasi langsung oleh pustakawan') ?>
  <?= venom_book_grid(venom_featured(venom_cfg('limits.featured', 10)), 'UNGGULAN') ?>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 7. JELAJAH TOPIK (TOPICS) -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('topics')): $vtopics = venom_topics(venom_cfg('limits.topics', 14)); ?>
<section class="v-wrap v-sec">
  <?= venom_section_head('tag', venom_t('topics', 'Jelajah Topik'), 'Temukan koleksi berdasarkan subjek') ?>
  <?php if ($vtopics): ?>
  <div class="v-topics">
    <?php foreach ($vtopics as $t): ?>
      <a class="v-topic v-reveal" href="<?= venom_u(venom_search_url($t['topic'])) ?>">
        <span><?= venom_e($t['topic']) ?></span><b><?= venom_num($t['total']) ?></b>
      </a>
    <?php endforeach; ?>
  </div>
  <?php else: echo venom_empty('Data topik belum tersedia.'); endif; ?>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 8. BERITA & PENGUMUMAN (NEWS) -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('news')): $vnews = venom_news(venom_cfg('limits.news', 3)); if ($vnews): ?>
<section class="v-wrap v-sec">
  <?= venom_section_head('news', venom_t('news', 'Berita & Pengumuman'), '', 'index.php?p=news') ?>
  <div class="v-grid v-grid-news">
    <?php foreach ($vnews as $n):
      $exc = Venom_Sec::text(isset($n['content_desc']) ? $n['content_desc'] : '', 160); ?>
      <article class="v-card v-pad v-news v-reveal">
        <span class="v-chip v-chip-xs"><?= venom_e(!empty($n['input_date']) ? substr($n['input_date'], 0, 10) : '') ?></span>
        <h3><?= venom_e(isset($n['content_title']) ? $n['content_title'] : 'Pengumuman') ?></h3>
        <p><?= venom_e($exc) ?>…</p>
        <a class="v-btn v-btn-ghost v-btn-sm" href="index.php?p=news">Selengkapnya<?= venom_icon('right', 15) ?></a>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; endif; ?>