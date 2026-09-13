<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
  // Helper to parse JSON fields safely
  $parseJsonArray = function($val) {
    if (empty($val)) return [];
    if (is_array($val)) return $val;
    try {
      $decoded = json_decode($val, true);
      if (is_array($decoded)) return $decoded;
    } catch (\Throwable $e) {}
    $lines = preg_split('/[\r\n,]+/', (string)$val);
    return array_values(array_filter(array_map('trim', $lines)));
  };

  $features = $parseJsonArray($tool['features_list'] ?? '');
  $platforms = $parseJsonArray($tool['platforms_supported'] ?? '');
  $alternatives = $parseJsonArray($tool['alternatives_list'] ?? '');
  $pros = $parseJsonArray($tool['pros_list'] ?? '');
  $cons = $parseJsonArray($tool['cons_list'] ?? '');
  $useCases = $parseJsonArray($tool['use_cases'] ?? '');
  $screenshots = $parseJsonArray($tool['screenshots_gallery'] ?? '');

  // Extract custom links from internal_notes
  $internalNotes = [];
  try {
    $internalNotes = json_decode($tool['internal_notes'] ?? '{}', true) ?: [];
  } catch (\Throwable $e) {}
  $customLinks = is_array($internalNotes['custom_links'] ?? null) ? $internalNotes['custom_links'] : [];

  $officialWebsite = url('/go/' . $tool['slug']);
  $affiliateLink = url('/go/' . $tool['slug']);
  $ratingAvg = (float)($tool['rating_avg'] ?? $tool['tool_rating'] ?? 4.85);
  $ratingEase = (float)($tool['rating_ease_of_use'] ?? 4.80);
  $ratingValue = (float)($tool['rating_value_for_money'] ?? 4.70);
  $ratingFeat = (float)($tool['rating_features'] ?? 4.90);
  $ratingPerf = (float)($tool['rating_performance'] ?? 4.85);
  $ratingSupp = (float)($tool['rating_support'] ?? 4.60);
?>

