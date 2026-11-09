<?php
/**
 * VENOM · Background Style Presets
 */
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access");

return [
  'none' => ['label' => 'None / Standard'],
  'soft-gradient' => ['label' => 'Soft Gradient (Theme)', 'theme_svg' => true],
  'aurora-glow' => ['label' => 'Aurora Glow (Theme)', 'theme_svg' => true],
  'ocean-waves' => ['label' => 'Ocean Waves', 'theme_svg' => true],
  'aurora-neon' => ['label' => 'Aurora Neon Borealis', 'theme_svg' => true],
  'galaxy-nebula' => ['label' => 'Galaxy Nebula', 'theme_svg' => true],
  'quantum-mesh' => ['label' => 'Quantum Cyber Mesh', 'theme_svg' => true],
  'zen-bamboo' => ['label' => 'Zen Bamboo', 'theme_svg' => true],
  'custom' => ['label' => 'Custom Background Style', 'custom' => true],
];