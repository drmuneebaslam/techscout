<?php
http_response_code(404);
require_once __DIR__ . '/bootstrap.php';
$pageTitle = '404 — Entity Not Scouted | TechScout';
require dirname(__DIR__) . '/views/layouts/header.php';
?>

<div class="container" style="padding: 5rem 1rem; text-align: center; max-width: 720px; margin: 0 auto; min-height: 65vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
  <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(236,72,153,0.15); border: 1px solid rgba(236,72,153,0.4); padding: 0.35rem 1rem; border-radius: 9999px; margin-bottom: 1.5rem;">
    <span style="width: 8px; height: 8px; border-radius: 50%; background: #ec4899; display: inline-block; box-shadow: 0 0 10px #ec4899;"></span>
    <span style="font-size: 0.8rem; font-weight: 700; color: #f472b6; font-family: var(--ts-font-mono, monospace); letter-spacing: 0.05em;">HTTP 404 &bull; RADAR SIGNAL LOST</span>
  </div>

  <h1 style="font-size: 3.8rem; font-weight: 800; font-family: 'Outfit', sans-serif; line-height: 1.1; margin: 0 0 1rem 0; background: linear-gradient(135deg, #ec4899, #6366f1, #38bdf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
    404 — Entity Not Scouted
  </h1>

  <p style="color: var(--ts-text-secondary, #94a3b8); font-size: 1.15rem; line-height: 1.6; margin: 0 0 2rem 0; max-width: 600px;">
    The technical resource, article, AI prompt, or directory entity you are looking for has been relocated, archived, or does not exist.
  </p>

  <!-- Instant Search Box -->
  <div style="background: rgba(15,23,42,0.65); border: 1px solid rgba(99,102,241,0.25); border-radius: 14px; padding: 1.75rem; margin-bottom: 2.5rem; text-align: left; max-width: 580px; width: 100%;">
    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem;">
      <i data-lucide="search" style="width: 14px; display: inline; color: #38bdf8;"></i> Search Across TechScout Intelligence Index
    </label>
    <form action="<?= function_exists('url') ? url('/blog') : '/blog' ?>" method="GET" style="display: flex; gap: 0.5rem;">
      <input type="text" name="q" placeholder="Search articles, AI models, tutorials, benchmarks..." style="flex: 1; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.7rem 1rem; border-radius: 8px; color: #f8fafc; font-size: 0.9rem;">
      <button type="submit" class="ts-btn ts-btn-primary" style="white-space: nowrap; font-weight: 700;">
        Scout ⚡
      </button>
    </form>
  </div>

  <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
    <a href="<?= function_exists('url') ? url('/') : '/' ?>" class="ts-btn ts-btn-primary"><i data-lucide="home"></i> Return to Homepage</a>
    <a href="<?= function_exists('url') ? url('/blog') : '/blog' ?>" class="ts-btn ts-btn-outline"><i data-lucide="layers"></i> Browse Articles</a>
    <a href="<?= function_exists('url') ? url('/tools') : '/tools' ?>" class="ts-btn ts-btn-outline"><i data-lucide="cpu"></i> AI Tools</a>
  </div>
</div>

<?php require dirname(__DIR__) . '/views/layouts/footer.php'; ?>
