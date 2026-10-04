<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">SUPPORT DESK</span>
        <span style="font-size: 0.8rem; color: #64748b;">Member Inquiries, Technical Support &amp; Appeals</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0;">Support Desk &amp; Engineering Tickets Queue</h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="location.reload()" class="ts-btn ts-btn-outline ts-btn-sm" title="Refresh Tickets Queue">
        <i data-lucide="rotate-cw"></i> Refresh Stream 🔄
      </button>
      <a href="<?= url('/admin/contact-messages') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="mail"></i> Inbox Inquiries
      </a>
      <a href="<?= url('/admin/users') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="users"></i> Users Roster
      </a>
    </div>
  </div>

  <!-- Stat Grid -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Support Tickets</div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= number_format($counts['all']) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Platform lifetime total</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Open &amp; Unresolved</div>
      <div class="admin-stat-val" style="color: #ef4444;"><?= number_format($counts['open']) ?></div>
      <div style="font-size: 0.8rem; color: #f87171;">Awaiting staff response</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">In Progress</div>
      <div class="admin-stat-val" style="color: #ec4899;"><?= number_format($counts['in_progress']) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Being investigated</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Resolved Tickets</div>
      <div class="admin-stat-val" style="color: #10b981;"><?= number_format($counts['resolved']) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Successfully closed</div>
    </div>
  </div>

  <!-- Filter & Search Navigation -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/tickets') ?>" class="ts-btn ts-btn-sm <?= empty($currentStatus) || $currentStatus === 'all' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        All (<?= $counts['all'] ?>)
      </a>
      <a href="<?= url('/admin/tickets?status=open') ?>" class="ts-btn ts-btn-sm <?= ($currentStatus ?? '') === 'open' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        Open (<?= $counts['open'] ?>)
      </a>
      <a href="<?= url('/admin/tickets?status=in_progress') ?>" class="ts-btn ts-btn-sm <?= ($currentStatus ?? '') === 'in_progress' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        In Progress (<?= $counts['in_progress'] ?>)
      </a>
      <a href="<?= url('/admin/tickets?status=resolved') ?>" class="ts-btn ts-btn-sm <?= ($currentStatus ?? '') === 'resolved' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        Resolved (<?= $counts['resolved'] ?>)
      </a>
    </div>

    <!-- Category Filter & Search -->
    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
      <select onchange="location.href='<?= url('/admin/tickets?status=' . ($currentStatus ?? 'all')) ?>&category=' + this.value" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.4rem 0.75rem; border-radius: 6px; color: #f8fafc; font-size: 0.82rem;">
        <option value="all" <?= empty($currentCategory) || $currentCategory === 'all' ? 'selected' : '' ?>>All Departments</option>
        <option value="technical" <?= ($currentCategory ?? '') === 'technical' ? 'selected' : '' ?>>Technical Support</option>
        <option value="creator" <?= ($currentCategory ?? '') === 'creator' ? 'selected' : '' ?>>Creator Studio</option>
        <option value="billing" <?= ($currentCategory ?? '') === 'billing' ? 'selected' : '' ?>>Billing / Payouts</option>
        <option value="appeal" <?= ($currentCategory ?? '') === 'appeal' ? 'selected' : '' ?>>Moderation Appeal</option>
        <option value="security" <?= ($currentCategory ?? '') === 'security' ? 'selected' : '' ?>>Security Report</option>
      </select>

      <input type="text" id="ticketSearchInput" onkeyup="filterTicketRows()" placeholder="🔍 Search ticket #, subject, user..." style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.4rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem; min-width: 240px;">
    </div>
  </div>

  <!-- Tickets Queue Table -->
    <table class="admin-table" id="ticketsAdminTable" data-paginate="true" data-search-id="ticketSearchInput" data-page-size="15">
      <thead>
        <tr>
          <th>Reference #</th>
          <th>Subject &amp; Department</th>
          <th>User / Requester</th>
          <th>Priority &amp; SLA Status</th>
          <th>Status</th>
          <th>Last Activity</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($tickets)): ?>
          <?php foreach ($tickets as $t): ?>
            <?php
              $isSlaBreached = (!empty($t['sla_due_at']) && strtotime($t['sla_due_at']) < time() && $t['status'] === 'open');
              $slaRemainingSec = !empty($t['sla_due_at']) ? strtotime($t['sla_due_at']) - time() : 0;
            ?>
            <tr class="ticket-row" data-search="<?= htmlspecialchars(strtolower($t['ticket_number'] . ' ' . $t['subject'] . ' ' . ($t['user_display_name'] ?? '') . ' ' . $t['email'] . ' ' . $t['category'])) ?>">
              <td>
                <strong style="color: #38bdf8; font-family: monospace; font-size: 0.95rem;">#<?= htmlspecialchars($t['ticket_number']) ?></strong>
                <div style="font-size: 0.75rem; color: #64748b;"><?= (int)($t['message_count'] ?? 1) ?> messages</div>
              </td>
              <td>
                <a href="<?= url('/support/ticket/' . $t['ticket_number']) ?>" style="color: #f8fafc; font-weight: 700; text-decoration: none; font-size: 0.95rem; display: block; margin-bottom: 0.2rem;">
                  <?= htmlspecialchars($t['subject']) ?>
                </a>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; text-transform: uppercase;"><?= htmlspecialchars($t['category']) ?></span>
                <?php if (!empty($t['is_escalated'])): ?>
                  <span class="ts-badge" style="background: rgba(239,68,68,0.25); color: #ef4444; border: 1px solid rgba(239,68,68,0.5); font-size: 0.65rem; font-weight: 800;">🚨 ESCALATED</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="<?= url('/admin/users?q=' . urlencode($t['username'])) ?>" target="_blank" style="text-decoration: none;">
                  <strong style="color: #f8fafc; font-size: 0.9rem;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#f8fafc'"><?= htmlspecialchars($t['user_display_name'] ?? $t['username']) ?> <span style="font-size: 0.7rem;">↗</span></strong>
                </a>
                <div style="font-size: 0.75rem; color: #38bdf8;"><?= htmlspecialchars($t['email']) ?></div>
              </td>
              <td>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                  <span style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: <?= $t['priority'] === 'urgent' ? '#ef4444' : ($t['priority'] === 'high' ? '#f97316' : '#38bdf8') ?>;">
                    <?= htmlspecialchars($t['priority'] ?? 'normal') ?>
                  </span>
                  <?php if ($t['status'] === 'open' && !empty($t['sla_due_at'])): ?>
                    <?php if ($isSlaBreached): ?>
                      <span class="ts-badge" style="background: rgba(239,68,68,0.2); color: #ef4444; border: 1px solid rgba(239,68,68,0.5); font-size: 0.7rem; font-weight: 800;">
                        ⚠️ SLA BREACHED
                      </span>
                    <?php else: ?>
                      <span class="ts-badge" style="background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); font-size: 0.7rem; font-weight: 700;">
                        ⏱️ <?= max(1, ceil($slaRemainingSec / 3600)) ?>h SLA Due
                      </span>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <?php if ($t['status'] === 'open'): ?>
                  <span class="ts-badge" style="background: rgba(239,68,68,0.2); color: #ef4444; border: 1px solid rgba(239,68,68,0.4); font-weight: 700;">OPEN</span>
                <?php elseif ($t['status'] === 'in_progress'): ?>
                  <span class="ts-badge ts-badge-pink" style="font-weight: 700;">IN PROGRESS</span>
                <?php elseif ($t['status'] === 'waiting_user'): ?>
                  <span class="ts-badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); font-weight: 700;">WAITING USER</span>
                <?php elseif ($t['status'] === 'resolved'): ?>
                  <span class="ts-badge ts-badge-green" style="font-weight: 700;">RESOLVED ✓</span>
                <?php else: ?>
                  <span class="ts-badge">CLOSED</span>
                <?php endif; ?>
              </td>
              <td style="font-size: 0.78rem; color: #64748b; font-family: monospace;">
                <?= \App\Core\DateHelper::formatDateTime($t['last_reply_at']) ?>
              </td>
              <td>
                <a href="<?= url('/support/ticket/' . $t['ticket_number']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; font-weight: 700;">
                  <i data-lucide="message-square"></i> Open &amp; Reply ↗
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">No support tickets found for this filter.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<script>
function filterTicketRows() {
  if (window.ticketsAdminTablePager) {
    window.ticketsAdminTablePager.refresh();
  }
}
document.addEventListener('DOMContentLoaded', function() {
  window.ticketsAdminTablePager = window.paginateAdminTable('ticketsAdminTable', {
    pageSize: 15,
    searchInputId: 'ticketSearchInput'
  });
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
