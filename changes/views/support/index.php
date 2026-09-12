<?php 
  $currentStatus = $currentStatus ?? 'all';
  $categories = $categories ?? [];
?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="max-width: 1040px; margin: 0 auto;">
    
    <!-- Header Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
          <span class="ts-badge ts-badge-cyan">ENGINEERING HELPDESK &amp; SLA OPERATIONS</span>
          <span style="font-size: 0.8rem; color: var(--ts-text-tertiary);">Technical Support, Royalties, Security &amp; Creator Desk</span>
        </div>
        <h1 style="font-size: 2rem; margin: 0;">Support &amp; Inquiries Desk</h1>
      </div>

      <div style="display: flex; gap: 0.75rem; align-items: center;">
        <?php if (!empty($user) && in_array($user['role'] ?? '', ['admin', 'super_admin', 'superadmin'], true)): ?>
          <a href="<?= url('/admin/support/categories') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="border-color: rgba(99,102,241,0.4); color: #818cf8;">
            <i data-lucide="layers"></i> Manage Categories &amp; SLAs
          </a>
        <?php endif; ?>
        <button onclick="document.getElementById('newTicketModal').style.display='flex'" class="ts-btn ts-btn-primary">
          <i data-lucide="plus-circle"></i> Open New Ticket
        </button>
      </div>
    </div>

    <!-- Status Filters -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
      <a href="<?= url('/support') ?>" class="ts-btn ts-btn-sm <?= empty($currentStatus) || $currentStatus === 'all' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        All Inquiries
      </a>
      <a href="<?= url('/support?status=open') ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'open' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        Open
      </a>
      <a href="<?= url('/support?status=in_progress') ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'in_progress' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        In Progress
      </a>
      <a href="<?= url('/support?status=waiting_user') ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'waiting_user' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        Waiting for You
      </a>
      <a href="<?= url('/support?status=resolved') ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'resolved' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
        Resolved / Closed
      </a>
    </div>

    <!-- Tickets List Card -->
    <div class="ts-card" style="padding: 0; overflow: hidden;">
      <?php if (!empty($tickets)): ?>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Ticket Reference</th>
              <th>Subject &amp; Hierarchy</th>
              <th>Status</th>
              <th>Priority &amp; SLA</th>
              <th>Last Activity</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tickets as $t): ?>
              <tr>
                <td>
                  <strong style="color: #38bdf8; font-family: monospace; font-size: 0.95rem;">#<?= htmlspecialchars($t['ticket_number']) ?></strong>
                  <div style="font-size: 0.72rem; color: var(--ts-text-tertiary);"><?= (int)$t['message_count'] ?> messages</div>
                </td>
                <td>
                  <a href="<?= url('/support/ticket/' . $t['ticket_number']) ?>" style="color: var(--ts-text-primary); font-weight: 700; text-decoration: none; font-size: 0.98rem; display: block; margin-bottom: 0.25rem;">
                    <?= htmlspecialchars($t['subject']) ?>
                  </a>
                  <div style="display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap;">
                    <?php if (!empty($t['department'])): ?>
                      <span style="font-size: 0.7rem; color: #94a3b8; background: rgba(148,163,184,0.1); padding: 0.15rem 0.45rem; border-radius: 4px;"><?= htmlspecialchars($t['department']) ?></span>
                    <?php endif; ?>
                    <span class="ts-badge" style="font-size: 0.68rem; text-transform: uppercase;"><?= htmlspecialchars($t['category']) ?></span>
                    <?php if (!empty($t['subcategory']) && $t['subcategory'] !== 'General'): ?>
                      <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem;"><?= htmlspecialchars($t['subcategory']) ?></span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <?php if ($t['status'] === 'open'): ?>
                    <span class="ts-badge ts-badge-cyan">OPEN</span>
                  <?php elseif ($t['status'] === 'in_progress'): ?>
                    <span class="ts-badge ts-badge-pink">IN PROGRESS</span>
                  <?php elseif ($t['status'] === 'waiting_user'): ?>
                    <span class="ts-badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4);">REPLY NEEDED</span>
                  <?php elseif ($t['status'] === 'resolved'): ?>
                    <span class="ts-badge ts-badge-green">RESOLVED</span>
                  <?php else: ?>
                    <span class="ts-badge">CLOSED</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: <?= $t['priority'] === 'urgent' ? '#ef4444' : ($t['priority'] === 'high' ? '#f97316' : '#38bdf8') ?>;">
                    <?= htmlspecialchars($t['priority']) ?>
                  </div>
                  <?php if (!empty($t['sla_due_at'])): ?>
                    <div style="font-size: 0.7rem; color: var(--ts-text-tertiary); font-family: monospace;" title="SLA Resolution Target">
                      SLA: <?= date('M j, H:i', strtotime($t['sla_due_at'])) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td style="font-size: 0.8rem; color: var(--ts-text-tertiary); font-family: monospace;">
                  <?= \App\Core\DateHelper::format($t['last_reply_at']) ?>
                </td>
                <td>
                  <a href="<?= url('/support/ticket/' . $t['ticket_number']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.3rem 0.75rem; font-size: 0.78rem;">
                    View Thread ↗
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div style="text-align: center; padding: 4rem 2rem; color: var(--ts-text-tertiary);">
          <i data-lucide="life-buoy" style="width: 48px; height: 48px; margin-bottom: 1rem; color: #475569;"></i>
          <h3 style="color: var(--ts-text-primary); margin-bottom: 0.25rem;">No Support Inquiries Yet</h3>
          <p style="font-size: 0.9rem; margin-bottom: 1.5rem;">Have questions regarding publishing, API limits, rewards, or platform features?</p>
          <button onclick="document.getElementById('newTicketModal').style.display='flex'" class="ts-btn ts-btn-primary ts-btn-sm">
            <i data-lucide="plus"></i> Open First Ticket
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Open New Ticket Modal with Dynamic Categories & Subcategories -->
<div id="newTicketModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="ts-card" style="width: 100%; max-width: 660px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.35); box-shadow: 0 25px 60px rgba(0,0,0,0.8);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <div>
        <h2 style="font-size: 1.3rem; color: #f8fafc; margin: 0;">Open Support &amp; Engineering Ticket</h2>
        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem;">Tickets are routed directly to specialized engineering &amp; editorial desks.</div>
      </div>
      <button onclick="document.getElementById('newTicketModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form action="<?= url('/support/ticket/create') ?>" method="POST" enctype="multipart/form-data">
      <div style="margin-bottom: 1.1rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Subject / Inquiry Headline <span style="color: #ef4444;">*</span> <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 150 chars)</span></label>
        <input type="text" name="subject" maxlength="150" placeholder="e.g. Issue connecting custom GPT endpoint, or author royalty question" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;" required>
      </div>

      <!-- Department & Category Selector -->
      <div class="ts-responsive-form-grid" style="margin-bottom: 1.1rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Department / Category <span style="color: #ef4444;">*</span></label>
          <select id="ticketCategorySelect" name="category" onchange="onCategoryChanged()" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;">
            <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat['slug']) ?>" data-department="<?= htmlspecialchars($cat['department']) ?>" data-priority="<?= htmlspecialchars($cat['default_priority']) ?>" data-sla="<?= (int)$cat['sla_hours'] ?>">
                [<?= htmlspecialchars($cat['department']) ?>] <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Specialized Subcategory</label>
          <select id="ticketSubcategorySelect" name="subcategory" onchange="onSubcategoryChanged()" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;">
            <!-- Populated dynamically by JS -->
          </select>
        </div>
      </div>

      <!-- Priority Level & SLA Guarantee Indicator -->
      <div style="margin-bottom: 1.1rem; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 8px; padding: 0.85rem 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <label style="font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin: 0;">Priority &amp; Expected SLA Response</label>
          <span id="slaBadge" class="ts-badge ts-badge-cyan" style="font-family: monospace;">SLA: 6 Hours</span>
        </div>
        <select id="ticketPrioritySelect" name="priority" onchange="updateSlaBadge()" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;">
          <option value="urgent">🚨 Urgent (Target SLA: 2 Hours — Outage / Security Compromise)</option>
          <option value="high" selected>⚡ High (Target SLA: 6 Hours — Production / Payout Issue)</option>
          <option value="medium">📌 Medium (Target SLA: 24 Hours — General Inquiry / Verification)</option>
          <option value="low">☕ Low (Target SLA: 48 Hours — Non-urgent Question / Feedback)</option>
        </select>
      </div>

      <div style="margin-bottom: 1.1rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Detailed Explanation <span style="color: #ef4444;">*</span> <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 2,500 chars)</span></label>
        <textarea name="message" rows="4" maxlength="2500" placeholder="Please provide detailed technical reproduction steps, error stack trace, affected account email, or context..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem; resize: vertical;" required></textarea>
      </div>

      <div class="ts-responsive-form-grid" style="margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Upload Attachment / Log File</label>
          <input type="file" name="attachment_file" accept=".png,.jpg,.jpeg,.webp,.pdf,.zip,.log,.txt,.json" style="width: 100%; font-size: 0.82rem; color: #94a3b8;">
        </div>
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.35rem;">Or Diagnostic URL (Optional)</label>
          <input type="text" name="attachment_url" placeholder="https://... or /storage/uploads/... or screenshot URL" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.55rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; flex-wrap: wrap;">
        <button type="button" onclick="document.getElementById('newTicketModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm" style="flex: 1;">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700; flex: 2;">Submit Support Ticket 🚀</button>
      </div>
    </form>
  </div>
