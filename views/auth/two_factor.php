<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 4rem 1.5rem; max-width: 520px; min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center;">
  <div class="ts-card" style="padding: 2.25rem; width: 100%; background: rgba(13,21,39,0.75); border: 1px solid rgba(99,102,241,0.25); border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.6);">
    
    <div style="text-align: center; margin-bottom: 1.75rem;">
      <div style="width: 56px; height: 56px; background: rgba(168,85,247,0.15); border: 1px solid rgba(168,85,247,0.3); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; color: #c084fc; margin-bottom: 0.75rem;">
        <i data-lucide="smartphone" style="width: 28px; height: 28px;"></i>
      </div>
      <h1 style="font-size: 1.6rem; margin-bottom: 0.35rem; color: #f8fafc; font-family: 'Outfit', sans-serif;">Two-Factor Authentication (2FA)</h1>
      <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.4;">Protect your TechScout builder profile and revenue balance with TOTP authenticator tokens.</p>
    </div>

    <!-- Mode: Setting up 2FA or Verifying 2FA -->
    <?php if (!empty($isEnrolling)): ?>
      <!-- Step 1: Scan QR or copy Secret -->
      <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 12px; padding: 1.25rem; text-align: center; margin-bottom: 1.5rem;">
        <div style="font-size: 0.82rem; color: #94a3b8; margin-bottom: 0.75rem;">Scan with Google Authenticator, 1Password, or Authy:</div>
        
        <!-- Authenticator QR Placeholder Canvas / SVG Box -->
        <div style="width: 140px; height: 140px; background: #ffffff; padding: 8px; border-radius: 8px; margin: 0 auto 0.75rem auto; display: flex; align-items: center; justify-content: center;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=<?= urlencode('otpauth://totp/TechScout:' . ($user['email'] ?? 'user') . '?secret=' . ($totpSecret ?? 'JBSWY3DPEHPK3PXP') . '&issuer=TechScout') ?>" alt="2FA QR Code" style="width: 100%; height: 100%;">
        </div>

        <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">Manual Entry Secret Key:</div>
        <code style="font-family: var(--ts-font-mono); font-size: 0.95rem; color: #c084fc; font-weight: 700; letter-spacing: 2px;">
          <?= htmlspecialchars($totpSecret ?? 'JBSWY3DPEHPK3PXP') ?>
        </code>
      </div>
    <?php endif; ?>

    <!-- OTP Code Submission -->
    <form action="<?= url('/two-factor-authentication') ?>" method="POST">
      <input type="hidden" name="action" value="<?= !empty($isEnrolling) ? 'enable' : 'verify' ?>">
      <input type="hidden" name="secret" value="<?= htmlspecialchars($totpSecret ?? '') ?>">

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem; text-align: center;">Enter 6-Digit Authenticator Code</label>
        <input type="text" name="totp_code" placeholder="0 0 0 0 0 0" maxlength="6" style="width: 100%; background: #05070b; border: 1px solid rgba(168,85,247,0.4); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #c084fc; font-size: 1.4rem; text-align: center; letter-spacing: 8px; font-family: var(--ts-font-mono); font-weight: 700;" required autofocus>
      </div>

      <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 700; background: linear-gradient(135deg, #a855f7, #6366f1);">
        <?= !empty($isEnrolling) ? 'Activate 2FA Security ✓' : 'Verify &amp; Proceed &rarr;' ?>
      </button>
    </form>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(148,163,184,0.1); font-size: 0.82rem;">
      <a href="<?= url('/profile/security') ?>" style="color: #64748b; text-decoration: none;">Security Settings</a>
      <a href="<?= url('/support') ?>" style="color: #38bdf8; text-decoration: none;">Lost 2FA Access?</a>
    </div>

  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
