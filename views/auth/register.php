<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 3.5rem 1rem 5rem 1rem; max-width: 520px;">
  <div class="ts-card" style="padding: 2.75rem 2.25rem; background: rgba(13, 21, 39, 0.75); backdrop-filter: blur(16px); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 16px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65);">
    
    <!-- Top Branding -->
    <div style="text-align: center; margin-bottom: 2rem;">
      <div class="logo-icon" style="margin: 0 auto 1rem auto; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(56,189,248,0.2)); border: 1px solid rgba(56,189,248,0.3); border-radius: 12px;">
        <i data-lucide="user-plus" style="width: 24px; height: 24px; color: #38bdf8;"></i>
      </div>
      <h1 style="font-size: 1.85rem; font-weight: 800; font-family: 'Outfit', sans-serif; color: #f8fafc; margin: 0 0 0.4rem 0;">
        Join TechScout
      </h1>
      <p style="font-size: 0.9rem; color: var(--ts-text-secondary); margin: 0;">
        Developer intelligence, AI tools radar, prompt templates &amp; research
      </p>
    </div>

    <!-- Registration Notice Pill -->
    <div style="display: flex; align-items: flex-start; gap: 0.65rem; background: rgba(56,189,248,0.06); border: 1px solid rgba(56,189,248,0.2); border-radius: 8px; padding: 0.75rem 0.9rem; margin-bottom: 1.75rem;">
      <i data-lucide="shield-check" style="width: 18px; height: 18px; color: #38bdf8; flex-shrink: 0; margin-top: 2px;"></i>
      <div style="font-size: 0.8rem; color: #cbd5e1; line-height: 1.45;">
        <strong style="color: #38bdf8;">Member Account:</strong> Full access to discussions, saved bookmarks, and custom feeds. You can apply to become a Verified Author anytime from your profile.
      </div>
    </div>

    <form action="<?= url('/auth/register') ?>" method="POST" id="regForm">
      
      <!-- Full Name / Display Name -->
      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <label style="font-size: 0.84rem; font-weight: 600; color: #f8fafc;">
            Full Name / Display Name <span style="color: #ef4444;">*</span>
          </label>
          <span style="font-size: 0.72rem; color: #64748b;">2–60 characters</span>
        </div>
        <input type="text" name="display_name" id="regDisplayName" placeholder="e.g. Alex Vance" class="ts-input" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: 8px; color: #f8fafc; font-size: 0.92rem;" minlength="2" maxlength="60" required autofocus>
      </div>

      <!-- Username Handle -->
      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <label style="font-size: 0.84rem; font-weight: 600; color: #f8fafc;">
            Username / Developer Handle <span style="color: #ef4444;">*</span>
          </label>
          <span style="font-size: 0.72rem; color: #64748b;">3–30 chars (letters, numbers, _)</span>
        </div>
        <div style="position: relative;">
          <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b; font-weight: 700;">@</span>
          <input type="text" name="username" id="regUsername" placeholder="alexvance" class="ts-input" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem 0.75rem 2.25rem; border-radius: 8px; color: #f8fafc; font-size: 0.92rem; font-family: monospace;" required minlength="3" maxlength="30" pattern="[a-zA-Z0-9_]{3,30}" title="3-30 characters, letters, numbers and underscores only" autocomplete="off">
        </div>
        <div id="usernameStatusMsg" style="margin-top: 0.35rem; font-size: 0.78rem; min-height: 18px;"></div>
      </div>

      <!-- Work Email Address -->
      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <label style="font-size: 0.84rem; font-weight: 600; color: #f8fafc;">
            Work / Personal Email <span style="color: #ef4444;">*</span>
          </label>
          <span style="font-size: 0.72rem; color: #64748b;">Instant verification</span>
        </div>
        <input type="email" name="email" id="regEmail" placeholder="alex@company.com" class="ts-input" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: 8px; color: #f8fafc; font-size: 0.92rem;" required autocomplete="off">
        <div id="emailStatusMsg" style="margin-top: 0.35rem; font-size: 0.78rem; min-height: 18px;"></div>
      </div>

      <!-- Technical Specialization / Focus -->
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.84rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
          Primary Technical Focus
        </label>
        <select name="developer_role" class="ts-select" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: 8px; color: #f8fafc; font-size: 0.9rem;">
          <option value="Full-Stack Developer">⚡ Full-Stack Developer</option>
          <option value="AI / ML Engineer & Researcher">🤖 AI / ML Engineer &amp; Researcher</option>
          <option value="Backend & Distributed Systems Architect">⚙️ Backend &amp; Systems Architect</option>
          <option value="Frontend & UI/UX Engineer">🎨 Frontend &amp; UI/UX Engineer</option>
          <option value="Cloud, DevOps & SRE Specialist">☁️ Cloud, DevOps &amp; SRE</option>
          <option value="Cybersecurity & AppSec Researcher">🛡️ Cybersecurity &amp; AppSec</option>
          <option value="Technical Writer & Developer Advocate">✍️ Technical Writer / DevRel</option>
          <option value="Student & Technology Enthusiast">🎓 Student / Tech Enthusiast</option>
        </select>
      </div>

      <!-- Password -->
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.84rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
          Password <span style="color: #ef4444;">*</span>
        </label>
        <div style="position: relative;">
          <input type="password" id="regPassword" name="password" placeholder="Minimum 6 characters" class="ts-input" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 2.75rem 0.75rem 1rem; border-radius: 8px; color: #f8fafc; font-size: 0.92rem;" required minlength="6" oninput="checkPassStrength(this.value)">
          <button type="button" onclick="togglePassVisibility()" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer; padding: 0.25rem;" title="Show/Hide password">
            <i data-lucide="eye" id="passToggleIcon" style="width: 18px; height: 18px;"></i>
          </button>
        </div>
        <div id="passStrengthBar" style="height: 3px; background: rgba(148,163,184,0.15); border-radius: 999px; margin-top: 0.45rem; overflow: hidden;">
          <div id="passStrengthFill" style="width: 0%; height: 100%; transition: all 0.2s ease;"></div>
        </div>
      </div>

      <!-- Terms & Privacy Agreement -->
      <div style="margin-bottom: 1.75rem; display: flex; align-items: flex-start; gap: 0.65rem;">
        <input type="checkbox" name="terms_agreed" id="terms_agreed" value="1" required style="accent-color: #6366f1; width: 17px; height: 17px; margin-top: 2px; cursor: pointer;">
        <label for="terms_agreed" style="font-size: 0.82rem; color: #94a3b8; line-height: 1.45; cursor: pointer;">
          I agree to the <a href="<?= url('/terms') ?>" target="_blank" style="color: #38bdf8; text-decoration: none;">Terms of Service</a>, <a href="<?= url('/privacy') ?>" target="_blank" style="color: #38bdf8; text-decoration: none;">Privacy Policy</a>, and <a href="<?= url('/community-guidelines') ?>" target="_blank" style="color: #38bdf8; text-decoration: none;">Community Guidelines</a>.
        </label>
      </div>

      <!-- Submit Button -->
      <button type="submit" id="regSubmitBtn" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; font-weight: 700; border-radius: 8px; box-shadow: 0 4px 20px rgba(99,102,241,0.35);">
        Create Member Account 🚀
      </button>
    </form>

    <div style="text-align: center; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid rgba(148,163,184,0.12); font-size: 0.875rem; color: #94a3b8;">
      Already registered? <a href="<?= url('/auth/login') ?>" style="color: #38bdf8; font-weight: 600; text-decoration: none;">Sign in here &rarr;</a>
    </div>
  </div>
