<?php defined('INDEX_AUTH') OR die('Direct access not allowed!');
$vslides = (array) venom_cfg('hero.slides', []);
$vstats  = venom_stats();
?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 1. HERO SECTION (PREMIUM PHOTO SLIDER atau CLASSIC FALLBACK)  -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('hero')): ?>
<section class="v-hero v-wrap" id="vHero">
  <?php if (!empty($vslides) && venom_cfg('hero.use_slider', true)): ?>
    <!-- PREMIUM PHOTO SLIDER -->
    <?php venom_part('_hero-slider'); ?>
  <?php else: ?>
    <!-- CLASSIC HERO FALLBACK (jika tidak ada slide) -->
    <div class="v-hero-inner">
      <div class="v-hero-copy">
        <span class="v-kicker"><?= venom_icon('sparkles', 15) ?><?= venom_e(venom_cfg('hero.kicker', 'Perpustakaan Digital')) ?></span>
        <h1 class="v-hero-title"><?= venom_e(venom_cfg('hero.title', 'Jelajahi Dunia Tanpa Batas')) ?></h1>
        <p class="v-hero-text"><?= venom_e(venom_cfg('hero.text', 'Temukan ribuan koleksi buku, jurnal, dan media digital dalam satu platform.')) ?></p>
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
  <?php endif; ?>

  <!-- SEARCH BOX (Premium) -->
  <?php venom_part('_search-form'); ?>

  <!-- QUICK TOPICS -->
  <div class="v-quick" style="margin-top: 16px;">
    <span>Populer:</span>
    <?php foreach (array_slice(venom_topics(6), 0, 6) as $t): ?>
      <a class="v-chip" href="<?= venom_u(venom_search_url($t['topic'])) ?>"><?= venom_e($t['topic']) ?></a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 2. TICKER STATS MARQUEE                                        -->
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
<!-- 3. WIDGET: JADWAL SHOLAT & LIVE VISITOR TICKER (TAHAP 2)       -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_cfg('features.prayer_times', true)): ?>
  <?php venom_part('waktu_sholat'); ?>
<?php endif; ?>

<?php if (venom_cfg('features.live_ticker', true)): ?>
  <?php venom_part('visitor/visitor_ticker'); ?>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 4. STATISTIK SECTION                                           -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('stats')): ?>
<section class="v-wrap v-sec">
  <?= venom_section_head('bar-chart', venom_t('stats', 'Statistik Perpustakaan'), 'Data real-time perpustakaan kami') ?>
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
<!-- 5. KOLEKSI TERBARU (NEW ARRIVALS)                              -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('new_arrivals')): ?>
<section class="v-wrap v-sec" id="v-koleksi">
  <?= venom_section_head('fire', venom_t('new', 'Koleksi Terbaru'),
        'Tambahan koleksi paling baru di perpustakaan kami', 'index.php?keywords=&search=Search', 'Lihat Semua') ?>
  <?= venom_book_grid(venom_new_arrivals(venom_cfg('limits.new', 10)), 'BARU') ?>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 6. KOLEKSI UNGGULAN (FEATURED)                                 -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('featured')): ?>
<section class="v-wrap v-sec" id="v-unggulan">
  <?= venom_section_head('star', venom_t('featured', 'Koleksi Unggulan'), 'Dikurasi langsung oleh pustakawan') ?>
  <?= venom_book_grid(venom_featured(venom_cfg('limits.featured', 10)), 'UNGGULAN') ?>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 6.5 KOLEKSI TERPOPULER (Popular by Loan - BEDA dari Featured) -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('featured')): $vpop = venom_popular_collections(6); if ($vpop): ?>
<section class="v-wrap v-sec">
  <?= venom_section_head('star', 'Koleksi Terpopuler', 'Buku yang paling sering dipinjam anggota perpustakaan') ?>
  <?= venom_book_grid($vpop, 'POPULER') ?>
