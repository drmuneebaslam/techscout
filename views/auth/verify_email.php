<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 4rem 1.5rem; max-width: 480px; min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center;">
  <div class="ts-card" style="padding: 2.25rem; width: 100%; background: rgba(13,21,39,0.75); border: 1px solid rgba(99,102,241,0.25); border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.6); text-align: center;">
    
    <div style="width: 56px; height: 56px; background: rgba(56,189,248,0.15); border: 1px solid rgba(56,189,248,0.3); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; color: #38bdf8; margin-bottom: 1rem;">
      <i data-lucide="mail-check" style="width: 28px; height: 28px;"></i>
    </div>

    <h1 style="font-size: 1.6rem; margin-bottom: 0.35rem; color: #f8fafc; font-family: 'Outfit', sans-serif;">Verify Your Email</h1>
    <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      We have sent an authentication verification code to your email address: <br>
      <strong style="color: #38bdf8; font-family: var(--ts-font-mono);"><?= htmlspecialchars($user['email'] ?? 'your registered email') ?></strong>
    </p>

    <!-- Code Verification Form -->
    <form action="<?= url('/verify-email') ?>" method="POST" style="margin-bottom: 1.5rem;">
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Enter 6-Digit Security Code</label>
        <input type="text" name="verification_code" placeholder="1 2 3 4 5 6" maxlength="8" style="width: 100%; background: #05070b; border: 1px solid rgba(56,189,248,0.4); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #38bdf8; font-size: 1.3rem; text-align: center; letter-spacing: 6px; font-family: var(--ts-font-mono); font-weight: 700;" required autofocus>
      </div>

      <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 700;">
        Verify &amp; Activate Account ✓
      </button>
    </form>

    <!-- Resend Action -->
    <form action="<?= url('/verify-email/resend') ?>" method="POST" style="margin-bottom: 1rem;">
      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8; font-size: 0.8rem;">
        Didn't receive email? Click to resend 📨
      </button>
    </form>

    <div style="font-size: 0.78rem; color: #64748b; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(148,163,184,0.1);">
      Logged in as @<?= htmlspecialchars($user['username'] ?? 'user') ?> &bull; <a href="<?= url('/auth/logout') ?>" style="color: #ef4444;">Sign out</a>
    </div>

  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
