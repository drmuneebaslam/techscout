<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0 0 0.25rem 0;">Custom GPTs & Autonomous Agents CRUD</h1>
    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Publish and maintain OpenAI custom GPTs and intelligent agent deep links.</p>
  </div>

  <button onclick="openNewGptModal()" class="ts-btn ts-btn-primary">
    <i data-lucide="plus-circle"></i> Add Custom GPT
  </button>
</div>

<!-- GPT Modal -->
<div id="gptFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 750px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(56,189,248,0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h2 id="modalGptTitle" style="font-size: 1.35rem; color: #f8fafc; margin: 0;">Add / Edit Custom GPT</h2>
      <button onclick="document.getElementById('gptFormModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form id="adminGptForm" action="<?= url('/admin/gpts/save') ?>" method="POST">
      <input type="hidden" name="id" id="gpt_id" value="">

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">GPT Name <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 100 chars)</span></label>
        <input type="text" name="title" id="gpt_title" maxlength="100" placeholder="e.g. SQL Query Refactoring Architect" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 1rem;" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Foundation Model Base</label>
          <input type="text" name="model_base" id="gpt_model_base" value="GPT-4o" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Creator Handle</label>
          <input type="text" name="creator_handle" id="gpt_creator_handle" value="@techscout" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">OpenAI Direct Deep Link URL</label>
        <input type="url" name="openai_url" id="gpt_openai_url" placeholder="https://chatgpt.com/g/g-..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;" required>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <label style="font-size: 0.85rem; font-weight: 600; color: #f8fafc;">GPT Avatar / Icon Image URL</label>
          <label class="ts-btn ts-btn-outline ts-btn-sm" style="cursor: pointer; padding: 0.15rem 0.5rem; font-size: 0.72rem; border-color: #38bdf8; color: #38bdf8; display: inline-flex; align-items: center; gap: 0.25rem;">
            <i data-lucide="upload" style="width: 12px; height: 12px;"></i> Upload Icon
            <input type="file" accept="image/*" onchange="uploadImageToInput(this, 'gpt_featured_image_url', 'gpts')" style="display: none;">
          </label>
        </div>
        <input type="url" name="featured_image_url" id="gpt_featured_image_url" placeholder="https://.../gpt-icon.png" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Summary / Capabilities <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 300 chars)</span></label>
        <textarea name="summary" id="gpt_summary" maxlength="300" rows="3" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;" required></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('gptFormModal').style.display='none'" class="ts-btn ts-btn-outline">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary">Save GPT Agent</button>
      </div>
    </form>
  </div>
</div>

<!-- GPT Table -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
  <div style="font-weight: 700; color: #f8fafc; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
    <i data-lucide="bot" style="width: 18px; height: 18px; color: #a855f7;"></i> Custom GPTs Directory (<?= count($gpts) ?> Agents)
  </div>
  <input type="text" id="gptSearchInput" placeholder="🔍 Search GPTs, creators, models..." class="ts-input" style="padding: 0.45rem 0.85rem; font-size: 0.84rem; width: 280px; background: #05070b; border: 1px solid rgba(148,163,184,0.25); border-radius: 8px; color: #f8fafc;">
</div>

<div class="admin-table-wrap">
  <table class="admin-table" id="gptsCrudTable" data-paginate="true" data-search-id="gptSearchInput" data-page-size="10">
    <thead>
      <tr>
        <th>Agent Name</th>
        <th>Model Base</th>
        <th>Creator</th>
        <th>OpenAI Link</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($gpts as $g): ?>
        <tr>
          <td>
            <div style="font-weight: 600; font-size: 0.95rem;">
              <a href="<?= url('/gpts/' . $g['slug']) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;">
                <?= htmlspecialchars($g['title']) ?> <span style="font-size: 0.75rem; color: #38bdf8;">↗</span>
              </a>
            </div>
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;"><?= htmlspecialchars($g['summary']) ?></div>
          </td>
          <td><span class="ts-badge ts-badge-cyan"><?= htmlspecialchars($g['model_base']) ?></span></td>
          <td><strong style="color: #94a3b8; font-size: 0.85rem;"><?= htmlspecialchars($g['creator_handle']) ?></strong></td>
          <td>
            <a href="<?= url('/go/' . $g['slug']) ?>" target="_blank" style="color: #38bdf8; font-size: 0.85rem;">Launch GPT ↗</a>
          </td>
          <td>
            <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
              <a href="<?= url('/admin/analytics/content/' . $g['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="View Telemetry">
                📊 Stats
              </a>
              <button onclick='editGpt(<?= json_encode($g, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                Edit
              </button>
              <form action="<?= url('/admin/gpts/delete/' . $g['id']) ?>" method="POST" onsubmit="return confirm('Delete this custom GPT?');">
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
function openNewGptModal() {
  document.getElementById('adminGptForm').reset();
  document.getElementById('gpt_id').value = '';
  document.getElementById('modalGptTitle').innerText = 'Add New Custom GPT Agent';
  document.getElementById('gptFormModal').style.display = 'flex';
}

function editGpt(g) {
  document.getElementById('adminGptForm').reset();
  document.getElementById('gpt_id').value = g.id;
  document.getElementById('gpt_title').value = g.title || '';
  document.getElementById('gpt_openai_url').value = g.openai_url || '';
  document.getElementById('gpt_model_base').value = g.model_base || 'GPT-4o';
  document.getElementById('gpt_creator_handle').value = g.creator_handle || '@techscout';
  document.getElementById('gpt_summary').value = g.summary || '';
  document.getElementById('modalGptTitle').innerText = 'Edit GPT #' + g.id;
  document.getElementById('gptFormModal').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
  window.gptsPager = window.paginateAdminTable('gptsCrudTable', {
    pageSize: 10,
    searchInputId: 'gptSearchInput'
  });
});
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
