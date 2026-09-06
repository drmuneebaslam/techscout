<?php require __DIR__ . '/../layouts/admin_header.php'; 

$activeCount = count(array_filter($flags, fn($f) => !empty($f['is_enabled'])));
$disabledCount = count($flags) - $activeCount;
?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink">DYNAMIC RUNTIME ENGINE</span>
        <span style="font-size: 0.8rem; color: #64748b;">Live Platform Module &amp; Capability Switches</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Feature Flags &amp; Capability Control
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/dashboard') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="arrow-left"></i> Dashboard
      </a>
      <button type="submit" form="flagsForm" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 700;">
        <i data-lucide="save"></i> Deploy Flag Updates ⚡
      </button>
    </div>
  </div>

  <!-- Telemetry Overview HUD -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="ts-card" style="padding: 1.15rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); display: flex; align-items: center; gap: 1rem;">
      <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981;">
        <i data-lucide="check-circle-2" style="width: 22px; height: 22px;"></i>
      </div>
      <div>
        <div style="font-size: 1.35rem; font-weight: 800; color: #34d399;" id="stat-active-count"><?= $activeCount ?></div>
        <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Active Online Flags</div>
      </div>
    </div>

    <div class="ts-card" style="padding: 1.15rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); display: flex; align-items: center; gap: 1rem;">
      <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(239,68,68,0.15); display: flex; align-items: center; justify-content: center; color: #ef4444;">
        <i data-lucide="pause-circle" style="width: 22px; height: 22px;"></i>
      </div>
      <div>
        <div style="font-size: 1.35rem; font-weight: 800; color: #f87171;" id="stat-disabled-count"><?= $disabledCount ?></div>
        <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Disabled Modules</div>
      </div>
    </div>

    <div class="ts-card" style="padding: 1.15rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); display: flex; align-items: center; gap: 1rem;">
      <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8;">
        <i data-lucide="layers" style="width: 22px; height: 22px;"></i>
      </div>
      <div>
        <div style="font-size: 1.35rem; font-weight: 800; color: #38bdf8;"><?= count($flags) ?></div>
        <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Total Control Switches</div>
      </div>
    </div>
  </div>

  <div class="admin-table-wrap" style="padding: 1.75rem; border-color: rgba(16,185,129,0.3); margin-bottom: 2rem;">
    <!-- Controls Filter & Search Strip -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
      <!-- Category Tabs -->
      <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
        <button type="button" onclick="filterFlagCategory('all')" id="fcat-btn-all" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
          All (<?= count($flags) ?>)
        </button>
        <button type="button" onclick="filterFlagCategory('modules')" id="fcat-btn-modules" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
          📦 Modules
        </button>
        <button type="button" onclick="filterFlagCategory('community')" id="fcat-btn-community" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
          👥 Community
        </button>
        <button type="button" onclick="filterFlagCategory('ux')" id="fcat-btn-ux" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
          ✨ UX &amp; Theme
        </button>
        <button type="button" onclick="filterFlagCategory('integrations')" id="fcat-btn-integrations" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
          🔌 Integrations
        </button>
        <button type="button" onclick="filterFlagCategory('security')" id="fcat-btn-security" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
          🛡️ Security
        </button>
        <button type="button" onclick="filterFlagCategory('seo')" id="fcat-btn-seo" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
          📡 SEO &amp; Feeds
        </button>
      </div>

      <!-- Search Input -->
      <div style="position: relative; width: 260px;">
        <input type="text" id="flagSearchInput" oninput="searchFlags()" placeholder="Search flags & keys..." style="width: 100%; background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.4rem 0.75rem 0.4rem 2rem; color: #f8fafc; font-size: 0.82rem;">
        <i data-lucide="search" style="position: absolute; left: 0.65rem; top: 50%; transform: translateY(-50%); width: 13px; height: 13px; color: #64748b;"></i>
      </div>
    </div>

    <!-- Flags Form with Real-time & Batch Deployment -->
    <form action="<?= url('/admin/feature-flags/save') ?>" method="POST" id="flagsForm">
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 340px), 1fr)); gap: 1.25rem;" id="flagsGrid">
        <?php foreach ($flags as $flag): 
          $isEnabled = !empty($flag['is_enabled']);
        ?>
          <div class="ts-card flag-item-card" 
               id="flag-card-<?= htmlspecialchars($flag['flag_key']) ?>"
               data-category="<?= htmlspecialchars($flag['category']) ?>"
               data-search="<?= htmlspecialchars(strtolower($flag['name'] . ' ' . $flag['flag_key'] . ' ' . $flag['description'])) ?>"
               style="background: rgba(15,23,42,0.85); border: 1px solid <?= $isEnabled ? 'rgba(16,185,129,0.35)' : 'rgba(239,68,68,0.25)' ?>; display: flex; flex-direction: column; justify-content: space-between; padding: 1.25rem; border-radius: 12px; transition: all 0.25s ease;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="ts-badge flag-status-badge <?= $isEnabled ? 'ts-badge-green' : 'ts-badge-pink' ?>" 
                      id="badge-status-<?= htmlspecialchars($flag['flag_key']) ?>" 
                      style="font-size: 0.65rem; text-transform: uppercase;">
                  <?= $isEnabled ? 'ONLINE' : 'DISABLED' ?>
                </span>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; font-family: monospace;">
                  <?= htmlspecialchars($flag['category']) ?>
                </span>
              </div>
              <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0 0 0.35rem 0;"><?= htmlspecialchars($flag['name']) ?></h4>
              <p style="font-size: 0.82rem; color: #94a3b8; line-height: 1.45; margin: 0 0 0.85rem 0;"><?= htmlspecialchars($flag['description']) ?></p>
              <div style="font-size: 0.72rem; color: #64748b; font-family: var(--admin-font-mono);">
                Key: <code><?= htmlspecialchars($flag['flag_key']) ?></code>
              </div>
            </div>

            <div style="border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.85rem; margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
              <span class="flag-status-label" id="label-status-<?= htmlspecialchars($flag['flag_key']) ?>" style="font-size: 0.8rem; color: <?= $isEnabled ? '#34d399' : '#f87171' ?>; font-weight: 600;">
                <?= $isEnabled ? 'Active on Public Site' : 'Currently Deactivated' ?>
              </span>

              <!-- Live Interactive CSS Toggle Switch -->
              <label class="ts-toggle-switch" style="position: relative; display: inline-block; width: 46px; height: 24px; margin: 0; cursor: pointer;">
                <input type="checkbox" 
                       name="<?= htmlspecialchars($flag['flag_key']) ?>" 
                       value="1" 
                       id="input-flag-<?= htmlspecialchars($flag['flag_key']) ?>"
                       <?= $isEnabled ? 'checked' : '' ?> 
                       onchange="handleFlagToggle('<?= htmlspecialchars($flag['flag_key']) ?>', this)">
                <span class="ts-toggle-slider"></span>
              </label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div style="margin-top: 2rem; text-align: right; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <span style="font-size: 0.82rem; color: #64748b;">
          💡 Changes toggle in real-time via AJAX and can also be bulk deployed.
        </span>
        <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 700; padding: 0.75rem 2.5rem;">
          <i data-lucide="save"></i> Deploy Feature Flag Changes ⚡
        </button>
      </div>
    </form>
  </div>
