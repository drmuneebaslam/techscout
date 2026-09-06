<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="max-width: 960px; margin: 0 auto;">
    
    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
          <span class="ts-badge ts-badge-cyan">COMMUNICATIONS HUB</span>
          <span style="font-size: 0.8rem; color: var(--ts-text-tertiary);">Direct Replies, Editorial Change Requests & Ticket Messages</span>
        </div>
        <h1 style="font-size: 2rem; margin: 0;">Unified Inbox & Messages</h1>
      </div>

      <div style="display: flex; gap: 0.75rem;">
        <a href="<?= url('/author/editor') ?>" class="ts-btn ts-btn-primary ts-btn-sm">
          <i data-lucide="plus-circle"></i> Write Article
        </a>
        <a href="<?= url('/support') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
          <i data-lucide="life-buoy"></i> Open Support Ticket
        </a>
      </div>
    </div>

    <?php
      $edCount = !empty($canAccessEditorial) ? count($editorialSubmissions) : 0;
      $ciCount = count($contactInquiries);
      $stCount = count($supportTickets);
      $appCount = count($authorApps);
      $totalCount = $totalCommunicationsCount ?? ($edCount + $ciCount + $stCount + $appCount);
    ?>

    <!-- Navigation Tabs Ribbon (Role-Filtered) -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 2rem; flex-wrap: wrap;">
      <button type="button" onclick="switchInboxTab('all')" class="ts-btn ts-btn-sm ts-btn-primary inbox-tab-btn" id="tab-btn-all">
        All Communications (<?= $totalCount ?>)
      </button>
      <?php if (!empty($canAccessEditorial)): ?>
        <button type="button" onclick="switchInboxTab('editorial')" class="ts-btn ts-btn-sm ts-btn-outline inbox-tab-btn" id="tab-btn-editorial">
          📋 Editorial Reviews (<?= $edCount ?>)
        </button>
      <?php endif; ?>
      <button type="button" onclick="switchInboxTab('contact')" class="ts-btn ts-btn-sm ts-btn-outline inbox-tab-btn" id="tab-btn-contact">
        💬 Contact Inquiries & Replies (<?= $ciCount ?>)
      </button>
      <button type="button" onclick="switchInboxTab('support')" class="ts-btn ts-btn-sm ts-btn-outline inbox-tab-btn" id="tab-btn-support">
        🎫 Support Tickets (<?= $stCount ?>)
      </button>
      <button type="button" onclick="switchInboxTab('applications')" class="ts-btn ts-btn-sm ts-btn-outline inbox-tab-btn" id="tab-btn-applications">
        ✍️ Author Applications (<?= $appCount ?>)
      </button>
    </div>

    <?php if (!empty($canAccessEditorial)): ?>
    <!-- Section 1: Editorial Feedback & Change Requests (Authors & Staff) -->
    <div class="inbox-section" id="inbox-section-editorial" style="margin-bottom: 2.5rem;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid var(--ts-border-main); padding-bottom: 0.5rem;">
        <h3 style="font-size: 1.15rem; margin: 0; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="edit-3" style="color: #38bdf8; width: 18px; height: 18px;"></i> Editorial Reviews & Author Revision Notes
        </h3>
        <span style="font-size: 0.8rem; color: #94a3b8;"><?= $edCount ?> Submissions</span>
      </div>

      <?php if (!empty($editorialSubmissions)): ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <?php foreach ($editorialSubmissions as $sub): ?>
            <div class="ts-card" style="padding: 1.5rem; background: rgba(13,19,34,0.85); border-color: <?= !empty($sub['editorial_notes']) ? 'rgba(245,158,11,0.4)' : 'rgba(99,102,241,0.2)' ?>;">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                  <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <span class="ts-badge <?= $sub['status'] === 'in_review' ? 'ts-badge-pink' : 'strike-badge' ?>">
                      <?= strtoupper(str_replace('_', ' ', $sub['status'])) ?>
                    </span>
                    <span style="font-size: 0.78rem; color: #64748b;">Updated: <?= \App\Core\DateHelper::format($sub['updated_at']) ?></span>
                  </div>
                  <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0.2rem 0;">
                    <?= htmlspecialchars($sub['title']) ?>
                  </h3>
                </div>

                <a href="<?= url('/author/editor/' . $sub['id']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">
                  <i data-lucide="edit"></i> Open in Editor & Fix ✏️
                </a>
              </div>

              <?php if (!empty($sub['editorial_notes'])): ?>
                <div style="background: rgba(245,158,11,0.1); border-left: 3px solid #f59e0b; padding: 0.85rem 1rem; border-radius: 0 6px 6px 0; margin-top: 0.75rem;">
                  <div style="font-size: 0.78rem; font-weight: 700; color: #fbbf24; text-transform: uppercase; margin-bottom: 0.25rem;">
                    📋 Editorial Board Feedback:
                  </div>
                  <div style="font-size: 0.9rem; color: #f8fafc; line-height: 1.5;">
                    <?= nl2br(htmlspecialchars($sub['editorial_notes'])) ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="ts-card" style="text-align: center; padding: 2rem; color: #64748b;">
          No active editorial reviews. When you submit technical articles, revision requests from the editorial board will appear here.
        </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Section 2: Contact Form Inquiries & Admin Replies -->
    <div class="inbox-section" id="inbox-section-contact" style="margin-bottom: 2.5rem;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid var(--ts-border-main); padding-bottom: 0.5rem;">
        <h3 style="font-size: 1.15rem; margin: 0; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="mail" style="color: #ec4899; width: 18px; height: 18px;"></i> Contact Form Inquiries & Staff Replies
        </h3>
        <span style="font-size: 0.8rem; color: #94a3b8;"><?= $ciCount ?> Inquiries</span>
      </div>

      <?php if (!empty($contactInquiries)): ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <?php foreach ($contactInquiries as $msg): ?>
            <?php $isReplied = $msg['status'] === 'replied'; ?>
            <div class="ts-card" style="padding: 1.5rem; background: rgba(13,19,34,0.85); border-color: <?= $isReplied ? 'rgba(16,185,129,0.35)' : 'rgba(148,163,184,0.15)' ?>;">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <div>
                  <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <span class="ts-badge <?= $isReplied ? 'ts-badge-green' : 'ts-badge-cyan' ?>">
                      <?= $isReplied ? 'STAFF REPLIED' : 'SENT / PENDING' ?>
                    </span>
                    <span style="font-size: 0.78rem; color: #64748b;"><?= \App\Core\DateHelper::format($msg['created_at']) ?></span>
                  </div>
                  <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0.2rem 0;">
                    <?= htmlspecialchars($msg['subject']) ?>
                  </h4>
                </div>
              </div>

              <!-- Your Inquiry -->
              <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.1); border-radius: 6px; padding: 0.85rem 1rem; margin-bottom: 0.75rem;">
                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem;">Your Inquiry:</div>
                <div style="color: #cbd5e1; font-size: 0.9rem; line-height: 1.5;"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
              </div>

              <!-- Staff Reply -->
              <?php if ($isReplied && !empty($msg['reply_text'])): ?>
                <div style="background: rgba(16,185,129,0.08); border-left: 3px solid #10b981; border-radius: 0 6px 6px 0; padding: 0.85rem 1rem;">
                  <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #34d399; font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem;">
                    <span>Official Reply from Staff (<?= htmlspecialchars($msg['replier_name'] ?? 'Admin') ?>):</span>
                    <span><?= !empty($msg['replied_at']) ? \App\Core\DateHelper::format($msg['replied_at']) : '' ?></span>
                  </div>
                  <div style="color: #f8fafc; font-size: 0.92rem; line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($msg['reply_text'])) ?>
                  </div>
                </div>
              <?php else: ?>
                <div style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">
                  Our operations team will review your inquiry and reply here shortly.
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="ts-card" style="text-align: center; padding: 2rem; color: #64748b;">
          No contact inquiries submitted. You can send questions directly through the <a href="<?= url('/contact') ?>" style="color: #38bdf8;">Contact Form</a>.
        </div>
      <?php endif; ?>
    </div>

    <!-- Section 3: Support Desk Tickets -->
    <div class="inbox-section" id="inbox-section-support" style="margin-bottom: 2.5rem;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid var(--ts-border-main); padding-bottom: 0.5rem;">
        <h3 style="font-size: 1.15rem; margin: 0; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="life-buoy" style="color: #a855f7; width: 18px; height: 18px;"></i> Support Desk Tickets & Discussions
        </h3>
        <span style="font-size: 0.8rem; color: #94a3b8;"><?= $stCount ?> Tickets</span>
      </div>

      <?php if (!empty($supportTickets)): ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <?php foreach ($supportTickets as $ticket): ?>
            <div class="ts-card" style="padding: 1.25rem 1.5rem; background: rgba(13,19,34,0.85); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
              <div>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                  <span class="ts-badge ts-badge-cyan" style="font-family: var(--ts-font-mono); font-size: 0.72rem;"><?= htmlspecialchars($ticket['ticket_number']) ?></span>
                  <span class="ts-badge <?= $ticket['status'] === 'resolved' ? 'ts-badge-green' : 'ts-badge-pink' ?>">
                    <?= strtoupper(str_replace('_', ' ', $ticket['status'])) ?>
                  </span>
                  <span style="font-size: 0.75rem; color: #64748b;"><?= \App\Core\DateHelper::format($ticket['last_reply_at']) ?></span>
                </div>
                <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0.2rem 0;">
                  <?= htmlspecialchars($ticket['subject']) ?>
                </h4>
                <?php if (!empty($ticket['latest_message'])): ?>
                  <div style="font-size: 0.82rem; color: #94a3b8; margin-top: 0.25rem;">
                    Latest message: <?= htmlspecialchars(mb_strimwidth($ticket['latest_message'], 0, 80, '...')) ?>
                  </div>
                <?php endif; ?>
              </div>

              <a href="<?= url('/support/ticket/' . $ticket['ticket_number']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-weight: 600;">
                Open Conversation Thread ↗
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="ts-card" style="text-align: center; padding: 2rem; color: #64748b;">
          No support tickets open. Need technical assistance? <a href="<?= url('/support') ?>" style="color: #38bdf8;">Submit a Support Ticket</a>.
        </div>
      <?php endif; ?>
    </div>

    <!-- Section 4: Author Applications -->
    <div class="inbox-section" id="inbox-section-applications" style="margin-bottom: 2.5rem;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid var(--ts-border-main); padding-bottom: 0.5rem;">
        <h3 style="font-size: 1.15rem; margin: 0; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="award" style="color: #10b981; width: 18px; height: 18px;"></i> Author Applications & Creator Approvals
        </h3>
        <span style="font-size: 0.8rem; color: #94a3b8;"><?= $appCount ?> Applications</span>
      </div>

      <?php if (!empty($authorApps)): ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <?php foreach ($authorApps as $app): ?>
            <div class="ts-card" style="padding: 1.5rem; background: rgba(13,19,34,0.85);">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <div>
                  <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <span class="ts-badge <?= $app['status'] === 'approved' ? 'ts-badge-green' : ($app['status'] === 'pending' ? 'ts-badge-pink' : 'strike-badge') ?>">
                      <?= strtoupper($app['status']) ?>
                    </span>
                    <span style="font-size: 0.78rem; color: #64748b;">Submitted: <?= \App\Core\DateHelper::format($app['created_at']) ?></span>
                  </div>
                  <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0.2rem 0;">
                    Author Application: <?= htmlspecialchars($app['headline'] ?? 'Technical Contributor') ?>
                  </h4>
                </div>
              </div>

              <?php if (!empty($app['admin_feedback'])): ?>
                <div style="background: rgba(99,102,241,0.1); border-left: 3px solid #6366f1; padding: 0.85rem 1rem; border-radius: 0 6px 6px 0; margin-top: 0.5rem;">
                  <div style="font-size: 0.75rem; font-weight: 700; color: #818cf8; text-transform: uppercase; margin-bottom: 0.25rem;">Review Verdict & Feedback:</div>
                  <div style="color: #f8fafc; font-size: 0.9rem; line-height: 1.5;"><?= nl2br(htmlspecialchars($app['admin_feedback'])) ?></div>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="ts-card" style="text-align: center; padding: 2rem; color: #64748b;">
          No author applications found. Interested in publishing? <a href="<?= url('/author/apply') ?>" style="color: #38bdf8;">Apply to Become an Author</a>.
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<script>
function switchInboxTab(tab) {
  document.querySelectorAll('.inbox-tab-btn').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById(`tab-btn-${tab}`);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  const sections = ['editorial', 'contact', 'support', 'applications'];
  sections.forEach(s => {
    const el = document.getElementById(`inbox-section-${s}`);
    if (!el) return;
    if (tab === 'all' || tab === s) {
      el.style.display = 'block';
    } else {
      el.style.display = 'none';
    }
  });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
