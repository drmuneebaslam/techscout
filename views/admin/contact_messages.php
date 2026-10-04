<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0 0 0.25rem 0;">Inbox & Contact Inquiries</h1>
    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Review partnerships, ad inquiries, and reader feedback with reply tracking.</p>
  </div>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Sender & Email</th>
        <th>Subject</th>
        <th>Message Snippet</th>
        <th>Status</th>
        <th>Received</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $msg): ?>
          <?php 
            $isUnread = ($msg['status'] === 'unread');
            $isReplied = ($msg['status'] === 'replied');
          ?>
          <tr style="<?= $isUnread ? 'background: rgba(99,102,241,0.06); font-weight: 600;' : '' ?>">
            <td>
              <div style="color: #f8fafc; font-weight: 700;"><?= htmlspecialchars($msg['name']) ?></div>
              <div style="font-size: 0.8rem; color: #38bdf8;"><?= htmlspecialchars($msg['email']) ?></div>
            </td>
            <td style="color: #f8fafc; max-width: 180px;"><?= htmlspecialchars($msg['subject']) ?></td>
            <td style="color: #94a3b8; font-size: 0.85rem; max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
              <?= htmlspecialchars($msg['message']) ?>
            </td>
            <td>
              <?php if ($isUnread): ?>
                <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem;">NEW / UNREAD</span>
              <?php elseif ($isReplied): ?>
                <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">REPLIED ✅</span>
              <?php else: ?>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;">READ</span>
              <?php endif; ?>
            </td>
            <td style="font-size: 0.8rem; color: #64748b; font-family: monospace;">
              <?= \App\Core\DateHelper::format($msg['created_at']) ?>
            </td>
            <td>
              <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.4rem;">
                <a href="<?= url('/admin/contact-messages/view/' . $msg['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                  <i data-lucide="eye"></i> Inspect
                </a>
                <form action="<?= url('/admin/contact-messages/delete/' . $msg['id']) ?>" method="POST" onsubmit="return confirm('Delete this inquiry?');">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #ef4444;">
                    <i data-lucide="trash-2"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
            <i data-lucide="inbox" style="width: 36px; height: 36px; color: var(--ts-primary); margin-bottom: 0.5rem;"></i>
            <div>No contact messages in inbox.</div>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