</div>

<style>
/* Modern Interactive Toggle Switch CSS */
.ts-toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
  position: absolute;
}

.ts-toggle-slider {
  position: absolute;
  cursor: pointer;
  inset: 0;
  background-color: #334155;
  transition: background-color 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease;
  border-radius: 9999px;
}

.ts-toggle-slider::before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0,0,0,0.4);
}

.ts-toggle-switch input:checked + .ts-toggle-slider {
  background-color: #10b981;
  box-shadow: 0 0 12px rgba(16, 185, 129, 0.45);
}

.ts-toggle-switch input:checked + .ts-toggle-slider::before {
  transform: translateX(22px);
}
</style>

<script>
async function handleFlagToggle(flagKey, checkbox) {
  const isChecked = checkbox.checked;
  const card = document.getElementById('flag-card-' + flagKey);
  const badge = document.getElementById('badge-status-' + flagKey);
  const label = document.getElementById('label-status-' + flagKey);

  // Optimistic UI updates
  if (badge) {
    badge.innerText = isChecked ? 'ONLINE' : 'DISABLED';
    badge.className = 'ts-badge flag-status-badge ' + (isChecked ? 'ts-badge-green' : 'ts-badge-pink');
  }
  if (label) {
    label.innerText = isChecked ? 'Active on Public Site' : 'Currently Deactivated';
    label.style.color = isChecked ? '#34d399' : '#f87171';
  }
  if (card) {
    card.style.borderColor = isChecked ? 'rgba(16,185,129,0.35)' : 'rgba(239,68,68,0.25)';
  }

  // Update counters
  updateTelemetryHUD();

  try {
    const fd = new FormData();
    fd.append('flag_key', flagKey);
    fd.append('is_enabled', isChecked ? '1' : '0');

    const res = await fetch('<?= url('/admin/feature-flags/toggle') ?>', {
      method: 'POST',
      body: fd
    });

    const data = await res.json();
    if (data.success) {
      if (typeof showToast === 'function') {
        showToast(`Flag "${flagKey}" is now ${isChecked ? 'ONLINE ⚡' : 'DEACTIVATED 🔒'}`, 'success');
      }
    } else {
      throw new Error(data.error || 'Server rejected toggle');
    }
  } catch (err) {
    // Revert optimistic UI on error
    checkbox.checked = !isChecked;
    if (badge) {
      badge.innerText = !isChecked ? 'ONLINE' : 'DISABLED';
      badge.className = 'ts-badge flag-status-badge ' + (!isChecked ? 'ts-badge-green' : 'ts-badge-pink');
    }
    if (label) {
      label.innerText = !isChecked ? 'Active on Public Site' : 'Currently Deactivated';
      label.style.color = !isChecked ? '#34d399' : '#f87171';
    }
    updateTelemetryHUD();
    if (typeof showToast === 'function') {
      showToast('Failed to update flag: ' + err.message, 'error');
    }
  }
}

