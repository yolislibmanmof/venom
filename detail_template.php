<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

// Fallback: Jika SLiMS merender konten sendiri, bungkus agar tetap rapi
$core_content = venom_core_content();
if ($core_content !== '' && empty($detail)) {
    echo '<div class="v-wrap v-core v-page-transition">' . $core_content . '</div>';
    return;
}

// Jika tidak ada data detail, tampilkan empty state
if (empty($detail) || !isset($detail['title'])) {
    echo '<div class="v-wrap v-sec">' . venom_empty('Detail koleksi tidak ditemukan.') . '</div>';
    return;
}

$title = $detail['title'] ?? 'Tanpa Judul';
$authors = $detail['authors'] ?? ($detail['author'] ?? 'Penulis tidak diketahui');
$year = $detail['publish_year'] ?? '—';
$publisher = $detail['publisher_name'] ?? '—';
$isbn = $detail['isbn_issn'] ?? '—';
$call_number = $detail['call_number'] ?? '—';
$notes = $detail['notes'] ?? '';
$topics = $detail['topics'] ?? [];
$items = $detail['items'] ?? [];
$image = $detail['image'] ?? '';
$cover_url = venom_cover($image, $title);
?>

<div class="v-wrap v-sec v-page-transition">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" style="margin-bottom: 24px; color: var(--v-mut); font-size: 0.9rem;">
        <a href="index.php" class="text-decoration-none" style="color: var(--v-mut);">Beranda</a>
        <span style="margin: 0 8px;">/</span>
        <span style="color: var(--v-tx); font-weight: 600;">Detail Koleksi</span>
    </nav>

    <div class="v-card" style="overflow: hidden;">
        <div style="display: grid; grid-template-columns: 320px 1fr;">
            
            <!-- Left: Cover & Actions -->
            <div style="background: var(--v-bg2); padding: 32px; display: flex; flex-direction: column; align-items: center; border-right: 1px solid var(--v-line);">
                <img src="<?= venom_e($cover_url) ?>" alt="<?= venom_e($title) ?>" 
                     style="width: 100%; max-width: 220px; border-radius: 12px; box-shadow: var(--v-sh2); margin-bottom: 24px; object-fit: cover; aspect-ratio: 3/4;">
                
                <div style="width: 100%; display: flex; flex-direction: column; gap: 12px;">
                    <button class="v-btn v-btn-primary" style="width: 100%; justify-content: center;" onclick="switchDetailTab('citation', document.getElementById('btn-tab-citation'))">
                        <?= venom_icon('quote', 18) ?> Buat Sitasi
                    </button>
                    <button class="v-btn v-btn-ghost" style="width: 100%; justify-content: center; border: 1px solid var(--v-line);" onclick="window.print()">
                        <?= venom_icon('print', 18) ?> Cetak
                    </button>
                </div>
            </div>

            <!-- Right: Metadata & Tabs -->
            <div style="padding: 32px;">
                <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 8px 0; line-height: 1.3; color: var(--v-tx);">
                    <?= venom_e($title) ?>
                </h1>
                <p style="font-size: 1.1rem; color: var(--v-p); font-weight: 600; margin: 0 0 24px 0;">
                    <?= venom_e($authors) ?>
                </p>

                <!-- Tabs Navigation -->
                <div style="display: flex; gap: 8px; border-bottom: 2px solid var(--v-line); margin-bottom: 24px;">
                    <button id="btn-tab-desc" class="v-tab-btn is-active" onclick="switchDetailTab('desc', this)" style="padding: 12px 20px; background: none; border: none; border-bottom: 3px solid var(--v-p); color: var(--v-p); font-weight: 700; cursor: pointer; transition: all 0.2s;">Deskripsi</button>
                    <button id="btn-tab-items" class="v-tab-btn" onclick="switchDetailTab('items', this)" style="padding: 12px 20px; background: none; border: none; border-bottom: 3px solid transparent; color: var(--v-mut); font-weight: 600; cursor: pointer; transition: all 0.2s;">Eksemplar (<?= count($items) ?>)</button>
                    <button id="btn-tab-citation" class="v-tab-btn" onclick="switchDetailTab('citation', this)" style="padding: 12px 20px; background: none; border: none; border-bottom: 3px solid transparent; color: var(--v-mut); font-weight: 600; cursor: pointer; transition: all 0.2s;">Sitasi</button>
                </div>

                <!-- Tab 1: Deskripsi -->
                <div id="tab-desc" class="v-tab-content" style="display: block;">
                    <div style="display: grid; grid-template-columns: 130px 1fr; gap: 16px; font-size: 0.95rem; line-height: 1.7;">
                        <div style="color: var(--v-mut); font-weight: 600;">Penerbit</div>
                        <div><?= venom_e($publisher) ?>, <?= venom_e($year) ?></div>
                        
                        <div style="color: var(--v-mut); font-weight: 600;">ISBN/ISSN</div>
                        <div><?= venom_e($isbn) ?></div>
                        
                        <div style="color: var(--v-mut); font-weight: 600;">No. Panggil</div>
                        <div><span class="v-chip" style="font-family: monospace; font-size: 1rem; background: var(--v-bg2);"><?= venom_e($call_number) ?></span></div>
                        
                        <?php if (!empty($topics)): ?>
                        <div style="color: var(--v-mut); font-weight: 600;">Subjek</div>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <?php foreach ($topics as $t): ?>
                                <a href="index.php?keywords=<?= urlencode($t['topic']) ?>" class="v-chip" style="text-decoration: none;">
                                    <?= venom_e($t['topic']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($notes)): ?>
                    <div style="margin-top: 32px; padding: 20px; background: var(--v-bg2); border-radius: 12px; border-left: 4px solid var(--v-p);">
                        <h4 style="margin: 0 0 12px 0; font-size: 1rem; font-weight: 700; color: var(--v-tx);">Abstrak / Catatan</h4>
                        <p style="margin: 0; color: var(--v-mut); white-space: pre-wrap; line-height: 1.6;"><?= venom_e($notes) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tab 2: Eksemplar -->
                <div id="tab-items" class="v-tab-content" style="display: none;">
                    <?php if (empty($items)): ?>
                        <p style="color: var(--v-mut); padding: 20px 0;">Belum ada data eksemplar untuk koleksi ini.</p>
                    <?php else: ?>
                        <div style="overflow-x: auto; border: 1px solid var(--v-line); border-radius: 12px;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                                <thead>
                                    <tr style="background: var(--v-bg2); text-align: left;">
                                        <th style="padding: 14px 16px; color: var(--v-mut); font-weight: 600; border-bottom: 1px solid var(--v-line);">Kode Barang</th>
                                        <th style="padding: 14px 16px; color: var(--v-mut); font-weight: 600; border-bottom: 1px solid var(--v-line);">No. Panggil</th>
                                        <th style="padding: 14px 16px; color: var(--v-mut); font-weight: 600; border-bottom: 1px solid var(--v-line);">Lokasi</th>
                                        <th style="padding: 14px 16px; color: var(--v-mut); font-weight: 600; border-bottom: 1px solid var(--v-line);">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): 
                                        // Logika status: jika ada field is_loan atau serupa, sesuaikan. Fallback ke 'Tersedia'
                                        $is_available = empty($item['is_loan']) || $item['is_loan'] == 0;
                                        $status_text = $is_available ? 'Tersedia' : 'Sedang Dipinjam';
                                        $status_style = $is_available ? 'background: #dcfce7; color: #166534;' : 'background: #fee2e2; color: #991b1b;';
                                    ?>
                                    <tr style="border-bottom: 1px solid var(--v-line); transition: background 0.2s;">
                                        <td style="padding: 14px 16px; font-family: monospace; color: var(--v-tx);"><?= venom_e($item['item_code']) ?></td>
                                        <td style="padding: 14px 16px; color: var(--v-tx);"><?= venom_e($item['call_number']) ?></td>
                                        <td style="padding: 14px 16px; color: var(--v-mut);"><?= venom_e($item['location_name'] ?? 'Umum') ?></td>
                                        <td style="padding: 14px 16px;">
                                            <span style="padding: 6px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; display: inline-block; <?= $status_style ?>">
                                                <?= $status_text ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab 3: Sitasi -->
                <div id="tab-citation" class="v-tab-content" style="display: none;">
                    <div style="margin-bottom: 16px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--v-mut); display: block; margin-bottom: 8px;">Pilih Gaya Sitasi</label>
                        <select id="citationStyle" onchange="updateCitation()" style="width: 100%; max-width: 240px; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--v-line); background: var(--v-surf); color: var(--v-tx); font-size: 0.9rem; cursor: pointer;">
                            <option value="apa">APA Style (7th ed.)</option>
                            <option value="mla">MLA Style (9th ed.)</option>
                            <option value="chicago">Chicago Style (17th ed.)</option>
                        </select>
                    </div>
                    <div style="position: relative; background: var(--v-bg2); padding: 24px; border-radius: 12px; border: 1px solid var(--v-line);">
                        <div id="citationOutput" style="margin: 0; font-family: 'Segoe UI', sans-serif; font-size: 0.95rem; line-height: 1.7; color: var(--v-tx);"></div>
                        <button onclick="copyCitation()" class="v-btn v-btn-sm" style="position: absolute; top: 16px; right: 16px; background: var(--v-surf); border: 1px solid var(--v-line);">
                            <?= venom_icon('copy', 16) ?> Salin
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function switchDetailTab(tabName, btn) {
    document.querySelectorAll('.v-tab-content').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + tabName).style.display = 'block';
    
    document.querySelectorAll('.v-tab-btn').forEach(el => {
        el.style.borderBottomColor = 'transparent';
        el.style.color = 'var(--v-mut)';
        el.style.fontWeight = '600';
    });
    btn.style.borderBottomColor = 'var(--v-p)';
    btn.style.color = 'var(--v-p)';
    btn.style.fontWeight = '700';
}

