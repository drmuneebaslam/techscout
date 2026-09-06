<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Top Bar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink">CREATOR ONBOARDING</span>
        <span style="font-size: 0.8rem; color: #64748b;">Community Growth Pipeline</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0;">Author Applications & Creator Approvals</h1>
    </div>

    <div style="display: flex; gap: 0.75rem;">
      <a href="<?= url('/admin/users') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="users"></i> Manage All Users
      </a>
      <a href="<?= url('/admin/content') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="check-square"></i> Review Content Queue
      </a>
    </div>
  </div>

  <!-- Summary Metric Stats -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Pending Review</div>
      <div class="admin-stat-val" style="color: #f59e0b;"><?= $counts['pending'] ?></div>
      <div style="font-size: 0.8rem; color: #fbbf24;">Awaiting editorial verdict</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Approved Authors</div>
      <div class="admin-stat-val" style="color: #34d399;"><?= $counts['approved'] ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Elevated creator accounts</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Revision Requested</div>
      <div class="admin-stat-val" style="color: #ef4444;"><?= $counts['rejected'] ?></div>
      <div style="font-size: 0.8rem; color: #f87171;">Feedback sent to applicants</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Submissions</div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= $counts['all'] ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">All-time applications</div>
    </div>
  </div>

  <!-- Filter Ribbon Tabs -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/applications') ?>" class="ts-btn ts-btn-sm <?= empty($currentStatus) ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        All Applications (<?= $counts['all'] ?>)
      </a>
      <a href="<?= url('/admin/applications?status=pending') ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'pending' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="<?= $currentStatus === 'pending' ? '' : 'color: #f59e0b; border-color: rgba(245,158,11,0.3);' ?>">
        ⏳ Pending Review (<?= $counts['pending'] ?>)
      </a>
      <a href="<?= url('/admin/applications?status=approved') ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'approved' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="<?= $currentStatus === 'approved' ? '' : 'color: #34d399; border-color: rgba(16,185,129,0.3);' ?>">
        ✅ Approved (<?= $counts['approved'] ?>)
      </a>
      <a href="<?= url('/admin/applications?status=rejected') ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'rejected' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="<?= $currentStatus === 'rejected' ? '' : 'color: #ef4444; border-color: rgba(239,68,68,0.3);' ?>">
        ⚠️ Revisions (<?= $counts['rejected'] ?>)
      </a>
    </div>
  </div>

  <!-- Cards Grid View -->
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <?php if (!empty($applications)): ?>
      <?php foreach ($applications as $app): ?>
        <?php 
          $appId = (int)$app['id'];
          $isPending = ($app['status'] === 'pending');
          $isApproved = ($app['status'] === 'approved');
          $isRejected = ($app['status'] === 'rejected');
        ?>
        <div class="ts-card" style="display: flex; flex-direction: column; background: #090d16; border: 1px solid <?= $isPending ? 'rgba(245,158,11,0.35)' : ($isApproved ? 'rgba(16,185,129,0.25)' : 'rgba(239,68,68,0.25)') ?>; border-radius: 12px; padding: 1.5rem;">
          
          <!-- Card Top: Avatar, Name & Status Badge -->
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #ec4899, #6366f1); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; color: #fff; flex-shrink: 0;">
                <?= strtoupper(substr($app['display_name'], 0, 1)) ?>
              </div>
              <div>
                <h3 style="font-size: 1.05rem; color: #f8fafc; margin: 0; line-height: 1.2;">
                  <?= htmlspecialchars($app['display_name']) ?>
                </h3>
                <span style="font-size: 0.8rem; color: #64748b; font-family: monospace;">@<?= htmlspecialchars($app['username']) ?></span>
              </div>
            </div>

            <?php if ($isPending): ?>
              <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">PENDING</span>
            <?php elseif ($isApproved): ?>
              <span class="ts-badge ts-badge-green" style="font-size: 0.65rem;">APPROVED</span>
            <?php else: ?>
              <span class="ts-badge" style="background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); font-size: 0.65rem;">REVISION</span>
            <?php endif; ?>
          </div>

          <!-- Headline & Bio Snippet -->
          <div style="font-size: 0.88rem; color: #38bdf8; font-weight: 600; margin-bottom: 0.5rem;">
            <?= htmlspecialchars($app['headline'] ?: 'Technical Writer & Scout') ?>
          </div>
          <p style="font-size: 0.84rem; color: #94a3b8; line-height: 1.5; margin: 0 0 1rem 0; flex: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
            <?= htmlspecialchars($app['bio'] ?: 'No bio provided.') ?>
          </p>

          <!-- Metadata Tags -->
          <div style="background: rgba(15,23,42,0.6); padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.08); font-size: 0.78rem; margin-bottom: 1.25rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
              <span style="color: #64748b;">Specialization:</span>
              <span style="color: #f8fafc; font-weight: 600;"><?= htmlspecialchars($app['specializations'] ?: 'General Engineering') ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
              <span style="color: #64748b;">Applied At:</span>
              <span style="color: #cbd5e1; font-family: monospace;"><?= \App\Core\DateHelper::format($app['created_at']) ?></span>
            </div>
            <div style="display: flex; gap: 0.35rem; margin-top: 0.5rem; flex-wrap: wrap;">
              <?php if (!empty($app['requested_prompts'])): ?><span class="ts-badge ts-badge-green" style="font-size: 0.62rem;">Prompts</span><?php endif; ?>
              <?php if (!empty($app['requested_gpts'])): ?><span class="ts-badge ts-badge-cyan" style="font-size: 0.62rem;">GPTs</span><?php endif; ?>
              <?php if (!empty($app['requested_tools'])): ?><span class="ts-badge ts-badge-pink" style="font-size: 0.62rem;">Tools</span><?php endif; ?>
            </div>
          </div>

          <!-- Bottom Action Buttons -->
          <div style="display: flex; gap: 0.6rem; align-items: center; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 1rem; margin-top: auto;">
            <button type="button" onclick='openApplicationModal(<?= json_encode($app, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-primary ts-btn-sm" style="flex: 1; font-weight: 700;">
              <i data-lucide="eye" style="width: 13px;"></i> Inspect & Decide 🔍
            </button>
            <form action="<?= url('/admin/applications/action/' . $appId) ?>" method="POST" onsubmit="return confirm('Permanently delete this application record?');">
              <input type="hidden" name="action" value="delete">
              <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.6rem; color: #ef4444;" title="Delete Application">
                <i data-lucide="trash-2" style="width: 13px;"></i>
              </button>
            </form>
          </div>

        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="ts-card" style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: #64748b;">
        <i data-lucide="user-check" style="width: 48px; height: 48px; color: var(--ts-primary); margin-bottom: 1rem;"></i>
        <h3 style="color: #f8fafc; margin-bottom: 0.5rem;">No Applications Found</h3>
        <p style="margin: 0;">There are currently no author submissions matching this filter.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Complete Interactive Application Review Modal -->
