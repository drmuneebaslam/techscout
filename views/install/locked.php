<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 680px; margin: 5rem auto; padding: 0 1.5rem;">
  <div class="ts-card" style="padding: 2.5rem; text-align: center; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); border-radius: 16px; box-shadow: var(--ts-shadow-xl);">
    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(56,189,248,0.12); color: var(--ts-primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
      <i data-lucide="shield-check" style="width: 32px; height: 32px;"></i>
    </div>

    <span class="ts-badge ts-badge-cyan" style="margin-bottom: 0.75rem; font-weight: 700;">SYSTEM PROTECTED</span>
    
    <h1 style="font-size: 1.85rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--ts-text-primary);">
      Platform is Already Installed
    </h1>

    <p style="color: var(--ts-text-secondary); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.75rem;">
      For security reasons, the web installer is automatically locked after the initial deployment. Re-running the installation wizard requires explicit authorization from an authenticated Super Administrator.
    </p>

    <!-- Installation Metadata Box -->
    <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 1.25rem; margin-bottom: 2rem; text-align: left; font-size: 0.85rem;">
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
        <span style="color: var(--ts-text-tertiary);">Platform Title:</span>
        <strong style="color: var(--ts-text-primary);"><?= htmlspecialchars($lockData['platform'] ?? 'TechScout') ?></strong>
      </div>
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
        <span style="color: var(--ts-text-tertiary);">Deployment Version:</span>
        <code style="color: var(--ts-primary);"><?= htmlspecialchars($lockData['version'] ?? '2.5.0') ?></code>
      </div>
      <div style="display: flex; justify-content: space-between;">
        <span style="color: var(--ts-text-tertiary);">Locked Timestamp:</span>
        <span style="color: var(--ts-text-secondary); font-family: var(--ts-font-mono);"><?= htmlspecialchars($lockData['installed_at'] ?? '2026-09-12') ?></span>
      </div>
    </div>

    <!-- Admin Instruction Callout -->
    <div style="background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.25); border-radius: 10px; padding: 1rem; margin-bottom: 2rem; font-size: 0.84rem; color: var(--ts-text-secondary); text-align: left;">
      <strong style="color: var(--ts-success); display: block; margin-bottom: 0.25rem;">🔑 Administrator Re-installation:</strong>
      To change database credentials or update white-label branding, log in to the Admin Panel and navigate to <strong>Admin &gt; Settings &gt; White-Label &amp; Setup Wizard</strong>, then click <em>"Launch Setup Wizard / Re-run Installer"</em>.
    </div>

    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
      <a href="<?= url('/') ?>" class="ts-btn ts-btn-outline" style="padding: 0.65rem 1.5rem;">
        <i data-lucide="home"></i> Return to Homepage
      </a>
      <a href="<?= url('/auth/login') ?>" class="ts-btn ts-btn-primary" style="padding: 0.65rem 1.75rem;">
        <i data-lucide="log-in"></i> Administrator Login
      </a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
