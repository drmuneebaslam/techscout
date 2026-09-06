<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
  <div>
    <h1 style="font-size: 2rem; color: #f8fafc; margin: 0 0 0.25rem 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
      Ad Placements & Sponsorship Scheduler (CRUD)
    </h1>
    <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Manage display slots, track impressions & clicks, and review advertiser contracts.</p>
  </div>

  <button onclick="openNewAdModal()" class="ts-btn ts-btn-primary">
    <i data-lucide="plus-circle"></i> Create Ad Campaign
  </button>
</div>

<!-- Ad Modal -->
<div id="adFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 700px; padding: 2rem; background: #090d16; border: 1px solid rgba(16,185,129,0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h2 id="modalAdTitle" style="font-size: 1.35rem; color: #f8fafc; margin: 0;">Create Ad Placement</h2>
      <button onclick="document.getElementById('adFormModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form id="adminAdForm" action="<?= url('/admin/advertisements/save') ?>" method="POST">
      <input type="hidden" name="id" id="ad_id" value="">

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Campaign / Sponsor Name</label>
        <input type="text" name="title" id="ad_title" placeholder="e.g. AWS Cloud Credits Sponsorship" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Slot Location</label>
          <select name="slot_location" id="ad_slot_location" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
            <option value="header_banner">Header Banner (Top)</option>
            <option value="article_inline">Article Inline (Between sections)</option>
            <option value="sidebar_sticky">Sidebar Sticky (Right)</option>
            <option value="category_featured">Category Featured</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Contract Budget (USD)</label>
          <input type="number" step="0.01" name="cost_usd" id="ad_cost_usd" placeholder="500.00" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Destination Target URL</label>
          <input type="url" name="target_url" id="ad_target_url" placeholder="https://..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;" required>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Status</label>
          <select name="status" id="ad_status" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
            <option value="active">Active</option>
            <option value="paused">Paused</option>
            <option value="completed">Completed</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Banner Creative Image URL</label>
        <input type="url" name="image_url" id="ad_image_url" placeholder="https://..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('adFormModal').style.display='none'" class="ts-btn ts-btn-outline">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary">Save Campaign 🚀</button>
      </div>
    </form>
  </div>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Campaign & Slot</th>
        <th>Target URL</th>
        <th>Impressions</th>
        <th>Clicks</th>
        <th>CTR</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($ads)): ?>
        <?php foreach ($ads as $ad): ?>
          <tr>
            <td>
              <div style="font-weight: 600; color: #f8fafc; font-size: 0.95rem;"><?= htmlspecialchars($ad['title']) ?></div>
              <div style="font-size: 0.75rem; color: #818cf8; margin-top: 0.15rem;"><?= htmlspecialchars($ad['slot_location']) ?></div>
            </td>
            <td>
              <a href="<?= htmlspecialchars($ad['target_url']) ?>" target="_blank" style="color: #38bdf8; font-size: 0.85rem;">
                <?= htmlspecialchars(substr($ad['target_url'], 0, 35)) ?>... ↗
              </a>
            </td>
            <td><?= number_format($ad['impression_count']) ?></td>
            <td><?= number_format($ad['click_count']) ?></td>
            <td>
              <strong style="color: #34d399;">
                <?= $ad['impression_count'] > 0 ? number_format(($ad['click_count'] / $ad['impression_count']) * 100, 2) : '0.00' ?>%
              </strong>
            </td>
            <td>
              <form action="<?= url('/admin/advertisements/toggle/' . $ad['id']) ?>" method="POST" style="display:inline;">
                <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.6rem; border-radius: 9999px; <?= $ad['status'] === 'active' ? 'color: #34d399; border-color: rgba(16,185,129,0.4); background: rgba(16,185,129,0.1);' : 'color: #f87171; border-color: rgba(239,68,68,0.3); background: rgba(239,68,68,0.1);' ?>" title="Click to Toggle Active/Paused Status">
                  <?= $ad['status'] === 'active' ? '● ACTIVE (Running)' : '⏸ PAUSED (Disabled)' ?>
                </button>
              </form>
            </td>
            <td>
              <div style="display: flex; gap: 0.35rem;">
                <button onclick='editAd(<?= json_encode($ad, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #38bdf8;">
                  Edit
                </button>
                <form action="<?= url('/admin/advertisements/delete/' . $ad['id']) ?>" method="POST" onsubmit="return confirm('Permanently remove this ad placement?');">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #ef4444;">
                    Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">No active direct display campaigns configured.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
function openNewAdModal() {
  document.getElementById('adminAdForm').reset();
  document.getElementById('ad_id').value = '';
  document.getElementById('ad_slot_location').value = 'header_banner';
  document.getElementById('ad_status').value = 'active';
  document.getElementById('modalAdTitle').innerText = 'Create New Ad Placement';
  document.getElementById('adFormModal').style.display = 'flex';
}

function editAd(ad) {
  document.getElementById('adminAdForm').reset();
  document.getElementById('ad_id').value = ad.id;
  document.getElementById('ad_title').value = ad.title || '';
  document.getElementById('ad_slot_location').value = ad.slot_location || 'header_banner';
  document.getElementById('ad_cost_usd').value = ad.cost_usd || '';
  document.getElementById('ad_target_url').value = ad.target_url || '';
  document.getElementById('ad_image_url').value = ad.image_url || '';
  document.getElementById('ad_status').value = ad.status || 'active';
  document.getElementById('modalAdTitle').innerText = 'Edit Ad Campaign #' + ad.id;
  document.getElementById('adFormModal').style.display = 'flex';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
