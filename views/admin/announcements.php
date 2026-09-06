<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-indigo">SITE BROADCAST &bull; MARQUEE RIBBONS</span>
        <span style="font-size: 0.8rem; color: #64748b;">Global Alert Banners, Flash Promotions &amp; Maintenance Notices</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Site Announcements &amp; Broadcast Ribbons
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="openNewAnnouncementModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #a855f7); font-weight: 700;">
        <i data-lucide="megaphone"></i> Create Announcement Banner 📢
      </button>
    </div>
  </div>

  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0;">Published Announcement Banners</h3>
      
      <div style="display: flex; gap: 0.5rem;">
        <button type="button" onclick="filterAnnouncements('all')" class="ts-btn ts-btn-sm ts-btn-primary ann-filter-btn" id="afbtn-all">All (<?= count($announcements) ?>)</button>
        <button type="button" onclick="filterAnnouncements('active')" class="ts-btn ts-btn-sm ts-btn-outline ann-filter-btn" id="afbtn-active">Active</button>
        <button type="button" onclick="filterAnnouncements('inactive')" class="ts-btn ts-btn-sm ts-btn-outline ann-filter-btn" id="afbtn-inactive">Paused</button>
      </div>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Title &amp; Banner Preview</th>
          <th>Ribbon Type</th>
          <th>Audience Target</th>
          <th>Action Link</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($announcements)): ?>
          <?php foreach ($announcements as $ann): ?>
            <tr class="ann-row" data-status="<?= !empty($ann['is_active']) ? 'active' : 'inactive' ?>">
              <td style="max-width: 340px;">
                <strong style="color: #f8fafc; font-size: 0.95rem; display: block; margin-bottom: 0.35rem;"><?= htmlspecialchars($ann['title']) ?></strong>
                <!-- Live Banner Simulation Capsule -->
                <div style="background: <?= htmlspecialchars($ann['banner_color']) ?>; color: <?= htmlspecialchars($ann['text_color']) ?>; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.78rem; font-weight: 600; display: inline-block;">
                  <?= htmlspecialchars($ann['message']) ?>
                  <?php if (!empty($ann['action_label'])): ?>
                    <span style="text-decoration: underline; margin-left: 6px;"><?= htmlspecialchars($ann['action_label']) ?> &rarr;</span>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; text-transform: uppercase;">
                  <?= htmlspecialchars($ann['ribbon_type']) ?>
                </span>
              </td>
              <td>
                <span class="ts-badge" style="background: rgba(168,85,247,0.2); color: #c084fc; font-size: 0.7rem; text-transform: uppercase;">
                  <?= htmlspecialchars($ann['target_audience']) ?>
                </span>
              </td>
              <td>
                <?php if (!empty($ann['action_url'])): ?>
                  <a href="<?= htmlspecialchars($ann['action_url']) ?>" target="_blank" style="color: #38bdf8; font-size: 0.8rem;">
                    <?= htmlspecialchars($ann['action_label'] ?: 'Visit Link') ?> ↗
                  </a>
                <?php else: ?>
                  <span style="color: #64748b; font-size: 0.8rem;">None</span>
                <?php endif; ?>
              </td>
              <td>
                <form action="<?= url('/admin/announcements/toggle/' . $ann['id']) ?>" method="POST" style="display: inline;">
                  <button type="submit" class="ts-btn ts-btn-sm <?= !empty($ann['is_active']) ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.72rem; padding: 0.2rem 0.6rem; <?= !empty($ann['is_active']) ? 'background: #10b981; border-color: #10b981;' : 'color: #94a3b8;' ?>" title="Click to Toggle">
                    <?= !empty($ann['is_active']) ? '✓ ACTIVE' : '⏸ PAUSED' ?>
                  </button>
                </form>
              </td>
              <td>
                <div style="display: flex; gap: 0.35rem;">
                  <button type="button" onclick='editAnnouncement(<?= json_encode($ann) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #38bdf8;">
                    Edit ✏️
                  </button>
                  <form action="<?= url('/admin/announcements/delete/' . $ann['id']) ?>" method="POST" onsubmit="return confirm('Remove this announcement banner?');" style="display: inline;">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #ef4444;">
                      ✕
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">No announcement banners configured yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Create / Edit Announcement -->
<div id="annModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-indigo" style="max-width: 580px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #818cf8;" id="modalTitle"><i data-lucide="megaphone"></i> Create Global Announcement Ribbon</h3>
      <button type="button" onclick="closeAnnModal()" class="admin-modal-close">✕</button>
    </div>

    <form action="<?= url('/admin/announcements/save') ?>" method="POST" id="annForm">
      <input type="hidden" name="id" id="ann_id" value="">

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Banner Internal Title</label>
        <input type="text" name="title" id="ann_title" class="admin-modal-input" placeholder="e.g. AI Blueprint Release 2026" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Marquee Broadcast Message (Display text)</label>
        <textarea name="message" id="ann_message" rows="2" class="admin-modal-textarea" placeholder="e.g. 🚀 TechScout v2.0 is live! Explore 200+ frontier AI tool blueprints." required></textarea>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Ribbon Type</label>
          <select name="ribbon_type" id="ann_type" class="admin-modal-select">
            <option value="announcement">📢 Announcement</option>
            <option value="promo">⚡ Promo &amp; Discount</option>
            <option value="warning">⚠️ Warning / Alert</option>
            <option value="maintenance">🔧 Maintenance Notice</option>
            <option value="info">ℹ️ General Info</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Target Audience</label>
          <select name="target_audience" id="ann_audience" class="admin-modal-select">
            <option value="all">🌐 All Users (Public &amp; Members)</option>
            <option value="guests">👤 Unauthenticated Guests</option>
            <option value="members">🛡️ Registered Members Only</option>
            <option value="authors">✍️ Authors &amp; Creators Only</option>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Banner Background Color</label>
          <input type="color" name="banner_color" id="ann_banner_color" value="#6366f1" style="width: 100%; height: 38px; border-radius: 6px; border: 1px solid rgba(148,163,184,0.2); background: #0b0f19; cursor: pointer;">
        </div>
        <div>
          <label class="admin-modal-label">Banner Text Color</label>
          <input type="color" name="text_color" id="ann_text_color" value="#ffffff" style="width: 100%; height: 38px; border-radius: 6px; border: 1px solid rgba(148,163,184,0.2); background: #0b0f19; cursor: pointer;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">CTA Button Label</label>
          <input type="text" name="action_label" id="ann_action_label" class="admin-modal-input" placeholder="e.g. Discover Now">
        </div>
        <div>
          <label class="admin-modal-label">CTA Destination URL</label>
          <input type="url" name="action_url" id="ann_action_url" class="admin-modal-input" placeholder="https://techscout.n/topics">
        </div>
      </div>

      <div style="display: flex; gap: 1.5rem; margin-bottom: 1.25rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; font-size: 0.85rem; cursor: pointer;">
          <input type="checkbox" name="is_dismissible" id="ann_dismissible" value="1" checked>
          Allow readers to dismiss banner
        </label>
        <label style="display: flex; align-items: center; gap: 0.5rem; color: #10b981; font-size: 0.85rem; cursor: pointer; font-weight: 600;">
          <input type="checkbox" name="is_active" id="ann_active" value="1" checked>
          Activate immediately
        </label>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeAnnModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #6366f1; border-color: #6366f1; font-weight: 700;" id="saveBtn">Publish Ribbon 🚀</button>
      </div>
    </form>
  </div>