</div>

<script>
function togglePassVisibility() {
  const p = document.getElementById('regPassword');
  const icon = document.getElementById('passToggleIcon');
  if (p.type === 'password') {
    p.type = 'text';
    icon.setAttribute('data-lucide', 'eye-off');
  } else {
    p.type = 'password';
    icon.setAttribute('data-lucide', 'eye');
  }
  if (window.lucide) lucide.createIcons();
}

function checkPassStrength(v) {
  const fill = document.getElementById('passStrengthFill');
  if (!v) {
    fill.style.width = '0%';
    return;
  }
  let score = 0;
  if (v.length >= 6) score += 25;
  if (v.length >= 10) score += 25;
  if (/[0-9]/.test(v)) score += 25;
  if (/[^A-Za-z0-9]/.test(v)) score += 25;

  fill.style.width = score + '%';
  if (score <= 25) fill.style.background = '#ef4444';
  else if (score <= 50) fill.style.background = '#f59e0b';
  else if (score <= 75) fill.style.background = '#38bdf8';
  else fill.style.background = '#10b981';
}

// Live Username & Email Availability Engine
let usernameDebounceTimer = null;
let emailDebounceTimer = null;
let isUsernameAvailable = true;
let isEmailAvailable = true;

const usernameInput = document.getElementById('regUsername');
const usernameMsg = document.getElementById('usernameStatusMsg');
const emailInput = document.getElementById('regEmail');
const emailMsg = document.getElementById('emailStatusMsg');
const submitBtn = document.getElementById('regSubmitBtn');

