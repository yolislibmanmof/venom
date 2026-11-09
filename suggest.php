<?php
/**
 * VENOM · Saran Koleksi Form
 * Form usulan buku/koleksi dengan integrasi WhatsApp/Email
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

// Ambil konfigurasi kontak dengan fallback
$wa = preg_replace('/[^0-9]/', '', (string) venom_cfg('contact.wa', ''));
$mail = (string) venom_cfg('contact.email', '');
$lib_name = venom_library_name();

// Fallback jika tidak ada kontak
$has_contact = ($wa !== '' || $mail !== '');
?>

<section class="v-wrap v-sec">
    <?= venom_section_head('sparkles', 'Saran Koleksi', 'Usulkan judul yang perlu kami sediakan di ' . venom_e($lib_name)) ?>
    
    <div class="v-card v-pad" style="max-width:620px;margin:0 auto">
        <?php if (!$has_contact): ?>
            <div style="padding:24px;text-align:center;background:color-mix(in srgb,var(--v-err) 10%,transparent);border-radius:12px;border:2px solid var(--v-err)">
                <p style="margin:0;color:var(--v-err);font-weight:600">
                    <?= venom_icon('alert', 20) ?> Kanal kontak belum dikonfigurasi
                </p>
                <p style="margin:8px 0 0 0;color:var(--v-mut);font-size:.9rem">
                    Hubungi administrator untuk mengaktifkan fitur ini.
                </p>
            </div>
        <?php else: ?>
            <form id="vSuggest" style="display:grid;gap:16px">
                <input type="hidden" name="venom_token" value="<?= venom_e(Venom_Sec::token('suggest')) ?>">
                
                <!-- Honeypot (anti-spam) -->
                <input type="text" name="venom_hp" value="" tabindex="-1" autocomplete="off" 
                       style="position:absolute;left:-9999px" aria-hidden="true">
                
                <!-- Judul -->
                <div>
                    <label style="display:block;font-weight:600;margin-bottom:6px;color:var(--v-tx)">
                        Judul Buku / Koleksi <span style="color:var(--v-err)">*</span>
                    </label>
                    <input name="judul" required maxlength="160" class="v-input" 
                           placeholder="Contoh: Laskar Pelangi">
                </div>
                
                <!-- Penulis -->
                <div>
                    <label style="display:block;font-weight:600;margin-bottom:6px;color:var(--v-tx)">
                        Penulis / Penerbit
                    </label>
                    <input name="penulis" maxlength="120" class="v-input" 
                           placeholder="Contoh: Andrea Hirata">
                </div>
                
                <!-- Alasan -->
                <div>
                    <label style="display:block;font-weight:600;margin-bottom:6px;color:var(--v-tx)">
                        Alasan / Kebutuhan
                    </label>
                    <textarea name="alasan" rows="4" maxlength="600" class="v-input" 
                              placeholder="Jelaskan mengapa koleksi ini penting untuk perpustakaan..."></textarea>
                </div>
                
                <!-- Submit Button -->
                <button class="v-btn v-btn-primary" type="submit" id="vSuggestBtn" style="justify-content:center">
                    <span id="vSuggestLabel">Kirim Usulan <?= venom_icon('right', 16) ?></span>
                </button>
                
                <p style="color:var(--v-mut);font-size:.82rem;margin:0;text-align:center">
                    Usulan akan dikirim melalui 
                    <?php if ($wa): ?>WhatsApp<?php elseif ($mail): ?>Email<?php endif; ?>
                    resmi perpustakaan.
                </p>
            </form>
        <?php endif; ?>
    </div>
</section>

<style>
.v-input {
    width: 100%;
    margin-top: 6px;
    padding: 11px 14px;
    border-radius: 12px;
    border: 1px solid var(--v-line);
    background: var(--v-surf2);
    color: var(--v-tx);
    font-size: 1rem;
    outline: 0;
    transition: all 0.3s;
}

.v-input:focus {
    border-color: var(--v-p);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--v-p) 15%, transparent);
}

textarea.v-input {
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

.v-input::placeholder {
    color: var(--v-mut);
    opacity: 0.6;
}
</style>

<script>
(function() {
    var form = document.getElementById('vSuggest');
    if (!form) return;
    
    var btn = document.getElementById('vSuggestBtn');
    var label = document.getElementById('vSuggestLabel');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Honeypot check (anti-bot)
        if (form.venom_hp.value) {
            console.warn('Spam detected');
            return;
        }
        
        // Validasi
        var judul = form.judul.value.trim();
        if (!judul) {
            window.venomToast('Judul wajib diisi', 'error');
            form.judul.focus();
            return;
        }
        
        // Disable button
        btn.disabled = true;
        label.textContent = 'Mengirim...';
        
        // Format message
        var message = 'Usulan Koleksi untuk <?= venom_e($lib_name) ?>\n\n';
        message += '📚 Judul: ' + judul + '\n';
        if (form.penulis.value.trim()) {
            message += '✍️ Penulis: ' + form.penulis.value.trim() + '\n';
        }
        if (form.alasan.value.trim()) {
            message += '\n💭 Alasan:\n' + form.alasan.value.trim();
        }
        message += '\n\nDikirim via website perpustakaan.';
        
        var wa = <?= json_encode($wa) ?>;
        var mail = <?= json_encode($mail) ?>;
        
        // Send via WhatsApp or Email
        if (wa) {
            window.open('https://wa.me/' + wa + '?text=' + encodeURIComponent(message), '_blank', 'noopener');
            window.venomToast('Mengarahkan ke WhatsApp...', 'success');
            
            setTimeout(function() {
                form.reset();
                btn.disabled = false;
                label.innerHTML = 'Kirim Usulan <?= venom_icon('right', 16) ?>';
            }, 1000);
            
        } else if (mail) {
            var subject = 'Usulan Koleksi: ' + judul;
            location.href = 'mailto:' + mail + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(message);
            window.venomToast('Mengarahkan ke Email...', 'success');
            
            setTimeout(function() {
                form.reset();
                btn.disabled = false;
                label.innerHTML = 'Kirim Usulan <?= venom_icon('right', 16) ?>';
            }, 1000);
            
        } else {
            window.venomToast('Kanal kontak belum dikonfigurasi', 'error');
            btn.disabled = false;
            label.innerHTML = 'Kirim Usulan <?= venom_icon('right', 16) ?>';
        }
    });
})();
</script>