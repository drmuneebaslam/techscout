<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0 0 0.25rem 0;">Prompt Blueprints Management (Full CRUD)</h1>
    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Create, edit, test variables, and organize engineering prompts.</p>
  </div>

  <button onclick="openNewPromptModal()" class="ts-btn ts-btn-primary">
    <i data-lucide="plus-circle"></i> Create Prompt Template
  </button>
</div>

<!-- Prompt Modal -->
<div id="promptFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 850px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(16,185,129,0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h2 id="modalPromptTitle" style="font-size: 1.35rem; color: #f8fafc; margin: 0;">Create / Edit Prompt Template</h2>
      <button onclick="document.getElementById('promptFormModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form id="adminPromptForm" action="<?= url('/admin/prompts/save') ?>" method="POST">
      <input type="hidden" name="id" id="prompt_id" value="">

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Prompt Title</label>
        <input type="text" name="title" id="prompt_title" placeholder="e.g. Production MySQL Schema & Index Optimizer" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 1rem;" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Category</label>
          <select name="category_id" id="prompt_category_id" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Target Models JSON</label>
          <input type="text" name="target_models" id="prompt_models" value='["ChatGPT", "Claude 3.5 Sonnet"]' style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: var(--ts-font-mono); font-size: 0.85rem;">
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Summary</label>
        <textarea name="summary" id="prompt_summary" rows="2" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;" required></textarea>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Prompt Blueprint (Use {{variable}} tags)</label>
        <textarea name="prompt_template" id="prompt_template_body" rows="8" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: var(--ts-font-mono); font-size: 0.9rem;" required></textarea>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Template Variables JSON Schema</label>
        <textarea name="template_variables" id="prompt_variables" rows="3" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: var(--ts-font-mono); font-size: 0.85rem;">[{"name":"framework","label":"Target Framework","default":"PHP/MySQL"}]</textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('promptFormModal').style.display='none'" class="ts-btn ts-btn-outline">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary">Save Prompt Blueprint ⚡</button>
      </div>
    </form>
  </div>
</div>

<!-- Prompts Table -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
  <div style="font-weight: 700; color: #f8fafc; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
    <i data-lucide="terminal" style="width: 18px; height: 18px; color: #f59e0b;"></i> Blueprints Index (<?= count($prompts) ?> Templates)
  </div>
  <input type="text" id="promptSearchInput" placeholder="🔍 Search prompts, models, tags..." class="ts-input" style="padding: 0.45rem 0.85rem; font-size: 0.84rem; width: 280px; background: #05070b; border: 1px solid rgba(148,163,184,0.25); border-radius: 8px; color: #f8fafc;">
</div>

<div class="admin-table-wrap">
  <table class="admin-table" id="promptsCrudTable" data-paginate="true" data-search-id="promptSearchInput" data-page-size="10">
    <thead>
      <tr>
        <th>Prompt Name</th>
        <th>Category</th>
        <th>Target Models</th>
        <th>Copies</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($prompts as $p): ?>
        <tr>
          <td>
            <div style="font-weight: 600; color: #f8fafc; font-size: 0.95rem;">
              <a href="<?= url('/prompts/' . $p['slug']) ?>" target="_blank" style="color: #f8fafc;"><?= htmlspecialchars($p['title']) ?> ↗</a>
            </div>
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">
              <?= htmlspecialchars(substr($p['summary'], 0, 75)) ?>...
            </div>
          </td>
          <td><span class="ts-badge"><?= htmlspecialchars($p['category_name'] ?? 'Prompt') ?></span></td>
          <td>
            <span style="font-family: var(--ts-font-mono); font-size: 0.8rem; color: #38bdf8;"><?= htmlspecialchars($p['target_models'] ?? '["All"]') ?></span>
          </td>
          <td><strong style="color: #34d399;"><?= number_format($p['copy_count'] ?? 0) ?></strong></td>
          <td>
            <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
              <a href="<?= url('/admin/analytics/content/' . $p['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="View Telemetry">
                📊 Stats
              </a>
              <button onclick='editPrompt(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                Edit
              </button>
              <form action="<?= url('/admin/prompts/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Delete this prompt blueprint?');">
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
function openNewPromptModal() {
  document.getElementById('adminPromptForm').reset();
  document.getElementById('prompt_id').value = '';
  document.getElementById('prompt_category_id').value = document.getElementById('prompt_category_id').options[0]?.value || '1';
  document.getElementById('prompt_models').value = '["ChatGPT", "Claude 3.5 Sonnet"]';
  document.getElementById('prompt_variables').value = '[{"name":"framework","label":"Target Framework","default":"PHP/MySQL"}]';
  document.getElementById('modalPromptTitle').innerText = 'Create New Prompt Blueprint';
  document.getElementById('promptFormModal').style.display = 'flex';
}

function editPrompt(p) {
  document.getElementById('adminPromptForm').reset();
  document.getElementById('prompt_id').value = p.id;
  document.getElementById('prompt_title').value = p.title || '';
  document.getElementById('prompt_category_id').value = p.category_id || 1;
  document.getElementById('prompt_summary').value = p.summary || '';
  document.getElementById('prompt_template_body').value = p.prompt_template || '';
  document.getElementById('prompt_variables').value = p.template_variables || '[]';
  document.getElementById('prompt_models').value = p.target_models || '[]';
  document.getElementById('modalPromptTitle').innerText = 'Edit Prompt #' + p.id;
  document.getElementById('promptFormModal').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
  window.promptsPager = window.paginateAdminTable('promptsCrudTable', {
    pageSize: 10,
    searchInputId: 'promptSearchInput'
  });
});
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
