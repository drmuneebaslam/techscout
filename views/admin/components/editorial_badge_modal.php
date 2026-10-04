<!-- ========================================================================= -->
<!-- MODAL: CUSTOM EDITORIAL BADGES PICKER & CREATOR (FEATURE 10) -->
<!-- ========================================================================= -->
<div id="assignBadgeModal" class="admin-modal-overlay" style="display: none; align-items: center; justify-content: center; z-index: 999999;">
  <div class="admin-modal-box modal-cyan" style="max-width: 540px; width: 95%; background: var(--ts-bg-surface, #090d16); border: 1px solid rgba(99,102,241,0.4); border-radius: 16px; padding: 2rem; box-shadow: 0 25px 70px rgba(0,0,0,0.85);">
    <div class="admin-modal-header" style="border-bottom: 1px solid var(--ts-border-subtle, rgba(148,163,184,0.12)); padding-bottom: 1rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
      <div>
        <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
          <span class="ts-badge ts-badge-cyan">EDITORIAL METADATA</span>
        </div>
        <h3 class="admin-modal-title" style="color: var(--ts-text-primary, #f8fafc); font-size: 1.25rem; font-weight: 800; margin: 0;">
          ⭐ Assign Custom Editorial Badge
        </h3>
      </div>
      <button type="button" onclick="closeBadgePickerModal()" class="admin-modal-close" style="font-size: 1.3rem;">✕</button>
    </div>

    <input type="hidden" id="badgeModalContentId" value="">

    <!-- Active Badges Roster -->
    <div style="margin-bottom: 1.5rem;">
      <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--ts-text-secondary); text-transform: uppercase; margin-bottom: 0.6rem;">
        Select from Preset &amp; Custom Badges:
      </label>
      <div id="badgeOptionsContainer" style="display: flex; flex-wrap: wrap; gap: 0.5rem; min-height: 50px;">
        <div style="color: var(--ts-text-tertiary); font-size: 0.85rem;">Loading badges...</div>
      </div>
    </div>

    <!-- Create Custom Badge Inline -->
    <div style="background: var(--ts-bg-surface-2, rgba(15,23,42,0.8)); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
      <div style="flex-wrap: wrap; align-items: center; font-size: 0.78rem; font-weight: 700; color: var(--ts-text-primary); text-transform: uppercase; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.35rem;">
        <i data-lucide="palette" style="width: 14px; height: 14px; color: #ec4899;"></i> Create New Custom Badge with Hex Colors
      </div>
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
        <div>
          <input type="text" id="newBadgeName" placeholder="e.g. Deep Dive, Breaking, Staff Pick" class="admin-modal-input" style="width: 100%; font-size: 0.85rem; padding: 0.45rem 0.75rem;" oninput="updateBadgePreview()">
        </div>
        <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
          <input type="color" id="newBadgeColor" value="#38bdf8" style="width: 38px; height: 34px; border: none; border-radius: 6px; cursor: pointer; background: transparent;" oninput="updateBadgePreview()" title="Badge Accent Color">
          <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); font-family: var(--ts-font-mono);" id="newBadgeColorHex">#38bdf8</span>
        </div>
      </div>
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div id="customBadgeLivePreview" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(56,189,248,0.15); color: #38bdf8; border: 1px solid rgba(56,189,248,0.35);">
          ⭐ Preview Badge
        </div>
        <button type="button" onclick="saveAndApplyCustomBadge()" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.8rem; background: #6366f1;">
          + Create &amp; Apply
        </button>
      </div>
    </div>

    <!-- Modal Footer Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--ts-border-subtle); padding-top: 1rem;">
      <button type="button" onclick="removeActiveBadge()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 0.78rem;">
        ✕ Remove Active Badge
      </button>
      <button type="button" onclick="closeBadgePickerModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.4rem 1rem;">
        Cancel
      </button>
    </div>
  </div>
</div>

<script>
let activeBadgePickerTargetId = 0;
let activeBadgePickerTargetTitle = '';
let cachedEditorialBadges = [];

function openBadgePickerModal(contentId, currentBadgeName) {
  activeBadgePickerTargetId = contentId;
  const modal = document.getElementById('assignBadgeModal');
  if (!modal) return;

  document.getElementById('badgeModalContentId').value = contentId;
  modal.style.display = 'flex';
  loadBadgesRoster(currentBadgeName);
  updateBadgePreview();
}

function closeBadgePickerModal() {
  const modal = document.getElementById('assignBadgeModal');
  if (modal) modal.style.display = 'none';
}

