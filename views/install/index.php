<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 820px; margin: 3.5rem auto 6rem auto; padding: 0 1.5rem;">

  <!-- Installer Brand Banner -->
  <div style="text-align: center; margin-bottom: 2.5rem;">
    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(56,189,248,0.1); border: 1px solid rgba(56,189,248,0.25); padding: 0.35rem 0.85rem; border-radius: 9999px; margin-bottom: 1rem;">
      <i data-lucide="sparkles" style="width: 15px; height: 15px; color: var(--ts-primary);"></i>
      <span style="font-size: 0.78rem; font-weight: 700; color: var(--ts-primary); text-transform: uppercase;">
        <?= $isReinstall ? 'Platform Reconfiguration Wizard' : 'Production Web Installer v2.5' ?>
      </span>
    </div>
    <h1 style="font-size: 2.35rem; font-weight: 800; color: var(--ts-text-primary); margin: 0 0 0.5rem 0; font-family: 'Outfit', sans-serif;">
      Enterprise Setup &amp; White-Label Studio
    </h1>
    <p style="color: var(--ts-text-secondary); font-size: 0.95rem; max-width: 580px; margin: 0 auto;">
      Configure system environment, verify database connectivity, customize white-label branding, and provision administrator credentials.
    </p>
  </div>

  <!-- Recovery Mode Banner -->
  <?php if (!empty($isRecovery) || empty($dbConnected)): ?>
    <div style="background: rgba(245,158,11,0.09); border: 1px solid rgba(245,158,11,0.3); border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 1rem;">
      <div style="background: rgba(245,158,11,0.2); color: #f59e0b; width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <i data-lucide="database" style="width: 20px; height: 20px;"></i>
      </div>
      <div style="flex: 1;">
        <div style="font-weight: 700; color: #f59e0b; font-size: 1rem; margin-bottom: 0.25rem;">
          Database Connection Recovery Mode
        </div>
        <p style="color: var(--ts-text-secondary); font-size: 0.88rem; margin: 0; line-height: 1.5;">
          The configured database server could not be reached (common when downloaded from FTP backups because remote MySQL connections are prohibited by hosting firewalls). Local MySQL parameters have been populated below. You can test and save them directly.
        </p>
      </div>
    </div>
  <?php endif; ?>

  <!-- Multi-Step Progress Tracker -->
  <div class="installer-step-tracker" style="display: flex; justify-content: space-between; position: relative; margin-bottom: 2.5rem;">
    <div class="step-indicator active" id="step_indicator_1" onclick="goToStep(1)">
      <div class="step-badge">1</div>
      <div class="step-label">Prerequisites</div>
    </div>
    <div class="step-indicator" id="step_indicator_2" onclick="goToStep(2)">
      <div class="step-badge">2</div>
      <div class="step-label">Database</div>
    </div>
    <div class="step-indicator" id="step_indicator_3" onclick="goToStep(3)">
      <div class="step-badge">3</div>
      <div class="step-label">White-Label</div>
    </div>
    <div class="step-indicator" id="step_indicator_4" onclick="goToStep(4)">
      <div class="step-badge">4</div>
      <div class="step-label">Admin Account</div>
    </div>
    <div class="step-indicator" id="step_indicator_5" onclick="goToStep(5)">
      <div class="step-badge">5</div>
      <div class="step-label">Launch</div>
    </div>
  </div>

  <!-- Main Installer Card -->
  <div class="ts-card" style="padding: 2.25rem; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); border-radius: 16px; box-shadow: var(--ts-shadow-xl);">
    
    <form id="installerForm" onsubmit="handleInstallerSubmit(event)">

      <!-- ================================================================= -->
      <!-- STEP 1: PREREQUISITES & SYSTEM REQUIREMENTS -->
      <!-- ================================================================= -->
      <div class="installer-step-pane" id="step_pane_1">
        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 1rem;">
          <h2 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">1. System Health &amp; Prerequisites</h2>
          <p style="color: var(--ts-text-secondary); font-size: 0.85rem; margin: 0;">Verifying PHP version, required MySQL extensions, and storage permissions.</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.65rem; margin-bottom: 2rem;">
          <?php foreach ($requirements as $req): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-radius: 8px; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle);">
              <div style="display: flex; align-items: center; gap: 0.65rem;">
                <?php if ($req['passed']): ?>
                  <span style="color: var(--ts-success); font-size: 1.1rem;">✓</span>
                <?php else: ?>
                  <span style="color: var(--ts-danger); font-size: 1.1rem;">✗</span>
                <?php endif; ?>
                <div>
                  <strong style="color: var(--ts-text-primary); font-size: 0.88rem;"><?= htmlspecialchars($req['name']) ?></strong>
                  <div style="font-size: 0.75rem; color: var(--ts-text-tertiary);">Requirement: <?= htmlspecialchars($req['required']) ?></div>
                </div>
              </div>
              <span class="ts-badge <?= $req['passed'] ? 'ts-badge-green' : 'ts-badge-red' ?>" style="font-family: var(--ts-font-mono); font-size: 0.72rem;">
                <?= htmlspecialchars($req['current']) ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if (!$allPassed): ?>
          <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #f87171; font-size: 0.85rem;">
            ⚠️ One or more critical system requirements were not met. Please ensure all PHP extensions are enabled before proceeding.
          </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: flex-end;">
          <button type="button" onclick="nextStep(2)" class="ts-btn ts-btn-primary" <?= !$allPassed ? 'disabled style="opacity: 0.5;"' : '' ?>>
            Continue to Database Setup <i data-lucide="arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- ================================================================= -->
      <!-- STEP 2: DATABASE CONNECTION CONFIGURATION -->
      <!-- ================================================================= -->
      <div class="installer-step-pane" id="step_pane_2" style="display: none;">
        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 1rem;">
          <h2 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">2. Database Configuration &amp; Live Test</h2>
          <p style="color: var(--ts-text-secondary); font-size: 0.85rem; margin: 0;">Specify MySQL / MariaDB connection parameters and verify connectivity in real-time.</p>
        </div>

        <!-- Quick DB Presets -->
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
          <button type="button" onclick="setDbPreset('local')" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.78rem;">
            💻 Localhost MySQL Defaults (127.0.0.1 / root)
          </button>
          <button type="button" onclick="setDbPreset('infinityfree')" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.78rem;">
            ☁️ InfinityFree Production Hosted Credentials
          </button>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label class="ts-form-label">Database Host / Server IP</label>
            <input type="text" name="db_host" id="input_db_host" value="<?= htmlspecialchars($currentConfig['db_host']) ?>" class="ts-form-input" placeholder="127.0.0.1 or sql306.infinityfree.com" required>
          </div>
          <div>
            <label class="ts-form-label">Port</label>
            <input type="text" name="db_port" id="input_db_port" value="<?= htmlspecialchars($currentConfig['db_port']) ?>" class="ts-form-input" placeholder="3306" required>
          </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label class="ts-form-label">Database Name</label>
          <input type="text" name="db_name" id="input_db_name" value="<?= htmlspecialchars($currentConfig['db_name']) ?>" class="ts-form-input" placeholder="techscout_db" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
          <div>
            <label class="ts-form-label">Database Username</label>
            <input type="text" name="db_user" id="input_db_user" value="<?= htmlspecialchars($currentConfig['db_user']) ?>" class="ts-form-input" placeholder="root" required>
          </div>
          <div>
            <label class="ts-form-label">Database Password</label>
            <input type="password" name="db_pass" id="input_db_pass" value="<?= htmlspecialchars($currentConfig['db_pass']) ?>" class="ts-form-input" placeholder="Enter password (leave blank for local root)">
          </div>
        </div>

        <!-- Connection Test Status Box -->
        <div id="dbTestStatusBox" style="display: none; padding: 0.85rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem;"></div>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
          <button type="button" onclick="testDatabaseConnection()" id="btnTestDb" class="ts-btn ts-btn-outline" style="color: var(--ts-primary); border-color: rgba(56,189,248,0.3);">
            <i data-lucide="activity"></i> Test Database Connection ⚡
          </button>
          <div style="display: flex; gap: 0.75rem;">
            <button type="button" onclick="prevStep(1)" class="ts-btn ts-btn-outline">Back</button>
            <button type="button" onclick="nextStep(3)" class="ts-btn ts-btn-primary">
              Continue to White-Label <i data-lucide="arrow-right"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- ================================================================= -->
      <!-- STEP 3: WHITE-LABEL BRANDING & THEME STUDIO -->
      <!-- ================================================================= -->
      <div class="installer-step-pane" id="step_pane_3" style="display: none;">
        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 1rem;">
          <h2 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">3. White-Label Branding &amp; Platform Identity</h2>
          <p style="color: var(--ts-text-secondary); font-size: 0.85rem; margin: 0;">Customize platform brand name, tagline, corporate color scheme, and support contact.</p>
        </div>

        <!-- Branding Presets -->
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
          <button type="button" onclick="setBrandingPreset('techscout')" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.78rem;">
            🚀 TechScout Default (#38bdf8)
          </button>
          <button type="button" onclick="setBrandingPreset('cyberscout')" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.78rem;">
            ⚡ CyberScout Pro (#10b981)
          </button>
          <button type="button" onclick="setBrandingPreset('aipulse')" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.78rem;">
            💜 AI Pulse Studio (#8b5cf6)
          </button>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label class="ts-form-label">Platform Brand Name</label>
            <input type="text" name="site_name" id="input_site_name" value="<?= htmlspecialchars($branding['site_name']) ?>" class="ts-form-input" placeholder="e.g. TechScout or YourBrand" required>
          </div>
          <div>
            <label class="ts-form-label">Primary Brand Accent Color</label>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
              <input type="color" id="input_color_picker" value="<?= htmlspecialchars($branding['primary_color']) ?>" onchange="document.getElementById('input_primary_color').value = this.value" style="width: 42px; height: 38px; border: none; border-radius: 6px; cursor: pointer; background: transparent;">
              <input type="text" name="primary_color" id="input_primary_color" value="<?= htmlspecialchars($branding['primary_color']) ?>" class="ts-form-input" placeholder="#38bdf8" style="flex: 1;" onchange="document.getElementById('input_color_picker').value = this.value">
            </div>
          </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label class="ts-form-label">Platform Tagline / Slogan</label>
          <input type="text" name="site_tagline" id="input_site_tagline" value="<?= htmlspecialchars($branding['site_tagline']) ?>" class="ts-form-input" placeholder="Next-Gen AI Directory & Intelligence" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label class="ts-form-label">Support Email Address</label>
            <input type="email" name="support_email" id="input_support_email" value="<?= htmlspecialchars($branding['support_email']) ?>" class="ts-form-input" placeholder="support@yourbrand.com" required>
          </div>
          <div>
            <label class="ts-form-label">Default Theme Mode</label>
            <select name="default_theme" id="input_default_theme" class="ts-form-input">
              <option value="dark" <?= ($branding['default_theme'] === 'dark') ? 'selected' : '' ?>>Obsidian Dark Mode (Default)</option>
              <option value="light" <?= ($branding['default_theme'] === 'light') ? 'selected' : '' ?>>Clean Light Mode</option>
            </select>
          </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
          <label class="ts-form-label">Footer Copyright Notice</label>
          <input type="text" name="site_copyright" id="input_site_copyright" value="<?= htmlspecialchars($branding['site_copyright']) ?>" class="ts-form-input" placeholder="© 2026 YourBrand Inc. All rights reserved.">
        </div>

        <div style="display: flex; justify-content: space-between;">
          <button type="button" onclick="prevStep(2)" class="ts-btn ts-btn-outline">Back</button>
          <button type="button" onclick="nextStep(4)" class="ts-btn ts-btn-primary">
            Continue to Administrator Setup <i data-lucide="arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- ================================================================= -->
      <!-- STEP 4: SUPER ADMINISTRATOR ACCOUNT -->
      <!-- ================================================================= -->
      <div class="installer-step-pane" id="step_pane_4" style="display: none;">
        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 1rem;">
          <h2 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">4. Super Administrator Account Setup</h2>
          <p style="color: var(--ts-text-secondary); font-size: 0.85rem; margin: 0;">Create or update the primary master SuperAdmin account with complete administrative control.</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label class="ts-form-label">Administrator Username</label>
            <input type="text" name="admin_username" id="input_admin_username" value="admin" class="ts-form-input" placeholder="admin" required>
          </div>
          <div>
            <label class="ts-form-label">Administrator Email</label>
            <input type="email" name="admin_email" id="input_admin_email" value="admin@techscout.ai" class="ts-form-input" placeholder="admin@domain.com" required>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
          <div>
            <label class="ts-form-label">Master Password</label>
            <input type="password" name="admin_password" id="input_admin_password" class="ts-form-input" placeholder="Minimum 8 characters" <?= !$isReinstall ? 'required' : '' ?>>
            <?php if ($isReinstall): ?>
              <small style="color: var(--ts-text-tertiary); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Leave blank to preserve existing master password.</small>
            <?php endif; ?>
          </div>
          <div>
            <label class="ts-form-label">Confirm Password</label>
            <input type="password" id="input_admin_password_confirm" class="ts-form-input" placeholder="Re-type password">
          </div>
        </div>

        <div style="display: flex; justify-content: space-between;">
          <button type="button" onclick="prevStep(3)" class="ts-btn ts-btn-outline">Back</button>
          <button type="button" onclick="nextStep(5)" class="ts-btn ts-btn-primary">
            Review &amp; Finalize Installation <i data-lucide="arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- ================================================================= -->
      <!-- STEP 5: REVIEW & LAUNCH INSTALLATION -->
      <!-- ================================================================= -->
      <div class="installer-step-pane" id="step_pane_5" style="display: none;">
        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 1rem;">
          <h2 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">5. Review &amp; Launch Installation</h2>
          <p style="color: var(--ts-text-secondary); font-size: 0.85rem; margin: 0;">Review your setup summary and click Launch to initialize the database and finalize deployment.</p>
        </div>

        <!-- Summary Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.75rem;">
          <div style="padding: 1rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 8px;">
            <div style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700; margin-bottom: 0.4rem;">Database Target</div>
            <div style="font-weight: 700; color: var(--ts-text-primary); font-family: var(--ts-font-mono);" id="summary_db">127.0.0.1:3306 / techscout_db</div>
          </div>
          <div style="padding: 1rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 8px;">
            <div style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700; margin-bottom: 0.4rem;">White-Label Brand</div>
            <div style="font-weight: 700; color: var(--ts-primary);" id="summary_brand">TechScout</div>
          </div>
          <div style="padding: 1rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 8px;">
            <div style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700; margin-bottom: 0.4rem;">Super Administrator</div>
            <div style="font-weight: 700; color: var(--ts-text-primary);" id="summary_admin">admin (admin@techscout.ai)</div>
          </div>
          <div style="padding: 1rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 8px;">
            <div style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700; margin-bottom: 0.4rem;">Lock Protection</div>
            <div style="font-weight: 700; color: var(--ts-success);">Auto-lock upon completion ✓</div>
          </div>
        </div>

        <div id="installErrorBox" style="display: none; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #f87171; font-size: 0.85rem;"></div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
          <button type="button" onclick="prevStep(4)" class="ts-btn ts-btn-outline">Back</button>
          <button type="submit" id="btnLaunchInstall" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #0284c7, #38bdf8); font-weight: 800; font-size: 1.05rem; padding: 0.75rem 2rem; box-shadow: 0 4px 15px rgba(56,189,248,0.4);">
            <i data-lucide="rocket"></i> Launch &amp; Finalize Platform 🚀
          </button>
        </div>
      </div>

    </form>

  </div>
</div>

<style>
.installer-step-tracker {
  display: flex;
  justify-content: space-between;
  position: relative;
}
.installer-step-tracker::before {
  content: '';
  position: absolute;
  top: 18px;
  left: 30px;
  right: 30px;
  height: 2px;
  background: var(--ts-border-main);
  z-index: 1;
}
.step-indicator {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.4rem;
  cursor: pointer;
  flex: 1;
}
.step-badge {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--ts-bg-surface-elevated);
  border: 2px solid var(--ts-border-main);
  color: var(--ts-text-tertiary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 0.85rem;
  transition: all 0.25s ease;
}
.step-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--ts-text-muted);
  text-transform: uppercase;
  transition: color 0.25s ease;
}
.step-indicator.active .step-badge {
  background: var(--ts-primary);
  border-color: var(--ts-primary);
  color: #ffffff;
  box-shadow: 0 0 15px rgba(56,189,248,0.5);
}
.step-indicator.active .step-label {
  color: var(--ts-text-primary);
}
.step-indicator.completed .step-badge {
  background: var(--ts-success);
  border-color: var(--ts-success);
  color: #ffffff;
}
.ts-form-label {
  display: block;
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--ts-text-secondary);
  margin-bottom: 0.4rem;
  text-transform: uppercase;
}
.ts-form-input {
  width: 100%;
  background: var(--ts-bg-surface-elevated);
  border: 1px solid var(--ts-border-main);
  border-radius: 8px;
  padding: 0.65rem 0.95rem;
  color: var(--ts-text-primary);
  font-size: 0.9rem;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.ts-form-input:focus {
  border-color: var(--ts-primary);
  box-shadow: 0 0 0 3px rgba(56,189,248,0.15);
}
</style>

<script>
let currentInstallerStep = 1;

function goToStep(step) {
  if (step > currentInstallerStep) {
    // Validate current step before advancing
    if (!validateStep(currentInstallerStep)) return;
  }
  showStep(step);
}

function nextStep(step) {
  if (!validateStep(currentInstallerStep)) return;
  showStep(step);
}

function prevStep(step) {
  showStep(step);
}

function showStep(step) {
  document.querySelectorAll('.installer-step-pane').forEach(p => p.style.display = 'none');
  const target = document.getElementById('step_pane_' + step);
  if (target) target.style.display = 'block';

  document.querySelectorAll('.step-indicator').forEach((ind, idx) => {
    const s = idx + 1;
    ind.classList.remove('active', 'completed');
    if (s === step) ind.classList.add('active');
    else if (s < step) ind.classList.add('completed');
  });

  currentInstallerStep = step;

  if (step === 5) {
    updateSummary();
  }
}

function validateStep(step) {
  if (step === 2) {
    const host = document.getElementById('input_db_host')?.value.trim();
    const name = document.getElementById('input_db_name')?.value.trim();
    const user = document.getElementById('input_db_user')?.value.trim();
    if (!host || !name || !user) {
      alert('Please fill out all required database connection fields.');
      return false;
    }
  } else if (step === 3) {
    const siteName = document.getElementById('input_site_name')?.value.trim();
    const email = document.getElementById('input_support_email')?.value.trim();
    if (!siteName || !email) {
      alert('Please fill out platform name and support email.');
      return false;
    }
  } else if (step === 4) {
    const u = document.getElementById('input_admin_username')?.value.trim();
    const e = document.getElementById('input_admin_email')?.value.trim();
    const p1 = document.getElementById('input_admin_password')?.value;
    const p2 = document.getElementById('input_admin_password_confirm')?.value;

    if (!u || !e) {
      alert('Please specify an administrator username and email.');
      return false;
    }
    if (p1 && p1.length < 8) {
      alert('Password must be at least 8 characters in length.');
      return false;
    }
    if (p1 && p1 !== p2) {
      alert('Passwords do not match. Please verify.');
      return false;
    }
  }
  return true;
}

function updateSummary() {
  const host = document.getElementById('input_db_host')?.value.trim() || '127.0.0.1';
  const port = document.getElementById('input_db_port')?.value.trim() || '3306';
  const db = document.getElementById('input_db_name')?.value.trim() || 'techscout_db';
  const siteName = document.getElementById('input_site_name')?.value.trim() || 'TechScout';
  const adminUser = document.getElementById('input_admin_username')?.value.trim() || 'admin';
  const adminEmail = document.getElementById('input_admin_email')?.value.trim() || 'admin@techscout.ai';

  const sumDb = document.getElementById('summary_db');
  if (sumDb) sumDb.innerText = `${host}:${port} / ${db}`;
  const sumBrand = document.getElementById('summary_brand');
  if (sumBrand) sumBrand.innerText = siteName;
  const sumAdmin = document.getElementById('summary_admin');
  if (sumAdmin) sumAdmin.innerText = `${adminUser} (${adminEmail})`;
}

function testDatabaseConnection() {
  const btn = document.getElementById('btnTestDb');
  const box = document.getElementById('dbTestStatusBox');
  if (!btn || !box) return;

  btn.disabled = true;
  btn.innerHTML = '<span style="display: inline-block; animation: spin 1s infinite linear;">⌛</span> Testing Connection...';
  box.style.display = 'none';

  const formData = new FormData();
  formData.append('db_host', document.getElementById('input_db_host')?.value || '');
  formData.append('db_port', document.getElementById('input_db_port')?.value || '');
  formData.append('db_name', document.getElementById('input_db_name')?.value || '');
  formData.append('db_user', document.getElementById('input_db_user')?.value || '');
  formData.append('db_pass', document.getElementById('input_db_pass')?.value || '');

  fetch('<?= url("/install/test-db") ?>', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
  })
  .then(r => r.json())
  .then(res => {
    btn.disabled = false;
    btn.innerHTML = '<i data-lucide="activity"></i> Test Database Connection ⚡';
    if (window.lucide) lucide.createIcons();

    box.style.display = 'block';
    if (res.success) {
      box.style.background = 'rgba(16,185,129,0.1)';
      box.style.border = '1px solid rgba(16,185,129,0.3)';
      box.style.color = '#34d399';
      box.innerHTML = '<strong>✓ Success:</strong> ' + res.message;
    } else {
      box.style.background = 'rgba(239,68,68,0.1)';
      box.style.border = '1px solid rgba(239,68,68,0.3)';
      box.style.color = '#f87171';
      box.innerHTML = '<strong>✗ Connection Failed:</strong> ' + (res.message || 'Unknown database error');
    }
  })
  .catch(err => {
    btn.disabled = false;
    btn.innerHTML = '<i data-lucide="activity"></i> Test Database Connection ⚡';
    box.style.display = 'block';
    box.style.background = 'rgba(239,68,68,0.1)';
    box.style.border = '1px solid rgba(239,68,68,0.3)';
    box.style.color = '#f87171';
    box.innerText = 'Network error while attempting database test.';
  });
}