function updateCitation() {
    const style = document.getElementById('citationStyle').value;
    const title = "<?= addslashes($title) ?>";
    const author = "<?= addslashes($authors) ?>";
    const year = "<?= addslashes($year) ?>";
    const publisher = "<?= addslashes($publisher) ?>";
    
    let html = "";
    if (style === 'apa') {
        html = author + " (" + year + "). <em>" + title + "</em>. " + publisher + ".";
    } else if (style === 'mla') {
        html = author + ". <em>" + title + "</em>. " + publisher + ", " + year + ".";
    } else if (style === 'chicago') {
        html = author + ". <em>" + title + "</em>. Jakarta: " + publisher + ", " + year + ".";
    }
    document.getElementById('citationOutput').innerHTML = html;
}

function copyCitation() {
    const text = document.getElementById('citationOutput').innerText;
    navigator.clipboard.writeText(text).then(() => {
        if (typeof venomToast === 'function') venomToast('Sitasi berhasil disalin!');
    });
}

// Init citation on load
document.addEventListener('DOMContentLoaded', updateCitation);
</script>

<style>
.v-tab-content { animation: vPageFade 0.3s ease-out; }
@media (max-width: 768px) {
    .v-wrap .v-card > div { grid-template-columns: 1fr !important; }
    .v-wrap .v-card > div > div:first-child { border-right: none !important; border-bottom: 1px solid var(--v-line); }
}
</style>