<?php
http_response_code(429);
require_once __DIR__ . '/bootstrap.php';
$pageTitle = '429 — Too Many Requests | TechScout';
require dirname(__DIR__) . '/views/layouts/header.php';
?>

<div class="container" style="padding: 5rem 1rem; text-align: center; max-width: 720px; margin: 0 auto; min-height: 65vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
  <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.4); padding: 0.35rem 1rem; border-radius: 9999px; margin-bottom: 1.5rem;">
    <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; display: inline-block; box-shadow: 0 0 10px #f59e0b;"></span>
    <span style="font-size: 0.8rem; font-weight: 700; color: #fbbf24; font-family: var(--ts-font-mono, monospace); letter-spacing: 0.05em;">HTTP 429 &bull; RATE LIMIT REACHED</span>
  </div>

  <h1 style="font-size: 3.8rem; font-weight: 800; font-family: 'Outfit', sans-serif; line-height: 1.1; margin: 0 0 1rem 0; background: linear-gradient(135deg, #fef08a, #f59e0b, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
    429 — Rate Limit Enforced
  </h1>

  <p style="color: var(--ts-text-secondary, #94a3b8); font-size: 1.15rem; line-height: 1.6; margin: 0 0 2rem 0; max-width: 580px;">
    You have dispatched too many requests in a short timeframe. To protect server health and community availability, access is temporarily throttled.
  </p>

  <div style="background: rgba(15,23,42,0.65); border: 1px dashed rgba(245,158,11,0.3); border-radius: 14px; padding: 1.5rem; margin-bottom: 2.5rem; text-align: center; max-width: 540px; width: 100%;">
    <div style="font-size: 0.95rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.35rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
      ⏳ Automated Cooldown Activated
    </div>
    <p style="font-size: 0.82rem; color: #94a3b8; line-height: 1.5; margin: 0;">
      Please wait 30–60 seconds before initiating subsequent interactions or refreshing this resource.
    </p>
  </div>

  <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
    <button onclick="window.location.reload()" class="ts-btn ts-btn-primary" style="background: #f59e0b; color: #020617; font-weight: 700;">
      <i data-lucide="refresh-cw"></i> Retry Now
    </button>
    <a href="<?= function_exists('url') ? url('/') : '/' ?>" class="ts-btn ts-btn-outline">
      <i data-lucide="home"></i> Return to Homepage
    </a>
  </div>
</div>

<?php require dirname(__DIR__) . '/views/layouts/footer.php'; ?>