if (usernameInput) {
  usernameInput.addEventListener('input', function() {
    clearTimeout(usernameDebounceTimer);
    const val = this.value.trim();

    if (!val) {
      usernameMsg.innerHTML = '';
      usernameInput.style.borderColor = 'rgba(148,163,184,0.2)';
      return;
    }

    if (val.length < 3) {
      usernameMsg.innerHTML = '<span style="color: #f59e0b; font-weight: 500;">⚠️ Username must be at least 3 characters.</span>';
      usernameInput.style.borderColor = '#f59e0b';
      isUsernameAvailable = false;
      return;
    }

    if (val.length > 30) {
      usernameMsg.innerHTML = '<span style="color: #ef4444; font-weight: 500;">✕ Maximum 30 characters allowed.</span>';
      usernameInput.style.borderColor = '#ef4444';
      isUsernameAvailable = false;
      return;
    }

    if (!/^[a-zA-Z0-9_]+$/.test(val)) {
      usernameMsg.innerHTML = '<span style="color: #ef4444; font-weight: 500;">✕ Only letters, numbers, and underscores allowed.</span>';
      usernameInput.style.borderColor = '#ef4444';
      isUsernameAvailable = false;
      return;
    }

    usernameMsg.innerHTML = '<span style="color: #38bdf8;">⏳ Checking availability...</span>';

    usernameDebounceTimer = setTimeout(async () => {
      try {
        const res = await fetch(`<?= url('/api/v1/auth/check-availability') ?>?type=username&value=${encodeURIComponent(val)}`);
        const data = await res.json();
        if (data.available) {
          usernameMsg.innerHTML = `<span style="color: #10b981; font-weight: 600;">✓ @${val} is available!</span>`;
          usernameInput.style.borderColor = 'rgba(16,185,129,0.5)';
          isUsernameAvailable = true;
        } else {
          usernameMsg.innerHTML = `<span style="color: #ef4444; font-weight: 600;">✕ ${data.message || 'Username taken'}</span>`;
          usernameInput.style.borderColor = 'rgba(239,68,68,0.5)';
          isUsernameAvailable = false;
        }
      } catch (err) {
        usernameMsg.innerHTML = '';
      }
    }, 350);
  });
}

if (emailInput) {
  emailInput.addEventListener('input', function() {
    clearTimeout(emailDebounceTimer);
    const val = this.value.trim();

    if (!val) {
      emailMsg.innerHTML = '';
      emailInput.style.borderColor = 'rgba(148,163,184,0.2)';
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
      emailMsg.innerHTML = '<span style="color: #f59e0b; font-weight: 500;">⚠️ Enter a valid email format.</span>';
      emailInput.style.borderColor = '#f59e0b';
      isEmailAvailable = false;
      return;
    }

    emailMsg.innerHTML = '<span style="color: #38bdf8;">⏳ Checking availability...</span>';

    emailDebounceTimer = setTimeout(async () => {
      try {
        const res = await fetch(`<?= url('/api/v1/auth/check-availability') ?>?type=email&value=${encodeURIComponent(val)}`);
        const data = await res.json();
        if (data.available) {
          emailMsg.innerHTML = `<span style="color: #10b981; font-weight: 600;">✓ Email is available!</span>`;
          emailInput.style.borderColor = 'rgba(16,185,129,0.5)';
          isEmailAvailable = true;
        } else {
          emailMsg.innerHTML = `<span style="color: #ef4444; font-weight: 600;">✕ ${data.message || 'Email already registered'}</span>`;
          emailInput.style.borderColor = 'rgba(239,68,68,0.5)';
          isEmailAvailable = false;
        }
      } catch (err) {
        emailMsg.innerHTML = '';
      }
    }, 350);
  });
}

const regForm = document.getElementById('regForm');
if (regForm) {
  regForm.addEventListener('submit', function(e) {
    if (!isUsernameAvailable) {
      e.preventDefault();
      usernameInput.focus();
      return;
    }
    if (!isEmailAvailable) {
      e.preventDefault();
      emailInput.focus();
      return;
    }
  });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
