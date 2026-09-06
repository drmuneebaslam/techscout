<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="max-width: 920px; margin: 0 auto;">
    
    <!-- Breadcrumb & Back -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
      <a href="<?= !empty($isAdmin) ? url('/admin/tickets') : url('/support') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="arrow-left"></i> <?= !empty($isAdmin) ? 'Back to Admin Tickets Queue' : 'Back to My Support Tickets' ?>
      </a>
      <span style="font-family: monospace; font-size: 0.9rem; color: #38bdf8; font-weight: 700;">
        Ticket #<?= htmlspecialchars($ticket['ticket_number']) ?>
      </span>
    </div>

    <!-- Ticket Summary Hero Card -->
    <div class="ts-card" style="margin-bottom: 2rem; border-color: rgba(99,102,241,0.35); padding: 1.75rem;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
        <div style="flex: 1; min-width: 280px;">
          <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
            <span class="ts-badge ts-badge-pink" style="text-transform: uppercase; font-weight: 700;"><?= htmlspecialchars($ticket['category']) ?></span>
            
            <span class="ts-badge" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: <?= $ticket['priority'] === 'urgent' ? '#ef4444' : ($ticket['priority'] === 'high' ? '#f97316' : '#38bdf8') ?>; border: 1px solid currentColor;">
              PRIORITY: <?= htmlspecialchars($ticket['priority']) ?>
            </span>

            <?php if ($ticket['status'] === 'open'): ?>
              <span class="ts-badge" style="background: rgba(239,68,68,0.2); color: #ef4444; border: 1px solid rgba(239,68,68,0.4); font-weight: 700;">OPEN</span>
            <?php elseif ($ticket['status'] === 'in_progress'): ?>
              <span class="ts-badge ts-badge-pink" style="font-weight: 700;">IN PROGRESS</span>
            <?php elseif ($ticket['status'] === 'waiting_user'): ?>
              <span class="ts-badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); font-weight: 700;">WAITING FOR USER</span>
            <?php elseif ($ticket['status'] === 'resolved'): ?>
              <span class="ts-badge ts-badge-green" style="font-weight: 700;">RESOLVED ✓</span>
            <?php else: ?>
              <span class="ts-badge">CLOSED</span>
            <?php endif; ?>
          </div>

          <h1 style="font-size: 1.7rem; margin: 0 0 0.5rem 0; color: var(--ts-text-primary); font-family: 'Outfit', sans-serif;">
            <?= htmlspecialchars($ticket['subject']) ?>
          </h1>
          
          <div style="font-size: 0.82rem; color: var(--ts-text-secondary); display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <span>Requester: <strong style="color: var(--ts-text-primary);"><?= htmlspecialchars($ticket['user_display_name'] ?? $ticket['username']) ?></strong> (<?= htmlspecialchars($ticket['email']) ?>)</span>
          </div>
        </div>

        <div style="font-size: 0.8rem; color: var(--ts-text-tertiary); font-family: monospace; text-align: right;">
          <div>Created: <?= \App\Core\DateHelper::formatDateTime($ticket['created_at']) ?></div>
          <?php if (!empty($ticket['updated_at'])): ?>
            <div style="color: #38bdf8; margin-top: 0.25rem;">Updated: <?= \App\Core\DateHelper::formatDateTime($ticket['updated_at']) ?></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Thread Messages Timeline -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 2.5rem;">
      <?php foreach ($messages as $msg): ?>
        <?php 
          $isStaff = !empty($msg['is_staff']); 
          $isInternal = !empty($msg['is_internal_note']); 
          $attachUrl = $msg['attachment_url'] ?? '';
          $isImage = false;
          if (!empty($attachUrl)) {
            $ext = strtolower(pathinfo(parse_url($attachUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'], true);
          }
        ?>
        
        <div class="ts-card" style="padding: 1.5rem; background: <?= $isInternal ? 'rgba(234,179,8,0.06)' : ($isStaff ? 'var(--ts-card-bg-elevated, var(--ts-card-bg))' : 'var(--ts-card-bg)') ?>; border: 1px solid <?= $isInternal ? 'rgba(234,179,8,0.35)' : ($isStaff ? 'rgba(99,102,241,0.45)' : 'var(--ts-border-subtle)') ?>; border-radius: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 0.6rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
              <div style="width: 34px; height: 34px; border-radius: 50%; background: <?= $isStaff ? 'linear-gradient(135deg, #6366f1, #38bdf8)' : '#1e293b' ?>; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 0.9rem; flex-shrink: 0;">
                <?= strtoupper(substr($msg['display_name'] ?? $msg['username'] ?? 'U', 0, 1)) ?>
              </div>
              <div>
                <strong style="color: <?= $isStaff ? '#38bdf8' : 'var(--ts-text-primary)' ?>; font-size: 0.92rem;"><?= htmlspecialchars($msg['display_name'] ?? $msg['username']) ?></strong>
                <span style="font-size: 0.72rem; color: var(--ts-text-tertiary); margin-left: 0.35rem;">@<?= htmlspecialchars($msg['username']) ?></span>
                <?php if ($isStaff): ?>
                  <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem; padding: 0.05rem 0.4rem; margin-left: 0.35rem; font-weight: 700;">SUPPORT ENGINEER</span>
                <?php endif; ?>
                <?php if ($isInternal): ?>
                  <span class="ts-badge" style="background: rgba(234,179,8,0.2); color: #eab308; font-size: 0.65rem; margin-left: 0.35rem; font-weight: 700;">🔒 INTERNAL STAFF NOTE</span>
                <?php endif; ?>
              </div>
            </div>

            <div style="font-size: 0.78rem; color: var(--ts-text-tertiary); font-family: monospace;">
              <?= \App\Core\DateHelper::formatDateTime($msg['created_at']) ?>
            </div>
          </div>

          <!-- Message Body -->
          <div style="color: var(--ts-text-primary); font-size: 0.96rem; line-height: 1.7; white-space: pre-wrap; word-break: break-word;">
            <?= htmlspecialchars($msg['message']) ?>
          </div>

          <!-- Attachment Box / Image Preview -->
          <?php if (!empty($attachUrl)): ?>
            <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--ts-border-subtle);">
              <?php if ($isImage): ?>
                <div>
                  <span style="font-size: 0.75rem; color: var(--ts-text-secondary); font-weight: 600; display: block; margin-bottom: 0.4rem;">📷 Attached Screenshot / Graphic:</span>
                  <div style="display: inline-block; border-radius: 8px; overflow: hidden; border: 1px solid var(--ts-border-subtle); cursor: pointer;" onclick="openTicketImgModal('<?= htmlspecialchars(addslashes(url($attachUrl))) ?>')">
                    <img src="<?= htmlspecialchars(url($attachUrl)) ?>" alt="Attached Image" style="max-height: 220px; max-width: 100%; display: block; object-fit: contain; background: #000;">
                  </div>
                </div>
              <?php else: ?>
                <div style="display: inline-flex; align-items: center; gap: 0.6rem; background: rgba(56,189,248,0.08); border: 1px solid rgba(56,189,248,0.25); padding: 0.6rem 1rem; border-radius: 8px;">
                  <i data-lucide="file-text" style="width: 18px; height: 18px; color: #38bdf8;"></i>
                  <div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); font-family: monospace;">
                      <?= htmlspecialchars(basename(parse_url($attachUrl, PHP_URL_PATH))) ?>
                    </div>
                    <a href="<?= htmlspecialchars(url($attachUrl)) ?>" target="_blank" download style="font-size: 0.75rem; color: #38bdf8; text-decoration: none;">
                      Download Attached File ↗
                    </a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Interactive Reply Box -->
    <div class="ts-card" style="padding: 2rem; background: var(--ts-card-bg); border: 1px solid var(--ts-border-main); border-radius: 12px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <h3 style="font-size: 1.25rem; color: var(--ts-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="message-square" style="color: #38bdf8;"></i> Post Response to Ticket
        </h3>

        <!-- Admin Canned Response Macros -->
        <?php if (!empty($isAdmin)): ?>
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 0.75rem; color: var(--ts-text-tertiary);">Macro Snippets:</span>
            <select onchange="insertMacro(this.value)" style="background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.35rem 0.75rem; border-radius: 6px; color: #38bdf8; font-size: 0.8rem;">
              <option value="">-- Choose Quick Canned Response --</option>
              <option value="review">🔍 Under Engineering Review</option>
              <option value="logs">📋 Request Diagnostic Logs & Steps</option>
              <option value="deployed">🚀 Fix Deployed to Production</option>
              <option value="roadmap">📌 Feature Logged in Product Roadmap</option>
              <option value="resolved">✅ Issue Resolved & Verified</option>
            </select>
          </div>
        <?php endif; ?>
      </div>

      <form action="<?= url('/support/ticket/' . $ticket['ticket_number'] . '/reply') ?>" method="POST" enctype="multipart/form-data">
        <div style="margin-bottom: 1.25rem;">
          <textarea name="message" id="ticketReplyMessage" rows="5" placeholder="Type your detailed reply, resolution steps, or feedback..." style="width: 100%; background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.85rem 1.1rem; border-radius: 8px; color: var(--ts-text-primary); font-size: 0.95rem; resize: vertical; line-height: 1.6;" required></textarea>
        </div>

        <!-- Attachment Handling: Direct File Upload + URL Fallback -->
        <div style="background: var(--ts-card-bg-elevated, rgba(148,163,184,0.05)); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--ts-border-subtle); margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.5rem;">
            📎 Attach Diagnostic Files / Screenshots (Images, PDF, ZIP, Logs up to 25MB)
          </label>
          <div class="ts-responsive-form-grid">
            <div>
              <span style="font-size: 0.75rem; color: var(--ts-text-secondary); display: block; margin-bottom: 0.25rem;">Direct File Upload:</span>
              <input type="file" name="attachment_file" accept="image/*,.pdf,.zip,.log,.txt,.csv,.json,.md,.tar,.gz" style="width: 100%; background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.5rem; border-radius: 6px; color: var(--ts-text-secondary); font-size: 0.82rem;">
            </div>
            <div>
              <span style="font-size: 0.75rem; color: var(--ts-text-secondary); display: block; margin-bottom: 0.25rem;">Or Paste Cloud Attachment URL:</span>
              <input type="url" name="attachment_url" placeholder="https://..." style="width: 100%; background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.55rem 0.85rem; border-radius: 6px; color: var(--ts-text-primary); font-size: 0.85rem;">
            </div>
          </div>
        </div>

        <!-- Status & Priority Controls -->
        <div style="display: grid; grid-template-columns: <?= !empty($isAdmin) ? '1fr 1fr 1fr' : '1fr' ?>; gap: 1rem; margin-bottom: 1.25rem; align-items: center;">
          <?php if (!empty($isAdmin)): ?>
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--ts-text-secondary); margin-bottom: 0.3rem;">Ticket Status</label>
              <select name="status" style="width: 100%; background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.6rem 0.85rem; border-radius: 6px; color: var(--ts-text-primary); font-size: 0.85rem;">
                <option value="waiting_user" <?= $ticket['status'] === 'waiting_user' ? 'selected' : '' ?>>Waiting for User Reply</option>
                <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Re-Open Ticket</option>
                <option value="resolved" <?= $ticket['status'] === 'resolved' ? 'selected' : '' ?>>Resolved ✓</option>
                <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
              </select>
            </div>

            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--ts-text-secondary); margin-bottom: 0.3rem;">Priority Level</label>
              <select name="priority" style="width: 100%; background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.6rem 0.85rem; border-radius: 6px; color: var(--ts-text-primary); font-size: 0.85rem;">
                <option value="low" <?= $ticket['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                <option value="medium" <?= $ticket['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                <option value="high" <?= $ticket['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                <option value="urgent" <?= $ticket['priority'] === 'urgent' ? 'selected' : '' ?>>Urgent 🔥</option>
              </select>
            </div>

            <div style="padding-top: 1.25rem;">
              <label style="display: flex; align-items: center; gap: 0.5rem; color: #eab308; font-size: 0.85rem; cursor: pointer;">
                <input type="checkbox" name="is_internal_note" value="1" style="accent-color: #eab308; width: 16px; height: 16px;">
                <span><strong>Internal Note</strong> (Hidden from user)</span>
              </label>
            </div>
          <?php else: ?>
            <div style="display: flex; align-items: center;">
              <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--ts-text-secondary); font-size: 0.85rem; cursor: pointer;">
                <input type="checkbox" name="status" value="resolved" style="accent-color: var(--ts-primary);">
                <span>Mark ticket as completely resolved</span>
              </label>
            </div>
          <?php endif; ?>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--ts-border-subtle); padding-top: 1.25rem;">
          <span style="font-size: 0.78rem; color: var(--ts-text-tertiary);">
            💡 Replies notify all relevant participants automatically.
          </span>
          <button type="submit" class="ts-btn ts-btn-primary" style="font-weight: 700; padding: 0.65rem 1.75rem;">
            <i data-lucide="send"></i> Post Response 🚀
          </button>
        </div>
      </form>
    </div>

  </div>
