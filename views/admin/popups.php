<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<!-- Topbar -->
<div class="admin-topbar">
  <div>
    <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <span class="ts-badge ts-badge-pink">ENGAGEMENT &bull; TARGETED CONVERSIONS</span>
      <span style="font-size: 0.8rem; color: var(--admin-text-secondary);">Dynamic Exit-Intent Modals, Lead Magnets &amp; Broadcast Overlays</span>
    </div>
    <h1 style="font-size: 1.6rem; color: var(--admin-text-primary); margin: 0; font-family: var(--admin-font-heading); font-weight: 800;">
      Custom Popup &amp; Conversion Studio
    </h1>
  </div>

  <div style="display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center;">
    <button type="button" onclick="openNewPopupModal()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
      <i data-lucide="plus"></i> Create New Popup 🚀
    </button>
  </div>
</div>

<!-- Popups Roster Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
  <?php if (!empty($popups)): ?>
    <?php foreach ($popups as $p): 
      $views = (int)($p['views_count'] ?? 0);
      $clicks = (int)($p['clicks_count'] ?? 0);
      $ctr = ($views > 0) ? round(($clicks / $views) * 100, 1) : 0;
    ?>
      <div class="admin-card" style="padding: 1.5rem; border-radius: 14px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg); display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 20px rgba(0,0,0,0.12); transition: transform 0.2s ease, border-color 0.2s ease;">
        <div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.65rem; gap: 0.5rem; flex-wrap: wrap;">
            <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem; font-weight: 800; letter-spacing: 0.5px;">
              <?= htmlspecialchars($p['badge_text'] ?? 'ANNOUNCEMENT') ?>
            </span>
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; align-items: center;">
              <span class="ts-badge <?= !empty($p['is_active']) ? 'ts-badge-green' : 'ts-badge-pink' ?>" style="font-size: 0.68rem; font-weight: 800;">
                <?= !empty($p['is_active']) ? 'ACTIVE' : 'PAUSED' ?>
              </span>
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;">
                CTR: <?= $ctr ?>%
              </span>
            </div>
          </div>

          <h3 style="font-size: 1.15rem; color: var(--admin-text-primary); margin: 0 0 0.35rem 0; font-weight: 800; font-family: var(--admin-font-heading);">
            <?= htmlspecialchars($p['title']) ?>
          </h3>

          <h4 style="font-size: 0.92rem; color: var(--admin-text-secondary); margin: 0 0 0.85rem 0; font-weight: 600; line-height: 1.4;">
            <?= htmlspecialchars($p['headline']) ?>
          </h4>

          <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 1rem;">
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">
              Audience: <?= strtoupper(htmlspecialchars($p['target_audience'])) ?>
            </span>
            <span class="ts-badge ts-badge-amber" style="font-size: 0.7rem;">
              Trigger: <?= strtoupper(htmlspecialchars($p['display_trigger'])) ?> (<?= (int)$p['trigger_delay_seconds'] ?>s)
            </span>
            <span class="ts-badge" style="font-size: 0.7rem; background: rgba(99,102,241,0.15); color: #818cf8; font-weight: 600;">
              Freq: <?= htmlspecialchars($p['frequency']) ?>
            </span>
            <span class="ts-badge" style="font-size: 0.7rem; background: rgba(16,185,129,0.15); color: #10b981; font-weight: 600;">
              Theme: <?= htmlspecialchars($p['popup_theme']) ?>
            </span>
          </div>

          <div style="background: var(--admin-card-bg-elevated); border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 0.65rem 0.85rem; font-size: 0.78rem; color: var(--admin-text-secondary); margin-bottom: 1rem;">
            CTA: <strong style="color: #38bdf8;"><?= htmlspecialchars($p['cta_button_text']) ?></strong> &rarr; <span style="font-family: var(--admin-font-mono); word-break: break-all;"><?= htmlspecialchars($p['cta_button_url']) ?></span>
          </div>

          <div style="display: flex; justify-content: space-between; font-size: 0.76rem; color: var(--admin-text-secondary); margin-bottom: 0.75rem; border-top: 1px solid var(--admin-border-subtle); padding-top: 0.6rem;">
            <span>Impressions: <strong style="color: var(--admin-text-primary);"><?= number_format($views) ?></strong></span>
            <span>Clicks: <strong style="color: #10b981;"><?= number_format($clicks) ?></strong></span>
          </div>
        </div>

        <div style="display: flex; gap: 0.4rem; border-top: 1px solid var(--admin-border-main); padding-top: 0.85rem; flex-wrap: wrap;">
          <button type="button" onclick='previewPopup(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="admin-btn admin-btn-outline admin-btn-sm" style="flex: 1; font-size: 0.76rem; font-weight: 700;">
            <i data-lucide="eye"></i> Live Preview
          </button>
          <button type="button" onclick='editPopup(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="admin-btn admin-btn-outline admin-btn-sm" style="color: #38bdf8; font-size: 0.76rem;" title="Edit Popup">
            <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i> Edit
          </button>
          <form action="<?= url('/admin/popups/toggle/' . $p['id']) ?>" method="POST" style="margin: 0;">
            <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" title="Toggle Active Status">
              <i data-lucide="<?= !empty($p['is_active']) ? 'pause' : 'play' ?>" style="width: 13px; height: 13px;"></i>
            </button>
          </form>
          <form action="<?= url('/admin/popups/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Permanently remove this popup?');" style="margin: 0;">
            <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444;" title="Delete Popup">
              <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
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

