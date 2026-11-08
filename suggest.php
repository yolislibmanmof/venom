<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';
$wa = preg_replace('/[^0-9]/', '', (string) venom_cfg('contact.wa', ''));
$mail = (string) venom_cfg('contact.email', '');
?>
<section class="v-wrap v-sec">
  <?= venom_section_head('sparkles', 'Saran Koleksi', 'Usulkan judul yang perlu kami sediakan') ?>
  <form class="v-card v-pad" id="vSuggest" style="max-width:620px;display:grid;gap:14px">
    <input type="hidden" name="venom_token" value="<?= venom_e(Venom_Sec::token('suggest')) ?>">
    <input type="text" name="venom_hp" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
    <label>Judul yang diusulkan<input name="judul" required maxlength="160" class="v-input"></label>
    <label>Penulis / Penerbit<input name="penulis" maxlength="120" class="v-input"></label>
    <label>Alasan / Kebutuhan<textarea name="alasan" rows="4" maxlength="600" class="v-input"></textarea></label>
    <button class="v-btn v-btn-primary" type="submit">Kirim Usulan<?= venom_icon('right', 16) ?></button>
    <p style="color:var(--v-mut);font-size:.82rem;margin:0">Usulan dikirim melalui kanal resmi perpustakaan.</p>
  </form>
</section>
<style>.v-input{width:100%;margin-top:6px;padding:11px 14px;border-radius:12px;border:1px solid var(--v-line);background:var(--v-surf2);outline:0}
.v-input:focus{border-color:var(--v-p)}</style>
<script>
document.getElementById('vSuggest').addEventListener('submit',function(e){
  e.preventDefault();var f=e.target;if(f.venom_hp.value){return;}
  var t='Usulan Koleksi%0AJudul: '+encodeURIComponent(f.judul.value)+
        '%0APenulis: '+encodeURIComponent(f.penulis.value)+'%0AAlasan: '+encodeURIComponent(f.alasan.value);
  var wa=<?= json_encode($wa) ?>,mail=<?= json_encode($mail) ?>;
  if(wa){window.open('https://wa.me/'+wa+'?text='+t,'_blank','noopener');}
  else if(mail){location.href='mailto:'+mail+'?subject=Usulan%20Koleksi&body='+t;}
  else{window.venomToast('Kanal kontak belum dikonfigurasi');}
});
</script>