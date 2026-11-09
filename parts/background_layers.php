<?php
/**
 * VENOM · Background Layers (Orbs + Grid + Advanced Animations)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

$anim = venom_cfg('hero.animation', 'none');
$bg_style = venom_cfg('hero.background_style', 'none');
?>

<!-- Base Animated Background Orbs & Grid (Estetika Dasar VENOM) -->
<div class="v-bg" aria-hidden="true">
  <div class="v-bg-grid"></div>
  <div class="v-bg-orb v-bg-orb-1"></div>
  <div class="v-bg-orb v-bg-orb-2"></div>
  <div class="v-bg-orb v-bg-orb-3"></div>
</div>

<!-- Advanced Background Animation Layer (Neural, Warp, Ripples, Glyphs, dll.) -->
<?php if ($anim !== 'none'): ?>
  <div id="venom-bg-animation-layer" 
       class="venom-background-animation-layer" 
       data-animation="<?= venom_e($anim) ?>" 
       data-speed-multiplier="<?= venom_e(venom_cfg('hero.animation_speed', 1)) ?>" 
       aria-hidden="true"></div>
<?php endif; ?>

<!-- Custom Background Image/Style Layer (Dari Pengaturan Admin) -->
<?php if ($bg_style !== 'none'): ?>
  <div class="venom-background-image-layer" 
       data-venom-bg-style="<?= venom_e($bg_style) ?>" 
       aria-hidden="true"></div>
<?php endif; ?>

<!-- Inject Custom Background CSS (Jika ada nilai kustom di admin) -->
<?php if (function_exists('venom_render_custom_background_css')): ?>
    <?= venom_render_custom_background_css() ?>
<?php endif; ?>

<script>
// Re-initialize background animation after DOM is ready
(function() {
  var layer = document.getElementById('venom-bg-animation-layer');
  if (layer && window.venomInitBgAnimation) {
    setTimeout(function() {
      window.venomInitBgAnimation(layer);
    }, 100);
  }
})();
</script>