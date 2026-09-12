<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<?php
  $activeTab = $activeTab ?? 'campaigns';
  $totalSubmissions = count($submissions ?? []);
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <span class="ts-badge ts-badge-pink">HACKATHONS &amp; CAMPAIGNS HUB</span>
      <span style="font-size: 0.8rem; color: #64748b;">Developer Challenges, Submission Governance &amp; Viral Referrals</span>
    </div>
    <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
      Developer Challenges &amp; Submissions Station
    </h1>
  </div>

  <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
    <button onclick="openNewCampModal()" class="ts-btn ts-btn-primary">
      <i data-lucide="plus-circle"></i> Launch New Challenge
    </button>
  </div>
</div>

<!-- Navigation Tabs -->
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.75rem; border-bottom: 1px solid rgba(148,163,184,0.12); padding-bottom: 0.75rem;">
  <button type="button" onclick="switchAdminCampTab('campaigns')" id="tab_btn_campaigns" class="ts-btn ts-btn-sm <?= $activeTab === 'campaigns' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 999px; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem;">
    <i data-lucide="trophy" style="width: 14px; height: 14px;"></i>
    <span>Active Challenges</span>
    <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; padding: 0.1rem 0.4rem;"><?= count($campaigns ?? []) ?></span>
  </button>

  <button type="button" onclick="switchAdminCampTab('submissions')" id="tab_btn_submissions" class="ts-btn ts-btn-sm <?= $activeTab === 'submissions' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 999px; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem;">
    <i data-lucide="inbox" style="width: 14px; height: 14px;"></i>
    <span>Participant Submissions Hub</span>
    <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem; padding: 0.1rem 0.4rem;"><?= $totalSubmissions ?></span>
  </button>
</div>

<!-- TAB 1: ACTIVE CHALLENGES TABLE -->
<div id="pane_campaigns" class="camp-pane" style="<?= $activeTab === 'campaigns' ? '' : 'display: none;' ?>">
  <div class="admin-table-wrap">
    <table class="admin-table" id="campaignsCrudTable" data-paginate="true" data-page-size="10">
      <thead>
        <tr>
          <th>Challenge Title</th>
          <th>Type</th>
          <th>Prize Pool</th>
          <th>Duration Window</th>
          <th style="text-align: center;">Submissions</th>
          <th>Features Configured</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($campaigns)): ?>
          <?php foreach ($campaigns as $c): ?>
            <tr>
              <td>
                <div style="font-weight: 700; color: #f8fafc; font-size: 0.95rem;">
                  <a href="<?= url('/campaigns/' . $c['slug']) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;">
                    <?= htmlspecialchars($c['title']) ?> ↗
                  </a>
                </div>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  <?= htmlspecialchars($c['summary']) ?>
                </div>
              </td>
              <td>
                <span class="ts-badge ts-badge-pink" style="text-transform: uppercase; font-size: 0.7rem;">
                  <?= htmlspecialchars($c['campaign_type']) ?>
                </span>
              </td>
              <td><strong style="color: #34d399; font-size: 0.95rem; font-family: 'Outfit', sans-serif;"><?= htmlspecialchars($c['prize_pool']) ?></strong></td>
              <td style="font-size: 0.8rem; color: #94a3b8; font-family: var(--admin-font-mono);">
                <?= \App\Core\DateHelper::render($c['start_date'], 'M j', 'date') ?> – <?= \App\Core\DateHelper::render($c['end_date'], 'M j, Y', 'date') ?>
              </td>
              <td style="text-align: center;">
                <span class="ts-badge ts-badge-cyan" style="font-weight: 700; font-size: 0.75rem;">
                  <?= (int)($c['submissions_count'] ?? 0) ?> Entries
                </span>
              </td>
              <td>
                <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                  <?php if (!empty($c['enable_referral_system'])): ?>
                    <span class="ts-badge ts-badge-green" style="font-size: 0.65rem;">Viral Referrals</span>
                  <?php endif; ?>
                  <?php if (!empty($c['enable_public_voting'])): ?>
                    <span class="ts-badge ts-badge-amber" style="font-size: 0.65rem;">Public Voting</span>
                  <?php endif; ?>
                </div>
              </td>
              <td style="text-align: right;">
                <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                  <button onclick='editCamp(<?= json_encode($c, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;">
                    Edit
                  </button>
                  <form action="<?= url('/admin/campaigns/delete/' . $c['id']) ?>" method="POST" onsubmit="return confirm('Delete this challenge?');" style="display: inline;">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.75rem; color: #ef4444; border-color: rgba(239,68,68,0.25);">
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
              No developer challenges launched yet. Click "Launch New Challenge" above.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- TAB 2: PARTICIPANT SUBMISSIONS HUB -->
