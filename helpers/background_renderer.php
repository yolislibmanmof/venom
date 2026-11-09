<?php
/**
 * VENOM · Background Renderer (HTML + CSS injection)
 */
if (!defined('INDEX_AUTH') || INDEX_AUTH != 1) die("cannot access");

if (!function_exists('venom_background_styles')) {
    function venom_background_styles() {
        static $styles = null;
        if ($styles === null) $styles = require __DIR__ . '/backgrounds.php';
        return is_array($styles) ? $styles : [];
    }
}

if (!function_exists('venom_background_image_size_options')) {
    function venom_background_image_size_options() {
        return ['normal' => 'Normal', 'crop' => 'Crop / Cover', 'contain' => 'Contain (Fit)',
                'stretch' => 'Stretch', 'tile' => 'Tile / Repeat', 'width' => 'Full Width', 'height' => 'Full Height'];
    }
}

if (!function_exists('venom_background_image_position_options')) {
    function venom_background_image_position_options() {
        return ['center' => 'Center', 'top' => 'Top', 'bottom' => 'Bottom', 'left' => 'Left', 'right' => 'Right',
                'top-left' => 'Top Left', 'top-right' => 'Top Right', 'bottom-left' => 'Bottom Left', 'bottom-right' => 'Bottom Right'];
    }
}

if (!function_exists('venom_background_image_filter_options')) {
    function venom_background_image_filter_options() {
        return ['none' => 'No Filter', 'soft' => 'Soft', 'readable' => 'Readable', 'vivid' => 'Vivid',
                'monochrome' => 'Monochrome', 'warm' => 'Warm', 'cool' => 'Cool'];
    }
}

if (!function_exists('venom_background_image_blur_options')) {
    function venom_background_image_blur_options() {
        return ['none' => 'No Blur', '1' => '1px', '2' => '2px', '4' => '4px', '8' => '8px'];
    }
}

if (!function_exists('venom_background_image_overlay_options')) {
    function venom_background_image_overlay_options() {
        return ['none' => 'No Overlay', 'auto' => 'Adaptive', 'subtle' => 'Subtle', 'readable' => 'Readable',
                'dim' => 'Dim', 'accent' => 'Accent Tint', 'frosted' => 'Frosted'];
    }
}

if (!function_exists('venom_background_animation_options')) {
    function venom_background_animation_options() {
        return [
            ['none', 'Nonaktif'], ['particles', 'Floating Glyphs'], ['rain', 'Code Rain'],
            ['grid', 'Moving Grid'], ['twinkle', 'Twinkling Stars'], ['zen-ripples', 'Zen Ripples'],
            ['neural-network', 'Neural Network'], ['starfield-warp', 'Starfield Warp'], ['floating-embers', 'Floating Embers']
        ];
    }
}

if (!function_exists('venom_cursor_icon_options')) {
    function venom_cursor_icon_options() {
        return [
            ['default', 'Default Browser'], ['neon-comet', 'Neon Comet'], ['pixel-sword', 'Pixel Sword'],
            ['electric-bolt', 'Electric Bolt'], ['ink-brush', 'Ink Brush'], ['rainbow-ribbon', 'Rainbow Ribbon']
        ];
    }
}

if (!function_exists('venom_cursor_particles_options')) {
    function venom_cursor_particles_options() {
        return [['auto', 'Auto'], ['low', 'Ringan'], ['medium', 'Sedang'], ['high', 'Optimal'], ['none', 'Nonaktif']];
    }
}

if (!function_exists('venom_render_background_layer')) {
    function venom_render_background_layer() {
        $anim = venom_cfg('hero.animation', 'none');
        $speed = venom_cfg('hero.animation_speed', 1);
        if ($anim === 'none') return '';
        return '<div class="venom-background-animation-layer" data-animation="' . venom_e($anim) . '" data-speed-multiplier="' . venom_e($speed) . '" aria-hidden="true"></div>';
    }
}

if (!function_exists('venom_render_custom_background_css')) {
    function venom_render_custom_background_css() {
        $custom = venom_cfg('hero.background_custom', '');
        if (empty($custom)) return '';
        $parts = explode('|', $custom, 2);
        $light = trim($parts[0] ?? '');
        $dark = trim($parts[1] ?? $light);
        if (empty($light) && empty($dark)) return '';
        $out = '<style id="venom-custom-background">';
        $out .= 'body.venom-bg-custom{background:' . venom_e($light) . ' !important;background-attachment:fixed !important}';
        if ($dark) $out .= '[data-theme="dark"] body.venom-bg-custom{background:' . venom_e($dark) . ' !important}';
        $out .= '</style>';
        return $out;
    }
}