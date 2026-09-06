<?php require __DIR__ . '/../layouts/header.php'; ?>

<main class="container" style="padding-top: 2.5rem; padding-bottom: 4rem; min-height: 80vh;">
  <!-- Member Profile Card -->
  <div class="ts-account-header-card" style="padding: 2.5rem; margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
      <div style="display: flex; align-items: center; gap: 1.5rem;">
        <div style="position: relative;">
          <?php if (!empty($member['avatar_url'])): ?>
            <img src="<?= htmlspecialchars(image_url($member['avatar_url'])) ?>" alt="Avatar" style="width: 88px; height: 88px; border-radius: 50%; object-fit: cover; border: 3px solid #38bdf8; box-shadow: 0 0 20px rgba(56,189,248,0.25);">
          <?php else: ?>
            <div style="width: 88px; height: 88px; border-radius: 50%; background: linear-gradient(135deg, #0284c7, #8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 800; color: #ffffff; border: 3px solid rgba(255,255,255,0.2);">
              <?= strtoupper(substr($member['display_name'] ?? $member['username'], 0, 2)) ?>
            </div>
          <?php endif; ?>
          <span style="position: absolute; bottom: 3px; right: 3px; width: 16px; height: 16px; background: #10b981; border-radius: 50%; border: 2px solid var(--ts-bg-surface, #0f172a);"></span>
        </div>

        <div>
          <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
            <h1 style="font-size: 1.85rem; color: var(--ts-text-primary, #f8fafc); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
              <?= htmlspecialchars($member['display_name'] ?? $member['username']) ?>
            </h1>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.75rem; padding: 0.2rem 0.65rem;">
              <?= strtoupper($member['role']) ?>
            </span>
            <?php if (!empty($member['verification_badge']) && $member['verification_badge'] !== 'none'): ?>
              <span class="ts-badge ts-badge-green" style="font-size: 0.75rem; padding: 0.2rem 0.65rem;">
                ✓ <?= htmlspecialchars($member['custom_badge_label'] ?: $member['verification_badge']) ?>
              </span>
            <?php endif; ?>
          </div>

          <div style="font-size: 0.88rem; color: var(--ts-text-muted, #94a3b8); margin-top: 0.35rem;">
            @<?= htmlspecialchars($member['username']) ?> &bull; Member since <?= date('F Y', strtotime($member['created_at'])) ?>
          </div>

          <?php if (!empty($member['headline'])): ?>
            <div style="color: var(--ts-text-secondary, #cbd5e1); font-size: 0.95rem; margin-top: 0.6rem; font-style: italic;">
              &ldquo;<?= htmlspecialchars($member['headline']) ?>&rdquo;
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Action buttons -->
      <div>
        <?php if (\App\Core\Auth::id() === (int)$member['id']): ?>
          <a href="<?= url('/account/settings') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="settings" style="width: 14px; height: 14px;"></i> Edit Settings
          </a>
        <?php elseif (\App\Core\Auth::isAdmin()): ?>
          <a href="<?= url('/admin/users/impersonate/' . $member['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f97316; border-color: rgba(249,115,22,0.4); display: inline-flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="user-check" style="width: 14px; height: 14px;"></i> Impersonate User
          </a>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($member['bio'])): ?>
      <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--ts-border-subtle); color: var(--ts-text-secondary, #cbd5e1); font-size: 0.92rem; line-height: 1.6;">
        <?= nl2br(htmlspecialchars($member['bio'])) ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Member Recent Public Comments / Contributions -->
  <div class="ts-settings-card-surface" style="padding: 1.75rem;">
    <h2 style="font-size: 1.25rem; color: var(--ts-text-primary, #f8fafc); margin-top: 0; margin-bottom: 1.25rem; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 0.5rem;">
      <i data-lucide="message-square" style="color: #38bdf8; width: 20px; height: 20px;"></i>
      Community Discussion Contributions (<?= count($comments ?? []) ?>)
    </h2>

    <?php if (!empty($comments)): ?>
      <div style="display: flex; flex-direction: column; gap: 1rem;">
        <?php foreach ($comments as $cm): ?>
          <div style="background: var(--ts-bg-subtle, rgba(30,41,59,0.5)); border: 1px solid var(--ts-border-subtle, rgba(255,255,255,0.06)); border-radius: 10px; padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <span style="font-size: 0.82rem; color: var(--ts-text-muted, #64748b);">
                Commented on 
                <a href="<?= url('/article/' . ($cm['content_slug'] ?? '')) ?>" style="color: #38bdf8; font-weight: 600; text-decoration: none;">
                  <?= htmlspecialchars($cm['content_title'] ?? 'Technical Publication') ?>
                </a>
              </span>
              <span style="font-size: 0.75rem; color: var(--ts-text-muted, #64748b);"><?= date('M j, Y', strtotime($cm['created_at'])) ?></span>
            </div>
            <div style="color: var(--ts-text-primary, #e2e8f0); font-size: 0.9rem; line-height: 1.5;">
              <?= nl2br(htmlspecialchars($cm['comment_text'])) ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p style="color: var(--ts-text-muted, #64748b); margin: 0; text-align: center; padding: 2rem;">No public comments yet from @<?= htmlspecialchars($member['username']) ?>.</p>
    <?php endif; ?>
  </div>
</main>

<style>
.ts-account-header-card {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.7), rgba(15, 23, 42, 0.85));
  border: 1px solid var(--ts-border-subtle, rgba(255,255,255,0.08));
  border-radius: 16px;
  backdrop-filter: blur(12px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.25);
  transition: all 0.2s ease;
}
[data-theme="light"] .ts-account-header-card,
body.light-mode .ts-account-header-card {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06) !important;
}

.ts-settings-card-surface {
  background: rgba(15, 23, 42, 0.8);
  border: 1px solid var(--ts-border-subtle, rgba(255,255,255,0.08));
  border-radius: 14px;
  backdrop-filter: blur(8px);
}
[data-theme="light"] .ts-settings-card-surface,
body.light-mode .ts-settings-card-surface {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04) !important;
}
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
