<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 4rem 0; max-width: 440px;">
  <div class="ts-card" style="padding: 2.5rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
      <div class="logo-icon" style="margin: 0 auto 1rem auto; width: 44px; height: 44px;"><i data-lucide="shield"></i></div>
      <h1 style="font-size: 1.75rem; margin-bottom: 0.25rem;">Sign In</h1>
      <p style="font-size: 0.875rem; color: var(--ts-text-secondary);">Access your TechScout creator studio or admin control center</p>
    </div>

    <form action="<?= url('/auth/login') ?>" method="POST">
      <?= csrf_field() ?>
      <?php if (!empty($redirect)): ?>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <?php endif; ?>
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.4rem;">Email or Username</label>
        <input type="text" name="email" value="" placeholder="name@example.com or username" style="width: 100%; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: var(--ts-text-primary);" required autocomplete="username">
      </div>

      <div style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
          <label style="font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary);">Password</label>
          <a href="<?= url('/auth/forgot-password') ?>" style="font-size: 0.8rem; color: var(--ts-primary-light);">Forgot?</a>
        </div>
        <input type="password" name="password" value="" placeholder="••••••••" style="width: 100%; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: var(--ts-text-primary);" required autocomplete="current-password">
      </div>

      <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.8rem; font-size: 0.95rem;">
        Sign In to Platform 🚀
      </button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--ts-border-main); font-size: 0.875rem;">
      New builder? <a href="<?= url('/auth/register') ?>" style="color: var(--ts-primary-light); font-weight: 600;">Create an account</a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
