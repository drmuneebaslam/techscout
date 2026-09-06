<?php
$title = $title ?? 'Entity Studio Analytics | TechScout';
require __DIR__ . '/../layouts/header.php';

$c = $analytics['content'];
$targetUrl = url('/article/' . $c['slug']);
if ($c['content_type'] === 'ai_tool') $targetUrl = url('/tools/' . $c['slug']);
elseif ($c['content_type'] === 'prompt') $targetUrl = url('/prompts/' . $c['slug']);
elseif ($c['content_type'] === 'gpt_listing') $targetUrl = url('/gpts/' . $c['slug']);
elseif ($c['content_type'] === 'campaign') $targetUrl = url('/campaigns/' . $c['slug']);

$totalWatchHours = round(($analytics['total_watch_seconds'] ?? 0) / 3600, 1);
$avgMin = floor(($analytics['avg_duration_seconds'] ?? 0) / 60);
$avgSec = ($analytics['avg_duration_seconds'] ?? 0) % 60;
$viewsDiff = (int)($analytics['views_diff_pct'] ?? 0);
?>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem; max-width: 1160px;">
  <!-- Breadcrumb & Actions Strip -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--ts-text-tertiary);">
      <a href="<?= url('/author/dashboard') ?>" style="color: #38bdf8; text-decoration: none;">Creator Studio</a>
      <span>&rsaquo;</span>
      <span style="color: var(--ts-text-primary); font-weight: 600;">Entity Telemetry Hub</span>
    </div>

    <div style="display: flex; gap: 0.6rem; align-items: center;">
      <a href="<?= $targetUrl ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        🌐 View Live Public ↗
      </a>
      <a href="<?= url('/author/editor/' . $c['id']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">
        ✏️ Edit Publication
      </a>
    </div>
  </div>

  <!-- YouTube Studio / TikTok Header Banner -->
  <div class="ts-card" style="padding: 1.75rem; margin-bottom: 1.75rem; background: linear-gradient(135deg, rgba(15,23,42,0.92), rgba(30,41,59,0.7)); border: 1px solid rgba(56,189,248,0.25); border-left: 4px solid #38bdf8; border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
      <div style="flex: 1; min-width: 280px;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; flex-wrap: wrap;">
          <span class="ts-badge ts-badge-cyan"><?= strtoupper($c['content_type']) ?></span>
          <span class="ts-badge <?= $c['status'] === 'published' ? 'ts-badge-green' : 'ts-badge-pink' ?>"><?= strtoupper($c['status']) ?></span>
          <?php if (!empty($c['category_name'])): ?>
            <span class="ts-badge ts-badge-indigo"><?= htmlspecialchars($c['category_name']) ?></span>
          <?php endif; ?>
          <?php if ($viewsDiff >= 0): ?>
            <span class="ts-badge ts-badge-green" style="font-size: 0.72rem; padding: 0.15rem 0.5rem;">🔥 Top Performer (+<?= $viewsDiff ?>% vs avg)</span>
          <?php endif; ?>
        </div>

        <h1 style="font-size: 1.65rem; color: #f8fafc; font-weight: 800; font-family: 'Outfit', sans-serif; margin: 0 0 0.5rem 0; line-height: 1.35;">
          <?= htmlspecialchars($c['title']) ?>
        </h1>

        <div style="font-size: 0.84rem; color: #94a3b8; display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
          <span>Published: <strong style="color: #f8fafc;"><?= \App\Core\DateHelper::formatDateTime($c['created_at']) ?></strong></span>
          <?php if (!empty($c['updated_at']) && strtotime($c['updated_at']) > strtotime($c['created_at']) + 60): ?>
            <span style="color: #38bdf8;">Updated: <strong><?= \App\Core\DateHelper::formatDateTime($c['updated_at']) ?></strong></span>
          <?php endif; ?>
          <span>Slug: <code style="color: #38bdf8; font-family: var(--ts-font-mono);"><?= htmlspecialchars($c['slug']) ?></code></span>
        </div>
      </div>
    </div>
  </div>

  <!-- YouTube Studio / TikTok Style 4-Metric Primary Overview Deck -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <!-- Metric 1: Views -->
    <div class="ts-card" style="padding: 1.35rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); border-top: 3px solid #38bdf8; border-radius: 12px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
        <span style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">👁️ Total Views</span>
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;"><?= $analytics['unique_visitors'] ?> unique</span>
      </div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #38bdf8; font-family: var(--ts-font-mono);">
        <?= number_format($analytics['total_views']) ?>
      </div>
      <div style="font-size: 0.75rem; color: <?= $viewsDiff >= 0 ? '#34d399' : '#f87171' ?>; margin-top: 0.25rem; font-weight: 600;">
        <?= $viewsDiff >= 0 ? "↑ {$viewsDiff}% higher than channel average" : "↓ " . abs($viewsDiff) . "% vs channel average" ?>
      </div>
    </div>

    <!-- Metric 2: Watch / Read Time -->
    <div class="ts-card" style="padding: 1.35rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); border-top: 3px solid #10b981; border-radius: 12px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
        <span style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">⏱️ Total Read Time</span>
        <span class="ts-badge ts-badge-green" style="font-size: 0.65rem;">Cumulative</span>
      </div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #10b981; font-family: var(--ts-font-mono);">
        <?= $totalWatchHours ?> <span style="font-size: 1rem; color: #94a3b8; font-weight: 600;">hrs</span>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
        Total community time invested
      </div>
    </div>

    <!-- Metric 3: Average View Duration & Retention -->
    <div class="ts-card" style="padding: 1.35rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); border-top: 3px solid #a855f7; border-radius: 12px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
        <span style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">📈 Avg View Duration</span>
        <span class="ts-badge ts-badge-purple" style="font-size: 0.65rem;"><?= $analytics['avg_scroll'] ?>% scroll</span>
      </div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #c084fc; font-family: var(--ts-font-mono);">
        <?= $avgMin ?>m <?= $avgSec ?>s
      </div>
      <div style="font-size: 0.75rem; color: #34d399; margin-top: 0.25rem; font-weight: 600;">
        <?= $analytics['completion_rate'] ?>% reader completion rate
      </div>
    </div>

    <!-- Metric 4: Engagement & Interactions -->
    <div class="ts-card" style="padding: 1.35rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); border-top: 3px solid #ec4899; border-radius: 12px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
        <span style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">⚡ Engagement Rate</span>
        <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;"><?= $analytics['total_bookmarks'] ?> saves</span>
      </div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #f472b6; font-family: var(--ts-font-mono);">
        <?= $analytics['engagement_rate'] ?>%
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
        <?= $analytics['total_reactions'] ?> reactions &bull; <?= $analytics['total_comments'] ?> comments
      </div>
    </div>
  </div>

  <!-- YouTube Studio Style Multi-Tab Navigation Strip -->
  <div style="display: flex; gap: 0.5rem; border-bottom: 1px solid var(--ts-border-main); margin-bottom: 1.75rem; flex-wrap: wrap;">
    <button type="button" onclick="switchAnalyticsTab('overview')" id="atab_overview" class="ts-btn ts-btn-sm ts-btn-primary" style="padding: 0.45rem 1.15rem; font-weight: 700;">
      📊 Overview &amp; Readership
    </button>
    <button type="button" onclick="switchAnalyticsTab('reach')" id="atab_reach" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.45rem 1.15rem; font-weight: 700; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
      🚀 Reach &amp; Traffic Discovery
    </button>
    <button type="button" onclick="switchAnalyticsTab('engagement')" id="atab_engagement" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.45rem 1.15rem; font-weight: 700; color: #f472b6; border-color: rgba(236,72,153,0.3);">
      🔥 Sentiment &amp; Reactions
    </button>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB 1: OVERVIEW & 30-DAY PERFORMANCE TREND -->
  <!-- ========================================================================= -->
  <div id="apane_overview" class="analytics-tab-pane">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
      <!-- Interactive Dual Metric Chart -->
      <div class="ts-card" style="padding: 1.5rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); border-radius: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
          <div>
            <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
              📈 Readership &amp; Watch Time Trend (30 Days)
            </h3>
            <p style="font-size: 0.78rem; color: #64748b; margin: 0.15rem 0 0 0;">Day-by-day active readership telemetry</p>
          </div>
          <div style="display: flex; gap: 0.35rem;">
            <button type="button" onclick="toggleChartMetric('views')" id="chart-btn-views" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">Views 👁️</button>
            <button type="button" onclick="toggleChartMetric('watch')" id="chart-btn-watch" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">Watch Mins ⏱️</button>
          </div>
        </div>
        <div style="height: 270px; position: relative;">
          <canvas id="authorStudioMainChart"></canvas>
        </div>
      </div>

      <!-- YouTube Style Key Moments for Audience Retention -->
      <div class="ts-card" style="padding: 1.5rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); border-radius: 14px; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.25rem 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
            🎯 Audience Retention Key Moments
          </h3>
          <p style="font-size: 0.78rem; color: #64748b; margin: 0 0 1.25rem 0;">Reader drop-off telemetry</p>

          <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            <div style="background: rgba(15,23,42,0.8); padding: 0.85rem; border-radius: 8px; border: 1px solid rgba(56,189,248,0.15);">
              <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 0.25rem;">
                <span style="color: #38bdf8; font-weight: 700;">Intro Retention (0–25%)</span>
                <strong style="color: #f8fafc;"><?= round((($analytics['scroll_funnel']['0_25'] ?? 0) / max(1, $analytics['reader_count'])) * 100) ?>%</strong>
              </div>
              <p style="font-size: 0.74rem; color: #94a3b8; margin: 0;">Skimmed title, executive summary &amp; diagram</p>
            </div>

            <div style="background: rgba(15,23,42,0.8); padding: 0.85rem; border-radius: 8px; border: 1px solid rgba(99,102,241,0.15);">
              <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 0.25rem;">
                <span style="color: #818cf8; font-weight: 700;">Deep Dive (51–75%)</span>
                <strong style="color: #f8fafc;"><?= round((($analytics['scroll_funnel']['51_75'] ?? 0) / max(1, $analytics['reader_count'])) * 100) ?>%</strong>
              </div>
              <p style="font-size: 0.74rem; color: #94a3b8; margin: 0;">Read architecture teardown &amp; benchmark code</p>
            </div>

            <div style="background: rgba(15,23,42,0.8); padding: 0.85rem; border-radius: 8px; border: 1px solid rgba(16,185,129,0.15);">
              <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 0.25rem;">
                <span style="color: #34d399; font-weight: 700;">Full Read Through (76–100%)</span>
                <strong style="color: #10b981;"><?= $analytics['completion_rate'] ?>%</strong>
              </div>
              <p style="font-size: 0.74rem; color: #94a3b8; margin: 0;">Completed entire publication and engaged</p>
            </div>
          </div>
        </div>

        <div style="font-size: 0.75rem; color: #64748b; margin-top: 1rem; border-top: 1px solid rgba(148,163,184,0.08); padding-top: 0.75rem;">
          💡 Tip: Technical articles with interactive code snippets achieve 42% higher retention.
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB 2: REACH & TRAFFIC DISCOVERY -->
  <!-- ========================================================================= -->
  <div id="apane_reach" class="analytics-tab-pane" style="display: none;">
    <div style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 1.5rem; margin-bottom: 2rem;">
      <!-- Referrer Domains -->
      <div class="ts-card" style="padding: 1.5rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); border-radius: 14px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
            🌐 External Inbound Traffic Sources
          </h3>
          <?php $totalRefEvents = array_sum(array_column($analytics['referrers'] ?? [], 'count')) ?: 1; ?>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem;">
            <?= number_format($totalRefEvents) ?> Total Inbound Events
          </span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
          <?php if (!empty($analytics['referrers'])): ?>
            <?php foreach ($analytics['referrers'] as $ref): ?>
              <?php 
                $refPct = round(((int)$ref['count'] / $totalRefEvents) * 100);
                $refCol = $ref['color'] ?? '#38bdf8';
                $refChan = $ref['channel'] ?? 'Referral';
                $refIcon = $ref['icon'] ?? 'globe';
              ?>
              <div style="background: rgba(30,41,59,0.5); border: 1px solid rgba(148,163,184,0.08); border-radius: 10px; padding: 0.75rem 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                  <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 28px; height: 28px; border-radius: 6px; background: <?= $refCol ?>22; color: <?= $refCol ?>; display: flex; align-items: center; justify-content: center;">
                      <i data-lucide="<?= $refIcon ?>" style="width: 15px; height: 15px;"></i>
                    </div>
                    <div>
                      <div style="font-family: var(--ts-font-mono); color: #f8fafc; font-weight: 700; font-size: 0.88rem;">
                        <?= htmlspecialchars($ref['domain']) ?>
                      </div>
                      <div style="font-size: 0.72rem; color: #94a3b8;">
                        <?= htmlspecialchars($refChan) ?>
                      </div>
                    </div>
                  </div>
                  <div style="text-align: right;">
                    <div style="font-family: var(--ts-font-mono); color: #f8fafc; font-weight: 800; font-size: 0.95rem;">
                      <?= number_format((int)$ref['count']) ?> <span style="font-size: 0.72rem; color: #64748b; font-weight: 500;">(<?= $refPct ?>%)</span>
                    </div>
                  </div>
                </div>
                <div style="height: 5px; background: rgba(148,163,184,0.1); border-radius: 999px; overflow: hidden;">
                  <div style="height: 100%; width: <?= $refPct ?>%; background: <?= $refCol ?>; border-radius: 999px;"></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div style="text-align: center; color: #64748b; padding: 2rem 0;">No external referrers logged yet.</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Device Share -->
      <div class="ts-card" style="padding: 1.5rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); border-radius: 14px; display: flex; flex-direction: column;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1rem 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
          📱 Device Form Factors Breakdown
        </h3>
        <div style="height: 190px; position: relative; margin-bottom: 1.25rem;">
          <canvas id="authorDeviceDonutChart"></canvas>
        </div>
        <?php
          $totDev = array_sum($analytics['devices']) ?: 1;
          $deskPct = round(($analytics['devices']['desktop'] / $totDev) * 100);
          $mobPct = round(($analytics['devices']['mobile'] / $totDev) * 100);
          $tabPct = max(0, 100 - ($deskPct + $mobPct));
        ?>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.6rem; margin-top: auto;">
          <div style="padding: 0.75rem 0.5rem; background: rgba(56,189,248,0.08); border: 1px solid rgba(56,189,248,0.2); border-radius: 8px; text-align: center;">
            <div style="color: #38bdf8; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Desktop</div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #f8fafc; font-family: var(--ts-font-mono); margin: 0.2rem 0;"><?= $deskPct ?>%</div>
            <div style="font-size: 0.7rem; color: #94a3b8;"><?= number_format($analytics['devices']['desktop']) ?> views</div>
          </div>
          <div style="padding: 0.75rem 0.5rem; background: rgba(168,85,247,0.08); border: 1px solid rgba(168,85,247,0.2); border-radius: 8px; text-align: center;">
            <div style="color: #c084fc; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Mobile</div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #f8fafc; font-family: var(--ts-font-mono); margin: 0.2rem 0;"><?= $mobPct ?>%</div>
            <div style="font-size: 0.7rem; color: #94a3b8;"><?= number_format($analytics['devices']['mobile']) ?> views</div>
          </div>
          <div style="padding: 0.75rem 0.5rem; background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); border-radius: 8px; text-align: center;">
            <div style="color: #34d399; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Tablet</div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #f8fafc; font-family: var(--ts-font-mono); margin: 0.2rem 0;"><?= $tabPct ?>%</div>
            <div style="font-size: 0.7rem; color: #94a3b8;"><?= number_format($analytics['devices']['tablet']) ?> views</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB 3: SENTIMENT & ENGAGEMENT -->
  <!-- ========================================================================= -->
  <div id="apane_engagement" class="analytics-tab-pane" style="display: none;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
      <!-- Emoji Sentiment Matrix -->
      <div class="ts-card" style="padding: 1.5rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); border-radius: 14px;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
          🔥 5-Reaction Sentiment Breakdown
        </h3>
        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
          <?php
            $rMap = $analytics['reactions_map'];
            $emojis = [
              'like' => ['label' => 'Fire 🔥', 'color' => '#ef4444', 'count' => $rMap['like'] ?? 0],
              'rocket' => ['label' => 'Rocket 🚀', 'color' => '#38bdf8', 'count' => $rMap['rocket'] ?? 0],
              'insightful' => ['label' => 'Insightful 💡', 'color' => '#f59e0b', 'count' => $rMap['insightful'] ?? 0],
              'mind_blown' => ['label' => 'Mind Blown 🤯', 'color' => '#a855f7', 'count' => $rMap['mind_blown'] ?? 0],
              'clap' => ['label' => 'Clap 👏', 'color' => '#10b981', 'count' => $rMap['clap'] ?? 0],
            ];
            $maxReaction = max(1, max(array_column($emojis, 'count')));
          ?>
          <?php foreach ($emojis as $rk => $rv): ?>
            <?php $pct = round(($rv['count'] / $maxReaction) * 100); ?>
            <div>
              <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 0.3rem; color: #cbd5e1;">
                <span><?= $rv['label'] ?></span>
                <strong style="color: <?= $rv['color'] ?>; font-family: var(--ts-font-mono);"><?= number_format($rv['count']) ?></strong>
              </div>
              <div style="width: 100%; height: 7px; background: rgba(148,163,184,0.1); border-radius: 999px; overflow: hidden;">
                <div style="width: <?= $pct ?>%; height: 100%; background: <?= $rv['color'] ?>; border-radius: 999px;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Interaction Velocity HUD -->
      <div class="ts-card" style="padding: 1.5rem; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); border-radius: 14px; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1rem 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
            💬 Discussion &amp; Library Saves
          </h3>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div style="background: rgba(15,23,42,0.8); padding: 1.25rem; border-radius: 10px; border: 1px solid rgba(148,163,184,0.12); text-align: center;">
              <div style="font-size: 1.65rem; font-weight: 800; color: #38bdf8; font-family: var(--ts-font-mono);"><?= number_format($analytics['total_comments']) ?></div>
              <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Discussion Threads</div>
            </div>
            <div style="background: rgba(15,23,42,0.8); padding: 1.25rem; border-radius: 10px; border: 1px solid rgba(148,163,184,0.12); text-align: center;">
              <div style="font-size: 1.65rem; font-weight: 800; color: #fbbf24; font-family: var(--ts-font-mono);"><?= number_format($analytics['total_bookmarks']) ?></div>
              <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Saved to Libraries</div>
            </div>
          </div>
        </div>

        <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px dashed rgba(148,163,184,0.15); font-size: 0.8rem; color: #94a3b8;">
          ⚡ Every reaction and discussion reply awards Creator Reputation XP toward partner royalty tiers.
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
let studioChartInstance = null;
const dailyViews = <?= json_encode($analytics['daily_views']) ?>;
const dailyWatch = <?= json_encode($analytics['daily_watch_time']) ?>;
const labels = Object.keys(dailyViews).map(d => d.substring(5));

