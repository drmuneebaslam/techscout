<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

  <!-- Topbar Header -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink">LAYOUT ARCHITECTURE &bull; MULTI-TIER MENUS</span>
        <span style="font-size: 0.8rem; color: #64748b;">Header Menus, Mega Menus, Dropdown Trees, Footer Columns &amp; External Social Links</span>
      </div>
      <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        CMS Navigation Management
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        <i data-lucide="external-link"></i> Preview Live Navigation ↗
      </a>
      <button type="button" onclick="openNewNavModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #ec4899); font-weight: 700;">
        <i data-lucide="plus-circle"></i> Add Navigation Link ⚡
      </button>
    </div>
  </div>

  <!-- Navigation Locations Ribbon (7 Modules) -->
  <div class="admin-table-wrap" style="padding: 0.5rem; margin-bottom: 1.5rem; overflow-x: auto;">
    <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; min-width: max-content;">
      <?php
      $navTabs = [
        'main' => ['label' => 'Main Navigation', 'icon' => 'menu', 'badge' => count($groupedNav['main'])],
        'secondary' => ['label' => 'Secondary Navigation', 'icon' => 'more-horizontal', 'badge' => count($groupedNav['secondary'])],
        'footer' => ['label' => 'Footer Navigation', 'icon' => 'columns-3', 'badge' => count($groupedNav['footer'])],
        'mobile' => ['label' => 'Mobile Navigation', 'icon' => 'smartphone', 'badge' => count($groupedNav['mobile'])],
        'dropdown' => ['label' => 'Dropdowns', 'icon' => 'chevron-down', 'badge' => count($groupedNav['dropdown'])],
        'mega_menu' => ['label' => 'Mega Menus', 'icon' => 'layout-grid', 'badge' => count($groupedNav['mega_menu'])],
        'external' => ['label' => 'External Links', 'icon' => 'external-link', 'badge' => count($groupedNav['external'])],
      ];
      ?>

      <?php foreach ($navTabs as $k => $t): ?>
        <button type="button" onclick="switchNavTab('<?= $k ?>')" id="navtab_<?= $k ?>" class="ts-btn ts-btn-sm <?= $activeTab === $k ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="<?= $t['icon'] ?>" style="width: 14px; height: 14px;"></i>
          <span><?= $t['label'] ?></span>
          <span class="ts-badge" style="font-size: 0.68rem; padding: 0.1rem 0.4rem; background: rgba(255,255,255,0.1);"><?= $t['badge'] ?></span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Workspace Panes -->
  <?php foreach ($navTabs as $k => $t): ?>
    <div class="nav-pane" id="navpane_<?= $k ?>" style="<?= $activeTab === $k ? 'display:block;' : 'display:none;' ?>">
      <div class="admin-table-wrap">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); flex-wrap: wrap; gap: 1rem;">
          <div>
            <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0;"><?= $t['label'] ?> Items</h3>
            <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Manage links, target tabs, badge callouts, and sort hierarchy for the <?= strtolower($t['label']) ?> layer.</p>
          </div>
          <button type="button" onclick="openNewNavModal('<?= $k ?>')" class="ts-btn ts-btn-primary ts-btn-sm">+ Add to <?= $t['label'] ?></button>
        </div>

        <table class="admin-table">
          <thead>
            <tr>
              <th style="width: 40px;">#</th>
              <th>Label &amp; Destination URL</th>
              <th>Icon</th>
              <th>Badge Callout</th>
              <th>Target</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($groupedNav[$k])): ?>
              <?php foreach ($groupedNav[$k] as $idx => $item): ?>
                <tr>
                  <td style="color: #64748b; font-family: var(--admin-font-mono);"><?= $idx + 1 ?></td>
                  <td>
                    <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem;">
                      <strong style="color: #f8fafc; font-size: 0.95rem;"><?= htmlspecialchars($item['label']) ?></strong>
                      <?php if (!empty($item['badge_text'])): ?>
                        <span class="ts-badge ts-badge-<?= htmlspecialchars($item['badge_color'] ?: 'cyan') ?>" style="font-size: 0.65rem;">
                          <?= htmlspecialchars($item['badge_text']) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                    <div style="font-size: 0.75rem; color: #38bdf8; font-family: var(--admin-font-mono); margin-top: 0.2rem;">
                      <?= htmlspecialchars($item['url']) ?>
                    </div>
                  </td>
                  <td>
                    <?php if (!empty($item['icon_name'])): ?>
                      <div style="display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(15,23,42,0.6); padding: 0.25rem 0.5rem; border-radius: 4px; border: 1px solid var(--admin-border-subtle); font-size: 0.75rem; color: #cbd5e1;">
                        <i data-lucide="<?= htmlspecialchars($item['icon_name']) ?>" style="width: 14px; height: 14px;"></i>
                        <span style="font-family: var(--admin-font-mono);"><?= htmlspecialchars($item['icon_name']) ?></span>
                      </div>
                    <?php else: ?>
                      <span style="color: #64748b; font-size: 0.8rem;">None</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= !empty($item['badge_text']) ? htmlspecialchars($item['badge_text']) : '<span style="color:#64748b;">—</span>' ?>
                  </td>
                  <td style="font-size: 0.8rem; font-family: var(--admin-font-mono); color: #94a3b8;">
                    <?= htmlspecialchars($item['target']) ?>
                  </td>
                  <td>
                    <span class="ts-badge <?= !empty($item['is_active']) ? 'ts-badge-green' : 'ts-badge-pink' ?>">
                      <?= !empty($item['is_active']) ? 'ACTIVE' : 'HIDDEN' ?>
                    </span>
                  </td>
                  <td>
                    <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.4rem;">
                      <button type="button" onclick='editNavItem(<?= json_encode($item) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #38bdf8;">
                        ✏️ Edit
                      </button>
                      <form action="<?= url('/admin/navigation/delete/' . $item['id']) ?>" method="POST" onsubmit="return confirm('Delete this navigation link?');" style="display: inline;">
                        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #ef4444;">
                          🗑
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: #64748b;">No navigation items configured for <?= htmlspecialchars($t['label']) ?> yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endforeach; ?>

