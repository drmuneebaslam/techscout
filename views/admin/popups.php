<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink">ENGAGEMENT &bull; TARGETED PROMOTIONS</span>
        <span style="font-size: 0.8rem; color: var(--admin-text-secondary);">Custom Dynamic Popup Maker &amp; Conversion Banners</span>
      </div>
      <h1 style="font-size: 1.6rem; color: var(--admin-text-primary); margin: 0; font-family: var(--admin-font-heading); font-weight: 800;">
        Custom Dynamic Popup Maker
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="openNewPopupModal()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
        <i data-lucide="plus"></i> Create New Popup 🚀
      </button>
    </div>
  </div>

  <!-- Popups Roster Grid -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <?php if (!empty($popups)): ?>
      <?php foreach ($popups as $p): ?>
        <div class="admin-card" style="padding: 1.5rem; border-radius: 14px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
              <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem; font-weight: 800;">
                <?= htmlspecialchars($p['badge_text'] ?? 'ANNOUNCEMENT') ?>
              </span>
              <span class="ts-badge <?= !empty($p['is_active']) ? 'ts-badge-green' : 'ts-badge-pink' ?>" style="font-size: 0.68rem;">
                <?= !empty($p['is_active']) ? 'ACTIVE' : 'PAUSED' ?>
              </span>
            </div>

            <h3 style="font-size: 1.15rem; color: var(--admin-text-primary); margin: 0 0 0.4rem 0; font-weight: 700;">
              <?= htmlspecialchars($p['title']) ?>
            </h3>

            <h4 style="font-size: 0.92rem; color: var(--admin-text-secondary); margin: 0 0 0.85rem 0; font-weight: 600;">
              <?= htmlspecialchars($p['headline']) ?>
            </h4>

            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 1rem;">
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">
                Audience: <?= strtoupper(htmlspecialchars($p['target_audience'])) ?>
              </span>
              <span class="ts-badge ts-badge-amber" style="font-size: 0.7rem;">
                Trigger: <?= strtoupper(htmlspecialchars($p['display_trigger'])) ?>
              </span>
              <span class="ts-badge" style="font-size: 0.7rem; background: rgba(99,102,241,0.15); color: #818cf8;">
                Freq: <?= htmlspecialchars($p['frequency']) ?>
              </span>
              <span class="ts-badge" style="font-size: 0.7rem; background: rgba(16,185,129,0.15); color: #10b981;">
                Theme: <?= htmlspecialchars($p['popup_theme']) ?>
              </span>
            </div>

            <div style="background: var(--admin-card-bg-elevated); border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; font-size: 0.78rem; color: var(--admin-text-secondary); margin-bottom: 1rem;">
              CTA: <strong style="color: #38bdf8;"><?= htmlspecialchars($p['cta_button_text']) ?></strong> &rarr; <span style="font-family: var(--admin-font-mono); word-break: break-all;"><?= htmlspecialchars($p['cta_button_url']) ?></span>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--admin-text-secondary); margin-bottom: 0.75rem;">
              <span>Impressions: <strong><?= number_format((int)($p['views_count'] ?? 0)) ?></strong></span>
              <span>Clicks: <strong><?= number_format((int)($p['clicks_count'] ?? 0)) ?></strong></span>
            </div>
          </div>

          <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--admin-border-main); padding-top: 0.85rem;">
            <button type="button" onclick='previewPopup(<?= json_encode($p) ?>)' class="admin-btn admin-btn-outline admin-btn-sm" style="flex: 1; font-size: 0.78rem;">
              <i data-lucide="eye"></i> Live Preview
            </button>
            <button type="button" onclick='editPopup(<?= json_encode($p) ?>)' class="admin-btn admin-btn-outline admin-btn-sm" style="color: #38bdf8; font-size: 0.78rem;">
              ✏️ Edit
            </button>
            <form action="<?= url('/admin/popups/toggle/' . $p['id']) ?>" method="POST" style="margin: 0;">
              <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" title="Toggle Active">
                <i data-lucide="<?= !empty($p['is_active']) ? 'pause' : 'play' ?>" style="width: 14px; height: 14px;"></i>
              </button>
            </form>
            <form action="<?= url('/admin/popups/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Permanently remove this popup?');" style="margin: 0;">
              <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444;" title="Delete">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="admin-card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
        <i data-lucide="bell" style="width: 44px; height: 44px; color: var(--admin-text-secondary); margin-bottom: 0.75rem;"></i>
        <h3 style="color: var(--admin-text-primary); margin: 0 0 0.5rem 0;">No Custom Popups Created</h3>
        <p style="color: var(--admin-text-secondary); font-size: 0.9rem; margin-bottom: 1.25rem;">Deploy high-converting modals and announcements targeted to guests, logged-in members, or supporters.</p>
        <button type="button" onclick="openNewPopupModal()" class="admin-btn admin-btn-primary">Create First Popup</button>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal: Create / Edit Popup -->
