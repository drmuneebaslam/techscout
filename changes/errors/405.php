<?php
http_response_code(405);
require_once __DIR__ . '/bootstrap.php';
$pageTitle = '405 — Method Not Allowed | TechScout';
require dirname(__DIR__) . '/views/layouts/header.php';
?>

<div class="container" style="padding: 5rem 1rem; text-align: center; max-width: 720px; margin: 0 auto; min-height: 65vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
  <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(168,85,247,0.15); border: 1px solid rgba(168,85,247,0.4); padding: 0.35rem 1rem; border-radius: 9999px; margin-bottom: 1.5rem;">
    <span style="width: 8px; height: 8px; border-radius: 50%; background: #a855f7; display: inline-block; box-shadow: 0 0 10px #a855f7;"></span>
    <span style="font-size: 0.8rem; font-weight: 700; color: #c084fc; font-family: var(--ts-font-mono, monospace); letter-spacing: 0.05em;">HTTP 405 &bull; METHOD NOT PERMITTED</span>
  </div>

  <h1 style="font-size: 3.8rem; font-weight: 800; font-family: 'Outfit', sans-serif; line-height: 1.1; margin: 0 0 1rem 0; background: linear-gradient(135deg, #c084fc, #a855f7, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
    405 — Protocol Misaligned
  </h1>

  <p style="color: var(--ts-text-secondary, #94a3b8); font-size: 1.15rem; line-height: 1.6; margin: 0 0 2rem 0; max-width: 580px;">
    The HTTP request verb (<?= htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN') ?>) is not permitted on this API or route endpoint.
  </p>

  <div style="background: rgba(15,23,42,0.65); border: 1px solid rgba(168,85,247,0.25); border-radius: 14px; padding: 1.5rem; margin-bottom: 2.5rem; text-align: left; max-width: 540px; width: 100%;">
    <div style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
      <i data-lucide="terminal" style="width: 16px; color: #c084fc;"></i> API Contract Specification
    </div>
    <p style="font-size: 0.82rem; color: #94a3b8; line-height: 1.5; margin: 0;">
      Forms and telemetry clients must dispatch requests using the appropriate HTTP method (GET, POST, etc.) corresponding to the target controller route.
    </p>
  </div>

  <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
    <a href="<?= function_exists('url') ? url('/') : '/' ?>" class="ts-btn ts-btn-primary" style="font-weight: 700;">
      <i data-lucide="home"></i> Return to Homepage
    </a>
    <a href="<?= function_exists('url') ? url('/blog') : '/blog' ?>" class="ts-btn ts-btn-outline">
      <i data-lucide="compass"></i> Explore Intelligence
    </a>
  </div>
</div>

<?php require dirname(__DIR__) . '/views/layouts/footer.php'; ?>
