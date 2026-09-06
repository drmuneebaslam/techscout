<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0 0 0.25rem 0;">Categories & Hierarchical Taxonomies CRUD</h1>
    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Organize 50+ categories, sub-categories, entity scopes (GPTs, Prompts, Tools, Articles), and homepage visibility.</p>
  </div>

  <button onclick="openNewCategoryModal()" class="ts-btn ts-btn-primary">
    <i data-lucide="plus-circle"></i> Create Category
  </button>
</div>

<!-- Category Modal -->
<div id="catFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 700px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h2 id="modalCatTitle" style="font-size: 1.35rem; color: #f8fafc; margin: 0;">Create / Edit Category</h2>
      <button onclick="document.getElementById('catFormModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form id="adminCatForm" action="<?= url('/admin/categories/save') ?>" method="POST">
      <input type="hidden" name="id" id="cat_id" value="">

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Category Name <span style="color: #ef4444;">*</span></label>
          <input type="text" name="name" id="cat_name" placeholder="e.g. Autonomous Coding Agents" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 1rem;" required>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Slug (Clean SEO URL)</label>
          <input type="text" name="slug" id="cat_slug" placeholder="autonomous-coding-agents" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Parent Topic Domain</label>
          <select name="topic_id" id="cat_topic_id" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
            <option value="">None / Unassigned</option>
            <?php foreach ($topics ?? [] as $top): ?>
              <option value="<?= $top['id'] ?>"><?= htmlspecialchars($top['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Parent Category (Sub-category Hierarchy)</label>
          <select name="parent_id" id="cat_parent_id" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
            <option value="">None (Top-Level Root Domain)</option>
            <?php foreach ($parentCategories as $pCat): ?>
              <option value="<?= $pCat['id'] ?>"><?= htmlspecialchars($pCat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Applicable Entity Scope</label>
          <select name="applicable_to" id="cat_applicable_to" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
            <option value="all">🌐 Global (Applicable to All Modules)</option>
            <option value="gpt_listing">🤖 Custom GPTs & Agents Only</option>
            <option value="prompt">⚡ Prompt Blueprints Only</option>
            <option value="ai_tool">🛠️ AI Tools Radar Only</option>
            <option value="article">📝 Technical Articles Only</option>
            <option value="campaign">🏆 Challenges Only</option>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Icon Name (Lucide)</label>
          <input type="text" name="icon_name" id="cat_icon" value="folder" placeholder="cpu, bot, terminal, cloud..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Brand Color</label>
          <input type="color" name="color_hex" id="cat_color" value="#6366f1" style="width: 100%; height: 42px; background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 6px; cursor: pointer;">
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Sort Order</label>
          <input type="number" name="sort_order" id="cat_sort_order" value="0" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Description</label>
        <textarea name="description" id="cat_desc" rows="2" placeholder="Brief 1-line description of this category topic..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;"></textarea>
      </div>

      <div style="margin-bottom: 1.5rem; background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1);">
        <label style="display: flex; align-items: center; gap: 0.6rem; color: #f8fafc; font-size: 0.9rem; cursor: pointer;">
          <input type="checkbox" name="is_featured_home" id="cat_is_featured_home" value="1" style="accent-color: #38bdf8; width: 18px; height: 18px;">
          <div>
            <strong>Feature on Homepage Explorer Grid</strong>
            <div style="font-size: 0.75rem; color: #64748b;">Display this category in the top curated category showcase on the homepage.</div>
          </div>
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('catFormModal').style.display='none'" class="ts-btn ts-btn-outline">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary">Save Category Specification</button>
      </div>
    </form>
  </div>
</div>

<!-- Categories Table -->
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Category / Subcategory</th>
        <th>Scope & Hierarchy</th>
        <th>Clean SEO Path</th>
        <th>Icon & Color</th>
        <th>Homepage</th>
        <th>Items</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
        <tr>
          <td>
            <div style="font-weight: 600; color: #f8fafc; font-size: 0.95rem;">
              <?php if (!empty($cat['parent_id'])): ?>
                <span style="color: #64748b; font-size: 0.8rem; margin-right: 0.3rem;">↳</span>
              <?php endif; ?>
              <?= htmlspecialchars($cat['name']) ?>
            </div>
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;"><?= htmlspecialchars($cat['description'] ?? 'No description') ?></div>
          </td>
          <td>
            <?php if (!empty($cat['topic_name'])): ?>
              <span class="ts-badge" style="background: rgba(99,102,241,0.15); color: #a5b4fc; font-size: 0.68rem; padding: 0.1rem 0.4rem; display: inline-block; margin-bottom: 0.25rem;">
                <i data-lucide="layers" style="width: 11px; display: inline; vertical-align: middle;"></i> <?= htmlspecialchars($cat['topic_name']) ?>
              </span>
            <?php endif; ?>

            <div>
              <?php if (!empty($cat['parent_name'])): ?>
                <span class="ts-badge" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">Parent: <?= htmlspecialchars($cat['parent_name']) ?></span>
              <?php else: ?>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">ROOT DOMAIN</span>
              <?php endif; ?>
            </div>
            
            <div style="margin-top: 0.25rem;">
              <?php 
                $scope = $cat['applicable_to'] ?? 'all';
                $badgeClass = 'ts-badge';
                if ($scope === 'gpt_listing') $badgeClass = 'ts-badge ts-badge-pink';
                elseif ($scope === 'prompt') $badgeClass = 'ts-badge ts-badge-cyan';
                elseif ($scope === 'ai_tool') $badgeClass = 'ts-badge ts-badge-green';
              ?>
              <span class="<?= $badgeClass ?>" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                <?= strtoupper(htmlspecialchars($scope)) ?>
              </span>
            </div>
          </td>
          <td>
            <?php 
              $entitySlug = 'category';
              if (($cat['applicable_to'] ?? '') === 'gpt_listing') $entitySlug = 'gpts/category';
              elseif (($cat['applicable_to'] ?? '') === 'ai_tool') $entitySlug = 'tools/category';
              elseif (($cat['applicable_to'] ?? '') === 'prompt') $entitySlug = 'prompts/category';
              elseif (($cat['applicable_to'] ?? '') === 'article') $entitySlug = 'blog/category';
            ?>
            <a href="<?= url('/' . $entitySlug . '/' . $cat['slug']) ?>" target="_blank" style="color: #38bdf8; font-size: 0.85rem; font-family: var(--admin-font-mono);">
              /<?= $entitySlug ?>/<?= htmlspecialchars($cat['slug']) ?> ↗
            </a>
          </td>
          <td>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <span style="display: inline-block; width: 14px; height: 14px; border-radius: 50%; background: <?= htmlspecialchars($cat['color_hex']) ?>;"></span>
              <span class="ts-badge" style="font-size: 0.75rem;"><i data-lucide="<?= htmlspecialchars($cat['icon_name'] ?: 'folder') ?>" style="width: 12px; display: inline;"></i> <?= htmlspecialchars($cat['icon_name'] ?: 'folder') ?></span>
            </div>
          </td>
          <td>
            <?php if (!empty($cat['is_featured_home'])): ?>
              <span class="ts-badge ts-badge-green">Featured ⭐</span>
            <?php else: ?>
              <span style="color: #64748b; font-size: 0.8rem;">Standard</span>
            <?php endif; ?>
          </td>
          <td><strong style="color: #38bdf8;"><?= number_format($cat['total_items'] ?? $cat['content_count'] ?? 0) ?></strong></td>
          <td>
            <div style="display: flex; gap: 0.35rem;">
              <button onclick='editCat(<?= json_encode($cat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                Edit
              </button>
              <form action="<?= url('/admin/categories/delete/' . $cat['id']) ?>" method="POST" onsubmit="return confirm('Delete this category?');">
                <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #ef4444;">
                  Delete
                </button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
function openNewCategoryModal() {
  document.getElementById('cat_id').value = '';
  document.getElementById('cat_name').value = '';
  document.getElementById('cat_slug').value = '';
  document.getElementById('cat_topic_id').value = '';
  document.getElementById('cat_parent_id').value = '';
  document.getElementById('cat_applicable_to').value = 'all';
  document.getElementById('cat_is_featured_home').checked = false;
  document.getElementById('cat_icon').value = 'folder';
  document.getElementById('cat_color').value = '#6366f1';
  document.getElementById('cat_sort_order').value = '0';
  document.getElementById('cat_desc').value = '';
  document.getElementById('modalCatTitle').innerText = 'Create Category';
  document.getElementById('catFormModal').style.display = 'flex';
}

function editCat(cat) {
  document.getElementById('cat_id').value = cat.id;
  document.getElementById('cat_name').value = cat.name || '';
  document.getElementById('cat_slug').value = cat.slug || '';
  document.getElementById('cat_topic_id').value = cat.topic_id || '';
  document.getElementById('cat_parent_id').value = cat.parent_id || '';
  document.getElementById('cat_applicable_to').value = cat.applicable_to || 'all';
  document.getElementById('cat_is_featured_home').checked = !!parseInt(cat.is_featured_home);
  document.getElementById('cat_icon').value = cat.icon_name || 'folder';
  document.getElementById('cat_color').value = cat.color_hex || '#6366f1';
  document.getElementById('cat_sort_order').value = cat.sort_order || 0;
  document.getElementById('cat_desc').value = cat.description || '';
  document.getElementById('modalCatTitle').innerText = 'Edit Category #' + cat.id;
  document.getElementById('catFormModal').style.display = 'flex';
}
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
