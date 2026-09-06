<?php require __DIR__ . '/../layouts/admin_header.php'; ?>
    <!-- Header -->
    <div class="admin-header-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
          <span class="ts-badge ts-badge-cyan">SUPPORT TAXONOMY &amp; SLA ENGINE</span>
          <span style="font-size: 0.8rem; color: #64748b;">Department Routing, Category Hierarchy &amp; Escalation Targets</span>
        </div>
        <h1 style="font-size: 1.85rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif;">
          Support Categories &amp; SLA Architecture
        </h1>
      </div>

      <div style="display: flex; gap: 0.75rem;">
        <a href="<?= url('/support') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm">
          Live Helpdesk ↗
        </a>
        <button onclick="openCategoryModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">
          <i data-lucide="plus-circle"></i> Add New Category
        </button>
      </div>
    </div>

    <!-- Overview Stats Cards -->
    <div class="admin-stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
      <div class="ts-card" style="padding: 1.25rem;">
        <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Active Categories</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;"><?= count($categories) ?></div>
        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem;">Cross-departmental routing</div>
      </div>
      <div class="ts-card" style="padding: 1.25rem;">
        <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Total Subcategories</div>
        <?php 
          $totalSub = 0;
          foreach ($categories as $cat) {
            $totalSub += count($cat['subcategories'] ?? []);
          }
        ?>
        <div style="font-size: 1.75rem; font-weight: 800; color: #10b981; margin-top: 0.25rem;"><?= $totalSub ?></div>
        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem;">Specialized queue topics</div>
      </div>
      <div class="ts-card" style="padding: 1.25rem;">
        <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Shortest Target SLA</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #ef4444; margin-top: 0.25rem;">2 Hours</div>
        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem;">Urgent / Security outages</div>
      </div>
      <div class="ts-card" style="padding: 1.25rem;">
        <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Default Resolution</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem;">24 Hours</div>
        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem;">Standard engineering queues</div>
      </div>
    </div>

    <!-- Category Hierarchy Table -->
    <div class="admin-table-wrap" style="background: rgba(13,21,39,0.7); border: 1px solid rgba(99,102,241,0.2); border-radius: 12px; overflow: hidden;">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width: 220px;">Department &amp; Category</th>
            <th>Slug &amp; Identifier</th>
            <th>Subcategories &amp; Specialized Queues</th>
            <th>Default Priority</th>
            <th>Target SLA</th>
            <th>Status</th>
            <th style="text-align: right; width: 240px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
              <tr>
                <td>
                  <div style="font-size: 0.72rem; text-transform: uppercase; color: #818cf8; font-weight: 700; letter-spacing: 0.5px;"><?= htmlspecialchars($cat['department']) ?></div>
                  <strong style="color: #f8fafc; font-size: 0.95rem; display: block; margin-top: 0.15rem;"><?= htmlspecialchars($cat['name']) ?></strong>
                  <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem;"><?= htmlspecialchars($cat['description']) ?></div>
                </td>
                <td>
                  <code style="background: rgba(15,23,42,0.8); color: #38bdf8; padding: 0.2rem 0.45rem; border-radius: 4px; font-size: 0.8rem; border: 1px solid rgba(56,189,248,0.2);">
                    <?= htmlspecialchars($cat['slug']) ?>
                  </code>
                </td>
                <td>
                  <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; max-width: 420px; align-items: center;">
                    <?php if (!empty($cat['subcategories'])): ?>
                      <?php foreach ($cat['subcategories'] as $sub): ?>
                        <span class="ts-badge" style="font-size: 0.72rem; background: rgba(148,163,184,0.1); border: 1px solid rgba(148,163,184,0.25); color: #e2e8f0; display: inline-flex; align-items: center; gap: 0.35rem;" title="Priority: <?= htmlspecialchars($sub['priority'] ?? 'default') ?> • SLA: <?= (int)($sub['sla_hours'] ?? 24) ?>h">
                          <?= htmlspecialchars($sub['name']) ?>
                          <span style="font-size: 0.65rem; color: #38bdf8;">(<?= (int)($sub['sla_hours'] ?? 24) ?>h)</span>
                          <button type="button" onclick="confirmDeleteSubcategory(<?= (int)$cat['id'] ?>, '<?= htmlspecialchars(addslashes($sub['slug'] ?? $sub['name'])) ?>', '<?= htmlspecialchars(addslashes($sub['name'])) ?>')" style="background:none; border:none; color:#ef4444; padding:0; cursor:pointer; font-size:0.85rem; line-height:1; opacity:0.75;" title="Remove this subcategory">&times;</button>
                        </span>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <span style="color: #64748b; font-size: 0.8rem; font-style: italic;">No subcategories defined</span>
                    <?php endif; ?>
                    <button onclick='openQuickSubcatModal(<?= (int)$cat['id'] ?>, <?= json_encode($cat['name']) ?>)' class="ts-btn ts-btn-outline ts-btn-xs" style="color: #38bdf8; border-color: rgba(56,189,248,0.3); padding: 0.15rem 0.45rem; font-size: 0.7rem;" title="Quickly add subcategory to <?= htmlspecialchars($cat['name']) ?>">
                      + Add
                    </button>
                  </div>
                </td>
                <td>
                  <span style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: <?= $cat['default_priority'] === 'urgent' ? '#ef4444' : ($cat['default_priority'] === 'high' ? '#f97316' : '#38bdf8') ?>;">
                    <?= htmlspecialchars($cat['default_priority']) ?>
                  </span>
                </td>
                <td>
                  <span style="font-family: monospace; font-size: 0.88rem; color: #f8fafc; font-weight: 700;">
                    <?= (int)$cat['sla_hours'] ?> Hours
                  </span>
                </td>
                <td>
                  <?php if ($cat['is_active']): ?>
                    <span class="ts-badge ts-badge-green">ACTIVE</span>
                  <?php else: ?>
                    <span class="ts-badge" style="background: rgba(239,68,68,0.2); color: #ef4444;">INACTIVE</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                    <button onclick='openQuickSubcatModal(<?= (int)$cat['id'] ?>, <?= json_encode($cat['name']) ?>)' class="ts-btn ts-btn-sm" style="background: rgba(56,189,248,0.15); color: #38bdf8; border: 1px solid rgba(56,189,248,0.3); padding: 0.3rem 0.65rem; font-size: 0.78rem;">
                      + Subcat
                    </button>
                    <button onclick='editCategory(<?= json_encode($cat, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.3rem 0.65rem; font-size: 0.78rem;">
                      Edit ✏️
                    </button>
                    <form action="<?= url('/admin/support/categories/' . $cat['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete category <?= htmlspecialchars(addslashes($cat['name'])) ?> and its subcategories?');" style="display: inline;">
                      <button type="submit" class="ts-btn ts-btn-sm" style="background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); padding: 0.3rem 0.65rem; font-size: 0.78rem;">
                        Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">No support categories found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<!-- Modal 1: Create / Edit Category with Visual Subcategory Builder -->
