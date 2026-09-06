<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 5rem 1rem; text-align: center; max-width: 680px; margin: 0 auto;">
  <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.4); padding: 0.35rem 1rem; border-radius: 9999px; margin-bottom: 1.5rem;">
    <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
    <span style="font-size: 0.8rem; font-weight: 700; color: #f87171; font-family: var(--ts-font-mono); letter-spacing: 0.05em;">HTTP 500 &bull; SERVER EXCEPTION DETECTED</span>
  </div>

  <h1 style="font-size: 3.5rem; font-weight: 800; font-family: 'Outfit', sans-serif; line-height: 1.1; margin: 0 0 1rem 0; background: linear-gradient(135deg, #ef4444, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
    500 — Server Incident
  </h1>

  <p style="color: var(--ts-text-secondary); font-size: 1.1rem; line-height: 1.6; margin: 0 0 2rem 0;">
    <?= !empty($customMessage) ? htmlspecialchars($customMessage) : 'An unexpected exception occurred while processing this request. Our internal telemetry system has logged the diagnostic incident.' ?>
  </p>

  <div style="background: rgba(15,23,42,0.65); border: 1px solid rgba(239,68,68,0.25); border-radius: 12px; padding: 1.5rem; margin-bottom: 2.5rem; text-align: left;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <i data-lucide="cpu" style="width: 16px; height: 16px; color: #f87171;"></i>
      <strong style="color: #f8fafc; font-size: 0.9rem;">Automated Recovery Stream</strong>
    </div>
    <p style="color: var(--ts-text-secondary); font-size: 0.85rem; line-height: 1.55; margin: 0;">
      Engineering leads have been notified. If this issue persists across multiple requests, please reach out to our support station with your current URL and timestamp.
    </p>
  </div>

  <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
    <a href="<?= url('/') ?>" class="ts-btn ts-btn-primary"><i data-lucide="home"></i> Return to Homepage</a>
    <a href="<?= url('/support') ?>" class="ts-btn ts-btn-outline"><i data-lucide="life-buoy"></i> Open Support Ticket</a>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