</div>

<script>
const supportCategoryData = <?= json_encode($categories) ?>;

function onCategoryChanged() {
  const catSelect = document.getElementById('ticketCategorySelect');
  const subSelect = document.getElementById('ticketSubcategorySelect');
  const prioritySelect = document.getElementById('ticketPrioritySelect');
  const selectedSlug = catSelect.value;

  const currentCat = supportCategoryData.find(c => c.slug === selectedSlug);
  subSelect.innerHTML = '';

  if (currentCat && currentCat.subcategories && currentCat.subcategories.length > 0) {
    currentCat.subcategories.forEach((sub, idx) => {
      const opt = document.createElement('option');
      opt.value = sub.slug || sub.name;
      opt.textContent = sub.name;
      opt.dataset.priority = sub.priority || currentCat.default_priority || 'medium';
      opt.dataset.sla = sub.sla_hours || currentCat.sla_hours || 24;
      if (idx === 0) opt.selected = true;
      subSelect.appendChild(opt);
    });
    onSubcategoryChanged();
  } else {
    const opt = document.createElement('option');
    opt.value = 'General';
    opt.textContent = 'General Inquiries';
    subSelect.appendChild(opt);
    if (currentCat && currentCat.default_priority) {
      prioritySelect.value = currentCat.default_priority;
    }
    updateSlaBadge();
  }
}

