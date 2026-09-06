<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$isStaff = in_array($user['role'] ?? '', ['admin', 'super_admin', 'editor'], true);
$hasApp = !empty($application);
$appStatus = $application['status'] ?? null;
$isRejected = ($hasApp && $appStatus === 'rejected');
$isPending = ($hasApp && $appStatus === 'pending');
$isApproved = ($hasApp && $appStatus === 'approved');
// User is an active author only if not rejected
$isAuthor = ($isStaff || (($user['role'] ?? '') === 'author')) && !$isRejected;
?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem; max-width: 1000px;">
  
  <!-- Breadcrumb & Top Tag -->
  <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
    <a href="<?= url('/') ?>" class="ts-badge ts-badge-cyan" style="text-decoration: none;">← TechScout Home</a>
    <span class="ts-badge ts-badge-pink">Creator Onboarding</span>
  </div>

  <!-- Hero Header -->
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.15), transparent 40%), #090d16; border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem; position: relative; overflow: hidden;">
    <div style="max-width: 720px; position: relative; z-index: 2;">
      <h1 style="font-size: 2.4rem; color: #f8fafc; margin-bottom: 0.75rem; line-height: 1.2;">
        Join the <span style="background: linear-gradient(135deg, #ec4899, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">TechScout Author Guild</span>
      </h1>
      <p style="font-size: 1.05rem; color: #cbd5e1; line-height: 1.6; margin-bottom: 1.5rem;">
        Publish technical architecture breakdowns, benchmark developer tools, share XML prompt blueprints, and establish yourself as an authority in front of 50,000+ software engineers.
      </p>

      <!-- 4 Value Props -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
        <div style="display: flex; gap: 0.6rem; align-items: flex-start;">
          <div style="color: #ec4899; font-size: 1.2rem;">💰</div>
          <div>
            <div style="font-weight: 700; color: #f8fafc; font-size: 0.9rem;">Creator Rewards</div>
            <div style="font-size: 0.75rem; color: #94a3b8;">Earn credits & milestone bounties for viral technical deep-dives.</div>
          </div>
        </div>
        <div style="display: flex; gap: 0.6rem; align-items: flex-start;">
          <div style="color: #38bdf8; font-size: 1.2rem;">🚀</div>
          <div>
            <div style="font-weight: 700; color: #f8fafc; font-size: 0.9rem;">Global Visibility</div>
            <div style="font-size: 0.75rem; color: #94a3b8;">Syndicate across search engines, social radar, and our developer feeds.</div>
          </div>
        </div>
        <div style="display: flex; gap: 0.6rem; align-items: flex-start;">
          <div style="color: #34d399; font-size: 1.2rem;">🛡️</div>
          <div>
            <div style="font-weight: 700; color: #f8fafc; font-size: 0.9rem;">Verified Scout Badge</div>
            <div style="font-size: 0.75rem; color: #94a3b8;">Custom public author portfolio showcasing all your publications.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($isRejected): ?>
    <!-- Application Status: Revision Requested / Declined Banner -->
    <div class="ts-card" style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.35); padding: 2rem; margin-bottom: 2rem; border-radius: 12px;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(239,68,68,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; color: #ef4444; flex-shrink: 0;">
            ⚠️
          </div>
          <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: #ef4444; text-transform: uppercase; letter-spacing: 0.05em;">Editorial Decision</div>
            <h3 style="font-size: 1.35rem; color: #f8fafc; margin: 0.2rem 0;">Application Status: Revisions Requested</h3>
            <span style="font-size: 0.8rem; color: #94a3b8;">
              Evaluated on <?= date('M j, Y \a\t g:i A', strtotime($application['reviewed_at'] ?? $application['updated_at'])) ?>
            </span>
          </div>
        </div>
        <span class="ts-badge" style="background: rgba(239,68,68,0.2); color: #f87171; border: 1px solid rgba(239,68,68,0.4); font-size: 0.78rem; padding: 0.35rem 0.85rem; font-weight: 700;">
          REVISIONS NEEDED
        </span>
      </div>

      <?php if (!empty($application['admin_feedback'])): ?>
        <div style="background: #05070b; border: 1px solid rgba(239,68,68,0.3); border-left: 4px solid #ef4444; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.25rem;">
          <div style="font-size: 0.78rem; font-weight: 700; color: #fca5a5; text-transform: uppercase; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
            <i data-lucide="message-square" style="width: 14px; height: 14px; color: #ef4444;"></i> Editorial Board Notes &amp; Feedback:
          </div>
          <p style="font-size: 0.95rem; color: #f8fafc; margin: 0; line-height: 1.6; font-style: italic;">
            "<?= nl2br(htmlspecialchars($application['admin_feedback'])) ?>"
          </p>
        </div>
      <?php endif; ?>

      <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.85rem;">
        <span style="font-size: 1.35rem;">🔒</span>
        <div style="font-size: 0.85rem; color: #94a3b8; line-height: 1.5;">
          <strong style="color: #cbd5e1;">Creator Studio Access Paused:</strong> Your author publishing privileges are temporarily inactive while this application is in revision. Update your engineering portfolio, links, or sample technical pitch in the form below and resubmit for priority re-evaluation.
        </div>
      </div>
    </div>

  <?php elseif ($isAuthor && empty($_GET['edit'])): ?>
    <!-- Already an Author Banner -->
    <div class="ts-card" style="background: rgba(16,185,129,0.08); border-color: rgba(16,185,129,0.3); padding: 2rem; text-align: center; margin-bottom: 2rem;">
      <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎉</div>
      <h2 style="font-size: 1.5rem; color: #f8fafc; margin-bottom: 0.5rem;">You are a Verified TechScout Author!</h2>
      <p style="color: #94a3b8; font-size: 0.95rem; max-width: 600px; margin: 0 auto 1.5rem auto;">
        Your account is already elevated to Author status. You have access to the MS Word Visual Document Studio and Creator Dashboard.
      </p>
      <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
        <a href="<?= url('/author/dashboard') ?>" class="ts-btn ts-btn-primary">
          <i data-lucide="layout-dashboard"></i> Launch Creator Studio →
        </a>
        <a href="<?= url('/author/editor') ?>" class="ts-btn ts-btn-outline">
          <i data-lucide="plus-circle"></i> Write New Article / Blueprint
        </a>
      </div>
    </div>

  <?php elseif ($isPending && empty($_GET['edit'])): ?>
    <!-- Status Tracker: Under Editorial Review -->
    <div class="ts-card" style="background: rgba(245,158,11,0.08); border-color: rgba(245,158,11,0.3); padding: 2rem; margin-bottom: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(245,158,11,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
            ⏳
          </div>
          <div>
            <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0;">Application Under Editorial Review</h3>
            <span style="font-size: 0.8rem; color: #f59e0b;">Submitted on <?= date('F j, Y \a\t H:i', strtotime($application['created_at'])) ?></span>
          </div>
        </div>
        <span class="ts-badge ts-badge-pink" style="font-size: 0.8rem; padding: 0.25rem 0.75rem;">PENDING REVIEW</span>
      </div>

      <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
        Thank you for applying! Our editorial staff is currently evaluating your engineering background, portfolio, and proposed technical pitch. Reviews typically take less than <strong>24 business hours</strong>.
      </p>

      <!-- Application Summary Box -->
      <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
          <div>
            <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; display: block;">Applicant Name</span>
            <span style="font-weight: 700; color: #f8fafc; font-size: 0.95rem;"><?= htmlspecialchars($application['display_name']) ?></span>
          </div>
          <div>
            <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; display: block;">Professional Headline</span>
            <span style="color: #cbd5e1; font-size: 0.9rem;"><?= htmlspecialchars($application['headline']) ?></span>
          </div>
          <div>
            <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; display: block;">Specializations</span>
            <span style="color: #38bdf8; font-size: 0.85rem;"><?= htmlspecialchars($application['specializations'] ?: 'General Software Engineering') ?></span>
          </div>
        </div>

        <div>
          <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Sample Technical Pitch</span>
          <p style="font-size: 0.85rem; color: #cbd5e1; margin: 0; line-height: 1.5; font-style: italic;">
            "<?= htmlspecialchars($application['sample_pitch']) ?>"
          </p>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end;">
        <a href="?edit=1" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.8rem;">
          <i data-lucide="edit-3"></i> Edit Application Details
        </a>
      </div>
    </div>
  <?php endif; ?>

  <!-- Application Submission / Revision Form -->
  <?php if (!$isAuthor || $isRejected || !empty($_GET['edit'])): ?>
    <div class="ts-card ts-author-form-card" style="padding: 2.25rem; background: #090d16; border-color: rgba(99,102,241,0.25);">
      <h2 style="font-size: 1.4rem; color: #f8fafc; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="<?= $isRejected ? 'edit-3' : 'file-signature' ?>" style="color: <?= $isRejected ? '#f59e0b' : 'var(--ts-primary)' ?>;"></i> 
        <?= $isRejected ? 'Revise & Resubmit Your Author Application' : 'Author Application Form' ?>
      </h2>
      <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 2rem;">
        <?= $isRejected ? 'Please address the editorial feedback above by updating your pitch, links, or sample technical work.' : 'Please tell us about your engineering experience, past technical writing, and the topics you are passionate about.' ?>
      </p>

      <form action="<?= url('/author/apply') ?>" method="POST" enctype="multipart/form-data">
        
        <!-- Step 1: Personal & Public Persona -->
        <h3 style="font-size: 1rem; color: #38bdf8; margin-bottom: 1rem; border-bottom: 1px solid rgba(148,163,184,0.1); padding-bottom: 0.4rem;">
          1. Public Author Identity
        </h3>

        <div class="ts-responsive-form-grid">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem;">
              Full Display Name <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="display_name" value="<?= htmlspecialchars($application['display_name'] ?? $user['display_name'] ?? $user['username'] ?? '') ?>" placeholder="e.g. Marcus Vance" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc;" required>
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem;">
              Professional Headline <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="headline" value="<?= htmlspecialchars($application['headline'] ?? '') ?>" placeholder="e.g. Principal Distributed Systems Architect @ TechCorp" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc;" required>
          </div>
        </div>

        <div style="margin-bottom: 1.75rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem;">
            Author Bio & Engineering Background <span style="color: #ef4444;">*</span>
          </label>
          <textarea name="bio" rows="3" placeholder="Brief 2-3 sentence overview of your technical background, languages, and distributed systems experience..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc; resize: vertical;" required><?= htmlspecialchars($application['bio'] ?? '') ?></textarea>
        </div>

        <!-- Step 2: Proof of Work & Socials -->
        <h3 style="font-size: 1rem; color: #38bdf8; margin-bottom: 1rem; border-bottom: 1px solid rgba(148,163,184,0.1); padding-bottom: 0.4rem;">
          2. Proof of Work & Links
        </h3>

        <div class="ts-responsive-form-grid">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem;">
              GitHub Username / Handle
            </label>
            <div style="display: flex; align-items: center; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: var(--ts-radius-md); padding-left: 0.85rem; width: 100%; min-width: 0;">
              <span style="color: #64748b; font-size: 0.85rem; white-space: nowrap;">github.com/</span>
              <input type="text" name="github_handle" value="<?= htmlspecialchars($application['github_handle'] ?? '') ?>" placeholder="username" style="flex: 1 1 auto; min-width: 0; width: 100%; background: transparent; border: none; padding: 0.75rem 0.5rem; color: #f8fafc; outline: none;">
            </div>
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem;">
              Personal Blog / Engineering Portfolio URL
            </label>
            <input type="url" name="portfolio_url" value="<?= htmlspecialchars($application['portfolio_url'] ?? '') ?>" placeholder="https://yourtechblog.com" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc;">
          </div>
        </div>

        <div class="ts-responsive-form-grid" style="margin-bottom: 1.75rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem;">
              Twitter / X Handle
            </label>
            <div style="display: flex; align-items: center; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: var(--ts-radius-md); padding-left: 0.85rem; width: 100%; min-width: 0;">
              <span style="color: #64748b; font-size: 0.85rem; white-space: nowrap;">@</span>
              <input type="text" name="twitter_handle" value="<?= htmlspecialchars($application['twitter_handle'] ?? '') ?>" placeholder="username" style="flex: 1 1 auto; min-width: 0; width: 100%; background: transparent; border: none; padding: 0.75rem 0.5rem; color: #f8fafc; outline: none;">
            </div>
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem;">
              LinkedIn Profile URL
            </label>
            <input type="url" name="linkedin_url" value="<?= htmlspecialchars($application['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/in/username" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc;">
          </div>
        </div>

        <!-- Step 3: Specializations & Topics -->
        <h3 style="font-size: 1rem; color: #38bdf8; margin-bottom: 1rem; border-bottom: 1px solid rgba(148,163,184,0.1); padding-bottom: 0.4rem;">
          3. Topic Focus & Engineering Domains
        </h3>

        <?php if (!empty($topics)): ?>
          <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem;">
              Topics You Plan to Write About (Select all that apply)
            </label>
            <?php 
              $savedTopics = !empty($application['topic_interests']) ? json_decode((string)$application['topic_interests'], true) : [];
              if (!is_array($savedTopics)) $savedTopics = [];
            ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.6rem;">
              <?php foreach ($topics as $top): ?>
                <label style="display: flex; align-items: center; gap: 0.5rem; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.82rem; color: #f8fafc; cursor: pointer; word-break: break-word;">
                  <input type="checkbox" name="topic_interests[]" value="<?= htmlspecialchars($top['name']) ?>" <?= in_array($top['name'], $savedTopics, true) ? 'checked' : '' ?> style="accent-color: var(--ts-primary); flex-shrink: 0;">
                  <i data-lucide="<?= htmlspecialchars($top['icon_name'] ?: 'layers') ?>" style="width: 13px; height: 13px; flex-shrink: 0; color: <?= htmlspecialchars($top['color_hex'] ?: '#6366f1') ?>;"></i>
                  <span><?= htmlspecialchars($top['name']) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Step 4: Sample Pitch & Word DOCX Upload -->
        <h3 style="font-size: 1rem; color: #38bdf8; margin-bottom: 1rem; border-bottom: 1px solid rgba(148,163,184,0.1); padding-bottom: 0.4rem; margin-top: 1.75rem;">
          4. Proposed First Article Pitch or Sample Document
        </h3>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem;">
            Sample Technical Pitch / Article Outline <span style="color: #ef4444;">*</span>
          </label>
          <textarea name="sample_pitch" rows="3" placeholder="Describe the title and core technical takeaways of your first article or blueprint (e.g. 'Benchmarking eBPF Socket Filters in Production: A 90% Latency Teardown')..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc; resize: vertical;" required><?= htmlspecialchars($application['sample_pitch'] ?? '') ?></textarea>
        </div>

        <div style="margin-bottom: 1.75rem; background: rgba(15,23,42,0.6); border: 1px dashed rgba(99,102,241,0.3); border-radius: var(--ts-radius-md); padding: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.35rem;">
            Attach Sample Article Document (.docx, .pdf, .md) — <span style="color: #38bdf8; font-weight: normal;">Optional but accelerates approval</span>
          </label>
          <input type="file" name="sample_file" accept=".docx,.doc,.pdf,.md,.txt" style="color: #94a3b8; font-size: 0.85rem; max-width: 100%; box-sizing: border-box;">
          <?php if (!empty($application['sample_file_url'])): ?>
            <div style="margin-top: 0.5rem; font-size: 0.8rem; color: #34d399; word-break: break-all;">
              ✓ Previously attached sample: <a href="<?= htmlspecialchars($application['sample_file_url']) ?>" target="_blank" style="color: #38bdf8;">View Attached File ↗</a>
            </div>
          <?php endif; ?>
        </div>

        <!-- Step 5: Content Publishing Permissions Requested -->
        <h3 style="font-size: 1rem; color: #38bdf8; margin-bottom: 1rem; border-bottom: 1px solid rgba(148,163,184,0.1); padding-bottom: 0.4rem;">
          5. Content Permissions Requested
        </h3>

        <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1.25rem; margin-bottom: 2rem;">
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
            <i data-lucide="check-circle" style="color: #34d399; width: 16px;"></i>
            <span style="font-size: 0.9rem; font-weight: 600; color: #f8fafc;">Technical Articles & System Tutorials</span>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; padding: 0.05rem 0.35rem;">INCLUDED BY DEFAULT</span>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-top: 1rem; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 1rem;">
            <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;">
              <input type="checkbox" name="requested_prompts" value="1" <?= !empty($application['requested_prompts']) ? 'checked' : '' ?> style="margin-top: 0.2rem; accent-color: #38bdf8; flex-shrink: 0;">
              <div>
                <div style="font-size: 0.85rem; font-weight: 600; color: #f8fafc;">⚡ Prompt Blueprints</div>
                <div style="font-size: 0.75rem; color: #94a3b8;">Publish parameterized system prompts & test cases.</div>
              </div>
            </label>

            <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;">
              <input type="checkbox" name="requested_gpts" value="1" <?= !empty($application['requested_gpts']) ? 'checked' : '' ?> style="margin-top: 0.2rem; accent-color: #ec4899; flex-shrink: 0;">
              <div>
                <div style="font-size: 0.85rem; font-weight: 600; color: #f8fafc;">🤖 Custom GPTs & Agents</div>
                <div style="font-size: 0.75rem; color: #94a3b8;">Submit OpenAI GPT listings and autonomous agents.</div>
              </div>
            </label>

            <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;">
              <input type="checkbox" name="requested_tools" value="1" <?= !empty($application['requested_tools']) ? 'checked' : '' ?> style="margin-top: 0.2rem; accent-color: #34d399; flex-shrink: 0;">
              <div>
                <div style="font-size: 0.85rem; font-weight: 600; color: #f8fafc;">🛠️ AI Tool Teardowns</div>
                <div style="font-size: 0.75rem; color: #94a3b8;">Publish benchmark comparisons & tool matrices.</div>
              </div>
            </label>
          </div>
        </div>

        <div class="ts-author-submit-wrap" style="display: flex; justify-content: flex-end; gap: 1rem;">
          <button type="submit" class="ts-btn ts-btn-primary ts-btn-full-mobile" style="padding: 0.75rem 2rem; font-size: 1rem; <?= $isRejected ? 'background: linear-gradient(135deg, #f59e0b, #ec4899); border: none;' : '' ?>">
            <i data-lucide="send"></i> <?= $isRejected ? 'Resubmit Updated Application for Review 🚀' : 'Submit Author Application 🚀' ?>
          </button>
        </div>
      </form>
    </div>
  <?php endif; ?>
</div>

<style>
@media (max-width: 768px) {
  .ts-author-form-card {
    padding: 1.15rem !important;
  }
  .ts-author-submit-wrap {
    width: 100% !important;
  }
  .ts-btn-full-mobile {
    width: 100% !important;
    justify-content: center !important;
  }
}
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