function loadBadgesRoster(currentBadgeName) {
  const container = document.getElementById('badgeOptionsContainer');
  fetch('<?= url('/admin/editorial-badges') ?>')
    .then(r => r.json())
    .then(data => {
      if (data && data.success && data.badges) {
        cachedEditorialBadges = data.badges;
        container.innerHTML = data.badges.map(b => {
          const isSelected = (currentBadgeName && currentBadgeName.toLowerCase() === b.name.toLowerCase());
          return `
            <button type="button" onclick="applyExistingBadge('${b.name.replace(/'/g, "\\'")}', '${b.color}', '${b.bg_color}')" 
              class="ts-badge" 
              style="cursor: pointer; padding: 0.4rem 0.85rem; font-size: 0.78rem; font-weight: 700; background: ${b.bg_color}; color: ${b.color}; border: 1px solid ${b.color}55; transition: transform 0.15s ease; ${isSelected ? 'outline: 2px solid #fff; box-shadow: 0 0 10px ' + b.color + ';' : ''}"
              onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"
              title="${b.description || b.name}">
              ⭐ ${b.name} ${isSelected ? '✓' : ''}
            </button>
          `;
        }).join('');
      }
    })
    .catch(() => {
      container.innerHTML = '<div style="color: #ef4444; font-size: 0.82rem;">Failed to load badges.</div>';
    });
}

function updateBadgePreview() {
  const name = document.getElementById('newBadgeName').value.trim() || 'Preview Badge';
  const color = document.getElementById('newBadgeColor').value;
  document.getElementById('newBadgeColorHex').innerText = color;
  
  const preview = document.getElementById('customBadgeLivePreview');
  preview.innerText = '⭐ ' + name;
  preview.style.color = color;
  preview.style.borderColor = color + '55';
  preview.style.background = hexToRgba(color, 0.15);
}

function hexToRgba(hex, alpha) {
  hex = hex.replace('#', '');
  if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
  const r = parseInt(hex.substring(0, 2), 16) || 0;
  const g = parseInt(hex.substring(2, 4), 16) || 0;
  const b = parseInt(hex.substring(4, 6), 16) || 0;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

function applyExistingBadge(badgeName, color, bgColor) {
  if (!activeBadgePickerTargetId) return;

  fetch('<?= url('/admin/content/assign-badge') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      content_id: activeBadgePickerTargetId,
      badge_name: badgeName,
      badge_color: color,
      badge_bg: bgColor
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data && data.success) {
      closeBadgePickerModal();
      if (typeof showToast === 'function') {
        showToast(data.message, 'success');
      }
      setTimeout(() => location.reload(), 300);
    } else {
      alert('Error assigning badge: ' + (data.error || 'Unknown error'));
    }
  });
}

function saveAndApplyCustomBadge() {
  const name = document.getElementById('newBadgeName').value.trim();
  if (!name) {
    alert('Please enter a badge name.');
    return;
  }
  const color = document.getElementById('newBadgeColor').value;
  const bgColor = hexToRgba(color, 0.15);

  fetch('<?= url('/admin/editorial-badges/create') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      name: name,
      color: color,
      bg_color: bgColor,
      description: 'Custom Editorial Badge: ' + name
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data && data.success) {
      applyExistingBadge(name, color, bgColor);
    } else {
      alert('Failed to create badge: ' + (data.error || 'Duplicate name or server error'));
    }
  });
}

function removeActiveBadge() {
  if (!activeBadgePickerTargetId) return;

  fetch('<?= url('/admin/content/assign-badge') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      content_id: activeBadgePickerTargetId,
      badge_name: '__REMOVE__'
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data && data.success) {
      closeBadgePickerModal();
      setTimeout(() => location.reload(), 300);
    }
  });
}

// =========================================================================
// FEATURE 4: QUICK CONTENT PINNING / STICKY POST TOGGLE DIRECT ACTION
// =========================================================================
function togglePinDirect(contentId, btnElem) {
  if (!contentId) return;

  fetch('<?= url('/admin/content/toggle-pin/') ?>' + contentId, {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data && data.success) {
      if (typeof showToast === 'function') {
        showToast(data.message, 'success');
      }
      setTimeout(() => location.reload(), 350);
    } else {
      alert('Error toggling pin state: ' + (data.error || 'Server error'));
    }
  })
  .catch(err => {
    console.error('Pin toggle error:', err);
    location.reload();
  });
}

// =========================================================================
// FEATURE 9: SAVE INTERNAL EDITORIAL SCRATCHPAD NOTE
// =========================================================================
function saveInternalNoteDirect(contentId, noteText, callback) {
  if (!contentId) return;

  fetch('<?= url('/admin/content/save-internal-note') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      content_id: contentId,
      internal_notes: noteText
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data && data.success) {
      if (typeof callback === 'function') callback(true, data.message);
    } else {
      if (typeof callback === 'function') callback(false, data.error || 'Error saving note');
    }
  })
  .catch(err => {
    if (typeof callback === 'function') callback(false, err.message);
  });
}
</script>