<div id="applicationReviewModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(10px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 860px; max-height: 92vh; overflow-y: auto; padding: 2.25rem; background: #090d16; border: 1px solid rgba(99,102,241,0.4); box-shadow: 0 25px 60px rgba(0,0,0,0.8);">
    
    <!-- Modal Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 1rem;">
      <div style="display: flex; align-items: center; gap: 1rem;">
        <div id="modalAvatar" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #ec4899, #6366f1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; color: #fff;">
          A
        </div>
        <div>
          <h2 id="modalDisplayName" style="font-size: 1.35rem; color: #f8fafc; margin: 0;">Applicant Name</h2>
          <div style="font-size: 0.85rem; color: #38bdf8; margin-top: 0.2rem;" id="modalHeadline">Lead Engineer</div>
        </div>
      </div>
      <button onclick="document.getElementById('applicationReviewModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.75rem; cursor: pointer; line-height: 1;">&times;</button>
    </div>

    <!-- Application Body Content Details -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
      <div style="background: rgba(15,23,42,0.6); padding: 1.25rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1);">
        <h4 style="color: #f8fafc; font-size: 0.95rem; margin: 0 0 0.75rem 0;">Applicant Profile & Links</h4>
        <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.5rem;">
          <div><strong style="color: #64748b;">Username:</strong> <span id="modalUsername" style="color: #f8fafc; font-family: monospace;"></span></div>
          <div><strong style="color: #64748b;">Specializations:</strong> <span id="modalSpecializations" style="color: #38bdf8;"></span></div>
          <div id="modalPortfolioWrap"><strong style="color: #64748b;">Portfolio / Website:</strong> <a id="modalPortfolio" href="#" target="_blank" style="color: #38bdf8; text-decoration: underline;">Link ↗</a></div>
          <div id="modalGithubWrap"><strong style="color: #64748b;">GitHub:</strong> <a id="modalGithub" href="#" target="_blank" style="color: #38bdf8; text-decoration: underline;">Profile ↗</a></div>
          <div id="modalLinkedinWrap"><strong style="color: #64748b;">LinkedIn:</strong> <a id="modalLinkedin" href="#" target="_blank" style="color: #38bdf8; text-decoration: underline;">Profile ↗</a></div>
        </div>
      </div>

      <div style="background: rgba(15,23,42,0.6); padding: 1.25rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1);">
        <h4 style="color: #f8fafc; font-size: 0.95rem; margin: 0 0 0.75rem 0;">Author Bio</h4>
        <div id="modalBio" style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.6; max-height: 140px; overflow-y: auto;"></div>
      </div>
    </div>

    <!-- Writing Sample / Article Pitch -->
    <div style="background: rgba(15,23,42,0.6); padding: 1.25rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1); margin-bottom: 1.5rem;">
      <h4 style="color: #f8fafc; font-size: 0.95rem; margin: 0 0 0.75rem 0;">Proposed Article Pitch / Technical Sample</h4>
      <div id="modalSamplePitch" style="font-size: 0.88rem; color: #cbd5e1; line-height: 1.6; white-space: pre-wrap; max-height: 180px; overflow-y: auto; background: #05070b; padding: 1rem; border-radius: 6px;"></div>
      <div id="modalSampleFileWrap" style="margin-top: 0.75rem; display: none;">
        <a id="modalSampleFile" href="#" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="border-color: #38bdf8; color: #38bdf8;">
          <i data-lucide="download"></i> Download Attached Sample Document (.docx) 📄
        </a>
      </div>
    </div>

    <!-- Action Forms Tabs -->
    <div style="background: #05070b; border: 1px solid rgba(99,102,241,0.3); border-radius: 10px; padding: 1.5rem;">
      <div style="display: flex; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.1); padding-bottom: 0.75rem;">
        <button type="button" onclick="switchDecisionTab('approve')" id="dec-tab-approve" class="ts-btn ts-btn-sm ts-btn-primary" style="font-weight: 700;">
          <i data-lucide="check-circle"></i> Approve as Author 🚀
        </button>
        <button type="button" onclick="switchDecisionTab('revision')" id="dec-tab-revision" class="ts-btn ts-btn-sm ts-btn-outline" style="color: #f59e0b; border-color: rgba(245,158,11,0.3);">
          <i data-lucide="alert-triangle"></i> Request Revision ⚠️
        </button>
        <button type="button" onclick="switchDecisionTab('reject')" id="dec-tab-reject" class="ts-btn ts-btn-sm ts-btn-outline" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">
          <i data-lucide="x-circle"></i> Reject Application ✕
        </button>
      </div>

      <!-- Approve Form -->
      <form id="approveForm" method="POST" action="">
        <input type="hidden" name="action" value="approve">
        <div id="pane-approve">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #94a3b8; margin-bottom: 0.35rem;">Creator Promo Credits</label>
              <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.2); padding: 0.55rem 0.75rem; border-radius: 6px; color: #94a3b8; font-size: 0.82rem;">
                $0.00 USD (Admin Manual Credit Only)
              </div>
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Initial Reputation Badge</label>
              <select name="grant_badge" style="width: 100%; background: #090d16; border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem; border-radius: 6px; color: #f8fafc;">
                <option value="verified_builder">Verified Builder 🛡️</option>
                <option value="lead_architect">Lead Architect 🏛️</option>
                <option value="ai_researcher">AI Researcher 🔬</option>
                <option value="staff_writer">Staff Editorial Scout ✍️</option>
              </select>
            </div>
          </div>

          <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1); margin-bottom: 1rem;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem;">Granular Creator Permissions:</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; font-size: 0.85rem; color: #f8fafc;">
              <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="grant_templates" value="1" checked style="accent-color: var(--ts-primary);">
                <span>Allow Custom Architecture Templates ⭐</span>
              </label>
              <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="grant_prompts" value="1" checked style="accent-color: var(--ts-primary);">
                <span>Publish Prompt Blueprints</span>
              </label>
              <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="grant_gpts" value="1" checked style="accent-color: var(--ts-primary);">
                <span>Publish Custom GPTs</span>
              </label>
              <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="grant_tools" value="1" checked style="accent-color: var(--ts-primary);">
                <span>Publish AI Tools & Software</span>
              </label>
            </div>
          </div>

          <div style="margin-bottom: 1rem;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Welcome Message & Editorial Notes</label>
            <input type="text" name="admin_feedback" value="Welcome to the TechScout Author Guild!" style="width: 100%; background: #090d16; border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;">
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button type="button" onclick="document.getElementById('applicationReviewModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
            <button type="submit" class="ts-btn ts-btn-primary" style="padding: 0.6rem 1.5rem; font-weight: 700;">Confirm & Approve Creator Account 🚀</button>
          </div>
        </div>
      </form>

      <!-- Revision / Reject Form -->
      <form id="actionForm" method="POST" action="">
        <input type="hidden" name="action" id="actionFormType" value="reject">
        <div id="pane-feedback" style="display: none;">
          <div style="margin-bottom: 1rem;">
            <label id="feedbackLabel" style="display: block; font-size: 0.8rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Feedback for Applicant</label>
            <textarea name="admin_feedback" id="feedbackText" rows="3" placeholder="Specify requirements or improvements needed..." style="width: 100%; background: #090d16; border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;" required></textarea>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button type="button" onclick="document.getElementById('applicationReviewModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
            <button type="submit" id="feedbackSubmitBtn" class="ts-btn ts-btn-primary" style="padding: 0.6rem 1.5rem; font-weight: 700;">Submit Decision</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>

