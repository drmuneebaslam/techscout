<?php
http_response_code(403);
require_once __DIR__ . '/bootstrap.php';
$pageTitle = '403 — Access Forbidden | TechScout';
require dirname(__DIR__) . '/views/layouts/header.php';
?>

<div class="container" style="padding: 5rem 1rem; text-align: center; max-width: 720px; margin: 0 auto; min-height: 65vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
  <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(244,63,94,0.15); border: 1px solid rgba(244,63,94,0.4); padding: 0.35rem 1rem; border-radius: 9999px; margin-bottom: 1.5rem;">
    <span style="width: 8px; height: 8px; border-radius: 50%; background: #f43f5e; display: inline-block; box-shadow: 0 0 10px #f43f5e;"></span>
    <span style="font-size: 0.8rem; font-weight: 700; color: #fb7185; font-family: var(--ts-font-mono, monospace); letter-spacing: 0.05em;">HTTP 403 &bull; ACCESS RESTRICTED</span>
  </div>

  <h1 style="font-size: 3.8rem; font-weight: 800; font-family: 'Outfit', sans-serif; line-height: 1.1; margin: 0 0 1rem 0; background: linear-gradient(135deg, #fb7185, #f43f5e, #e11d48); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
    403 — Forbidden Zone
  </h1>

  <p style="color: var(--ts-text-secondary, #94a3b8); font-size: 1.15rem; line-height: 1.6; margin: 0 0 2rem 0; max-width: 580px;">
    You do not possess the required clearance level, role authorization, or network credentials to inspect this resource.
  </p>

  <div style="background: rgba(15,23,42,0.65); border: 1px solid rgba(244,63,94,0.25); border-radius: 14px; padding: 1.5rem; margin-bottom: 2.5rem; text-align: left; max-width: 540px; width: 100%;">
    <div style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
      <i data-lucide="shield-alert" style="width: 16px; color: #fb7185;"></i> Access Control Notice
    </div>
    <p style="font-size: 0.82rem; color: #94a3b8; line-height: 1.5; margin: 0;">
      If you believe this restriction is an error, ensure you are logged into the correct account or verify your permissions in your author studio.
    </p>
  </div>

  <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
    <a href="<?= function_exists('url') ? url('/') : '/' ?>" class="ts-btn ts-btn-primary" style="font-weight: 700;">
      <i data-lucide="home"></i> Return to Homepage
    </a>
    <a href="<?= function_exists('url') ? url('/auth/login') : '/auth/login' ?>" class="ts-btn ts-btn-outline">
      <i data-lucide="log-in"></i> Sign In to Account
    </a>
    <a href="<?= function_exists('url') ? url('/contact') : '/contact' ?>" class="ts-btn ts-btn-outline">
      <i data-lucide="life-buoy"></i> Contact Support
    </a>
  </div>
</div>

<?php require dirname(__DIR__) . '/views/layouts/footer.php'; ?>
