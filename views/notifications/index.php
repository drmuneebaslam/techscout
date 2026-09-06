<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
/* ========================================================================= */
/* NOTIFICATION COMMAND CENTER: FULL MOBILE RESPONSIVENESS & TOUCH ADAPTATION */
/* ========================================================================= */
.ts-notif-container {
  padding-top: 2.5rem;
  margin-bottom: 5rem;
  max-width: 1040px;
}

.ts-notification-hero {
  border-radius: 20px;
  padding: 2rem 2.25rem;
  margin-bottom: 2rem;
}

.ts-hero-title {
  font-size: 2.3rem;
  margin: 0;
  font-family: 'Outfit', sans-serif;
  font-weight: 800;
  letter-spacing: -0.5px;
}

.ts-kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 0.85rem;
  margin-top: 1.75rem;
}

.ts-toolbar-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.25rem;
  flex-wrap: wrap;
}

.ts-notif-filter-strip {
  display: flex;
  gap: 0.45rem;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  padding-bottom: 0.35rem;
  scrollbar-width: none;
  flex-wrap: nowrap;
}
.ts-notif-filter-strip::-webkit-scrollbar {
  display: none;
}

.ts-notif-search-wrap {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  min-width: 260px;
}

.notif-item-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--ts-border-subtle);
  cursor: pointer;
  transition: all 0.2s ease;
  gap: 1rem;
}

.notif-item-left {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  flex: 1;
  min-width: 0;
}

.notif-item-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: var(--ts-bg-surface);
  border: 1px solid var(--ts-border-main);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.notif-item-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-shrink: 0;
}

/* Tablet & Mobile Media Queries */
@media (max-width: 768px) {
  .ts-notif-container {
    padding-top: 1.25rem !important;
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
    margin-bottom: 3.5rem !important;
  }
  .ts-notification-hero {
    padding: 1.25rem 1rem !important;
    border-radius: 16px !important;
    margin-bottom: 1.25rem !important;
  }
  .ts-hero-top-row {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 1rem !important;
  }
  .ts-hero-title {
    font-size: 1.6rem !important;
    line-height: 1.25 !important;
  }
  .ts-hero-actions-bar {
    width: 100% !important;
    display: flex !important;
    gap: 0.5rem !important;
  }
  .ts-hero-actions-bar button {
    flex: 1 !important;
    justify-content: center !important;
    font-size: 0.78rem !important;
    padding: 0.5rem 0.4rem !important;
    text-align: center !important;
  }
  .ts-kpi-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 0.6rem !important;
    margin-top: 1.25rem !important;
  }
  .ts-toolbar-row {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 0.75rem !important;
  }
  .ts-notif-filter-strip {
    width: 100% !important;
    padding-bottom: 0.5rem !important;
  }
  .ts-notif-search-wrap {
    width: 100% !important;
    min-width: 0 !important;
  }
  .notif-item-row {
    flex-direction: column !important;
    align-items: stretch !important;
    padding: 1rem 0.85rem !important;
    gap: 0.75rem !important;
  }
  .notif-item-left {
    gap: 0.75rem !important;
  }
  .notif-item-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 10px !important;
  }
  .notif-item-icon i,
  .notif-item-icon svg {
    width: 18px !important;
    height: 18px !important;
  }
  .notif-item-controls {
    width: 100% !important;
    display: flex !important;
    justify-content: flex-end !important;
    align-items: center !important;
    padding-top: 0.6rem !important;
    border-top: 1px solid var(--ts-border-subtle, rgba(148,163,184,0.08)) !important;
    gap: 0.5rem !important;
  }
  .notif-item-controls a,
  .notif-item-controls button {
    padding: 0.45rem 0.75rem !important;
    min-height: 36px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  #notifDetailModal {
    padding: 0.65rem !important;
  }
  #notifDetailModal .ts-card {
    padding: 1.25rem 1rem !important;
    border-radius: 14px !important;
    max-height: 92vh !important;
    overflow-y: auto !important;
  }
  #batch-action-bar {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 0.6rem !important;
    padding: 0.65rem 0.85rem !important;
  }
  #batch-action-bar > div:last-child {
    display: flex !important;
    gap: 0.4rem !important;
  }
  #batch-action-bar button {
    flex: 1 !important;
    justify-content: center !important;
  }
  .notif-pagination-row {
    flex-direction: column !important;
    align-items: center !important;
    gap: 0.75rem !important;
    padding: 1rem !important;
  }
}