<div id="popupModal" class="admin-modal" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closePopupModal()"></div>
  <div class="admin-modal-content" style="max-width: 640px; max-height: 90vh; overflow-y: auto;">
    <div class="admin-modal-header">
      <h3 id="popupModalTitle" style="margin: 0; color: var(--admin-text-primary);">Create Custom Popup</h3>
      <button type="button" onclick="closePopupModal()" class="admin-modal-close">&times;</button>
    </div>
    <form id="popupForm" action="<?= url('/admin/popups/save') ?>" method="POST" style="padding: 1.25rem;">
      <input type="hidden" name="id" id="popup_id" value="0">

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Internal Title</label>
          <input type="text" name="title" id="popup_title" placeholder="e.g. Black Friday AI Directory Special" class="admin-input" required>
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Badge Pill Text</label>
          <input type="text" name="badge_text" id="popup_badge" value="FEATURED ANNOUNCEMENT" class="admin-input" required>
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Headline</label>
        <input type="text" name="headline" id="popup_headline" placeholder="e.g. Unlock Pro AI Benchmarks & Verified Checkpoints" class="admin-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Message Content (HTML Allowed)</label>
        <textarea name="content_html" id="popup_content" rows="4" class="admin-input" style="font-size: 0.85rem;" required placeholder="Get unlimited access to our prompt blueprints, benchmark scoring radars, and daily intelligence digests."></textarea>
      </div>

      <!-- Action URLs -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Primary CTA Button Text</label>
          <input type="text" name="cta_button_text" id="popup_cta_text" value="Explore Platform ↗" class="admin-input" required>
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Primary CTA URL (Internal or External)</label>
          <input type="text" name="cta_button_url" id="popup_cta_url" value="/tools" class="admin-input" required placeholder="https://... or /tools">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Secondary Button Text (Optional)</label>
          <input type="text" name="secondary_button_text" id="popup_sec_text" placeholder="e.g. Maybe Later" class="admin-input">
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Secondary URL (Optional)</label>
          <input type="text" name="secondary_button_url" id="popup_sec_url" placeholder="Optional dismiss or alternative link" class="admin-input">
        </div>
      </div>

      <!-- Targeting & Triggers -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Target Audience</label>
          <select name="target_audience" id="popup_audience" class="admin-input">
            <option value="all">Everyone (All Visitors)</option>
            <option value="guests_only">Guests / Unregistered Visitors Only</option>
            <option value="logged_in_only">Registered Members Only</option>
            <option value="donors_only">Donors &amp; VIPs Only</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Display Trigger</label>
          <select name="display_trigger" id="popup_trigger" class="admin-input">
            <option value="delay">Time Delay (Seconds)</option>
            <option value="immediate">Immediate on Page Load</option>
            <option value="scroll_depth">Scroll Depth (% of page)</option>
            <option value="exit_intent">Exit Intent (Mouse leaving window)</option>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Delay Seconds</label>
          <input type="number" name="trigger_delay_seconds" id="popup_delay" value="3" min="0" max="60" class="admin-input">
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Frequency</label>
          <select name="frequency" id="popup_freq" class="admin-input">
            <option value="once_per_session">Once Per Session</option>
            <option value="once_ever">Once Ever (Per Device)</option>
            <option value="always">Always (Every Page View)</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Visual Theme</label>
          <select name="popup_theme" id="popup_theme" class="admin-input">
            <option value="modern_gradient">Modern Indigo Gradient</option>
            <option value="minimal_card">Crisp Minimal Card</option>
            <option value="dark_glass">Sleek Obsidian Glass</option>
            <option value="cyber_amber">Cyber Amber Alert</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.88rem; font-weight: 700; color: var(--admin-text-primary);">
          <input type="checkbox" name="is_active" id="popup_active" value="1" checked style="width: 18px; height: 18px; accent-color: #10b981;">
          <span>Enable this popup immediately (Active)</span>
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="closePopupModal()" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="font-weight: 700;">Save Popup Settings 🚀</button>
      </div>
    </form>
  </div>
