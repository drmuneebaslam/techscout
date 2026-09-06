<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- Jobs Hub Hero -->
<div class="ts-hero">
  <div class="container" style="text-align: center; max-width: 800px; margin: 0 auto;">
    <span class="ts-badge ts-badge-cyan" style="margin-bottom: 1rem; display: inline-flex; align-items: center; gap: 0.4rem;">
      <i data-lucide="briefcase"></i> TECHSCOUT ENGINEERING TALENT NETWORK
    </span>
    <h1 style="font-size: 2.5rem; margin-bottom: 0.75rem; font-weight: 800; font-family: 'Outfit', sans-serif;">
      High-Impact Engineering & AI Roles
    </h1>
    <p style="color: var(--ts-text-secondary); font-size: 1.05rem; line-height: 1.6;">
      Explore verified technical positions, distributed systems architect roles, and AI engineering opportunities curated for the TechScout builder community.
    </p>
  </div>
</div>

<div class="container" style="margin-bottom: 5rem;">
  <div style="display: flex; flex-direction: column; gap: 1.25rem; max-width: 900px; margin: 0 auto;">
    <?php if (!empty($jobs)): ?>
      <?php foreach ($jobs as $job): ?>
        <div class="ts-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; border-color: rgba(99,102,241,0.2); transition: transform 0.2s ease, border-color 0.2s ease;">
          <div style="display: flex; gap: 1.25rem; align-items: center; min-width: 280px; flex: 1;">
            <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(99,102,241,0.12); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 1.5rem; font-weight: 800; flex-shrink: 0;">
              <?= strtoupper(substr($job['company_name'], 0, 1)) ?>
            </div>
            <div>
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
                <h3 style="font-size: 1.15rem; margin: 0; color: #f8fafc;">
                  <a href="<?= url('/jobs/' . $job['slug']) ?>" style="color: #f8fafc; text-decoration: none;"><?= htmlspecialchars($job['title']) ?></a>
                </h3>
                <?php if (!empty($job['is_featured'])): ?>
                  <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">FEATURED</span>
                <?php endif; ?>
              </div>
              <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; color: #94a3b8; flex-wrap: wrap;">
                <span style="color: #f8fafc; font-weight: 600;">🏢 <?= htmlspecialchars($job['company_name']) ?></span>
                <span>&bull;</span>
                <span>📍 <?= htmlspecialchars($job['location']) ?></span>
                <span>&bull;</span>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;"><?= htmlspecialchars($job['job_type']) ?></span>
              </div>
            </div>
          </div>

          <div style="display: flex; align-items: center; gap: 1rem;">
            <?php if (!empty($job['salary_range'])): ?>
              <div style="text-align: right;">
                <div style="font-size: 0.95rem; font-weight: 700; color: #34d399;"><?= htmlspecialchars($job['salary_range']) ?></div>
                <div style="font-size: 0.75rem; color: #64748b;">Annual Compensation</div>
              </div>
            <?php endif; ?>

            <a href="<?= url('/jobs/' . $job['slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-weight: 600;">
              View Role →
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="ts-card" style="text-align: center; padding: 3.5rem;">
        <i data-lucide="briefcase" style="width: 48px; height: 48px; color: var(--ts-primary); margin-bottom: 1rem;"></i>
        <h3>No Open Positions at This Moment</h3>
        <p style="color: var(--ts-text-secondary); margin-top: 0.5rem;">Check back soon as new engineering positions are updated weekly.</p>
        <a href="<?= url('/') ?>" class="ts-btn ts-btn-primary" style="margin-top: 1rem;">Return to Homepage</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
