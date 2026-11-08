<?php
/**
 * VENOM · Floating Actions
 * HANYA Scroll to Top. (Tombol Mode/Palette SUDAH ada di Navbar)
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
?>

<div class="v-fab" id="vFab">
    <button type="button" class="v-fab-btn" id="vScrollTop" aria-label="Kembali ke atas" hidden>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 15l-6-6-6 6"/>
        </svg>
    </button>
</div>

<script>
(function() {
    var scrollTopBtn = document.getElementById('vScrollTop');
    if (!scrollTopBtn) return;
    
    window.addEventListener('scroll', function() {
        scrollTopBtn.hidden = window.pageYOffset <= 400;
    }, { passive: true });
    
    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
</script>