<div id="pane_submissions" class="camp-pane" style="<?= $activeTab === 'submissions' ? '' : 'display: none;' ?>">
  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.25rem 0;">All Participant Challenge Submissions</h3>
        <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Review entries, test live demos &amp; GitHub code, assign judge scores, and award prizes.</p>
      </div>

      <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
        <input type="text" id="filter_sub_search" onkeyup="filterSubmissionsTable(this.value)" placeholder="Search projects or builders..." style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.45rem 0.85rem; color: #f8fafc; font-size: 0.85rem; min-width: 220px;">
        <select id="filter_sub_status" onchange="filterSubmissionsByStatus(this.value)" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.45rem 0.85rem; color: #f8fafc; font-size: 0.85rem;">
          <option value="all">All Statuses</option>
          <option value="submitted">Submitted</option>
          <option value="under_review">Under Review</option>
          <option value="shortlisted">Shortlisted</option>
          <option value="winner">Winner</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>
    </div>

    <table class="admin-table" id="submissionsTable" data-paginate="true" data-search-id="filter_sub_search" data-page-size="10">
      <thead>
        <tr>
          <th>Participant</th>
          <th>Challenge</th>
          <th>Project Title &amp; Tech Stack</th>
          <th>Repository &amp; Demo</th>
          <th style="text-align: center;">Referral Clicks &amp; Votes</th>
          <th>Status</th>
          <th style="text-align: right;">Judge Score</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($submissions)): ?>
          <?php foreach ($submissions as $sub): ?>
            <?php
              $subId = (int)$sub['id'];
              $status = $sub['status'] ?? 'submitted';
            ?>
            <tr class="sub-row" data-status="<?= htmlspecialchars($status) ?>">
              <td>
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                  <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #0284c7, #6366f1); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; color: #fff;">
                    <?= strtoupper(substr($sub['submitter_name'] ?: 'U', 0, 1)) ?>
                  </div>
                  <div>
                    <div style="font-weight: 700; color: #f8fafc; font-size: 0.88rem;">
                      <?= htmlspecialchars($sub['submitter_name']) ?>
                    </div>
                    <div style="font-size: 0.72rem; color: #94a3b8;">
                      @<?= htmlspecialchars($sub['username']) ?> &bull; <?= htmlspecialchars($sub['user_email']) ?>
                    </div>
                  </div>
                </div>
              </td>
              <td>
                <a href="<?= url('/campaigns/' . $sub['campaign_slug']) ?>" target="_blank" style="color: #38bdf8; font-size: 0.85rem; font-weight: 600; text-decoration: none;">
                  <?= htmlspecialchars($sub['campaign_title']) ?> ↗
                </a>
              </td>
              <td>
                <div style="font-weight: 700; color: #f8fafc; font-size: 0.9rem;">
                  <?= htmlspecialchars($sub['project_title']) ?>
                </div>
                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem;">
                  <?= htmlspecialchars(mb_strimwidth($sub['summary'], 0, 90, '...')) ?>
                </div>
                <?php if (!empty($sub['tech_stack'])): ?>
                  <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; margin-top: 0.35rem;">
                    <?php foreach (array_slice(explode(',', $sub['tech_stack']), 0, 3) as $stk): ?>
                      <span style="font-size: 0.65rem; background: rgba(148,163,184,0.12); color: #cbd5e1; padding: 1px 5px; border-radius: 3px;">
                        <?= htmlspecialchars(trim($stk)) ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                  <?php if (!empty($sub['github_repo_url'])): ?>
                    <a href="<?= htmlspecialchars($sub['github_repo_url']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.72rem;" title="GitHub Repo">
                      <i data-lucide="github" style="width: 12px; height: 12px;"></i>
                    </a>
                  <?php endif; ?>
                  <?php if (!empty($sub['live_demo_url'])): ?>
                    <a href="<?= htmlspecialchars($sub['live_demo_url']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.72rem; color: #38bdf8;" title="Live Demo">
                      Demo ↗
                    </a>
                  <?php endif; ?>
                  <?php if (!empty($sub['video_demo_url'])): ?>
                    <a href="<?= htmlspecialchars($sub['video_demo_url']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.72rem; color: #ec4899;" title="Video Walkthrough">
                      Video ↗
                    </a>
                  <?php endif; ?>
                </div>
              </td>
              <td style="text-align: center;">
                <span style="font-weight: 700; color: #38bdf8; font-family: monospace; font-size: 0.85rem;">
                  <?= number_format((int)$sub['referral_clicks']) ?> Clicks
                </span>
                <div style="font-size: 0.7rem; color: #ec4899;">
                  <?= number_format((int)$sub['votes_count']) ?> Votes
                </div>
              </td>
              <td>
                <span class="ts-badge <?= in_array($status, ['winner', 'shortlisted']) ? 'ts-badge-green' : ($status === 'rejected' ? 'ts-badge-pink' : 'ts-badge-cyan') ?>" style="font-size: 0.68rem; text-transform: uppercase;">
                  <?= str_replace('_', ' ', $status) ?>
                </span>
                <?php if (!empty($sub['prize_awarded'])): ?>
                  <div style="font-size: 0.7rem; color: #34d399; font-weight: 700; margin-top: 0.2rem;">
                    🏆 <?= htmlspecialchars($sub['prize_awarded']) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td style="text-align: right; font-family: var(--admin-font-mono); font-weight: 700; color: #fbbf24;">
                <?= $sub['score'] !== null ? number_format((float)$sub['score'], 1) . ' / 100' : '—' ?>
              </td>
              <td style="text-align: right;">
                <button type="button" onclick='openGradeModal(<?= json_encode($sub, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; font-weight: 700;">
                  Review &amp; Grade ⚖️
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" style="text-align: center; padding: 3rem; color: #64748b;">
              No participant entries submitted yet across challenges.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal 1: Launch / Edit Challenge with Defined Submission Schema -->
