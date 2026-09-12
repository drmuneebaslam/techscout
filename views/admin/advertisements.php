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

  <!-- Sub-Tabs Navigation Strip -->
  <div style="display: flex; gap: 0.5rem; border-bottom: 1px solid rgba(148,163,184,0.15); margin-bottom: 1.5rem; overflow-x: auto;">
    <a href="<?= url('/admin/advertisements?tab=ads') ?>" class="ts-subtab-link <?= (($activeTab ?? 'ads') === 'ads') ? 'active' : '' ?>">
      📢 Display Banners &amp; Ad Slots (<?= count($ads ?? []) ?>)
    </a>
    <a href="<?= url('/admin/advertisements?tab=sponsored') ?>" class="ts-subtab-link <?= (($activeTab ?? '') === 'sponsored') ? 'active' : '' ?>">
      🪙 Sponsored Submissions &amp; Crypto Reviews (<?= count($sponsoredSubmissions ?? []) ?>)
      <?php 
        $pendingCount = count(array_filter($sponsoredSubmissions ?? [], fn($s) => ($s['status'] ?? '') === 'pending_review'));
      ?>
      <?php if ($pendingCount > 0): ?>
        <span class="ts-badge ts-badge-gold" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;"><?= $pendingCount ?> PENDING</span>
      <?php endif; ?>
    </a>
  </div>

  <?php if (($activeTab ?? 'ads') === 'ads'): ?>
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
                <td style="font-family: var(--admin-font-mono); color: #94a3b8;"><?= number_format((int)$ad['impression_count']) ?></td>
                <td style="font-family: var(--admin-font-mono); color: #94a3b8;"><?= number_format((int)$ad['click_count']) ?></td>
                <td style="font-family: var(--admin-font-mono); color: #38bdf8;">
                  <?= $ad['impression_count'] > 0 ? round(($ad['click_count'] / $ad['impression_count']) * 100, 2) : 0 ?>%
                </td>
                <td>
                  <span class="ts-badge <?= $ad['status'] === 'active' ? 'ts-badge-green' : 'ts-badge-gray' ?>">
                    <?= ucfirst(htmlspecialchars($ad['status'])) ?>
                  </span>
                </td>
                <td>
                  <div style="display: flex; gap: 0.5rem;">
                    <button onclick="editAd(<?= htmlspecialchars(json_encode($ad), ENT_QUOTES, 'UTF-8') ?>)" class="ts-btn ts-btn-outline ts-btn-sm">Edit</button>
                    <form action="<?= url('/admin/advertisements/delete/' . $ad['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this ad campaign?');" style="margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">Del</button>
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

  <!-- TAB 2: SPONSORED SUBMISSIONS & CRYPTO REVIEWS -->
  <?php else: ?>
    <div class="admin-table-wrap" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: var(--admin-text-main); margin: 0 0 0.25rem 0;">Sponsored Tools &amp; Posts Verification Queue</h3>
          <p style="color: var(--admin-text-muted); font-size: 0.82rem; margin: 0;">Verify advertiser crypto transaction hashes (TXIDs) on-chain and approve placements.</p>
        </div>
        <a href="<?= url('/sponsor') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
          View Public Sponsor Page ↗
        </a>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Type &amp; Title</th>
            <th>Sponsor Brand &amp; Contact</th>
            <th>Amount &amp; Crypto TXID</th>
            <th>Submitted At</th>
            <th>Status</th>
            <th style="width: 180px; text-align: right;">Review Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($sponsoredSubmissions)): ?>
            <?php foreach ($sponsoredSubmissions as $sub): ?>
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem;">
                    <span class="ts-badge <?= $sub['type'] === 'tool' ? 'ts-badge-cyan' : 'ts-badge-gold' ?>" style="font-size: 0.65rem; font-weight: 800;">
                      <?= strtoupper(htmlspecialchars($sub['type'])) ?>
                    </span>
                    <strong style="color: var(--admin-text-main); font-size: 0.92rem;"><?= htmlspecialchars($sub['title']) ?></strong>
                  </div>
                  <?php if (!empty($sub['target_url'])): ?>
                    <a href="<?= htmlspecialchars($sub['target_url']) ?>" target="_blank" style="color: #38bdf8; font-size: 0.78rem; text-decoration: none;">
                      <?= htmlspecialchars(substr($sub['target_url'], 0, 40)) ?>... ↗
                    </a>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-weight: 600; color: var(--admin-text-main); font-size: 0.85rem;"><?= htmlspecialchars($sub['author_name']) ?></div>
                  <div style="font-size: 0.78rem; color: var(--admin-text-muted);"><?= htmlspecialchars($sub['author_email']) ?></div>
                </td>
                <td>
                  <div style="font-weight: 800; color: #10b981; font-family: var(--admin-font-mono); font-size: 0.9rem;">
                    $<?= number_format((float)$sub['pricing_amount_usd'], 2) ?> USD (<?= htmlspecialchars($sub['crypto_currency']) ?>)
                  </div>
                  <div style="display: flex; align-items: center; gap: 0.35rem; margin-top: 0.2rem;">
                    <code style="font-size: 0.72rem; color: #38bdf8; background: rgba(56,189,248,0.08); padding: 0.15rem 0.4rem; border-radius: 4px; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($sub['crypto_txid']) ?>">
                      <?= htmlspecialchars($sub['crypto_txid']) ?>
                    </code>
                    <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($sub['crypto_txid'], ENT_QUOTES) ?>'); alert('TXID copied to clipboard!');" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">Copy</button>
                  </div>
                </td>
                <td>
                  <?= \App\Core\DateHelper::render($sub['created_at'], 'M j, Y • g:i A', 'datetime') ?>
                </td>
                <td>
                  <span class="ts-badge <?= $sub['status'] === 'approved' ? 'ts-badge-green' : ($sub['status'] === 'rejected' ? 'ts-badge-red' : 'ts-badge-gold') ?>">
                    <?= ucfirst(str_replace('_', ' ', htmlspecialchars($sub['status']))) ?>
                  </span>
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.4rem; justify-content: flex-end;">
                    <?php if ($sub['status'] !== 'approved'): ?>
                      <form action="<?= url('/admin/sponsored/approve/' . $sub['id']) ?>" method="POST" style="margin: 0;">
                        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.3); font-size: 0.72rem; padding: 0.2rem 0.5rem;" title="Approve &amp; Activate Placement">
                          Approve ✓
                        </button>
                      </form>
                    <?php endif; ?>
                    <?php if ($sub['status'] !== 'rejected'): ?>
                      <form action="<?= url('/admin/sponsored/reject/' . $sub['id']) ?>" method="POST" style="margin: 0;">
                        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 0.72rem; padding: 0.2rem 0.5rem;" title="Mark as Rejected">
                          Reject ✗
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align: center; padding: 3rem; color: var(--admin-text-muted);">No sponsored submissions received yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

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
