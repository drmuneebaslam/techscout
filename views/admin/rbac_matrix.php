<?php
$title = 'Staff Roles & Permission Matrix | TechScout Admin';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="admin-content-inner">
  <!-- Page Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-purple" style="font-size: 0.68rem; font-weight: 800;">SECURITY &amp; GOVERNANCE HUD</span>
        <span style="color: #64748b; font-size: 0.8rem;">Granular Role-Based Access Control (RBAC)</span>
      </div>
      <h1 style="font-size: 1.65rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        🔐 Staff Roles &amp; Capability Matrix
      </h1>
    </div>

    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
      <button type="button" onclick="openCreateRoleModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #a855f7, #38bdf8); font-weight: 700;">
        <i data-lucide="shield-plus"></i> + Create Custom Role
      </button>
    </div>
  </div>

  <!-- Role Overview Table -->
  <div class="admin-table-wrap" style="margin-bottom: 2.5rem;">
    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Role Name</th>
            <th>Role Key</th>
            <th>Type</th>
            <th>Description</th>
            <th>Capabilities</th>
            <th>Staff Count</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($roles as $r): ?>
            <tr>
              <td><strong style="color: #f8fafc;"><?= htmlspecialchars($r['name']) ?></strong></td>
              <td><span style="font-family: monospace; font-size: 0.75rem; color: #38bdf8;">@<?= htmlspecialchars($r['role_key']) ?></span></td>
              <td>
                <?php if (!empty($r['is_system'])): ?>
                  <span class="ts-badge ts-badge-purple" style="font-size: 0.65rem;">SYSTEM</span>
                <?php else: ?>
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">CUSTOM</span>
                <?php endif; ?>
              </td>
              <td><span style="font-size: 0.82rem; color: #94a3b8;"><?= htmlspecialchars($r['description'] ?: 'Custom administrative staff role.') ?></span></td>
              <td><strong style="color: #10b981;"><?= count($r['permissions']) ?></strong></td>
              <td><strong style="color: #f8fafc;"><?= $r['user_count'] ?></strong></td>
              <td>
                <button type="button" onclick='editRole(<?= json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">
                  Configure &rarr;
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Staff User Role Assignments Table -->
  <div class="admin-table-wrap" style="margin-bottom: 2.5rem;">
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; font-weight: 700;">
          👥 Staff User Role Assignments
        </h3>
        <p style="font-size: 0.8rem; color: #94a3b8; margin: 0.2rem 0 0 0;">Assign granular permission roles to administrative team members.</p>
      </div>
    </div>

    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Email</th>
            <th>Base Role</th>
            <th>Assigned RBAC Role</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($staffUsers as $su): ?>
            <tr>
              <td>
                <strong style="color: #f8fafc;"><?= htmlspecialchars($su['username']) ?></strong>
              </td>
              <td style="color: #94a3b8; font-size: 0.85rem;">
                <?= htmlspecialchars($su['email']) ?>
              </td>
              <td>
                <span class="ts-badge ts-badge-indigo" style="font-size: 0.7rem; text-transform: uppercase;">
                  <?= htmlspecialchars($su['role']) ?>
                </span>
              </td>
              <td>
                <form action="<?= url('/admin/roles/assign') ?>" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                  <input type="hidden" name="user_id" value="<?= $su['id'] ?>">
                  <select name="role_id" class="admin-modal-select" style="padding: 0.3rem 0.5rem; font-size: 0.8rem; max-width: 220px;">
                    <option value="">-- Standard Base Role --</option>
                    <?php foreach ($roles as $r): ?>
                      <option value="<?= $r['id'] ?>" <?= ((int)$su['admin_role_id'] === (int)$r['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.3rem 0.65rem;">
                    Update
                  </button>
                </form>
              </td>
              <td>
                <span style="font-size: 0.75rem; color: #10b981;">Active ✓</span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal: Create / Edit Staff Role -->
<div id="roleModal" class="admin-modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(5px); z-index: 999999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 720px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #0f172a; border: 1px solid rgba(168,85,247,0.4); border-radius: 14px; box-shadow: 0 20px 40px rgba(0,0,0,0.8);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 1rem;">
      <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; font-weight: 800;" id="roleModalTitle">
        🔐 Configure Staff Role &amp; Capabilities
      </h3>
      <button type="button" onclick="closeRoleModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form action="<?= url('/admin/roles/save') ?>" method="POST" id="roleForm">
      <input type="hidden" name="role_id" id="rf_role_id" value="">

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label class="admin-modal-label">Role Display Name <span style="color: #ef4444;">*</span></label>
          <input type="text" name="name" id="rf_name" placeholder="e.g. Lead Technical Auditor" class="admin-modal-input" required>
        </div>
        <div>
          <label class="admin-modal-label">Role Key Slug <span style="color: #ef4444;">*</span></label>
          <input type="text" name="role_key" id="rf_key" placeholder="e.g. tech_auditor" class="admin-modal-input" required style="font-family: monospace;">
        </div>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label class="admin-modal-label">Role Description</label>
        <input type="text" name="description" id="rf_description" placeholder="e.g. Full oversight of financial revenue streams and content originality." class="admin-modal-input">
      </div>

      <!-- Master Permission Nodes Checkbox Grid -->
      <div style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <label class="admin-modal-label" style="margin: 0;">Assigned Capabilities &amp; Permission Nodes</label>
          <div style="display: flex; gap: 0.5rem;">
            <button type="button" onclick="toggleAllPerms(true)" style="background: none; border: none; color: #38bdf8; font-size: 0.75rem; cursor: pointer; text-decoration: underline;">Select All</button>
            <button type="button" onclick="toggleAllPerms(false)" style="background: none; border: none; color: #94a3b8; font-size: 0.75rem; cursor: pointer; text-decoration: underline;">Clear All</button>
          </div>
        </div>

        <?php foreach ($catalog as $domain => $perms): ?>
          <div style="margin-bottom: 1.25rem; background: rgba(15,23,42,0.5); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1rem;">
            <div style="font-size: 0.85rem; font-weight: 700; color: #38bdf8; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
              <span>📁</span> <?= htmlspecialchars($domain) ?>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem;">
              <?php foreach ($perms as $pKey => $pLabel): ?>
                <label style="display: flex; align-items: flex-start; gap: 0.5rem; color: #cbd5e1; font-size: 0.8rem; cursor: pointer;">
                  <input type="checkbox" name="permissions[]" value="<?= htmlspecialchars($pKey) ?>" class="perm-chk" id="pchk_<?= str_replace('.', '_', $pKey) ?>" style="accent-color: #a855f7; margin-top: 0.15rem;">
                  <span><?= htmlspecialchars($pLabel) ?> <code style="font-size: 0.7rem; color: #64748b;">(<?= $pKey ?>)</code></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeRoleModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #a855f7, #38bdf8); font-weight: 800; padding: 0.6rem 1.5rem;">
          Save Role Capabilities ✓
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openCreateRoleModal() {
  document.getElementById('roleModalTitle').innerText = '🔐 Create New Staff Role';
  document.getElementById('rf_role_id').value = '';
  document.getElementById('rf_name').value = '';
  document.getElementById('rf_key').value = '';
  document.getElementById('rf_key').readOnly = false;
  document.getElementById('rf_description').value = '';
  toggleAllPerms(false);
  document.getElementById('roleModal').style.display = 'flex';
}

function editRole(role) {
  document.getElementById('roleModalTitle').innerText = `🔐 Edit Role: ${role.name}`;
  document.getElementById('rf_role_id').value = role.id;
  document.getElementById('rf_name').value = role.name;
  document.getElementById('rf_key').value = role.role_key;
  document.getElementById('rf_key').readOnly = (role.is_system == 1);
  document.getElementById('rf_description').value = role.description || '';
  
  toggleAllPerms(false);
  if (Array.isArray(role.permissions)) {
    role.permissions.forEach(p => {
      const chk = document.getElementById('pchk_' + p.replace('.', '_'));
      if (chk) chk.checked = true;
    });
  }

  document.getElementById('roleModal').style.display = 'flex';
}

function closeRoleModal() {
  document.getElementById('roleModal').style.display = 'none';
}

function toggleAllPerms(checked) {
  document.querySelectorAll('.perm-chk').forEach(c => c.checked = checked);
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