<div id="campFormModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-rose" style="max-width: 820px; max-height: 90vh; overflow-y: auto;">
    <div class="admin-modal-header">
      <h2 id="modalCampTitle" class="admin-modal-title" style="color: #ec4899;"><i data-lucide="trophy"></i> Create / Edit Developer Challenge</h2>
      <button type="button" onclick="document.getElementById('campFormModal').style.display='none'" class="admin-modal-close">&times;</button>
    </div>

    <form id="adminCampForm" action="<?= url('/admin/campaigns/save') ?>" method="POST">
      <input type="hidden" name="id" id="camp_id" value="">

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Challenge Title * <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 100 chars)</span></label>
        <input type="text" name="title" id="camp_title" maxlength="100" placeholder="e.g. Autonomous AI Agent Hackathon 2026" class="admin-modal-input" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label class="admin-modal-label">Total Prize Pool ($ USD / Credits) *</label>
          <input type="text" name="prize_pool" id="camp_prize_pool" placeholder="$25,000 Cash + AWS Credits" class="admin-modal-input" required>
        </div>

        <div>
          <label class="admin-modal-label">Challenge Format</label>
          <select name="campaign_type" id="camp_type" class="admin-modal-select">
            <option value="hackathon">Hackathon</option>
            <option value="contest">Technical Contest</option>
            <option value="sponsorship">Community Sponsorship</option>
            <option value="launch">Product Launch Challenge</option>
            <option value="referral">Viral Referral Campaign</option>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label class="admin-modal-label">Submission Window Starts</label>
          <input type="datetime-local" name="start_date" id="camp_start_date" class="admin-modal-input">
        </div>

        <div>
          <label class="admin-modal-label">Submissions Close (Deadline)</label>
          <input type="datetime-local" name="end_date" id="camp_end_date" class="admin-modal-input">
        </div>
      </div>

      <!-- Submission Fields Configuration -->
      <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(56,189,248,0.2); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.25rem;">
        <div style="font-size: 0.85rem; font-weight: 700; color: #38bdf8; text-transform: uppercase; margin-bottom: 0.6rem;">
          ⚙️ Participant Submission Form Configuration
        </div>
        <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.75rem;">
          Select which components participants are permitted / required to submit:
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem;">
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #f8fafc; cursor: pointer;">
            <input type="checkbox" name="submission_fields[]" value="github" checked style="accent-color: #38bdf8;">
            GitHub Repository URL
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #f8fafc; cursor: pointer;">
            <input type="checkbox" name="submission_fields[]" value="demo" checked style="accent-color: #38bdf8;">
            Live Deployed Web Application URL
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #f8fafc; cursor: pointer;">
            <input type="checkbox" name="submission_fields[]" value="video" checked style="accent-color: #38bdf8;">
            Architecture Video Walkthrough (Loom/YouTube)
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #f8fafc; cursor: pointer;">
            <input type="checkbox" name="submission_fields[]" value="article" checked style="accent-color: #38bdf8;">
            Linked TechScout Authored Publication
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #f8fafc; cursor: pointer;">
            <input type="checkbox" name="submission_fields[]" value="tech_stack" checked style="accent-color: #38bdf8;">
            Architecture Tech Stack Tags
          </label>
        </div>

        <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1); display: flex; gap: 1.5rem; flex-wrap: wrap;">
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #34d399; font-weight: 600; cursor: pointer;">
            <input type="checkbox" name="enable_referral_system" id="camp_enable_referral" value="1" checked style="accent-color: #10b981;">
            Enable Participant Viral Referral Tracking
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #fbbf24; font-weight: 600; cursor: pointer;">
            <input type="checkbox" name="enable_public_voting" id="camp_enable_voting" value="1" checked style="accent-color: #f59e0b;">
            Enable Public Community Upvoting
          </label>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <label class="admin-modal-label">Banner Image URL</label>
          <label class="ts-btn ts-btn-outline ts-btn-sm" style="cursor: pointer; padding: 0.15rem 0.5rem; font-size: 0.72rem; border-color: #38bdf8; color: #38bdf8; display: inline-flex; align-items: center; gap: 0.25rem;">
            <i data-lucide="upload" style="width: 12px; height: 12px;"></i> Upload Banner
            <input type="file" accept="image/*" onchange="uploadImageToInput(this, 'camp_featured_image_url', 'general')" style="display: none;">
          </label>
        </div>
        <input type="text" name="featured_image_url" id="camp_featured_image_url" placeholder="https://images.unsplash.com/... or /storage/uploads/..." class="admin-modal-input">
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Short Summary * <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 300 chars)</span></label>
        <textarea name="summary" id="camp_summary" maxlength="300" rows="2" class="admin-modal-input" required></textarea>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label class="admin-modal-label">Rules, Judging Criteria &amp; Technical Brief (HTML/Markdown) *</label>
        <textarea name="body_html" id="camp_body_html" rows="6" class="admin-modal-input" style="font-family: var(--ts-font-mono); font-size: 0.88rem;" required></textarea>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="document.getElementById('campFormModal').style.display='none'" class="ts-btn ts-btn-outline">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary">Publish Challenge 🚀</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal 2: Review & Grade Participant Submission -->