</section>
<?php endif; endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 6.6 KLASIFIKASI DDC (10 kelas 000-900 dengan warna berbeda)    -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_cfg('features.ddc_browse', true)): $ddc = venom_ddc_classes(); $ddc_counts = venom_ddc_counts(); ?>
<section class="v-wrap v-sec">
  <?= venom_section_head('grid', 'Jelajahi DDC', 'Temukan koleksi berdasarkan 10 kelas utama Dewey Decimal Classification') ?>
  <div class="v-ddc-grid">
    <?php foreach ($ddc as $d): $count = $ddc_counts[$d['code']]; ?>
      <a class="v-ddc-item v-reveal" style="--ddc-color: <?= $d['color'] ?>"
         href="<?= venom_u('index.php?callnumber=' . $d['code'] . '&search=Search') ?>">
        <div class="v-ddc-head">
          <span class="v-ddc-ico"><?= venom_icon($d['icon'], 20) ?></span>
          <span class="v-ddc-code">DDC <?= $d['code'] ?>00</span>
        </div>
        <strong class="v-ddc-title"><?= venom_e($d['title']) ?></strong>
        <small class="v-ddc-desc"><?= venom_e($d['desc']) ?></small>
        <em class="v-ddc-count"><?= venom_num($count) ?> koleksi</em>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 6.7 KATEGORI KOLEKSI (3 Card: Tipe Material / Jenis / Lokasi)  -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_cfg('features.category_cards', true)): $cats = venom_category_groups(10); ?>
<section class="v-wrap v-sec">
  <?= venom_section_head('layers', 'Jelajahi Kategori', 'Pilih berdasarkan tipe material, jenis koleksi, atau lokasi') ?>
  <div class="v-cat-grid">
    <?php foreach ($cats as $g): ?>
      <div class="v-cat-card v-reveal">
        <div class="v-cat-head">
          <span class="v-cat-icon"><?= venom_icon($g['icon'], 22) ?></span>
          <div>
            <strong><?= venom_e($g['title']) ?></strong>
            <small><?= venom_num(count($g['rows'])) ?> kategori</small>
          </div>
        </div>
        <ul class="v-cat-list">
          <?php foreach ($g['rows'] as $row): $name = $row[$g['label']]; ?>
            <li>
              <a href="<?= venom_u('index.php?' . $g['param'] . '=' . urlencode($name) . '&search=Search') ?>">
                <span><?= venom_e($name) ?></span>
                <span class="v-cat-count"><?= venom_num($row['c']) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
          <?php if (empty($g['rows'])): ?><li><span style="color:var(--v-mut);font-style:italic">Belum ada data</span></li><?php endif; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 6.8 PILLS TIPE KOLEKSI (5 tipe terbesar sebagai tombol cepat) -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_cfg('features.ctype_pills', true)): $pills = venom_collection_type_pills(5); if ($pills): ?>
<section class="v-wrap v-sec" style="padding-top:0">
  <div class="v-ctype-pills">
    <?php foreach ($pills as $p): ?>
      <a class="v-ctype-pill v-reveal" href="<?= venom_u('index.php?colltype=' . urlencode($p['coll_type_name']) . '&search=Search') ?>">
        <span class="v-ctype-icon"><?= venom_icon('tag', 16) ?></span>
        <span class="v-ctype-text">
          <strong><?= venom_e($p['coll_type_name']) ?></strong>
          <small><?= venom_num((int) $p['c']) ?> koleksi</small>
        </span>
        <?= venom_icon('right', 16) ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; endif; ?>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 7. JELAJAH TOPIK (TOPICS)                                      -->
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
<!-- 8. BERITA & PENGUMUMAN (NEWS)                                  -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_on('news')): $vnews = venom_news(venom_cfg('limits.news', 3)); if ($vnews): ?>
<section class="v-wrap v-sec">
  <?= venom_section_head('news', venom_t('news', 'Berita & Pengumuman'), '', 'index.php?p=news', 'Semua Berita') ?>
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

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- 9. CTA: AJAK MENJADI ANGGOTA                                   -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<?php if (venom_cfg('features.member_cta', true)): ?>
<section class="v-wrap v-sec">
  <div class="vnx-cta-banner">
    <div class="vnx-cta-bg"></div>
    <div class="vnx-cta-content">
      <h2>Belum Jadi Anggota?</h2>
      <p>Daftar gratis sekarang dan nikmati akses penuh ke koleksi perpustakaan kami secara digital.</p>
      <a href="index.php?p=member" class="vnx-slide-btn">
        Daftar Sekarang
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>