<div id="catModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="ts-card" style="width: 100%; max-width: 720px; max-height: 92vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.35); box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <h2 id="modalTitle" style="font-size: 1.3rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif;">Add Support Category</h2>
      <button onclick="closeCategoryModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer; line-height: 1;">&times;</button>
    </div>

    <form id="catForm" action="<?= url('/admin/support/categories/save') ?>" method="POST" onsubmit="return handleCatFormSubmit();">
      <input type="hidden" name="id" id="cat_id" value="">

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Department Name <span style="color: #ef4444;">*</span></label>
          <input type="text" name="department" id="cat_department" placeholder="e.g. Engineering &amp; Systems" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;" required>
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Category Name <span style="color: #ef4444;">*</span></label>
          <input type="text" name="name" id="cat_name" placeholder="e.g. Cloud Infrastructure &amp; Servers" oninput="autoSlugCategory(this.value)" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;" required>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">URL Slug Identifier</label>
          <input type="text" name="slug" id="cat_slug" placeholder="e.g. cloud_infra" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #38bdf8; font-size: 0.88rem; font-family: monospace;">
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Sort Order Index</label>
          <input type="number" name="sort_order" id="cat_sort_order" value="0" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;">
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Brief Summary / Description</label>
        <input type="text" name="description" id="cat_description" placeholder="Short description shown to builders when selecting ticket routing" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Default Priority</label>
          <select name="default_priority" id="cat_priority" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;">
            <option value="urgent">Urgent</option>
            <option value="high">High</option>
            <option value="medium" selected>Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Default SLA (Hours)</label>
          <input type="number" name="sla_hours" id="cat_sla_hours" value="24" min="1" max="168" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;">
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Visibility Status</label>
          <select name="is_active" id="cat_is_active" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;">
            <option value="1">Active (Published)</option>
            <option value="0">Disabled</option>
          </select>
        </div>
      </div>

      <!-- VISUAL SUBCATEGORY BUILDER (NO RAW JSON) -->
      <div style="margin-bottom: 1.5rem; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 1.2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
          <div>
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.15rem;">
              Subcategories &amp; Specialized Queues
            </label>
            <span style="font-size: 0.75rem; color: #94a3b8;">
              Manage ticket subtopics and custom SLA response times with clean visual fields (no JSON required).
            </span>
          </div>
          <button type="button" onclick="addSubcategoryRow()" class="ts-btn ts-btn-sm" style="background: rgba(56,189,248,0.15); color: #38bdf8; border: 1px solid rgba(56,189,248,0.4); font-weight: 700;">
            + Add Subcategory
          </button>
        </div>

        <div id="subcat_list_container" style="display: flex; flex-direction: column; gap: 0.6rem; max-height: 260px; overflow-y: auto; padding-right: 0.25rem;">
          <!-- Dynamically populated rows -->
        </div>
        <input type="hidden" name="subcategories" id="cat_subcategories_hidden" value="[]">
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid rgba(148,163,184,0.15); padding-top: 1rem;">
        <button type="button" onclick="closeCategoryModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">Save Category</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal 2: Quick Add Subcategory Direct Modal -->
