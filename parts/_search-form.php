<?php
/**
 * VENOM · Premium Search Form
 * Features: autocomplete, voice search, recent searches, filter chips
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

$venom_q = isset($_GET['keywords']) ? trim((string) $_GET['keywords']) : '';
$venom_recent = isset($_SESSION['venom_recent_searches']) ? (array) $_SESSION['venom_recent_searches'] : [];
?>

<form class="vnx-search" action="index.php" method="get" role="search" autocomplete="off">
  <div class="vnx-search-box">
    <span class="vnx-search-icon">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/>
        <path d="m21 21-4.35-4.35"/>
      </svg>
    </span>
    <input 
      type="search" 
      name="keywords" 
      value="<?= venom_e($venom_q) ?>" 
      maxlength="200"
      placeholder="<?= venom_e(venom_t('search_placeholder', 'Cari judul, penulis, atau kata kunci...')) ?>" 
      aria-label="Kata kunci pencarian"
      id="vnxSearchInput"
      autocomplete="off"
    >
    <?php if ($venom_q !== ''): ?>
      <button type="button" class="vnx-search-clear" id="vnxSearchClear" aria-label="Hapus pencarian">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    <?php endif; ?>
    <button type="button" class="vnx-search-voice" id="vnxSearchVoice" aria-label="Pencarian suara" title="Cari dengan suara">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
        <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
        <line x1="12" y1="19" x2="12" y2="23"/>
        <line x1="8" y1="23" x2="16" y2="23"/>
      </svg>
    </button>
    <button type="submit" name="search" value="Search" class="vnx-search-submit" aria-label="Cari">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="5" y1="12" x2="19" y2="12"/>
        <polyline points="12 5 19 12 12 19"/>
      </svg>
    </button>
  </div>

  <!-- Dropdown: Suggestions & Recent -->
  <div class="vnx-search-dropdown" id="vnxSearchDropdown" hidden>
    <?php if (!empty($venom_recent)): ?>
      <div class="vnx-search-section">
        <div class="vnx-search-section-head">
          <span class="vnx-search-section-title">Pencarian Terakhir</span>
          <button type="button" class="vnx-search-clear-all" id="vnxClearRecent">Hapus semua</button>
        </div>
        <div class="vnx-search-chips">
          <?php foreach (array_slice($venom_recent, 0, 5) as $rq): ?>
            <button type="button" class="vnx-search-chip" data-recent="<?= venom_e($rq) ?>">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="1 4 1 10 7 10"/>
                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
              </svg>
              <span><?= venom_e($rq) ?></span>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="vnx-search-section">
      <div class="vnx-search-section-title">Saran Pencarian</div>
      <div class="vnx-search-suggestions" id="vnxSuggestions">
        <!-- Diisi via JS -->
      </div>
    </div>
  </div>
</form>

<script>
(function(){
  var input = document.getElementById('vnxSearchInput');
  var dropdown = document.getElementById('vnxSearchDropdown');
  var clearBtn = document.getElementById('vnxSearchClear');
  var clearAll = document.getElementById('vnxClearRecent');
  var voiceBtn = document.getElementById('vnxSearchVoice');
  var suggestions = document.getElementById('vnxSuggestions');
  var form = input ? input.closest('form') : null;
  if (!input || !dropdown) return;

  // Show/hide dropdown
  input.addEventListener('focus', function(){
    dropdown.hidden = false;
  });
  document.addEventListener('click', function(e){
    if (!form.contains(e.target)) dropdown.hidden = true;
  });

  // Clear button
  if (clearBtn) {
    clearBtn.addEventListener('click', function(){
      input.value = '';
      input.focus();
      clearBtn.remove();
    });
  }

  // Clear all recent
  if (clearAll) {
    clearAll.addEventListener('click', function(){
      try { sessionStorage.removeItem('venom_recent'); } catch(e){}
      dropdown.hidden = true;
    });
  }

  // Recent chips
  dropdown.addEventListener('click', function(e){
    var chip = e.target.closest('[data-recent]');
    if (!chip) return;
    input.value = chip.getAttribute('data-recent');
    if (form) form.submit();
  });

  // Voice search
  if (voiceBtn && 'webkitSpeechRecognition' in window) {
    voiceBtn.addEventListener('click', function(){
      var recognition = new webkitSpeechRecognition();
      recognition.lang = 'id-ID';
      recognition.interimResults = false;
      recognition.onresult = function(event){
        var transcript = event.results[0][0].transcript;
        input.value = transcript;
        if (form) form.submit();
      };
      recognition.start();
      voiceBtn.classList.add('is-listening');
      recognition.onend = function(){ voiceBtn.classList.remove('is-listening'); };
    });
  } else if (voiceBtn) {
    voiceBtn.style.display = 'none';
  }

  // Live suggestions (simple keyword match)
  var debounce = null;
  input.addEventListener('input', function(){
    clearTimeout(debounce);
    var q = input.value.trim();
    if (q.length < 2) { suggestions.innerHTML = ''; return; }
    debounce = setTimeout(function(){
      fetch('index.php?p=show_detail&keywords=' + encodeURIComponent(q) + '&ajax=suggest')
        .then(function(r){ return r.json(); })
        .then(function(data){
          suggestions.innerHTML = '';
          (data || []).slice(0, 5).forEach(function(item){
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'vnx-search-suggest-item';
            btn.innerHTML = '<span>' + item.title + '</span><small>' + (item.author || '') + '</small>';
            btn.addEventListener('click', function(){
              window.location.href = 'index.php?p=show_detail&id=' + item.id;
            });
            suggestions.appendChild(btn);
          });
        })
        .catch(function(){ suggestions.innerHTML = ''; });
    }, 300);
  });
})();
</script>