<?php
/**
 * VENOM · APA Style Citation (7th Edition)
 * Route: popup sitasi SLiMS & show_detail. Zero-dependency.
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

/* ── Aset bersama (dimuat sekali per halaman) ── */
if (!defined('VENOM_CITE_ASSETS_LOADED')) {
    define('VENOM_CITE_ASSETS_LOADED', true);
    echo '<style>
:root{--vnx-p:#7c3aed;--vnx-a:#22d3ee;--vnx-bg:#f6f7fb;--vnx-surf:#fff;--vnx-tx:#0f172a;--vnx-mut:#64748b;--vnx-line:rgba(15,23,42,.1)}
html[data-theme="dark"]{--vnx-bg:#080b16;--vnx-surf:#111827;--vnx-tx:#e8ecf6;--vnx-mut:#94a3b8;--vnx-line:rgba(255,255,255,.12)}
html,body{background:var(--vnx-bg)!important;color:var(--vnx-tx)!important;margin:0;padding:18px 16px;font-family:"Segoe UI",system-ui,-apple-system,sans-serif;line-height:1.6}
.vnx-cite{position:relative;overflow:hidden;background:var(--vnx-surf);border:1px solid var(--vnx-line);border-radius:14px;padding:14px 16px 14px 20px;margin:0 0 14px;box-shadow:0 8px 24px rgba(15,23,42,.08)}
.vnx-cite::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(180deg,var(--vnx-p),var(--vnx-a))}
.vnx-cite-head{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.vnx-cite-badge{padding:3px 10px;border-radius:999px;font-size:.68rem;font-weight:800;letter-spacing:.08em;color:#fff;background:linear-gradient(120deg,var(--vnx-p),var(--vnx-a))}
.vnx-cite-name{font-size:.85rem;font-weight:700;color:var(--vnx-mut)}
.vnx-cite-copy{margin-left:auto;display:inline-grid;place-items:center;width:30px;height:30px;border-radius:9px;border:1px solid var(--vnx-line);background:transparent;color:var(--vnx-mut);cursor:pointer;transition:.2s}
.vnx-cite-copy:hover{color:var(--vnx-p);border-color:var(--vnx-p)}
.vnx-cite-copy.is-ok{color:#10b981;border-color:#10b981}
.vnx-cite-body{margin:0;font-size:.92rem;line-height:1.7;color:var(--vnx-tx)}
</style>
<script>
(function(){
var dark=false;
try{if(window.parent&&window.parent!==window&&window.parent.document.documentElement.getAttribute("data-theme")==="dark"){dark=true;}else if(window.opener&&window.opener.document.documentElement.getAttribute("data-theme")==="dark"){dark=true;}}catch(e){}
if(!dark){var m=null;try{m=localStorage.getItem("venom.mode");}catch(e){}
if(m==="dark"){dark=true;}else if(!m||m==="auto"){dark=!!(window.matchMedia&&window.matchMedia("(prefers-color-scheme: dark)").matches);}}
if(dark){document.documentElement.setAttribute("data-theme","dark");}
document.addEventListener("click",function(e){
var b=e.target.closest?e.target.closest(".vnx-cite-copy"):null;if(!b)return;
var card=b.closest(".vnx-cite");var body=card?card.querySelector(".vnx-cite-body"):null;if(!body)return;
var txt=body.innerText.replace(/\s+/g," ").trim();
function ok(){b.classList.add("is-ok");setTimeout(function(){b.classList.remove("is-ok");},1500);}
if(navigator.clipboard&&navigator.clipboard.writeText){navigator.clipboard.writeText(txt).then(ok,ok);}
else{var ta=document.createElement("textarea");ta.value=txt;document.body.appendChild(ta);ta.select();try{document.execCommand("copy");}catch(err){}document.body.removeChild(ta);ok();}
});
})();
</script>';
}

/* ── Normalisasi input bibliografi (variabel native SLiMS) ── */
$vnx_src   = (isset($detail) && is_array($detail)) ? $detail : [];
$vnx_e     = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
$vnx_title = (string) ($title ?? ($vnx_src['title'] ?? ''));
$vnx_year  = (string) ($publish_year ?? ($vnx_src['publish_year'] ?? ''));
$vnx_ed    = (string) ($edition ?? ($vnx_src['edition'] ?? ''));
$vnx_place = (string) ($publish_place ?? ($vnx_src['publish_place'] ?? ''));
$vnx_pub   = (string) ($publisher_name ?? ($vnx_src['publisher_name'] ?? ''));

$vnx_raw = [];
if (isset($authors) && is_array($authors)) {
    foreach ($authors as $vnx_a) $vnx_raw[] = is_array($vnx_a) ? (string) ($vnx_a['author_name'] ?? '') : (string) $vnx_a;
} elseif (isset($vnx_src['authors']) && is_array($vnx_src['authors'])) {
    foreach ($vnx_src['authors'] as $vnx_a) $vnx_raw[] = is_array($vnx_a) ? (string) ($vnx_a['author_name'] ?? '') : (string) $vnx_a;
} elseif (isset($vnx_src['authors']) && is_string($vnx_src['authors']) && $vnx_src['authors'] !== '') {
    $vnx_raw = explode(';', $vnx_src['authors']);
}
$vnx_raw = array_values(array_filter(array_map('trim', $vnx_raw), function ($s) { return $s !== ''; }));

$vnx_split = function ($name) {
    $p = preg_split('/\s+/', trim((string) $name));
    if (count($p) < 2) return [trim((string) $name), ''];
    $last = array_pop($p);
    return [$last, implode(' ', $p)];
};
$vnx_inis = function ($firsts) {
    $o = [];
    foreach (preg_split('/\s+/', trim((string) $firsts)) as $q) {
        if ($q === '') continue;
        $o[] = strtoupper(substr($q, 0, 1)) . '.';
    }
    return implode(' ', $o);
};

/* ── Format penulis APA ── */
$vnx_n = [];
foreach ($vnx_raw as $vnx_nm) {
    list($vnx_l, $vnx_f) = $vnx_split($vnx_nm);
    $vnx_i = $vnx_inis($vnx_f);
    $vnx_n[] = $vnx_l . ($vnx_i !== '' ? ', ' . $vnx_i : '');
}
$c = count($vnx_n);
if ($c === 0) $vnx_au = '';
elseif ($c === 1) $vnx_au = $vnx_n[0];
elseif ($c === 2) $vnx_au = $vnx_n[0] . ', & ' . $vnx_n[1];
elseif ($c === 3) $vnx_au = $vnx_n[0] . ', ' . $vnx_n[1] . ', & ' . $vnx_n[2];
else $vnx_au = $vnx_n[0] . ', et al.';

$vnx_out = '';
if ($vnx_au !== '') $vnx_out .= $vnx_e($vnx_au) . ' ';
$vnx_out .= '(' . $vnx_e($vnx_year !== '' ? $vnx_year : 'n.d.') . '). ';
$vnx_out .= '<em>' . $vnx_e($vnx_title !== '' ? $vnx_title : 'Tanpa judul') . '</em>';
if ($vnx_ed !== '') $vnx_out .= ' (' . $vnx_e($vnx_ed) . ')';
$vnx_out .= '.';
if ($vnx_pub !== '') $vnx_out .= ' ' . $vnx_e($vnx_pub) . '.';
?>
<div class="vnx-cite">
  <div class="vnx-cite-head">
    <span class="vnx-cite-badge">APA</span>
    <span class="vnx-cite-name">APA Style · 7th Edition</span>
    <button type="button" class="vnx-cite-copy" title="Salin sitasi APA" aria-label="Salin sitasi APA"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></button>
  </div>
  <p class="vnx-cite-body"><?= $vnx_out ?></p>
</div>