<!-- Modal: Create / Edit Popup with Split-Pane Live Simulator -->
<div id="popupModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closePopupModal()"></div>
  <div class="admin-modal-box modal-indigo" style="max-width: 960px; max-height: 90vh; display: flex; flex-direction: column; padding: 0; overflow: hidden;">
    <div class="admin-modal-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-main); display: flex; justify-content: space-between; align-items: center;">
      <h3 id="popupModalTitle" style="margin: 0; color: var(--admin-text-primary); font-size: 1.2rem; font-family: var(--admin-font-heading); font-weight: 800;">Create Custom Popup</h3>
      <button type="button" onclick="closePopupModal()" class="admin-modal-close" style="background: none; border: none; font-size: 1.5rem; color: var(--admin-text-secondary); cursor: pointer;">&times;</button>
    </div>

    <div style="display: grid; grid-template-columns: 1.2fr 1fr; flex: 1; overflow: hidden;" id="popupEditorLayout">
      <!-- Left: Configuration Form -->
      <div style="padding: 1.5rem; overflow-y: auto;">
        <form id="popupForm" action="<?= url('/admin/popups/save') ?>" method="POST">
          <input type="hidden" name="id" id="popup_id" value="0">

          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
              <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Internal Title</label>
              <input type="text" name="title" id="popup_title" placeholder="e.g. Black Friday Special" class="admin-input" required oninput="updateLivePreviewFromInputs()">
            </div>
            <div>
              <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Badge Pill Text</label>
              <input type="text" name="badge_text" id="popup_badge" value="FEATURED ANNOUNCEMENT" class="admin-input" required oninput="updateLivePreviewFromInputs()">
            </div>
          </div>

          <div style="margin-bottom: 1rem;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Headline</label>
            <input type="text" name="headline" id="popup_headline" placeholder="e.g. Unlock Pro AI Benchmarks & Verified Checkpoints" class="admin-input" required oninput="updateLivePreviewFromInputs()">
          </div>

          <div style="margin-bottom: 1rem;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Message Content (HTML Allowed)</label>
            <textarea name="content_html" id="popup_content" rows="3" class="admin-input" style="font-size: 0.85rem;" required placeholder="Get unlimited access to our prompt blueprints, benchmark scoring radars, and daily intelligence digests." oninput="updateLivePreviewFromInputs()"></textarea>
          </div>

          <!-- Action URLs -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
              <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Primary CTA Button Text</label>
              <input type="text" name="cta_button_text" id="popup_cta_text" value="Explore Platform ↗" class="admin-input" required oninput="updateLivePreviewFromInputs()">
            </div>
            <div>
              <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Primary CTA URL</label>
              <input type="text" name="cta_button_url" id="popup_cta_url" value="/tools" class="admin-input" required placeholder="https://... or /tools" oninput="updateLivePreviewFromInputs()">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
              <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Secondary Button Text (Optional)</label>
              <input type="text" name="secondary_button_text" id="popup_sec_text" placeholder="e.g. Maybe Later" class="admin-input" oninput="updateLivePreviewFromInputs()">
            </div>
            <div>
              <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Secondary URL (Optional)</label>
              <input type="text" name="secondary_button_url" id="popup_sec_url" placeholder="Optional dismiss or link" class="admin-input">
            </div>
          </div>

          <!-- Targeting & Triggers -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
              <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Target Audience</label>
              <select name="target_audience" id="popup_audience" class="admin-input" onchange="updateLivePreviewFromInputs()">
                <option value="all">Everyone (All Visitors)</option>
                <option value="guests_only">Guests / Unregistered Visitors Only</option>
                <option value="logged_in_only">Registered Members Only</option>
                <option value="donors_only">Donors &amp; VIPs Only</option>
              </select>
            </div>
            <div>
              <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Display Trigger</label>
              <select name="display_trigger" id="popup_trigger" class="admin-input" onchange="updateLivePreviewFromInputs()">
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
              <select name="popup_theme" id="popup_theme" class="admin-input" onchange="updateLivePreviewFromInputs()">
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

          <div style="flex-wrap: wrap; align-items: center; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--admin-border-main); padding-top: 1rem;">
            <button type="button" onclick="closePopupModal()" class="admin-btn admin-btn-outline">Cancel</button>
            <button type="submit" class="admin-btn admin-btn-primary" style="font-weight: 700;">Save Popup Settings 🚀</button>
          </div>
        </form>
      </div>

      <!-- Right: Real-Time Live Preview Studio Pane -->
      <div style="background: var(--admin-card-bg-elevated); border-left: 1px solid var(--admin-border-main); padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <div style="flex-wrap: wrap; align-items: center; font-size: 0.78rem; font-weight: 800; color: var(--admin-text-secondary); text-transform: uppercase; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="monitor" style="width: 14px; height: 14px; color: #38bdf8;"></i> Live Modal Preview (WYSIWYG)
        </div>

        <div id="editorLiveCard" style="width: 100%; max-width: 380px; border-radius: 14px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.5); transition: all 0.3s ease;">
          <div style="margin-bottom: 0.65rem;">
            <span id="prevBadge" class="ts-badge ts-badge-pink" style="font-size: 0.68rem; font-weight: 800;">FEATURED</span>
          </div>
          <h3 id="prevHeadline" style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.5rem 0; line-height: 1.3;">Headline</h3>
          <div id="prevBody" style="font-size: 0.85rem; line-height: 1.5; margin-bottom: 1.25rem; opacity: 0.9;">Message body goes here...</div>
          <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button type="button" id="prevCtaBtn" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">Explore Platform ↗</button>
            <button type="button" id="prevSecBtn" class="admin-btn admin-btn-outline admin-btn-sm" style="display: none;">Maybe Later</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Standalone Live Interactive Popup Simulator Modal -->