</div>

<!-- Modal: Add / Edit Navigation Item -->
<div id="navItemModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 580px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" id="navModalTitle" style="color: #38bdf8;"><i data-lucide="link"></i> Add / Edit Navigation Link</h3>
      <button type="button" onclick="closeNavModal()" class="admin-modal-close">✕</button>
    </div>

    <form id="adminNavForm" action="<?= url('/admin/navigation/save') ?>" method="POST">
      <input type="hidden" name="id" id="nav_id" value="">

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Menu Location</label>
          <select name="location" id="nav_location" class="admin-modal-select">
            <option value="main">Main Header Navigation</option>
            <option value="secondary">Secondary Utility Ribbon</option>
            <option value="footer">Footer Column</option>
            <option value="mobile">Mobile Drawer Nav</option>
            <option value="dropdown">Nested Dropdown</option>
            <option value="mega_menu">Mega Menu Block</option>
            <option value="external">External Social Link</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Sort Order</label>
          <input type="number" name="sort_order" id="nav_sort_order" value="0" class="admin-modal-input">
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Navigation Label</label>
        <input type="text" name="label" id="nav_label" placeholder="e.g. AI Tools Directory" class="admin-modal-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Destination URL</label>
        <input type="text" name="url" id="nav_url" placeholder="/tools or https://github.com/techscout" class="admin-modal-input" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Lucide Icon Name (Optional)</label>
          <input type="text" name="icon_name" id="nav_icon_name" placeholder="e.g. cpu, terminal, bot, layers" class="admin-modal-input">
        </div>
        <div>
          <label class="admin-modal-label">Target Window</label>
          <select name="target" id="nav_target" class="admin-modal-select">
            <option value="_self">Same Window (_self)</option>
            <option value="_blank">New Tab (_blank)</option>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label class="admin-modal-label">Badge Callout Text (e.g. "New", "Live")</label>
          <input type="text" name="badge_text" id="nav_badge_text" placeholder="e.g. 100+, New, Live" class="admin-modal-input">
        </div>
        <div>
          <label class="admin-modal-label">Badge Glow Color</label>
          <select name="badge_color" id="nav_badge_color" class="admin-modal-select">
            <option value="cyan">Cyan Glow</option>
            <option value="pink">Pink Accent</option>
            <option value="green">Emerald Green</option>
            <option value="amber">Amber Gold</option>
            <option value="indigo">Indigo Purple</option>
          </select>
        </div>
      </div>

      <div style="background: rgba(15,23,42,0.6); padding: 0.85rem 1rem; border-radius: 6px; border: 1px solid var(--admin-border-subtle); margin-bottom: 1.25rem;">
        <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer;">
          <input type="checkbox" name="is_active" id="nav_is_active" value="1" checked style="accent-color: #10b981; width: 16px; height: 16px;">
          <span style="color: #f8fafc; font-size: 0.88rem; font-weight: 600;">Visible &amp; Active in Navigation Menu</span>
        </label>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeNavModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; border-color: #38bdf8; color: #000; font-weight: 700;">Save Link ⚡</button>
      </div>
    </form>
  </div>
</div>

<script>
function switchNavTab(tabKey) {
  document.querySelectorAll('[id^="navtab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('navtab_' + tabKey);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.nav-pane').forEach(p => p.style.display = 'none');
  const targetPane = document.getElementById('navpane_' + tabKey);
  if (targetPane) targetPane.style.display = 'block';

  const url = new URL(window.location);
  url.searchParams.set('tab', tabKey);
  window.history.replaceState({}, '', url);

  if (window.lucide) lucide.createIcons();
}

function openNewNavModal(defaultLoc = 'main') {
  document.getElementById('adminNavForm').reset();
  document.getElementById('nav_id').value = '';
  document.getElementById('nav_location').value = defaultLoc;
  document.getElementById('nav_is_active').checked = true;
  document.getElementById('navModalTitle').innerText = 'Add Navigation Link';
  document.getElementById('navItemModal').style.display = 'flex';
}

function editNavItem(item) {
  document.getElementById('nav_id').value = item.id;
  document.getElementById('nav_location').value = item.location || 'main';
  document.getElementById('nav_label').value = item.label || '';
  document.getElementById('nav_url').value = item.url || '';
  document.getElementById('nav_target').value = item.target || '_self';
  document.getElementById('nav_icon_name').value = item.icon_name || '';
  document.getElementById('nav_badge_text').value = item.badge_text || '';
  document.getElementById('nav_badge_color').value = item.badge_color || 'cyan';
  document.getElementById('nav_sort_order').value = item.sort_order || 0;
  document.getElementById('nav_is_active').checked = parseInt(item.is_active, 10) === 1;
  document.getElementById('navModalTitle').innerText = 'Edit Navigation Link: ' + item.label;
  document.getElementById('navItemModal').style.display = 'flex';
}

function closeNavModal() {
  document.getElementById('navItemModal').style.display = 'none';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