<div id="gradeSubmissionModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-indigo" style="max-width: 760px; max-height: 90vh; overflow-y: auto;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #818cf8;"><i data-lucide="award"></i> Review &amp; Grade Challenge Submission</h3>
      <button type="button" onclick="document.getElementById('gradeSubmissionModal').style.display='none'" class="admin-modal-close">&times;</button>
    </div>

    <form action="<?= url('/admin/campaigns/submission/grade') ?>" method="POST">
      <input type="hidden" name="submission_id" id="grade_sub_id" value="">

      <!-- Project Snapshot Card -->
      <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
          <h4 id="grade_project_title" style="font-size: 1.25rem; color: #f8fafc; font-family: 'Outfit', sans-serif; margin: 0;"></h4>
          <span id="grade_status_badge" class="ts-badge ts-badge-cyan"></span>
        </div>
        <div style="font-size: 0.82rem; color: #94a3b8; margin-bottom: 0.75rem;" id="grade_submitter_info"></div>
        <p id="grade_summary" style="font-size: 0.9rem; color: #cbd5e1; line-height: 1.5; margin-bottom: 1rem;"></p>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;" id="grade_external_links"></div>
      </div>

      <!-- Grading Inputs -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label class="admin-modal-label">Update Submission Status *</label>
          <select name="status" id="grade_status_select" class="admin-modal-select" required>
            <option value="submitted">Submitted (Under Queue)</option>
            <option value="under_review">Under Official Review</option>
            <option value="shortlisted">⭐ Shortlisted Finalist</option>
            <option value="winner">🏆 Challenge Winner</option>
            <option value="runner_up">🥈 Runner-Up Awardee</option>
            <option value="rejected">✕ Rejected / Disqualified</option>
          </select>
        </div>

        <div>
          <label class="admin-modal-label">Judge Score (0 – 100.0)</label>
          <input type="number" step="0.1" min="0" max="100" name="score" id="grade_score_input" placeholder="e.g. 94.5" class="admin-modal-input">
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Prize / Grant Allocation Title (Optional)</label>
        <input type="text" name="prize_awarded" id="grade_prize_input" placeholder="e.g. 1st Place: $10,000 USD + $5,000 Cloud GPU Credits" class="admin-modal-input">
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label class="admin-modal-label">Judge Feedback &amp; Editorial Recommendations (Notified to Participant)</label>
        <textarea name="judge_feedback" id="grade_feedback_input" rows="4" placeholder="Great multi-agent architecture. We were impressed with your vector retrieval benchmark..." class="admin-modal-input"></textarea>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="document.getElementById('gradeSubmissionModal').style.display='none'" class="ts-btn ts-btn-outline">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary" style="font-weight: 700;">Save Review &amp; Dispatch Notification 🔔</button>
      </div>
    </form>
  </div>