<script>
function openApplicationModal(app) {
  const modal = document.getElementById('applicationReviewModal');
  document.getElementById('modalAvatar').innerText = (app.display_name || 'A').charAt(0).toUpperCase();
  document.getElementById('modalDisplayName').innerText = app.display_name || 'Applicant';
  document.getElementById('modalHeadline').innerText = app.headline || 'Technical Writer';
  document.getElementById('modalUsername').innerText = '@' + (app.username || 'user');
  document.getElementById('modalSpecializations').innerText = app.specializations || 'General Engineering';
  document.getElementById('modalBio').innerText = app.bio || 'No bio provided.';
  document.getElementById('modalSamplePitch').innerText = app.sample_pitch || 'No sample text pitch provided.';

  // Links
  const portWrap = document.getElementById('modalPortfolioWrap');
  if (app.portfolio_url) {
    portWrap.style.display = 'block';
    document.getElementById('modalPortfolio').href = app.portfolio_url;
  } else {
    portWrap.style.display = 'none';
  }

  const gitWrap = document.getElementById('modalGithubWrap');
  if (app.github_handle) {
    gitWrap.style.display = 'block';
    document.getElementById('modalGithub').href = 'https://github.com/' + app.github_handle.replace('@','');
  } else {
    gitWrap.style.display = 'none';
  }

  const inWrap = document.getElementById('modalLinkedinWrap');
  if (app.linkedin_url) {
    inWrap.style.display = 'block';
    document.getElementById('modalLinkedin').href = app.linkedin_url;
  } else {
    inWrap.style.display = 'none';
  }

  // Sample docx file link
  const fileWrap = document.getElementById('modalSampleFileWrap');
  if (app.sample_file_url) {
    fileWrap.style.display = 'block';
    document.getElementById('modalSampleFile').href = app.sample_file_url;
  } else {
    fileWrap.style.display = 'none';
  }

  // Set action routes
  const baseUrl = '<?= url('/admin/applications/action/') ?>' + app.id;
  document.getElementById('approveForm').action = baseUrl;
  document.getElementById('actionForm').action = baseUrl;

  switchDecisionTab('approve');
  modal.style.display = 'flex';
  if (typeof lucide !== 'undefined') lucide.createIcons();
}

