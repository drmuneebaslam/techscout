<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0 0 0.25rem 0;">Tech & Engineering Jobs Board CRUD</h1>
    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Post engineering opportunities, manage job listings, and toggle homepage visibility.</p>
  </div>

  <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.75rem; align-items: center;">
    <button onclick="openJobModal()" class="ts-btn ts-btn-primary">
      <i data-lucide="plus-circle"></i> Post New Tech Job
    </button>
  </div>
</div>

<!-- Jobs Modal -->
<div id="jobFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h2 id="modalJobTitle" style="font-size: 1.35rem; color: #f8fafc; margin: 0;">Create / Edit Tech Job</h2>
      <button onclick="document.getElementById('jobFormModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form id="adminJobForm" action="<?= url('/admin/jobs/save') ?>" method="POST">
      <input type="hidden" name="id" id="job_id" value="">

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Job Title</label>
          <input type="text" name="title" id="job_title" placeholder="e.g. Senior Distributed Systems Architect" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 1rem;" required>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Company Name</label>
          <input type="text" name="company_name" id="job_company" placeholder="e.g. ScaleAI, Stripe, NeuralLink" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;" required>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Location</label>
          <input type="text" name="location" id="job_location" value="Remote" placeholder="San Francisco, CA / Remote" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Job Type</label>
          <select name="job_type" id="job_type" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
            <option value="Full-time">Full-time</option>
            <option value="Contract">Contract</option>
            <option value="Remote">Remote</option>
            <option value="Part-time">Part-time</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Salary / Compensation</label>
          <input type="text" name="salary_range" id="job_salary" placeholder="$160,000 - $220,000" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Apply / Target URL</label>
        <input type="url" name="apply_url" id="job_apply_url" placeholder="https://jobs.lever.co/company/role" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;" required>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Job Description (HTML allowed)</label>
        <textarea name="description_html" id="job_desc" rows="6" placeholder="<p>Job responsibilities, requirements, and tech stack details...</p>" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: var(--admin-font-mono); font-size: 0.85rem;" required></textarea>
      </div>

      <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 2rem; margin-bottom: 1.5rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; font-size: 0.9rem; cursor: pointer;">
          <input type="checkbox" name="is_featured" id="job_is_featured" value="1">
          <span>Featured Position ⭐</span>
        </label>
        <label style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; font-size: 0.9rem; cursor: pointer;">
          <input type="checkbox" name="is_active" id="job_is_active" value="1" checked>
          <span>Active Listing 🟢</span>
        </label>
      </div>

      <div style="flex-wrap: wrap; align-items: center; display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('jobFormModal').style.display='none'" class="ts-btn ts-btn-outline">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary" style="font-weight: 700;">Save Job Opportunity</button>
      </div>
    </form>
  </div>
</div>

<!-- Jobs Table -->
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Job Title & Company</th>
        <th>Type & Location</th>
        <th>Compensation</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($jobs as $j): ?>
        <tr>
          <td>
            <div style="font-weight: 700; color: #f8fafc; font-size: 0.95rem;">
              <?= htmlspecialchars($j['title']) ?>
            </div>
            <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.2rem;">
              🏢 <?= htmlspecialchars($j['company_name']) ?> &bull; 
              <a href="<?= url('/jobs/' . $j['slug']) ?>" target="_blank" style="color: #38bdf8; text-decoration: none;">/jobs/<?= htmlspecialchars($j['slug']) ?> ↗</a>
            </div>
          </td>
          <td>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;"><?= htmlspecialchars($j['job_type']) ?></span>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">📍 <?= htmlspecialchars($j['location']) ?></div>
          </td>
          <td style="color: #34d399; font-weight: 600; font-size: 0.85rem;">
            <?= htmlspecialchars($j['salary_range'] ?: 'Competitive') ?>
          </td>
          <td>
            <?php if (!empty($j['is_active'])): ?>
              <span class="ts-badge ts-badge-green" style="font-size: 0.7rem;">ACTIVE</span>
            <?php else: ?>
              <span class="ts-badge" style="font-size: 0.7rem; color: #64748b;">INACTIVE</span>
            <?php endif; ?>
            <?php if (!empty($j['is_featured'])): ?>
              <span class="ts-badge ts-badge-pink" style="font-size: 0.7rem; margin-left: 0.25rem;">FEATURED</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; align-items: center;">
              <form action="<?= url('/admin/jobs/toggle/' . $j['id']) ?>" method="POST" style="display: inline;">
                <button type="submit" class="ts-btn <?= !empty($j['is_active']) ? 'ts-btn-outline' : 'ts-btn-primary' ?> ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.72rem;" title="Toggle public visibility without deleting">
                  <?= !empty($j['is_active']) ? 'Hide 🔒' : 'Publish 🟢' ?>
                </button>
              </form>
              <button onclick='editJob(<?= json_encode($j, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                Edit
              </button>
              <form action="<?= url('/admin/jobs/delete/' . $j['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this job?');">
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
function openJobModal() {
  document.getElementById('job_id').value = '';
  document.getElementById('job_title').value = '';
  document.getElementById('job_company').value = '';
  document.getElementById('job_location').value = 'Remote';
  document.getElementById('job_type').value = 'Full-time';
  document.getElementById('job_salary').value = '';
  document.getElementById('job_apply_url').value = '';
  document.getElementById('job_desc').value = '';
  document.getElementById('job_is_featured').checked = false;
  document.getElementById('job_is_active').checked = true;
  document.getElementById('modalJobTitle').innerText = 'Post New Tech Job';
  document.getElementById('jobFormModal').style.display = 'flex';
}

function editJob(job) {
  document.getElementById('job_id').value = job.id;
  document.getElementById('job_title').value = job.title || '';
  document.getElementById('job_company').value = job.company_name || '';
  document.getElementById('job_location').value = job.location || 'Remote';
  document.getElementById('job_type').value = job.job_type || 'Full-time';
  document.getElementById('job_salary').value = job.salary_range || '';
  document.getElementById('job_apply_url').value = job.apply_url || '';
  document.getElementById('job_desc').value = job.description_html || '';
  document.getElementById('job_is_featured').checked = !!parseInt(job.is_featured);
  document.getElementById('job_is_active').checked = !!parseInt(job.is_active);
  document.getElementById('modalJobTitle').innerText = 'Edit Job #' + job.id;
  document.getElementById('jobFormModal').style.display = 'flex';
}
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
