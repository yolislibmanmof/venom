<?php defined('INDEX_AUTH') OR die('Direct access not allowed!');
$vnx_topics = venom_topics(50);
?>
<div class="vnx-modal" id="vnxTopicModal">
  <div class="vnx-modal-backdrop" data-close="1"></div>
  <div class="vnx-modal-panel vnx-modal-center">
    <div class="vnx-modal-head">
      <h2 class="vnx-modal-title">Jelajah Topik</h2>
      <button type="button" class="vnx-modal-close" data-close="1" aria-label="Tutup"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="vnx-modal-body">
      <?php if ($vnx_topics): ?>
      <div class="vnx-topic-cloud-large">
        <?php foreach ($vnx_topics as $t):
          $size = min(1.7, max(0.9, 0.9 + ((int)$t['total'] / 60)));
        ?>
          <a href="<?= venom_u(venom_search_url($t['topic'])) ?>" class="vnx-topic-cloud-item" style="font-size:<?= $size ?>rem">
            <?= venom_e($t['topic']) ?><span><?= venom_num($t['total']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
      <?php else: echo venom_empty('Belum ada data topik.'); endif; ?>
    </div>
  </div>
</div>