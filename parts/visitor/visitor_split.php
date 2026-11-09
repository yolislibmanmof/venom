<?php
/**
 * VENOM · Visitor Split Layout (2-Panel)
 * Kiri: Form check-in | Kanan: Info & Trending
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/../../helpers/core.php';
require_once __DIR__ . '/../../helpers/visitor.php';

$stats = venom_visitor_stats();
$greeting = venom_visitor_greeting();
$date = venom_visitor_date();
$hours = venom_visitor_hours();
?>

<div class="v-visitor-split">
    <div class="v-split-container">
        <!-- PANEL KIRI: Form -->
        <section class="v-split-left">
            <div class="v-split-header">
                <p class="v-greeting"><?= venom_e($greeting) ?>,</p>
                <h2 class="v-welcome-title">Check-In Pengunjung</h2>
                <p class="v-date"><?= venom_e($date) ?></p>
            </div>

            <!-- Quick Stats -->
            <div class="v-quick-stats">
                <div class="v-quick-stat">
                    <strong><?= number_format($stats['today'], 0, ',', '.') ?></strong>
                    <span>Hari Ini</span>
                </div>
                <div class="v-quick-stat">
                    <strong><?= number_format($stats['week'], 0, ',', '.') ?></strong>
                    <span>Minggu Ini</span>
                </div>
                <div class="v-quick-stat">
                    <strong><?= number_format($stats['member'], 0, ',', '.') ?></strong>
                    <span>Anggota</span>
                </div>
            </div>

            <!-- Form -->
            <form id="visitorFormSplit" class="v-split-form">
                <nav class="v-tabs">
                    <button type="button" class="v-tab active" data-vtab="member">Anggota</button>
                    <button type="button" class="v-tab" data-vtab="guest">Tamu</button>
                </nav>

                <div class="v-form-section" id="vSecMember">
                    <input id="vSplitMemberId" type="text" class="v-input v-input-lg" 
                           placeholder="ID Anggota" autofocus autocomplete="off">
                </div>

                <div class="v-form-section" id="vSecMember">
                    <input id="vSplitGuestName" type="text" class="v-input v-input-lg mb-3" 
                           placeholder="Nama Lengkap" autocomplete="off">
                    <select id="vSplitInst" class="v-input v-input-lg">
                        <option value="">Pilih Institusi</option>
                        <?php foreach (venom_visitor_institutions() as $inst): ?>
                            <option value="<?= venom_e($inst['value']) ?>"><?= venom_e($inst['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
<button type="submit" id="vSplitBtn" class="v-btn v-btn-primary v-btn-lg v-btn-block"><span id="vSplitLabel">Check-In</span></button>
            </form>

            <!-- Feedback -->
<div id="vSplitFeedback" class="v-feedback" hidden><p class="v-feedback-text" id="vSplitFbText"></p></div>

            <div class="v-split-footer">
                <div class="v-clock" id="vSplitClock">00:00:00</div>
                <a href="index.php" class="v-home-link">
                    <?= venom_icon('home', 16) ?> Beranda
                </a>
            </div>
        </section>

        <!-- PANEL KANAN: Info -->
        <section class="v-split-right">
            <!-- Opening Hours -->
            <div class="v-info-card">
                <h3 class="v-info-title">
                    <?= venom_icon('clock', 18) ?> Jam Layanan
                </h3>
                <ul class="v-hours-list">
                    <?php foreach ($hours as $h): ?>
                    <li>
                        <span class="v-hours-day"><?= venom_e($h[0]) ?></span>
                        <span class="v-hours-dots"></span>
                        <span class="v-hours-time"><?= venom_e($h[1]) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Trending Books -->
            <?php if (!empty($stats['trending'])): ?>
            <div class="v-info-card">
                <h3 class="v-info-title">
                    <?= venom_icon('fire', 18) ?> Trending Minggu Ini
                </h3>
                <div class="v-trending-grid">
                    <?php foreach (array_slice($stats['trending'], 0, 3) as $book): ?>
                    <a href="index.php?p=show_detail&id=<?= (int)$book['biblio_id'] ?>" class="v-trending-item">
                        <img src="<?= venom_e(venom_cover($book['image'], $book['title'])) ?>" 
                             alt="<?= venom_e($book['title']) ?>" 
                             class="v-trending-cover">
                        <div class="v-trending-info">
                            <span class="v-trending-title"><?= venom_e(Venom_Sec::text($book['title'], 40)) ?></span>
                            <span class="v-trending-loans">
                                <?= venom_icon('refresh', 12) ?> <?= (int)$book['loan_count'] ?>x dipinjam
                            </span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
(function () {
    var clock = document.getElementById('vSplitClock');
    function tick() { if (clock) clock.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false }); }
    tick(); setInterval(tick, 1000);

    var tabs = document.querySelectorAll('[data-vtab]');
    var secM = document.getElementById('vSecMember');
    var secG = document.getElementById('vSecGuest');
    tabs.forEach(function (t) {
        t.addEventListener('click', function () {
            tabs.forEach(function (x) { x.classList.toggle('active', x === t); });
            var m = t.getAttribute('data-vtab') === 'member';
            secM.style.display = m ? '' : 'none';
            secG.style.display = m ? 'none' : '';
        });
    });

    var form = document.getElementById('visitorFormSplit'),
        btn = document.getElementById('vSplitBtn'),
        label = document.getElementById('vSplitLabel'),
        fb = document.getElementById('vSplitFeedback'),
        fbText = document.getElementById('vSplitFbText');

    function show(type, msg) {
        fb.className = 'v-feedback v-feedback-' + type;
        fb.hidden = false;
        fbText.textContent = msg;
        setTimeout(function () { fb.hidden = true; }, 3500);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var isMember = secM.style.display !== 'none';
        var id = (isMember ? document.getElementById('vSplitMemberId').value : document.getElementById('vSplitGuestName').value).trim();
        var inst = isMember ? '' : document.getElementById('vSplitInst').value;
        if (!id) { show('error', 'ID atau nama tidak boleh kosong'); return; }

        btn.disabled = true; label.textContent = 'Memproses...';
        var fd = new FormData();
        fd.append('memberID', id); fd.append('institution', inst); fd.append('counter', 'true');

        fetch('index.php?p=visitor', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json().catch(function () { return {}; }); })
            .then(function (d) {
                btn.disabled = false; label.textContent = 'Check-In';
                if (!d.status || d.status === 'success') {
                    show('success', d.message || 'Check-in berhasil!');
                    document.getElementById('vSplitMemberId').value = '';
                    document.getElementById('vSplitGuestName').value = '';
                } else show('error', d.message || 'Check-in gagal.');
            })
            .catch(function () { btn.disabled = false; label.textContent = 'Check-In'; show('error', 'Kesalahan jaringan.'); });
    });
})();
</script>

<style>
.v-visitor-split {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: var(--v-bg);
}

.v-split-container {
    max-width: 1100px;
    width: 100%;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 32px;
}

.v-split-left, .v-split-right {
    background: var(--v-glass);
    backdrop-filter: blur(20px);
    border: 1px solid var(--v-gline);
    border-radius: 24px;
    padding: 40px;
}

.v-split-header {
    margin-bottom: 24px;
}

.v-quick-stats {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}

.v-quick-stat {
    flex: 1;
    padding: 12px;
    background: var(--v-surf2);
    border-radius: 10px;
    text-align: center;
    border: 1px solid var(--v-line);
}

.v-quick-stat strong {
    display: block;
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--v-tx);
    margin-bottom: 4px;
}

.v-quick-stat span {
    font-size: 0.75rem;
    color: var(--v-mut);
}

.v-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    border-bottom: 2px solid var(--v-line);
}

.v-tab {
    flex: 1;
    padding: 12px;
    background: none;
    border: none;
    color: var(--v-mut);
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
}

.v-tab.active {
    color: var(--v-p);
    border-bottom-color: var(--v-p);
}

.v-form-section {
    margin-bottom: 16px;
}

.mb-3 {
    margin-bottom: 12px;
}

.v-split-footer {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid var(--v-line);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.v-info-card {
    margin-bottom: 24px;
    padding: 24px;
    background: var(--v-surf2);
    border-radius: 16px;
    border: 1px solid var(--v-line);
}

.v-info-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--v-tx);
    margin: 0 0 16px 0;
}

.v-hours-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.v-hours-list li {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px dashed var(--v-line);
}

.v-hours-list li:last-child {
    border-bottom: none;
}

.v-hours-day {
    font-weight: 600;
    color: var(--v-tx);
    min-width: 120px;
}

.v-hours-dots {
    flex: 1;
    border-bottom: 1px dotted var(--v-mut);
    opacity: 0.3;
}

.v-hours-time {
    font-weight: 700;
    color: var(--v-p);
}

.v-trending-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.v-trending-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--v-surf);
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s;
}

.v-trending-item:hover {
    transform: translateX(4px);
    box-shadow: var(--v-sh);
}

.v-trending-cover {
    width: 50px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
    flex-shrink: 0;
}

.v-trending-info {
    flex: 1;
}

.v-trending-title {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--v-tx);
    margin-bottom: 4px;
}

.v-trending-loans {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8rem;
    color: var(--v-mut);
}

@media (max-width: 900px) {
    .v-split-container {
        grid-template-columns: 1fr;
    }
}
</style>