function updateTelemetryHUD() {
  const allCheckboxes = document.querySelectorAll('#flagsGrid input[type="checkbox"]');
  let active = 0;
  allCheckboxes.forEach(cb => { if (cb.checked) active++; });
  const disabled = allCheckboxes.length - active;

  const actEl = document.getElementById('stat-active-count');
  const disEl = document.getElementById('stat-disabled-count');
  if (actEl) actEl.innerText = active;
  if (disEl) disEl.innerText = disabled;
}

function filterFlagCategory(cat) {
  document.querySelectorAll('[id^="fcat-btn-"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const btn = document.getElementById('fcat-btn-' + cat);
  if (btn) {
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
  }

  const cards = document.querySelectorAll('.flag-item-card');
  cards.forEach(c => {
    const cardCat = c.getAttribute('data-category');
    if (cat === 'all' || cardCat === cat) {
      c.style.display = 'flex';
    } else {
      c.style.display = 'none';
    }
  });
}

function searchFlags() {
  const query = (document.getElementById('flagSearchInput').value || '').toLowerCase().trim();
  const cards = document.querySelectorAll('.flag-item-card');
  cards.forEach(c => {
    const searchData = c.getAttribute('data-search') || '';
    if (!query || searchData.includes(query)) {
      c.style.display = 'flex';
    } else {
      c.style.display = 'none';
    }
  });
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
