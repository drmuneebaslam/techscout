<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem; max-width: 1200px;">
  <!-- Breadcrumb & Top Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <a href="<?= url('/author/dashboard') ?>" style="color: #38bdf8; text-decoration: none; font-size: 0.85rem;">&larr; Creator Studio</a>
        <span style="color: var(--ts-text-tertiary);">&bull;</span>
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;">TELEMETRY ENGINE</span>
      </div>
      <h1 style="font-size: 2rem; font-family: 'Outfit', sans-serif; font-weight: 800; color: var(--ts-text-primary); margin: 0;">
        Cross-Entity Telemetry &amp; Readership Intelligence
      </h1>
    </div>

    <div style="display: flex; gap: 0.6rem; align-items: center;">
      <a href="<?= url('/author/dashboard') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="layout-dashboard"></i> Studio Dashboard
      </a>
      <a href="<?= url('/author/editor') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
        <i data-lucide="plus-circle"></i> Create Publication ⚡
      </a>
    </div>
  </div>

  <!-- Realtime KPI Metric Grid -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 220px), 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="ts-card" style="padding: 1.5rem; background: linear-gradient(135deg, rgba(15,23,42,0.9), rgba(10,18,30,0.85)); border: 1px solid rgba(56,189,248,0.25);">
      <div style="font-size: 0.78rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 0.05em;">Total Lifetime Reads</div>
      <div style="font-size: 2.2rem; font-weight: 800; color: #38bdf8; margin-top: 0.35rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($totalViews) ?>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem;">Across all publications &amp; tools</div>
    </div>

    <div class="ts-card" style="padding: 1.5rem; background: linear-gradient(135deg, rgba(15,23,42,0.9), rgba(10,18,30,0.85)); border: 1px solid rgba(236,72,153,0.25);">
      <div style="font-size: 0.78rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 0.05em;">Total Reactions</div>
      <div style="font-size: 2.2rem; font-weight: 800; color: #ec4899; margin-top: 0.35rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($totalReactions) ?>
      </div>
      <div style="font-size: 0.75rem; color: #f472b6; margin-top: 0.35rem;">🔥 Fire, 🚀 Rockets &amp; 💡 Insights</div>
    </div>

    <div class="ts-card" style="padding: 1.5rem; background: linear-gradient(135deg, rgba(15,23,42,0.9), rgba(10,18,30,0.85)); border: 1px solid rgba(245,158,11,0.25);">
      <div style="font-size: 0.78rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 0.05em;">Bookmarks &amp; Saves</div>
      <div style="font-size: 2.2rem; font-weight: 800; color: #fbbf24; margin-top: 0.35rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($totalBookmarks) ?>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem;">Saved in reader personal libraries</div>
    </div>

    <div class="ts-card" style="padding: 1.5rem; background: linear-gradient(135deg, rgba(15,23,42,0.9), rgba(10,18,30,0.85)); border: 1px solid rgba(16,185,129,0.25);">
      <div style="font-size: 0.78rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 0.05em;">Discussions &amp; Replies</div>
      <div style="font-size: 2.2rem; font-weight: 800; color: #34d399; margin-top: 0.35rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($totalComments) ?>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem;">Peer reviews and technical discourse</div>
    </div>
  </div>

  <!-- 30-Day Readership Trend & Traffic Acquisition -->
  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem;">
    <!-- Telemetry Chart -->
    <div class="ts-card" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h3 style="font-size: 1.1rem; color: var(--ts-text-primary); margin: 0; font-weight: 700;">
          📈 30-Day Readership &amp; Discovery Velocity
        </h3>
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">Live Aggregate</span>
      </div>
      <canvas id="authorTelemetryChart" style="width: 100%; max-height: 260px;"></canvas>
    </div>

    <!-- Acquisition & Devices -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <!-- Devices Breakdown -->
      <div class="ts-card" style="padding: 1.25rem;">
        <h4 style="font-size: 0.95rem; margin: 0 0 1rem 0; font-weight: 700; color: var(--ts-text-primary);">💻 Reader Devices</h4>
        <?php 
          $devTotal = max(1, ($devices['desktop'] ?? 0) + ($devices['mobile'] ?? 0) + ($devices['tablet'] ?? 0));
          $dPct = round((($devices['desktop'] ?? 0) / $devTotal) * 100);
          $mPct = round((($devices['mobile'] ?? 0) / $devTotal) * 100);
          $tPct = round((($devices['tablet'] ?? 0) / $devTotal) * 100);
        ?>
        <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.82rem;">
          <div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.2rem; color: var(--ts-text-secondary);">
              <span>Desktop / Workstation</span>
              <strong style="color: #38bdf8;"><?= $dPct ?>%</strong>
            </div>
            <div style="width: 100%; height: 6px; background: rgba(148,163,184,0.15); border-radius: 9999px; overflow: hidden;">
              <div style="width: <?= $dPct ?>%; height: 100%; background: #38bdf8;"></div>
            </div>
          </div>
          <div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.2rem; color: var(--ts-text-secondary);">
              <span>Mobile Phone</span>
              <strong style="color: #ec4899;"><?= $mPct ?>%</strong>
            </div>
            <div style="width: 100%; height: 6px; background: rgba(148,163,184,0.15); border-radius: 9999px; overflow: hidden;">
              <div style="width: <?= $mPct ?>%; height: 100%; background: #ec4899;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Referrers -->
      <div class="ts-card" style="padding: 1.25rem;">
        <h4 style="font-size: 0.95rem; margin: 0 0 0.85rem 0; font-weight: 700; color: var(--ts-text-primary);">🌐 Top Inbound Sources</h4>
        <div style="display: flex; flex-direction: column; gap: 0.45rem; font-size: 0.82rem;">
          <?php if (!empty($topReferrers)): ?>
            <?php foreach ($topReferrers as $ref): ?>
              <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.3rem 0; border-bottom: 1px solid rgba(148,163,184,0.08);">
                <span style="color: var(--ts-text-secondary);"><?= htmlspecialchars($ref['domain']) ?></span>
                <span class="ts-badge" style="font-size: 0.68rem; font-family: monospace;"><?= number_format($ref['count']) ?> visits</span>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div style="color: var(--ts-text-tertiary); font-size: 0.8rem;">Direct / In-App Platform Readers</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Entity Telemetry Filter Bar & Publications Roster -->
  <div class="ts-card" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h3 style="font-size: 1.2rem; color: var(--ts-text-primary); margin: 0 0 0.25rem 0; font-weight: 700;">
          All Published Entities &amp; Individual Telemetry
        </h3>
        <p style="font-size: 0.82rem; color: var(--ts-text-secondary); margin: 0;">
          Select an entity to view its deep-dive scroll retention funnel, reader watch time, and engagement curve.
        </p>
      </div>

      <!-- Filter Entity Tabs -->
      <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
        <button type="button" onclick="filterEntities('all')" id="efilter_all" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.75rem;">All Entities (<?= count($contents) ?>)</button>
        <button type="button" onclick="filterEntities('article')" id="efilter_article" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">Articles</button>
        <button type="button" onclick="filterEntities('ai_tool')" id="efilter_ai_tool" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">AI Tools</button>
        <button type="button" onclick="filterEntities('prompt')" id="efilter_prompt" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">Prompts</button>
        <button type="button" onclick="filterEntities('gpt_listing')" id="efilter_gpt_listing" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">Custom GPTs</button>
      </div>
    </div>

    <!-- Publications Table -->
    <div style="overflow-x: auto;">
      <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
        <thead>
          <tr style="border-bottom: 1px solid var(--ts-border-main); text-align: left; color: var(--ts-text-tertiary); font-size: 0.75rem; text-transform: uppercase;">
            <th style="padding: 0.75rem 1rem;">Publication Title</th>
            <th style="padding: 0.75rem 1rem;">Type</th>
            <th style="padding: 0.75rem 1rem;">Status</th>
            <th style="padding: 0.75rem 1rem;">Reads / Views</th>
            <th style="padding: 0.75rem 1rem;">Reactions</th>
            <th style="padding: 0.75rem 1rem;">Bookmarks</th>
            <th style="padding: 0.75rem 1rem;">Comments</th>
            <th style="padding: 0.75rem 1rem; text-align: right;">Telemetry Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($contents)): ?>
            <?php foreach ($contents as $item): ?>
              <?php
                $targetUrl = url('/article/' . $item['slug']);
                if ($item['content_type'] === 'ai_tool') $targetUrl = url('/tools/' . $item['slug']);
                elseif ($item['content_type'] === 'prompt') $targetUrl = url('/prompts/' . $item['slug']);
                elseif ($item['content_type'] === 'gpt_listing') $targetUrl = url('/gpts/' . $item['slug']);
                elseif ($item['content_type'] === 'campaign') $targetUrl = url('/campaigns/' . $item['slug']);
              ?>
              <tr class="entity-row" data-type="<?= htmlspecialchars($item['content_type']) ?>" style="border-bottom: 1px solid rgba(148,163,184,0.06);">
                <td style="padding: 0.85rem 1rem; max-width: 320px;">
                  <a href="<?= url('/author/analytics/content/' . $item['id']) ?>" style="color: var(--ts-text-primary); font-weight: 600; text-decoration: none; font-size: 0.92rem; display: block;">
                    <?= htmlspecialchars($item['title']) ?>
                  </a>
                  <span style="font-size: 0.72rem; color: var(--ts-text-tertiary); font-family: var(--ts-font-mono);">
                    <?= \App\Core\DateHelper::formatDateTime($item['created_at']) ?>
                  </span>
                </td>
                <td style="padding: 0.85rem 1rem;">
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;"><?= strtoupper(htmlspecialchars($item['content_type'])) ?></span>
                </td>
                <td style="padding: 0.85rem 1rem;">
                  <span class="ts-badge <?= $item['status'] === 'published' ? 'ts-badge-green' : 'ts-badge-pink' ?>" style="font-size: 0.65rem;">
                    <?= strtoupper(htmlspecialchars($item['status'])) ?>
                  </span>
                </td>
                <td style="padding: 0.85rem 1rem; font-weight: 700; color: #38bdf8; font-family: var(--ts-font-mono);">
                  <?= number_format((int)$item['view_count']) ?>
                </td>
                <td style="padding: 0.85rem 1rem; color: #ec4899; font-weight: 600;">
                  <?= number_format((int)$item['reaction_count']) ?>
                </td>
                <td style="padding: 0.85rem 1rem; color: #fbbf24; font-weight: 600;">
                  <?= number_format((int)($item['bookmarks_count'] ?? 0)) ?>
                </td>
                <td style="padding: 0.85rem 1rem; color: #34d399; font-weight: 600;">
                  <?= number_format((int)($item['comments_count'] ?? 0)) ?>
                </td>
                <td style="padding: 0.85rem 1rem; text-align: right;">
                  <div style="display: inline-flex; gap: 0.4rem;">
                    <a href="<?= url('/author/analytics/content/' . $item['id']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.6rem; background: linear-gradient(135deg, #6366f1, #38bdf8);">
                      📊 Telemetry HUD ↗
                    </a>
                    <a href="<?= $targetUrl ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;" title="View Live Public">
                      🌐
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="text-align: center; padding: 3rem; color: var(--ts-text-tertiary);">
                No publications found. Create your first publication to begin tracking telemetry!
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
const dailyViewsData = <?= json_encode($dailyViews) ?>;