function onSubcategoryChanged() {
  const subSelect = document.getElementById('ticketSubcategorySelect');
  const prioritySelect = document.getElementById('ticketPrioritySelect');
  const selectedOpt = subSelect.options[subSelect.selectedIndex];

  if (selectedOpt && selectedOpt.dataset.priority) {
    prioritySelect.value = selectedOpt.dataset.priority;
  }
  updateSlaBadge();
}

function updateSlaBadge() {
  const priority = document.getElementById('ticketPrioritySelect').value;
  const slaBadge = document.getElementById('slaBadge');
  const slaMap = {
    'urgent': { text: 'SLA: 2 Hours Guaranteed', color: '#ef4444', bg: 'rgba(239,68,68,0.15)' },
    'high': { text: 'SLA: 6 Hours Target', color: '#f97316', bg: 'rgba(249,115,22,0.15)' },
    'medium': { text: 'SLA: 24 Hours Standard', color: '#38bdf8', bg: 'rgba(56,189,248,0.15)' },
    'low': { text: 'SLA: 48 Hours General', color: '#94a3b8', bg: 'rgba(148,163,184,0.15)' }
  };

  const item = slaMap[priority] || slaMap['medium'];
  slaBadge.textContent = item.text;
  slaBadge.style.color = item.color;
  slaBadge.style.background = item.bg;
  slaBadge.style.borderColor = item.color;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  if (document.getElementById('ticketCategorySelect')) {
    onCategoryChanged();
  }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
