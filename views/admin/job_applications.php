<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0 0 0.25rem 0;">Candidate Job Applications</h1>
    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Review direct on-site candidate submissions for TechScout engineering and editorial roles.</p>
  </div>

  <a href="<?= url('/admin/jobs') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
    <i data-lucide="briefcase"></i> Manage Jobs Board
  </a>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Candidate & Email</th>
        <th>Applied Position</th>
        <th>Portfolio & Resume</th>
        <th>Status</th>
        <th>Submitted</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($applications)): ?>
        <?php foreach ($applications as $app): ?>
          <tr>
            <td>
              <div style="color: #f8fafc; font-weight: 700; font-size: 0.95rem;"><?= htmlspecialchars($app['applicant_name']) ?></div>
              <div style="font-size: 0.8rem; color: #38bdf8;"><?= htmlspecialchars($app['applicant_email']) ?></div>
            </td>
            <td>
              <a href="<?= url('/jobs/' . $app['job_slug']) ?>" target="_blank" style="color: #f8fafc; font-weight: 600; text-decoration: underline;">
                <?= htmlspecialchars($app['job_title']) ?> ↗
              </a>
            </td>
            <td>
              <div style="font-size: 0.85rem;">
                <?php if (!empty($app['portfolio_url'])): ?>
                  <a href="<?= htmlspecialchars($app['portfolio_url']) ?>" target="_blank" style="color: #38bdf8; display: block; margin-bottom: 0.2rem;">Portfolio / GitHub ↗</a>
                <?php endif; ?>
                <span style="color: #94a3b8; font-size: 0.78rem;">Resume provided</span>
              </div>
            </td>
            <td>
              <?php 
                $statusColors = [
                  'pending' => 'ts-badge-pink',
                  'reviewed' => 'ts-badge-cyan',
                  'interviewing' => 'ts-badge-pink',
                  'accepted' => 'ts-badge-green',
                  'rejected' => 'ts-badge'
                ];
                $badgeClass = $statusColors[$app['status']] ?? 'ts-badge-cyan';
              ?>
              <span class="ts-badge <?= $badgeClass ?>" style="font-size: 0.7rem;"><?= strtoupper(htmlspecialchars($app['status'])) ?></span>
            </td>
            <td style="font-size: 0.8rem; color: #64748b; font-family: monospace;">
              <?= \App\Core\DateHelper::format($app['created_at']) ?>
            </td>
            <td>
              <button type="button" onclick='inspectCandidate(<?= json_encode($app, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                Inspect & Review 🔍
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
            <i data-lucide="user-x" style="width: 36px; height: 36px; color: var(--ts-primary); margin-bottom: 0.5rem;"></i>
            <div>No candidate job applications received yet.</div>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Candidate Inspection Modal -->
<div id="candidateModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 720px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.35);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <h2 id="candModalName" style="font-size: 1.3rem; color: #f8fafc; margin: 0;">Candidate Review</h2>
      <button onclick="document.getElementById('candidateModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.88rem;">
      <div><strong style="color: #64748b;">Applied For:</strong> <span id="candModalJob" style="color: #f8fafc; font-weight: 700;"></span></div>
      <div style="margin-top: 0.35rem;"><strong style="color: #64748b;">Email:</strong> <span id="candModalEmail" style="color: #38bdf8;"></span></div>
      <div style="margin-top: 0.35rem;"><strong style="color: #64748b;">Portfolio Link:</strong> <a id="candModalPortfolio" href="#" target="_blank" style="color: #38bdf8; text-decoration: underline;">Link ↗</a></div>
    </div>

    <div style="margin-bottom: 1.25rem;">
      <h4 style="color: #f8fafc; font-size: 0.9rem; margin: 0 0 0.4rem 0;">Resume & Technical Experience:</h4>
      <div id="candModalResume" style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 6px; padding: 1rem; color: #cbd5e1; line-height: 1.6; font-size: 0.88rem; max-height: 160px; overflow-y: auto; white-space: pre-wrap;"></div>
    </div>

    <div style="margin-bottom: 1.5rem;">
      <h4 style="color: #f8fafc; font-size: 0.9rem; margin: 0 0 0.4rem 0;">Cover Letter / Why TechScout:</h4>
      <div id="candModalCover" style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 6px; padding: 1rem; color: #cbd5e1; line-height: 1.6; font-size: 0.88rem; max-height: 140px; overflow-y: auto; white-space: pre-wrap;"></div>
    </div>

    <form id="candStatusForm" method="POST" action="">
      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Update Application Status</label>
          <select name="status" id="candStatusSelect" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem; border-radius: 6px; color: #f8fafc;">
            <option value="pending">Pending</option>
            <option value="reviewed">Reviewed</option>
            <option value="interviewing">Interviewing 🎤</option>
            <option value="accepted">Accepted / Hired 🎉</option>
            <option value="rejected">Rejected ✕</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Internal Editorial / Interview Notes</label>
          <input type="text" name="admin_notes" id="candAdminNotes" placeholder="Interview scheduled for Thursday..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;">
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('candidateModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">Save Candidate Status</button>
      </div>
    </form>
  </div>
</div>

<script>
function inspectCandidate(app) {
  document.getElementById('candModalName').innerText = app.applicant_name || 'Candidate';
  document.getElementById('candModalJob').innerText = app.job_title || 'Position';
  document.getElementById('candModalEmail').innerText = app.applicant_email || '';
  document.getElementById('candModalResume').innerText = app.resume_text_or_url || 'No resume text';
  document.getElementById('candModalCover').innerText = app.cover_letter || 'No cover letter';
  document.getElementById('candStatusSelect').value = app.status || 'pending';
  document.getElementById('candAdminNotes').value = app.admin_notes || '';

  const port = document.getElementById('candModalPortfolio');
  if (app.portfolio_url) {
    port.style.display = 'inline';
    port.href = app.portfolio_url;
  } else {
    port.style.display = 'none';
  }

  document.getElementById('candStatusForm').action = '<?= url('/admin/jobs/applications/update/') ?>' + app.id;
  document.getElementById('candidateModal').style.display = 'flex';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
