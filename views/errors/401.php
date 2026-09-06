<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 5rem 1rem; text-align: center; max-width: 680px; margin: 0 auto;">
  <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.4); padding: 0.35rem 1rem; border-radius: 9999px; margin-bottom: 1.5rem;">
    <span style="width: 8px; height: 8px; border-radius: 50%; background: #6366f1; display: inline-block;"></span>
    <span style="font-size: 0.8rem; font-weight: 700; color: #818cf8; font-family: var(--ts-font-mono); letter-spacing: 0.05em;">HTTP 401 &bull; AUTHENTICATION REQUIRED</span>
  </div>

  <h1 style="font-size: 3.5rem; font-weight: 800; font-family: 'Outfit', sans-serif; line-height: 1.1; margin: 0 0 1rem 0; background: linear-gradient(135deg, #6366f1, #38bdf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
    401 — Unauthorized Access
  </h1>

  <p style="color: var(--ts-text-secondary); font-size: 1.1rem; line-height: 1.6; margin: 0 0 2rem 0;">
    <?= !empty($customMessage) ? htmlspecialchars($customMessage) : 'Access to this secure endpoint requires active user credentials. Please sign in to verify your identity.' ?>
  </p>

  <div style="background: rgba(15,23,42,0.65); border: 1px solid rgba(99,102,241,0.2); border-radius: 12px; padding: 1.5rem; margin-bottom: 2.5rem; text-align: left;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <i data-lucide="lock" style="width: 16px; height: 16px; color: #38bdf8;"></i>
      <strong style="color: #f8fafc; font-size: 0.9rem;">Identity Verification Gateway</strong>
    </div>
    <p style="color: var(--ts-text-secondary); font-size: 0.85rem; line-height: 1.55; margin: 0;">
      Certain platform hubs (Author Studios, Challenge Submissions, Ticket Helpdesks, and Bookmarks) are reserved for authenticated members of the TechScout community.
    </p>
  </div>

  <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
    <a href="<?= url('/auth/login') ?>" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #6366f1, #38bdf8);"><i data-lucide="log-in"></i> Sign In to Account</a>
    <a href="<?= url('/auth/register') ?>" class="ts-btn ts-btn-outline"><i data-lucide="user-plus"></i> Create Free Account</a>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
