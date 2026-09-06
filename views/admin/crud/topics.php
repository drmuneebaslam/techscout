<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0 0 0.25rem 0;">High-Level Topics Taxonomy</h1>
    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Organize the highest-level domains that bundle sub-categories and power personalized user radar feeds.</p>
  </div>

  <div style="display: flex; gap: 0.75rem;">
    <a href="<?= url('/admin/categories') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
      <i data-lucide="folder-tree"></i> Categories CRUD
    </a>
    <button onclick="openCreateTopicModal()" class="ts-btn ts-btn-primary ts-btn-sm">
      <i data-lucide="plus"></i> Add New Topic
    </button>
  </div>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Topic Name & Slug</th>
        <th>Visual Icon & Color</th>
        <th>Description</th>
        <th>Sub-Categories</th>
        <th>Subscribers</th>
        <th>Sort Order</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($topics)): ?>
        <?php foreach ($topics as $t): ?>
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 0.5rem;">
                <strong style="color: #f8fafc; font-size: 1rem;"><?= htmlspecialchars($t['name']) ?></strong>
                <?php if (!empty($t['is_featured'])): ?>
                  <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">FEATURED</span>
                <?php endif; ?>
              </div>
              <div style="font-size: 0.75rem; color: #38bdf8; font-family: monospace; margin-top: 0.2rem;">
                <a href="<?= url('/topic/' . $t['slug']) ?>" target="_blank" style="color: #38bdf8; text-decoration: none;">/topic/<?= htmlspecialchars($t['slug']) ?> ↗</a>
              </div>
            </td>
            <td>
              <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(15,23,42,0.6); padding: 0.35rem 0.75rem; border-radius: 6px; border: 1px solid rgba(148,163,184,0.15);">
                <div style="width: 16px; height: 16px; border-radius: 50%; background: <?= htmlspecialchars($t['color_hex'] ?: '#6366f1') ?>;"></div>
                <i data-lucide="<?= htmlspecialchars($t['icon_name'] ?: 'layers') ?>" style="width: 16px; height: 16px; color: <?= htmlspecialchars($t['color_hex'] ?: '#6366f1') ?>;"></i>
                <span style="font-size: 0.78rem; font-family: monospace; color: #94a3b8;"><?= htmlspecialchars($t['icon_name'] ?: 'layers') ?></span>
              </div>
            </td>
            <td style="color: #94a3b8; font-size: 0.85rem; max-width: 260px;">
              <?= htmlspecialchars($t['description'] ?: 'No description.') ?>
            </td>
            <td>
              <span class="ts-badge ts-badge-cyan"><?= (int)$t['category_count'] ?> categories</span>
            </td>
            <td>
              <a href="<?= url('/admin/subscribers') ?>" class="ts-badge ts-badge-green" style="text-decoration: none; font-weight: 700; font-size: 0.82rem;" title="Inspect subscribers in Audience Roster">
                <i data-lucide="users" style="width: 12px; display: inline;"></i> <?= (int)$t['subscriber_count'] ?> followers
              </a>
            </td>
            <td style="font-family: monospace; color: #cbd5e1;"><?= (int)$t['sort_order'] ?></td>
            <td>
              <div style="display: flex; gap: 0.35rem;">
                <button onclick='editTopic(<?= json_encode($t, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                  Edit
                </button>
                <form action="<?= url('/admin/topics/delete/' . $t['id']) ?>" method="POST" onsubmit="return confirm('Delete this topic? Sub-categories will be unlinked.');">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #ef4444;">
                    Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">
            No topics defined yet. Click "Add New Topic" to create one.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Create / Edit Topic Modal -->
<div id="topicModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 580px; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.35);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <h2 id="topicModalTitle" style="font-size: 1.3rem; color: #f8fafc; margin: 0;">Add New Topic</h2>
      <button onclick="document.getElementById('topicModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form action="<?= url('/admin/topics/save') ?>" method="POST">
      <input type="hidden" name="id" id="topicId" value="">

      <div style="margin-bottom: 1.1rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Topic Name</label>
        <input type="text" name="name" id="topicName" placeholder="e.g. Autonomous AI & Agents" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;" required>
      </div>

      <div style="margin-bottom: 1.1rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Slug (optional, auto-generated)</label>
        <input type="text" name="slug" id="topicSlug" placeholder="autonomous-ai-agents" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;">
      </div>

      <div style="margin-bottom: 1.1rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Description</label>
        <textarea name="description" id="topicDescription" rows="2" placeholder="Brief technical summary of this domain..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;"></textarea>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.1rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Lucide Icon Name</label>
          <input type="text" name="icon_name" id="topicIcon" placeholder="bot, server, database, layers" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;">
        </div>
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Brand Color Hex</label>
          <input type="text" name="color_hex" id="topicColor" placeholder="#6366f1" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Sort Order Index</label>
          <input type="number" name="sort_order" id="topicSortOrder" value="0" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;">
        </div>
        <div style="display: flex; align-items: flex-end; padding-bottom: 0.5rem;">
          <label style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; font-size: 0.88rem; cursor: pointer;">
            <input type="checkbox" name="is_featured" id="topicIsFeatured" value="1" style="width: 18px; height: 18px; accent-color: var(--ts-primary);">
            <span>Featured Topic Badge</span>
          </label>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('topicModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">Save Topic</button>
      </div>
    </form>
  </div>
</div>

<script>
function openCreateTopicModal() {
  document.getElementById('topicModalTitle').innerText = 'Add New Topic';
  document.getElementById('topicId').value = '';
  document.getElementById('topicName').value = '';
  document.getElementById('topicSlug').value = '';
  document.getElementById('topicDescription').value = '';
  document.getElementById('topicIcon').value = 'layers';
  document.getElementById('topicColor').value = '#6366f1';
  document.getElementById('topicSortOrder').value = '0';
  document.getElementById('topicIsFeatured').checked = false;
  document.getElementById('topicModal').style.display = 'flex';
}

function editTopic(t) {
  document.getElementById('topicModalTitle').innerText = 'Edit Topic: ' + (t.name || '');
  document.getElementById('topicId').value = t.id || '';
  document.getElementById('topicName').value = t.name || '';
  document.getElementById('topicSlug').value = t.slug || '';
  document.getElementById('topicDescription').value = t.description || '';
  document.getElementById('topicIcon').value = t.icon_name || 'layers';
  document.getElementById('topicColor').value = t.color_hex || '#6366f1';
  document.getElementById('topicSortOrder').value = t.sort_order || 0;
  document.getElementById('topicIsFeatured').checked = (t.is_featured == 1);
  document.getElementById('topicModal').style.display = 'flex';
}
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
