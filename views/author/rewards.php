<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="margin-bottom: 2rem;">
    <span class="ts-badge ts-badge-green" style="margin-bottom: 0.5rem;">Zero-Cash-Burn Rewards Wallet</span>
    <h1 style="font-size: 2.25rem;">Creator Rewards & Promo Credits</h1>
    <p style="color: var(--ts-text-secondary); font-size: 1rem;">
      Redeem earned credits for self-serve ad promotions, featured homepage spots, and verified builder perks.
    </p>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; margin-bottom: 3rem;">
    <!-- Balance Card -->
    <div class="ts-card" style="border-color: rgba(16, 185, 129, 0.4); text-align: center; padding: 2rem;">
      <div style="font-size: 0.85rem; text-transform: uppercase; color: var(--ts-text-tertiary);">Available Balance</div>
      <div style="font-size: 2.75rem; font-weight: 800; color: #34d399; margin: 0.5rem 0;"><?= number_format((float)($author['promo_credits'] ?? 0), 2) ?></div>
      <div style="font-size: 0.85rem; color: var(--ts-text-tertiary); margin-bottom: 1.5rem;">Promo Credits (CR)</div>

      <button onclick="showToast('Launched self-serve promo campaign with 100 CR!', 'success')" class="ts-btn ts-btn-accent" style="width: 100%;">
        <i data-lucide="zap"></i> Launch Promo Card (100 CR)
      </button>
    </div>

    <!-- Reward Repertoire -->
    <div class="ts-card">
      <h3 style="font-size: 1.2rem; margin-bottom: 1.25rem;">Reward Opportunities</h3>
      <div style="display: grid; gap: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--ts-bg-surface-elevated); border-radius: 8px;">
          <div>
            <div style="font-weight: 600; color: var(--ts-text-primary); font-size: 0.95rem;">Homepage Hero Spotlight (48 Hours)</div>
            <div style="font-size: 0.8rem; color: var(--ts-text-tertiary);">Guaranteed rotation in top homepage carousel</div>
          </div>
          <span class="ts-badge ts-badge-pink">250 CR</span>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--ts-bg-surface-elevated); border-radius: 8px;">
          <div>
            <div style="font-weight: 600; color: var(--ts-text-primary); font-size: 0.95rem;">Weekly Digest Email Feature</div>
            <div style="font-size: 0.8rem; color: var(--ts-text-tertiary);">Dedicated slot in Tuesday email digest to 14k+ subscribers</div>
          </div>
          <span class="ts-badge ts-badge-cyan">150 CR</span>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--ts-bg-surface-elevated); border-radius: 8px;">
          <div>
            <div style="font-weight: 600; color: var(--ts-text-primary); font-size: 0.95rem;">Verified Builder Checkmark Badge</div>
            <div style="font-size: 0.8rem; color: var(--ts-text-tertiary);">Permanent visual authority badge next to your handle</div>
          </div>
          <span class="ts-badge ts-badge-green">Unlocked ✓</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Credit Ledger History -->
  <h3 style="font-size: 1.25rem; margin-bottom: 1rem;">Credits History & Rewards Ledger</h3>
  <div class="ts-card">
    <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
      <thead>
        <tr style="border-bottom: 1px solid var(--ts-border-main); text-align: left;">
          <th style="padding: 0.75rem 1rem;">Date</th>
          <th style="padding: 0.75rem 1rem;">Activity / Description</th>
          <th style="padding: 0.75rem 1rem;">Credits</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($transactions)): ?>
          <?php foreach ($transactions as $tx): ?>
            <tr style="border-bottom: 1px solid rgba(148,163,184,0.06);">
              <td style="padding: 0.85rem 1rem; color: var(--ts-text-tertiary);"><?= date('M j, Y', strtotime($tx['created_at'])) ?></td>
              <td style="padding: 0.85rem 1rem; color: var(--ts-text-primary);"><?= htmlspecialchars($tx['description']) ?></td>
              <td style="padding: 0.85rem 1rem; font-weight: 700; color: #34d399;">+<?= number_format((float)$tx['credits_granted'], 2) ?> CR</td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" style="padding: 1.5rem; text-align: center; color: var(--ts-text-tertiary);">Earn credits automatically by publishing verified articles and reaching reader milestones!</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
