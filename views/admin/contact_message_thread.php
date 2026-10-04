<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Top Bar -->
  <div class="admin-topbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <a href="<?= url('/admin/contact-messages') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
          &larr; Back to Inbox
        </a>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0;">Inquiry Thread</h1>
    </div>
  </div>

  <div class="admin-table-wrap" style="width: 100%; max-width: 900px; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.3); border-radius: 12px; margin-bottom: 3rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;"><?= htmlspecialchars($msg['subject'] ?: 'Inquiry') ?></h2>
      <span style="font-family: monospace; font-size: 0.85rem; color: #cbd5e1;"><?= \App\Core\DateHelper::format($msg['created_at']) ?></span>
    </div>

    <div style="background: rgba(15,23,42,0.6); padding: 1.25rem; border-radius: 8px; margin-bottom: 2rem; font-size: 0.95rem; display: flex; justify-content: space-between;">
      <div>
        <div style="margin-bottom: 0.5rem;"><strong style="color: #64748b;">From:</strong> <span style="color: #f8fafc; font-weight: 700;"><?= htmlspecialchars($msg['name']) ?></span></div>
        <div><strong style="color: #64748b;">Email:</strong> <span style="color: #38bdf8;"><?= htmlspecialchars($msg['email']) ?></span></div>
      </div>
      <div style="text-align: right;">
        <?php if ($msg['status'] === 'unread'): ?>
          <span class="ts-badge ts-badge-pink">NEW / UNREAD</span>
        <?php elseif ($msg['status'] === 'replied'): ?>
          <span class="ts-badge ts-badge-green">REPLIED ✅</span>
        <?php else: ?>
          <span class="ts-badge ts-badge-cyan">READ</span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Original Message -->
    <div style="margin-bottom: 2.5rem;">
      <h4 style="color: #f8fafc; font-size: 1rem; margin: 0 0 0.75rem 0;">Original Message:</h4>
      <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1.5rem; color: #cbd5e1; line-height: 1.7; font-size: 0.95rem; white-space: pre-wrap;"><?= htmlspecialchars($msg['message']) ?></div>
    </div>

    <!-- Existing Reply -->
    <?php if (!empty($msg['reply_text'])): ?>
      <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); border-radius: 8px; padding: 1.5rem; margin-bottom: 2.5rem;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
          <div style="color: #34d399; font-weight: 700; font-size: 1rem;"><i data-lucide="check-circle" style="width: 16px; height: 16px; vertical-align: -3px; margin-right: 4px;"></i> Staff Reply Recorded</div>
          <div style="color: #64748b; font-size: 0.85rem; font-family: monospace;"><?= \App\Core\DateHelper::format($msg['replied_at']) ?></div>
        </div>
        <div style="color: #f8fafc; font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap;"><?= htmlspecialchars($msg['reply_text']) ?></div>
      </div>
    <?php else: ?>
      <!-- Reply Form -->
      <form method="POST" action="<?= url('/admin/contact-messages/reply/' . $msg['id']) ?>" style="background: rgba(15,23,42,0.8); border: 1px solid rgba(99,102,241,0.25); border-radius: 8px; padding: 1.5rem;">
        <h4 style="color: #f8fafc; font-size: 1rem; margin: 0 0 1rem 0;">Compose Staff Reply</h4>
        <div style="margin-bottom: 1.25rem;">
          <textarea name="reply_text" rows="5" placeholder="Enter reply message to send to the user..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 1rem; border-radius: 6px; color: #f8fafc; font-size: 0.95rem; font-family: inherit;" required></textarea>
          <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.5rem;">This reply will be sent via email to <?= htmlspecialchars($msg['email']) ?> and recorded in the system.</div>
        </div>
        <div style="display: flex; justify-content: flex-end;">
          <button type="submit" class="ts-btn ts-btn-primary" style="font-weight: 700; padding: 0.75rem 1.5rem;">
            <i data-lucide="send" style="width: 16px; height: 16px; margin-right: 4px;"></i> Send Reply
          </button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