function handleInstallerSubmit(e) {
  e.preventDefault();
  const btn = document.getElementById('btnLaunchInstall');
  const errBox = document.getElementById('installErrorBox');
  if (errBox) errBox.style.display = 'none';

  if (btn) {
    btn.disabled = true;
    btn.innerHTML = 'Initializing Platform &amp; Importing Schema...';
  }

  const form = document.getElementById('installerForm');
  const formData = new FormData(form);

  fetch('<?= url("/install/process") ?>', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      if (btn) btn.innerHTML = '✓ Installation Succeeded! Redirecting...';
      setTimeout(() => {
        window.location.href = '<?= url("/auth/login") ?>';
      }, 1200);
    } else {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="rocket"></i> Launch &amp; Finalize Platform 🚀';
      }
      if (errBox) {
        errBox.style.display = 'block';
        errBox.innerHTML = '<strong>Installation Error:</strong> ' + (res.message || 'Unknown failure occurred.');
      }
    }
  })
  .catch(err => {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="rocket"></i> Launch &amp; Finalize Platform 🚀';
    }
    if (errBox) {
      errBox.style.display = 'block';
      errBox.innerText = 'Network failure during installation. Please check server logs.';
    }
  });
}

function setDbPreset(type) {
  if (type === 'local') {
    document.getElementById('input_db_host').value = '127.0.0.1';
    document.getElementById('input_db_port').value = '3306';
    document.getElementById('input_db_name').value = 'techscout_db';
    document.getElementById('input_db_user').value = 'root';
    document.getElementById('input_db_pass').value = '';
  } else if (type === 'infinityfree') {
    document.getElementById('input_db_host').value = 'sql306.infinityfree.com';
    document.getElementById('input_db_port').value = '3306';
    document.getElementById('input_db_name').value = 'if0_40102772_techscoutmainafterallchanges';
    document.getElementById('input_db_user').value = 'if0_40102772';
    document.getElementById('input_db_pass').value = 'Fp9QFWfj5fm';
  }
}