</div>

<!-- Image Attachment Fullscreen Modal -->
<div id="ticketImgModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.92); backdrop-filter: blur(10px); z-index: 999999; align-items: center; justify-content: center; padding: 2rem;" onclick="this.style.display='none'">
  <div style="max-width: 90vw; max-height: 90vh;">
    <img id="ticketModalImgEl" src="" alt="Full Preview" style="max-width: 100%; max-height: 85vh; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 25px 60px rgba(0,0,0,0.95);">
  </div>
</div>

<script>
function openTicketImgModal(url) {
  document.getElementById('ticketModalImgEl').src = url;
  document.getElementById('ticketImgModal').style.display = 'flex';
}

function insertMacro(key) {
  const textarea = document.getElementById('ticketReplyMessage');
  if (!textarea || !key) return;

  const macros = {
    review: "Hello,\n\nThank you for reaching out. Our engineering team is currently reviewing the technical details and logs for this inquiry. We will provide an update within 24 hours.\n\nBest regards,\nTechScout Engineering Support",
    logs: "Hello,\n\nTo help us diagnose and resolve this issue efficiently, could you please provide:\n1. Your operating system and browser version\n2. Any console errors or relevant error messages\n3. The exact steps to reproduce the issue\n\nThank you for your cooperation,\nTechScout Support",
    deployed: "Hello,\n\nWe are pleased to inform you that a fix for this issue has been deployed to production. Please refresh your session and verify. Let us know if you encounter any further issues.\n\nBest regards,\nTechScout Staff",
    roadmap: "Hello,\n\nThank you for this valuable feature suggestion! We have logged this in our internal product roadmap for upcoming release planning.\n\nBest regards,\nTechScout Product Team",
    resolved: "Hello,\n\nThis support ticket has been marked as resolved following our verification. If you require further assistance, simply reply to this thread to re-open it.\n\nThank you for choosing TechScout!"
  };

  if (macros[key]) {
    textarea.value = macros[key];
    textarea.focus();
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
