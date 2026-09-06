<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

  <!-- Topbar Header -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-indigo">COMMUNICATIONS &bull; EMAIL BROADCAST ENGINE</span>
        <span style="font-size: 0.8rem; color: #64748b;">Automated Digests, Subscriber Rosters, HTML Issue Composer &amp; Dispatch Analytics</span>
      </div>
      <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        CMS Newsletter Management
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/subscribers/export') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.3);">
        <i data-lucide="download"></i> Export Subscribers CSV
      </a>
      <button type="button" onclick="openNewIssueModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
        <i data-lucide="send"></i> Compose Issue ⚡
      </button>
    </div>
  </div>

  <!-- Telemetry Overview Bar -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Audience</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($stats['total']) ?>
      </div>
      <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.2rem;">Verified opt-in readers</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Active Subscribers</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($stats['active']) ?>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Receiving weekly broadcasts</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Average Open Rate</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= $stats['avg_open_rate'] ?>
      </div>
      <div style="font-size: 0.75rem; color: #f472b6; margin-top: 0.2rem;">+8.2% vs industry standard</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Average CTR</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #a855f7; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= $stats['avg_ctr'] ?>
      </div>
      <div style="font-size: 0.75rem; color: #c084fc; margin-top: 0.2rem;">Direct links to articles & tools</div>
    </div>
  </div>

  <!-- Newsletter Tabs Strip (6 Modules) -->
  <div class="admin-table-wrap" style="padding: 0.5rem; margin-bottom: 1.5rem; overflow-x: auto;">
    <div style="display: flex; gap: 0.35rem; min-width: max-content;">
      <?php
      $nTabs = [
        'newsletters' => ['label' => 'Newsletters', 'icon' => 'mail'],
        'subscribers' => ['label' => 'Subscribers', 'icon' => 'users'],
        'templates' => ['label' => 'Templates', 'icon' => 'layout-template'],
        'issues' => ['label' => 'Issues', 'icon' => 'file-text'],
        'scheduling' => ['label' => 'Scheduling', 'icon' => 'calendar-clock'],
        'analytics' => ['label' => 'Analytics', 'icon' => 'bar-chart-2'],
      ];
      ?>

      <?php foreach ($nTabs as $k => $t): ?>
        <button type="button" onclick="switchNewsletterTab('<?= $k ?>')" id="ntab_<?= $k ?>" class="ts-btn ts-btn-sm <?= $activeTab === $k ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="<?= $t['icon'] ?>" style="width: 14px; height: 14px;"></i>
          <span><?= $t['label'] ?></span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 1. NEWSLETTERS TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_newsletters" style="<?= $activeTab === 'newsletters' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap">
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle);">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0;">📬 Active Broadcast Campaigns</h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Recent newsletter broadcasts delivered to subscribers across all segments.</p>
        </div>
        <button type="button" onclick="openNewIssueModal()" class="ts-btn ts-btn-primary ts-btn-sm">Create Issue +</button>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Issue Title & Subject</th>
            <th>Type</th>
            <th>Recipients</th>
            <th>Opens</th>
            <th>Clicks</th>
            <th>Status</th>
            <th>Dispatched</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($issues)): ?>
            <?php foreach ($issues as $iss): ?>
              <tr>
                <td>
                  <div style="font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($iss['title']) ?></div>
                  <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem;"><?= htmlspecialchars($iss['subject_line']) ?></div>
                </td>
                <td><span class="ts-badge ts-badge-indigo"><?= strtoupper(htmlspecialchars($iss['template_type'])) ?></span></td>
                <td style="font-family: var(--admin-font-mono); color: #cbd5e1;"><?= number_format((int)$iss['total_recipients']) ?></td>
                <td style="font-family: var(--admin-font-mono); color: #38bdf8;"><?= number_format((int)$iss['open_count']) ?></td>
                <td style="font-family: var(--admin-font-mono); color: #10b981;"><?= number_format((int)$iss['click_count']) ?></td>
                <td>
                  <span class="ts-badge <?= $iss['status'] === 'sent' ? 'ts-badge-green' : ($iss['status'] === 'scheduled' ? 'ts-badge-cyan' : 'ts-badge-pink') ?>">
                    <?= strtoupper(htmlspecialchars($iss['status'])) ?>
                  </span>
                </td>
                <td style="font-size: 0.78rem; color: #64748b;"><?= !empty($iss['sent_at']) ? date('M j, Y H:i', strtotime($iss['sent_at'])) : ($iss['scheduled_at'] ? 'Sched: ' . date('M j, H:i', strtotime($iss['scheduled_at'])) : 'Draft') ?></td>
                <td>
                  <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                    <button type="button" onclick="startLiveDispatch(<?= (int)$iss['id'] ?>, '<?= htmlspecialchars(addslashes($iss['title'])) ?>')" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; background: #10b981; border-color: #10b981; font-weight: 700;" title="Dispatch live to subscribers">
                      🚀 Send Live
                    </button>
                    <button type="button" onclick='editIssue(<?= json_encode($iss) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #38bdf8;">
                      ✏️ Edit
                    </button>
                    <form action="<?= url('/admin/newsletters/delete/' . $iss['id']) ?>" method="POST" onsubmit="return confirm('Delete this issue record?');" style="display: inline;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #ef4444;">
                        🗑
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8" style="text-align: center; padding: 2.5rem; color: #64748b;">No newsletter issues composed yet. Click "Compose Issue" to start.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 2. SUBSCRIBERS TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_subscribers" style="<?= $activeTab === 'subscribers' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap">
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0;">👥 Verified Subscriber Registry</h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Manage opt-in subscribers, delivery preferences, and unsubscribe requests.</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
          <button type="button" onclick="openAddSubscriberModal()" class="ts-btn ts-btn-primary ts-btn-sm">Add Subscriber +</button>
        </div>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Subscriber Email</th>
            <th>Subscription Segment</th>
            <th>Status</th>
            <th>Joined Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($subscribers)): ?>
            <?php foreach ($subscribers as $sub): ?>
              <tr>
                <td style="font-weight: 600; color: #f8fafc; font-family: var(--admin-font-mono);"><?= htmlspecialchars($sub['email']) ?></td>
                <td><span class="ts-badge ts-badge-cyan"><?= strtoupper(htmlspecialchars($sub['subscriber_type'])) ?></span></td>
                <td>
                  <span class="ts-badge <?= $sub['status'] === 'subscribed' ? 'ts-badge-green' : 'ts-badge-pink' ?>">
                    <?= strtoupper(htmlspecialchars($sub['status'])) ?>
                  </span>
                </td>
                <td style="font-size: 0.8rem; color: #94a3b8;"><?= date('M j, Y H:i', strtotime($sub['created_at'])) ?></td>
                <td>
                  <form action="<?= url('/admin/subscribers/delete/' . $sub['id']) ?>" method="POST" onsubmit="return confirm('Remove this subscriber?');" style="display: inline;">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #ef4444;">
                      Remove ✕
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align: center; padding: 2.5rem; color: #64748b;">No subscribers found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 3. TEMPLATES TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_templates" style="<?= $activeTab === 'templates' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
      <div class="admin-table-wrap" style="padding: 1.5rem; display: flex; flex-direction: column;">
        <span class="ts-badge ts-badge-cyan" style="align-self: flex-start; margin-bottom: 0.75rem;">WEEKLY DIGEST</span>
        <h4 style="color: #f8fafc; font-size: 1.1rem; margin: 0 0 0.4rem 0;">Engineering Radar &amp; Architecture Digest</h4>
        <p style="color: #94a3b8; font-size: 0.85rem; flex: 1; margin-bottom: 1rem;">Curated multi-story layout featuring deep dive articles, top trending AI tools, and prompt blueprints.</p>
        <button type="button" onclick="useTemplatePreset('weekly_digest')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Use Template →</button>
      </div>

      <div class="admin-table-wrap" style="padding: 1.5rem; display: flex; flex-direction: column;">
        <span class="ts-badge ts-badge-pink" style="align-self: flex-start; margin-bottom: 0.75rem;">BREAKING NEWS</span>
        <h4 style="color: #f8fafc; font-size: 1.1rem; margin: 0 0 0.4rem 0;">Frontier AI &amp; Model Release Alert</h4>
        <p style="color: #94a3b8; font-size: 0.85rem; flex: 1; margin-bottom: 1rem;">High-impact single story alert format designed for breaking releases and zero-day vulnerabilities.</p>
        <button type="button" onclick="useTemplatePreset('breaking_alert')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Use Template →</button>
      </div>

      <div class="admin-table-wrap" style="padding: 1.5rem; display: flex; flex-direction: column;">
        <span class="ts-badge ts-badge-green" style="align-self: flex-start; margin-bottom: 0.75rem;">PRODUCT SHOWCASE</span>
        <h4 style="color: #f8fafc; font-size: 1.1rem; margin: 0 0 0.4rem 0;">Verified Tool Spotlight &amp; Teardown</h4>
        <p style="color: #94a3b8; font-size: 0.85rem; flex: 1; margin-bottom: 1rem;">Feature comparison matrix, pricing breakdown, and verified builder teardown verdict.</p>
        <button type="button" onclick="useTemplatePreset('tool_spotlight')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Use Template →</button>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 4. ISSUES & COMPOSER TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_issues" style="<?= $activeTab === 'issues' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
        <div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0;" id="composerFormTitle">📝 Compose &amp; Schedule Newsletter Issue</h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Draft your responsive email issue, insert published content cards, and schedule the broadcast.</p>
        </div>
      </div>

      <form id="newsletterIssueForm" action="<?= url('/admin/newsletters/save') ?>" method="POST">
        <input type="hidden" name="id" id="issue_id" value="">

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
          <div>
            <label class="admin-modal-label">Issue Title (Internal Reference)</label>
            <input type="text" name="title" id="issue_title" placeholder="e.g. TechScout Dispatch #48 - PostgreSQL Index Scaling" class="admin-modal-input" required>
          </div>
          <div>
            <label class="admin-modal-label">Issue Number</label>
            <input type="number" name="issue_number" id="issue_number" value="48" class="admin-modal-input" required>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
          <div>
            <label class="admin-modal-label">Email Subject Line (Inbox Headline)</label>
            <input type="text" name="subject_line" id="issue_subject" placeholder="⚡ Deep Dive: Zero-Downtime Database Migrations" class="admin-modal-input" required>
          </div>
          <div>
            <label class="admin-modal-label">Preview Preheader Snippet</label>
            <input type="text" name="preview_text" id="issue_preview" placeholder="Inside: Sharding strategies, Claude 3.5 XML prompts..." class="admin-modal-input">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
          <div>
            <label class="admin-modal-label">Template Preset</label>
            <select name="template_type" id="issue_template" class="admin-modal-select">
              <option value="weekly_digest">Weekly Engineering Digest</option>
              <option value="breaking_alert">Breaking Model Alert</option>
              <option value="tool_spotlight">Verified Tool Spotlight</option>
            </select>
          </div>
          <div>
            <label class="admin-modal-label">Status</label>
            <select name="status" id="issue_status" class="admin-modal-select">
              <option value="draft">Draft (Private)</option>
              <option value="scheduled">Scheduled for Queue</option>
              <option value="sent">Sent / Published</option>
            </select>
          </div>
          <div>
            <label class="admin-modal-label">Schedule Send Date &amp; Time</label>
            <input type="datetime-local" name="scheduled_at" id="issue_scheduled_at" class="admin-modal-input">
          </div>
        </div>

        <!-- Quick Article Block Inserter -->
        <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle); margin-bottom: 1.25rem;">
          <label class="admin-modal-label" style="margin-bottom: 0.4rem;">💡 Quick Inserter: Embed Published Article into Issue</label>
          <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
            <select id="articleQuickPicker" class="admin-modal-select" style="max-width: 450px;">
              <?php foreach ($recentArticles as $ra): ?>
                <option value="<?= htmlspecialchars($ra['title']) ?>" data-slug="<?= htmlspecialchars($ra['slug']) ?>" data-summary="<?= htmlspecialchars($ra['summary']) ?>">
                  <?= htmlspecialchars($ra['title']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="button" onclick="insertArticleIntoNewsletter()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
              + Insert Card Block
            </button>
          </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
          <label class="admin-modal-label">Newsletter Body (HTML &amp; Design Layout)</label>
          <textarea name="body_html" id="issue_body_html" rows="12" class="admin-modal-textarea" style="font-family: var(--admin-font-mono); font-size: 0.88rem; line-height: 1.5;"><h2>⚡ This Week's Engineering Highlights</h2>
<p>Welcome to this week's issue of the TechScout Dispatch. Here are our top architectural blueprints and tool reviews.</p>

<div style="border: 1px solid #334155; padding: 16px; border-radius: 8px; margin: 16px 0; background: #0f172a;">
  <h3 style="margin-top: 0; color: #38bdf8;">1. PostgreSQL High-Throughput Index Tuning</h3>
  <p style="color: #cbd5e1;">A deep dive into partial indexing, BRIN vs B-Tree tradeoffs, and query execution plan optimization under heavy write load.</p>
  <a href="https://techscout.online/article/postgresql-indexing" style="color: #ec4899; font-weight: bold; text-decoration: none;">Read Deep Dive &rarr;</a>
</div></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
          <div style="display: flex; gap: 0.5rem;">
            <input type="email" id="testRecipientEmail" placeholder="admin@domain.com" class="admin-modal-input" style="width: 240px; font-size: 0.82rem;">
            <button type="button" onclick="sendTestNewsletter()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
              ⚡ Send Test Email
            </button>
          </div>

          <div style="display: flex; gap: 0.6rem;">
            <button type="reset" class="ts-btn ts-btn-outline ts-btn-sm">Reset</button>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
              Save Issue &amp; Schedule 🚀
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 5. SCHEDULING TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_scheduling" style="<?= $activeTab === 'scheduling' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1rem 0;">⏰ Scheduled Broadcast Queue</h3>
      <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;">Issues queued for automatic delivery based on scheduled timestamps.</p>
      
      <table class="admin-table">
        <thead>
          <tr>
            <th>Issue Title</th>
            <th>Target Audience</th>
            <th>Scheduled Time (UTC)</th>
            <th>Queue State</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $schedIssues = array_filter($issues, fn($i) => $i['status'] === 'scheduled');
          if (!empty($schedIssues)):
            foreach ($schedIssues as $si):
          ?>
            <tr>
              <td style="font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($si['title']) ?></td>
              <td><span class="ts-badge ts-badge-cyan">All Active Subscribers (<?= number_format($stats['active']) ?>)</span></td>
              <td style="font-family: var(--admin-font-mono); color: #38bdf8;"><?= date('M j, Y H:i:s', strtotime($si['scheduled_at'])) ?></td>
              <td><span class="ts-badge ts-badge-green">QUEUED</span></td>
              <td>
                <button type="button" onclick='editIssue(<?= json_encode($si) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">Edit Schedule</button>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">No broadcasts currently scheduled in queue.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 6. ANALYTICS TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_analytics" style="<?= $activeTab === 'analytics' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Average Delivery Rate</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: var(--admin-font-mono);">99.4%</div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Average Unique Opens</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: var(--admin-font-mono);">48.9%</div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Click-to-Open (CTOR)</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: var(--admin-font-mono);">34.2%</div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Unsubscribe Rate</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem; font-family: var(--admin-font-mono);">0.12%</div>
      </div>
    </div>
  </div>

</div>

<!-- Modal: Add Subscriber Manual -->
<div id="addSubscriberModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 480px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="user-plus"></i> Add Newsletter Subscriber</h3>
      <button type="button" onclick="closeAddSubscriberModal()" class="admin-modal-close">✕</button>
    </div>
    <form action="<?= url('/admin/subscribers/save') ?>" method="POST">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Subscriber Email Address</label>
        <input type="email" name="email" placeholder="subscriber@example.com" class="admin-modal-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Subscription Segment</label>
        <select name="subscriber_type" class="admin-modal-select">
          <option value="all">All Broadcasts &amp; Digests</option>
          <option value="weekly_digest">Weekly Digest Only</option>
          <option value="ai_tools_only">AI Tools Radar Only</option>
        </select>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeAddSubscriberModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; border-color: #38bdf8; color: #000; font-weight: 700;">Add Subscriber ✓</button>
      </div>
    </form>
  </div>
</div>

<script>
function switchNewsletterTab(tabKey) {
  document.querySelectorAll('[id^="ntab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('ntab_' + tabKey);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.news-pane').forEach(p => p.style.display = 'none');
  const targetPane = document.getElementById('npane_' + tabKey);
  if (targetPane) targetPane.style.display = 'block';

  const url = new URL(window.location);
  url.searchParams.set('tab', tabKey);
  window.history.replaceState({}, '', url);

  if (window.lucide) lucide.createIcons();
}

function openNewIssueModal() {
  document.getElementById('newsletterIssueForm').reset();
  document.getElementById('issue_id').value = '';
  document.getElementById('composerFormTitle').innerText = '📝 Compose & Schedule Newsletter Issue';
  switchNewsletterTab('issues');
}

function editIssue(iss) {
  document.getElementById('issue_id').value = iss.id;
  document.getElementById('issue_title').value = iss.title || '';
  document.getElementById('issue_number').value = iss.issue_number || 1;
  document.getElementById('issue_subject').value = iss.subject_line || '';
  document.getElementById('issue_preview').value = iss.preview_text || '';
  document.getElementById('issue_template').value = iss.template_type || 'weekly_digest';
  document.getElementById('issue_status').value = iss.status || 'draft';
  document.getElementById('issue_body_html').value = iss.body_html || '';
  if (iss.scheduled_at) {
    document.getElementById('issue_scheduled_at').value = iss.scheduled_at.replace(' ', 'T').substring(0, 16);
  }
  document.getElementById('composerFormTitle').innerText = '✏️ Edit Newsletter Issue: ' + iss.title;
  switchNewsletterTab('issues');
}

function insertArticleIntoNewsletter() {
  const picker = document.getElementById('articleQuickPicker');
  const opt = picker.options[picker.selectedIndex];
  if (!opt) return;

  const title = opt.value;
  const slug = opt.getAttribute('data-slug');
  const summary = opt.getAttribute('data-summary');

  const cardHtml = `\n<div style="border: 1px solid #334155; padding: 16px; border-radius: 8px; margin: 16px 0; background: #0f172a;">
  <h3 style="margin-top: 0; color: #38bdf8;">${title}</h3>
  <p style="color: #cbd5e1;">${summary}</p>
  <a href="https://techscout.online/article/${slug}" style="color: #ec4899; font-weight: bold; text-decoration: none;">Read Full Publication &rarr;</a>
</div>\n`;

  const textarea = document.getElementById('issue_body_html');
  textarea.value += cardHtml;
  alert('Article card inserted into newsletter body!');
}

function sendTestNewsletter() {
  const email = document.getElementById('testRecipientEmail').value;
  const subject = document.getElementById('issue_subject').value || 'TechScout Newsletter Test';
  const body = document.getElementById('issue_body_html').value;

  if (!email) {
    alert('Please enter a test recipient email.');
    return;
  }

  fetch('<?= url('/admin/newsletters/dispatch-test') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ test_email: email, subject: subject, body_html: body })
  })
  .then(r => r.json())
  .then(d => {
    alert(d.message);
  });
}