function switchDecisionTab(tab) {
  const paneApprove = document.getElementById('pane-approve');
  const paneFeedback = document.getElementById('pane-feedback');
  const tabApprove = document.getElementById('dec-tab-approve');
  const tabRev = document.getElementById('dec-tab-revision');
  const tabRej = document.getElementById('dec-tab-reject');
  const formType = document.getElementById('actionFormType');
  const submitBtn = document.getElementById('feedbackSubmitBtn');
  const label = document.getElementById('feedbackLabel');
  const text = document.getElementById('feedbackText');

  [tabApprove, tabRev, tabRej].forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });

  if (tab === 'approve') {
    tabApprove.classList.add('ts-btn-primary');
    tabApprove.classList.remove('ts-btn-outline');
    paneApprove.style.display = 'block';
    paneFeedback.style.display = 'none';
  } else if (tab === 'revision') {
    tabRev.classList.add('ts-btn-primary');
    tabRev.classList.remove('ts-btn-outline');
    paneApprove.style.display = 'none';
    paneFeedback.style.display = 'block';
    formType.value = 'reject';
    label.innerText = 'Revision Feedback (Applicant will be notified to update pitch)';
    text.value = 'Please provide a more detailed architectural sample and expand your portfolio links.';
    submitBtn.innerText = 'Send Revision Request ⚠️';
    submitBtn.style.background = '#f59e0b';
  } else if (tab === 'reject') {
    tabRej.classList.add('ts-btn-primary');
    tabRej.classList.remove('ts-btn-outline');
    paneApprove.style.display = 'none';
    paneFeedback.style.display = 'block';
    formType.value = 'reject';
    label.innerText = 'Rejection Reason / Notes';
    text.value = 'Thank you for your submission. At this time, your application does not match our current editorial criteria.';
    submitBtn.innerText = 'Confirm Application Rejection ✕';
    submitBtn.style.background = '#ef4444';
  }
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
