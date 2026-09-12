<?php
$title = 'Live System Audit Logs & Forensic Telemetry | TechScout Admin';
require __DIR__ . '/../layouts/admin_header.php';

// Entity Link & Icon Resolver Helper
function resolveEntityLink(?string $entityType, mixed $entityId): array {
    if (empty($entityType)) {
        return ['name' => 'General System', 'icon' => 'server', 'url' => null, 'badge' => 'ts-badge-cyan'];
    }

    $type = strtolower(trim($entityType));
    $id = (int)$entityId;

    switch ($type) {
        case 'user':
        case 'member':
        case 'author':
            return [
                'name' => 'User Account',
                'label' => 'User #' . $id,
                'icon' => 'user',
                'url' => $id > 0 ? url('/admin/users?q=' . $id) : url('/admin/users'),
                'badge' => 'ts-badge-indigo'
            ];
        case 'content':
        case 'article':
        case 'post':
            return [
                'name' => 'Editorial Article',
                'label' => 'Article #' . $id,
                'icon' => 'file-text',
                'url' => $id > 0 ? url('/admin/content/edit/' . $id) : url('/admin/content-queue'),
                'badge' => 'ts-badge-cyan'
            ];
        case 'ticket':
        case 'support_ticket':
            return [
                'name' => 'Support Ticket',
                'label' => 'Ticket #' . $id,
                'icon' => 'life-buoy',
                'url' => $id > 0 ? url('/admin/tickets?ticket=' . $id) : url('/admin/tickets'),
                'badge' => 'ts-badge-amber'
            ];
        case 'comment':
            return [
                'name' => 'Discussion Comment',
                'label' => 'Comment #' . $id,
                'icon' => 'message-square',
                'url' => url('/admin/moderation'),
                'badge' => 'ts-badge-pink'
            ];
        case 'job':
        case 'job_application':
            return [
                'name' => 'Engineering Job Opening',
                'label' => 'Job #' . $id,
                'icon' => 'briefcase',
                'url' => url('/admin/jobs/applications'),
                'badge' => 'ts-badge-green'
            ];
        case 'author_application':
            return [
                'name' => 'Author Application',
                'label' => 'Application #' . $id,
                'icon' => 'user-check',
                'url' => url('/admin/author-applications'),
                'badge' => 'ts-badge-indigo'
            ];
        case 'mail':
        case 'mail_template':
            return [
                'name' => 'Mail Template',
                'label' => 'Template #' . $id,
                'icon' => 'mail',
                'url' => url('/admin/mail'),
                'badge' => 'ts-badge-cyan'
            ];
        case 'database_backup':
        case 'backup':
            return [
                'name' => 'Database Backup Snapshot',
                'label' => 'Snapshot #' . $id,
                'icon' => 'database',
                'url' => url('/admin/db-manager?tab=snapshots'),
                'badge' => 'ts-badge-pink'
            ];
        case 'firewall':
        case 'firewall_rule':
        case 'ip_lockout':
            return [
                'name' => 'Firewall & IP Rule',
                'label' => 'Rule #' . $id,
                'icon' => 'shield',
                'url' => url('/admin/firewall'),
                'badge' => 'ts-badge-red'
            ];
        case 'setting':
        case 'site_setting':
        case 'system_setting':
            return [
                'name' => 'System Setting',
                'label' => 'Config Matrix',
                'icon' => 'settings',
                'url' => url('/admin/settings'),
                'badge' => 'ts-badge-green'
            ];
        case 'api_key':
            return [
                'name' => 'REST API Key',
                'label' => 'Key #' . $id,
                'icon' => 'key',
                'url' => url('/admin/api-keys'),
                'badge' => 'ts-badge-amber'
            ];
        case 'webhook':
            return [
                'name' => 'Webhook Endpoint',
                'label' => 'Webhook #' . $id,
                'icon' => 'webhook',
                'url' => url('/admin/webhooks'),
                'badge' => 'ts-badge-indigo'
            ];
        default:
            return [
                'name' => ucwords(str_replace('_', ' ', $type)),
                'label' => ucwords(str_replace('_', ' ', $type)) . ($id > 0 ? ' #' . $id : ''),
                'icon' => 'box',
                'url' => null,
                'badge' => 'ts-badge-cyan'
            ];
    }
}
?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">FORENSIC TELEMETRY HUD</span>
        <span style="font-size: 0.8rem; color: #64748b;">Immutable Entity Audit Trail &amp; Administrative Mutation Explorer</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif;">Live System Audit Logs</h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="location.reload()" class="ts-btn ts-btn-outline ts-btn-sm" title="Refresh Log Stream">
        🔄 Refresh Stream
      </button>
      <button type="button" onclick="exportAuditCsv()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #34d399; border-color: rgba(16,185,129,0.3);">
        📊 Export CSV
      </button>
      <a href="<?= url('/admin/db-manager') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        🗄️ Database Manager
      </a>
      <a href="<?= url('/admin/system-logs') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #ec4899, #f43f5e);">
        🚨 System Error Logs
      </a>
    </div>
  </div>

  <!-- Telemetry Stat Grid -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Audit Events</div>
      <div class="admin-stat-val" style="color: #38bdf8; font-family: var(--admin-font-mono);"><?= number_format($stats['total']) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Platform immutable log records</div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-label">Tracked Actors</div>
      <div class="admin-stat-val" style="color: #818cf8; font-family: var(--admin-font-mono);"><?= number_format($stats['unique_users']) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Unique active administrators &amp; users</div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-label">Distinct Action Vectors</div>
      <div class="admin-stat-val" style="color: #ec4899; font-family: var(--admin-font-mono);"><?= number_format($stats['unique_actions']) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Security, mutation &amp; auth triggers</div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-label">Events Today</div>
      <div class="admin-stat-val" style="color: #10b981; font-family: var(--admin-font-mono);"><?= number_format($stats['today']) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Live operations recorded today (PKT)</div>
    </div>
  </div>

  <!-- Multi-Facet Filters Bar -->
  <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(99,102,241,0.2); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.75rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      
      <!-- Search Input -->
      <div style="flex: 1; min-width: 240px;">
        <input type="text" id="auditSearchInput" onkeyup="filterAuditTable()" placeholder="🔍 Fast search (Action, User, Entity, ID, IP, Country)..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.5rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
      </div>

      <!-- User Selector -->
      <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.82rem; font-weight: 600; color: #94a3b8;">👤 Actor:</span>
        <select onchange="applyAuditFilters('user_id', this.value)" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.5rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
          <option value="">All Actors &amp; Staff</option>
          <?php foreach ($allUsers as $u): ?>
            <option value="<?= $u['id'] ?>" <?= ($selectedUserId === (int)$u['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($u['display_name']) ?> (@<?= htmlspecialchars($u['username']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Action Type Selector -->
      <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.82rem; font-weight: 600; color: #94a3b8;">⚡ Action:</span>
        <select onchange="applyAuditFilters('action', this.value)" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.5rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
          <option value="all">All Action Types</option>
          <?php foreach ($distinctActions as $act): ?>
            <option value="<?= htmlspecialchars($act) ?>" <?= ($selectedAction === $act) ? 'selected' : '' ?>>
              <?= htmlspecialchars($act) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Reset -->
      <?php if (!empty($selectedUserId) || ($selectedAction !== 'all' && !empty($selectedAction))): ?>
        <div>
          <a href="<?= url('/admin/audit-logs') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.4);">
            ✕ Clear Filters
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Detailed Audit Records Table -->
  <div class="admin-table-wrap">
    <table class="admin-table" id="auditLogsTable">
      <thead>
        <tr>
          <th style="width: 130px;">Timestamp (PKT)</th>
          <th>Actor / Operator</th>
          <th>Action Vector</th>
          <th>Linked Entity &amp; Destination</th>
          <th>Origin IP &amp; Geolocation</th>
          <th>Device &amp; Browser</th>
          <th style="width: 150px; text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($logs)): ?>
          <?php foreach ($logs as $log): ?>
            <?php
              $entityInfo = resolveEntityLink($log['entity_type'] ?? null, $log['entity_id'] ?? null);
              
              // Action badge color
              $actionName = $log['action'] ?? '';
              $actionBadgeClass = 'ts-badge-cyan';
              if (str_contains($actionName, 'delete') || str_contains($actionName, 'ban') || str_contains($actionName, 'strike') || str_contains($actionName, 'suspend')) {
                $actionBadgeClass = 'ts-badge-red';
              } elseif (str_contains($actionName, 'approve') || str_contains($actionName, 'create') || str_contains($actionName, 'publish')) {
                $actionBadgeClass = 'ts-badge-green';
              } elseif (str_contains($actionName, 'update') || str_contains($actionName, 'edit') || str_contains($actionName, 'toggle')) {
                $actionBadgeClass = 'ts-badge-pink';
              } elseif (str_contains($actionName, 'login') || str_contains($actionName, 'auth')) {
                $actionBadgeClass = 'ts-badge-indigo';
              }

              // Build serialized JSON for forensic modal
              $auditPayload = [
                  'id' => (int)$log['id'],
                  'action' => $actionName,
                  'action_domain' => $log['action_domain'] ?? 'core',
                  'entity_type' => $log['entity_type'] ?? '',
                  'entity_id' => $log['entity_id'] ?? null,
                  'entity_name' => $entityInfo['name'],
                  'entity_url' => $entityInfo['url'],
                  'user_id' => $log['user_id'] ? (int)$log['user_id'] : null,
                  'username' => $log['username'] ?? '',
                  'display_name' => $log['display_name'] ?? $log['username'] ?? 'System / Anonymous',
                  'actor_role' => $log['user_role'] ?? $log['actor_role'] ?? 'member',
                  'ip_address' => $log['ip_address'] ?? '127.0.0.1',
                  'country' => $log['country'] ?? 'Pakistan 🇵🇰',
                  'device_info' => $log['device_info'] ?? 'Desktop Client',
                  'user_agent' => $log['user_agent'] ?? '',
                  'details' => !empty($log['details']) ? @json_decode($log['details'], true) : null,
                  'before_state' => !empty($log['before_state']) ? @json_decode($log['before_state'], true) : null,
                  'after_state' => !empty($log['after_state']) ? @json_decode($log['after_state'], true) : null,
                  'created_at' => $log['created_at'] ?? ''
              ];
            ?>
            <tr>
              <!-- 1. TIMESTAMP -->
              <td style="font-family: var(--admin-font-mono); font-size: 0.78rem; color: #94a3b8; white-space: nowrap;">
                <div style="font-weight: 700; color: #f8fafc;"><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                <div style="font-size: 0.7rem; color: #64748b;"><?= date('M j, Y', strtotime($log['created_at'])) ?></div>
              </td>

              <!-- 2. ACTOR / USER -->
              <td>
                <?php if (!empty($log['username'])): ?>
                  <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #1e293b; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #38bdf8; font-size: 0.75rem;">
                      <?= strtoupper(substr($log['display_name'] ?? $log['username'], 0, 1)) ?>
                    </div>
                    <div>
                      <a href="<?= url('/admin/users?q=' . urlencode($log['username'])) ?>" target="_blank" style="font-weight: 700; color: #38bdf8; text-decoration: none; font-size: 0.88rem;" title="View User in Admin Hub">
                        <?= htmlspecialchars($log['display_name'] ?? $log['username']) ?> ↗
                      </a>
                      <div style="font-size: 0.72rem; color: #64748b;">
                        ID #<?= (int)$log['user_id'] ?> &bull; 
                        <span class="ts-badge" style="font-size: 0.6rem; padding: 0.05rem 0.35rem;"><?= strtoupper($log['user_role'] ?? 'USER') ?></span>
                        <a href="<?= url('/admin/audit-logs?user_id=' . $log['user_id']) ?>" style="color: #94a3b8; margin-left: 0.3rem; text-decoration: underline;" title="Filter by this user">Filter</a>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <span style="color: #64748b; font-size: 0.82rem;">⚙️ Automated System Runner</span>
                <?php endif; ?>
              </td>

              <!-- 3. ACTION -->
              <td>
                <span class="ts-badge <?= $actionBadgeClass ?>" style="font-family: var(--admin-font-mono); font-size: 0.72rem; font-weight: 700;">
                  <?= htmlspecialchars($actionName) ?>
                </span>
              </td>

              <!-- 4. LINKED ENTITY -->
              <td>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                  <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <span class="ts-badge <?= $entityInfo['badge'] ?>" style="font-size: 0.68rem; font-weight: 700;">
                      <?= htmlspecialchars($entityInfo['name']) ?>
                    </span>
                    <?php if (!empty($log['entity_id'])): ?>
                      <span style="color: #cbd5e1; font-family: var(--admin-font-mono); font-size: 0.78rem; font-weight: 700;">
                        #<?= (int)$log['entity_id'] ?>
                      </span>
                    <?php endif; ?>
                  </div>

                  <!-- Clickable Target Link -->
                  <?php if (!empty($entityInfo['url'])): ?>
                    <a href="<?= htmlspecialchars($entityInfo['url']) ?>" target="_blank" style="font-size: 0.78rem; color: #38bdf8; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 600;">
                      <span>Inspect <?= htmlspecialchars($entityInfo['label']) ?></span>
                      <span style="font-size: 0.7rem;">↗</span>
                    </a>
                  <?php else: ?>
                    <span style="font-size: 0.75rem; color: #64748b;">Global / Non-relational</span>
                  <?php endif; ?>
                </div>
              </td>

              <!-- 5. IP & ORIGIN COUNTRY -->
              <td>
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                  <span style="font-family: var(--admin-font-mono); font-size: 0.8rem; color: #38bdf8; font-weight: 600;"><?= htmlspecialchars($log['ip_address']) ?></span>
                  <a href="<?= url('/admin/firewall?ip=' . urlencode($log['ip_address'])) ?>" target="_blank" style="font-size: 0.68rem; color: #94a3b8;" title="Check IP in Firewall">🛡️</a>
                </div>
                <div style="font-size: 0.75rem; color: #10b981; font-weight: 600; margin-top: 0.15rem;">
                  <?= htmlspecialchars($log['country'] ?? 'Pakistan 🇵🇰') ?>
                </div>
              </td>

              <!-- 6. DEVICE & BROWSER -->
              <td style="max-width: 200px;">
                <div style="font-size: 0.78rem; color: #e2e8f0; line-height: 1.35;">
                  <?= htmlspecialchars($log['device_info'] ?? 'Desktop Client') ?>
                </div>
              </td>

              <!-- 7. ACTIONS -->
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.35rem; justify-content: flex-end; align-items: center;">
                  <button type="button" 
                          data-audit="<?= htmlspecialchars(json_encode($auditPayload, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>" 
                          onclick="openAuditForensicInspector(this)" 
                          class="ts-btn ts-btn-outline ts-btn-sm" 
                          style="padding: 0.25rem 0.6rem; font-size: 0.75rem; color: #38bdf8; border-color: rgba(56,189,248,0.4); background: rgba(56,189,248,0.08); font-weight: 700;">
                    🔍 Forensics
                  </button>

                  <form action="<?= url('/admin/audit-logs/delete/' . $log['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this audit trail entry?');" style="display: inline; margin: 0;">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.45rem; font-size: 0.72rem; color: #ef4444; border-color: rgba(239,68,68,0.3);" title="Delete Log Record">
                      🗑️
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">
              No audit trail events match your current filter parameters.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ========================================================================= -->
<!-- ========================================================================= -->
<!-- COMPREHENSIVE AUDIT FORENSIC INSPECTOR MODAL -->
<!-- ========================================================================= -->
<div id="auditInspectorModal" class="admin-modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="audit-inspector-box" style="max-width: 980px; width: 100%; max-height: 90vh;">
    
    <!-- Modal Header -->
    <div class="audit-inspector-header">
      <div>
        <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.75rem; font-weight: 800;">AUDIT FORENSIC TELEMETRY</span>
          <span id="auditModalActionBadge" class="ts-badge ts-badge-indigo" style="font-size: 0.75rem; font-weight: 700; font-family: var(--admin-font-mono);"></span>
          <span id="auditModalTimestamp" style="font-size: 0.75rem; color: var(--admin-text-muted); font-family: var(--admin-font-mono);"></span>
        </div>
        <h3 id="auditModalTitle" style="color: var(--admin-text-main); font-size: 1.1rem; margin: 0; font-family: 'Outfit', sans-serif;"></h3>
      </div>
      <button type="button" onclick="closeAuditInspector()" style="background: transparent; border: none; color: var(--admin-text-muted); font-size: 1.4rem; cursor: pointer; padding: 0.2rem 0.5rem; line-height: 1;">✕</button>
    </div>

    <!-- Modal Sub-Tabs -->
    <div class="audit-inspector-tabs">
      <button type="button" class="audit-tab-btn active" onclick="switchAuditTab('atab-overview')" id="atabBtn-overview">
        📌 Overview &amp; Linked Entity
      </button>
      <button type="button" class="audit-tab-btn" onclick="switchAuditTab('atab-diff')" id="atabBtn-diff">
        ⚖️ State Mutation Diff (Before / After)
      </button>
      <button type="button" class="audit-tab-btn" onclick="switchAuditTab('atab-payload')" id="atabBtn-payload">
        📋 Payload &amp; Metadata JSON
      </button>
    </div>

    <!-- Modal Tab Content -->
    <div class="audit-inspector-body">
      
      <!-- SUB-TAB 1: OVERVIEW & LINKED ENTITY -->
      <div id="panel-atab-overview" class="audit-panel active">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
          
          <!-- Actor Card -->
          <div class="audit-inspector-card">
            <div style="font-size: 0.72rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700; margin-bottom: 0.5rem;">Actor / Operator Profile</div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div id="auditActorInitial" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #38bdf8); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: 800; color: #fff;"></div>
              <div>
                <div id="auditActorName" style="color: var(--admin-text-main); font-weight: 800; font-size: 0.95rem;"></div>
                <div id="auditActorRole" style="font-size: 0.75rem; color: #38bdf8; font-family: var(--admin-font-mono);"></div>
              </div>
            </div>
            <div id="auditActorLinkWrap" style="margin-top: 0.75rem;"></div>
          </div>

          <!-- Target Entity Card -->
          <div class="audit-inspector-card">
            <div style="font-size: 0.72rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700; margin-bottom: 0.5rem;">Linked Target Entity</div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;">
              <span id="auditEntityNameBadge" class="ts-badge ts-badge-cyan" style="font-size: 0.75rem; font-weight: 800;"></span>
              <span id="auditEntityIdBadge" style="font-family: var(--admin-font-mono); color: var(--admin-text-main); font-weight: 700;"></span>
            </div>
            <div id="auditEntityDirectBtnWrap" style="margin-top: 0.75rem;"></div>
          </div>

          <!-- Network & Device Card -->
          <div class="audit-inspector-card">
            <div style="font-size: 0.72rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700; margin-bottom: 0.5rem;">Network &amp; Device Context</div>
            <div style="display: flex; align-items: center; gap: 0.4rem; font-family: var(--admin-font-mono); font-size: 0.88rem; color: #38bdf8; font-weight: 700;">
              <span id="auditClientIp"></span>
            </div>
            <div id="auditCountryLocation" style="font-size: 0.8rem; color: #10b981; font-weight: 600; margin-top: 0.2rem;"></div>
            <div id="auditDeviceInfo" style="font-size: 0.75rem; color: var(--admin-text-muted); margin-top: 0.35rem;"></div>
          </div>
        </div>

        <div>
          <label style="font-size: 0.85rem; font-weight: 700; color: var(--admin-text-main); margin-bottom: 0.4rem; display: block;">Raw User-Agent Header</label>
          <pre id="auditRawUserAgent" class="audit-inspector-code"></pre>
        </div>
      </div>

      <!-- SUB-TAB 2: MUTATION DIFF -->
      <div id="panel-atab-diff" class="audit-panel" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="font-size: 0.85rem; font-weight: 700; color: #f87171; display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.4rem;">
              <span>⏮ Before State (Original)</span>
            </label>
            <pre id="auditBeforeState" class="audit-inspector-code" style="border-color: rgba(239,68,68,0.35);"></pre>
          </div>
          <div>
            <label style="font-size: 0.85rem; font-weight: 700; color: #34d399; display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.4rem;">
              <span>⏭ After State (Mutated)</span>
            </label>
            <pre id="auditAfterState" class="audit-inspector-code" style="border-color: rgba(16,185,129,0.35);"></pre>
          </div>
        </div>
      </div>

      <!-- SUB-TAB 3: PAYLOAD & METADATA -->
      <div id="panel-atab-payload" class="audit-panel" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <label style="font-size: 0.85rem; font-weight: 700; color: var(--admin-text-main);">Captured Event Metadata &amp; Parameters</label>
          <button type="button" onclick="copyAuditPayload()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">📋 Copy JSON</button>
        </div>
        <pre id="auditPayloadContent" class="audit-inspector-code" style="color: #38bdf8;"></pre>
      </div>

    </div>

    <!-- Modal Footer -->
    <div class="audit-inspector-footer">
      <button type="button" onclick="copyFullAuditJson()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        📋 Copy Complete Forensic Record
      </button>
      <button type="button" onclick="closeAuditInspector()" class="ts-btn ts-btn-primary ts-btn-sm">
        Close Inspector
      </button>
    </div>

  </div>
</div>

<style>
.audit-tab-btn {
  background: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  color: #94a3b8;
  font-size: 0.85rem;
  font-weight: 700;
  padding: 0.85rem 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.audit-tab-btn:hover {
  color: #f8fafc;
  background: rgba(255,255,255,0.02);
}
.audit-tab-btn.active {
  color: #38bdf8;
  border-bottom-color: #38bdf8;
  background: rgba(56,189,248,0.06);
}
.audit-panel {
  animation: fadeIn 0.15s ease;
}
</style>

<script>
let currentAuditLog = null;

function openAuditForensicInspector(btn) {
  try {
    const raw = btn.getAttribute('data-audit');
    if (!raw) return;
    const log = JSON.parse(raw);
    currentAuditLog = log;

    // Header
    document.getElementById('auditModalActionBadge').innerText = log.action || 'MUTATION';
    document.getElementById('auditModalTimestamp').innerText = `🕒 ${log.created_at || ''}`;
    document.getElementById('auditModalTitle').innerText = `${log.display_name} performed "${log.action}" on ${log.entity_name}`;

    // Actor
    const name = log.display_name || log.username || 'System';
    document.getElementById('auditActorInitial').innerText = name.charAt(0).toUpperCase();
    document.getElementById('auditActorName').innerText = name;
    document.getElementById('auditActorRole').innerText = `${(log.actor_role || 'member').toUpperCase()} (ID #${log.user_id || 'SYS'})`;

    const actorWrap = document.getElementById('auditActorLinkWrap');
    if (log.user_id) {
      actorWrap.innerHTML = `
        <div style="display: flex; gap: 0.5rem;">
          <a href="${window.location.origin}/admin/users?q=${encodeURIComponent(log.username || log.user_id)}" target="_blank" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">
            👤 View User in Admin ↗
          </a>
          <a href="${window.location.origin}/admin/audit-logs?user_id=${log.user_id}" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #38bdf8;">
            Filter Actor Logs
          </a>
        </div>
      `;
    } else {
      actorWrap.innerHTML = '<span style="color: #64748b; font-size: 0.75rem;">Automated System Worker</span>';
    }

    // Entity
    document.getElementById('auditEntityNameBadge').innerText = log.entity_name || 'System';
    document.getElementById('auditEntityIdBadge').innerText = log.entity_id ? `#${log.entity_id}` : '';
    
    const entityBtnWrap = document.getElementById('auditEntityDirectBtnWrap');
    if (log.entity_url) {
      entityBtnWrap.innerHTML = `
        <a href="${log.entity_url}" target="_blank" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.78rem; padding: 0.25rem 0.7rem; background: linear-gradient(135deg, #10b981, #059669);">
          🚀 Open Entity Directly in Admin ↗
        </a>
      `;
    } else {
      entityBtnWrap.innerHTML = '<span style="color: #64748b; font-size: 0.75rem;">Direct URL not applicable for global state</span>';
    }

    // Network & Device
    document.getElementById('auditClientIp').innerText = log.ip_address || '127.0.0.1';
    document.getElementById('auditCountryLocation').innerText = log.country || 'Pakistan 🇵🇰';
    document.getElementById('auditDeviceInfo').innerText = log.device_info || 'Desktop Client';
    document.getElementById('auditRawUserAgent').innerText = log.user_agent || 'User-Agent not recorded';

    // Before & After Diff
    document.getElementById('auditBeforeState').innerText = log.before_state ? JSON.stringify(log.before_state, null, 2) : 'No before-state snapshot recorded for this action.';
    document.getElementById('auditAfterState').innerText = log.after_state ? JSON.stringify(log.after_state, null, 2) : 'No after-state snapshot recorded for this action.';

    // Payload
    document.getElementById('auditPayloadContent').innerText = log.details ? JSON.stringify(log.details, null, 2) : 'No additional metadata payload attached.';

    switchAuditTab('atab-overview');
    document.getElementById('auditInspectorModal').style.display = 'flex';
  } catch (err) {
    console.error('Audit inspector error:', err);
  }
}

function closeAuditInspector() {
  document.getElementById('auditInspectorModal').style.display = 'none';
}

function switchAuditTab(tabId) {
  document.querySelectorAll('.audit-tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.audit-panel').forEach(p => {
    p.style.display = 'none';
    p.classList.remove('active');
  });

  const activeBtn = document.getElementById('atabBtn-' + tabId.replace('atab-', ''));
  if (activeBtn) activeBtn.classList.add('active');

  const panel = document.getElementById('panel-' + tabId);
  if (panel) {
    panel.style.display = 'block';
    panel.classList.add('active');
  }
}

function copyAuditPayload() {
  if (!currentAuditLog) return;
  const d = currentAuditLog.details;
  navigator.clipboard.writeText(JSON.stringify(d, null, 2)).then(() => alert('Payload JSON copied to clipboard!'));
}

function copyFullAuditJson() {
  if (!currentAuditLog) return;
  navigator.clipboard.writeText(JSON.stringify(currentAuditLog, null, 2)).then(() => alert('Complete Forensic Audit Record copied to clipboard!'));
}

function applyAuditFilters(key, val) {
  const url = new URL(window.location.href);
  if (val && val !== 'all') {
    url.searchParams.set(key, val);
  } else {
    url.searchParams.delete(key);
  }
  window.location.href = url.toString();
}

function filterAuditTable() {
  const q = document.getElementById('auditSearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#auditLogsTable tbody tr');
  rows.forEach(tr => {
    const text = tr.innerText.toLowerCase();
    tr.style.display = text.includes(q) ? '' : 'none';
  });
}

function exportAuditCsv() {
  const table = document.getElementById('auditLogsTable');
  if (!table) return;
  
  let csv = [];
  const rows = table.querySelectorAll('tr');
  for (let i = 0; i < rows.length; i++) {
    const row = [];
    const cols = rows[i].querySelectorAll('td, th');
    for (let j = 0; j < cols.length - 1; j++) {
      let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/"/g, '""').trim();
      row.push('"' + data + '"');
    }
    if (row.length > 0) csv.push(row.join(','));
  }
  
  const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
  const link = document.createElement('a');
  link.download = `techscout_audit_logs_${Date.now()}.csv`;
  link.href = window.URL.createObjectURL(csvFile);
  link.style.display = 'none';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeAuditInspector();
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