@media (max-width: 480px) {
  .ts-hero-title {
    font-size: 1.35rem !important;
  }
  .ts-kpi-grid {
    grid-template-columns: 1fr 1fr !important;
  }
}
</style>

<div class="container ts-notif-container">
  
  <!-- Adaptable Light/Dark Hero Header -->
  <div class="ts-card ts-notification-hero">
    <div class="ts-hero-top-row" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.4rem;">
          <span class="ts-badge ts-badge-pink" style="font-size: 0.72rem; letter-spacing: 0.8px; font-weight: 800;">INTELLIGENCE DISPATCH</span>
          <span id="unread-status-badge" class="ts-badge ts-badge-cyan" style="font-size: 0.72rem; font-weight: 700;">
            <?= (int)$unreadCount ?> Unread Alerts
          </span>
        </div>
        <h1 class="ts-hero-title">
          Notification Command Center
        </h1>
        <p style="font-size: 0.95rem; margin: 0.4rem 0 0 0; opacity: 0.85;">
          Real-time activity telemetry, review verdicts, community replies, support tickets, and revenue milestones.
        </p>
      </div>

      <!-- Quick Global Actions -->
      <div class="ts-hero-actions-bar" style="display: flex; gap: 0.6rem; align-items: center; flex-wrap: wrap;">
        <button type="button" onclick="markAllNotificationsRead()" id="btn-mark-all-read" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.4); font-weight: 700;">
          <i data-lucide="check-check"></i> Mark All as Read
        </button>
        
        <?php if (!empty($notifications)): ?>
          <button type="button" onclick="confirmClearAllNotifications()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);" title="Clear current filter notifications">
            <i data-lucide="trash-2"></i> Clear Stream
          </button>
        <?php endif; ?>
      </div>
    </div>

    <!-- Live Telemetry KPI Tiles -->
    <div class="ts-kpi-grid">
      <div class="ts-telemetry-tile">
        <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Total Stream</div>
        <div class="ts-telemetry-tile-val" style="font-size: 1.4rem; font-weight: 800; font-family: 'JetBrains Mono', monospace;"><?= (int)($counts['all'] ?? $total) ?></div>
      </div>
      <div class="ts-telemetry-tile" style="border-color: rgba(236,72,153,0.3);">
        <div style="font-size: 0.72rem; color: #f472b6; font-weight: 700; text-transform: uppercase;">Unread Active</div>
        <div id="kpi-unread-val" style="font-size: 1.4rem; font-weight: 800; color: #ec4899; font-family: 'JetBrains Mono', monospace;"><?= (int)($counts['unread'] ?? $unreadCount) ?></div>
      </div>
      <div class="ts-telemetry-tile" style="border-color: rgba(56,189,248,0.3);">
        <div style="font-size: 0.72rem; color: #38bdf8; font-weight: 700; text-transform: uppercase;">Replies &amp; Chats</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #38bdf8; font-family: 'JetBrains Mono', monospace;"><?= (int)($counts['replies'] ?? 0) ?></div>
      </div>
      <div class="ts-telemetry-tile" style="border-color: rgba(16,185,129,0.3);">
        <div style="font-size: 0.72rem; color: #34d399; font-weight: 700; text-transform: uppercase;">Rewards &amp; Revenue</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #10b981; font-family: 'JetBrains Mono', monospace;"><?= (int)($counts['rewards'] ?? 0) ?></div>
      </div>
      <div class="ts-telemetry-tile" style="border-color: rgba(245,158,11,0.3);">
        <div style="font-size: 0.72rem; color: #fbbf24; font-weight: 700; text-transform: uppercase;">Tickets &amp; Support</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #f59e0b; font-family: 'JetBrains Mono', monospace;"><?= (int)($counts['tickets'] ?? 0) ?></div>
      </div>
    </div>
  </div>

  <!-- Filter Strip & Instant Search Toolbar -->
  <div class="ts-toolbar-row">
    
    <!-- Filter Navigation Tabs with Live Badges (Smooth Horizontal Swipe on Mobile) -->
    <div class="ts-notif-filter-strip">
      <a href="<?= url('/notifications') ?>" class="ts-btn ts-btn-sm <?= empty($currentFilter) || $currentFilter === 'all' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.82rem; white-space: nowrap; border-radius: 8px;">
        All <span class="ts-badge" style="font-size: 0.68rem; margin-left: 0.35rem;"><?= (int)($counts['all'] ?? $total) ?></span>
      </a>
      <a href="<?= url('/notifications?filter=unread') ?>" class="ts-btn ts-btn-sm <?= ($currentFilter ?? '') === 'unread' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.82rem; white-space: nowrap; border-radius: 8px;">
        Unread <span id="chip-unread-count" class="ts-badge ts-badge-pink" style="font-size: 0.68rem; margin-left: 0.35rem;"><?= (int)($counts['unread'] ?? $unreadCount) ?></span>
      </a>
      <a href="<?= url('/notifications?filter=replies') ?>" class="ts-btn ts-btn-sm <?= ($currentFilter ?? '') === 'replies' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.82rem; white-space: nowrap; border-radius: 8px;">
        Replies &amp; Comments <span class="ts-badge" style="font-size: 0.68rem; margin-left: 0.35rem;"><?= (int)($counts['replies'] ?? 0) ?></span>
      </a>
      <a href="<?= url('/notifications?filter=editorial') ?>" class="ts-btn ts-btn-sm <?= ($currentFilter ?? '') === 'editorial' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.82rem; white-space: nowrap; border-radius: 8px;">
        Editorial &amp; Review <span class="ts-badge" style="font-size: 0.68rem; margin-left: 0.35rem;"><?= (int)($counts['editorial'] ?? 0) ?></span>
      </a>
      <a href="<?= url('/notifications?filter=system') ?>" class="ts-btn ts-btn-sm <?= ($currentFilter ?? '') === 'system' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.82rem; white-space: nowrap; border-radius: 8px;">
        System &amp; Security <span class="ts-badge" style="font-size: 0.68rem; margin-left: 0.35rem;"><?= (int)($counts['system'] ?? 0) ?></span>
      </a>
      <a href="<?= url('/notifications?filter=rewards') ?>" class="ts-btn ts-btn-sm <?= ($currentFilter ?? '') === 'rewards' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.82rem; white-space: nowrap; border-radius: 8px;">
        Rewards &amp; Follows <span class="ts-badge" style="font-size: 0.68rem; margin-left: 0.35rem;"><?= (int)($counts['rewards'] ?? 0) ?></span>
      </a>
    </div>

    <!-- Quick Live Filter Search Box & Per-Page Controls -->
    <div class="ts-notif-search-wrap">
      <div style="position: relative; flex: 1;">
        <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--ts-text-tertiary);"></i>
        <input type="text" id="notif-search-filter" oninput="filterNotificationsLive(this.value)" placeholder="Search in notifications..." style="width: 100%; padding: 0.45rem 0.8rem 0.45rem 2rem; font-size: 0.82rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); border-radius: 8px; color: var(--ts-text-primary); outline: none;">
      </div>

      <select onchange="changePerPage(this.value)" style="padding: 0.45rem 0.65rem; font-size: 0.8rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); border-radius: 8px; color: var(--ts-text-primary); outline: none; cursor: pointer;" title="Items per page">
        <option value="15" <?= ($perPage ?? 15) == 15 ? 'selected' : '' ?>>15 / pg</option>
        <option value="25" <?= ($perPage ?? 15) == 25 ? 'selected' : '' ?>>25 / pg</option>
        <option value="50" <?= ($perPage ?? 15) == 50 ? 'selected' : '' ?>>50 / pg</option>
        <option value="100" <?= ($perPage ?? 15) == 100 ? 'selected' : '' ?>>100 / pg</option>
      </select>
    </div>
  </div>

  <!-- Batch Action Toolbar (Reveals when items selected) -->
  <div id="batch-action-bar" style="display: none; align-items: center; justify-content: space-between; background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.35); border-radius: 12px; padding: 0.65rem 1.25rem; margin-bottom: 1rem; backdrop-filter: blur(12px);">
    <div style="font-size: 0.85rem; color: var(--ts-text-primary); font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
      <span id="batch-selected-count">0</span> items selected
    </div>
    <div style="display: flex; gap: 0.5rem;">
      <button type="button" onclick="batchMarkSelectedRead()" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.78rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        <i data-lucide="check"></i> Mark Selected Read
      </button>
      <button type="button" onclick="batchDeleteSelected()" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.78rem; color: #ef4444; border-color: rgba(239,68,68,0.3);">
        <i data-lucide="trash-2"></i> Delete Selected
      </button>
    </div>
  </div>

  <!-- Notifications List Card with Glassmorphism Surface -->
  <div class="ts-card ts-glassmorphic-card" style="padding: 0; overflow: hidden; border-radius: 16px; border: 1px solid var(--ts-border-main);">
    
    <!-- Top Table Options Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--ts-border-subtle); background: var(--ts-bg-surface);">
      <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: var(--ts-text-secondary); cursor: pointer; user-select: none;">
        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this.checked)" style="accent-color: var(--ts-primary); width: 15px; height: 15px;">
        <span>Select All on Page</span>
      </label>
      <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); font-family: 'JetBrains Mono', monospace;">
        Showing <?= count($notifications ?? []) ?> of <?= number_format($total ?? 0) ?>
      </span>
    </div>

    <?php if (!empty($notifications)): ?>
      <div style="display: flex; flex-direction: column;" id="notif-list-wrapper">
        <?php foreach ($notifications as $n): ?>
          <?php 
            $isUnread = (int)$n['is_read'] === 0; 
            $rawUrl = $n['action_url'] ?? '';
            $fullUrl = '';
            if (!empty($rawUrl)) {
              $cleanedUrl = preg_replace('#^https?://[^/]+/(techscout\.n|tech)(/|$)#i', '/', $rawUrl);
              $cleanedUrl = preg_replace('#^/(techscout\.n|tech)(/|$)#i', '/', $cleanedUrl);
              if (empty($cleanedUrl)) $cleanedUrl = '/';
              $fullUrl = (str_starts_with($cleanedUrl, 'http://') || str_starts_with($cleanedUrl, 'https://')) ? $cleanedUrl : url($cleanedUrl);
            }

            $iconName = $n['icon'] ?? 'bell';
            $badgeClass = $n['badge_class'] ?? 'ts-badge-blue';
            $typeLabel = $n['type_label'] ?? 'SYSTEM';
            $actionBtnLabel = $n['action_label'] ?? 'View ↗';
            $iconColor = $n['color'] ?? '#38bdf8';
            $relTime = $n['relative_time'] ?? \App\Core\NotificationHelper::formatRelativeTime($n['created_at'] ?? '');
            $formattedExact = \App\Core\DateHelper::formatDateTime($n['created_at'] ?? '');
          ?>
          <div id="notif-card-<?= (int)$n['id'] ?>" 
               data-id="<?= (int)$n['id'] ?>"
               data-unread="<?= $isUnread ? '1' : '0' ?>"
               data-search="<?= htmlspecialchars(strtolower(($n['title'] ?? '') . ' ' . ($n['message'] ?? '') . ' ' . ($typeLabel ?? ''))) ?>"
               onclick="openNotificationViewer(<?= (int)$n['id'] ?>, '<?= htmlspecialchars(addslashes($n['title']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($typeLabel), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($n['message']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($fullUrl), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($formattedExact), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($actionBtnLabel), ENT_QUOTES) ?>')" 
               style="background: <?= $isUnread ? 'rgba(56,189,248,0.05)' : 'transparent' ?>; border-left: 4px solid <?= $isUnread ? '#38bdf8' : 'transparent' ?>;" 
               class="notif-item-row">
            
            <div class="notif-item-left">
              <!-- Selection Checkbox -->
              <div onclick="event.stopPropagation();" style="margin-top: 10px;">
                <input type="checkbox" class="notif-select-box" value="<?= (int)$n['id'] ?>" onchange="updateBatchBar()" style="accent-color: var(--ts-primary); width: 16px; height: 16px; cursor: pointer;">
              </div>

              <!-- Notification Icon with Dynamic Gradient -->
              <div class="notif-item-icon" style="color: <?= $iconColor ?>;">
                <i data-lucide="<?= $iconName ?>" style="width: 20px; height: 20px;"></i>
              </div>

              <!-- Notification Body Details -->
              <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                  <span class="ts-badge <?= $badgeClass ?>" style="font-size: 0.65rem; padding: 0.1rem 0.5rem; font-weight: 800; letter-spacing: 0.5px;">
                    <?= htmlspecialchars($typeLabel) ?>
                  </span>
                  <strong id="notif-title-<?= (int)$n['id'] ?>" style="color: var(--ts-text-primary); font-size: 0.98rem; font-weight: <?= $isUnread ? '700' : '600' ?>;">
                    <?= htmlspecialchars($n['title']) ?>
                  </strong>
                  <?php if ($isUnread): ?>
                    <span id="notif-pill-<?= (int)$n['id'] ?>" class="ts-badge ts-badge-pink" style="font-size: 0.62rem; padding: 0.1rem 0.4rem; font-weight: 800;">NEW</span>
                  <?php endif; ?>
                </div>
                
                <div style="color: var(--ts-text-secondary); font-size: 0.92rem; line-height: 1.5; margin-bottom: 0.35rem; word-break: break-word;">
                  <?= htmlspecialchars($n['message']) ?>
                </div>

                <div style="display: flex; align-items: center; gap: 1.25rem; font-size: 0.76rem; color: var(--ts-text-tertiary); flex-wrap: wrap;">
                  <span><i data-lucide="clock" style="width: 12px; height: 12px; vertical-align: -1px; display: inline-block;"></i> <?= $relTime ?></span>
                  <?php if (!empty($fullUrl)): ?>
                    <span style="color: #38bdf8; font-weight: 600;">
                      <i data-lucide="external-link" style="width: 12px; height: 12px; vertical-align: -1px; display: inline-block;"></i> <?= htmlspecialchars($actionBtnLabel) ?>
                    </span>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Action Controls Strip -->
            <div class="notif-item-controls" onclick="event.stopPropagation();">
              <?php if (!empty($fullUrl)): ?>
                <a href="<?= htmlspecialchars($fullUrl) ?>" onclick="markSingleRead(<?= (int)$n['id'] ?>);" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.4rem 0.9rem; font-size: 0.8rem; font-weight: 700; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.35rem; border-radius: 8px;">
                  <?= htmlspecialchars($actionBtnLabel) ?>
                </a>
              <?php endif; ?>

              <!-- Toggle Read/Unread Button -->
              <button type="button" onclick="toggleNotificationRead(<?= (int)$n['id'] ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.4rem 0.6rem; font-size: 0.75rem; border-radius: 8px; color: <?= $isUnread ? '#38bdf8' : '#94a3b8' ?>;" title="<?= $isUnread ? 'Mark as read' : 'Mark as unread' ?>" id="btn-toggle-read-<?= (int)$n['id'] ?>">
                <i data-lucide="<?= $isUnread ? 'check' : 'mail' ?>" style="width: 15px; height: 15px;"></i>
              </button>

              <!-- Delete Notification Button -->
              <button type="button" onclick="deleteNotificationItem(<?= (int)$n['id'] ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.4rem 0.6rem; font-size: 0.75rem; border-radius: 8px; color: #ef4444; border-color: rgba(239,68,68,0.25);" title="Dismiss notification">
                <i data-lucide="x" style="width: 15px; height: 15px;"></i>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Enhanced Pagination Strip -->
      <?php if (($totalPages ?? 1) > 1): ?>
        <div class="notif-pagination-row" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-top: 1px solid rgba(148,163,184,0.1); background: rgba(15,23,42,0.6); flex-wrap: wrap; gap: 1rem;">
          <div style="font-size: 0.85rem; color: #94a3b8;">
            Showing page <strong style="color: #f8fafc;"><?= $currentPage ?></strong> of <strong style="color: #f8fafc;"><?= $totalPages ?></strong> (<?= number_format($total) ?> total items)
          </div>

          <div style="display: flex; gap: 0.35rem; align-items: center;">
            <?php if ($currentPage > 1): ?>
              <a href="<?= url('/notifications?filter=' . urlencode($currentFilter) . '&page=' . ($currentPage - 1) . '&per_page=' . ($perPage ?? 15)) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.85rem; font-size: 0.82rem; border-radius: 6px;">
                &larr; Prev
              </a>
            <?php endif; ?>

            <?php 
              $startPage = max(1, $currentPage - 2);
              $endPage = min($totalPages, $currentPage + 2);
              for ($p = $startPage; $p <= $endPage; $p++): 
            ?>
              <a href="<?= url('/notifications?filter=' . urlencode($currentFilter) . '&page=' . $p . '&per_page=' . ($perPage ?? 15)) ?>" class="ts-btn ts-btn-sm <?= $p === $currentPage ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="padding: 0.35rem 0.75rem; font-size: 0.82rem; font-weight: 700; min-width: 34px; text-align: center; border-radius: 6px;">
                <?= $p ?>
              </a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
              <a href="<?= url('/notifications?filter=' . urlencode($currentFilter) . '&page=' . ($currentPage + 1) . '&per_page=' . ($perPage ?? 15)) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.85rem; font-size: 0.82rem; border-radius: 6px;">
                Next &rarr;
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

    <?php else: ?>
      <div style="text-align: center; padding: 5rem 2rem; color: #94a3b8;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.25); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; color: #818cf8;">
          <i data-lucide="bell-off" style="width: 32px; height: 32px;"></i>
        </div>
        <h3 style="color: #f8fafc; margin-bottom: 0.4rem; font-family: 'Outfit', sans-serif; font-size: 1.45rem; font-weight: 800;">
          No Notifications Found
        </h3>
        <p style="font-size: 0.95rem; margin: 0 auto; max-width: 500px; line-height: 1.6; color: #64748b;">
          You're all caught up on this stream! New editorial reviews, author milestone payouts, report verdicts, and discussion mentions will appear instantly.
        </p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Rich Notification Reader Modal with Glassmorphism -->
