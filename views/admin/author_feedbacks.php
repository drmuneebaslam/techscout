<?php
use App\Core\Config;
$title = "Author Platform Feedback | Enterprise Admin | " . Config::get('site_name', 'TechScout');
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="admin-header-strip">
  <div class="admin-header-strip-left">
    <div class="admin-header-strip-icon"><i data-lucide="message-square"></i></div>
    <div class="admin-header-strip-text">
      <h1>Author Feedback & Suggestions</h1>
      <p>Review direct platform feedback and feature requests from creators.</p>
    </div>
  </div>
</div>

<div class="admin-content-inner">
  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h3 style="font-size: 1.15rem; color: var(--admin-text-primary); margin: 0;">Incoming Author Feedback</h3>
      <span class="admin-pill-badge badge-cyan"><?= count($feedbacks ?? []) ?> Total Records</span>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Author Name</th>
          <th>Subject</th>
          <th>Status</th>
          <th>Submitted At</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($feedbacks)): ?>
          <?php foreach ($feedbacks as $fb): ?>
            <tr>
              <td><?= $fb['id'] ?></td>
              <td>
                <strong style="color: var(--admin-text-primary);"><?= htmlspecialchars($fb['author_name'] ?? 'Unknown') ?></strong>
                <div style="font-size: 0.75rem; color: var(--admin-text-secondary);">@<?= htmlspecialchars($fb['author_username'] ?? '') ?></div>
              </td>
              <td>
                <strong style="color: #f8fafc; font-size: 0.9rem;"><?= htmlspecialchars($fb['subject']) ?></strong>
                <div style="font-size: 0.8rem; color: #94a3b8; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  <?= htmlspecialchars($fb['message']) ?>
                </div>
              </td>
              <td>
                <?php
                  $badge = 'badge-warning';
                  if ($fb['status'] === 'reviewed') $badge = 'badge-cyan';
                  if ($fb['status'] === 'resolved') $badge = 'badge-success';
                ?>
                <span class="admin-pill-badge <?= $badge ?>"><?= ucfirst($fb['status']) ?></span>
              </td>
              <td style="font-size: 0.8rem; color: var(--admin-text-secondary);">
                <?= \App\Core\DateHelper::formatDateTime($fb['created_at']) ?>
              </td>
              <td style="text-align: right;">
                <button type="button" class="admin-btn admin-btn-outline admin-btn-sm" onclick="document.getElementById('fbModal_<?= $fb['id'] ?>').style.display='flex'">
                  <i data-lucide="eye" style="width: 14px;"></i> View
                </button>
                
                <!-- Feedback Modal -->
                <div id="fbModal_<?= $fb['id'] ?>" class="admin-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 10000; align-items: center; justify-content: center; text-align: left;">
                  <div class="admin-modal-content" style="background: var(--admin-bg-elevated); border: 1px solid var(--admin-border-color); border-radius: 12px; width: 100%; max-width: 600px; padding: 2rem; position: relative;">
                    <button type="button" onclick="this.closest('.admin-modal').style.display='none'" style="position: absolute; top: 1rem; right: 1rem; background: transparent; border: none; color: var(--admin-text-secondary); cursor: pointer;"><i data-lucide="x"></i></button>
                    
                    <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--admin-text-primary); font-size: 1.25rem;">Author Feedback #<?= $fb['id'] ?></h3>
                    
                    <div style="background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; color: var(--admin-text-secondary);">
                      <div style="margin-bottom: 0.75rem;"><strong>Author:</strong> <span style="color: var(--admin-text-primary);"><?= htmlspecialchars($fb['author_name'] ?? '') ?> (@<?= htmlspecialchars($fb['author_username'] ?? '') ?>)</span></div>
                      <div style="margin-bottom: 0.75rem;"><strong>Subject:</strong> <span style="color: var(--admin-text-primary);"><?= htmlspecialchars($fb['subject']) ?></span></div>
                      <div style="margin-bottom: 0.75rem;"><strong>Message:</strong><br><div style="background: #0f172a; padding: 1rem; border-radius: 6px; margin-top: 0.5rem; color: #cbd5e1; white-space: pre-wrap;"><?= htmlspecialchars($fb['message']) ?></div></div>
                      <div style="margin-bottom: 0.75rem;"><strong>Submitted:</strong> <?= \App\Core\DateHelper::formatDateTime($fb['created_at']) ?></div>
                    </div>
                    
                    <form action="<?= url('/admin/author-feedbacks/update/' . $fb['id']) ?>" method="POST">
                      <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: var(--admin-text-secondary); font-size: 0.85rem; margin-bottom: 0.4rem;">Update Status</label>
                        <select name="status" class="admin-input">
                          <option value="pending" <?= $fb['status']==='pending'?'selected':'' ?>>Pending</option>
                          <option value="reviewed" <?= $fb['status']==='reviewed'?'selected':'' ?>>Reviewed</option>
                          <option value="resolved" <?= $fb['status']==='resolved'?'selected':'' ?>>Resolved</option>
                        </select>
                      </div>
                      <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: var(--admin-text-secondary); font-size: 0.85rem; margin-bottom: 0.4rem;">Admin Internal Notes</label>
                        <textarea name="admin_notes" rows="3" class="admin-input"><?= htmlspecialchars($fb['admin_notes'] ?? '') ?></textarea>
                      </div>
                      <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <button type="submit" class="admin-btn admin-btn-primary">
                          <i data-lucide="save" style="width: 16px; margin-right: 6px;"></i> Save Changes
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--admin-text-secondary);">No author feedback received yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        lucide.createIcons();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>