</div>

<script>
function openNewAnnouncementModal() {
  document.getElementById('ann_id').value = '';
  document.getElementById('ann_title').value = '';
  document.getElementById('ann_message').value = '';
  document.getElementById('ann_type').value = 'announcement';
  document.getElementById('ann_audience').value = 'all';
  document.getElementById('ann_banner_color').value = '#6366f1';
  document.getElementById('ann_text_color').value = '#ffffff';
  document.getElementById('ann_action_label').value = '';
  document.getElementById('ann_action_url').value = '';
  document.getElementById('ann_dismissible').checked = true;
  document.getElementById('ann_active').checked = true;
  document.getElementById('modalTitle').innerHTML = '<i data-lucide="megaphone"></i> Create Global Announcement Ribbon';
  document.getElementById('saveBtn').innerText = 'Publish Ribbon 🚀';
  document.getElementById('annModal').style.display = 'flex';
  if (window.lucide) lucide.createIcons();
}

function editAnnouncement(ann) {
  document.getElementById('ann_id').value = ann.id;
  document.getElementById('ann_title').value = ann.title;
  document.getElementById('ann_message').value = ann.message;
  document.getElementById('ann_type').value = ann.ribbon_type || 'announcement';
  document.getElementById('ann_audience').value = ann.target_audience || 'all';
  document.getElementById('ann_banner_color').value = ann.banner_color || '#6366f1';
  document.getElementById('ann_text_color').value = ann.text_color || '#ffffff';
  document.getElementById('ann_action_label').value = ann.action_label || '';
  document.getElementById('ann_action_url').value = ann.action_url || '';
  document.getElementById('ann_dismissible').checked = ann.is_dismissible == 1;
  document.getElementById('ann_active').checked = ann.is_active == 1;
  document.getElementById('modalTitle').innerHTML = '<i data-lucide="edit-3"></i> Edit Announcement Ribbon';
  document.getElementById('saveBtn').innerText = 'Update Ribbon 💾';
  document.getElementById('annModal').style.display = 'flex';
  if (window.lucide) lucide.createIcons();
}

function closeAnnModal() {
  document.getElementById('annModal').style.display = 'none';
}

function filterAnnouncements(status) {
  document.querySelectorAll('.ann-filter-btn').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('afbtn-' + status);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.ann-row').forEach(row => {
    const rowStatus = row.getAttribute('data-status');
    if (status === 'all' || rowStatus === status) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