</div>

<!-- Live Interactive Popup Simulator Modal -->
<div id="popupSimulatorModal" class="admin-modal" style="display: none; z-index: 99999;">
  <div class="admin-modal-backdrop" onclick="closePopupSimulator()"></div>
  <div class="admin-modal-content" style="max-width: 540px; padding: 0; background: transparent; border: none; box-shadow: none;">
    <div id="simPopupContainer" style="border-radius: 16px; padding: 2.25rem; position: relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.6); overflow: hidden;">
      <button type="button" onclick="closePopupSimulator()" style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.15); border: none; color: inherit; width: 32px; height: 32px; border-radius: 50%; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
      
      <div style="margin-bottom: 0.75rem;">
        <span id="simBadge" class="ts-badge ts-badge-pink" style="font-size: 0.72rem; font-weight: 800; letter-spacing: 0.5px;">FEATURED</span>
      </div>

      <h2 id="simHeadline" style="font-size: 1.45rem; font-weight: 800; margin: 0 0 0.75rem 0; line-height: 1.3;">Headline</h2>
      
      <div id="simBody" style="font-size: 0.92rem; line-height: 1.6; margin-bottom: 1.5rem; opacity: 0.92;">Message Body</div>

      <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <a id="simCtaBtn" href="#" target="_blank" class="admin-btn admin-btn-primary" style="padding: 0.75rem 1.75rem; font-weight: 700; border-radius: 10px; text-decoration: none;">Explore Platform ↗</a>
        <button type="button" id="simSecBtn" onclick="closePopupSimulator()" class="admin-btn admin-btn-outline" style="border-radius: 10px;">Maybe Later</button>
      </div>
      
      <div style="margin-top: 1.5rem; font-size: 0.72rem; opacity: 0.7; display: flex; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 0.75rem;">
        <span id="simTriggerInfo">Trigger: Delay (3s)</span>
        <span id="simAudienceInfo">Audience: Everyone</span>
      </div>
    </div>
  </div>
</div>

<script>
function openNewPopupModal() {
  document.getElementById('popup_id').value = '0';
  document.getElementById('popupModalTitle').textContent = 'Create Custom Popup';
  document.getElementById('popup_title').value = '';
  document.getElementById('popup_badge').value = 'FEATURED ANNOUNCEMENT';
  document.getElementById('popup_headline').value = '';
  document.getElementById('popup_content').value = '';
  document.getElementById('popup_cta_text').value = 'Explore Platform ↗';
  document.getElementById('popup_cta_url').value = '/tools';
  document.getElementById('popup_sec_text').value = '';
  document.getElementById('popup_sec_url').value = '';
  document.getElementById('popup_audience').value = 'all';
  document.getElementById('popup_trigger').value = 'delay';
  document.getElementById('popup_delay').value = '3';
  document.getElementById('popup_freq').value = 'once_per_session';
  document.getElementById('popup_theme').value = 'modern_gradient';
  document.getElementById('popup_active').checked = true;
  document.getElementById('popupModal').style.display = 'flex';
}