<style>
  /* Tool Single Universal Fluid & Dual-Theme Responsive Rules */
  .tool-single-container {
    max-width: 960px;
    margin: 0 auto;
    width: 100%;
    overflow-x: hidden;
  }
  .tool-hero-wrap {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
  }
  .tool-hero-avatar {
    width: 110px;
    height: 110px;
    border-radius: 16px;
    object-fit: cover;
    border: 1px solid var(--ts-border-subtle, rgba(255,255,255,0.12));
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    flex-shrink: 0;
  }
  .tool-hero-avatar-fallback {
    width: 110px;
    height: 110px;
    border-radius: 16px;
    background: linear-gradient(135deg, #6366f1, #ec4899);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 2.5rem;
    font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
  }
  .tool-hero-info {
    flex: 1;
    min-width: 0;
    width: 100%;
  }
  .tool-hero-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
  }
  .tool-pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
  }
  .tool-radar-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
  }
  .tool-proscons-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
  }
  .tool-features-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
  }
  .tool-table-cell-header {
    padding: 0.75rem 0;
    color: var(--ts-text-tertiary);
    width: 200px;
    vertical-align: top;
  }
  .tool-table-cell-value {
    padding: 0.75rem 0;
    word-break: break-all;
  }

  @media (max-width: 768px) {
    .tool-hero-wrap {
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 1.25rem;
    }
    .tool-hero-avatar, .tool-hero-avatar-fallback {
      width: 90px;
      height: 90px;
      font-size: 2rem;
      margin: 0 auto;
    }
    .tool-hero-info {
      text-align: center;
    }
    .tool-badge-strip {
      justify-content: center !important;
    }
    .tool-hero-meta {
      justify-content: center !important;
    }
    .tool-usecases-strip {
      justify-content: center !important;
    }
    .tool-hero-actions {
      display: flex;
      flex-direction: column;
      width: 100%;
      gap: 0.6rem !important;
    }
    .tool-hero-actions > * {
      width: 100% !important;
      justify-content: center;
      text-align: center;
    }
    .tool-pricing-grid {
      grid-template-columns: 1fr !important;
      gap: 0.75rem;
    }
    .tool-radar-grid {
      grid-template-columns: 1fr !important;
      gap: 1.25rem;
    }
    .tool-radar-grid > div {
      grid-column: auto !important;
    }
    .tool-proscons-grid {
      grid-template-columns: 1fr !important;
      gap: 1rem;
    }
    .tool-features-grid {
      grid-template-columns: 1fr !important;
      gap: 0.5rem;
    }
    .tool-ratings-header {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 0.75rem;
    }
  }

  @media (max-width: 520px) {
    .tool-meta-table tr {
      display: flex;
      flex-direction: column;
      padding: 0.6rem 0;
      border-bottom: 1px solid var(--ts-border-subtle);
    }
    .tool-meta-table td {
      width: 100% !important;
      padding: 0.15rem 0 !important;
    }
    .tool-meta-table .tool-table-cell-header {
      font-weight: 700;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
  }
</style>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div class="tool-single-container">
    
    <!-- Breadcrumb Bar -->
    <div style="display: flex; gap: 0.5rem; align-items: center; font-size: 0.85rem; color: var(--ts-text-tertiary); margin-bottom: 1.5rem; flex-wrap: wrap;">
      <a href="<?= url('/') ?>" style="color: var(--ts-text-tertiary); text-decoration: none;">Home</a> <span>/</span>
      <a href="<?= url('/tools') ?>" style="color: var(--ts-text-tertiary); text-decoration: none;">AI Tools Directory</a> <span>/</span>
      <a href="<?= url('/category/' . ($tool['category_slug'] ?? 'ai')) ?>" style="color: var(--ts-accent-light); font-weight: 600; text-decoration: none;">
        <?= htmlspecialchars($tool['category_name'] ?? 'Machine Learning') ?>
      </a>
    </div>

    <!-- 1. Tool Header Hero Card -->
    <div class="ts-card" style="padding: 2rem; margin-bottom: 2rem; border: 1px solid rgba(99,102,241,0.25); position: relative; overflow: hidden;">
      <div style="position: absolute; top: -50px; right: -50px; width: 180px; height: 180px; background: radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%); pointer-events: none;"></div>

      <div class="tool-hero-wrap">
        <?php if (!empty($tool['featured_image_url'])): ?>
          <img src="<?= htmlspecialchars(image_url($tool['featured_image_url'])) ?>" alt="<?= htmlspecialchars($tool['title']) ?> Logo" class="tool-hero-avatar">
        <?php else: ?>
          <div class="tool-hero-avatar-fallback">
            <?= strtoupper(substr($tool['title'], 0, 1)) ?>
          </div>
        <?php endif; ?>

        <div class="tool-hero-info">
          <div class="tool-badge-strip" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem; align-items: center; flex-wrap: wrap;">
            <span class="ts-badge ts-badge-pink" style="text-transform: capitalize; font-weight: 700;">
              <?= htmlspecialchars($tool['pricing_model'] ?? 'Freemium') ?>
            </span>
            <span class="ts-badge ts-badge-cyan" style="font-weight: 700;">
              ⭐ <?= number_format($ratingAvg, 1) ?> / 5.0 (<?= $tool['tool_reviews'] ?? 420 ?> benchmark reviews)
            </span>
            <?php if (!empty($tool['verified_by_techscout'])): ?>
              <span class="ts-badge ts-badge-green" style="font-weight: 700;">
                <i data-lucide="check-circle" style="width: 12px; height: 12px; vertical-align: -1px; display: inline-block;"></i> TechScout Verified
              </span>
            <?php endif; ?>
            <?php if (!empty($tool['api_available'])): ?>
              <span class="ts-badge ts-badge-blue">API Available</span>
            <?php endif; ?>
          </div>

          <h1 style="font-size: 2.2rem; margin: 0 0 0.5rem 0; color: var(--ts-text-primary); font-family: 'Outfit', sans-serif; word-break: break-word; line-height: 1.25;">
            <?= htmlspecialchars($tool['title']) ?>
          </h1>

          <div class="tool-hero-meta" style="font-size: 0.82rem; color: var(--ts-text-tertiary); margin-bottom: 0.85rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <span>Added <?= \App\Core\DateHelper::render($tool['created_at'] ?? 'now', 'M j, Y • g:i A', 'datetime') ?></span>
            <?php if (!empty($tool['updated_at']) && strtotime($tool['updated_at']) > strtotime($tool['created_at'] ?? 'now') + 60): ?>
              <span style="color: #38bdf8;">&bull; Updated <?= \App\Core\DateHelper::render($tool['updated_at'], 'M j, Y • g:i A', 'datetime') ?></span>
            <?php endif; ?>
            <span>&bull; Category: <strong style="color: var(--ts-text-secondary);"><?= htmlspecialchars($tool['category_name'] ?? 'AI') ?></strong></span>
          </div>

          <!-- Use Cases Tags -->
          <?php if (!empty($useCases)): ?>
            <div class="tool-usecases-strip" style="display: flex; gap: 0.4rem; margin-bottom: 1rem; flex-wrap: wrap;">
              <?php foreach ($useCases as $uc): ?>
                <span class="ts-badge" style="font-size: 0.75rem; background: var(--ts-bg-surface-2, rgba(99,102,241,0.08)); color: var(--ts-text-secondary); border: 1px solid var(--ts-border-subtle, rgba(148,163,184,0.15));">
                  <?= htmlspecialchars($uc) ?>
                </span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <p style="color: var(--ts-text-secondary); font-size: 1.02rem; line-height: 1.6; margin-bottom: 1.25rem;">
            <?= htmlspecialchars($tool['summary']) ?>
          </p>

          <!-- Launch & CTA Action Buttons -->
          <div class="tool-hero-actions">
            <a href="<?= htmlspecialchars($officialWebsite) ?>" target="_blank" rel="noopener noreferrer" class="ts-btn ts-btn-primary" style="font-weight: 700; padding: 0.6rem 1.25rem;">
              <i data-lucide="external-link"></i> Launch Official Website ↗
            </a>

            <?php if (!empty($tool['affiliate_url']) || !empty($tool['discount_code']) || !empty($tool['discount_deal'])): ?>
              <a href="<?= htmlspecialchars($affiliateLink) ?>" target="_blank" rel="noopener noreferrer" class="ts-btn ts-btn-accent" style="font-weight: 700; padding: 0.6rem 1.25rem; background: linear-gradient(135deg, #ec4899, #f59e0b); border: none;">
                <i data-lucide="tag"></i> <?= htmlspecialchars($tool['discount_deal'] ?: 'Claim Exclusive Deal 🎁') ?>
              </a>
            <?php endif; ?>

            <button type="button" onclick="toggleBookmark(<?= (int)$tool['id'] ?>)" id="content-bookmark-btn" class="ts-btn <?= !empty($isBookmarked) ? 'ts-btn-primary' : 'ts-btn-outline' ?> ts-btn-sm" style="<?= !empty($isBookmarked) ? 'background:#ec4899; border-color:#ec4899;' : 'color:#ec4899; border-color:rgba(236,72,153,0.3);' ?>">
              <span><?= !empty($isBookmarked) ? '🔖 Saved in Library' : '🔖 Save Bookmark' ?></span>
            </button>

            <button type="button" onclick="openReportModal('ai_tool', <?= (int)$tool['id'] ?>, '<?= htmlspecialchars(addslashes($tool['title'])) ?>', window.location.href)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: var(--ts-text-tertiary); font-size: 0.78rem;">
              <i data-lucide="flag" style="width: 12px; height: 12px; color: #ef4444;"></i> Report Tool
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Commercial Pricing & Deal Box -->
    <div class="tool-pricing-grid">
      <div class="ts-card" style="padding: 1.25rem; text-align: center;">
        <span style="font-size: 0.78rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 0.35rem;">Pricing Structure</span>
        <div style="font-size: 1.4rem; font-weight: 800; color: #38bdf8;">
          <?= htmlspecialchars($tool['pricing_starting_at'] ?? '$0') ?>
        </div>
        <span style="font-size: 0.78rem; color: var(--ts-text-tertiary);">billing per <?= htmlspecialchars($tool['pricing_billing_cycle'] ?? 'month') ?></span>
      </div>

      <div class="ts-card" style="padding: 1.25rem; text-align: center;">
        <span style="font-size: 0.78rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 0.35rem;">Free Trial Offer</span>
        <div style="font-size: 1.4rem; font-weight: 800; color: #10b981;">
          <?= !empty($tool['free_trial_available']) ? ((int)($tool['free_trial_days'] ?? 14) . ' Days Free') : 'No Trial' ?>
        </div>
        <span style="font-size: 0.78rem; color: var(--ts-text-tertiary);">no credit card required</span>
      </div>

      <div class="ts-card" style="padding: 1.25rem; text-align: center;">
        <span style="font-size: 0.78rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 0.35rem;">Verified Coupon Code</span>
        <div style="font-size: 1.2rem; font-weight: 800; color: #ec4899; font-family: var(--ts-font-mono, monospace);">
          <?= !empty($tool['discount_code']) ? htmlspecialchars($tool['discount_code']) : 'None Active' ?>
        </div>
        <span style="font-size: 0.78rem; color: var(--ts-text-tertiary);"><?= htmlspecialchars($tool['discount_deal'] ?: 'standard rates') ?></span>
      </div>
    </div>

    <!-- 3. 5-Dimensional Benchmark Ratings Radar HUD -->
    <div class="ts-card" style="padding: 1.75rem; margin-bottom: 2rem; border: 1px solid rgba(245,158,11,0.25);">
      <div class="tool-ratings-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.25rem; margin: 0 0 0.25rem 0; color: var(--ts-text-primary); display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="award" style="color: #f59e0b;"></i> TechScout 5-Dimensional Benchmark Score
          </h3>
          <span style="font-size: 0.82rem; color: var(--ts-text-tertiary);">Evaluated across 5 core developer vectors by technical editors</span>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; background: rgba(245,158,11,0.1); padding: 0.5rem 1rem; border-radius: 10px; border: 1px solid rgba(245,158,11,0.3);">
          <span style="font-size: 1.8rem; font-weight: 900; color: #f59e0b; font-family: 'Outfit', sans-serif;">⭐ <?= number_format($ratingAvg, 1) ?></span>
          <span style="font-size: 0.8rem; color: var(--ts-text-secondary); line-height: 1.2;">Overall<br>Score</span>
        </div>
      </div>

      <div class="tool-radar-grid">
        <!-- Ease of Use -->
        <div>
          <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--ts-text-secondary);">
            <span>⚡ Ease of Use &amp; DX</span>
            <strong style="color: var(--ts-text-primary);"><?= number_format($ratingEase, 1) ?> / 5.0</strong>
          </div>
          <div style="height: 8px; background: var(--ts-border-subtle, rgba(148,163,184,0.2)); border-radius: 4px; overflow: hidden;">
            <div style="height: 100%; width: <?= ($ratingEase / 5.0) * 100 ?>%; background: linear-gradient(90deg, #38bdf8, #6366f1); border-radius: 4px;"></div>
          </div>
        </div>

        <!-- Value for Money -->
        <div>
          <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--ts-text-secondary);">
            <span>💎 Value for Money</span>
            <strong style="color: var(--ts-text-primary);"><?= number_format($ratingValue, 1) ?> / 5.0</strong>
          </div>
          <div style="height: 8px; background: var(--ts-border-subtle, rgba(148,163,184,0.2)); border-radius: 4px; overflow: hidden;">
            <div style="height: 100%; width: <?= ($ratingValue / 5.0) * 100 ?>%; background: linear-gradient(90deg, #10b981, #38bdf8); border-radius: 4px;"></div>
          </div>
        </div>

        <!-- Feature Depth -->
        <div>
          <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--ts-text-secondary);">
            <span>🛠️ Feature Set &amp; Breadth</span>
            <strong style="color: var(--ts-text-primary);"><?= number_format($ratingFeat, 1) ?> / 5.0</strong>
          </div>
          <div style="height: 8px; background: var(--ts-border-subtle, rgba(148,163,184,0.2)); border-radius: 4px; overflow: hidden;">
            <div style="height: 100%; width: <?= ($ratingFeat / 5.0) * 100 ?>%; background: linear-gradient(90deg, #ec4899, #8b5cf6); border-radius: 4px;"></div>
          </div>
        </div>

        <!-- Speed & Performance -->
        <div>
          <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--ts-text-secondary);">
            <span>🚀 Speed, Latency &amp; Reliability</span>
            <strong style="color: var(--ts-text-primary);"><?= number_format($ratingPerf, 1) ?> / 5.0</strong>
          </div>
          <div style="height: 8px; background: var(--ts-border-subtle, rgba(148,163,184,0.2)); border-radius: 4px; overflow: hidden;">
            <div style="height: 100%; width: <?= ($ratingPerf / 5.0) * 100 ?>%; background: linear-gradient(90deg, #f59e0b, #ec4899); border-radius: 4px;"></div>
          </div>
        </div>

        <!-- Support & Docs -->
        <div style="grid-column: span 2;">
          <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--ts-text-secondary);">
            <span>💬 Documentation, API Specs &amp; Community Support</span>
            <strong style="color: var(--ts-text-primary);"><?= number_format($ratingSupp, 1) ?> / 5.0</strong>
          </div>
          <div style="height: 8px; background: var(--ts-border-subtle, rgba(148,163,184,0.2)); border-radius: 4px; overflow: hidden;">
            <div style="height: 100%; width: <?= ($ratingSupp / 5.0) * 100 ?>%; background: linear-gradient(90deg, #06b6d4, #10b981); border-radius: 4px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- 4. Editorial Verdict, Pros & Cons Comparison -->
    <div style="margin-bottom: 2rem;">
      <?php if (!empty($tool['editorial_review'])): ?>
        <div class="ts-card" style="padding: 1.5rem; margin-bottom: 1.5rem; background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.3); border-radius: 10px;">
          <h4 style="margin: 0 0 0.5rem 0; color: #38bdf8; font-size: 1.05rem; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="file-check-2"></i> TechScout Editorial Review &amp; Verdict
          </h4>
          <p style="color: var(--ts-text-secondary); font-size: 0.96rem; line-height: 1.7; margin: 0;">
            <?= nl2br(htmlspecialchars($tool['editorial_review'])) ?>
          </p>
        </div>
      <?php endif; ?>

      <?php if (!empty($pros) || !empty($cons)): ?>
        <div class="tool-proscons-grid">
          <!-- Pros Card -->
          <div class="ts-card" style="padding: 1.5rem; border: 1px solid rgba(16,185,129,0.3); background: rgba(16,185,129,0.04);">
            <h4 style="margin: 0 0 1rem 0; color: #10b981; font-size: 1rem; display: flex; align-items: center; gap: 0.4rem;">
              <i data-lucide="thumbs-up"></i> Key Advantages (Pros)
            </h4>
            <ul style="margin: 0; padding-left: 1.2rem; color: var(--ts-text-secondary); font-size: 0.92rem; line-height: 1.7;">
              <?php foreach ($pros as $p): ?>
                <li style="margin-bottom: 0.35rem;"><?= htmlspecialchars($p) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- Cons Card -->
          <div class="ts-card" style="padding: 1.5rem; border: 1px solid rgba(239,68,68,0.3); background: rgba(239,68,68,0.04);">
            <h4 style="margin: 0 0 1rem 0; color: #ef4444; font-size: 1rem; display: flex; align-items: center; gap: 0.4rem;">
              <i data-lucide="thumbs-down"></i> Potential Limitations (Cons)
            </h4>
            <ul style="margin: 0; padding-left: 1.2rem; color: var(--ts-text-secondary); font-size: 0.92rem; line-height: 1.7;">
              <?php foreach ($cons as $c): ?>
                <li style="margin-bottom: 0.35rem;"><?= htmlspecialchars($c) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- 5. Features & Platform Capabilities Checklist -->
    <?php if (!empty($features) || !empty($platforms)): ?>
      <div class="ts-card" style="padding: 1.75rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; margin: 0 0 1.25rem 0; color: var(--ts-text-primary);">
          Key Features &amp; Platform Support
        </h3>

        <?php if (!empty($platforms)): ?>
          <div style="margin-bottom: 1.5rem;">
            <span style="font-size: 0.8rem; color: var(--ts-text-tertiary); font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 0.5rem;">Supported Environments:</span>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
              <?php foreach ($platforms as $plat): ?>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.8rem; padding: 0.25rem 0.65rem;">
                  <?= htmlspecialchars($plat) ?>
                </span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($features)): ?>
          <div class="tool-features-grid">
            <?php foreach ($features as $f): ?>
              <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.92rem; color: var(--ts-text-secondary);">
                <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981; flex-shrink: 0;"></i>
                <span><?= htmlspecialchars($f) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- 6. Alternatives & Competitor Comparison Highlights -->
    <?php if (!empty($alternatives) || !empty($tool['comparison_highlights'])): ?>
      <div class="ts-card" style="padding: 1.75rem; margin-bottom: 2rem; border: 1px solid rgba(56,189,248,0.25);">
        <h3 style="font-size: 1.25rem; margin: 0 0 1rem 0; color: var(--ts-text-primary); display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="git-compare" style="color: #38bdf8;"></i> Top Alternatives &amp; Competitor Comparison
        </h3>

        <?php if (!empty($alternatives)): ?>
          <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem; align-items: center;">
            <span style="font-size: 0.82rem; color: var(--ts-text-tertiary); font-weight: 600;">Similar Competitors:</span>
            <?php foreach ($alternatives as $alt): ?>
              <a href="<?= url('/tools?q=' . urlencode($alt)) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem; padding: 0.2rem 0.6rem;">
                <?= htmlspecialchars($alt) ?> ↗
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($tool['comparison_highlights'])): ?>
          <div style="background: var(--ts-bg-surface-2, rgba(99,102,241,0.06)); padding: 1.25rem; border-radius: 8px; border-left: 3px solid #38bdf8; font-size: 0.95rem; color: var(--ts-text-secondary); line-height: 1.7;">
            <?= nl2br(htmlspecialchars($tool['comparison_highlights'])) ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- 7. Screenshots Gallery -->
    <?php if (!empty($screenshots)): ?>
      <div class="ts-card" style="padding: 1.75rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; margin: 0 0 1.25rem 0; color: var(--ts-text-primary); display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="image" style="color: #ec4899;"></i> Product Interface &amp; Screenshots
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
          <?php foreach ($screenshots as $imgUrl): ?>
            <div style="border-radius: 8px; overflow: hidden; border: 1px solid var(--ts-border-subtle, rgba(255,255,255,0.1)); cursor: pointer;" onclick="openScreenshotModal('<?= htmlspecialchars(addslashes($imgUrl)) ?>')">
              <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Screenshot" style="width: 100%; height: 160px; object-fit: cover; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- 8. In-Depth Overview & Architecture Documentation -->
    <?php if (!empty($tool['body_html'])): ?>
      <div class="ts-card" style="padding: 2rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.35rem; margin: 0 0 1.25rem 0; color: var(--ts-text-primary);">
          In-Depth Platform Architecture &amp; Guide
        </h3>
        <div class="article-body-content" style="font-size: 1.02rem; line-height: 1.8; color: var(--ts-text-secondary);">
          <?= $tool['body_html'] ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- 9. Official Links & Tech Specifications Table -->
    <div class="ts-card" style="margin-bottom: 2rem; padding: 1.75rem;">
      <h3 style="font-size: 1.25rem; margin: 0 0 1.25rem 0; color: var(--ts-text-primary);">Official Links &amp; Technical Metadata</h3>
      <table class="tool-meta-table" style="width: 100%; border-collapse: collapse; font-size: 0.92rem;">
        <tr style="border-bottom: 1px solid var(--ts-border-main);">
          <td class="tool-table-cell-header">Official Website</td>
          <td class="tool-table-cell-value">
            <a href="<?= url('/go/' . $tool['slug']) ?>" target="_blank" rel="noopener noreferrer" style="color: #38bdf8; text-decoration: none; font-weight: 600;">
              <?= htmlspecialchars(parse_url($tool['website_url'] ?? '', PHP_URL_HOST) ?: ($tool['title'] . ' Official Site')) ?> ↗
            </a>
          </td>
        </tr>
        <?php if (!empty($tool['docs_url'])): ?>
          <tr style="border-bottom: 1px solid var(--ts-border-main);">
            <td class="tool-table-cell-header">API Documentation</td>
            <td class="tool-table-cell-value">
              <a href="<?= url('/go/' . $tool['slug'] . '?type=docs') ?>" target="_blank" rel="noopener noreferrer" style="color: #38bdf8; text-decoration: none;">
                View Official Documentation ↗
              </a>
            </td>
          </tr>
        <?php endif; ?>
        <?php if (!empty($tool['open_source_repo'])): ?>
          <tr style="border-bottom: 1px solid var(--ts-border-main);">
            <td class="tool-table-cell-header">GitHub Repository</td>
            <td class="tool-table-cell-value">
              <a href="<?= url('/go/' . $tool['slug'] . '?type=repo') ?>" target="_blank" rel="noopener noreferrer" style="color: #38bdf8; text-decoration: none;">
                <?= htmlspecialchars(basename($tool['open_source_repo']) ?: 'GitHub Repository') ?> ↗
              </a>
            </td>
          </tr>
        <?php endif; ?>
        <?php if (!empty($tool['discord_url'])): ?>
          <tr style="border-bottom: 1px solid var(--ts-border-main);">
            <td class="tool-table-cell-header">Discord Community</td>
            <td class="tool-table-cell-value">
              <a href="<?= url('/go/' . $tool['slug'] . '?type=discord') ?>" target="_blank" rel="noopener noreferrer" style="color: #818cf8; text-decoration: none;">
                Join Discord Server ↗
              </a>
            </td>
          </tr>
        <?php endif; ?>
        <?php if (!empty($tool['twitter_handle'])): ?>
          <tr style="border-bottom: 1px solid var(--ts-border-main);">
            <td class="tool-table-cell-header">Twitter / 𝕏 Handle</td>
            <td class="tool-table-cell-value" style="font-weight: 600; color: var(--ts-text-primary);">
              <?= htmlspecialchars($tool['twitter_handle']) ?>
            </td>
          </tr>
        <?php endif; ?>
        <?php if (!empty($customLinks)): ?>
          <?php foreach ($customLinks as $cLink): ?>
            <?php if (!empty($cLink['name']) && !empty($cLink['url'])): ?>
              <tr style="border-bottom: 1px solid var(--ts-border-main);">
                <td class="tool-table-cell-header"><?= htmlspecialchars($cLink['name']) ?></td>
                <td class="tool-table-cell-value">
                  <a href="<?= htmlspecialchars($cLink['url']) ?>" target="_blank" rel="noopener noreferrer" style="color: #38bdf8; text-decoration: none; font-weight: 600;">
                    <?= htmlspecialchars(parse_url($cLink['url'], PHP_URL_HOST) ?: $cLink['name']) ?> ↗
                  </a>
                </td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
        <tr>
          <td class="tool-table-cell-header">License Type</td>
          <td class="tool-table-cell-value" style="font-weight: 600; color: var(--ts-text-secondary);">
            <?= htmlspecialchars($tool['license_type'] ?? 'Proprietary') ?>
          </td>
        </tr>
      </table>
    </div>

    <!-- 10. Tags -->
    <?php $toolTags = \App\Core\TagHelper::getTagsForContent((int)$tool['id']); ?>
    <?php if (!empty($toolTags)): ?>
      <div style="display: flex; gap: 0.5rem; margin: 2rem 0; flex-wrap: wrap; align-items: center;">
        <span style="font-size: 0.8rem; color: var(--ts-text-tertiary); font-weight: 600;">TAGS:</span>
        <?php foreach ($toolTags as $tt): ?>
          <a href="<?= url('/tag/' . $tt['slug']) ?>" class="ts-badge ts-badge-cyan" style="text-decoration: none;">
            #<?= htmlspecialchars($tt['name']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- 11. Social Sharing Distribution Bar -->
    <?php 
      $shareTitle = $tool['title'] . ' — AI Tool Review, Pricing & Alternatives';
      $shareUrl = url('/tools/' . $tool['slug']);
      $shareSummary = $tool['summary'];
      require __DIR__ . '/../components/social_share.php'; 
    ?>
  </div>
</div>

<!-- Screenshot Full-Screen Modal -->
<div id="screenshotModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.92); backdrop-filter: blur(10px); z-index: 999999; align-items: center; justify-content: center; padding: 2rem;" onclick="this.style.display='none'">
  <div style="max-width: 90vw; max-height: 90vh;">
    <img id="modalScreenshotImg" src="" alt="Full Preview" style="max-width: 100%; max-height: 85vh; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 25px 60px rgba(0,0,0,0.95);">
  </div>
</div>

<script>
function openScreenshotModal(url) {
  document.getElementById('modalScreenshotImg').src = url;
  document.getElementById('screenshotModal').style.display = 'flex';
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
