<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 5rem 1rem; text-align: center; max-width: 680px; margin: 0 auto;">
  <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.4); padding: 0.35rem 1rem; border-radius: 9999px; margin-bottom: 1.5rem;">
    <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
    <span style="font-size: 0.8rem; font-weight: 700; color: #f59e0b; font-family: var(--ts-font-mono); letter-spacing: 0.05em;">HTTP 400 &bull; CLIENT PROTOCOL ERROR</span>
  </div>

  <h1 style="font-size: 3.5rem; font-weight: 800; font-family: 'Outfit', sans-serif; line-height: 1.1; margin: 0 0 1rem 0; background: linear-gradient(135deg, #f59e0b, #fbbf24); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
    400 — Bad Request
  </h1>

  <p style="color: var(--ts-text-secondary); font-size: 1.1rem; line-height: 1.6; margin: 0 0 2rem 0;">
    <?= !empty($customMessage) ? htmlspecialchars($customMessage) : 'The server could not understand or parse the incoming request due to malformed syntax, invalid characters, or an incomplete payload.' ?>
  </p>

  <div style="background: rgba(15,23,42,0.65); border: 1px solid rgba(148,163,184,0.15); border-radius: 12px; padding: 1.5rem; margin-bottom: 2.5rem; text-align: left;">
    <div style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem;">Suggested Diagnostics:</div>
    <ul style="color: var(--ts-text-secondary); font-size: 0.85rem; line-height: 1.6; margin: 0; padding-left: 1.25rem;">
      <li>Verify the URL parameters and ensure no special characters are unencoded.</li>
      <li>Clear your browser cookies and cache for this session.</li>
      <li>If submitting a form, ensure all required fields contain valid data types.</li>
    </ul>
  </div>

  <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
    <a href="<?= url('/') ?>" class="ts-btn ts-btn-primary"><i data-lucide="home"></i> Return Home</a>
    <a href="javascript:history.back()" class="ts-btn ts-btn-outline"><i data-lucide="arrow-left"></i> Go Back</a>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