function switchAnalyticsTab(tab) {
  document.querySelectorAll('.analytics-tab-pane').forEach(p => p.style.display = 'none');
  document.querySelectorAll('[id^="atab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });

  const pane = document.getElementById('apane_' + tab);
  const btn = document.getElementById('atab_' + tab);
  if (pane) pane.style.display = 'block';
  if (btn) {
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
  }
}

function toggleChartMetric(type) {
  const btnViews = document.getElementById('chart-btn-views');
  const btnWatch = document.getElementById('chart-btn-watch');
  if (!studioChartInstance) return;

  if (type === 'views') {
    btnViews.classList.remove('ts-btn-outline');
    btnViews.classList.add('ts-btn-primary');
    btnWatch.classList.remove('ts-btn-primary');
    btnWatch.classList.add('ts-btn-outline');

    studioChartInstance.data.datasets[0].label = 'Daily Reads';
    studioChartInstance.data.datasets[0].data = Object.values(dailyViews);
    studioChartInstance.data.datasets[0].borderColor = '#38bdf8';
    studioChartInstance.data.datasets[0].backgroundColor = 'rgba(56, 189, 248, 0.1)';
  } else {
    btnWatch.classList.remove('ts-btn-outline');
    btnWatch.classList.add('ts-btn-primary');
    btnViews.classList.remove('ts-btn-primary');
    btnViews.classList.add('ts-btn-outline');

    studioChartInstance.data.datasets[0].label = 'Read Time (Minutes)';
    studioChartInstance.data.datasets[0].data = Object.values(dailyWatch);
    studioChartInstance.data.datasets[0].borderColor = '#10b981';
    studioChartInstance.data.datasets[0].backgroundColor = 'rgba(16, 185, 129, 0.1)';
  }
  studioChartInstance.update();
}

document.addEventListener('DOMContentLoaded', () => {
  // 1. Line Chart
  const ctx = document.getElementById('authorStudioMainChart');
  if (ctx && typeof Chart !== 'undefined') {
    studioChartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Daily Reads',
          data: Object.values(dailyViews),
          borderColor: '#38bdf8',
          backgroundColor: 'rgba(56, 189, 248, 0.1)',
          borderWidth: 2.5,
          fill: true,
          tension: 0.35,
          pointRadius: 2,
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            grid: { color: 'rgba(148, 163, 184, 0.08)' },
            ticks: { color: '#64748b', font: { size: 10 } }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.08)' },
            ticks: { color: '#64748b', font: { size: 10 }, precision: 0 }
          }
        }
      }
    });
  }

  // 2. Device Donut Chart
  const donutCtx = document.getElementById('authorDeviceDonutChart');
  if (donutCtx && typeof Chart !== 'undefined') {
    const devices = <?= json_encode($analytics['devices']) ?>;
    new Chart(donutCtx, {
      type: 'doughnut',
      data: {
        labels: ['Desktop 💻', 'Mobile 📱', 'Tablet 📟'],
        datasets: [{
          data: [devices.desktop || 0, devices.mobile || 0, devices.tablet || 0],
          backgroundColor: ['#38bdf8', '#a855f7', '#10b981'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: { color: '#cbd5e1', font: { size: 11 }, padding: 15 }
          }
        },
        cutout: '70%'
      }
    });
  }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
