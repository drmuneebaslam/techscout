<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
.ts-otp-container {
  padding: 3.5rem 1rem;
  max-width: 520px;
  min-height: calc(100vh - 220px);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.ts-otp-card {
  width: 100%;
  padding: 2.5rem 2.25rem;
  background: var(--ts-bg-surface, #0d1527);
  border: 1px solid var(--ts-border-main, rgba(99,102,241,0.25));
  border-radius: 20px;
  box-shadow: 0 25px 60px rgba(0,0,0,0.45);
  text-align: center;
  position: relative;
  overflow: hidden;
}

.ts-otp-inputs {
  display: flex;
  gap: 0.65rem;
  justify-content: center;
  margin: 1.75rem 0 1.5rem 0;
}

.ts-otp-digit {
  width: 52px;
  height: 60px;
  font-size: 1.65rem;
  font-weight: 800;
  text-align: center;
  border-radius: 12px;
  border: 2px solid var(--ts-border-main, rgba(148,163,184,0.25));
  background: var(--ts-bg-main, #05070b);
  color: #38bdf8;
  font-family: 'JetBrains Mono', monospace;
  outline: none;
  transition: all 0.2s ease;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
}

.ts-otp-digit:focus {
  border-color: #38bdf8;
  box-shadow: 0 0 0 3px rgba(56,189,248,0.25);
  transform: translateY(-2px);
}

@media (max-width: 480px) {
  .ts-otp-card {
    padding: 1.75rem 1.25rem;
    border-radius: 16px;
  }
  .ts-otp-digit {
    width: 42px;
    height: 52px;
    font-size: 1.35rem;
    gap: 0.4rem;
  }
  .ts-otp-inputs {
    gap: 0.45rem;
  }
}
</style>

<div class="container ts-otp-container">
  <div class="ts-otp-card">
    
    <!-- Pulse Badge Icon -->
    <div style="width: 64px; height: 64px; background: rgba(56,189,248,0.12); border: 1px solid rgba(56,189,248,0.3); border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; color: #38bdf8; margin-bottom: 1.25rem; box-shadow: 0 8px 20px rgba(56,189,248,0.15);">
      <i data-lucide="shield-check" style="width: 32px; height: 32px;"></i>
    </div>

    <h1 style="font-size: 1.75rem; margin-bottom: 0.4rem; color: var(--ts-text-primary, #f8fafc); font-family: 'Outfit', sans-serif; font-weight: 800; letter-spacing: -0.5px;">
      Verify Your Email
    </h1>
    
    <p style="font-size: 0.92rem; color: var(--ts-text-secondary, #94a3b8); line-height: 1.55; margin-bottom: 1.5rem;">
      We've sent a 6-digit authentication security code to: <br>
      <span style="display: inline-block; margin-top: 0.35rem; background: rgba(56,189,248,0.1); border: 1px solid rgba(56,189,248,0.25); padding: 0.3rem 0.85rem; border-radius: 8px; color: #38bdf8; font-weight: 700; font-family: 'JetBrains Mono', monospace; font-size: 0.88rem;">
        <?= htmlspecialchars($user['email'] ?? 'your registered email') ?>
      </span>
    </p>

    <!-- 6-Digit OTP Interactive Verification Form -->
    <form action="<?= url('/verify-email') ?>" method="POST" id="otpVerificationForm" onsubmit="return handleOtpSubmit(event)">
      <input type="hidden" name="verification_code" id="hiddenVerificationCode" value="">

      <div class="ts-otp-inputs" id="otpInputsGroup">
        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="ts-otp-digit" data-index="0" autofocus required>
        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="ts-otp-digit" data-index="1" required>
        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="ts-otp-digit" data-index="2" required>
        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="ts-otp-digit" data-index="3" required>
        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="ts-otp-digit" data-index="4" required>
        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="ts-otp-digit" data-index="5" required>
      </div>

      <button type="submit" id="btnVerifySubmit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.85rem; font-weight: 800; font-size: 1rem; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #38bdf8); box-shadow: 0 4px 18px rgba(99,102,241,0.35);">
        Verify &amp; Activate Account ✓
      </button>
    </form>

    <!-- Resend Action with Cooldown Timer -->
    <div style="margin-top: 1.5rem; display: flex; justify-content: center; align-items: center;">
      <form action="<?= url('/verify-email/resend') ?>" method="POST" id="resendCodeForm">
        <button type="submit" id="btnResendCode" class="ts-btn ts-btn-outline ts-btn-sm" style="color: var(--ts-text-secondary, #94a3b8); border-color: var(--ts-border-main, rgba(148,163,184,0.25)); font-size: 0.82rem; border-radius: 8px;">
          Didn't receive email? <span id="resendText">Click to resend 📨</span>
        </button>
      </form>
    </div>

    <!-- Security Tip -->
    <div style="margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--ts-border-subtle, rgba(148,163,184,0.1)); font-size: 0.8rem; color: var(--ts-text-tertiary, #64748b); line-height: 1.5;">
      💡 Tip: Check your Spam, Junk, or Promotions folder if the email does not arrive within a minute. <br>
      Logged in as <strong>@<?= htmlspecialchars($user['username'] ?? 'member') ?></strong> &bull; 
      <a href="<?= url('/auth/logout') ?>" style="color: #ef4444; text-decoration: underline; margin-left: 0.35rem;">Sign out</a>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const digits = document.querySelectorAll('.ts-otp-digit');
  const hiddenCode = document.getElementById('hiddenVerificationCode');
  const form = document.getElementById('otpVerificationForm');

  digits.forEach((input, index) => {
    // Focus first input automatically
    if (index === 0) input.focus();

    // Input event (digit entered)
    input.addEventListener('input', (e) => {
      const val = e.target.value.replace(/\D/g, '');
      e.target.value = val ? val[0] : '';
      
      if (val && index < digits.length - 1) {
        digits[index + 1].focus();
      }
      checkAutoSubmit();
    });

    // Keydown event (backspace navigation)
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !e.target.value && index > 0) {
        digits[index - 1].focus();
      }
    });

    // Paste event: split 6 digits across inputs
    input.addEventListener('paste', (e) => {
      e.preventDefault();
      const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
      if (pasteData) {
        pasteData.split('').forEach((char, i) => {
          if (digits[i]) {
            digits[i].value = char;
          }
        });
        const nextIndex = Math.min(pasteData.length, digits.length - 1);
        digits[nextIndex].focus();
        checkAutoSubmit();
      }
    });
  });

  function checkAutoSubmit() {
    let fullCode = '';
    digits.forEach(d => fullCode += d.value.trim());
    if (fullCode.length === 6) {
      hiddenCode.value = fullCode;
    }
  }

  window.handleOtpSubmit = function(e) {
    let fullCode = '';
    digits.forEach(d => fullCode += d.value.trim());
    if (fullCode.length !== 6) {
      alert('Please enter all 6 digits of the verification code.');
      e.preventDefault();
      return false;
    }
    hiddenCode.value = fullCode;
    return true;
  };
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
