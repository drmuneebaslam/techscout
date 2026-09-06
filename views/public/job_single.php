<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem; max-width: 860px;">
  <!-- Breadcrumb -->
  <div style="display: flex; gap: 0.5rem; align-items: center; font-size: 0.85rem; color: #64748b; margin-bottom: 1.5rem;">
    <a href="<?= url('/') ?>" style="color: #64748b; text-decoration: none;">Home</a>
    <span>/</span>
    <a href="<?= url('/jobs') ?>" style="color: #64748b; text-decoration: none;">Engineering Jobs</a>
    <span>/</span>
    <span style="color: #cbd5e1;"><?= htmlspecialchars($job['title']) ?></span>
  </div>

  <!-- Job Header Card -->
  <div class="ts-card" style="padding: 2.5rem; border-color: rgba(99,102,241,0.3); margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 1.5rem;">
      <div>
        <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem;">
          <span class="ts-badge ts-badge-cyan"><?= htmlspecialchars($job['job_type']) ?></span>
          <?php if (!empty($job['is_featured'])): ?>
            <span class="ts-badge ts-badge-pink">VERIFIED OPENING ⭐</span>
          <?php endif; ?>
        </div>
        <h1 style="font-size: 2.2rem; color: #f8fafc; margin: 0 0 0.5rem 0; line-height: 1.2; font-family: 'Outfit', sans-serif;">
          <?= htmlspecialchars($job['title']) ?>
        </h1>
        <div style="font-size: 1.1rem; color: #38bdf8; font-weight: 600;">
          🏢 <?= htmlspecialchars($job['company_name']) ?>
        </div>
      </div>

      <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <button type="button" onclick="document.getElementById('applyJobModal').style.display='flex'" class="ts-btn ts-btn-primary" style="padding: 0.85rem 2rem; font-size: 1.05rem; font-weight: 700;">
          Apply for this Position 🚀
        </button>
        <button type="button" onclick="openReportModal('job', <?= (int)$job['id'] ?>, '<?= htmlspecialchars(addslashes($job['title'] . ' at ' . $job['company_name'])) ?>', window.location.href)" class="ts-btn ts-btn-outline" style="color: #94a3b8; font-size: 0.82rem;" title="Report this job listing">
          <i data-lucide="flag" style="width: 13px; height: 13px; color: #ef4444;"></i> Report
        </button>
      </div>
    </div>

    <!-- Quick Meta Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; background: rgba(15,23,42,0.6); padding: 1.25rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1);">
      <div>
        <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Location</div>
        <div style="color: #f8fafc; font-weight: 600; font-size: 0.95rem; margin-top: 0.2rem;">📍 <?= htmlspecialchars($job['location']) ?></div>
      </div>
      <div>
        <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Compensation</div>
        <div style="color: #34d399; font-weight: 700; font-size: 0.95rem; margin-top: 0.2rem;"><?= htmlspecialchars($job['salary_range'] ?: 'Competitive Market') ?></div>
      </div>
      <div>
        <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Posted Date</div>
        <div style="color: #f8fafc; font-weight: 600; font-size: 0.95rem; margin-top: 0.2rem;"><?= \App\Core\DateHelper::format($job['created_at'], 'M j, Y') ?></div>
      </div>
    </div>
  </div>

  <!-- Job Description Body -->
  <div class="ts-card" style="padding: 2.5rem; line-height: 1.8; font-size: 1.02rem; color: #cbd5e1; margin-bottom: 2rem;">
    <h2 style="color: #f8fafc; font-size: 1.4rem; margin-top: 0; margin-bottom: 1rem;">Role Overview & Responsibilities</h2>
    <div class="article-body-content" style="color: #cbd5e1;">
      <?= $job['description_html'] ?>
    </div>

    <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid rgba(148,163,184,0.15); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h4 style="margin: 0 0 0.25rem 0; color: #f8fafc;">Ready to shape the future at <?= htmlspecialchars($job['company_name']) ?>?</h4>
        <span style="font-size: 0.85rem; color: #64748b;">Applications are processed directly by our engineering leads.</span>
      </div>
      <button type="button" onclick="document.getElementById('applyJobModal').style.display='flex'" class="ts-btn ts-btn-accent" style="padding: 0.85rem 2rem; font-weight: 700;">
        Submit Application 🚀
      </button>
    </div>
  </div>
</div>

<!-- On-Site Application Modal -->
<?php $user = \App\Core\Auth::user(); ?>
<div id="applyJobModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 680px; max-height: 90vh; overflow-y: auto; padding: 2.25rem; background: #090d16; border: 1px solid rgba(99,102,241,0.4); box-shadow: 0 25px 60px rgba(0,0,0,0.8);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <div>
        <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">Apply for: <?= htmlspecialchars($job['title']) ?></h2>
        <span style="font-size: 0.85rem; color: #38bdf8;">TechScout Organization Direct Hiring</span>
      </div>
      <button onclick="document.getElementById('applyJobModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.75rem; cursor: pointer; line-height: 1;">&times;</button>
    </div>

    <form action="<?= url('/jobs/' . $job['slug'] . '/apply') ?>" method="POST">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Full Name</label>
          <input type="text" name="applicant_name" value="<?= htmlspecialchars($user['display_name'] ?? $user['username'] ?? '') ?>" placeholder="Ada Lovelace" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.7rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;" required>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Email Address</label>
          <input type="email" name="applicant_email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="ada@example.com" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.7rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;" required>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Portfolio / GitHub / LinkedIn URL</label>
        <input type="url" name="portfolio_url" placeholder="https://github.com/username or https://yourportfolio.dev" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.7rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;">
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Resume & Technical Experience Details</label>
        <textarea name="resume_text" rows="4" placeholder="Paste your resume summary, tech stack, and key achievements..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem; resize: vertical;" required></textarea>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Cover Letter / Why TechScout?</label>
        <textarea name="cover_letter" rows="3" placeholder="Tell us why you want to build with TechScout..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem; resize: vertical;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('applyJobModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary" style="padding: 0.75rem 2rem; font-weight: 700;">Submit Application 🚀</button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