<div id="popupSimulatorModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closePopupSimulator()"></div>
  <div class="admin-modal-box modal-cyan" style="max-width: 540px; padding: 0; background: transparent; border: none; box-shadow: none;">
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
// Global Escape Key Listener
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closePopupModal();
    closePopupSimulator();
  }
});

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
  updateLivePreviewFromInputs();
  document.body.classList.add('ts-modal-open');
  document.getElementById('popupModal').style.display = 'flex';
}

function closePopupModal() {
  document.getElementById('popupModal').style.display = 'none';
  document.body.classList.remove('ts-modal-open');
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
  updateLivePreviewFromInputs();
  document.body.classList.add('ts-modal-open');
  document.getElementById('popupModal').style.display = 'flex';
}

function updateLivePreviewFromInputs() {
  const card = document.getElementById('editorLiveCard');
  const badge = document.getElementById('prevBadge');
  const headline = document.getElementById('prevHeadline');
  const body = document.getElementById('prevBody');
  const ctaBtn = document.getElementById('prevCtaBtn');
  const secBtn = document.getElementById('prevSecBtn');

  badge.textContent = document.getElementById('popup_badge').value || 'FEATURED';
  headline.textContent = document.getElementById('popup_headline').value || 'Your Headline Here';
  body.innerHTML = document.getElementById('popup_content').value || 'Your popup message content will be displayed here in full styling.';
  ctaBtn.textContent = document.getElementById('popup_cta_text').value || 'Explore ↗';

  const secVal = document.getElementById('popup_sec_text').value;
  if (secVal) {
    secBtn.style.display = 'inline-block';
    secBtn.textContent = secVal;
  } else {
    secBtn.style.display = 'none';
  }

  const theme = document.getElementById('popup_theme').value || 'modern_gradient';
  if (theme === 'dark_glass') {
    card.style.background = 'rgba(15, 23, 42, 0.95)';
    card.style.color = '#f8fafc';
    card.style.border = '1px solid rgba(56,189,248,0.4)';
  } else if (theme === 'cyber_amber') {
    card.style.background = '#0f172a';
    card.style.color = '#fef3c7';
    card.style.border = '2px solid #f59e0b';
  } else if (theme === 'minimal_card') {
    card.style.background = 'var(--admin-card-bg)';
    card.style.color = 'var(--admin-text-primary)';
    card.style.border = '1px solid var(--admin-border-main)';
  } else {
    card.style.background = 'linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%)';
    card.style.color = '#ffffff';
    card.style.border = '1px solid rgba(99,102,241,0.4)';
  }
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

  document.body.classList.add('ts-modal-open');
  document.getElementById('popupSimulatorModal').style.display = 'flex';
}

function closePopupSimulator() {
  document.getElementById('popupSimulatorModal').style.display = 'none';
  document.body.classList.remove('ts-modal-open');
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