function filterEntities(type) {
  document.querySelectorAll('[id^="efilter_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const btn = document.getElementById(`efilter_${type}`);
  if (btn) {
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.entity-row').forEach(row => {
    if (type === 'all' || row.getAttribute('data-type') === type) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const canvas = document.getElementById('authorTelemetryChart');
  if (canvas && dailyViewsData) {
    const dates = Object.keys(dailyViewsData);
    const counts = Object.values(dailyViewsData);

    const ctx = canvas.getContext('2d');
    const width = canvas.offsetWidth || 700;
    const height = 240;
    canvas.width = width;
    canvas.height = height;

    const maxCount = Math.max(...counts, 10);
    const padding = { top: 20, right: 20, bottom: 30, left: 40 };
    const chartW = width - padding.left - padding.right;
    const chartH = height - padding.top - padding.bottom;

    // Draw Grid Lines
    ctx.strokeStyle = 'rgba(148, 163, 184, 0.08)';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 4; i++) {
      const y = padding.top + (chartH / 4) * i;
      ctx.beginPath();
      ctx.moveTo(padding.left, y);
      ctx.lineTo(width - padding.right, y);
      ctx.stroke();

      ctx.fillStyle = '#64748b';
      ctx.font = '10px monospace';
      ctx.fillText(Math.round(maxCount * (1 - i / 4)), 5, y + 3);
    }

    // Draw Line Chart
    const points = counts.map((c, i) => ({
      x: padding.left + (chartW / (counts.length - 1)) * i,
      y: padding.top + chartH - (c / maxCount) * chartH
    }));

    // Area Fill Gradient
    const gradient = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
    gradient.addColorStop(0, 'rgba(56, 189, 248, 0.35)');
    gradient.addColorStop(1, 'rgba(56, 189, 248, 0.0)');

    ctx.beginPath();
    ctx.moveTo(points[0].x, height - padding.bottom);
    points.forEach(p => ctx.lineTo(p.x, p.y));
    ctx.lineTo(points[points.length - 1].x, height - padding.bottom);
    ctx.closePath();
    ctx.fillStyle = gradient;
    ctx.fill();

    // Stroke Path
    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);
    points.forEach(p => ctx.lineTo(p.x, p.y));
    ctx.strokeStyle = '#38bdf8';
    ctx.lineWidth = 2.5;
    ctx.stroke();

    // Data Points
    points.forEach(p => {
      ctx.beginPath();
      ctx.arc(p.x, p.y, 3, 0, Math.PI * 2);
      ctx.fillStyle = '#38bdf8';
      ctx.fill();
    });
  }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