<div id="notifDetailModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.8); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 999999; align-items: center; justify-content: center; padding: 1.5rem;" onclick="if(event.target===this) this.style.display='none'">
  <div class="ts-card" style="width: 100%; max-width: 620px; padding: 2.25rem; border-radius: 20px; animation: tsModalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 1.25rem;">
      <div>
        <span id="modalNotifType" class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.4rem;">SYSTEM ALERT</span>
        <h3 id="modalNotifTitle" style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0.35rem 0 0 0; line-height: 1.4; font-family: 'Outfit', sans-serif; font-weight: 800;">Notification Details</h3>
        <div id="modalNotifTime" style="font-size: 0.78rem; color: var(--ts-text-tertiary); font-family: 'JetBrains Mono', monospace; margin-top: 0.4rem;">Aug 30, 2026 &bull; 12:00 PM</div>
      </div>
      <button onclick="document.getElementById('notifDetailModal').style.display='none'" style="background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); width: 32px; height: 32px; border-radius: 8px; color: var(--ts-text-secondary); font-size: 1.4rem; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1;">&times;</button>
    </div>

    <div style="background: var(--ts-bg-surface); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--ts-border-main); margin-bottom: 1.5rem;">
      <div id="modalNotifBody" style="font-size: 0.96rem; color: var(--ts-text-primary); line-height: 1.7; white-space: pre-wrap; word-break: break-word;"></div>
    </div>

    <div id="modalTargetUrlBox" style="display: none; background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.25); border-radius: 10px; padding: 0.85rem 1.15rem; margin-bottom: 1.75rem; font-size: 0.82rem; color: var(--ts-text-secondary); word-break: break-all;">
      <strong style="color: #38bdf8; display: block; margin-bottom: 0.25rem;">🎯 Actionable Destination Target:</strong>
      <span id="modalTargetUrlText" style="font-family: 'JetBrains Mono', monospace; color: var(--ts-text-primary);"></span>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
      <button type="button" onclick="document.getElementById('notifDetailModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm" style="border-radius: 8px; padding: 0.5rem 1.1rem;">Close</button>
      <a id="modalNotifActionBtn" href="#" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; border-radius: 8px; padding: 0.5rem 1.25rem;">
        Open Linked Page ↗
      </a>
    </div>
  </div>
