<?php
/**
 * VENOM · Enhanced Live Visitor Ticker
 * Running text dengan cover cards untuk trending books
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/../../helpers/core.php';
require_once __DIR__ . '/../../helpers/visitor.php';

$stats = venom_visitor_stats();
$trending = array_slice($stats['trending'] ?? [], 0, 8);
?>

<?php if (!empty($trending)): ?>
<div class="v-enhanced-ticker">
    <div class="v-ticker-label">
        <span class="v-ticker-dot"></span>
        <span>Sedang Trending</span>
    </div>
    
    <div class="v-ticker-track">
        <?php for ($loop = 0; $loop < 2; $loop++): ?>
        <div class="v-ticker-group">
            <?php foreach ($trending as $book): ?>
            <a href="index.php?p=show_detail&id=<?= (int)$book['biblio_id'] ?>" class="v-ticker-card">
                <img src="<?= venom_e(venom_cover($book['image'], $book['title'])) ?>" 
                     alt="<?= venom_e($book['title']) ?>" 
                     class="v-ticker-cover"
                     onerror="this.style.display='none'">
                <div class="v-ticker-body">
                    <span class="v-ticker-badge v-ticker-badge-book">
                        <?= venom_icon('book', 12) ?> Buku
                    </span>
                    <span class="v-ticker-title"><?= venom_e(Venom_Sec::text($book['title'], 48)) ?></span>
                    <span class="v-ticker-loans">
                        <?= venom_icon('refresh', 12) ?> <?= (int)$book['loan_count'] ?>x
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>

<style>
.v-enhanced-ticker {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: var(--v-glass);
    backdrop-filter: blur(16px);
    border-top: 1px solid var(--v-gline);
    padding: 12px 0;
    overflow: hidden;
}

.v-ticker-label {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--v-tx);
    background: var(--v-surf);
    padding: 8px 16px;
    border-radius: 999px;
    z-index: 10;
    box-shadow: var(--v-sh);
}

.v-ticker-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--v-ok);
    animation: tickerPulse 2s ease-in-out infinite;
}

@keyframes tickerPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.3); }
}

.v-ticker-track {
    display: flex;
    animation: tickerScroll 60s linear infinite;
    padding-left: 180px;
}

@keyframes tickerScroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

.v-ticker-group {
    display: flex;
    gap: 16px;
    flex-shrink: 0;
}

.v-ticker-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 16px 8px 8px;
    background: var(--v-surf2);
    border: 1px solid var(--v-line);
    border-radius: 12px;
    text-decoration: none;
    min-width: 280px;
    transition: all 0.2s;
}

.v-ticker-card:hover {
    border-color: var(--v-p);
    transform: translateY(-2px);
    box-shadow: var(--v-sh);
}

.v-ticker-cover {
    width: 48px;
    height: 64px;
    object-fit: cover;
    border-radius: 8px;
    flex-shrink: 0;
}

.v-ticker-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.v-ticker-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    width: fit-content;
}

.v-ticker-badge-book {
    background: linear-gradient(135deg, var(--v-p), var(--v-a));
    color: #fff;
}

.v-ticker-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--v-tx);
    line-height: 1.3;
}

.v-ticker-loans {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.75rem;
    color: var(--v-mut);
}

@media (max-width: 768px) {
    .v-ticker-label {
        display: none;
    }
    
    .v-ticker-track {
        padding-left: 20px;
    }
}
</style>