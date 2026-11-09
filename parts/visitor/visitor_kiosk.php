<?php
/**
 * VENOM · Visitor Kiosk Layout (Vanilla JS — CSP-safe, zero dependency)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/../../helpers/core.php';
require_once __DIR__ . '/../../helpers/visitor.php';

$stats = venom_visitor_stats();
$greeting = venom_visitor_greeting();
$date = venom_visitor_date();
$quote = venom_visitor_quote();
$has_featured = !empty($stats['featured']);
$progress = min(100, ($stats['today'] / 50) * 100);
$circ = 2 * M_PI * 28;
?>

<div class="v-visitor-kiosk">
    <div class="v-kiosk-card">
        <!-- Scan Animation Badge -->
        <div class="v-scan-badge" aria-hidden="true">
            <svg class="v-barcode-svg" viewBox="0 0 64 64">
                <path d="M4 4h4v56H4zM12 4h2v56h-2zM20 4h4v56h-4zM28 4h2v56h-2zM36 4h4v56h-4zM44 4h2v56h-2zM52 4h8v56h-8z"/>
            </svg>
            <div class="v-scan-laser"></div>
        </div>

        <!-- Header -->
        <div class="v-kiosk-header">
            <p class="v-greeting"><?= venom_e($greeting) ?>,</p>
            <h2 class="v-welcome-title">Selamat Datang</h2>
            <p class="v-subtitle">di <?= venom_e(venom_library_name()) ?></p>
            <p class="v-date"><?= venom_e($date) ?></p>
        </div>

        <!-- Live Statistics -->
        <div class="v-kiosk-stats">
            <div class="v-stat-item">
                <span class="v-stat-num"><?= number_format($stats['today'], 0, ',', '.') ?></span>
                <span class="v-stat-label">Hari Ini</span>
            </div>
            <div class="v-stat-item v-stat-with-ring">
                <svg class="v-progress-ring" viewBox="0 0 64 64">
                    <circle class="v-ring-bg" cx="32" cy="32" r="28"></circle>
                    <circle class="v-ring-fg" cx="32" cy="32" r="28"
                            style="stroke-dasharray: <?= $circ ?>; stroke-dashoffset: <?= $circ * (1 - $progress / 100) ?>;"></circle>
                </svg>
                <span class="v-stat-num"><?= number_format($stats['week'], 0, ',', '.') ?></span>
                <span class="v-stat-label">Minggu Ini</span>
            </div>
            <div class="v-stat-item">
                <span class="v-stat-num"><?= number_format($stats['member'], 0, ',', '.') ?></span>
                <span class="v-stat-label">Anggota</span>
            </div>
        </div>

        <!-- Form (Vanilla JS, tanpa Vue) -->
        <form id="visitorForm" class="v-kiosk-form" autocomplete="off">
            <div class="v-form-group">
                <input id="vMemberId" type="text" class="v-input v-input-lg"
                       placeholder="ID Anggota atau Nama" autofocus>
            </div>
            <div class="v-form-group">
                <input id="vInstitution" type="text" class="v-input v-input-lg"
                       placeholder="Institusi (opsional)">
                <div class="v-inst-chips">
                    <?php foreach (array_slice(venom_visitor_institutions(), 0, 4) as $inst): ?>
                        <button type="button" class="v-chip" data-inst="<?= venom_e($inst['label']) ?>"><?= venom_e($inst['label']) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" id="vSubmitBtn" class="v-btn v-btn-primary v-btn-lg v-btn-block">
                <span id="vBtnLabel"><?= venom_icon('right', 18) ?> Masuk</span>
            </button>
        </form>

        <!-- Feedback Area (hidden default) -->
        <div id="vFeedback" class="v-feedback" hidden>
            <div class="v-feedback-icon" id="vFeedbackIcon"></div>
            <p class="v-feedback-text" id="vFeedbackText"></p>
            <p class="v-feedback-hint" id="vFeedbackHint"></p>
        </div>

        <!-- Featured Book -->
        <?php if ($has_featured): ?>
        <a href="index.php?p=show_detail&id=<?= (int)$stats['featured']['id'] ?>" class="v-featured-book">
            <img src="<?= venom_e(venom_cover($stats['featured']['image'], $stats['featured']['title'])) ?>"
                 alt="<?= venom_e($stats['featured']['title']) ?>" class="v-featured-cover">
            <div class="v-featured-body">
                <span class="v-featured-tag">Koleksi Terbaru</span>
                <span class="v-featured-title"><?= venom_e(Venom_Sec::text($stats['featured']['title'], 48)) ?></span>
                <span class="v-featured-cta">Lihat Detail →</span>
            </div>
        </a>
        <?php endif; ?>

        <!-- Quote -->
        <blockquote class="v-quote">
            <span class="v-quote-mark">"</span>
            <span class="v-quote-text"><?= venom_e($quote) ?></span>
        </blockquote>

        <!-- Footer -->
        <div class="v-kiosk-footer">
            <div class="v-clock" id="vClock">00:00:00</div>
            <a href="index.php" class="v-home-link"><?= venom_icon('left', 16) ?> Beranda</a>
        </div>
    </div>
</div>

<script>
/* ═══ VENOM Visitor Kiosk Controller (Vanilla JS) ═══ */
(function () {
    var SVG_OK = '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
    var SVG_NO = '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>';

    /* Jam real-time */
    var clock = document.getElementById('vClock');
    function tick() { if (clock) clock.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false }); }
    tick(); setInterval(tick, 1000);

    /* Chips institusi */
    var instInput = document.getElementById('vInstitution');
    document.querySelectorAll('.v-chip[data-inst]').forEach(function (c) {
        c.addEventListener('click', function () {
            instInput.value = c.getAttribute('data-inst');
            document.querySelectorAll('.v-chip[data-inst]').forEach(function (x) { x.classList.remove('v-chip-active'); });
            c.classList.add('v-chip-active');
        });
    });

    /* Feedback dengan countdown */
    var fb = document.getElementById('vFeedback'),
        fbIcon = document.getElementById('vFeedbackIcon'),
        fbText = document.getElementById('vFeedbackText'),
        fbHint = document.getElementById('vFeedbackHint'),
        fbTimer = null;

    function showFeedback(type, msg) {
        fb.className = 'v-feedback v-feedback-' + type;
        fb.hidden = false;
        fbIcon.innerHTML = (type === 'success') ? SVG_OK : SVG_NO;
        fbText.textContent = msg;
        var n = 5;
        fbHint.textContent = 'Otomatis tertutup dalam ' + n + ' detik...';
        clearInterval(fbTimer);
        fbTimer = setInterval(function () {
            n--;
            if (n <= 0) { clearInterval(fbTimer); fb.hidden = true; }
            else fbHint.textContent = 'Otomatis tertutup dalam ' + n + ' detik...';
        }, 1000);
    }

    /* Submit form */
    var form = document.getElementById('visitorForm'),
        idInput = document.getElementById('vMemberId'),
        btn = document.getElementById('vSubmitBtn'),
        label = document.getElementById('vBtnLabel');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var id = idInput.value.trim();
        if (!id) { showFeedback('error', 'ID Anggota atau nama tidak boleh kosong'); idInput.focus(); return; }

        btn.disabled = true;
        label.textContent = 'Memproses...';

        var fd = new FormData();
        fd.append('memberID', id);
        fd.append('institution', instInput.value);
        fd.append('counter', 'true');

        fetch('index.php?p=visitor', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json().catch(function () { return {}; }); })
            .then(function (data) {
                btn.disabled = false;
                label.innerHTML = '<?= venom_icon('right', 18) ?> Masuk';
                if (!data.status || data.status === 'success') {
                    showFeedback('success', data.message || 'Check-in berhasil! Selamat menikmati koleksi.');
                    idInput.value = ''; instInput.value = '';
                    document.querySelectorAll('.v-chip[data-inst]').forEach(function (x) { x.classList.remove('v-chip-active'); });
                } else {
                    showFeedback('error', data.message || 'Check-in gagal. Periksa kembali data Anda.');
                }
            })
            .catch(function () {
                btn.disabled = false;
                label.innerHTML = '<?= venom_icon('right', 18) ?> Masuk';
                showFeedback('error', 'Terjadi kesalahan jaringan. Coba lagi.');
            });
    });
})();
</script>