function openAddSubscriberModal() {
  document.getElementById('addSubscriberModal').style.display = 'flex';
}
function closeAddSubscriberModal() {
  document.getElementById('addSubscriberModal').style.display = 'none';
}

// ============================================================================
// LIVE BATCH DISPATCH ENGINE
// ============================================================================
let isDispatchPaused = false;
let currentDispatchId = null;

function startLiveDispatch(issueId, issueTitle) {
  currentDispatchId = issueId;
  isDispatchPaused = false;
  
  document.getElementById('dispatchModalTitle').innerText = '🚀 Live Dispatch: ' + issueTitle;
  document.getElementById('dispatchModal').style.display = 'flex';
  document.getElementById('dispatchStreamLog').innerHTML = '<div style="color:#64748b;">Initializing live batch queue...</div>';
  document.getElementById('dispatchProgressFill').style.width = '0%';
  document.getElementById('dispatchProgressText').innerText = '0%';
  document.getElementById('dispatchBtnPause').innerText = '⏸ Pause Queue';
  document.getElementById('dispatchBtnPause').style.display = 'inline-block';
  
  // Query status first
  fetch('<?= url('/admin/newsletters/dispatch-status/') ?>' + issueId)
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        document.getElementById('dsp_total').innerText = d.total;
        document.getElementById('dsp_sent').innerText = d.sent;
        document.getElementById('dsp_rem').innerText = d.remaining;
        document.getElementById('dsp_fail').innerText = d.failed;
        
        if (d.total === 0) {
          logDispatch('⚠️ No active subscribers found in mailing list.');
          return;
        }
        
        logDispatch(`Found ${d.total} active subscriber recipients. Starting batch transmission (20/batch)...`);
        processNextDispatchBatch(issueId, 0, 20);
      }
    })
    .catch(err => {
      logDispatch('❌ Failed to fetch queue status: ' + err.message);
    });
}