<div id="quickSubcatModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="ts-card" style="width: 100%; max-width: 480px; padding: 1.75rem; background: #090d16; border: 1px solid rgba(56,189,248,0.35); box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.5rem;">
      <div>
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; margin-bottom: 0.25rem;">NEW QUEUE TOPIC</span>
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0;">Add Subcategory to <span id="quick_cat_name" style="color: #38bdf8;"></span></h3>
      </div>
      <button onclick="closeQuickSubcatModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.4rem; cursor: pointer; line-height: 1;">&times;</button>
    </div>
    
    <form id="quickSubcatForm" action="" method="POST">
      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Subcategory Name <span style="color: #ef4444;">*</span></label>
        <input type="text" name="name" id="quick_subcat_name" placeholder="e.g. Database Replication Issues" oninput="autoSlugQuick(this.value)" required style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;">
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Slug Identifier</label>
        <input type="text" name="slug" id="quick_subcat_slug" placeholder="e.g. db_replication" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #38bdf8; font-size: 0.88rem; font-family: monospace;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Priority Level</label>
          <select name="priority" id="quick_subcat_priority" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;">
            <option value="urgent">Urgent</option>
            <option value="high">High</option>
            <option value="medium" selected>Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Target SLA (Hours)</label>
          <input type="number" name="sla_hours" id="quick_subcat_sla" value="24" min="1" max="168" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;">
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="closeQuickSubcatModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">Add Subcategory</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal 3: Hidden Subcategory Delete Form -->
<form id="subcatDeleteForm" action="" method="POST" style="display: none;">
  <input type="hidden" name="slug" id="subcat_delete_slug" value="">
</form>

