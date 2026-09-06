<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 5rem 1rem; text-align: center; max-width: 680px; margin: 0 auto;">
  <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.4); padding: 0.35rem 1rem; border-radius: 9999px; margin-bottom: 1.5rem;">
    <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
    <span style="font-size: 0.8rem; font-weight: 700; color: #f59e0b; font-family: var(--ts-font-mono); letter-spacing: 0.05em;">HTTP 419 &bull; SESSION TOKEN EXPIRED</span>
  </div>

  <h1 style="font-size: 3.5rem; font-weight: 800; font-family: 'Outfit', sans-serif; line-height: 1.1; margin: 0 0 1rem 0; background: linear-gradient(135deg, #f59e0b, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
    419 — Page Expired
  </h1>

  <p style="color: var(--ts-text-secondary); font-size: 1.1rem; line-height: 1.6; margin: 0 0 2rem 0;">
    <?= !empty($customMessage) ? htmlspecialchars($customMessage) : 'Your security token or form session has expired due to inactivity. This automated defense protects your account against Cross-Site Request Forgery (CSRF).' ?>
  </p>

  <div style="background: rgba(15,23,42,0.65); border: 1px solid rgba(245,158,11,0.2); border-radius: 12px; padding: 1.5rem; margin-bottom: 2.5rem; text-align: left;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <i data-lucide="refresh-cw" style="width: 16px; height: 16px; color: #f59e0b;"></i>
      <strong style="color: #f8fafc; font-size: 0.9rem;">Quick Recovery Action</strong>
    </div>
    <p style="color: var(--ts-text-secondary); font-size: 0.85rem; line-height: 1.55; margin: 0;">
      Simply refresh the previous page to regenerate a fresh cryptographic security token, then re-submit your form.
    </p>
  </div>

  <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
    <button type="button" onclick="location.reload()" class="ts-btn ts-btn-primary" style="background: #f59e0b; border-color: #f59e0b; color: #000; font-weight: 700;">
      <i data-lucide="rotate-cw"></i> Refresh Page
    </button>
    <a href="javascript:history.back()" class="ts-btn ts-btn-outline"><i data-lucide="arrow-left"></i> Return to Form</a>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