function processNextDispatchBatch(issueId, offset, batchSize) {
  if (isDispatchPaused) {
    logDispatch('⏸ Queue transmission paused by operator.');
    return;
  }
  
  fetch('<?= url('/admin/newsletters/dispatch-batch/') ?>' + issueId, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ offset: offset, batch_size: batchSize })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) {
      logDispatch('❌ Batch error: ' + (data.error || 'Unknown dispatch failure'));
      return;
    }
    
    // Update HUD counters
    document.getElementById('dsp_total').innerText = data.total;
    document.getElementById('dsp_sent').innerText = data.sent_total;
    document.getElementById('dsp_rem').innerText = data.remaining;
    document.getElementById('dsp_fail').innerText = (parseInt(document.getElementById('dsp_fail').innerText) || 0) + (data.batch_failed || 0);
    
    document.getElementById('dispatchProgressFill').style.width = data.percent + '%';
    document.getElementById('dispatchProgressText').innerText = data.percent + '%';
    
    // Stream recipient logs
    if (data.dispatched_in_batch && data.dispatched_in_batch.length > 0) {
      data.dispatched_in_batch.forEach(item => {
        const icon = item.status === 'sent' ? '✓' : '✗';
        const color = item.status === 'sent' ? '#34d399' : '#ef4444';
        logDispatch(`<span style="color:${color};">[${new Date().toLocaleTimeString()}] ${icon} Delivered to ${item.email}</span>`);
      });
    }
    
    if (data.is_complete) {
      logDispatch(`<strong style="color: #10b981;">🎉 BROADCAST DISPATCH COMPLETED! ${data.sent_total} delivered, ${data.batch_failed} failed.</strong>`);
      document.getElementById('dispatchBtnPause').style.display = 'none';
      if (typeof showToast === 'function') {
        showToast('🎉 Newsletter broadcast successfully dispatched!', 'success');
      }
    } else {
      setTimeout(() => {
        processNextDispatchBatch(issueId, data.next_offset, batchSize);
      }, 400); // 400ms interval between batches to respect rate limits
    }
  })
  .catch(err => {
    logDispatch('❌ Network exception: ' + err.message);
  });
}

