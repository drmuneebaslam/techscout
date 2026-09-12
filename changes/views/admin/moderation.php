<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink">TRUST & SAFETY HUD</span>
        <span style="font-size: 0.8rem; color: #64748b;">Community Reports, Spam Defense & Content Moderation</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Moderation Station & Entity Reports
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/content') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="layers"></i> Review Queue
      </a>
      <a href="<?= url('/admin/users') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="users"></i> Users &amp; Strikes
      </a>
    </div>
  </div>

  <!-- Stat Grid -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Reports Filed</div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= number_format($counts['all'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Across all platform entities</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Pending Action</div>
      <div class="admin-stat-val" style="color: #ef4444;"><?= number_format($counts['pending'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #f87171;">Awaiting staff review</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Actioned &amp; Resolved</div>
      <div class="admin-stat-val" style="color: #10b981;"><?= number_format($counts['actioned'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Enforced / Settled</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Dismissed False Reports</div>
      <div class="admin-stat-val" style="color: #64748b;"><?= number_format($counts['dismissed'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Marked invalid</div>
    </div>
  </div>

  <!-- Filter Navigation -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/moderation') ?>" class="ts-btn ts-btn-sm <?= empty($currentStatus) || $currentStatus === 'all' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        All (<?= $counts['all'] ?? 0 ?>)
      </a>
      <a href="<?= url('/admin/moderation?status=pending' . (!empty($currentType) && $currentType !== 'all' ? '&type=' . $currentType : '')) ?>" class="ts-btn ts-btn-sm <?= ($currentStatus ?? '') === 'pending' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-color: rgba(239,68,68,0.4); color: <?= ($currentStatus ?? '') === 'pending' ? '#fff' : '#f87171' ?>;">
        ⏳ Pending (<?= $counts['pending'] ?? 0 ?>)
      </a>
      <a href="<?= url('/admin/moderation?status=actioned' . (!empty($currentType) && $currentType !== 'all' ? '&type=' . $currentType : '')) ?>" class="ts-btn ts-btn-sm <?= ($currentStatus ?? '') === 'actioned' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-color: rgba(16,185,129,0.4); color: <?= ($currentStatus ?? '') === 'actioned' ? '#fff' : '#34d399' ?>;">
        ✓ Actioned (<?= $counts['actioned'] ?? 0 ?>)
      </a>
      <a href="<?= url('/admin/moderation?status=dismissed' . (!empty($currentType) && $currentType !== 'all' ? '&type=' . $currentType : '')) ?>" class="ts-btn ts-btn-sm <?= ($currentStatus ?? '') === 'dismissed' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        Dismissed (<?= $counts['dismissed'] ?? 0 ?>)
      </a>
    </div>

    <!-- Entity Type Selector -->
    <div style="display: flex; gap: 0.5rem; align-items: center;">
      <span style="font-size: 0.8rem; color: #64748b;">Entity Filter:</span>
      <select onchange="location.href='<?= url('/admin/moderation?status=' . ($currentStatus ?? 'all')) ?>&type=' + this.value" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.35rem 0.75rem; border-radius: 6px; color: #f8fafc; font-size: 0.82rem;">
        <option value="all" <?= empty($currentType) || $currentType === 'all' ? 'selected' : '' ?>>All Entities</option>
        <option value="article" <?= ($currentType ?? '') === 'article' ? 'selected' : '' ?>>Articles</option>
        <option value="ai_tool" <?= ($currentType ?? '') === 'ai_tool' ? 'selected' : '' ?>>AI Tools</option>
        <option value="gpt_listing" <?= ($currentType ?? '') === 'gpt_listing' ? 'selected' : '' ?>>Custom GPTs</option>
        <option value="prompt" <?= ($currentType ?? '') === 'prompt' ? 'selected' : '' ?>>Prompts</option>
        <option value="job" <?= ($currentType ?? '') === 'job' ? 'selected' : '' ?>>Jobs</option>
        <option value="comment" <?= ($currentType ?? '') === 'comment' ? 'selected' : '' ?>>Comments</option>
        <option value="campaign" <?= ($currentType ?? '') === 'campaign' ? 'selected' : '' ?>>Campaigns</option>
      </select>
    </div>
  </div>

  <!-- Reports Table -->
  <div class="admin-table-wrap">
    <table class="admin-table" data-paginate="true" data-page-size="15">
      <thead>
        <tr>
          <th>Target Entity &amp; Details</th>
          <th>Report Reason</th>
          <th>Context &amp; User Notes</th>
          <th>Reporter</th>
          <th>Resolution Status</th>
          <th>Staff Action &amp; Notes</th>
          <th>Administrative Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($reports)): ?>
          <?php foreach ($reports as $r): ?>
            <tr>
              <td>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; text-transform: uppercase;">
                  <?= htmlspecialchars(str_replace('_', ' ', $r['entity_type'])) ?> #<?= (int)$r['entity_id'] ?>
                </span>
                <div style="font-weight: 700; color: #f8fafc; font-size: 0.95rem; margin-top: 0.25rem;">
                  <?= htmlspecialchars($r['entity_title'] ?: ('Entity #' . $r['entity_id'])) ?>
                </div>
                <?php if (!empty($r['entity_url'])): ?>
                  <?php 
                    $cleanEntUrl = $r['entity_url'];
                    // Strip hardcoded domain/prefix if present
                    $cleanEntUrl = preg_replace('#^https?://[^/]+(/techscout\.n)?#i', '', $cleanEntUrl);
                    // Correct plural /articles/ to canonical /article/
                    $cleanEntUrl = preg_replace('#^/articles/#', '/article/', $cleanEntUrl);
                    $liveEntityUrl = url($cleanEntUrl);
                  ?>
                  <a href="<?= htmlspecialchars($liveEntityUrl) ?>" target="_blank" style="font-size: 0.75rem; color: #38bdf8; text-decoration: none;">
                    Open Live Entity ↗
                  </a>
                <?php endif; ?>
                <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem; font-family: monospace;">
                  Submitted: <?= \App\Core\DateHelper::render($r['created_at'] ?? 'now', 'M j, Y H:i', 'short') ?>
                </div>
              </td>

              <td>
                <span class="ts-badge ts-badge-pink" style="font-size: 0.72rem; text-transform: uppercase;">
                  <?= htmlspecialchars(str_replace('_', ' ', $r['reason'])) ?>
                </span>
              </td>

              <td style="max-width: 240px; font-size: 0.85rem; color: #cbd5e1;">
                <?= htmlspecialchars($r['details'] ?: 'No additional details provided.') ?>
              </td>

              <td>
                <?php if (!empty($r['reporter_username'])): ?>
                  <strong style="color: #f8fafc; font-size: 0.85rem;">@<?= htmlspecialchars($r['reporter_username']) ?></strong>
                  <div style="font-size: 0.72rem; color: #64748b;"><?= htmlspecialchars($r['reporter_email']) ?></div>
                <?php else: ?>
                  <span style="color: #64748b; font-size: 0.82rem;">Anonymous</span>
                  <div style="font-size: 0.7rem; color: #475569; font-family: monospace;"><?= htmlspecialchars($r['ip_address'] ?? 'Guest') ?></div>
                <?php endif; ?>
              </td>

              <td>
                <?php if ($r['status'] === 'pending'): ?>
                  <span class="ts-badge" style="background: rgba(239,68,68,0.2); color: #ef4444; border: 1px solid rgba(239,68,68,0.4);">PENDING</span>
                <?php elseif ($r['status'] === 'actioned'): ?>
                  <span class="ts-badge ts-badge-green">✓ ACTIONED</span>
                <?php elseif ($r['status'] === 'dismissed'): ?>
                  <span class="ts-badge" style="background: rgba(148,163,184,0.15); color: #94a3b8;">DISMISSED</span>
                <?php else: ?>
                  <span class="ts-badge"><?= strtoupper(htmlspecialchars($r['status'])) ?></span>
                <?php endif; ?>
              </td>

              <td style="max-width: 220px; font-size: 0.82rem;">
                <div style="font-weight: 600; color: #38bdf8; margin-bottom: 0.2rem;">
                  <?= htmlspecialchars($r['action_taken'] ?: 'Awaiting Action') ?>
                </div>
                <?php if (!empty($r['admin_notes'])): ?>
                  <div style="color: #94a3b8; font-size: 0.75rem; background: rgba(15,23,42,0.6); padding: 0.3rem 0.5rem; border-radius: 4px; border: 1px solid rgba(148,163,184,0.1);">
                    <?= nl2br(htmlspecialchars($r['admin_notes'])) ?>
                  </div>
                <?php else: ?>
                  <span style="color: #64748b; font-style: italic; font-size: 0.75rem;">No staff notes</span>
                <?php endif; ?>
              </td>

              <td>
                <div style="display: flex; gap: 0.3rem; flex-wrap: wrap;">
                  <!-- EDIT / RE-EDIT RESOLUTION (Always Available!) -->
                  <button type="button" onclick="openEditReportModal(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['entity_title'] ?: ('Entity #' . $r['entity_id']))) ?>', '<?= htmlspecialchars(addslashes($r['status'])) ?>', '<?= htmlspecialchars(addslashes($r['action_taken'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($r['admin_notes'] ?? '')) ?>')" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.22rem 0.5rem; font-size: 0.72rem; background: #6366f1; border-color: #6366f1;" title="Edit Status, Notes & Decision (Anytime)">
                    Edit Decision ✏️
                  </button>

                  <!-- Reopen Option (if closed) -->
                  <?php if ($r['status'] !== 'pending'): ?>
                    <form action="<?= url('/admin/moderation/action/' . $r['id']) ?>" method="POST">
                      <input type="hidden" name="action" value="reopen">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.5rem; font-size: 0.72rem; color: #f59e0b; border-color: rgba(245,158,11,0.4);" title="Reopen Report">
                        Reopen 🔄
                      </button>
                    </form>
                  <?php endif; ?>

                  <!-- Issue Strike to Author -->
                  <button type="button" onclick="openStrikeModal(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['entity_title'] ?: ('Entity #' . $r['entity_id']))) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.5rem; font-size: 0.72rem; color: #ec4899; border-color: rgba(236,72,153,0.4);" title="Issue Strike to Author">
                    Strike ⚠️
                  </button>

                  <!-- Message Reporter -->
                  <?php if (!empty($r['reporter_user_id'])): ?>
                    <button type="button" onclick="openMessageReporterModal(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['reporter_username'] ?? 'User')) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.45rem; font-size: 0.72rem; color: #38bdf8; border-color: rgba(56,189,248,0.4);" title="Direct Message Reporter">
                      Message 💬
                    </button>
                  <?php endif; ?>

                  <!-- Delete Entity -->
                  <form action="<?= url('/admin/moderation/action/' . $r['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete the reported entity? This cannot be undone.');">
                    <input type="hidden" name="action" value="delete_entity">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.45rem; font-size: 0.72rem; color: #ef4444; border-color: rgba(239,68,68,0.4);" title="Delete Reported Content Permanently">
                      Delete 🗑
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">No moderation reports found for this filter.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<!-- MODAL: EDIT REPORT DECISION & NOTES (ANYTIME) -->
<div id="editReportModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-indigo">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #818cf8;">
        <i data-lucide="edit-3"></i> Edit Report Decision &amp; Resolution
      </h3>
      <button type="button" onclick="closeEditReportModal()" class="admin-modal-close" title="Close Modal">✕</button>
    </div>

    <form id="editReportForm" action="" method="POST">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Target Reported Entity</label>
        <input type="text" id="editReportTitle" readonly class="admin-modal-input" style="opacity: 0.8; cursor: not-allowed;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Resolution Status</label>
          <select name="status" id="editReportStatus" class="admin-modal-select">
            <option value="pending">Pending Review ⏳</option>
            <option value="actioned">Actioned &amp; Resolved ✓</option>
            <option value="dismissed">Dismissed (False Report) ✕</option>
          </select>
        </div>

        <div>
          <label class="admin-modal-label">Action Taken Tag</label>
          <input type="text" name="action_taken" id="editReportActionTaken" placeholder="e.g. Warning Issued, Entity Modified" class="admin-modal-input">
        </div>
      </div>

      <!-- Quick Action Presets -->
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label" style="margin-bottom: 0.35rem; font-size: 0.75rem; color: #94a3b8;">Quick Action Presets (Click to autofill tag):</label>
        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
          <button type="button" onclick="setActionPreset('Actioned & Cleared ✓', 'actioned')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #34d399;">✓ Cleared</button>
          <button type="button" onclick="setActionPreset('Warning & Strike Issued ⚠️', 'actioned')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #ec4899;">⚠️ Strike Issued</button>
          <button type="button" onclick="setActionPreset('Entity Hidden from Discovery 👁️', 'actioned')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #38bdf8;">👁️ Entity Hidden</button>
          <button type="button" onclick="setActionPreset('Editorial Changes Requested 📋', 'actioned')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #f59e0b;">📋 Changes Req</button>
          <button type="button" onclick="setActionPreset('Dismissed / False Report ✕', 'dismissed')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #94a3b8;">✕ Dismissed</button>
          <button type="button" onclick="setActionPreset('Reopened for Investigation 🔄', 'pending')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #a855f7;">🔄 Reopened</button>
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Administrative &amp; Investigation Notes</label>
        <textarea name="admin_notes" id="editReportNotes" rows="3" placeholder="Enter findings, decisions, actions taken..." class="admin-modal-textarea"></textarea>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; color: #cbd5e1; font-size: 0.85rem; cursor: pointer;">
          <input type="checkbox" name="notify_reporter" value="1" checked style="accent-color: #6366f1;">
          Dispatch status notification update to reporter
        </label>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeEditReportModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #6366f1; border-color: #6366f1; font-weight: 700;">
          Save Decision Changes 💾
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: ISSUE STRIKE -->
<div id="strikeModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-pink">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #ec4899;">
        <i data-lucide="alert-triangle"></i> Issue Moderation Strike to Author
      </h3>
      <button type="button" onclick="closeStrikeModal()" class="admin-modal-close" title="Close Modal">✕</button>
    </div>

    <form id="strikeForm" action="" method="POST">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Target Reported Entity</label>
        <input type="text" id="strikeTitle" readonly class="admin-modal-input" style="opacity: 0.8; cursor: not-allowed;">
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Strike Reason &amp; Policy Violation</label>
        <textarea name="strike_reason" rows="3" placeholder="Explain the policy violation..." class="admin-modal-textarea" required>Violation of TechScout Community &amp; Quality Guidelines.</textarea>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeStrikeModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #ec4899; border-color: #ec4899; font-weight: 700;">
          Issue Strike ⚠️
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: MESSAGE REPORTER -->
<div id="messageModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;">
        <i data-lucide="message-square"></i> Send Update to Reporting User
      </h3>
      <button type="button" onclick="closeMessageReporterModal()" class="admin-modal-close" title="Close Modal">✕</button>
    </div>

    <form id="messageForm" action="" method="POST">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Recipient Username</label>
        <input type="text" id="messageRecipient" readonly class="admin-modal-input" style="opacity: 0.8; cursor: not-allowed;">
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Message Content</label>
        <textarea name="message" rows="3" placeholder="Thank you for your report. We have investigated and resolved the issue..." class="admin-modal-textarea" required></textarea>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeMessageReporterModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; border-color: #38bdf8; font-weight: 700; color: #05070b;">
          Send Notification 🚀
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditReportModal(id, title, status, actionTaken, notes) {
  document.getElementById('editReportForm').action = '<?= url('/admin/moderation/update/') ?>' + id;
  document.getElementById('editReportTitle').value = title;
  document.getElementById('editReportStatus').value = status;
  document.getElementById('editReportActionTaken').value = actionTaken || '';
  document.getElementById('editReportNotes').value = notes || '';
  document.getElementById('editReportModal').style.display = 'flex';
}

function setActionPreset(tag, status) {
  document.getElementById('editReportActionTaken').value = tag;
  if (status) {
    document.getElementById('editReportStatus').value = status;
  }
}

function closeEditReportModal() {
  document.getElementById('editReportModal').style.display = 'none';
}

function openStrikeModal(id, title) {
  document.getElementById('strikeForm').action = '<?= url('/admin/moderation/strike/') ?>' + id;
  document.getElementById('strikeTitle').value = title;
  document.getElementById('strikeModal').style.display = 'flex';
}

function closeStrikeModal() {
  document.getElementById('strikeModal').style.display = 'none';
}

function openMessageReporterModal(id, username) {
  document.getElementById('messageForm').action = '<?= url('/admin/moderation/message/') ?>' + id;
  document.getElementById('messageRecipient').value = '@' + username;
  document.getElementById('messageModal').style.display = 'flex';
}

function closeMessageReporterModal() {
  document.getElementById('messageModal').style.display = 'none';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
