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
              <div style="display: flex; gap: 0.4rem;">
                <button type="button" onclick='viewMessage(<?= json_encode($msg, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                  <i data-lucide="eye"></i> Inspect
                </button>
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

<!-- Message Modal -->
<div id="messageModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 680px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <h2 id="msgModalSubject" style="font-size: 1.25rem; color: #f8fafc; margin: 0;">Message Subject</h2>
      <button onclick="document.getElementById('messageModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.88rem;">
      <div><strong style="color: #64748b;">From:</strong> <span id="msgModalSender" style="color: #f8fafc; font-weight: 600;"></span> (<span id="msgModalEmail" style="color: #38bdf8;"></span>)</div>
      <div style="margin-top: 0.35rem;"><strong style="color: #64748b;">Date:</strong> <span id="msgModalDate" style="color: #cbd5e1; font-family: monospace;"></span></div>
    </div>

    <div style="margin-bottom: 1.5rem;">
      <h4 style="color: #f8fafc; font-size: 0.9rem; margin: 0 0 0.5rem 0;">Message Content:</h4>
      <div id="msgModalBody" style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 6px; padding: 1rem; color: #cbd5e1; line-height: 1.6; font-size: 0.9rem; white-space: pre-wrap;"></div>
    </div>

    <div id="existingReplyBox" style="display: none; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
      <div style="color: #34d399; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">Previous Reply Recorded:</div>
      <div id="existingReplyText" style="color: #f8fafc; font-size: 0.88rem; line-height: 1.5;"></div>
    </div>

    <form id="msgReplyForm" method="POST" action="">
      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Record Official Response / Editorial Reply</label>
        <textarea name="reply_text" id="msgReplyText" rows="3" placeholder="Enter reply message or internal resolution details..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem; border-radius: 6px; color: #f8fafc; font-size: 0.88rem;" required></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('messageModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm">Close</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">Save & Mark as Replied ✉️</button>
      </div>
    </form>
  </div>
</div>

<script>
function viewMessage(msg) {
  document.getElementById('msgModalSubject').innerText = msg.subject || 'Inquiry';
  document.getElementById('msgModalSender').innerText = msg.name || 'Anonymous';
  document.getElementById('msgModalEmail').innerText = msg.email || '';
  document.getElementById('msgModalDate').innerText = msg.created_at || '';
  document.getElementById('msgModalBody').innerText = msg.message || '';

  const replyWrap = document.getElementById('existingReplyBox');
  if (msg.reply_text) {
    replyWrap.style.display = 'block';
    document.getElementById('existingReplyText').innerText = msg.reply_text;
  } else {
    replyWrap.style.display = 'none';
  }

  document.getElementById('msgReplyForm').action = '<?= url('/admin/contact-messages/reply/') ?>' + msg.id;
  document.getElementById('messageModal').style.display = 'flex';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