function togglePauseDispatch() {
  isDispatchPaused = !isDispatchPaused;
  const btn = document.getElementById('dispatchBtnPause');
  if (isDispatchPaused) {
    btn.innerText = '▶ Resume Queue';
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
    logDispatch('⏸ Dispatch paused.');
  } else {
    btn.innerText = '⏸ Pause Queue';
    btn.classList.remove('ts-btn-primary');
    btn.classList.add('ts-btn-outline');
    logDispatch('▶ Resuming dispatch queue...');
    const curSent = parseInt(document.getElementById('dsp_sent').innerText) || 0;
    processNextDispatchBatch(currentDispatchId, curSent, 20);
  }
}

function logDispatch(msg) {
  const stream = document.getElementById('dispatchStreamLog');
  const row = document.createElement('div');
  row.style.cssText = 'padding: 2px 0; border-bottom: 1px dashed rgba(148,163,184,0.06);';
  row.innerHTML = msg;
  stream.appendChild(row);
  stream.scrollTop = stream.scrollHeight;
}

function closeDispatchModal() {
  isDispatchPaused = true;
  document.getElementById('dispatchModal').style.display = 'none';
  location.reload();
}
</script>

<!-- ========================================================================= -->
<!-- MODAL: LIVE BATCH DISPATCH ENGINE -->
<!-- ========================================================================= -->
<div id="dispatchModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-emerald" style="max-width: 680px; max-height: 90vh; display: flex; flex-direction: column;">
    <div class="admin-modal-header" style="flex: 0 0 auto;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.2rem;">
          <span class="live-pulse"></span>
          <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">LIVE TRANSMISSION ENGINE</span>
        </div>
        <h3 class="admin-modal-title" id="dispatchModalTitle" style="color: #34d399; font-size: 1.15rem;">🚀 Live Broadcast Dispatch</h3>
      </div>
      <button type="button" onclick="closeDispatchModal()" class="admin-modal-close">✕</button>
    </div>

    <div style="padding: 1.5rem; overflow-y: auto; flex: 1 1 auto;">
      <!-- Stats 4-Grid -->
      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.25rem;">
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Total Target</div>
          <div id="dsp_total" style="font-size: 1.35rem; font-weight: 800; color: #f8fafc; font-family: var(--admin-font-mono);">0</div>
        </div>
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(16,185,129,0.3); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #34d399; text-transform: uppercase; font-weight: 700;">Sent</div>
          <div id="dsp_sent" style="font-size: 1.35rem; font-weight: 800; color: #10b981; font-family: var(--admin-font-mono);">0</div>
        </div>
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(56,189,248,0.3); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #38bdf8; text-transform: uppercase; font-weight: 700;">Remaining</div>
          <div id="dsp_rem" style="font-size: 1.35rem; font-weight: 800; color: #38bdf8; font-family: var(--admin-font-mono);">0</div>
        </div>
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(239,68,68,0.25); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #f87171; text-transform: uppercase; font-weight: 700;">Failed</div>
          <div id="dsp_fail" style="font-size: 1.35rem; font-weight: 800; color: #ef4444; font-family: var(--admin-font-mono);">0</div>
        </div>
      </div>

      <!-- Animated Progress Bar -->
      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.35rem; color: #cbd5e1;">
          <span>Transmission Progress</span>
          <strong id="dispatchProgressText" style="color: #34d399; font-family: var(--admin-font-mono);">0%</strong>
        </div>
        <div style="width: 100%; height: 10px; background: rgba(148,163,184,0.15); border-radius: 9999px; overflow: hidden;">
          <div id="dispatchProgressFill" style="width: 0%; height: 100%; background: linear-gradient(90deg, #10b981, #38bdf8); border-radius: 9999px; transition: width 0.3s ease;"></div>
        </div>
      </div>

      <!-- Live Stream Output Terminal -->
      <div>
        <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
          <span>Live Dispatch Stream Log</span>
          <span style="font-size: 0.7rem; color: #64748b;">Real-time SMTP / API Relay</span>
        </label>
        <div id="dispatchStreamLog" style="background: #020617; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.85rem; height: 180px; overflow-y: auto; font-family: var(--admin-font-mono); font-size: 0.75rem; color: #94a3b8; line-height: 1.45;">
          <div style="color: #64748b;">Awaiting dispatch ignition...</div>
        </div>
      </div>
    </div>

    <div class="admin-modal-footer" style="flex: 0 0 auto; display: flex; justify-content: space-between;">
      <button type="button" onclick="togglePauseDispatch()" id="dispatchBtnPause" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f59e0b; border-color: rgba(245,158,11,0.3);">
        ⏸ Pause Queue
      </button>
      <button type="button" onclick="closeDispatchModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #334155; border-color: #334155; font-weight: 700;">
        Close &amp; Finish
      </button>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
