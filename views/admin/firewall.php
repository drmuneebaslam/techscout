<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

  <?php
    $activeLockouts = \App\Core\Database::query("SELECT * FROM ip_lockouts WHERE is_active = 1 AND locked_until > NOW() ORDER BY created_at DESC");
    $recentAnomalies = \App\Models\SecurityAnomalyEngine::getRecentAnomalies(50);
    $activeTab = $_GET['tab'] ?? 'rules';
  ?>

  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink">CYBER SHIELD &bull; WAF DEFENSE</span>
        <span style="font-size: 0.8rem; color: #64748b;">Intrusion Detection, Malicious Bot Mitigation &amp; IP Blacklisting</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Firewall &amp; Threat Perimeter
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
      <a href="<?= url('/admin/audit-logs') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="View dedicated system audit logs">
        <i data-lucide="file-text"></i> System Audit Telemetry &rarr;
      </a>
      <form action="<?= url('/admin/firewall/logs/clear') ?>" method="POST" onsubmit="return confirm('Clear all threat telemetry logs?');" style="margin: 0;">
        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8;">
          <i data-lucide="trash-2"></i> Clear Threat Logs
        </button>
      </form>
      <button type="button" onclick="openNewRuleModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #ef4444; border-color: #ef4444; font-weight: 700;">
        <i data-lucide="shield-alert"></i> Enforce Block Rule 🛡️
      </button>
    </div>
  </div>

  <!-- Metric Stat Cards -->
  <div class="admin-stat-grid" style="margin-bottom: 2rem;">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Intrusions Blocked</div>
      <div class="admin-stat-val" style="color: #ef4444;"><?= number_format($totalBlocked ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #f87171;">Automated WAF mitigations</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Active Enforcement Rules</div>
      <div class="admin-stat-val" style="color: #f59e0b;"><?= count($rules ?? []) ?></div>
      <div style="font-size: 0.8rem; color: #fbbf24;">IPs &amp; User-Agent signatures</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Threat Telemetry Records</div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= count($logs ?? []) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Recent security events</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Active 15-Min Lockouts</div>
      <div class="admin-stat-val" style="color: <?= count($activeLockouts) > 0 ? '#ef4444' : '#10b981' ?>;">
        <?= count($activeLockouts) ?>
      </div>
      <div style="font-size: 0.8rem; color: #34d399;">Brute-force defensive shield</div>
    </div>
  </div>

  <!-- Tabbed Navigation Ribbon -->
  <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(148,163,184,0.12); padding-bottom: 0.75rem; overflow-x: auto; scrollbar-width: none;">
    <button type="button" onclick="switchSecurityTab('rules')" id="tab_btn_rules" class="ts-btn ts-btn-sm" style="border-radius: 999px; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem;">
      <i data-lucide="shield" style="width: 14px; height: 14px;"></i>
      <span>Perimeter Rules</span>
      <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; padding: 0.1rem 0.4rem;"><?= count($rules ?? []) ?></span>
    </button>

    <button type="button" onclick="switchSecurityTab('threats')" id="tab_btn_threats" class="ts-btn ts-btn-sm" style="border-radius: 999px; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem;">
      <i data-lucide="zap" style="width: 14px; height: 14px;"></i>
      <span>Threat Intrusion Stream</span>
      <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem; padding: 0.1rem 0.4rem;"><?= count($logs ?? []) ?></span>
    </button>

    <button type="button" onclick="switchSecurityTab('lockouts')" id="tab_btn_lockouts" class="ts-btn ts-btn-sm" style="border-radius: 999px; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem;">
      <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
      <span>15-Minute IP Lockouts</span>
      <span class="ts-badge <?= count($activeLockouts) > 0 ? 'ts-badge-pink' : 'ts-badge-green' ?>" style="font-size: 0.68rem; padding: 0.1rem 0.4rem;">
        <?= count($activeLockouts) ?>
      </span>
    </button>

    <button type="button" onclick="switchSecurityTab('anomalies')" id="tab_btn_anomalies" class="ts-btn ts-btn-sm" style="border-radius: 999px; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem;">
      <i data-lucide="alert-triangle" style="width: 14px; height: 14px;"></i>
      <span>Live Security Anomalies</span>
      <span class="ts-badge ts-badge-amber" style="font-size: 0.68rem; padding: 0.1rem 0.4rem;"><?= count($recentAnomalies ?? []) ?></span>
    </button>
  </div>

  <!-- TAB 1: Perimeter Enforcement Rules -->
  <div id="pane_rules" class="security-tab-pane" style="display: none;">
    <div class="admin-table-wrap" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.25rem 0;">Active Perimeter Enforcement Rules</h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Configured IP blocks, IP allowlists, and malicious bot user-agent signatures.</p>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
          <input type="text" id="filter_rules_search" onkeyup="filterTableRows('rulesTable', this.value, 'rules_page_info')" placeholder="Search rules (IP, reason)..." style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.45rem 0.85rem; color: #f8fafc; font-size: 0.85rem; min-width: 220px;">
        </div>
      </div>

      <table class="admin-table" id="rulesTable">
        <thead>
          <tr>
            <th>Rule Type</th>
            <th>Target Value (IP / User-Agent)</th>
            <th>Action</th>
            <th>Reason</th>
            <th style="text-align: right;">Hits Blocked</th>
            <th>Date Added</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody id="rules_tbody">
          <?php if (!empty($rules)): ?>
            <?php foreach ($rules as $r): ?>
              <tr class="paginated-row-rules">
                <td>
                  <span class="ts-badge ts-badge-cyan" style="font-family: monospace; font-size: 0.7rem; text-transform: uppercase;">
                    <?= htmlspecialchars($r['rule_type']) ?>
                  </span>
                </td>
                <td>
                  <code style="color: #f8fafc; font-size: 0.88rem; background: #05070b; padding: 3px 8px; border-radius: 4px; border: 1px solid rgba(148,163,184,0.15);">
                    <?= htmlspecialchars($r['target_value']) ?>
                  </code>
                </td>
                <td>
                  <span class="ts-badge <?= $r['action'] === 'block' ? 'ts-badge-pink' : 'ts-badge-green' ?>">
                    <?= strtoupper($r['action']) ?>
                  </span>
                </td>
                <td style="color: #94a3b8; font-size: 0.85rem;"><?= htmlspecialchars($r['reason'] ?? 'Manual Enforcement') ?></td>
                <td style="text-align: right;">
                  <span style="font-weight: 700; color: #ef4444; font-family: monospace;"><?= number_format($r['hits_count'] ?? 0) ?></span>
                </td>
                <td style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #94a3b8;">
                  <?= \App\Core\DateHelper::render($r['created_at'], 'Y-m-d H:i', 'short') ?>
                </td>
                <td style="text-align: right;">
                  <form action="<?= url('/admin/firewall/rules/delete/' . $r['id']) ?>" method="POST" onsubmit="return confirm('Remove this firewall rule?');" style="display: inline;">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #ef4444; border-color: rgba(239,68,68,0.3);">
                      Delete Rule ✕
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: #64748b;">No active firewall rules defined.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Pagination for Rules -->
      <div id="rules_pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(148,163,184,0.1);">
        <div id="rules_page_info" style="font-size: 0.82rem; color: #64748b;">Showing <?= count($rules ?? []) ?> rules</div>
        <div style="display: flex; gap: 0.35rem;" id="rules_page_btns"></div>
      </div>
    </div>
  </div>

  <!-- TAB 2: Threat Intrusion Stream -->
  <div id="pane_threats" class="security-tab-pane" style="display: none;">
    <div class="admin-table-wrap" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.25rem 0;">Real-Time Intrusion &amp; Threat Detection Stream</h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Live cyber telemetry stream capturing automated 403 blocks, path traversal, and malicious probes.</p>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
          <input type="text" id="filter_threats_search" onkeyup="filterTableRows('threatsTable', this.value, 'threats_page_info')" placeholder="Search threats (IP, path)..." style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.45rem 0.85rem; color: #f8fafc; font-size: 0.85rem; min-width: 220px;">
        </div>
      </div>

      <table class="admin-table" id="threatsTable">
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>Attacker IP</th>
            <th>Threat Classification</th>
            <th>Requested URI Path</th>
            <th>User Agent Signature</th>
            <th>Action Taken</th>
          </tr>
        </thead>
        <tbody id="threats_tbody">
          <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $l): ?>
              <tr class="paginated-row-threats">
                <td style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #94a3b8; white-space: nowrap;">
                  <?= htmlspecialchars($l['created_at']) ?>
                </td>
                <td>
                  <strong style="color: #ef4444; font-family: monospace; font-size: 0.88rem;"><?= htmlspecialchars($l['ip_address']) ?></strong>
                </td>
                <td>
                  <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem;"><?= htmlspecialchars($l['threat_type']) ?></span>
                </td>
                <td style="font-family: monospace; font-size: 0.8rem; color: #e2e8f0; max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($l['path']) ?>">
                  <?= htmlspecialchars($l['path']) ?>
                </td>
                <td style="font-size: 0.72rem; color: #64748b; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($l['user_agent']) ?>">
                  <?= htmlspecialchars($l['user_agent']) ?>
                </td>
                <td>
                  <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem;">BLOCKED 403</span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">No recent threat intrusion events detected.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Pagination for Threats -->
      <div id="threats_pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(148,163,184,0.1);">
        <div id="threats_page_info" style="font-size: 0.82rem; color: #64748b;">Showing <?= count($logs ?? []) ?> events</div>
        <div style="display: flex; gap: 0.35rem;" id="threats_page_btns"></div>
      </div>
    </div>
  </div>

  <!-- TAB 3: 15-Minute IP Lockouts -->
  <div id="pane_lockouts" class="security-tab-pane" style="display: none;">
    <div class="admin-table-wrap" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f87171; margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.4rem;">
            <span>🛑</span> Active 15-Minute IP Lockouts
          </h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Automated brute-force lockdown triggered by 5+ failed login attempts in 15 minutes.</p>
        </div>
        <span class="ts-badge ts-badge-pink"><?= count($activeLockouts) ?> Currently Locked</span>
      </div>

      <table class="admin-table" id="lockoutsTable">
        <thead>
          <tr>
            <th>Locked IP Address</th>
            <th>Reason &amp; Violation</th>
            <th style="text-align: center;">Failed Attempts</th>
            <th>Unlocks In</th>
            <th>Locked Since</th>
            <th style="text-align: right;">Manual Override</th>
          </tr>
        </thead>
        <tbody id="lockouts_tbody">
          <?php if (!empty($activeLockouts)): ?>
            <?php foreach ($activeLockouts as $lck): ?>
              <?php $remMin = max(1, ceil((strtotime($lck['locked_until']) - time()) / 60)); ?>
              <tr class="paginated-row-lockouts">
                <td>
                  <code style="color: #f87171; font-weight: 700; font-size: 0.9rem;"><?= htmlspecialchars($lck['ip_address']) ?></code>
                </td>
                <td style="font-size: 0.82rem; color: #94a3b8;">
                  <?= htmlspecialchars($lck['reason']) ?>
                </td>
                <td style="font-family: monospace; font-weight: 700; color: #f59e0b; text-align: center;">
                  <?= (int)$lck['failed_count'] ?>
                </td>
                <td style="font-size: 0.85rem; color: #fbbf24; font-weight: 700;">
                  <?= $remMin ?> mins remaining
                </td>
                <td style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #64748b;">
                  <?= htmlspecialchars($lck['created_at']) ?>
                </td>
                <td style="text-align: right;">
                  <form action="<?= url('/admin/security/unlock-ip') ?>" method="POST" style="display: inline;">
                    <input type="hidden" name="ip_address" value="<?= htmlspecialchars($lck['ip_address']) ?>">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; color: #34d399; border-color: rgba(16,185,129,0.4);">
                      Unlock 🔓
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                <i data-lucide="shield-check" style="width: 36px; height: 36px; color: #10b981; margin-bottom: 0.5rem; display: block; margin: 0 auto 0.5rem auto;"></i>
                No IP addresses are currently locked out by brute-force defense.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Pagination for Lockouts -->
      <div id="lockouts_pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(148,163,184,0.1);">
        <div id="lockouts_page_info" style="font-size: 0.82rem; color: #64748b;">Showing <?= count($activeLockouts) ?> locked IPs</div>
        <div style="display: flex; gap: 0.35rem;" id="lockouts_page_btns"></div>
      </div>
    </div>
  </div>

  <!-- TAB 4: Live Security Anomalies -->
  <div id="pane_anomalies" class="security-tab-pane" style="display: none;">
    <div class="admin-table-wrap" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #38bdf8; margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.4rem;">
            <span>🚨</span> Live Security Anomalies Telemetry Feed
          </h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Automated behavioral anomaly engine tracking comment flooding, rapid API hits, and credential surges.</p>
        </div>
        <span class="ts-badge ts-badge-cyan"><?= count($recentAnomalies) ?> Anomalies Tracked</span>
      </div>

      <table class="admin-table" id="anomaliesTable">
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>Event / Severity</th>
            <th>Actor &amp; Origin</th>
            <th>Anomaly Description</th>
            <th style="text-align: right;">Status &amp; Action</th>
          </tr>
        </thead>
        <tbody id="anomalies_tbody">
          <?php if (!empty($recentAnomalies)): ?>
            <?php foreach ($recentAnomalies as $anom): ?>
              <tr class="paginated-row-anomalies">
                <td style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #94a3b8; white-space: nowrap;">
                  <?= htmlspecialchars($anom['created_at']) ?>
                </td>
                <td>
                  <span class="ts-badge <?= in_array($anom['severity'] ?? '', ['high', 'critical']) ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="font-size: 0.68rem; text-transform: uppercase;">
                    <?= htmlspecialchars($anom['event_type']) ?>
                  </span>
                </td>
                <td>
                  <div style="color: #f8fafc; font-size: 0.82rem; font-weight: 600;"><?= htmlspecialchars($anom['username'] ?? 'Anonymous') ?></div>
                  <code style="font-size: 0.75rem; color: #94a3b8;"><?= htmlspecialchars($anom['ip_address']) ?></code>
                </td>
                <td style="font-size: 0.82rem; color: #cbd5e1; max-width: 320px;">
                  <?= htmlspecialchars($anom['description']) ?>
                </td>
                <td style="text-align: right;">
                  <?php if (empty($anom['is_resolved'])): ?>
                    <form action="<?= url('/admin/security/resolve-anomaly/' . $anom['id']) ?>" method="POST" style="display: inline;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.72rem; color: #34d399; border-color: rgba(16,185,129,0.3);">
                        Resolve ✓
                      </button>
                    </form>
                  <?php else: ?>
                    <span style="color: #64748b; font-size: 0.72rem; font-weight: 600;">✓ Resolved</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">
                <i data-lucide="check-circle" style="width: 36px; height: 36px; color: #10b981; margin-bottom: 0.5rem; display: block; margin: 0 auto 0.5rem auto;"></i>
                No security anomalies recorded. System behavior is nominal.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Pagination for Anomalies -->
      <div id="anomalies_pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(148,163,184,0.1);">
        <div id="anomalies_page_info" style="font-size: 0.82rem; color: #64748b;">Showing <?= count($recentAnomalies) ?> anomaly records</div>
        <div style="display: flex; gap: 0.35rem;" id="anomalies_page_btns"></div>
      </div>
    </div>
  </div>

  <!-- Modal: Enforce Rule -->
  <div id="ruleModal" class="admin-modal-overlay">
    <div class="admin-modal-box modal-rose" style="max-width: 500px;">
      <div class="admin-modal-header">
        <h3 class="admin-modal-title" style="color: #f43f5e;"><i data-lucide="shield-alert"></i> Enforce Firewall Rule</h3>
        <button type="button" onclick="closeRuleModal()" class="admin-modal-close">✕</button>
      </div>

      <form action="<?= url('/admin/firewall/rules/save') ?>" method="POST">
        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label">Rule Target Type</label>
          <select name="rule_type" class="admin-modal-select">
            <option value="ip">Single IP Address (e.g. 192.168.1.50)</option>
            <option value="user_agent">Malicious User Agent Substring (e.g. sqlmap, nikto, curl)</option>
          </select>
        </div>

        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label">Target Value</label>
          <input type="text" name="target_value" placeholder="e.g. 203.0.113.195 or python-requests" class="admin-modal-input" required>
        </div>

        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label">Action</label>
          <select name="action" class="admin-modal-select">
            <option value="block">Block Immediately (HTTP 403 Forbidden)</option>
            <option value="allow">Allow List (Bypass WAF)</option>
          </select>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label class="admin-modal-label">Reason / Security Note</label>
          <input type="text" name="reason" placeholder="e.g. Repeated credential stuffing attempts" class="admin-modal-input">
        </div>

        <div class="admin-modal-footer">
          <button type="button" onclick="closeRuleModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
          <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #ef4444; border-color: #ef4444; font-weight: 700;">Enforce Rule 🛡️</button>
        </div>
      </form>
    </div>
  </div>

  <script>
  const activeSecurityTab = '<?= htmlspecialchars($activeTab) ?>';

  function switchSecurityTab(tabName) {
    document.querySelectorAll('.security-tab-pane').forEach(p => p.style.display = 'none');
    document.querySelectorAll('[id^="tab_btn_"]').forEach(b => {
      b.classList.remove('ts-btn-primary');
      b.classList.add('ts-btn-outline');
    });

    const targetPane = document.getElementById('pane_' + tabName);
    const targetBtn = document.getElementById('tab_btn_' + tabName);
    if (targetPane) targetPane.style.display = 'block';
    if (targetBtn) {
      targetBtn.classList.remove('ts-btn-outline');
      targetBtn.classList.add('ts-btn-primary');
    }

    if (history.pushState) {
      const newUrl = new URL(window.location);
      newUrl.searchParams.set('tab', tabName);
      window.history.pushState({}, '', newUrl);
    }
  }

  // Generic Client-Side Table Pagination Engine
  function paginateTable(rowClass, infoId, btnsId, pageSize = 10) {
    const rows = Array.from(document.querySelectorAll('.' + rowClass));
    if (!rows.length) return;

    let currentPage = 1;
    const totalPages = Math.ceil(rows.length / pageSize);

    function renderPage(page) {
      currentPage = page;
      const start = (page - 1) * pageSize;
      const end = start + pageSize;

      rows.forEach((r, idx) => {
        if (r.dataset.filteredOut === 'true') {
          r.style.display = 'none';
        } else {
          r.style.display = (idx >= start && idx < end) ? '' : 'none';
        }
      });

      const infoEl = document.getElementById(infoId);
      if (infoEl) {
        infoEl.innerHTML = `Showing <strong>${start + 1}–${Math.min(end, rows.length)}</strong> of <strong>${rows.length}</strong> records (Page ${page}/${totalPages})`;
      }

      const btnsEl = document.getElementById(btnsId);
      if (btnsEl) {
        btnsEl.innerHTML = '';
        if (totalPages <= 1) return;

        // Prev button
        const prevBtn = document.createElement('button');
        prevBtn.className = 'ts-btn ts-btn-outline ts-btn-sm';
        prevBtn.innerText = '← Prev';
        prevBtn.disabled = (currentPage === 1);
        prevBtn.style.padding = '0.2rem 0.5rem';
        prevBtn.style.fontSize = '0.75rem';
        prevBtn.onclick = () => renderPage(Math.max(1, currentPage - 1));
        btnsEl.appendChild(prevBtn);

        // Page number pills
        for (let p = 1; p <= totalPages; p++) {
          if (p === 1 || p === totalPages || Math.abs(p - currentPage) <= 1) {
            const pBtn = document.createElement('button');
            pBtn.className = 'ts-btn ts-btn-sm ' + (p === currentPage ? 'ts-btn-primary' : 'ts-btn-outline');
            pBtn.innerText = p;
            pBtn.style.padding = '0.2rem 0.5rem';
            pBtn.style.fontSize = '0.75rem';
            pBtn.style.minWidth = '28px';
            pBtn.onclick = () => renderPage(p);
            btnsEl.appendChild(pBtn);
          }
        }

        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = 'ts-btn ts-btn-outline ts-btn-sm';
        nextBtn.innerText = 'Next →';
        nextBtn.disabled = (currentPage === totalPages);
        nextBtn.style.padding = '0.2rem 0.5rem';
        nextBtn.style.fontSize = '0.75rem';
        nextBtn.onclick = () => renderPage(Math.min(totalPages, currentPage + 1));
        btnsEl.appendChild(nextBtn);
      }
    }

    renderPage(1);
  }

  // Filter Table by Text Search
  function filterTableRows(tableId, query, infoId) {
    const q = query.toLowerCase().trim();
    const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
    let matched = 0;
    rows.forEach(r => {
      const txt = r.innerText.toLowerCase();
      if (!q || txt.includes(q)) {
        r.style.display = '';
        r.dataset.filteredOut = 'false';
        matched++;
      } else {
        r.style.display = 'none';
        r.dataset.filteredOut = 'true';
      }
    });
    const infoEl = document.getElementById(infoId);
    if (infoEl) {
      infoEl.innerHTML = q ? `Found <strong>${matched}</strong> matching records for "<em>${query}</em>"` : `Showing all records`;
    }
  }

  function openNewRuleModal() {
    document.getElementById('ruleModal').style.display = 'flex';
  }
  function closeRuleModal() {
    document.getElementById('ruleModal').style.display = 'none';
  }

  // Initialize on page load
  document.addEventListener('DOMContentLoaded', () => {
    switchSecurityTab(activeSecurityTab);
    paginateTable('paginated-row-rules', 'rules_page_info', 'rules_page_btns', 10);
    paginateTable('paginated-row-threats', 'threats_page_info', 'threats_page_btns', 10);
    paginateTable('paginated-row-lockouts', 'lockouts_page_info', 'lockouts_page_btns', 10);
    paginateTable('paginated-row-anomalies', 'anomalies_page_info', 'anomalies_page_btns', 10);
  });
  </script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