</div>

<script>
function switchAdminCampTab(tabName) {
  document.querySelectorAll('.camp-pane').forEach(p => p.style.display = 'none');
  document.querySelectorAll('[id^="tab_btn_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });

  const targetPane = document.getElementById('pane_' + tabName);
  const targetBtn = document.getElementById('tab_btn_' + tabName);
  if (targetPane) targetPane.style.display = 'block';
  if (targetBtn) {
    targetBtn.classList.remove('ts-btn-outline');
    targetBtn.classList.add('ts-btn-primary');
  }

  if (history.pushState) {
    const newUrl = new URL(window.location);
    newUrl.searchParams.set('tab', tabName);
    window.history.pushState({}, '', newUrl);
  }
}

function openNewCampModal() {
  document.getElementById('adminCampForm').reset();
  document.getElementById('camp_id').value = '';
  document.getElementById('camp_prize_pool').value = '$15,000';
  document.getElementById('camp_type').value = 'hackathon';
  document.getElementById('camp_enable_referral').checked = true;
  document.getElementById('camp_enable_voting').checked = true;
  document.getElementById('modalCampTitle').innerText = 'Launch New Developer Challenge';
  document.getElementById('campFormModal').style.display = 'flex';
}

function editCamp(c) {
  document.getElementById('adminCampForm').reset();
  document.getElementById('camp_id').value = c.id;
  document.getElementById('camp_title').value = c.title || '';
  document.getElementById('camp_prize_pool').value = c.prize_pool || '$15,000';
  document.getElementById('camp_type').value = c.campaign_type || 'hackathon';
  document.getElementById('camp_featured_image_url').value = c.featured_image_url || '';
  document.getElementById('camp_summary').value = c.summary || '';
  document.getElementById('camp_body_html').value = c.body_html || '';
  document.getElementById('camp_enable_referral').checked = (c.enable_referral_system == 1);
  document.getElementById('camp_enable_voting').checked = (c.enable_public_voting == 1);
  document.getElementById('modalCampTitle').innerText = 'Edit Challenge #' + c.id;
  document.getElementById('campFormModal').style.display = 'flex';
}

function openGradeModal(sub) {
  document.getElementById('grade_sub_id').value = sub.id;
  document.getElementById('grade_project_title').innerText = sub.project_title;
  document.getElementById('grade_status_badge').innerText = (sub.status || 'submitted').toUpperCase();
  document.getElementById('grade_submitter_info').innerText = `Submitted by ${sub.submitter_name} (@${sub.username}) • ${sub.user_email}`;
  document.getElementById('grade_summary').innerText = sub.summary || '';
  document.getElementById('grade_status_select').value = sub.status || 'submitted';
  document.getElementById('grade_score_input').value = sub.score !== null ? sub.score : '';
  document.getElementById('grade_prize_input').value = sub.prize_awarded || '';
  document.getElementById('grade_feedback_input').value = sub.judge_feedback || '';

  const linksWrap = document.getElementById('grade_external_links');
  linksWrap.innerHTML = '';
  if (sub.github_repo_url) {
    linksWrap.innerHTML += `<a href="${sub.github_repo_url}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;"><i data-lucide="github" style="width: 12px; height: 12px;"></i> GitHub ↗</a>`;
  }
  if (sub.live_demo_url) {
    linksWrap.innerHTML += `<a href="${sub.live_demo_url}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #38bdf8;"><i data-lucide="external-link" style="width: 12px; height: 12px;"></i> Demo ↗</a>`;
  }
  if (sub.video_demo_url) {
    linksWrap.innerHTML += `<a href="${sub.video_demo_url}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #ec4899;"><i data-lucide="video" style="width: 12px; height: 12px;"></i> Video ↗</a>`;
  }
  if (sub.linked_article_slug) {
    linksWrap.innerHTML += `<a href="<?= url('/blog/') ?>${sub.linked_article_slug}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #818cf8;">Read Article ↗</a>`;
  }

  document.getElementById('gradeSubmissionModal').style.display = 'flex';
  if (window.lucide) lucide.createIcons();
}

function filterSubmissionsTable(q) {
  if (window.submissionsPager) {
    window.submissionsPager.refresh();
  }
}

function filterSubmissionsByStatus(status) {
  document.querySelectorAll('#submissionsTable tbody tr.sub-row').forEach(r => {
    const rowStatus = r.getAttribute('data-status');
    if (status === 'all' || rowStatus === status) {
      r.classList.remove('no-paginate');
    } else {
      r.classList.add('no-paginate');
    }
  });
  if (window.submissionsPager) {
    window.submissionsPager.refresh();
  }
}

document.addEventListener('DOMContentLoaded', function() {
  window.campaignsPager = window.paginateAdminTable('campaignsCrudTable', { pageSize: 10 });
  window.submissionsPager = window.paginateAdminTable('submissionsTable', {
    pageSize: 10,
    searchInputId: 'filter_sub_search'
  });
});
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
