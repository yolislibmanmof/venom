<?php
/**
 * VENOM · Icon set (inline SVG, stroke = currentColor).
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

function venom_icon($name, $size = 20, $class = '')
{
    static $p = [
        'search'   => '<circle cx="11" cy="11" r="7"/><path d="M16.5 16.5 21 21"/>',
        'book'     => '<path d="M4 5a2 2 0 0 1 2-2h8v18H6a2 2 0 0 1-2-2z"/><path d="M14 3h6v18h-6"/>',
        'moon'     => '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>',
        'sun'      => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M5 5l1.5 1.5M17.5 17.5 19 19M19 5l-1.5 1.5M6.5 17.5 5 19"/>',
        'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"/>',
        'users'    => '<circle cx="9" cy="8" r="3.5"/><path d="M2 21c0-3.5 3-5.5 7-5.5s7 2 7 5.5"/><path d="M17 5.5a3.5 3.5 0 0 1 0 7M18 21c0-2.5-.6-4-2-5"/>',
        'up'       => '<path d="M12 19V5M5 12l7-7 7 7"/>',
        'right'    => '<path d="M5 12h14M12 5l7 7-7 7"/>',
        'left'     => '<path d="M19 12H5M12 19l-7-7 7-7"/>',
        'down'     => '<path d="M6 9l6 6 6-6"/>',
        'menu'     => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'x'        => '<path d="M6 6l12 12M18 6 6 18"/>',
        'grid'     => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'list'     => '<path d="M8 6h13M8 12h13M8 18h13"/><circle cx="4" cy="6" r="1.2"/><circle cx="4" cy="12" r="1.2"/><circle cx="4" cy="18" r="1.2"/>',
        'quote'    => '<path d="M9 6H5v6h4l-2 6h3l2-6V6zM19 6h-4v6h4l-2 6h3l2-6V6z"/>',
        'share'    => '<circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="M8.2 10.8 15.8 6.4M8.2 13.2l7.6 4.4"/>',
        'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
        'chart'    => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'tag'      => '<path d="M20.5 12.5 12 21 3 12l8.5-8.5H20a1 1 0 0 1 1 1z"/><circle cx="16.5" cy="7.5" r="1.4"/>',
        'fire'     => '<path d="M12 3c3.5 4.2 6 5.6 6 9.2A6 6 0 0 1 6 12.2C6 8.6 8.5 7.2 12 3z"/>',
        'star'     => '<path d="M12 3.5l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17.6 6.6 20.4l1-6.1L3.2 10l6.1-.9z"/>',
        'shield'   => '<path d="M12 3l8 3v6c0 5-3.6 8.2-8 9-4.4-.8-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/>',
        'sparkles' => '<path d="M12 3.5 13.6 8 18 9.6 13.6 11.2 12 15.7 10.4 11.2 6 9.6 10.4 8z"/><path d="M18.5 15.5l.7 1.8 1.8.7-1.8.7-.7 1.8-.7-1.8-1.8-.7 1.8-.7z"/>',
        'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 7l8.5 6 8.5-6"/>',
        'phone'    => '<path d="M5 3h3l2 5-2.5 1.5a12 12 0 0 0 6 6L15 13l5 2v3a2 2 0 0 1-2.2 2A16 16 0 0 1 3 5.2A2 2 0 0 1 5 3z"/>',
        'pin'      => '<path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/>',
        'chat'     => '<path d="M21 12a9 9 0 0 1-13.2 8L3 21.5l1.6-4.6A9 9 0 1 1 21 12z"/>',
        'facebook' => '<path d="M14 8.5h3v-3h-3a4 4 0 0 0-4 4v2H8v3h2v7h3v-7h2.4l.6-3H13v-2a1 1 0 0 1 1-1z"/>',
        'instagram'=> '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1.1"/>',
        'youtube'  => '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M11 9.5l5 2.5-5 2.5z"/>',
        'twitter'  => '<path d="M4 4l7 8.5L4.5 20H7l5.2-5.6L16.5 20H20l-7.2-9L19.5 4H17l-4.6 5L8 4z"/>',
        'filter'   => '<path d="M3 5h18l-7 8v6l-4-2v-4z"/>',
        'check'    => '<path d="M4 12.5l5 5L20 6.5"/>',
        'copy'     => '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M15 5H5a2 2 0 0 0-2 2v10"/>',
        'print'    => '<path d="M7 8V3h10v5"/><rect x="3" y="8" width="18" height="8" rx="2"/><path d="M7 16h10v5H7z"/>',
        'palette'  => '<path d="M12 3a9 9 0 1 0 0 18c1.7 0 2-1.2 1.3-2.2-.8-1.2 0-2.8 1.5-2.8H18a3 3 0 0 0 3-3 9 9 0 0 0-9-10z"/><circle cx="8.5" cy="10" r="1.2"/><circle cx="12" cy="7.5" r="1.2"/><circle cx="15.5" cy="10" r="1.2"/>',
        'layers'   => '<path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/>',
        'news'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 9h7M7 13h10M7 16h6"/>',
        'plus'     => '<path d="M12 5v14M5 12h14"/>',
        'file'     => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>',
        'code'     => '<path d="M8 9l-4 3 4 3M16 9l4 3-4 3"/>',
    ];
    if (!isset($p[$name])) $name = 'book';
    $s = (int) $size;
    $c = $class !== '' ? ' class="' . venom_e($class) . '"' : '';
    return '<svg' . $c . ' width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" '
         . 'stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" '
         . 'aria-hidden="true" focusable="false">' . $p[$name] . '</svg>';
}