<script>
function autoSlugCategory(val) {
  const slugInput = document.getElementById('cat_slug');
  if (!slugInput.dataset.manual) {
    slugInput.value = val.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
  }
}
document.getElementById('cat_slug').addEventListener('input', function() {
  this.dataset.manual = 'true';
});

function autoSlugQuick(val) {
  const slugInput = document.getElementById('quick_subcat_slug');
  if (!slugInput.dataset.manual) {
    slugInput.value = val.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
  }
}
document.getElementById('quick_subcat_slug').addEventListener('input', function() {
  this.dataset.manual = 'true';
});

// Dynamic Subcategory Visual Rows Generator
function renderSubcategoryRows(subcategories) {
  const container = document.getElementById('subcat_list_container');
  container.innerHTML = '';

  if (!subcategories || subcategories.length === 0) {
    container.innerHTML = '<div id="no_subcats_hint" style="color: #64748b; font-size: 0.82rem; font-style: italic; padding: 0.5rem 0;">No subcategories added yet. Click "+ Add Subcategory" to create one.</div>';
    return;
  }

  subcategories.forEach((sub, idx) => {
    appendSubcatRowHtml(sub.name || '', sub.slug || '', sub.priority || 'medium', sub.sla_hours || 24);
  });
}

function appendSubcatRowHtml(name = '', slug = '', priority = 'medium', sla = 24) {
  const container = document.getElementById('subcat_list_container');
  const emptyHint = document.getElementById('no_subcats_hint');
  if (emptyHint) emptyHint.remove();

  const row = document.createElement('div');
  row.className = 'subcat-editor-row';
  row.style.cssText = 'display: grid; grid-template-columns: 2fr 1.5fr 1.2fr 1fr 36px; gap: 0.5rem; align-items: center; background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 6px; padding: 0.5rem 0.65rem;';

  row.innerHTML = `
    <div>
      <input type="text" class="sub-name" placeholder="Subcategory Name" value="${escapeHtml(name)}" oninput="onSubNameInput(this)" style="width: 100%; background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.2); padding: 0.45rem 0.65rem; border-radius: 4px; color: #f8fafc; font-size: 0.82rem;" required>
    </div>
    <div>
      <input type="text" class="sub-slug" placeholder="slug" value="${escapeHtml(slug)}" style="width: 100%; background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.2); padding: 0.45rem 0.65rem; border-radius: 4px; color: #38bdf8; font-size: 0.82rem; font-family: monospace;">
    </div>
    <div>
      <select class="sub-priority" style="width: 100%; background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.2); padding: 0.45rem 0.5rem; border-radius: 4px; color: #f8fafc; font-size: 0.82rem;">
        <option value="urgent" ${priority === 'urgent' ? 'selected' : ''}>Urgent</option>
        <option value="high" ${priority === 'high' ? 'selected' : ''}>High</option>
        <option value="medium" ${priority === 'medium' ? 'selected' : ''}>Medium</option>
        <option value="low" ${priority === 'low' ? 'selected' : ''}>Low</option>
      </select>
    </div>
    <div style="display: flex; align-items: center; gap: 0.25rem;">
      <input type="number" class="sub-sla" value="${parseInt(sla) || 24}" min="1" max="168" style="width: 100%; background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.2); padding: 0.45rem 0.5rem; border-radius: 4px; color: #f8fafc; font-size: 0.82rem;">
      <span style="font-size: 0.72rem; color: #94a3b8;">h</span>
    </div>
    <div style="text-align: right;">
      <button type="button" onclick="this.closest('.subcat-editor-row').remove()" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; border-radius: 4px; width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; line-height: 1;" title="Remove">&times;</button>
    </div>
  `;

  container.appendChild(row);
}

function onSubNameInput(nameEl) {
  const row = nameEl.closest('.subcat-editor-row');
  const slugEl = row.querySelector('.sub-slug');
  if (!slugEl.dataset.manual) {
    slugEl.value = nameEl.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
  }
}

