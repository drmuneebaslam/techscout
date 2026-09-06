<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
  $user = \App\Core\Auth::user();
  $hasSubmitted = !empty($userSubmission);
  $activeTab = $activeTab ?? ($hasSubmitted ? 'submission' : 'overview');
  $submissionCount = count($showcaseSubmissions ?? []);
  $now = time();
  $endDate = strtotime($campaign['end_date'] ?? '+30 days');
  $isExpired = ($endDate < $now);
  $daysRemaining = max(0, ceil(($endDate - $now) / 86400));
?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem; max-width: 1080px;">

  <!-- Breadcrumb -->
  <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--ts-text-tertiary); margin-bottom: 1.25rem;">
    <a href="<?= url('/') ?>" style="color: var(--ts-text-secondary); text-decoration: none;">Home</a>
    <span>/</span>
    <a href="<?= url('/campaigns') ?>" style="color: var(--ts-text-secondary); text-decoration: none;">Challenges &amp; Hackathons</a>
    <span>/</span>
    <span style="color: var(--ts-text-primary); font-weight: 600;"><?= htmlspecialchars($campaign['title']) ?></span>
  </div>

  <!-- Header & Badges -->
  <div style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span class="ts-badge ts-badge-pink" style="font-size: 0.75rem; font-weight: 700;">
          <i data-lucide="trophy" style="width: 12px; height: 12px; display: inline-block;"></i> OFFICIAL HACKATHON
        </span>
        <span class="ts-badge <?= $isExpired ? 'ts-badge-amber' : 'ts-badge-green' ?>" style="font-size: 0.75rem; font-weight: 700;">
          <?= $isExpired ? 'SUBMISSIONS CLOSED' : 'SUBMISSIONS ACTIVE' ?>
        </span>
        <?php if (!empty($campaign['campaign_type'])): ?>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.75rem; text-transform: uppercase;">
            <?= htmlspecialchars($campaign['campaign_type']) ?>
          </span>
        <?php endif; ?>
      </div>

      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <button type="button" onclick="openReportModal('campaign', <?= (int)$campaign['id'] ?>, '<?= htmlspecialchars(addslashes($campaign['title'])) ?>', window.location.href)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8; font-size: 0.78rem;" title="Report this challenge">
          <i data-lucide="flag" style="width: 12px; height: 12px; color: #ef4444;"></i> Report
        </button>
      </div>
    </div>

    <h1 style="font-size: 2.75rem; color: #f8fafc; font-family: 'Outfit', sans-serif; font-weight: 800; line-height: 1.2; margin-bottom: 0.75rem;">
      <?= htmlspecialchars($campaign['title']) ?>
    </h1>
    <p style="font-size: 1.15rem; color: var(--ts-text-secondary); line-height: 1.6; max-width: 820px; margin: 0;">
      <?= htmlspecialchars($campaign['summary']) ?>
    </p>
  </div>

  <?php if (!empty($campaign['featured_image_url'])): ?>
    <div style="margin-bottom: 2.5rem; border-radius: var(--ts-radius-lg); overflow: hidden; border: 1px solid var(--ts-border-main); max-height: 420px; background: #05070b;">
      <img src="<?= htmlspecialchars(image_url($campaign['featured_image_url'])) ?>" alt="<?= htmlspecialchars($campaign['title']) ?>" style="width: 100%; height: 100%; max-height: 420px; object-fit: cover;">
    </div>
  <?php endif; ?>

  <!-- Prize & Key Telemetry Ribbon -->
  <div class="ts-card" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 1.5rem; text-align: center; margin-bottom: 2.5rem; background: var(--ts-bg-surface, rgba(15,23,42,0.7));">
    <div>
      <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--ts-text-tertiary); font-weight: 700; letter-spacing: 0.5px;">Total Prize Pool</div>
      <div style="font-size: 1.75rem; font-weight: 800; color: #34d399; margin-top: 0.3rem; font-family: 'Outfit', sans-serif;"><?= htmlspecialchars($campaign['prize_pool'] ?? '$15,000') ?></div>
      <div style="font-size: 0.72rem; color: #10b981;">Cash &amp; Cloud Credits</div>
    </div>
    <div>
      <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--ts-text-tertiary); font-weight: 700; letter-spacing: 0.5px;">Submission Deadline</div>
      <div style="font-size: 1.35rem; font-weight: 700; color: #f8fafc; margin-top: 0.3rem; font-family: 'Outfit', sans-serif;"><?= date('M j, Y', $endDate) ?></div>
      <div style="font-size: 0.72rem; color: <?= $daysRemaining <= 5 ? '#f59e0b' : '#38bdf8' ?>;"><?= $isExpired ? 'Completed' : $daysRemaining . ' days left to submit' ?></div>
    </div>
    <div>
      <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--ts-text-tertiary); font-weight: 700; letter-spacing: 0.5px;">Showcase Submissions</div>
      <div style="font-size: 1.75rem; font-weight: 800; color: var(--ts-primary-light); margin-top: 0.3rem; font-family: 'Outfit', sans-serif;"><?= $submissionCount ?></div>
      <div style="font-size: 0.72rem; color: var(--ts-text-tertiary);">Projects Submitted</div>
    </div>
    <div>
      <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--ts-text-tertiary); font-weight: 700; letter-spacing: 0.5px;">Your Participation</div>
      <div style="margin-top: 0.45rem;">
        <?php if ($hasSubmitted): ?>
          <span class="ts-badge ts-badge-green" style="font-weight: 700; font-size: 0.8rem; padding: 0.25rem 0.75rem;">
            ✓ SUBMISSION FILED
          </span>
        <?php elseif ($user): ?>
          <button type="button" onclick="switchChallengeTab('submission')" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700; padding: 0.35rem 0.85rem;">
            Submit Project 🚀
          </button>
        <?php else: ?>
          <a href="<?= url('/auth/login?redirect=' . urlencode('/campaigns/' . $campaign['slug'] . '?tab=submission')) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.8rem; color: #38bdf8;">
            Sign In to Join
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Interactive Navigation Tabs -->
  <div style="display: flex; gap: 0.5rem; margin-bottom: 2rem; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 0.75rem; overflow-x: auto; scrollbar-width: none;">
    <button type="button" onclick="switchChallengeTab('overview')" id="btn_tab_overview" class="ts-btn ts-btn-sm <?= $activeTab === 'overview' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 999px; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.15rem;">
      <i data-lucide="book-open" style="width: 14px; height: 14px;"></i>
      <span>Overview &amp; Guidelines</span>
    </button>

    <button type="button" onclick="switchChallengeTab('submission')" id="btn_tab_submission" class="ts-btn ts-btn-sm <?= $activeTab === 'submission' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 999px; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.15rem;">
      <i data-lucide="<?= $hasSubmitted ? 'award' : 'send' ?>" style="width: 14px; height: 14px;"></i>
      <span><?= $hasSubmitted ? 'My Submission &amp; Referral Hub 🌟' : 'Submit Project 🚀' ?></span>
      <?php if ($hasSubmitted): ?>
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;"><?= strtoupper($userSubmission['status']) ?></span>
      <?php endif; ?>
    </button>

    <button type="button" onclick="switchChallengeTab('showcase')" id="btn_tab_showcase" class="ts-btn ts-btn-sm <?= $activeTab === 'showcase' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 999px; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.15rem;">
      <i data-lucide="grid" style="width: 14px; height: 14px;"></i>
      <span>Submissions Showcase &amp; Leaderboard</span>
      <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;"><?= $submissionCount ?></span>
    </button>
  </div>

  <!-- TAB 1: OVERVIEW & RULES -->
  <div id="pane_overview" class="challenge-tab-pane" style="<?= $activeTab === 'overview' ? '' : 'display: none;' ?>">
    <div class="ts-card" style="margin-bottom: 2.5rem; background: var(--ts-bg-surface, rgba(15,23,42,0.7)); border: 1px solid var(--ts-border-main);">
      <h3 style="font-size: 1.35rem; color: #f8fafc; font-family: 'Outfit', sans-serif; font-weight: 700; margin: 0 0 1.25rem 0;">
        Challenge Brief &amp; Technical Specifications
      </h3>
      <div style="font-size: 1.05rem; line-height: 1.8; color: var(--ts-text-secondary);" class="article-body-content">
        <?= $campaign['body_html'] ?>
      </div>
    </div>

    <!-- Ready to Submit Banner -->
    <div class="ts-card" style="background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(236,72,153,0.1)); border: 1px solid rgba(99,102,241,0.3); border-radius: 16px; padding: 2.5rem 2rem; text-align: center;">
      <h3 style="font-size: 1.5rem; color: #f8fafc; font-family: 'Outfit', sans-serif; font-weight: 800; margin: 0 0 0.75rem 0;">
        Ready to build and showcase your engineering prowess?
      </h3>
      <p style="color: var(--ts-text-secondary); font-size: 1rem; max-width: 600px; margin: 0 auto 1.5rem auto;">
        Submit your GitHub repository, live demonstration agent, and architecture walkthrough. Compete for the <?= htmlspecialchars($campaign['prize_pool'] ?? '$15,000') ?> prize pool!
      </p>
      <button type="button" onclick="switchChallengeTab('submission')" class="ts-btn ts-btn-primary" style="font-weight: 700; font-size: 1rem; padding: 0.75rem 2rem; border-radius: 10px;">
        <?= $hasSubmitted ? 'View Your Submission &rarr;' : 'Launch Project Submission &rarr;' ?>
      </button>
    </div>
  </div>

  <!-- TAB 2: PROJECT SUBMISSION & PERSONAL REFERRAL HUB -->
  <div id="pane_submission" class="challenge-tab-pane" style="<?= $activeTab === 'submission' ? '' : 'display: none;' ?>">
    <?php if (!$user): ?>
      <!-- Guest Call to Action -->
      <div class="ts-card" style="text-align: center; padding: 4rem 2rem; background: var(--ts-bg-surface, rgba(15,23,42,0.7)); border: 1px dashed rgba(148,163,184,0.25); border-radius: 16px;">
        <i data-lucide="lock" style="width: 48px; height: 48px; color: #38bdf8; margin-bottom: 1rem;"></i>
        <h3 style="color: #f8fafc; font-size: 1.45rem; margin: 0 0 0.5rem 0; font-family: 'Outfit', sans-serif;">
          Sign In Required to Enter the Challenge
        </h3>
        <p style="color: var(--ts-text-secondary); font-size: 0.95rem; max-width: 520px; margin: 0 auto 1.75rem auto;">
          Participants must have a verified TechScout developer account to submit entries, generate personal referral links, and receive judge awards.
        </p>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
          <a href="<?= url('/auth/login?redirect=' . urlencode('/campaigns/' . $campaign['slug'] . '?tab=submission')) ?>" class="ts-btn ts-btn-primary" style="font-weight: 700;">
            Sign In with Email &rarr;
          </a>
          <a href="<?= url('/auth/register?redirect=' . urlencode('/campaigns/' . $campaign['slug'] . '?tab=submission')) ?>" class="ts-btn ts-btn-outline">
            Create Free Account
          </a>
        </div>
      </div>
    <?php elseif ($hasSubmitted): ?>
      <!-- Existing Submission View & Participant Referral Hub -->
      <div style="display: flex; flex-direction: column; gap: 2rem;">

        <!-- 1. Submission Status Card -->
        <div class="ts-card" style="background: var(--ts-bg-surface, rgba(15,23,42,0.8)); border: 1px solid var(--ts-border-main); border-radius: 16px; padding: 2rem;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;">
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; font-weight: 700;">YOUR ACTIVE SUBMISSION</span>
                <span class="ts-badge <?= in_array($userSubmission['status'], ['winner', 'shortlisted']) ? 'ts-badge-green' : 'ts-badge-pink' ?>" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                  <?= str_replace('_', ' ', $userSubmission['status']) ?>
                </span>
                <?php if ($userSubmission['score'] !== null): ?>
                  <span class="ts-badge ts-badge-amber" style="font-size: 0.75rem; font-weight: 700;">
                    JUDGE SCORE: <?= number_format((float)$userSubmission['score'], 1) ?> / 100
                  </span>
                <?php endif; ?>
              </div>
              <h2 style="font-size: 1.85rem; color: #f8fafc; font-family: 'Outfit', sans-serif; font-weight: 800; margin: 0;">
                <?= htmlspecialchars($userSubmission['project_title']) ?>
              </h2>
            </div>

            <button type="button" onclick="toggleEditForm()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
              <i data-lucide="edit-3"></i> Edit Submission
            </button>
          </div>

          <?php if (!empty($userSubmission['prize_awarded'])): ?>
            <div style="background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(6,182,212,0.12)); border: 1px solid rgba(16,185,129,0.4); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
              <div style="font-size: 2.25rem;">🏆</div>
              <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #34d399; text-transform: uppercase;">Challenge Prize Awarded</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #f8fafc;"><?= htmlspecialchars($userSubmission['prize_awarded']) ?></div>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($userSubmission['judge_feedback'])): ?>
            <div style="background: rgba(30,41,59,0.6); border-left: 4px solid #38bdf8; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
              <div style="font-size: 0.78rem; font-weight: 700; color: #38bdf8; text-transform: uppercase; margin-bottom: 0.35rem;">
                Official Judge &amp; Reviewer Feedback
              </div>
              <div style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.6;">
                <?= nl2br(htmlspecialchars($userSubmission['judge_feedback'])) ?>
              </div>
            </div>
          <?php endif; ?>

          <p style="color: var(--ts-text-secondary); font-size: 1rem; line-height: 1.6; margin-bottom: 1.5rem;">
            <?= nl2br(htmlspecialchars($userSubmission['summary'])) ?>
          </p>

          <?php if (!empty($userSubmission['tech_stack'])): ?>
            <div style="margin-bottom: 1.5rem;">
              <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 0.4rem;">Tech Stack Architecture</div>
              <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                <?php foreach (explode(',', $userSubmission['tech_stack']) as $tag): ?>
                  <span class="ts-badge" style="background: rgba(99,102,241,0.12); color: #818cf8; border: 1px solid rgba(99,102,241,0.25); font-size: 0.75rem;">
                    <?= htmlspecialchars(trim($tag)) ?>
                  </span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Submission Action Buttons -->
          <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; border-top: 1px solid var(--ts-border-subtle); padding-top: 1.25rem;">
            <?php if (!empty($userSubmission['github_repo_url'])): ?>
              <a href="<?= htmlspecialchars($userSubmission['github_repo_url']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i data-lucide="github" style="width: 14px; height: 14px;"></i> GitHub Repository ↗
              </a>
            <?php endif; ?>

            <?php if (!empty($userSubmission['live_demo_url'])): ?>
              <a href="<?= htmlspecialchars($userSubmission['live_demo_url']) ?>" target="_blank" class="ts-btn ts-btn-primary ts-btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i data-lucide="external-link" style="width: 14px; height: 14px;"></i> Launch Live Demo ↗
              </a>
            <?php endif; ?>

            <?php if (!empty($userSubmission['video_demo_url'])): ?>
              <a href="<?= htmlspecialchars($userSubmission['video_demo_url']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; color: #ec4899; border-color: rgba(236,72,153,0.3);">
                <i data-lucide="video" style="width: 14px; height: 14px;"></i> Watch Video Walkthrough ↗
              </a>
            <?php endif; ?>
          </div>
        </div>

        <!-- 2. Participant Referral & Inbound Traffic Telemetry -->
        <?php
          $referralUrl = url('/campaigns/' . $campaign['slug'] . '?ref=' . $userSubmission['referral_code']);
        ?>
        <div class="ts-card" style="background: linear-gradient(135deg, rgba(15,23,42,0.9), rgba(30,41,59,0.7)); border: 1px solid rgba(56,189,248,0.25); border-radius: 16px; padding: 2rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
              <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem;">
                <span class="ts-badge ts-badge-cyan">PARTICIPANT GROWTH RADAR</span>
                <span style="font-size: 0.8rem; color: #94a3b8;">Viral Referral &amp; Engagement Tracking</span>
              </div>
              <h3 style="font-size: 1.35rem; color: #f8fafc; font-family: 'Outfit', sans-serif; font-weight: 700; margin: 0;">
                📢 Personal Challenge Referral Link
              </h3>
            </div>
            <span class="ts-badge ts-badge-green">Code: <?= htmlspecialchars($userSubmission['referral_code']) ?></span>
          </div>

          <p style="color: var(--ts-text-secondary); font-size: 0.92rem; margin-bottom: 1.25rem;">
            Invite peer engineers, colleagues, and followers to discover the challenge and view your submission. Every visit via your link automatically credits your verified referral telemetry!
          </p>

          <!-- Referral Link Box -->
          <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <input type="text" readonly value="<?= htmlspecialchars($referralUrl) ?>" id="userRefLinkInput" style="flex: 1; min-width: 260px; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.75rem 1rem; color: #38bdf8; font-family: var(--ts-font-mono); font-size: 0.88rem;">
            <button type="button" onclick="copyReferralLink()" class="ts-btn ts-btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.75rem 1.25rem;">
              <i data-lucide="copy" style="width: 14px; height: 14px;"></i> Copy Link
            </button>
          </div>

          <!-- Referral Telemetry Stats Grid -->
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 150px), 1fr)); gap: 1rem;">
            <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.1); border-radius: 10px; padding: 1rem; text-align: center;">
              <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Inbound Clicks</div>
              <div style="font-size: 1.5rem; font-weight: 800; color: #38bdf8; margin-top: 0.2rem; font-family: 'Outfit', sans-serif;">
                <?= number_format((int)$userSubmission['referral_clicks']) ?>
              </div>
              <div style="font-size: 0.68rem; color: #64748b;">Unique visits logged</div>
            </div>

            <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.1); border-radius: 10px; padding: 1rem; text-align: center;">
              <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Community Votes</div>
              <div style="font-size: 1.5rem; font-weight: 800; color: #ec4899; margin-top: 0.2rem; font-family: 'Outfit', sans-serif;">
                <?= number_format((int)$userSubmission['votes_count']) ?>
              </div>
              <div style="font-size: 0.68rem; color: #64748b;">Upvotes from peers</div>
            </div>

            <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.1); border-radius: 10px; padding: 1rem; text-align: center;">
              <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Signups Generated</div>
              <div style="font-size: 1.5rem; font-weight: 800; color: #34d399; margin-top: 0.2rem; font-family: 'Outfit', sans-serif;">
                <?= number_format((int)$userSubmission['referral_signups']) ?>
              </div>
              <div style="font-size: 0.68rem; color: #64748b;">New builders onboarded</div>
            </div>
          </div>
        </div>

      </div>
    <?php endif; ?>

    <!-- Submission Form (Visible for New Submission OR in Edit Mode) -->
    <div id="challengeSubmissionFormWrap" style="<?= ($hasSubmitted && $user) ? 'display: none;' : '' ?> margin-top: 2rem;">
      <div class="ts-card" style="background: var(--ts-bg-surface, rgba(15,23,42,0.7)); border: 1px solid var(--ts-border-main); border-radius: 16px; padding: 2.25rem;">
        <div style="margin-bottom: 1.5rem;">
          <h3 style="font-size: 1.45rem; color: #f8fafc; font-family: 'Outfit', sans-serif; font-weight: 700; margin: 0 0 0.35rem 0;">
            <?= $hasSubmitted ? 'Edit Your Challenge Submission' : 'Submit Your Project Entry 🚀' ?>
          </h3>
          <p style="color: var(--ts-text-secondary); font-size: 0.92rem; margin: 0;">
            Provide complete details, open-source repository link, and live demonstration. Submissions are reviewed by the judging panel.
          </p>
        </div>

        <form action="<?= url('/campaigns/' . $campaign['slug'] . '/submit') ?>" method="POST">
          <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
              Project Title *
            </label>
            <input type="text" name="project_title" value="<?= htmlspecialchars($userSubmission['project_title'] ?? '') ?>" placeholder="e.g. Agentic RAG Multi-Modal Orchestrator" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.75rem 1rem; color: #f8fafc; font-size: 1rem;" required>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
              Executive Summary (1–2 Paragraphs) *
            </label>
            <textarea name="summary" rows="3" placeholder="Briefly describe what your system solves, how it works, and its core breakthroughs..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.75rem 1rem; color: #f8fafc; font-size: 0.95rem; line-height: 1.5;" required><?= htmlspecialchars($userSubmission['summary'] ?? '') ?></textarea>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
              Tech Stack Architecture (Comma Separated)
            </label>
            <input type="text" name="tech_stack" value="<?= htmlspecialchars($userSubmission['tech_stack'] ?? '') ?>" placeholder="e.g. Next.js, Python, LangChain, PostgreSQL, Docker" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.75rem 1rem; color: #f8fafc; font-size: 0.95rem;">
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
                GitHub Repository URL *
              </label>
              <input type="url" name="github_repo_url" value="<?= htmlspecialchars($userSubmission['github_repo_url'] ?? '') ?>" placeholder="https://github.com/username/project" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.75rem 1rem; color: #f8fafc; font-size: 0.95rem;" required>
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
                Live Deployed Demo / Agent URL
              </label>
              <input type="url" name="live_demo_url" value="<?= htmlspecialchars($userSubmission['live_demo_url'] ?? '') ?>" placeholder="https://myagent.demo.app" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.75rem 1rem; color: #f8fafc; font-size: 0.95rem;">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
                Video Demo / Loom Walkthrough URL
              </label>
              <input type="url" name="video_demo_url" value="<?= htmlspecialchars($userSubmission['video_demo_url'] ?? '') ?>" placeholder="https://loom.com/share/..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.75rem 1rem; color: #f8fafc; font-size: 0.95rem;">
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
                Linked TechScout Article (Optional)
              </label>
              <?php if (!empty($userArticles)): ?>
                <select name="linked_article_id" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.75rem 1rem; color: #f8fafc; font-size: 0.92rem;">
                  <option value="">-- None / Select Authored Article --</option>
                  <?php foreach ($userArticles as $uArt): ?>
                    <option value="<?= (int)$uArt['id'] ?>" <?= ((int)($userSubmission['linked_article_id'] ?? 0) === (int)$uArt['id']) ? 'selected' : '' ?>>
                      📝 <?= htmlspecialchars($uArt['title']) ?> (<?= number_format((int)($uArt['view_count'] ?? 0)) ?> reads)
                    </option>
                  <?php endforeach; ?>
                </select>
                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.35rem;">
                  <i data-lucide="shield-check" style="width: 13px; height: 13px; color: #10b981;"></i>
                  <span>Only technical articles authored by your account can be linked to this submission.</span>
                </div>
              <?php else: ?>
                <select name="linked_article_id" disabled style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.75rem 1rem; color: #64748b; font-size: 0.92rem; opacity: 0.6; cursor: not-allowed;">
                  <option value="">No published articles found under your author account</option>
                </select>
                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem;">
                  You have not published any articles yet. <a href="<?= url('/author/editor') ?>" style="color: #38bdf8; text-decoration: underline;">Publish an article in Creator Studio</a> to attach your technical deep dive.
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div style="margin-bottom: 1.75rem;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
              Technical Architecture &amp; Implementation Deep-Dive (Markdown/HTML)
            </label>
            <textarea name="detailed_description" rows="6" placeholder="Provide system architecture diagrams, benchmark numbers, setup instructions, or design decisions..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.75rem 1rem; color: #f8fafc; font-family: var(--ts-font-mono); font-size: 0.88rem; line-height: 1.6;"><?= htmlspecialchars($userSubmission['detailed_description'] ?? '') ?></textarea>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <?php if ($hasSubmitted): ?>
              <button type="button" onclick="toggleEditForm()" class="ts-btn ts-btn-outline">Cancel</button>
            <?php endif; ?>
            <button type="submit" class="ts-btn ts-btn-primary" style="font-weight: 700; padding: 0.75rem 2rem;">
              <?= $hasSubmitted ? 'Save Updated Submission ✓' : 'Submit Project to Challenge 🚀' ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- TAB 3: COMMUNITY SHOWCASE & SUBMISSIONS GALLERY -->
  <div id="pane_showcase" class="challenge-tab-pane" style="<?= $activeTab === 'showcase' ? '' : 'display: none;' ?>">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h3 style="font-size: 1.35rem; color: #f8fafc; font-family: 'Outfit', sans-serif; font-weight: 700; margin: 0 0 0.25rem 0;">
          Submitted Projects &amp; Community Showcase
        </h3>
        <p style="color: var(--ts-text-secondary); font-size: 0.9rem; margin: 0;">
          Explore and upvote projects created by builders across the global developer community.
        </p>
      </div>
      <span class="ts-badge ts-badge-pink"><?= $submissionCount ?> Projects Active</span>
    </div>

    <?php if (!empty($showcaseSubmissions)): ?>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 1.5rem;">
        <?php foreach ($showcaseSubmissions as $sub): ?>
          <?php
            $subId = (int)$sub['id'];
            $isVoted = in_array($subId, $userVotedIds ?? []);
            $votes = (int)$sub['votes_count'];
          ?>
          <div class="ts-card" id="submission_card_<?= $subId ?>" style="background: var(--ts-bg-surface, rgba(15,23,42,0.7)); border: 1px solid var(--ts-border-main); border-radius: 14px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <span class="ts-badge <?= in_array($sub['status'], ['winner', 'shortlisted']) ? 'ts-badge-green' : 'ts-badge-cyan' ?>" style="font-size: 0.68rem; text-transform: uppercase;">
                  <?= str_replace('_', ' ', $sub['status']) ?>
                </span>

                <!-- Upvote Button -->
                <button type="button" onclick="upvoteSubmission(<?= $subId ?>, this)" class="ts-btn ts-btn-sm <?= $isVoted ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="padding: 0.2rem 0.6rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem; border-radius: 999px;">
                  <i data-lucide="thumbs-up" style="width: 12px; height: 12px;"></i>
                  <span class="vote-count-<?= $subId ?>"><?= $votes ?></span>
                </button>
              </div>

              <h4 style="font-size: 1.15rem; color: #f8fafc; font-family: 'Outfit', sans-serif; font-weight: 700; margin: 0 0 0.5rem 0;">
                <?= htmlspecialchars($sub['project_title']) ?>
              </h4>

              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.85rem;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background: linear-gradient(135deg, #0284c7, #6366f1); display: flex; align-items: center; justify-content: center; font-size: 0.65rem; color: #fff; font-weight: 700;">
                  <?= strtoupper(substr($sub['submitter_name'] ?: 'U', 0, 1)) ?>
                </div>
                <span style="font-size: 0.8rem; color: #94a3b8;">by <?= htmlspecialchars($sub['submitter_name']) ?></span>
              </div>

              <p style="font-size: 0.88rem; color: var(--ts-text-secondary); line-height: 1.5; margin-bottom: 1rem;">
                <?= htmlspecialchars(mb_strimwidth($sub['summary'], 0, 130, '...')) ?>
              </p>

              <?php if (!empty($sub['tech_stack'])): ?>
                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 1rem;">
                  <?php foreach (array_slice(explode(',', $sub['tech_stack']), 0, 4) as $t): ?>
                    <span style="font-size: 0.68rem; background: rgba(148,163,184,0.1); color: #cbd5e1; padding: 2px 6px; border-radius: 4px;">
                      <?= htmlspecialchars(trim($t)) ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>

            <div style="border-top: 1px solid var(--ts-border-subtle); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
              <div style="display: flex; gap: 0.4rem;">
                <?php if (!empty($sub['github_repo_url'])): ?>
                  <a href="<?= htmlspecialchars($sub['github_repo_url']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.72rem;" title="View GitHub Repo">
                    <i data-lucide="github" style="width: 12px; height: 12px;"></i>
                  </a>
                <?php endif; ?>

                <?php if (!empty($sub['live_demo_url'])): ?>
                  <a href="<?= htmlspecialchars($sub['live_demo_url']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.72rem; color: #38bdf8;" title="Launch Live Demo">
                    <i data-lucide="external-link" style="width: 12px; height: 12px;"></i> Demo
                  </a>
                <?php endif; ?>
              </div>

              <?php if (!empty($sub['linked_article_slug'])): ?>
                <a href="<?= url('/blog/' . $sub['linked_article_slug']) ?>" style="font-size: 0.75rem; color: #818cf8; text-decoration: none; font-weight: 600;">
                  Read Article &rarr;
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="ts-card" style="text-align: center; padding: 4rem 2rem; background: var(--ts-bg-surface, rgba(15,23,42,0.6)); border: 1px dashed rgba(148,163,184,0.2); border-radius: 16px;">
        <i data-lucide="inbox" style="width: 48px; height: 48px; color: #475569; margin-bottom: 1rem;"></i>
        <h3 style="color: #f8fafc; font-size: 1.35rem; margin: 0 0 0.5rem 0;">No project entries submitted yet</h3>
        <p style="color: var(--ts-text-secondary); font-size: 0.95rem; max-width: 480px; margin: 0 auto 1.5rem auto;">
          Be the pioneer! Submit your open-source project or custom AI agent to appear first on the community leaderboard.
        </p>
        <button type="button" onclick="switchChallengeTab('submission')" class="ts-btn ts-btn-primary" style="font-weight: 700;">
          Submit First Entry 🚀
        </button>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
function switchChallengeTab(tabName) {
  document.querySelectorAll('.challenge-tab-pane').forEach(p => p.style.display = 'none');
  document.querySelectorAll('[id^="btn_tab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });

  const targetPane = document.getElementById('pane_' + tabName);
  const targetBtn = document.getElementById('btn_tab_' + tabName);
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

function toggleEditForm() {
  const wrap = document.getElementById('challengeSubmissionFormWrap');
  if (wrap) {
    wrap.style.display = (wrap.style.display === 'none' || wrap.style.display === '') ? 'block' : 'none';
    if (wrap.style.display === 'block') {
      wrap.scrollIntoView({ behavior: 'smooth' });
    }
  }
}

function copyReferralLink() {
  const input = document.getElementById('userRefLinkInput');
  if (input) {
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value);
    if (typeof showToast === 'function') {
      showToast('Referral link copied to clipboard! 📋', 'success');
    } else {
      alert('Referral link copied to clipboard!');
    }
  }
}

async function upvoteSubmission(submissionId, btn) {
  try {
    const r = await fetch('<?= url('/campaigns/' . $campaign['slug'] . '/vote/') ?>' + submissionId, {
      method: 'POST'
    });
    const d = await r.json();
    if (d && d.success) {
      document.querySelectorAll('.vote-count-' + submissionId).forEach(el => el.innerText = d.votes_count);
      if (d.voted) {
        btn.classList.remove('ts-btn-outline');
        btn.classList.add('ts-btn-primary');
      } else {
        btn.classList.remove('ts-btn-primary');
        btn.classList.add('ts-btn-outline');
      }
    } else if (d && d.error) {
      alert(d.error);
    }
  } catch (e) {
    console.error('Voting error:', e);
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