function setBrandingPreset(type) {
  if (type === 'techscout') {
    document.getElementById('input_site_name').value = 'TechScout';
    document.getElementById('input_site_tagline').value = 'Enterprise AI Directory & Engineering Ecosystem';
    document.getElementById('input_primary_color').value = '#38bdf8';
    document.getElementById('input_color_picker').value = '#38bdf8';
    document.getElementById('input_support_email').value = 'support@techscout.ai';
  } else if (type === 'cyberscout') {
    document.getElementById('input_site_name').value = 'CyberScout Pro';
    document.getElementById('input_site_tagline').value = 'Next-Gen Cybersecurity & AI Developer Hub';
    document.getElementById('input_primary_color').value = '#10b981';
    document.getElementById('input_color_picker').value = '#10b981';
    document.getElementById('input_support_email').value = 'contact@cyberscout.pro';
  } else if (type === 'aipulse') {
    document.getElementById('input_site_name').value = 'AI Pulse Studio';
    document.getElementById('input_site_tagline').value = 'Curated Intelligence & Autonomous AI Workflows';
    document.getElementById('input_primary_color').value = '#8b5cf6';
    document.getElementById('input_color_picker').value = '#8b5cf6';
    document.getElementById('input_support_email').value = 'hello@aipulse.studio';
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
