<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 4rem 1.5rem; max-width: 460px; min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center;">
  <div class="ts-card" style="padding: 2.25rem; width: 100%; background: rgba(13,21,39,0.75); border: 1px solid rgba(99,102,241,0.25); border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.6);">
    <div style="text-align: center; margin-bottom: 1.75rem;">
      <div style="width: 48px; height: 48px; background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; color: #818cf8; margin-bottom: 0.75rem;">
        <i data-lucide="key-round" style="width: 24px; height: 24px;"></i>
      </div>
      <h1 style="font-size: 1.6rem; margin-bottom: 0.35rem; color: #f8fafc; font-family: 'Outfit', sans-serif;">Reset Password</h1>
      <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.4;">Enter your registered email address to receive a cryptographically signed recovery token.</p>
    </div>

    <?php if ($successMsg = \App\Core\Session::flash('success')): ?>
      <div style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.35); border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 0.75rem;">
        <i data-lucide="mail-check" style="width: 20px; height: 20px; color: #10b981; flex-shrink: 0; margin-top: 2px;"></i>
        <div>
          <div style="color: #34d399; font-weight: 700; font-size: 0.88rem; margin-bottom: 0.25rem;">Recovery Email Dispatched</div>
          <div style="font-size: 0.8rem; color: #cbd5e1; line-height: 1.45;">
            <?= htmlspecialchars($successMsg) ?> Please check your inbox and spam folder within the next 2 minutes.
          </div>
        </div>
      </div>
    <?php endif; ?>

    <form action="<?= url('/forgot-password') ?>" method="POST">
      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">Account Email Address</label>
        <input type="email" name="email" placeholder="builder@techscout.online" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc; font-size: 0.9rem;" required autofocus>
      </div>

      <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 700;">
        Generate Recovery Token ✉️
      </button>
    </form>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid rgba(148,163,184,0.1); font-size: 0.82rem;">
      <a href="<?= url('/auth/login') ?>" style="color: var(--ts-primary-light); text-decoration: none;">&larr; Back to Sign In</a>
      <a href="<?= url('/support') ?>" style="color: #64748b; text-decoration: none;">Contact Support</a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