</div>

<script>
function getApiBase() {
  if (typeof getTsApiBase === 'function') return getTsApiBase();
  const origin = window.location.origin;
  const path = window.location.pathname;
  if (path.includes('/techscout.n')) return `${origin}/techscout.n`;
  return origin;
}

function updateUnreadCounters(count) {
  const badge = document.getElementById('unread-status-badge');
  if (badge) badge.innerText = `${count} Unread Alerts`;
  const chip = document.getElementById('chip-unread-count');
  if (chip) chip.innerText = count;
  const kpi = document.getElementById('kpi-unread-val');
  if (kpi) kpi.innerText = count;
  
  // Navbar bell badge
  const navBadge = document.getElementById('notif-badge');
  if (navBadge) {
    navBadge.innerText = count > 99 ? '99+' : count;
    navBadge.style.display = count > 0 ? 'flex' : 'none';
  }
}

function filterNotificationsLive(query) {
  const q = query.toLowerCase().trim();
  const rows = document.querySelectorAll('.notif-item-row');
  rows.forEach(row => {
    const text = row.getAttribute('data-search') || '';
    if (!q || text.includes(q)) {
      row.style.display = 'flex';
    } else {
      row.style.display = 'none';
    }
  });
}

function changePerPage(val) {
  const url = new URL(window.location.href);
  url.searchParams.set('per_page', val);
  url.searchParams.set('page', '1');
  window.location.href = url.toString();
}