function closePopupModal() {
  document.getElementById('popupModal').style.display = 'none';
}

function editPopup(p) {
  document.getElementById('popup_id').value = p.id;
  document.getElementById('popupModalTitle').textContent = 'Edit Popup: ' + p.title;
  document.getElementById('popup_title').value = p.title || '';
  document.getElementById('popup_badge').value = p.badge_text || 'FEATURED ANNOUNCEMENT';
  document.getElementById('popup_headline').value = p.headline || '';
  document.getElementById('popup_content').value = p.content_html || '';
  document.getElementById('popup_cta_text').value = p.cta_button_text || 'Explore Platform ↗';
  document.getElementById('popup_cta_url').value = p.cta_button_url || '/tools';
  document.getElementById('popup_sec_text').value = p.secondary_button_text || '';
  document.getElementById('popup_sec_url').value = p.secondary_button_url || '';
  document.getElementById('popup_audience').value = p.target_audience || 'all';
  document.getElementById('popup_trigger').value = p.display_trigger || 'delay';
  document.getElementById('popup_delay').value = p.trigger_delay_seconds || '3';
  document.getElementById('popup_freq').value = p.frequency || 'once_per_session';
  document.getElementById('popup_theme').value = p.popup_theme || 'modern_gradient';
  document.getElementById('popup_active').checked = (parseInt(p.is_active, 10) === 1);
  document.getElementById('popupModal').style.display = 'flex';
}

function previewPopup(p) {
  const container = document.getElementById('simPopupContainer');
  const badge = document.getElementById('simBadge');
  const headline = document.getElementById('simHeadline');
  const body = document.getElementById('simBody');
  const ctaBtn = document.getElementById('simCtaBtn');
  const secBtn = document.getElementById('simSecBtn');
  const triggerInfo = document.getElementById('simTriggerInfo');
  const audienceInfo = document.getElementById('simAudienceInfo');

  badge.textContent = p.badge_text || 'ANNOUNCEMENT';
  headline.textContent = p.headline || 'Featured Update';
  body.innerHTML = p.content_html || '';
  ctaBtn.textContent = p.cta_button_text || 'Learn More ↗';
  ctaBtn.href = p.cta_button_url || '#';

  if (p.secondary_button_text) {
    secBtn.style.display = 'inline-block';
    secBtn.textContent = p.secondary_button_text;
  } else {
    secBtn.style.display = 'none';
  }

  triggerInfo.textContent = 'Trigger: ' + (p.display_trigger || 'delay') + ' (' + (p.trigger_delay_seconds || 3) + 's)';
  audienceInfo.textContent = 'Target: ' + (p.target_audience || 'all');

  const theme = p.popup_theme || 'modern_gradient';
  if (theme === 'dark_glass') {
    container.style.background = 'rgba(15, 23, 42, 0.95)';
    container.style.color = '#f8fafc';
    container.style.border = '1px solid rgba(56,189,248,0.35)';
  } else if (theme === 'cyber_amber') {
    container.style.background = '#0f172a';
    container.style.color = '#fef3c7';
    container.style.border = '2px solid #f59e0b';
  } else if (theme === 'minimal_card') {
    container.style.background = 'var(--admin-card-bg)';
    container.style.color = 'var(--admin-text-primary)';
    container.style.border = '1px solid var(--admin-border-main)';
  } else {
    container.style.background = 'linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%)';
    container.style.color = '#ffffff';
    container.style.border = '1px solid rgba(99,102,241,0.4)';
  }

  document.getElementById('popupSimulatorModal').style.display = 'flex';
}

function closePopupSimulator() {
  document.getElementById('popupSimulatorModal').style.display = 'none';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
