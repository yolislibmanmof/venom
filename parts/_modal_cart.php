<?php defined('INDEX_AUTH') OR die('Direct access not allowed!'); ?>
<div class="vnx-modal" id="vCartModal" aria-hidden="true">
    <div class="vnx-modal-panel vnx-modal-center">
        <div class="vnx-modal-head">
            <h3 class="vnx-modal-title"><span class="v-grad-text">Keranjang Sitasi</span></h3>
            <button class="vnx-modal-close" data-close><?= venom_icon('x', 20) ?></button>
        </div>
        <div class="vnx-modal-body">
            <div id="vCartList" class="v-cart-list">
                <p class="v-empty-cart-msg">Belum ada buku yang ditambahkan ke keranjang sitasi.</p>
            </div>
            <div class="v-cart-actions mt-4" style="display:flex; gap:10px; justify-content:flex-end; border-top:1px solid var(--v-line); padding-top:20px;">
                <button class="vnx-btn vnx-btn-ghost" id="clearCart">Kosongkan</button>
                <button class="vnx-btn vnx-btn-primary" id="exportBibTeX">Export BibTeX</button>
            </div>
        </div>
    </div>
</div>