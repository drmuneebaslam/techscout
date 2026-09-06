<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 4rem 1.5rem; max-width: 460px; min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center;">
  <div class="ts-card" style="padding: 2.25rem; width: 100%; background: rgba(13,21,39,0.75); border: 1px solid rgba(99,102,241,0.25); border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.6);">
    <div style="text-align: center; margin-bottom: 1.75rem;">
      <div style="width: 48px; height: 48px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; color: #34d399; margin-bottom: 0.75rem;">
        <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
      </div>
      <h1 style="font-size: 1.6rem; margin-bottom: 0.35rem; color: #f8fafc; font-family: 'Outfit', sans-serif;">Set New Password</h1>
      <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.4;">Enter a new strong password for your TechScout account.</p>
    </div>

    <form action="<?= url('/reset-password') ?>" method="POST">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">New Password</label>
        <input type="password" name="password" id="new_password_input" placeholder="Minimum 6 characters" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc; font-size: 0.9rem;" required minlength="6">
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">Confirm New Password</label>
        <input type="password" name="password_confirmation" placeholder="Re-type new password" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc; font-size: 0.9rem;" required minlength="6">
      </div>

      <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 700; background: linear-gradient(135deg, #10b981, #06b6d4);">
        Update &amp; Secure Account 🔒
      </button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem; font-size: 0.82rem;">
      <a href="<?= url('/auth/login') ?>" style="color: var(--ts-primary-light); text-decoration: none;">&larr; Return to Sign In</a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