<style>
.v-visitor-kiosk{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;background:var(--v-bg)}
.v-kiosk-card{position:relative;max-width:520px;width:100%;background:var(--v-glass);backdrop-filter:blur(20px);border:1px solid var(--v-gline);border-radius:24px;padding:48px 40px;box-shadow:var(--v-sh2);text-align:center}
.v-scan-badge{position:absolute;top:24px;right:24px;width:48px;height:48px;background:linear-gradient(135deg,var(--v-p),var(--v-a));border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px color-mix(in srgb,var(--v-p) 40%,transparent)}
.v-barcode-svg{width:28px;height:28px;fill:#fff}
.v-scan-laser{position:absolute;width:2px;height:32px;background:#fff;box-shadow:0 0 8px #fff;animation:scanLaser 2s ease-in-out infinite}
@keyframes scanLaser{0%,100%{transform:translateX(-8px);opacity:0}50%{transform:translateX(8px);opacity:1}}
.v-kiosk-header{margin-bottom:32px}
.v-greeting{font-size:1rem;color:var(--v-mut);margin:0 0 8px 0}
.v-welcome-title{font-size:2rem;font-weight:800;background:linear-gradient(135deg,var(--v-p),var(--v-a));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin:0 0 8px 0}
.v-subtitle{font-size:1rem;color:var(--v-tx);margin:0 0 8px 0;font-weight:600}
.v-date{font-size:.85rem;color:var(--v-mut);margin:0}
.v-kiosk-stats{display:flex;gap:16px;margin-bottom:32px}
.v-stat-item{flex:1;padding:16px 12px;background:var(--v-surf2);border-radius:12px;border:1px solid var(--v-line)}
.v-stat-num{display:block;font-size:1.5rem;font-weight:800;color:var(--v-tx);margin-bottom:4px}
.v-stat-label{display:block;font-size:.75rem;color:var(--v-mut);text-transform:uppercase;letter-spacing:.05em}
.v-stat-with-ring{position:relative}
.v-progress-ring{position:absolute;top:8px;right:8px;width:32px;height:32px;transform:rotate(-90deg)}
.v-ring-bg{fill:none;stroke:var(--v-line);stroke-width:4}
.v-ring-fg{fill:none;stroke:var(--v-p);stroke-width:4;stroke-linecap:round;transition:stroke-dashoffset 1s ease}
.v-kiosk-form{margin-bottom:24px}
.v-form-group{margin-bottom:16px}
.v-input{width:100%;padding:14px 16px;border:2px solid var(--v-line);border-radius:12px;font-size:1rem;background:var(--v-surf);color:var(--v-tx);transition:all .3s}
.v-input:focus{border-color:var(--v-p);outline:none;box-shadow:0 0 0 4px color-mix(in srgb,var(--v-p) 15%,transparent)}
.v-input-lg{padding:16px 18px;font-size:1.05rem}
.v-inst-chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;justify-content:center}
.v-chip{padding:6px 14px;border-radius:999px;background:var(--v-surf2);border:1px solid var(--v-line);color:var(--v-tx);font-size:.8rem;font-weight:600;cursor:pointer;transition:all .2s}
.v-chip:hover{border-color:var(--v-p);color:var(--v-p)}
.v-chip-active{background:linear-gradient(135deg,var(--v-p),var(--v-a));color:#fff;border-color:transparent}
.v-btn-lg{padding:16px;font-size:1.05rem}
.v-btn-block{width:100%;justify-content:center}
.v-feedback{margin-top:24px;padding:24px;border-radius:16px;text-align:center;animation:feedbackSlide .4s ease}
.v-feedback[hidden]{display:none}
@keyframes feedbackSlide{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
.v-feedback-success{background:color-mix(in srgb,var(--v-ok) 15%,transparent);border:2px solid var(--v-ok)}
.v-feedback-error{background:color-mix(in srgb,var(--v-err) 15%,transparent);border:2px solid var(--v-err)}
.v-feedback-icon{margin-bottom:12px}
.v-feedback-success .v-feedback-icon{color:var(--v-ok)}
.v-feedback-error .v-feedback-icon{color:var(--v-err)}
.v-feedback-text{font-size:1.1rem;font-weight:700;color:var(--v-tx);margin:0 0 8px 0}
.v-feedback-hint{font-size:.85rem;color:var(--v-mut);margin:0}
.v-featured-book{display:flex;align-items:center;gap:16px;margin-top:24px;padding:16px;background:var(--v-surf2);border-radius:12px;border:1px solid var(--v-line);text-decoration:none;transition:all .3s}
.v-featured-book:hover{border-color:var(--v-p);transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.1)}
.v-featured-cover{width:60px;height:80px;object-fit:cover;border-radius:8px;flex-shrink:0}
.v-featured-body{flex:1;text-align:left}
.v-featured-tag{display:inline-block;padding:3px 10px;background:linear-gradient(135deg,var(--v-p),var(--v-a));color:#fff;font-size:.7rem;font-weight:700;border-radius:999px;margin-bottom:6px}
.v-featured-title{display:block;font-size:.9rem;font-weight:600;color:var(--v-tx);margin-bottom:4px}
.v-featured-cta{display:block;font-size:.8rem;color:var(--v-p);font-weight:600}
.v-quote{margin-top:24px;padding:20px;background:var(--v-surf2);border-radius:12px;border-left:4px solid var(--v-p);text-align:left;font-style:italic}
.v-quote-mark{font-size:2rem;font-weight:800;color:var(--v-p);line-height:1;margin-right:4px}
.v-quote-text{font-size:.95rem;color:var(--v-tx);line-height:1.6}
.v-kiosk-footer{margin-top:32px;padding-top:24px;border-top:1px solid var(--v-line);display:flex;justify-content:space-between;align-items:center}
.v-clock{font-size:1.5rem;font-weight:800;color:var(--v-tx);font-variant-numeric:tabular-nums}
.v-home-link{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:var(--v-surf2);border:1px solid var(--v-line);border-radius:10px;color:var(--v-tx);text-decoration:none;font-weight:600;transition:all .2s}
.v-home-link:hover{border-color:var(--v-p);color:var(--v-p)}
@media (max-width:600px){.v-kiosk-card{padding:32px 24px}.v-kiosk-stats{flex-direction:column}}
</style>