function toggleSelectAll(checked) {
  document.querySelectorAll('.notif-select-box').forEach(cb => cb.checked = checked);
  updateBatchBar();
}

function updateBatchBar() {
  const selected = Array.from(document.querySelectorAll('.notif-select-box:checked')).map(cb => cb.value);
  const bar = document.getElementById('batch-action-bar');
  const countSpan = document.getElementById('batch-selected-count');
  if (selected.length > 0) {
    bar.style.display = 'flex';
    countSpan.innerText = selected.length;
  } else {
    bar.style.display = 'none';
  }
}

async function batchMarkSelectedRead() {
  const ids = Array.from(document.querySelectorAll('.notif-select-box:checked')).map(cb => parseInt(cb.value));
  if (ids.length === 0) return;

  try {
    const baseUrl = getApiBase();
    const fd = new FormData();
    ids.forEach(id => fd.append('ids[]', id));

    const res = await fetch(`${baseUrl}/api/v1/notifications/read`, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const data = await res.json();
    if (data.success) {
      ids.forEach(id => {
        const card = document.getElementById(`notif-card-${id}`);
        if (card) {
          card.setAttribute('data-unread', '0');
          card.style.borderLeftColor = 'transparent';
          card.style.background = 'transparent';
          const pill = document.getElementById(`notif-pill-${id}`);
          if (pill) pill.remove();
        }
      });
      document.getElementById('selectAllCheckbox').checked = false;
      toggleSelectAll(false);
      if (typeof data.unread_count !== 'undefined') updateUnreadCounters(data.unread_count);
      if (typeof showToast === 'function') showToast(`Marked ${ids.length} notifications as read.`, 'success');
    }
  } catch (e) {
    console.error('Batch read error:', e);
  }
}

async function batchDeleteSelected() {
  const ids = Array.from(document.querySelectorAll('.notif-select-box:checked')).map(cb => parseInt(cb.value));
  if (ids.length === 0) return;

  if (!confirm(`Are you sure you want to dismiss ${ids.length} selected notifications?`)) return;

  try {
    const baseUrl = getApiBase();
    const fd = new FormData();
    ids.forEach(id => fd.append('ids[]', id));

    const res = await fetch(`${baseUrl}/api/v1/notifications/delete/0`, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const data = await res.json();
    if (data.success) {
      ids.forEach(id => {
        const card = document.getElementById(`notif-card-${id}`);
        if (card) {
          card.style.opacity = '0';
          card.style.transform = 'translateX(20px)';
          setTimeout(() => card.remove(), 150);
        }
      });
      document.getElementById('selectAllCheckbox').checked = false;
      toggleSelectAll(false);
      if (typeof data.unread_count !== 'undefined') updateUnreadCounters(data.unread_count);
      if (typeof showToast === 'function') showToast(`Dismissed ${ids.length} notifications.`, 'info');
    }
  } catch (e) {
    console.error('Batch delete error:', e);
  }
}

async function markSingleRead(id) {
  try {
    const baseUrl = getApiBase();
    const fd = new FormData();
    fd.append('id', id);
    const res = await fetch(`${baseUrl}/api/v1/notifications/read`, { 
      method: 'POST', 
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd 
    });
    const data = await res.json();
    if (data.success) {
      const card = document.getElementById(`notif-card-${id}`);
      if (card) {
        card.setAttribute('data-unread', '0');
        card.style.background = 'transparent';
        card.style.borderLeftColor = 'transparent';
      }
      const pill = document.getElementById(`notif-pill-${id}`);
      if (pill) pill.remove();
      if (typeof data.unread_count !== 'undefined') {
        updateUnreadCounters(data.unread_count);
      }
    }
  } catch (e) {
    console.error('Mark read error:', e);
  }
}

async function toggleNotificationRead(id) {
  try {
    const baseUrl = getApiBase();
    const fd = new FormData();
    fd.append('id', id);
    fd.append('toggle', '1');
    const res = await fetch(`${baseUrl}/api/v1/notifications/read`, { 
      method: 'POST', 
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd 
    });
    const data = await res.json();
    if (data.success) {
      const card = document.getElementById(`notif-card-${id}`);
      if (card) {
        const isNowUnread = card.getAttribute('data-unread') === '0';
        card.setAttribute('data-unread', isNowUnread ? '1' : '0');
        card.style.background = isNowUnread ? 'rgba(56,189,248,0.05)' : 'transparent';
        card.style.borderLeftColor = isNowUnread ? '#38bdf8' : 'transparent';

        const btn = document.getElementById(`btn-toggle-read-${id}`);
        if (btn) {
          btn.title = isNowUnread ? 'Mark as read' : 'Mark as unread';
          btn.innerHTML = `<i data-lucide="${isNowUnread ? 'check' : 'mail'}" style="width: 15px; height: 15px;"></i>`;
          btn.style.color = isNowUnread ? '#38bdf8' : '#94a3b8';
          if (typeof lucide !== 'undefined') lucide.createIcons();
        }
      }
      if (typeof data.unread_count !== 'undefined') {
        updateUnreadCounters(data.unread_count);
      }
    }
  } catch (e) {
    console.error('Toggle read error:', e);
  }
}

async function deleteNotificationItem(id) {
  try {
    const baseUrl = getApiBase();
    const res = await fetch(`${baseUrl}/api/v1/notifications/delete/${id}`, { 
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.success) {
      const card = document.getElementById(`notif-card-${id}`);
      if (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateX(20px)';
        setTimeout(() => card.remove(), 200);
      }
      if (typeof showToast === 'function') {
        showToast('Notification dismissed.', 'info');
      }
      if (typeof data.unread_count !== 'undefined') {
        updateUnreadCounters(data.unread_count);
      }
    }
  } catch (e) {
    console.error('Delete error:', e);
  }
}

function openNotificationViewer(id, title, type, message, actionUrl, formattedTime, btnLabel) {
  markSingleRead(id);

  document.getElementById('modalNotifTitle').innerText = title;
  document.getElementById('modalNotifType').innerText = type;
  document.getElementById('modalNotifTime').innerText = formattedTime;
  document.getElementById('modalNotifBody').innerText = message;

  const actionBtn = document.getElementById('modalNotifActionBtn');
  const urlBox = document.getElementById('modalTargetUrlBox');
  const urlText = document.getElementById('modalTargetUrlText');

  if (actionUrl && actionUrl.trim() !== '') {
    let cleanUrl = actionUrl.trim();
    try {
      if (cleanUrl.startsWith('http://') || cleanUrl.startsWith('https://')) {
        const u = new URL(cleanUrl);
        let p = u.pathname.replace(/^\/(techscout\.n|tech)(\/|$)/i, '/');
        cleanUrl = window.location.origin + p + u.search + u.hash;
      } else {
        let p = cleanUrl.replace(/^\/(techscout\.n|tech)(\/|$)/i, '/');
        if (!p.startsWith('/')) p = '/' + p;
        cleanUrl = window.location.origin + p;
      }
    } catch(e) {
      cleanUrl = window.location.origin + (cleanUrl.startsWith('/') ? cleanUrl : '/' + cleanUrl);
    }
    actionBtn.href = cleanUrl;
    actionBtn.innerText = btnLabel || 'Open Linked Target ↗';
    actionBtn.style.display = 'inline-flex';
    
    urlBox.style.display = 'block';
    urlText.innerText = cleanUrl;
  } else {
    actionBtn.style.display = 'none';
    urlBox.style.display = 'none';
  }

  document.getElementById('notifDetailModal').style.display = 'flex';
}

async function markAllNotificationsRead() {
  try {
    const baseUrl = getApiBase();
    const filter = '<?= htmlspecialchars($currentFilter ?? 'all') ?>';
    const fd = new FormData();
    fd.append('all', '1');
    fd.append('filter', filter);
    const res = await fetch(`${baseUrl}/api/v1/notifications/read-all`, { 
      method: 'POST', 
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd 
    });
    const data = await res.json();
    if (data.success) {
      document.querySelectorAll('.notif-item-row').forEach(row => {
        row.setAttribute('data-unread', '0');
        row.style.background = 'transparent';
        row.style.borderLeftColor = 'transparent';
      });
      document.querySelectorAll('[id^="notif-pill-"]').forEach(pill => pill.remove());
      updateUnreadCounters(0);
      if (typeof showToast === 'function') {
        showToast('All notifications marked as read! ✓', 'success');
      }
    }
  } catch (e) {
    console.error('Mark all read error:', e);
  }
}

async function confirmClearAllNotifications() {
  if (typeof tsModal !== 'undefined') {
    const ok = await tsModal.confirm('Are you sure you want to clear these notifications? This action cannot be undone.', 'Clear Notifications');
    if (!ok) return;
  } else {
    if (!confirm('Are you sure you want to clear these notifications?')) return;
  }

  try {
    const baseUrl = getApiBase();
    const filter = '<?= htmlspecialchars($currentFilter ?? 'all') ?>';
    const fd = new FormData();
    fd.append('filter', filter);
    const res = await fetch(`${baseUrl}/api/v1/notifications/clear-all`, { 
      method: 'POST', 
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd 
    });
    const data = await res.json();
    if (data.success) {
      location.reload();
    }
  } catch (e) {
    console.error('Clear all error:', e);
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