function addSubcategoryRow() {
  appendSubcatRowHtml('', '', 'medium', 24);
}

function escapeHtml(text) {
  if (!text) return '';
  return text.toString()
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function handleCatFormSubmit() {
  const rows = document.querySelectorAll('.subcat-editor-row');
  const subcategories = [];

  rows.forEach(row => {
    const name = row.querySelector('.sub-name').value.trim();
    let slug = row.querySelector('.sub-slug').value.trim();
    const priority = row.querySelector('.sub-priority').value;
    const sla = parseInt(row.querySelector('.sub-sla').value) || 24;

    if (name) {
      if (!slug) {
        slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
      }
      subcategories.push({
        name: name,
        slug: slug,
        priority: priority,
        sla_hours: sla
      });
    }
  });

  document.getElementById('cat_subcategories_hidden').value = JSON.stringify(subcategories);
  return true;
}

function openCategoryModal() {
  document.getElementById('modalTitle').textContent = 'Add Support Category';
  document.getElementById('cat_id').value = '';
  document.getElementById('cat_department').value = 'Engineering & Systems';
  document.getElementById('cat_name').value = '';
  document.getElementById('cat_slug').value = '';
  document.getElementById('cat_slug').dataset.manual = '';
  document.getElementById('cat_description').value = '';
  document.getElementById('cat_priority').value = 'medium';
  document.getElementById('cat_sla_hours').value = 24;
  document.getElementById('cat_sort_order').value = 0;
  document.getElementById('cat_is_active').value = 1;

  renderSubcategoryRows([
    { name: 'General Queue', slug: 'general_queue', priority: 'medium', sla_hours: 24 }
  ]);
  document.getElementById('catModal').style.display = 'flex';
}

function editCategory(cat) {
  document.getElementById('modalTitle').textContent = 'Edit Category: ' + cat.name;
  document.getElementById('cat_id').value = cat.id;
  document.getElementById('cat_department').value = cat.department || '';
  document.getElementById('cat_name').value = cat.name || '';
  document.getElementById('cat_slug').value = cat.slug || '';
  document.getElementById('cat_slug').dataset.manual = 'true';
  document.getElementById('cat_description').value = cat.description || '';
  document.getElementById('cat_priority').value = cat.default_priority || 'medium';
  document.getElementById('cat_sla_hours').value = cat.sla_hours || 24;
  document.getElementById('cat_sort_order').value = cat.sort_order || 0;
  document.getElementById('cat_is_active').value = cat.is_active ? 1 : 0;

  renderSubcategoryRows(cat.subcategories || []);
  document.getElementById('catModal').style.display = 'flex';
}

function closeCategoryModal() {
  document.getElementById('catModal').style.display = 'none';
}

// Quick Subcategory Modal Handlers
function openQuickSubcatModal(catId, catName) {
  document.getElementById('quick_cat_name').textContent = catName;
  document.getElementById('quick_subcat_name').value = '';
  document.getElementById('quick_subcat_slug').value = '';
  document.getElementById('quick_subcat_slug').dataset.manual = '';
  document.getElementById('quick_subcat_priority').value = 'medium';
  document.getElementById('quick_subcat_sla').value = 24;
  
  const form = document.getElementById('quickSubcatForm');
  form.action = '<?= url('/admin/support/categories/') ?>' + catId + '/subcategory/add';
  
  document.getElementById('quickSubcatModal').style.display = 'flex';
}

function closeQuickSubcatModal() {
  document.getElementById('quickSubcatModal').style.display = 'none';
}

// Delete Single Subcategory from table
function confirmDeleteSubcategory(catId, subSlug, subName) {
  if (confirm(`Remove subcategory "${subName}" from this category?`)) {
    const form = document.getElementById('subcatDeleteForm');
    form.action = '<?= url('/admin/support/categories/') ?>' + catId + '/subcategory/delete';
    document.getElementById('subcat_delete_slug').value = subSlug;
    form.submit();
  }
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
