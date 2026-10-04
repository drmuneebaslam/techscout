<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

  <!-- Topbar Header -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-green">CREATOR ECONOMY &bull; REVENUE &amp; DISBURSEMENTS</span>
        <span style="font-size: 0.8rem; color: #64748b;">Author Payout Approvals, Editorial Grants, Milestone Bounties &amp; Revenue Sharing</span>
      </div>
      <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        CMS Revenue &amp; Creator Rewards Hub
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/revenue/export') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.3);">
        <i data-lucide="download"></i> Export Payout Ledger CSV
      </a>
      <button type="button" onclick="openGrantRewardModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 700;">
        <i data-lucide="gift"></i> Grant Author Reward ⚡
      </button>
    </div>
  </div>

  <!-- Telemetry Overview Bar -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Disbursed</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        $<?= number_format($stats['total_paid'], 2) ?>
      </div>
      <div style="font-size: 0.75rem; color: #34d399; margin-top: 0.2rem;">Processed author payouts</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Pending Payouts</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        $<?= number_format($stats['total_pending'], 2) ?>
      </div>
      <div style="font-size: 0.75rem; color: #fbbf24; margin-top: 0.2rem;">Awaiting admin approval</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Credits in Circulation</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($stats['credits_pool'], 2) ?>
      </div>
      <div style="font-size: 0.75rem; color: #7dd3fc; margin-top: 0.2rem;">Author promotional credit balance</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Monetized Authors</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($stats['monetized_count']) ?>
      </div>
      <div style="font-size: 0.75rem; color: #f472b6; margin-top: 0.2rem;">Active revenue share creators</div>
    </div>
  </div>

  <!-- Revenue Tabs Strip -->
  <div class="admin-table-wrap" style="padding: 0.5rem; margin-bottom: 1.5rem; overflow-x: auto;">
    <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; min-width: max-content;">
      <?php
      $appliedCount = count(array_filter($monetizationList ?? [], fn($m) => ($m['monetization_status'] ?? '') === 'applied'));
      $revTabs = [
        'payouts' => ['label' => 'Payout Requests & Queue', 'icon' => 'credit-card', 'badge' => count($payouts)],
        'pool' => ['label' => 'Royalty Pool Distribution', 'icon' => 'pie-chart', 'badge' => '⚡ Distribute'],
        'ledger' => ['label' => 'Universal Financial Ledger', 'icon' => 'book-open', 'badge' => $stats['ledger_count'] ?? count($ledger ?? [])],
        'referrals' => ['label' => 'Ambassador Program', 'icon' => 'users', 'badge' => count($ambassadorCommissions ?? [])],
        'monetization' => ['label' => 'Creator Monetization Partners', 'icon' => 'award', 'badge' => $appliedCount > 0 ? "{$appliedCount} Pending" : count($monetizationList ?? [])],
        'grant' => ['label' => 'Credit / Deduct Balance', 'icon' => 'sliders-horizontal', 'badge' => null],
        'rewards' => ['label' => 'Legacy Reward Logs', 'icon' => 'history', 'badge' => count($rewards)],
        'settings' => ['label' => 'Monetization Parameters', 'icon' => 'sliders', 'badge' => null]
      ];
      ?>

      <?php foreach ($revTabs as $k => $t): ?>
        <button type="button" onclick="switchRevTab('<?= $k ?>')" id="revtab_<?= $k ?>" class="ts-btn ts-btn-sm <?= $activeTab === $k ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="<?= $t['icon'] ?>" style="width: 14px; height: 14px;"></i>
          <span><?= $t['label'] ?></span>
          <?php if ($t['badge'] !== null): ?>
            <span class="ts-badge" style="font-size: 0.68rem; padding: 0.1rem 0.4rem; background: rgba(255,255,255,0.1);"><?= $t['badge'] ?></span>
          <?php endif; ?>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 1. PAYOUT REQUESTS & LEDGER -->
  <!-- ========================================================================= -->
  <div class="rev-pane" id="revpane_payouts" style="<?= $activeTab === 'payouts' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap">
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0;">💳 Author Payout Requests &amp; Disbursal Ledger</h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Review, approve, adjust processing fees, assign transaction references, or reject withdrawal claims.</p>
        </div>

        <!-- Status Filter Buttons -->
        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
          <button type="button" onclick="filterPayouts('all')" class="ts-btn ts-btn-sm ts-btn-primary pfilter-btn" id="pfbtn-all">All (<?= count($payouts) ?>)</button>
          <button type="button" onclick="filterPayouts('pending')" class="ts-btn ts-btn-sm ts-btn-outline pfilter-btn" id="pfbtn-pending" style="color: #f59e0b;">Pending ⏳</button>
          <button type="button" onclick="filterPayouts('processing')" class="ts-btn ts-btn-sm ts-btn-outline pfilter-btn" id="pfbtn-processing" style="color: #38bdf8;">Processing 🔄</button>
          <button type="button" onclick="filterPayouts('paid')" class="ts-btn ts-btn-sm ts-btn-outline pfilter-btn" id="pfbtn-paid" style="color: #10b981;">Paid ✅</button>
          <button type="button" onclick="filterPayouts('rejected')" class="ts-btn ts-btn-sm ts-btn-outline pfilter-btn" id="pfbtn-rejected" style="color: #ef4444;">Rejected ❌</button>
        </div>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Author Name &amp; Email</th>
            <th>Gross Claim</th>
            <th>Fee &amp; Net Amount</th>
            <th>Method &amp; Destination</th>
            <th>Status</th>
            <th>Tx Reference</th>
            <th>Dates</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="payoutsTbody">
          <?php if (!empty($payouts)): ?>
            <?php foreach ($payouts as $p): ?>
              <?php 
                $fee = (float)($p['fee_deducted'] ?? 0);
                $rawNet = isset($p['net_amount']) && $p['net_amount'] !== null ? (float)$p['net_amount'] : null;
                $net = ($rawNet !== null && $rawNet > 0) ? $rawNet : max(0, (float)($p['amount_usd'] ?? 0) - $fee);
              ?>
              <tr class="payout-row" data-status="<?= htmlspecialchars($p['status']) ?>">
                <td>
                  <div style="font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($p['display_name']) ?></div>
                  <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem; font-family: var(--admin-font-mono);"><?= htmlspecialchars($p['user_email']) ?></div>
                </td>
                <td>
                  <div style="font-size: 1.05rem; font-weight: 800; color: #f8fafc; font-family: var(--admin-font-mono);">
                    $<?= number_format((float)$p['amount_usd'], 2) ?>
                  </div>
                  <div style="font-size: 0.72rem; color: #64748b;"><?= number_format((float)$p['credits_deducted'], 2) ?> credits</div>
                </td>
                <td>
                  <div style="font-size: 1.05rem; font-weight: 800; color: #10b981; font-family: var(--admin-font-mono);">
                    $<?= number_format($net, 2) ?>
                  </div>
                  <div style="font-size: 0.72rem; color: #f59e0b;">Tax/Fee: -$<?= number_format($fee, 2) ?></div>
                </td>
                <td>
                  <div style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span class="ts-badge ts-badge-indigo" style="font-size: 0.68rem;"><?= strtoupper(htmlspecialchars($p['payout_method'])) ?></span>
                  </div>
                  <div style="font-size: 0.78rem; color: #cbd5e1; font-family: var(--admin-font-mono); margin-top: 0.25rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($p['payout_address']) ?>">
                    <?= htmlspecialchars($p['payout_address']) ?>
                  </div>
                </td>
                <td>
                  <span class="ts-badge <?= $p['status'] === 'paid' ? 'ts-badge-green' : ($p['status'] === 'processing' || $p['status'] === 'approved' ? 'ts-badge-cyan' : ($p['status'] === 'pending' ? 'ts-badge-amber' : 'strike-badge')) ?>">
                    <?= strtoupper(htmlspecialchars($p['status'])) ?>
                  </span>
                  <?php if (!empty($p['admin_notes'])): ?>
                    <div style="font-size: 0.72rem; color: #f87171; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 0.25rem;" title="<?= htmlspecialchars($p['admin_notes']) ?>">
                      Note: <?= htmlspecialchars($p['admin_notes']) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td style="font-family: var(--admin-font-mono); font-size: 0.78rem; color: #94a3b8;">
                  <?= !empty($p['transaction_reference']) ? htmlspecialchars($p['transaction_reference']) : '<span style="color:#64748b;">Pending Tx</span>' ?>
                </td>
                <td style="font-size: 0.78rem; color: #64748b;">
                  <div>Req: <?= \App\Core\DateHelper::render($p['created_at'], 'M j, Y', 'date') ?></div>
                  <?php if (!empty($p['processed_at'])): ?>
                    <div style="color: #38bdf8;">Proc: <?= \App\Core\DateHelper::render($p['processed_at'], 'M j, Y', 'date') ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; align-items: center;">
                    <button type="button" onclick="openProcessPayoutModal(<?= htmlspecialchars(json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #38bdf8;" title="Edit & Process Payout">
                      ⚡ Process
                    </button>
                    <form action="<?= url('/admin/revenue/delete-payout/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Delete payout record #<?= (int)$p['id'] ?>? Any pending credits will be refunded back to the author.');" style="display: inline;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #ef4444;" title="Delete Payout">
                        🗑
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8" style="text-align: center; padding: 2.5rem; color: #64748b;">No payout requests recorded.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>



  <!-- ========================================================================= -->
  <!-- 1B. CREATOR ROYALTY POOL DISTRIBUTION ENGINE -->
  <!-- ========================================================================= -->
  <div class="rev-pane" id="revpane_pool" style="<?= $activeTab === 'pool' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 1.75rem; margin-bottom: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1.25rem;">
        <div>
          <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-cyan">PROPORTIONAL READERSHIP ROYALTIES</span>
            <span style="font-size: 0.8rem; color: #94a3b8;">Fluctuating Base CPM ($0.80) &bull; Dynamic Pool Surplus &bull; Platform Reserve Safeguard</span>
          </div>
          <h2 style="font-size: 1.45rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            ⚡ Creator Royalty Pool Distribution Engine
          </h2>
          <p style="color: #94a3b8; font-size: 0.88rem; margin: 0.35rem 0 0 0; max-width: 850px;">
            Input platform earnings for the period (week/month). A configurable percentage is reserved for platform maintenance, and the remainder forms the creator pool. Royalties are calculated by verified reads at base CPM ($0.80 USD). <strong>Safeguard:</strong> Payouts scale strictly by qualified reads so lone authors never drain the entire pool, retaining unearned surplus in platform treasury.
          </p>
        </div>
        <div style="text-align: right;">
          <span class="ts-badge ts-badge-green" style="font-size: 0.75rem; padding: 0.3rem 0.75rem;">
            ✓ Double-Entry Ledger Enforced
          </span>
        </div>
      </div>

      <form action="<?= url('/admin/revenue/distribute-pool') ?>" method="POST" id="royaltyPoolForm" onsubmit="return confirmPoolDistribution(event)">
        <!-- 1. Distribution Parameter Controls -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 210px), 1fr)); gap: 1.25rem; margin-bottom: 1.75rem; background: rgba(15,23,42,0.6); padding: 1.25rem; border-radius: 12px; border: 1px solid rgba(148,163,184,0.12);">
          <div>
            <label class="admin-modal-label" style="display: block; font-size: 0.8rem; color: #cbd5e1; font-weight: 700; margin-bottom: 0.35rem;">
              Platform Gross Earnings ($ USD) <span style="color: #ef4444;">*</span>
            </label>
            <input type="number" step="0.01" min="1" name="gross_revenue" id="pool_gross_revenue" value="1000.00" class="admin-modal-input" style="font-size: 1.1rem; font-weight: 800; color: #10b981;" oninput="recalculateRoyaltyPool()" required>
            <span style="font-size: 0.72rem; color: #64748b;">Total platform revenue this period</span>
          </div>

          <div>
            <label class="admin-modal-label" style="display: block; font-size: 0.8rem; color: #cbd5e1; font-weight: 700; margin-bottom: 0.35rem;">
              Distribution Cycle
            </label>
            <select name="period_cycle" id="pool_period_cycle" class="admin-modal-select" onchange="updatePeriodLabel(this.value)">
              <option value="this_month" selected>This Month</option>
              <option value="this_week">This Week</option>
              <option value="bi_weekly">Bi-Weekly (14 Days)</option>
              <option value="quarterly">Quarterly Pool</option>
              <option value="custom">Custom Date Range</option>
            </select>
            <span style="font-size: 0.72rem; color: #64748b;">Cadence of distribution</span>
          </div>

          <div>
            <label class="admin-modal-label" style="display: block; font-size: 0.8rem; color: #cbd5e1; font-weight: 700; margin-bottom: 0.35rem;">
              Period Identifier Label <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="period_label" id="pool_period_label" value="<?= date('F Y') ?> Author Royalty Pool" class="admin-modal-input" required>
            <span style="font-size: 0.72rem; color: #64748b;">Stamped on creators' ledger entries</span>
          </div>

          <div>
            <label class="admin-modal-label" style="display: block; font-size: 0.8rem; color: #cbd5e1; font-weight: 700; margin-bottom: 0.35rem;">
              Platform Maintenance Cut (%) <span style="color: #ef4444;">*</span>
            </label>
            <input type="number" step="1" min="0" max="95" name="platform_cut_pct" id="pool_platform_cut_pct" value="25" class="admin-modal-input" style="font-weight: 700; color: #38bdf8;" oninput="recalculateRoyaltyPool()" required>
            <span style="font-size: 0.72rem; color: #64748b;">Retained for server infra &amp; ops</span>
          </div>

          <div>
            <label class="admin-modal-label" style="display: block; font-size: 0.8rem; color: #cbd5e1; font-weight: 700; margin-bottom: 0.35rem;">
              Base CPM ($ USD per 1k views) <span style="color: #ef4444;">*</span>
            </label>
            <input type="number" step="0.05" min="0.10" name="base_cpm" id="pool_base_cpm" value="0.80" class="admin-modal-input" style="font-weight: 700; color: #f59e0b;" oninput="recalculateRoyaltyPool()" required>
            <span style="font-size: 0.72rem; color: #64748b;">Base guarantee ($0.80 / 1k reads)</span>
          </div>

          <div>
            <label class="admin-modal-label" style="display: block; font-size: 0.8rem; color: #cbd5e1; font-weight: 700; margin-bottom: 0.35rem;">
              Max CPM Dynamic Cap ($ USD)
            </label>
            <input type="number" step="0.10" min="0.80" id="pool_max_cpm" value="2.50" class="admin-modal-input" style="font-weight: 700; color: #a855f7;" oninput="recalculateRoyaltyPool()">
            <span style="font-size: 0.72rem; color: #64748b;">Ceiling to prevent single-author drain</span>
          </div>
        </div>

        <!-- 2. Mathematical Telemetry & Safeguard Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 1rem; margin-bottom: 2rem;">
          <div style="background: rgba(30,41,59,0.5); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1rem;">
            <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Gross Revenue</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #f8fafc; font-family: 'Outfit', sans-serif; margin-top: 0.2rem;" id="disp_gross_rev">$1,000.00</div>
            <div style="font-size: 0.7rem; color: #64748b;">100% Platform Period Gross</div>
          </div>

          <div style="background: rgba(56,189,248,0.06); border: 1px solid rgba(56,189,248,0.2); border-radius: 10px; padding: 1rem;">
            <div style="font-size: 0.72rem; color: #38bdf8; font-weight: 700; text-transform: uppercase;">Maintenance Cut (25%)</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #38bdf8; font-family: 'Outfit', sans-serif; margin-top: 0.2rem;" id="disp_maintenance_cut">$250.00</div>
            <div style="font-size: 0.7rem; color: #7dd3fc;">Platform operations reserve</div>
          </div>

          <div style="background: rgba(168,85,247,0.06); border: 1px solid rgba(168,85,247,0.2); border-radius: 10px; padding: 1rem;">
            <div style="font-size: 0.72rem; color: #c084fc; font-weight: 700; text-transform: uppercase;">Creator Pool Available</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #c084fc; font-family: 'Outfit', sans-serif; margin-top: 0.2rem;" id="disp_creator_pool">$750.00</div>
            <div style="font-size: 0.7rem; color: #d8b4fe;">Max allocatable to authors</div>
          </div>

          <div style="background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.2); border-radius: 10px; padding: 1rem;">
            <div style="font-size: 0.72rem; color: #10b981; font-weight: 700; text-transform: uppercase;">Total to Disburse</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #10b981; font-family: 'Outfit', sans-serif; margin-top: 0.2rem;" id="disp_total_disburse">$0.00</div>
            <div style="font-size: 0.7rem; color: #34d399;" id="disp_authors_count">0 authors included</div>
          </div>

          <div style="background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.2); border-radius: 10px; padding: 1rem;">
            <div style="font-size: 0.72rem; color: #fbbf24; font-weight: 700; text-transform: uppercase;">Platform Reserve Retained</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #f59e0b; font-family: 'Outfit', sans-serif; margin-top: 0.2rem;" id="disp_reserve_retained">$750.00</div>
            <div style="font-size: 0.7rem; color: #fcd34d;">Protected unallocated treasury</div>
          </div>

          <div style="background: rgba(30,41,59,0.5); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1rem;">
            <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Effective Avg CPM</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #f8fafc; font-family: 'Outfit', sans-serif; margin-top: 0.2rem;" id="disp_effective_cpm">$0.80</div>
            <div style="font-size: 0.7rem; color: #64748b;" id="disp_total_views_sum">Across 0 total reads</div>
          </div>
        </div>

        <!-- 3. Pre-Distribution Review & Editable Adjustment Table -->
        <div style="margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
              <h3 style="font-size: 1.1rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif;">
                📋 Pre-Disbursal Review Table &bull; Author Allocation Breakdown
              </h3>
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">Proportional Distribution Engine</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
              <!-- 1-Click Filter: Only Monetized Authors -->
              <button type="button" onclick="filterPoolMonetizedOnly()" id="btn_pool_filter_monetized" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.78rem; padding: 0.35rem 0.75rem; background: linear-gradient(135deg, #10b981, #059669); border: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;" title="1-Click: Deselect all non-monetized authors so list only shows monetized">
                <i data-lucide="award" style="width: 14px; height: 14px;"></i> 💎 Show Monetized Only (Deselect Non-Monetized)
              </button>

              <!-- Admin Permission Toggle: Show Non-Monetized Authors -->
              <button type="button" onclick="toggleAdminPermissionNonMonetized()" id="btn_pool_filter_all" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem; padding: 0.35rem 0.75rem; color: #f59e0b; border-color: rgba(245,158,11,0.4); display: inline-flex; align-items: center; gap: 0.35rem;" title="Unlock non-monetized authors by admin permission">
                <i data-lucide="shield-alert" style="width: 14px; height: 14px;"></i> 🔓 Show Non-Monetized (Admin Permission)
              </button>

              <button type="button" onclick="deselectNonMonetizedPoolAuthors()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.6rem; color: #cbd5e1;" title="Deselect all non-monetized authors">
                Deselect Non-Monetized
              </button>
              <button type="button" onclick="selectOnlyMonetizedPoolAuthors()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.6rem; color: #10b981;" title="Select only monetized authors">
                Select Monetized
              </button>
              <button type="button" onclick="selectAllPoolAuthors(true)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">Select All</button>
              <button type="button" onclick="selectAllPoolAuthors(false)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">Deselect All</button>
              <button type="button" onclick="resetCalculatedPoolAmounts()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.6rem; color: #38bdf8;">↺ Reset</button>
            </div>
          </div>

          <!-- Admin Permission Notice Banner -->
          <div id="admin_non_monetized_banner" style="display: none; background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.35); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.82rem; color: #fbbf24; align-items: center; justify-content: space-between; gap: 1rem;">
            <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.6rem;">
              <i data-lucide="shield-alert" style="width: 18px; height: 18px; color: #f59e0b; flex-shrink: 0;"></i>
              <span><strong>Admin Permission Active:</strong> Non-monetized authors are currently visible. By administrative permission, you may include them in this distribution cycle or deselect them.</span>
            </div>
            <button type="button" onclick="filterPoolMonetizedOnly()" class="ts-btn ts-btn-sm" style="background: rgba(245,158,11,0.25); color: #fef08a; border: 1px solid rgba(245,158,11,0.5); font-size: 0.75rem; padding: 0.25rem 0.65rem; white-space: nowrap;">
              Hide &amp; Exclude Non-Monetized
            </button>
          </div>

          <div style="overflow-x: auto; border: 1px solid var(--admin-border-subtle); border-radius: 10px;">
            <table class="admin-table" id="royaltyPoolTable" style="margin: 0; font-size: 0.85rem;">
              <thead>
                <tr style="background: rgba(15,23,42,0.9);">
                  <th style="width: 44px; text-align: center;">
                    <input type="checkbox" id="pool_select_all" checked onchange="selectAllPoolAuthors(this.checked)" style="accent-color: #10b981; transform: scale(1.15); cursor: pointer;" title="Select / Deselect All">
                  </th>
                  <th>Creator Identity</th>
                  <th>Monetization Status</th>
                  <th style="text-align: right;" title="Active reading sessions with >=10 seconds dwell time">Verified Reads (10s+) ⏱️</th>
                  <th style="text-align: right;" title="Proportional share of verified readership">Readership Share (%)</th>
                  <th style="text-align: right;" title="Effective calculated CPM for this author">Effective CPM</th>
                  <th style="width: 160px; text-align: right;">Total Disbursal ($ USD)</th>
                  <th style="min-width: 200px;">Audit Memo</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  // Filter monetized or approved authors first, or authors with views
                  $eligiblePoolAuthors = array_filter($authors, function($a) {
                      return !empty($a['is_monetized']) || 
                             ($a['monetization_status'] ?? '') === 'approved' || 
                             ($a['monetization_status'] ?? '') === 'eligible' || 
                             (int)($a['verified_10s_reads'] ?? $a['total_views'] ?? 0) > 0;
                  });
                  if (empty($eligiblePoolAuthors)) {
                      $eligiblePoolAuthors = array_slice($authors, 0, 15);
                  }
                ?>
                <?php if (!empty($eligiblePoolAuthors)): ?>
                  <?php foreach ($eligiblePoolAuthors as $pAuth): ?>
                    <?php
                      $authId = (int)$pAuth['id'];
                      $verified10s = (int)($pAuth['verified_10s_reads'] ?? 0);
                      $authViews = $verified10s > 0 ? $verified10s : (int)($pAuth['total_views'] ?? 0);
                      $isMon = !empty($pAuth['is_monetized']) || ($pAuth['monetization_status'] ?? '') === 'approved';
                      $email = $pAuth['user_email'] ?? $pAuth['email'] ?? '';
                    ?>
                    <tr class="pool-author-row" data-author-id="<?= $authId ?>" data-views="<?= $authViews ?>" data-monetized="<?= $isMon ? '1' : '0' ?>" style="border-bottom: 1px solid rgba(148,163,184,0.06);">
                      <td style="text-align: center;">
                        <input type="checkbox" name="payouts[<?= $authId ?>][include]" value="1" <?= $isMon ? 'checked' : '' ?> class="pool-author-chk" onchange="recalculateRoyaltyPool()" style="accent-color: #10b981; transform: scale(1.15); cursor: pointer;">
                        <input type="hidden" name="payouts[<?= $authId ?>][author_id]" value="<?= $authId ?>">
                        <input type="hidden" name="payouts[<?= $authId ?>][views]" value="<?= $authViews ?>">
                      </td>
                      <td>
                        <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.6rem;">
                          <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #0284c7, #6366f1); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; color: #ffffff;">
                            <?= strtoupper(substr($pAuth['display_name'] ?: 'A', 0, 1)) ?>
                          </div>
                          <div>
                            <div style="font-weight: 700; color: #f8fafc; font-size: 0.88rem;">
                              <?= htmlspecialchars($pAuth['display_name']) ?>
                            </div>
                            <div style="font-size: 0.72rem; color: #94a3b8;">
                              @<?= htmlspecialchars($pAuth['slug']) ?> &bull; <?= htmlspecialchars($email) ?>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td>
                        <?php if ($isMon): ?>
                          <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">
                            💎 Partner Monetized
                          </span>
                        <?php elseif (($pAuth['monetization_status'] ?? '') === 'eligible'): ?>
                          <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">
                            Eligible Creator
                          </span>
                        <?php else: ?>
                          <span class="ts-badge" style="font-size: 0.68rem; padding: 0.15rem 0.5rem; background: rgba(148,163,184,0.15); color: #94a3b8;">
                            Standard Author
                          </span>
                        <?php endif; ?>
                      </td>
                      <td style="text-align: right; font-family: var(--admin-font-mono); font-weight: 700; color: #38bdf8;">
                        <?= number_format($authViews) ?>
                        <div style="font-size: 0.68rem; color: #64748b; font-weight: 400;">≥10s verified</div>
                      </td>
                      <td style="text-align: right; font-family: var(--admin-font-mono); color: #cbd5e1;" class="row-view-share">
                        0.0%
                      </td>
                      <td style="text-align: right; font-family: var(--admin-font-mono); color: #38bdf8;" class="row-pool-cpm">
                        $0.00 CPM
                      </td>
                      <td style="text-align: right;">
                        <input type="number" step="0.01" min="0" name="payouts[<?= $authId ?>][amount]" id="pool_amt_<?= $authId ?>" value="0.00" class="admin-modal-input pool-editable-amount" style="width: 120px; text-align: right; font-weight: 800; color: #10b981; padding: 0.35rem 0.5rem; font-size: 0.92rem;" oninput="updateManualDisbursalTotal()">
                      </td>
                      <td>
                        <input type="text" name="payouts[<?= $authId ?>][memo]" id="pool_memo_<?= $authId ?>" value="Period Royalty Distribution (<?= number_format($authViews) ?> views)" class="admin-modal-input" style="font-size: 0.78rem; padding: 0.35rem 0.6rem; color: #cbd5e1;">
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="8" style="text-align: center; color: #64748b; padding: 2rem;">No authors registered yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 4. Execution Call to Action & Summary Box -->
        <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; padding: 1.25rem 1.75rem; flex-wrap: wrap; gap: 1.25rem;">
          <div>
            <div style="flex-wrap: wrap; align-items: center; font-weight: 800; color: #10b981; font-size: 1.05rem; display: flex; align-items: center; gap: 0.4rem;">
              <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i> Ready for Distribution
            </div>
            <div style="font-size: 0.82rem; color: #94a3b8; margin-top: 0.2rem;" id="cta_distribution_summary">
              Disbursing $0.00 USD across 0 creators. Platform retains $250.00 maintenance cut and $750.00 pool reserve.
            </div>
          </div>

          <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.75rem; align-items: center;">
            <button type="button" onclick="recalculateRoyaltyPool()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
              <i data-lucide="refresh-cw"></i> Recalculate
            </button>
            <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #0284c7); padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(16,185,129,0.3);">
              <i data-lucide="send"></i> Execute &amp; Disburse Royalty Pool ⚡
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 1C. AMBASSADOR & GROWTH REFERRAL PROGRAM REWARDS -->
  <!-- ========================================================================= -->
  <div class="rev-pane" id="revpane_referrals" style="<?= $activeTab === 'referrals' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap">
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-purple">GROWTH AMBASSADOR ENGINE</span>
            <span style="font-size: 0.8rem; color: #94a3b8;">5% Commission Pool on Referred Creators' Royalties</span>
          </div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            🤝 Creator Referral Commissions Ledger
          </h3>
        </div>
      </div>

      <!-- Referral Metrics Strip -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; padding: 1.25rem 1.5rem; background: rgba(15,23,42,0.4); border-bottom: 1px solid var(--admin-border-subtle);">
        <div>
          <div style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase;">Total Ambassador Payouts</div>
          <div style="font-size: 1.3rem; font-weight: 800; color: #a855f7; font-family: monospace;">$<?= number_format((float)($stats['referral_commissions_paid'] ?? 0), 2) ?></div>
        </div>
        <div>
          <div style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase;">Total Commission Events</div>
          <div style="font-size: 1.3rem; font-weight: 800; color: #cbd5e1; font-family: monospace;"><?= count($ambassadorCommissions ?? []) ?></div>
        </div>
      </div>

      <div style="overflow-x: auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Ledger Ref</th>
              <th>Ambassador Recipient</th>
              <th>Earning Referred Creator</th>
              <th>Commission (5%)</th>
              <th>Trigger Event</th>
              <th>Date (AM/PM)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($ambassadorCommissions)): ?>
              <?php foreach ($ambassadorCommissions as $comm): ?>
                <tr>
                  <td>
                    <span style="font-family: monospace; font-size: 0.78rem; font-weight: 700; color: #38bdf8; background: rgba(56,189,248,0.1); padding: 0.2rem 0.45rem; border-radius: 4px;">
                      <?= htmlspecialchars($comm['ledger_reference_no']) ?>
                    </span>
                  </td>
                  <td>
                    <strong style="color: #f8fafc; font-size: 0.88rem;"><?= htmlspecialchars($comm['referrer_name']) ?></strong>
                  </td>
                  <td>
                    <span style="color: #38bdf8; font-weight: 600;"><?= htmlspecialchars($comm['earner_name'] ?: 'Referred Author') ?></span>
                  </td>
                  <td>
                    <strong style="color: #10b981; font-family: monospace; font-size: 0.95rem;">+$<?= number_format((float)$comm['amount_usd'], 2) ?></strong>
                  </td>
                  <td style="color: #cbd5e1; font-size: 0.82rem;">
                    <?= htmlspecialchars($comm['source_event']) ?>
                  </td>
                  <td style="font-size: 0.76rem; color: #94a3b8; font-family: monospace; white-space: nowrap;">
                    <?= \App\Core\DateHelper::formatDateTime($comm['created_at']) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                  <i data-lucide="users" style="width: 36px; height: 36px; margin-bottom: 0.5rem;"></i>
                  <p style="margin: 0;">No referral commissions recorded yet.</p>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 1B. CREATOR MONETIZATION PARTNER APPLICATIONS & CRITERIA REVIEW -->
  <!-- ========================================================================= -->
  <div class="rev-pane" id="revpane_monetization" style="<?= $activeTab === 'monetization' ? 'display:block;' : 'display:none;' ?>">
    <!-- Monetization Sub-navigation Bar -->
    <div class="admin-table-wrap" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid #38bdf8;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-cyan">PARTNER ECONOMY &bull; EDITORIAL REVIEW</span>
            <span style="font-size: 0.8rem; color: #94a3b8;">Criteria: 1,000 Followers &bull; 100k Qualified Reads (&ge;10s) &bull; 5 Publications &bull; &ge;70% Quality</span>
          </div>
          <h3 style="font-size: 1.35rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            💎 Creator Partner Monetization Operations Center
          </h3>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
          <!-- Live Search Input -->
          <div style="position: relative; width: 220px;">
            <input type="text" id="monetizeSearchInput" oninput="searchMonetizationTable()" placeholder="Search creator name/handle..." style="width: 100%; background: rgba(15,23,42,0.9); border: 1px solid rgba(148,163,184,0.25); border-radius: 6px; padding: 0.4rem 0.65rem 0.4rem 2rem; color: #f8fafc; font-size: 0.82rem;">
            <i data-lucide="search" style="position: absolute; left: 0.65rem; top: 50%; transform: translateY(-50%); width: 13px; height: 13px; color: #64748b;"></i>
          </div>
        </div>
      </div>

      <!-- Segmented Queue Navigation Tabs -->
      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; border-top: 1px solid var(--admin-border-subtle); padding-top: 1rem;">
        <button type="button" onclick="switchMonetizeSubTab('pending')" id="msubtab_pending" class="ts-btn ts-btn-sm <?= (!empty($pendingMonetizationList) || empty($activeTabSub)) ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.82rem; padding: 0.45rem 1rem; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
          <span>Pending Applications</span>
          <span class="ts-badge" style="background: <?= count($pendingMonetizationList) > 0 ? '#ef4444' : 'rgba(255,255,255,0.15)' ?>; color: #fff; font-size: 0.72rem; font-weight: 800; padding: 0.15rem 0.5rem; border-radius: 999px;">
            <?= count($pendingMonetizationList) ?>
          </span>
        </button>

        <button type="button" onclick="switchMonetizeSubTab('active')" id="msubtab_active" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.82rem; padding: 0.45rem 1rem; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="check-circle" style="width: 14px; height: 14px; color: #10b981;"></i>
          <span>Active Monetized Partners</span>
          <span class="ts-badge ts-badge-green" style="font-size: 0.72rem; padding: 0.15rem 0.5rem;">
            <?= count($activeMonetizedList) ?>
          </span>
        </button>

        <button type="button" onclick="switchMonetizeSubTab('eligible')" id="msubtab_eligible" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.82rem; padding: 0.45rem 1rem; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="sparkles" style="width: 14px; height: 14px; color: #38bdf8;"></i>
          <span>Eligible Pipeline</span>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem; padding: 0.15rem 0.5rem;">
            <?= count($eligibleMonetizationList) ?>
          </span>
        </button>

        <button type="button" onclick="switchMonetizeSubTab('all')" id="msubtab_all" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.82rem; padding: 0.45rem 1rem; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="users" style="width: 14px; height: 14px; color: #94a3b8;"></i>
          <span>All Network Authors</span>
          <span class="ts-badge" style="font-size: 0.72rem; padding: 0.15rem 0.5rem; background: rgba(255,255,255,0.1);">
            <?= count($monetizationList) ?>
          </span>
        </button>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SUB-PANE 1: PENDING APPLICATIONS QUEUE -->
    <!-- ========================================================================= -->
    <div id="msubpane_pending" class="msubpane" style="display: block;">
      <?php if (!empty($pendingMonetizationList)): ?>
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
          <?php foreach ($pendingMonetizationList as $pAuth): ?>
            <?php 
              $pStatus = $pAuth['criteria_status'];
              $mf = $pStatus['metrics']['followers'] ?? ['current' => 0, 'required' => 1000, 'met' => false, 'percent' => 0];
              $mv = $pStatus['metrics']['qualified_views'] ?? ['current' => 0, 'required' => 100000, 'met' => false, 'percent' => 0];
              $mp = $pStatus['metrics']['published_articles'] ?? ['current' => 0, 'required' => 5, 'met' => false, 'percent' => 0];
              $mq = $pStatus['metrics']['quality_score'] ?? ['current' => 80, 'required' => 70, 'met' => true, 'percent' => 100];
              $pProfileSlug = $pAuth['slug'] ?: ($pAuth['username'] ?: (string)$pAuth['id']);
            ?>
            <div class="admin-table-wrap monetize-row" data-status="applied" data-search="<?= htmlspecialchars(strtolower($pAuth['display_name'] . ' ' . $pAuth['username'] . ' ' . $pAuth['user_email'])) ?>" style="padding: 1.75rem; border: 1px solid rgba(245,158,11,0.35); background: linear-gradient(135deg, rgba(15,23,42,0.95), rgba(30,41,59,0.7)); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.4);">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 1rem;">
                  <div style="width: 54px; height: 54px; border-radius: 50%; background: #1e293b; border: 2px solid #fbbf24; overflow: hidden; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; font-weight: 800; color: #fbbf24;">
                    <?php if (!empty($pAuth['avatar_url'])): ?>
                      <img src="<?= url($pAuth['avatar_url']) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                      <?= strtoupper(substr($pAuth['display_name'] ?: 'C', 0, 1)) ?>
                    <?php endif; ?>
                  </div>
                  <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                      <a href="<?= url('/author/profile/' . $pProfileSlug) ?>" target="_blank" style="font-size: 1.25rem; font-weight: 800; color: #f8fafc; text-decoration: none; font-family: 'Outfit', sans-serif;">
                        <?= htmlspecialchars($pAuth['display_name']) ?> <span style="font-size: 0.85rem; color: #38bdf8;">↗</span>
                      </a>
                      <span class="ts-badge ts-badge-pink" style="background: rgba(245,158,11,0.15); color: #fbbf24; border-color: rgba(245,158,11,0.4); font-size: 0.75rem; padding: 0.2rem 0.6rem;">
                        APPLICATION QUEUED ⏳
                      </span>
                    </div>
                    <div style="font-size: 0.82rem; color: #94a3b8; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
                      @<?= htmlspecialchars($pAuth['username']) ?> &bull; <?= htmlspecialchars($pAuth['user_email']) ?> &bull; Submitted: <strong style="color: #f8fafc;"><?= !empty($pAuth['monetization_applied_at']) ? \App\Core\DateHelper::formatDateTime($pAuth['monetization_applied_at']) : 'Recently' ?></strong>
                    </div>
                  </div>
                </div>

                <!-- Fast Actions -->
                <div style="display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center;">
                  <form action="<?= url('/admin/revenue/monetization/approve/' . $pAuth['id']) ?>" method="POST" onsubmit="return confirm('Approve <?= htmlspecialchars(addslashes($pAuth['display_name'])) ?> for the Creator Partner Monetization Program?');">
                    <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); border: none; font-weight: 800; padding: 0.55rem 1.25rem; box-shadow: 0 4px 14px rgba(16,185,129,0.35);">
                      <i data-lucide="check" style="width: 15px; height: 15px;"></i> Approve Partner Program ✓
                    </button>
                  </form>

                  <button type="button" onclick="openDeclineMonetizationModal(<?= (int)$pAuth['id'] ?>, '<?= htmlspecialchars(addslashes($pAuth['display_name'])) ?>')" class="ts-btn ts-btn-outline" style="color: #ef4444; border-color: rgba(239,68,68,0.4); padding: 0.55rem 1.15rem; font-weight: 700;">
                    <i data-lucide="x" style="width: 15px; height: 15px;"></i> Decline with Feedback
                  </button>
                </div>
              </div>

              <!-- 4-Gauge Metric Cards -->
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; background: rgba(15,23,42,0.6); padding: 1.25rem; border-radius: 10px; border: 1px solid rgba(148,163,184,0.12);">
                <!-- Metric 1: Followers -->
                <div>
                  <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.35rem;">
                    <span>1. Verified Followers</span>
                    <span style="color: <?= $mf['met'] ? '#10b981' : '#f59e0b' ?>;"><?= $mf['met'] ? '✓ UNLOCKED' : number_format($mf['percent'], 1) . '%' ?></span>
                  </div>
                  <div style="font-size: 1.15rem; font-weight: 800; color: #f8fafc; font-family: var(--admin-font-mono); margin-bottom: 0.35rem;">
                    <?= number_format($mf['current']) ?> <span style="font-size: 0.75rem; color: #64748b;">/ 1,000</span>
                  </div>
                  <div style="width: 100%; height: 5px; background: rgba(148,163,184,0.1); border-radius: 999px; overflow: hidden;">
                    <div style="width: <?= min(100, max(5, $mf['percent'])) ?>%; height: 100%; background: <?= $mf['met'] ? '#10b981' : '#38bdf8' ?>; border-radius: 999px;"></div>
                  </div>
                </div>

                <!-- Metric 2: Qualified Reads -->
                <div>
                  <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.35rem;">
                    <span>2. Qualified Reads (&ge;10s)</span>
                    <span style="color: <?= $mv['met'] ? '#10b981' : '#f59e0b' ?>;"><?= $mv['met'] ? '✓ UNLOCKED' : number_format($mv['percent'], 1) . '%' ?></span>
                  </div>
                  <div style="font-size: 1.15rem; font-weight: 800; color: #38bdf8; font-family: var(--admin-font-mono); margin-bottom: 0.35rem;">
                    <?= number_format($mv['current']) ?> <span style="font-size: 0.75rem; color: #64748b;">/ 100,000</span>
                  </div>
                  <div style="width: 100%; height: 5px; background: rgba(148,163,184,0.1); border-radius: 999px; overflow: hidden;">
                    <div style="width: <?= min(100, max(5, $mv['percent'])) ?>%; height: 100%; background: <?= $mv['met'] ? '#10b981' : '#38bdf8' ?>; border-radius: 999px;"></div>
                  </div>
                </div>

                <!-- Metric 3: Published Articles -->
                <div>
                  <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.35rem;">
                    <span>3. Published Articles</span>
                    <span style="color: <?= $mp['met'] ? '#10b981' : '#f59e0b' ?>;"><?= $mp['met'] ? '✓ UNLOCKED' : number_format($mp['percent'], 1) . '%' ?></span>
                  </div>
                  <div style="font-size: 1.15rem; font-weight: 800; color: #c084fc; font-family: var(--admin-font-mono); margin-bottom: 0.35rem;">
                    <?= number_format($mp['current']) ?> <span style="font-size: 0.75rem; color: #64748b;">/ 5</span>
                  </div>
                  <div style="width: 100%; height: 5px; background: rgba(148,163,184,0.1); border-radius: 999px; overflow: hidden;">
                    <div style="width: <?= min(100, max(5, $mp['percent'])) ?>%; height: 100%; background: <?= $mp['met'] ? '#10b981' : '#c084fc' ?>; border-radius: 999px;"></div>
                  </div>
                </div>

                <!-- Metric 4: Quality Score -->
                <div>
                  <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.35rem;">
                    <span>4. Quality Score Index</span>
                    <span style="color: <?= $mq['met'] ? '#10b981' : '#ef4444' ?>;"><?= $mq['met'] ? '✓ MET' : 'BELOW' ?></span>
                  </div>
                  <div style="font-size: 1.15rem; font-weight: 800; color: #10b981; font-family: var(--admin-font-mono); margin-bottom: 0.35rem;">
                    <?= number_format($mq['current'], 1) ?>% <span style="font-size: 0.75rem; color: #64748b;">/ 70% min</span>
                  </div>
                  <div style="width: 100%; height: 5px; background: rgba(148,163,184,0.1); border-radius: 999px; overflow: hidden;">
                    <div style="width: <?= min(100, max(5, $mq['current'])) ?>%; height: 100%; background: #10b981; border-radius: 999px;"></div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="admin-table-wrap" style="padding: 3.5rem 2rem; text-align: center; background: rgba(15,23,42,0.4); border: 1px dashed rgba(148,163,184,0.2);">
          <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🛡️</div>
          <h4 style="font-size: 1.2rem; color: #f8fafc; margin: 0 0 0.4rem 0; font-weight: 700;">Zero Pending Partner Applications</h4>
          <p style="color: #94a3b8; font-size: 0.88rem; margin: 0 auto; max-width: 520px;">
            All submitted creator applications have been reviewed. As soon as an eligible creator applies for the monetization program, their review dossier will appear here for editorial approval.
          </p>
        </div>
      <?php endif; ?>
    </div>

    <!-- ========================================================================= -->
    <!-- SUB-PANE 2: ACTIVE MONETIZED CREATORS -->
    <!-- ========================================================================= -->
    <div id="msubpane_active" class="msubpane" style="display: none;">
      <div class="admin-table-wrap">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h4 style="font-size: 1.1rem; color: #f8fafc; margin: 0;">✅ Verified Monetized Creators Roster</h4>
            <p style="color: #64748b; font-size: 0.8rem; margin: 0.2rem 0 0 0;">Creators actively enrolled in revenue sharing and promotional credit disbursals.</p>
          </div>
          <span class="ts-badge ts-badge-green"><?= count($activeMonetizedList) ?> Active Partners</span>
        </div>

        <table class="admin-table">
          <thead>
            <tr>
              <th>Creator</th>
              <th>Followers</th>
              <th>Total Views</th>
              <th>Published Articles</th>
              <th>Promo Credits</th>
              <th>Approved Date</th>
              <th style="text-align: right;">Partner Controls</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($activeMonetizedList)): ?>
              <?php foreach ($activeMonetizedList as $aAuth): ?>
                <?php $aSlug = $aAuth['slug'] ?: ($aAuth['username'] ?: (string)$aAuth['id']); ?>
                <tr class="monetize-row" data-status="approved" data-search="<?= htmlspecialchars(strtolower($aAuth['display_name'] . ' ' . $aAuth['username'] . ' ' . $aAuth['user_email'])) ?>">
                  <td>
                    <div style="font-weight: 700; color: #f8fafc;">
                      <a href="<?= url('/author/profile/' . $aSlug) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;">
                        <?= htmlspecialchars($aAuth['display_name']) ?> <span style="color: #38bdf8; font-size: 0.75rem;">↗</span>
                      </a>
                    </div>
                    <div style="font-size: 0.75rem; color: #94a3b8; font-family: var(--admin-font-mono);">
                      @<?= htmlspecialchars($aAuth['username']) ?> &bull; <?= htmlspecialchars($aAuth['user_email']) ?>
                    </div>
                  </td>
                  <td style="font-weight: 700; color: #10b981; font-family: var(--admin-font-mono);">
                    <?= number_format((int)($aAuth['followers_count'] ?? 0)) ?>
                  </td>
                  <td style="font-weight: 700; color: #38bdf8; font-family: var(--admin-font-mono);">
                    <?= number_format((int)($aAuth['total_views'] ?? 0)) ?>
                  </td>
                  <td style="font-weight: 700; color: #c084fc; font-family: var(--admin-font-mono);">
                    <?= number_format((int)($aAuth['published_articles'] ?? 0)) ?>
                  </td>
                  <td style="font-weight: 700; color: #f59e0b; font-family: var(--admin-font-mono);">
                    $<?= number_format((float)($aAuth['promo_credits'] ?? 0), 2) ?>
                  </td>
                  <td style="font-size: 0.78rem; color: #64748b; font-family: var(--admin-font-mono);">
                    <?= !empty($aAuth['monetization_approved_at']) ? \App\Core\DateHelper::formatDateTime($aAuth['monetization_approved_at']) : 'Active' ?>
                  </td>
                  <td style="text-align: right;">
                    <form action="<?= url('/admin/revenue/monetization/revoke/' . $aAuth['id']) ?>" method="POST" onsubmit="return confirm('Revoke / suspend monetization privileges for <?= htmlspecialchars(addslashes($aAuth['display_name'])) ?>?');" style="display: inline-block;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                        Suspend ⏸️
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: #64748b;">No active monetized creators found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SUB-PANE 3: UNLOCKED & ELIGIBLE PIPELINE -->
    <!-- ========================================================================= -->
    <div id="msubpane_eligible" class="msubpane" style="display: none;">
      <div class="admin-table-wrap">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h4 style="font-size: 1.1rem; color: #f8fafc; margin: 0;">🚀 Unlocked &amp; Eligible Creators Pipeline</h4>
            <p style="color: #64748b; font-size: 0.8rem; margin: 0.2rem 0 0 0;">Authors who have reached all 4 thresholds and are eligible for partner monetization.</p>
          </div>
          <span class="ts-badge ts-badge-cyan"><?= count($eligibleMonetizationList) ?> Ready to Monetize</span>
        </div>

        <table class="admin-table">
          <thead>
            <tr>
              <th>Creator</th>
              <th>Followers</th>
              <th>Qualified Reads</th>
              <th>Published</th>
              <th>Status</th>
              <th style="text-align: right;">Fast-Track Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($eligibleMonetizationList)): ?>
              <?php foreach ($eligibleMonetizationList as $eAuth): ?>
                <?php 
                  $eSlug = $eAuth['slug'] ?: ($eAuth['username'] ?: (string)$eAuth['id']);
                  $eStatus = $eAuth['criteria_status'];
                ?>
                <tr class="monetize-row" data-status="eligible" data-search="<?= htmlspecialchars(strtolower($eAuth['display_name'] . ' ' . $eAuth['username'] . ' ' . $eAuth['user_email'])) ?>">
                  <td>
                    <div style="font-weight: 700; color: #f8fafc;">
                      <a href="<?= url('/author/profile/' . $eSlug) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;">
                        <?= htmlspecialchars($eAuth['display_name']) ?> <span style="color: #38bdf8; font-size: 0.75rem;">↗</span>
                      </a>
                    </div>
                    <div style="font-size: 0.75rem; color: #94a3b8; font-family: var(--admin-font-mono);">
                      @<?= htmlspecialchars($eAuth['username']) ?> &bull; <?= htmlspecialchars($eAuth['user_email']) ?>
                    </div>
                  </td>
                  <td style="font-weight: 700; color: #10b981; font-family: var(--admin-font-mono);">
                    <?= number_format((int)($eStatus['metrics']['followers']['current'] ?? 0)) ?> / 1k ✓
                  </td>
                  <td style="font-weight: 700; color: #38bdf8; font-family: var(--admin-font-mono);">
                    <?= number_format((int)($eStatus['metrics']['qualified_views']['current'] ?? 0)) ?> / 100k ✓
                  </td>
                  <td style="font-weight: 700; color: #c084fc; font-family: var(--admin-font-mono);">
                    <?= (int)($eStatus['metrics']['published_articles']['current'] ?? 0) ?> / 5 ✓
                  </td>
                  <td>
                    <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem;">100% CRITERIA UNLOCKED 🚀</span>
                  </td>
                  <td style="text-align: right;">
                    <form action="<?= url('/admin/revenue/monetization/approve/' . $eAuth['id']) ?>" method="POST" onsubmit="return confirm('Directly approve partner monetization for <?= htmlspecialchars(addslashes($eAuth['display_name'])) ?>?');" style="display: inline-block;">
                      <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #10b981; border-color: #10b981; padding: 0.25rem 0.65rem; font-size: 0.75rem; font-weight: 700;">
                        Direct Approve ✓
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">No authors currently in eligible pipeline.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SUB-PANE 4: ALL NETWORK AUTHORS -->
    <!-- ========================================================================= -->
    <div id="msubpane_all" class="msubpane" style="display: none;">
      <div class="admin-table-wrap">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h4 style="font-size: 1.1rem; color: #f8fafc; margin: 0;">📋 Complete Author Network Directory</h4>
            <p style="color: #64748b; font-size: 0.8rem; margin: 0.2rem 0 0 0;">All registered creators with instant status overview and manual override actions.</p>
          </div>
          <span class="ts-badge" style="background: rgba(255,255,255,0.1);"><?= count($monetizationList) ?> Total Authors</span>
        </div>

        <table class="admin-table">
          <thead>
            <tr>
              <th>Author Creator</th>
              <th>Followers</th>
              <th>Qualified Reads</th>
              <th>Articles</th>
              <th>Status</th>
              <th style="text-align: right;">Manual Controls</th>
            </tr>
          </thead>
          <tbody id="monetizeTbody">
            <?php if (!empty($monetizationList)): ?>
              <?php foreach ($monetizationList as $mAuth): ?>
                <?php 
                  $mStatus = $mAuth['criteria_status'];
                  $fCount = $mStatus['metrics']['followers']['current'] ?? 0;
                  $vCount = $mStatus['metrics']['qualified_views']['current'] ?? 0;
                  $pCount = $mStatus['metrics']['published_articles']['current'] ?? 0;
                  $isMon = !empty($mAuth['is_monetized']) || ($mAuth['monetization_status'] === 'approved');
                  $isApp = ($mAuth['monetization_status'] === 'applied');
                  $mSlug = $mAuth['slug'] ?: ($mAuth['username'] ?: (string)$mAuth['id']);
                ?>
                <tr class="monetize-row" data-status="<?= $isMon ? 'approved' : ($isApp ? 'applied' : ($mStatus['is_eligible'] ? 'eligible' : 'in_progress')) ?>" data-search="<?= htmlspecialchars(strtolower($mAuth['display_name'] . ' ' . $mAuth['username'] . ' ' . $mAuth['user_email'])) ?>">
                  <td>
                    <div style="font-weight: 700; color: #f8fafc; font-size: 0.95rem;">
                      <a href="<?= url('/author/profile/' . $mSlug) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;">
                        <?= htmlspecialchars($mAuth['display_name']) ?> <span style="color: #38bdf8; font-size: 0.75rem;">↗</span>
                      </a>
                    </div>
                    <div style="font-size: 0.75rem; color: #94a3b8; font-family: var(--admin-font-mono);">
                      @<?= htmlspecialchars($mAuth['username']) ?> &bull; <?= htmlspecialchars($mAuth['user_email']) ?>
                    </div>
                  </td>

                  <td>
                    <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.4rem;">
                      <strong style="color: <?= $fCount >= 1000 ? '#10b981' : '#f59e0b' ?>; font-family: var(--admin-font-mono);">
                        <?= number_format($fCount) ?>
                      </strong>
                      <span style="font-size: 0.72rem; color: #64748b;">/ 1k</span>
                      <?php if ($fCount >= 1000): ?>
                        <span class="ts-badge ts-badge-green" style="font-size: 0.65rem; padding: 0 0.3rem;">✓</span>
                      <?php endif; ?>
                    </div>
                  </td>

                  <td>
                    <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.4rem;">
                      <strong style="color: <?= $vCount >= 100000 ? '#38bdf8' : '#cbd5e1' ?>; font-family: var(--admin-font-mono);">
                        <?= number_format($vCount) ?>
                      </strong>
                      <span style="font-size: 0.72rem; color: #64748b;">/ 100k</span>
                      <?php if ($vCount >= 100000): ?>
                        <span class="ts-badge ts-badge-green" style="font-size: 0.65rem; padding: 0 0.3rem;">✓</span>
                      <?php endif; ?>
                    </div>
                  </td>

                  <td>
                    <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.4rem;">
                      <strong style="color: <?= $pCount >= 5 ? '#c084fc' : '#cbd5e1' ?>; font-family: var(--admin-font-mono);">
                        <?= $pCount ?>
                      </strong>
                      <span style="font-size: 0.72rem; color: #64748b;">/ 5</span>
                      <?php if ($pCount >= 5): ?>
                        <span class="ts-badge ts-badge-green" style="font-size: 0.65rem; padding: 0 0.3rem;">✓</span>
                      <?php endif; ?>
                    </div>
                  </td>

                  <td>
                    <?php if ($isMon): ?>
                      <span class="ts-badge ts-badge-green" style="font-size: 0.72rem;">ACTIVE PARTNER ✅</span>
                    <?php elseif ($isApp): ?>
                      <span class="ts-badge ts-badge-pink" style="font-size: 0.72rem; background: rgba(245,158,11,0.15); color: #fbbf24; border-color: rgba(245,158,11,0.4);">
                        APPLIED ⏳
                      </span>
                    <?php elseif ($mStatus['is_eligible']): ?>
                      <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem;">ELIGIBLE 🚀</span>
                    <?php else: ?>
                      <span class="ts-badge" style="font-size: 0.72rem; color: #94a3b8;">IN PROGRESS (<?= $mStatus['overall_progress'] ?>%)</span>
                    <?php endif; ?>
                  </td>

                  <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 0.35rem; justify-content: flex-end; flex-wrap: wrap;">
                      <?php if (!$isMon): ?>
                        <form action="<?= url('/admin/revenue/monetization/approve/' . $mAuth['id']) ?>" method="POST" onsubmit="return confirm('Grant partner monetization to <?= htmlspecialchars(addslashes($mAuth['display_name'])) ?>?');">
                          <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; background: #10b981; border-color: #10b981; font-weight: 700;">
                            Approve ✓
                          </button>
                        </form>
                      <?php else: ?>
                        <form action="<?= url('/admin/revenue/monetization/revoke/' . $mAuth['id']) ?>" method="POST" onsubmit="return confirm('Suspend monetization for <?= htmlspecialchars(addslashes($mAuth['display_name'])) ?>?');">
                          <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #ef4444; border-color: rgba(239,68,68,0.3);">
                            Suspend ⏸️
                          </button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">No author profiles found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 2. GRANT AUTHOR REWARD -->
  <!-- ========================================================================= -->
  <!-- 1C. UNIVERSAL FINANCIAL LEDGER (DOUBLE-ENTRY AUDIT RECORD) -->
  <!-- ========================================================================= -->
  <div class="rev-pane" id="revpane_ledger" style="<?= $activeTab === 'ledger' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap">
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-purple">IMMUTABLE FINANCIAL AUDIT</span>
            <span style="font-size: 0.8rem; color: #64748b;">Double-Entry Transaction Ledger with Before/After Snapshots</span>
          </div>
          <h3 style="font-size: 1.2rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            📖 Universal Creator Financial Ledger
          </h3>
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
          <!-- Quick Search -->
          <div style="position: relative; min-width: 240px;">
            <i data-lucide="search" style="width: 14px; height: 14px; position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
            <input type="text" id="ledgerSearchInput" oninput="filterLedgerTable()" placeholder="Search Ref #, creator, reason..." style="width: 100%; background: rgba(15,23,42,0.9); border: 1px solid rgba(148,163,184,0.25); border-radius: 6px; padding: 0.4rem 0.65rem 0.4rem 2rem; color: #f8fafc; font-size: 0.82rem;">
          </div>

          <!-- Type Filter -->
          <select id="ledgerTypeFilter" onchange="filterLedgerTable()" style="background: rgba(15,23,42,0.9); border: 1px solid rgba(148,163,184,0.25); border-radius: 6px; padding: 0.4rem 0.65rem; color: #f8fafc; font-size: 0.82rem;">
            <option value="all">All Types (Credit &amp; Debit)</option>
            <option value="credit">Credits Only (+)</option>
            <option value="debit">Debits Only (-)</option>
          </select>

          <button type="button" onclick="switchRevTab('grant')" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 700;">
            <i data-lucide="plus-circle"></i> Adjust Balance
          </button>
        </div>
      </div>

      <!-- Financial Ledger Metrics Strip -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; padding: 1.25rem 1.5rem; background: rgba(15,23,42,0.4); border-bottom: 1px solid var(--admin-border-subtle);">
        <div>
          <div style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase;">Total Injected Credits</div>
          <div style="font-size: 1.3rem; font-weight: 800; color: #10b981; font-family: monospace;">+$<?= number_format((float)($stats['ledger_credits'] ?? 0), 2) ?></div>
        </div>
        <div>
          <div style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase;">Total Deducted / Paid</div>
          <div style="font-size: 1.3rem; font-weight: 800; color: #f43f5e; font-family: monospace;">-$<?= number_format((float)($stats['ledger_debits'] ?? 0), 2) ?></div>
        </div>
        <div>
          <div style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase;">Circulating Author Balances</div>
          <div style="font-size: 1.3rem; font-weight: 800; color: #38bdf8; font-family: monospace;">$<?= number_format((float)($stats['credits_pool'] ?? 0), 2) ?></div>
        </div>
        <div>
          <div style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase;">Total Ledger Events</div>
          <div style="font-size: 1.3rem; font-weight: 800; color: #cbd5e1; font-family: monospace;"><?= number_format((int)($stats['ledger_count'] ?? 0)) ?></div>
        </div>
      </div>

      <!-- Ledger Table -->
      <div style="overflow-x: auto;">
        <table class="admin-table" id="ledgerMasterTable">
          <thead>
            <tr>
              <th>Reference #</th>
              <th>Creator / User</th>
              <th>Type &amp; Category</th>
              <th>Amount</th>
              <th>Balance Snapshot</th>
              <th>Reason &amp; Audit Trail</th>
              <th>Admin / Origin</th>
              <th>Timestamp (AM/PM)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($ledger)): ?>
              <?php foreach ($ledger as $entry): ?>
                <tr class="ledger-row" data-type="<?= htmlspecialchars($entry['entry_type']) ?>" data-search="<?= htmlspecialchars(strtolower(($entry['reference_no'] ?? '') . ' ' . ($entry['author_name'] ?? '') . ' ' . ($entry['user_email'] ?? '') . ' ' . ($entry['reason'] ?? '') . ' ' . ($entry['category'] ?? ''))) ?>">
                  <td>
                    <span style="font-family: var(--ts-font-mono); font-size: 0.78rem; font-weight: 700; color: #38bdf8; background: rgba(56,189,248,0.1); padding: 0.2rem 0.45rem; border-radius: 4px; border: 1px solid rgba(56,189,248,0.25);">
                      <?= htmlspecialchars($entry['reference_no']) ?>
                    </span>
                  </td>
                  <td>
                    <div style="font-weight: 700; color: #f8fafc; font-size: 0.88rem;">
                      <?= htmlspecialchars($entry['author_name']) ?>
                    </div>
                    <div style="font-size: 0.74rem; color: #64748b;">
                      <?= htmlspecialchars($entry['user_email']) ?>
                    </div>
                  </td>
                  <td>
                    <?php if ($entry['entry_type'] === 'credit'): ?>
                      <span class="ts-badge ts-badge-green" style="font-size: 0.7rem; font-weight: 800;">
                        + CREDIT
                      </span>
                    <?php else: ?>
                      <span class="ts-badge ts-badge-pink" style="font-size: 0.7rem; font-weight: 800; background: rgba(244,63,94,0.15); color: #fb7185; border-color: rgba(244,63,94,0.3);">
                        - DEBIT
                      </span>
                    <?php endif; ?>
                    <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem; text-transform: uppercase; letter-spacing: 0.03em;">
                      <?= htmlspecialchars(str_replace('_', ' ', $entry['category'])) ?>
                    </div>
                  </td>
                  <td>
                    <span style="font-family: var(--ts-font-mono); font-weight: 800; font-size: 0.95rem; color: <?= $entry['entry_type'] === 'credit' ? '#10b981' : '#fb7185' ?>;">
                      <?= $entry['entry_type'] === 'credit' ? '+' : '-' ?>$<?= number_format((float)$entry['amount_usd'], 2) ?>
                    </span>
                  </td>
                  <td>
                    <div style="font-family: var(--ts-font-mono); font-size: 0.75rem; color: #94a3b8;">
                      <span style="color: #64748b;">$<?= number_format((float)$entry['balance_before'], 2) ?></span>
                      <span style="color: #38bdf8;"> &rarr; </span>
                      <strong style="color: #f8fafc;">$<?= number_format((float)$entry['balance_after'], 2) ?></strong>
                    </div>
                  </td>
                  <td style="max-width: 280px;">
                    <div style="color: #cbd5e1; font-size: 0.82rem; line-height: 1.4;">
                      <?= htmlspecialchars($entry['reason']) ?>
                    </div>
                    <?php if (!empty($entry['internal_notes'])): ?>
                      <div style="font-size: 0.72rem; color: #f59e0b; margin-top: 0.15rem;">
                        <i data-lucide="lock" style="width: 10px; height: 10px; display: inline-block; vertical-align: -1px;"></i> Staff Note: <?= htmlspecialchars($entry['internal_notes']) ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="ts-badge ts-badge-indigo" style="font-size: 0.7rem;">
                      <?= htmlspecialchars($entry['performed_by_username'] ?: ($entry['performed_by_role'] ?? 'System')) ?>
                    </span>
                  </td>
                  <td style="font-size: 0.76rem; color: #94a3b8; font-family: var(--ts-font-mono); white-space: nowrap;">
                    <?= \App\Core\DateHelper::formatDateTime($entry['created_at']) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" style="text-align: center; padding: 3rem; color: #64748b;">
                  <i data-lucide="book-open" style="width: 36px; height: 36px; margin-bottom: 0.5rem;"></i>
                  <p style="margin: 0; font-size: 0.9rem;">No financial ledger transactions recorded yet.</p>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 2. BALANCE ADJUSTMENTS (CREDIT & DEDUCT WITH AUDIT REASON) -->
  <!-- ========================================================================= -->
  <div class="rev-pane" id="revpane_grant" style="<?= $activeTab === 'grant' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 2.25rem; max-width: 860px; margin: 0 auto;">
      <div style="margin-bottom: 1.75rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1.25rem;">
        <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
          <span class="ts-badge ts-badge-cyan">ADMINISTRATIVE BALANCE MANAGEMENT</span>
        </div>
        <h3 style="font-size: 1.35rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
          ⚖️ Creator Balance Adjustment &amp; Clawback Engine
        </h3>
        <p style="color: #94a3b8; font-size: 0.88rem; margin: 0.35rem 0 0 0;">
          Credit bonuses or deduct mistake balance allocations. Every action creates an immutable double-entry ledger event.
        </p>
      </div>

      <form action="<?= url('/admin/revenue/adjust-balance') ?>" method="POST" id="balanceAdjustmentForm" onsubmit="return confirmBalanceAdjustment(event)">
        <!-- 1. Action Type Selector: Credit (+) vs Deduct (-) -->
        <div style="margin-bottom: 1.5rem;">
          <label class="admin-modal-label" style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem; display: block;">
            Adjustment Operation <span style="color: #ef4444;">*</span>
          </label>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <label style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 10px; background: rgba(16,185,129,0.08); border: 2px solid rgba(16,185,129,0.35); cursor: pointer;" id="opt_credit_wrap">
              <input type="radio" name="action" value="credit" checked onchange="handleAdjActionChange('credit')" style="accent-color: #10b981; transform: scale(1.2);">
              <div>
                <div style="font-weight: 800; color: #10b981; font-size: 0.95rem;">🟢 Credit Balance (+)</div>
                <div style="font-size: 0.75rem; color: #94a3b8;">Add rewards, bonuses, grants, or refund balance</div>
              </div>
            </label>

            <label style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 10px; background: rgba(244,63,94,0.08); border: 2px solid rgba(244,63,94,0.35); cursor: pointer;" id="opt_debit_wrap">
              <input type="radio" name="action" value="debit" onchange="handleAdjActionChange('debit')" style="accent-color: #f43f5e; transform: scale(1.2);">
              <div>
                <div style="font-weight: 800; color: #fb7185; font-size: 0.95rem;">🔴 Deduct / Clawback Balance (-)</div>
                <div style="font-size: 0.75rem; color: #94a3b8;">Correct mistaken additions, abuse penalty, or chargeback</div>
              </div>
            </label>
          </div>
        </div>

        <!-- 2. Target Creator Selector with Live Search by Multiple Identifiers -->
        <div style="margin-bottom: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <label class="admin-modal-label" style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin: 0;">
              Target Creator Recipient <span style="color: #ef4444;">*</span>
            </label>
            <span id="creator_match_count" style="font-size: 0.75rem; color: #94a3b8;">
              <?= count($authors) ?> creators available
            </span>
          </div>

          <!-- Live Search Bar -->
          <div style="position: relative; margin-bottom: 0.5rem;">
            <input type="text" id="adj_author_search" placeholder="🔍 Search creator by display name, @handle, slug, author ID (#1), or email..." class="admin-modal-input" style="padding-left: 1rem; font-size: 0.85rem; background: rgba(15,23,42,0.85); border: 1px solid rgba(56,189,248,0.35);" oninput="filterCreatorDropdown(this.value)">
          </div>

          <select name="author_id" id="adj_author_id" class="admin-modal-select" required onchange="handleAdjAuthorSelect(this)" size="6" style="height: auto; max-height: 180px; padding: 0.35rem; background: rgba(15,23,42,0.95); border: 1px solid var(--admin-border-subtle); overflow-y: auto;">
            <option value="" data-balance="0" style="padding: 0.45rem; color: #64748b;">-- Select Creator --</option>
            <?php foreach ($authors as $auth): ?>
              <?php
                $email = $auth['user_email'] ?? $auth['email'] ?? '';
                $username = $auth['user_username'] ?? $auth['username'] ?? $auth['slug'];
                $searchTokens = strtolower("#{$auth['id']} {$auth['display_name']} {$auth['slug']} {$username} {$email}");
              ?>
              <option value="<?= $auth['id'] ?>" data-balance="<?= (float)$auth['promo_credits'] ?>" data-name="<?= htmlspecialchars(addslashes($auth['display_name'])) ?>" data-tokens="<?= htmlspecialchars($searchTokens) ?>" style="padding: 0.45rem 0.6rem; border-radius: 4px; margin-bottom: 2px;">
                #<?= $auth['id'] ?> &bull; <?= htmlspecialchars($auth['display_name']) ?> (@<?= htmlspecialchars($auth['slug']) ?>) <?= !empty($email) ? "— {$email}" : '' ?> &bull; Balance: $<?= number_format((float)$auth['promo_credits'], 2) ?> USD
              </option>
            <?php endforeach; ?>
          </select>
          <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem;">
            💡 Search by display name, username (@handle), email, or author ID to filter and click to select recipient.
          </div>
        </div>

        <!-- 3. Category & Amount Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
          <div>
            <label class="admin-modal-label" style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; display: block;">
              Adjustment Category <span style="color: #ef4444;">*</span>
            </label>
            <select name="category" id="adj_category_sel" class="admin-modal-select" required>
              <optgroup label="Credit Categories" id="cg_credit">
                <option value="editorial_grant">Editorial Excellence Grant ($)</option>
                <option value="view_milestone">Viral Readership Milestone Bounty ($)</option>
                <option value="upvote_bonus">Community Upvote Bonus ($)</option>
                <option value="manual_credit_admin">Manual Admin Balance Injection ($)</option>
                <option value="refund_rejected_payout">Payout Cancellation Refund ($)</option>
              </optgroup>
              <optgroup label="Debit / Deduction Categories" id="cg_debit" style="display: none;">
                <option value="manual_debit_admin">Mistake Addition Correction / Clawback ($)</option>
                <option value="clawback_violation">Policy Violation / Plagiarism Clawback ($)</option>
                <option value="adjustment_correction">Balance Reconciliation Adjustment ($)</option>
                <option value="fee_deduction">Administrative Settlement Fee ($)</option>
              </optgroup>
            </select>
          </div>

          <div>
            <label class="admin-modal-label" style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; display: block;">
              Adjustment Amount ($ USD) <span style="color: #ef4444;">*</span>
            </label>
            <input type="number" step="0.01" min="0.01" name="amount_usd" id="adj_amount" placeholder="e.g. 50.00" class="admin-modal-input" required oninput="calculateAdjPreview()">
          </div>
        </div>

        <!-- Live Impact Calculation Card -->
        <div id="adj_calc_card" style="display: none; background: rgba(15,23,42,0.8); border: 1px solid rgba(56,189,248,0.25); border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.84rem; margin-bottom: 0.35rem; color: #94a3b8;">
            <span>Current Balance:</span>
            <span id="prev_cur_bal" style="font-family: monospace; color: #f8fafc; font-weight: 700;">$0.00</span>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.84rem; margin-bottom: 0.35rem; color: #94a3b8;">
            <span id="prev_op_label">Operation:</span>
            <span id="prev_op_amount" style="font-family: monospace; font-weight: 800;">$0.00</span>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(148,163,184,0.15); padding-top: 0.45rem; font-size: 0.95rem; font-weight: 800;">
            <span style="color: #f8fafc;">New Resulting Balance:</span>
            <span id="prev_res_bal" style="font-family: monospace; color: #10b981; font-size: 1.1rem;">$0.00</span>
          </div>
        </div>

        <!-- 4. Mandatory Reason & Audit Note -->
        <div style="margin-bottom: 1.25rem;">
          <label class="admin-modal-label" style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; display: block;">
            Reason for Adjustment <span style="color: #ef4444;">*</span> <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Sent in creator notification &amp; recorded on ledger)</span>
          </label>
          <textarea name="reason" rows="2" placeholder="e.g. Correcting mistakenly added test balance from Aug 29th payout review" class="admin-modal-textarea" required></textarea>
        </div>

        <!-- 5. Optional Internal Notes -->
        <div style="margin-bottom: 1.25rem;">
          <label class="admin-modal-label" style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; display: block;">
            Internal Staff Notes (Optional) <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Only visible to Administrators)</span>
          </label>
          <input type="text" name="internal_notes" placeholder="e.g. Approved by Finance Director as per ticket #8849" class="admin-modal-input">
        </div>

        <!-- Allow Overdraft Switch (Only for Debits) -->
        <div id="overdraft_option_wrap" style="display: none; margin-bottom: 1.5rem; background: rgba(245,158,11,0.08); border: 1px dashed rgba(245,158,11,0.3); border-radius: 8px; padding: 0.85rem 1rem;">
          <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; color: #fbbf24; font-size: 0.85rem; font-weight: 600;">
            <input type="checkbox" name="allow_overdraft" value="1" style="accent-color: #f59e0b; transform: scale(1.15);">
            Allow Overdraft / Negative Balance Adjustment (If deduction exceeds available balance)
          </label>
        </div>

        <div style="flex-wrap: wrap; align-items: center; display: flex; justify-content: flex-end; gap: 0.75rem;">
          <button type="submit" id="adjSubmitBtn" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 800; padding: 0.65rem 1.6rem;">
            Execute Balance Adjustment ✓
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 3. REWARD HISTORY LOGS -->
  <!-- ========================================================================= -->
  <div class="rev-pane" id="revpane_rewards" style="<?= $activeTab === 'rewards' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap">
      <div style="padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle);">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0;">📜 Creator Reward Grant Ledger</h3>
        <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Historical record of all bonuses, editorial grants, and milestone disbursements.</p>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Author</th>
            <th>Reason &amp; Description</th>
            <th>Type</th>
            <th>Amount Granted</th>
            <th>Dispatched Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($rewards)): ?>
            <?php foreach ($rewards as $r): ?>
              <tr>
                <td style="font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($r['display_name']) ?></td>
                <td style="color: #cbd5e1; font-size: 0.88rem;"><?= htmlspecialchars($r['description']) ?></td>
                <td><span class="ts-badge ts-badge-cyan"><?= strtoupper(htmlspecialchars($r['transaction_type'])) ?></span></td>
                <td style="font-family: var(--admin-font-mono); font-weight: 800; color: #10b981;">
                  $<?= number_format((float)$r['amount_usd'], 2) ?>
                </td>
                <td style="font-size: 0.8rem; color: #64748b;"><?= \App\Core\DateHelper::render($r['created_at'], 'M j, Y H:i', 'short') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align: center; padding: 2.5rem; color: #64748b;">No reward logs found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 4. REVENUE SETTINGS -->
  <!-- ========================================================================= -->
  <div class="rev-pane" id="revpane_settings" style="<?= $activeTab === 'settings' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 2.25rem; max-width: 960px; margin: 0 auto;">
      
      <!-- Top Title Header -->
      <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1.25rem;">
        <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
          <span class="ts-badge ts-badge-cyan">FINANCIAL GOVERNANCE &amp; SETTLEMENT RULES</span>
        </div>
        <h3 style="font-size: 1.4rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
          ⚙️ Creator Monetization, Payout Methods &amp; Fee Percentages Hub
        </h3>
        <p style="color: #94a3b8; font-size: 0.88rem; margin: 0.35rem 0 0 0;">
          Configure global platform withholding percentages, bank transaction fees, allowed payout methods, and add custom settlement channels.
        </p>
      </div>

      <!-- FORM 1: GENERAL MONETIZATION, FEE PERCENTAGES & METHOD TOGGLES -->
      <form action="<?= url('/admin/revenue/save-settings') ?>" method="POST" style="margin-bottom: 3rem;">
        <input type="hidden" name="subaction" value="save_general">

        <!-- ========================================================================= -->
        <!-- SECTION 1: PAYOUT FEE PERCENTAGES & WITHHOLDING FORMULAS -->
        <!-- ========================================================================= -->
        <div style="background: rgba(15,23,42,0.85); border: 1px solid rgba(56,189,248,0.25); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
              <div style="flex-wrap: wrap; align-items: center; font-weight: 800; color: #38bdf8; font-size: 1.05rem; display: flex; align-items: center; gap: 0.4rem;">
                <span>💸 Payout Fee Percentages &amp; Bank Deductions</span>
              </div>
              <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.15rem;">
                Both platform and bank fee percentages are dynamically deducted from the author's gross claim upon withdrawal.
              </div>
            </div>
            <span class="ts-badge ts-badge-green" style="font-size: 0.72rem;">ACTIVE ENFORCEMENT</span>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
            <!-- 1. Platform Fee Percentage -->
            <div>
              <label class="admin-modal-label" style="font-weight: 700; color: #f8fafc; display: flex; align-items: center; gap: 0.35rem;">
                <span>Platform Operations Fee (%)</span>
                <span style="color: #38bdf8;">*</span>
              </label>
              <div style="position: relative;">
                <input type="number" step="0.01" min="0" max="100" name="payout_platform_fee_percent" id="set_platform_fee_pct" value="<?= (float)($feeSettings['platform_fee_percent'] ?? 5.0) ?>" class="admin-modal-input" style="font-size: 1rem; font-weight: 700; color: #38bdf8; padding-right: 2rem;" oninput="updateFeePreviewCalc()" required>
                <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-weight: 800; color: #64748b;">%</span>
              </div>
              <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.3rem;">Default standard: 5.00%. Deducted from all withdrawal methods.</div>
            </div>

            <!-- 2. Bank Transaction Fee Percentage -->
            <div>
              <label class="admin-modal-label" style="font-weight: 700; color: #f8fafc; display: flex; align-items: center; gap: 0.35rem;">
                <span>Bank Transaction &amp; Remittance Fee (%)</span>
                <span style="color: #ef4444;">*</span>
              </label>
              <div style="position: relative;">
                <input type="number" step="0.01" min="0" max="100" name="payout_bank_fee_percent" id="set_bank_fee_pct" value="<?= (float)($feeSettings['bank_fee_percent'] ?? 2.5) ?>" class="admin-modal-input" style="font-size: 1rem; font-weight: 700; color: #f59e0b; padding-right: 2rem;" oninput="updateFeePreviewCalc()" required>
                <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-weight: 800; color: #64748b;">%</span>
              </div>
              <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.3rem;">Default standard: 2.50%. Deducted for bank wire &amp; cross-border transfers.</div>
            </div>

            <!-- 3. Fixed Wire Processing Surcharge -->
            <div>
              <label class="admin-modal-label" style="font-weight: 700; color: #f8fafc; display: flex; align-items: center; gap: 0.35rem;">
                <span>Fixed Bank Wire Surcharge ($ USD)</span>
              </label>
              <div style="position: relative;">
                <input type="number" step="0.01" min="0" max="500" name="payout_bank_wire_surcharge" id="set_bank_surcharge" value="<?= (float)($feeSettings['bank_wire_surcharge'] ?? 5.0) ?>" class="admin-modal-input" style="font-size: 1rem; font-weight: 700; color: #fb7185; padding-left: 1.8rem;" oninput="updateFeePreviewCalc()" required>
                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-weight: 800; color: #64748b;">$</span>
              </div>
              <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.3rem;">Default standard: $5.00 USD. Bank intermediary wire dispatch fee.</div>
            </div>
          </div>

          <!-- Dynamic Live Calculator Preview Box -->
          <div style="background: rgba(7,10,19,0.9); border: 1px dashed rgba(148,163,184,0.2); border-radius: 8px; padding: 0.85rem 1.15rem; font-size: 0.82rem;">
            <div style="flex-wrap: wrap; align-items: center; font-weight: 700; color: #cbd5e1; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
              <span>🧮 Live Calculation Example ($100.00 Author Withdrawal Claim)</span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; color: #94a3b8;">
              <div>
                <strong style="color: #38bdf8;">Digital Payout (PayPal / Crypto):</strong>
                <div>Platform Fee: <span id="prev_dig_fee" style="color: #f59e0b; font-weight: 600;">-$5.00</span></div>
                <div>Net Creator Disbursal: <strong id="prev_dig_net" style="color: #10b981; font-size: 0.92rem;">$95.00 USD</strong></div>
              </div>
              <div>
                <strong style="color: #c084fc;">Direct Bank Wire (SWIFT / SEPA):</strong>
                <div>Platform Fee: <span id="prev_bank_pfee" style="color: #f59e0b; font-weight: 600;">-$5.00</span> | Bank Fee: <span id="prev_bank_bfee" style="color: #fb7185; font-weight: 600;">-$7.50</span></div>
                <div>Net Creator Disbursal: <strong id="prev_bank_net" style="color: #10b981; font-size: 0.92rem;">$87.50 USD</strong></div>
              </div>
            </div>
          </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 2: ALLOWED PAYOUT METHODS & TOGGLE MATRIX -->
        <!-- ========================================================================= -->
        <div style="background: rgba(15,23,42,0.85); border: 1px solid rgba(16,185,129,0.25); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
              <div style="flex-wrap: wrap; align-items: center; font-weight: 800; color: #34d399; font-size: 1.05rem; display: flex; align-items: center; gap: 0.4rem;">
                <span>🏦 Allowed Payout Methods for Creators</span>
              </div>
              <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.15rem;">
                Select which payout methods creators are allowed to link in their Royalties &amp; Ledger dashboard.
              </div>
            </div>
            <a href="#addCustomMethodCard" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3); font-size: 0.76rem;">
              + Add Custom Payout Method
            </a>
          </div>

          <div style="overflow-x: auto;">
            <table class="admin-table" style="margin-bottom: 0;">
              <thead>
                <tr>
                  <th style="width: 50px; text-align: center;">Allow</th>
                  <th>Method &amp; Provider</th>
                  <th>Key / Code</th>
                  <th>Fee Deductions Applied</th>
                  <th>Classification</th>
                  <th style="text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($allowedMethods as $m): ?>
                  <tr>
                    <td style="text-align: center;">
                      <input type="checkbox" name="enabled_methods[]" value="<?= htmlspecialchars($m['key']) ?>" <?= !empty($m['enabled']) ? 'checked' : '' ?> style="accent-color: #10b981; transform: scale(1.25); cursor: pointer;">
                    </td>
                    <td>
                      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.6rem;">
                        <span style="font-size: 1.3rem;"><?= htmlspecialchars($m['icon'] ?? '💳') ?></span>
                        <div>
                          <div style="font-weight: 700; color: #f8fafc; font-size: 0.9rem;">
                            <?= htmlspecialchars($m['name']) ?>
                          </div>
                          <div style="font-size: 0.74rem; color: #94a3b8;">
                            <?= htmlspecialchars($m['description']) ?>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span style="font-family: var(--admin-font-mono); font-size: 0.78rem; color: #38bdf8; background: rgba(56,189,248,0.1); padding: 0.2rem 0.45rem; border-radius: 4px;">
                        <?= htmlspecialchars($m['key']) ?>
                      </span>
                    </td>
                    <td>
                      <?php if (($m['fee_type'] ?? '') === 'platform_and_bank'): ?>
                        <span class="ts-badge ts-badge-purple" style="font-size: 0.7rem;">
                          Platform % + Bank Tax % + Wire Fee
                        </span>
                      <?php else: ?>
                        <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">
                          Platform % Only
                        </span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (!empty($m['is_custom'])): ?>
                        <span class="ts-badge ts-badge-green" style="font-size: 0.7rem; font-weight: 800;">CUSTOM METHOD ✨</span>
                      <?php else: ?>
                        <span class="ts-badge" style="font-size: 0.7rem; color: #94a3b8;">STANDARD CORE</span>
                      <?php endif; ?>
                    </td>
                    <td style="text-align: right;">
                      <?php if (!empty($m['is_custom'])): ?>
                        <button type="submit" name="subaction" value="delete_custom_method" onclick="document.getElementById('del_custom_key').value='<?= htmlspecialchars($m['key']) ?>'; return confirm('Remove custom payout method <?= htmlspecialchars(addslashes($m['name'])) ?>?');" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 0.72rem; padding: 0.2rem 0.5rem;">
                          Delete ✕
                        </button>
                      <?php else: ?>
                        <span style="font-size: 0.75rem; color: #64748b;">Protected</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 3: CREATOR CPM & GENERAL MONETIZATION PARAMETERS -->
        <!-- ========================================================================= -->
        <div style="background: rgba(15,23,42,0.6); border: 1px solid var(--admin-border-subtle); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
          <h4 style="font-size: 1rem; color: #f8fafc; margin: 0 0 1rem 0; font-family: 'Outfit', sans-serif;">
            📈 Creator Monetization &amp; CPM Rules
          </h4>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Author Revenue Share (%)</label>
              <input type="number" name="revenue_author_share_percent" value="70" class="admin-modal-input" required>
              <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Creator programmatic rev-share percentage.</div>
            </div>
            <div>
              <label class="admin-modal-label">Base Article View CPM ($ USD)</label>
              <input type="number" step="0.01" name="revenue_author_cpm" value="2.50" class="admin-modal-input" required>
              <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Calculated per 1,000 verified readers.</div>
            </div>
            <div>
              <label class="admin-modal-label">Minimum Payout Cashout Threshold ($)</label>
              <input type="number" step="0.01" name="revenue_min_payout_usd" value="25.00" class="admin-modal-input" required>
              <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Minimum balance required for cashout claim.</div>
            </div>
            <div>
              <label class="admin-modal-label">Monetization Eligibility Focus Views</label>
              <input type="number" name="revenue_min_views_monetization" value="10000" class="admin-modal-input" required>
              <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">10s+ focus reads needed to qualify.</div>
            </div>
          </div>
        </div>

        <input type="hidden" name="method_key" id="del_custom_key" value="">

        <div style="display: flex; justify-content: flex-end;">
          <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 800; padding: 0.7rem 1.8rem;">
            Save All Monetization &amp; Payout Settings ✓
          </button>
        </div>
      </form>

      <!-- ========================================================================= -->
      <!-- FORM 2: ADD CUSTOM PAYOUT METHOD STATION -->
      <!-- ========================================================================= -->
      <div id="addCustomMethodCard" style="background: rgba(15,23,42,0.9); border: 1px solid rgba(168,85,247,0.3); border-radius: 12px; padding: 1.75rem;">
        <div style="margin-bottom: 1.25rem;">
          <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-purple">CUSTOM METHOD BUILDER</span>
          </div>
          <h4 style="font-size: 1.2rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            ✨ Add Custom Settlement / Payout Method
          </h4>
          <p style="font-size: 0.82rem; color: #94a3b8; margin: 0.25rem 0 0 0;">
            Enable alternative or regional payout channels (e.g. Payoneer, Easypaisa, JazzCash, Direct ACH, Revolut, Local Bank Clearing).
          </p>
        </div>

        <form action="<?= url('/admin/revenue/save-settings') ?>" method="POST">
          <input type="hidden" name="subaction" value="add_custom_method">

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
            <div>
              <label class="admin-modal-label">Method Display Name <span style="color: #ef4444;">*</span></label>
              <input type="text" name="custom_method_name" placeholder="e.g. Payoneer Global Wire" class="admin-modal-input" required>
            </div>
            <div>
              <label class="admin-modal-label">Unique Key / Code <span style="color: #ef4444;">*</span></label>
              <input type="text" name="custom_method_key" placeholder="e.g. payoneer" class="admin-modal-input" style="font-family: monospace;" required>
            </div>
            <div>
              <label class="admin-modal-label">Icon / Emoji</label>
              <input type="text" name="custom_method_icon" value="💳" class="admin-modal-input" style="text-align: center;">
            </div>
            <div>
              <label class="admin-modal-label">Fee Assessment Policy <span style="color: #ef4444;">*</span></label>
              <select name="custom_method_fee_type" class="admin-modal-select">
                <option value="platform_only">Platform Fee Only (Digital)</option>
                <option value="platform_and_bank">Platform Fee + Bank Tax &amp; Surcharge (Bank Transfer)</option>
              </select>
            </div>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label class="admin-modal-label">Instructions / Description for Authors</label>
            <input type="text" name="custom_method_desc" placeholder="e.g. Fast local bank deposit or Payoneer account email transfer" class="admin-modal-input">
          </div>

          <div style="display: flex; justify-content: flex-end;">
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #a855f7; border-color: #a855f7; font-weight: 700;">
              + Add Custom Method to Platform 🚀
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>

  <script>
  function updateFeePreviewCalc() {
    const pFeePct = parseFloat(document.getElementById('set_platform_fee_pct')?.value || 5);
    const bFeePct = parseFloat(document.getElementById('set_bank_fee_pct')?.value || 2.5);
    const bSurcharge = parseFloat(document.getElementById('set_bank_surcharge')?.value || 5);

    const gross = 100.0;
    const pFee = gross * (pFeePct / 100);
    const bFee = (gross * (bFeePct / 100)) + bSurcharge;

    const digNet = Math.max(0, gross - pFee);
    const bankNet = Math.max(0, gross - pFee - bFee);

    const elDigFee = document.getElementById('prev_dig_fee');
    if (elDigFee) elDigFee.textContent = `-$${pFee.toFixed(2)} (${pFeePct.toFixed(1)}%)`;

    const elDigNet = document.getElementById('prev_dig_net');
    if (elDigNet) elDigNet.textContent = `$${digNet.toFixed(2)} USD`;

    const elBankPfee = document.getElementById('prev_bank_pfee');
    if (elBankPfee) elBankPfee.textContent = `-$${pFee.toFixed(2)}`;

    const elBankBfee = document.getElementById('prev_bank_bfee');
    if (elBankBfee) elBankBfee.textContent = `-$${bFee.toFixed(2)}`;

    const elBankNet = document.getElementById('prev_bank_net');
    if (elBankNet) elBankNet.textContent = `$${bankNet.toFixed(2)} USD`;
  }
  document.addEventListener('DOMContentLoaded', updateFeePreviewCalc);
  </script>

</div>

<!-- Modal: Process Author Payout -->
<div id="processPayoutModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 540px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="check-circle"></i> Process Author Payout Claim</h3>
      <button type="button" onclick="closeProcessPayoutModal()" class="admin-modal-close">✕</button>
    </div>

    <form action="<?= url('/admin/revenue/process-payout') ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="payout_id" id="proc_payout_id" value="">

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Author &amp; Gross Requested</label>
        <input type="text" id="proc_author_info" class="admin-modal-input" readonly style="background: rgba(15,23,42,0.8); color: #38bdf8; font-weight: 700;">
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Payout Method &amp; Destination Coordinates</label>
        <input type="text" id="proc_address_info" class="admin-modal-input" readonly style="background: rgba(15,23,42,0.8); font-family: var(--admin-font-mono);">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Update Payout Status</label>
          <select name="action" id="proc_action" class="admin-modal-select">
            <option value="pending">Pending Review ⏳</option>
            <option value="processing">Processing (In Transfer) 🔄</option>
            <option value="paid">Mark as Paid &amp; Completed ✅</option>
            <option value="rejected">Reject &amp; Refund Credits ❌</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Processing Fee Deducted ($)</label>
          <input type="number" step="0.01" name="fee_deducted" id="proc_fee" value="0.00" class="admin-modal-input">
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Transaction / Bank Wire / Tx Hash Reference</label>
        <input type="text" name="transaction_reference" id="proc_tx_ref" placeholder="e.g. PP-TX-984210492 or Bank Ref ID or Tx Hash" class="admin-modal-input">
      </div>

      <!-- Payout Proof / Receipt File Attachment -->
      <div style="margin-bottom: 1rem; background: rgba(15,23,42,0.5); border: 1px dashed rgba(56,189,248,0.3); border-radius: 8px; padding: 0.85rem 1rem;">
        <label class="admin-modal-label" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
          <span>📎 Attach Payment Receipt Proof (PDF / PNG / JPG)</span>
          <span id="proc_existing_receipt_wrap" style="display: none; font-size: 0.75rem;">
            <a href="#" id="proc_existing_receipt_link" target="_blank" style="color: #38bdf8; text-decoration: underline;">View Current Receipt ↗</a>
          </span>
        </label>
        <input type="file" name="receipt_file" accept=".pdf,.png,.jpg,.jpeg,.webp" class="admin-modal-input" style="padding: 0.4rem; font-size: 0.82rem;">
        <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.3rem;">Upload PDF wire confirmation, bank slip, or payment gateway snapshot. Author can view and download on their dashboard.</div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Admin Notes &amp; Feedback (Visible in Author Notification)</label>
        <textarea name="admin_notes" id="proc_admin_notes" rows="2" class="admin-modal-textarea" placeholder="e.g. Wire initiated via Standard Chartered Bank. Arrives in 2-3 days."></textarea>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeProcessPayoutModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #10b981; border-color: #10b981; color: #fff; font-weight: 700;">Save &amp; Update Payout ✓</button>
      </div>
    </form>
  </div>
</div>

<script>
function switchRevTab(tabKey) {
  document.querySelectorAll('[id^="revtab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('revtab_' + tabKey);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.rev-pane').forEach(p => p.style.display = 'none');
  const targetPane = document.getElementById('revpane_' + tabKey);
  if (targetPane) targetPane.style.display = 'block';

  const url = new URL(window.location);
  url.searchParams.set('tab', tabKey);
  window.history.replaceState({}, '', url);

  if (window.lucide) lucide.createIcons();
}

function openGrantRewardModal() {
  switchRevTab('grant');
}

function filterPayouts(status) {
  document.querySelectorAll('.pfilter-btn').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const btn = document.getElementById(`pfbtn-${status}`);
  if (btn) {
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.payout-row').forEach(r => {
    const rowStatus = r.getAttribute('data-status');
    if (status === 'all' || rowStatus === status) {
      r.style.display = '';
    } else {
      r.style.display = 'none';
    }
  });
}

function openProcessPayoutModal(p) {
  document.getElementById('proc_payout_id').value = p.id;
  document.getElementById('proc_author_info').value = p.display_name + ' — $' + parseFloat(p.amount_usd).toFixed(2);
  document.getElementById('proc_address_info').value = (p.payout_method || '').toUpperCase() + ': ' + (p.payout_address || '');
  document.getElementById('proc_action').value = p.status || 'paid';
  document.getElementById('proc_fee').value = parseFloat(p.fee_deducted || 0).toFixed(2);
  document.getElementById('proc_tx_ref').value = p.transaction_reference || '';
  document.getElementById('proc_admin_notes').value = p.admin_notes || '';

  const receiptWrap = document.getElementById('proc_existing_receipt_wrap');
  const receiptLink = document.getElementById('proc_existing_receipt_link');
  if (p.receipt_url) {
    receiptWrap.style.display = 'inline';
    receiptLink.href = '<?= url('') ?>' + p.receipt_url;
  } else {
    receiptWrap.style.display = 'none';
    receiptLink.href = '#';
  }

  document.getElementById('processPayoutModal').style.display = 'flex';
}

function closeProcessPayoutModal() {
  document.getElementById('processPayoutModal').style.display = 'none';
}

function switchMonetizeSubTab(subKey) {
  ['pending', 'active', 'eligible', 'all'].forEach(k => {
    const btn = document.getElementById('msubtab_' + k);
    const pane = document.getElementById('msubpane_' + k);
    if (btn) {
      btn.classList.remove('ts-btn-primary');
      btn.classList.add('ts-btn-outline');
    }
    if (pane) {
      pane.style.display = 'none';
    }
  });

  const activeBtn = document.getElementById('msubtab_' + subKey);
  const activePane = document.getElementById('msubpane_' + subKey);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }
  if (activePane) {
    activePane.style.display = 'block';
  }

  if (window.lucide) lucide.createIcons();
}

function filterMonetization(status) {
  document.querySelectorAll('.mfilter-btn').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const btn = document.getElementById(`mfbtn-${status}`);
  if (btn) {
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.monetize-row').forEach(r => {
    const rowStatus = r.getAttribute('data-status');
    if (status === 'all' || rowStatus === status) {
      r.style.display = '';
    } else {
      r.style.display = 'none';
    }
  });
}

function searchMonetizationTable() {
  const q = (document.getElementById('monetizeSearchInput').value || '').toLowerCase().trim();
  document.querySelectorAll('.monetize-row').forEach(r => {
    const s = r.getAttribute('data-search') || '';
    if (!q || s.includes(q)) {
      r.style.display = '';
    } else {
      r.style.display = 'none';
    }
  });
}

function openDeclineMonetizationModal(authorId, authorName) {
  document.getElementById('decline_author_id').value = authorId;
  document.getElementById('decline_author_name').innerText = authorName;
  document.getElementById('decline_monetization_form').action = '<?= url('/admin/revenue/monetization/reject/') ?>' + authorId;
  document.getElementById('declineMonetizationModal').style.display = 'flex';
}

function closeDeclineMonetizationModal() {
  document.getElementById('declineMonetizationModal').style.display = 'none';
}

function filterLedgerTable() {
  const q = (document.getElementById('ledgerSearchInput').value || '').toLowerCase().trim();
  const type = document.getElementById('ledgerTypeFilter').value;

  document.querySelectorAll('.ledger-row').forEach(r => {
    const s = r.getAttribute('data-search') || '';
    const t = r.getAttribute('data-type') || '';
    const matchSearch = !q || s.includes(q);
    const matchType = (type === 'all' || t === type);
    r.style.display = (matchSearch && matchType) ? '' : 'none';
  });
}

function handleAdjActionChange(action) {
  const optCreditWrap = document.getElementById('opt_credit_wrap');
  const optDebitWrap = document.getElementById('opt_debit_wrap');
  const cgCredit = document.getElementById('cg_credit');
  const cgDebit = document.getElementById('cg_debit');
  const overdraftWrap = document.getElementById('overdraft_option_wrap');
  const submitBtn = document.getElementById('adjSubmitBtn');

  if (action === 'credit') {
    optCreditWrap.style.border = '2px solid #10b981';
    optCreditWrap.style.background = 'rgba(16,185,129,0.12)';
    optDebitWrap.style.border = '1px solid rgba(148,163,184,0.2)';
    optDebitWrap.style.background = 'rgba(15,23,42,0.4)';

    cgCredit.style.display = '';
    cgDebit.style.display = 'none';
    document.getElementById('adj_category_sel').value = 'editorial_grant';
    if (overdraftWrap) overdraftWrap.style.display = 'none';

    if (submitBtn) {
      submitBtn.style.background = 'linear-gradient(135deg, #10b981, #38bdf8)';
      submitBtn.innerHTML = 'Execute Credit Balance (+$) ✓';
    }
  } else {
    optDebitWrap.style.border = '2px solid #f43f5e';
    optDebitWrap.style.background = 'rgba(244,63,94,0.12)';
    optCreditWrap.style.border = '1px solid rgba(148,163,184,0.2)';
    optCreditWrap.style.background = 'rgba(15,23,42,0.4)';

    cgCredit.style.display = 'none';
    cgDebit.style.display = '';
    document.getElementById('adj_category_sel').value = 'manual_debit_admin';
    if (overdraftWrap) overdraftWrap.style.display = 'block';

    if (submitBtn) {
      submitBtn.style.background = 'linear-gradient(135deg, #f43f5e, #e11d48)';
      submitBtn.innerHTML = 'Execute Deduct / Clawback (-$) ⚠️';
    }
  }
  calculateAdjPreview();
}

function handleAdjAuthorSelect(sel) {
  calculateAdjPreview();
}

function calculateAdjPreview() {
  const sel = document.getElementById('adj_author_id');
  const opt = sel ? sel.options[sel.selectedIndex] : null;
  const curBal = opt ? parseFloat(opt.getAttribute('data-balance') || 0) : 0;
  const amount = parseFloat(document.getElementById('adj_amount').value || 0);
  const action = document.querySelector('input[name="action"]:checked') ? document.querySelector('input[name="action"]:checked').value : 'credit';

  const card = document.getElementById('adj_calc_card');
  if (!sel || !sel.value || isNaN(amount) || amount <= 0) {
    if (card) card.style.display = 'none';
    return;
  }

  card.style.display = 'block';
  document.getElementById('prev_cur_bal').innerText = '$' + curBal.toFixed(2) + ' USD';

  let resBal = 0;
  if (action === 'credit') {
    resBal = curBal + amount;
    document.getElementById('prev_op_label').innerText = 'Credit Injection:';
    document.getElementById('prev_op_amount').innerHTML = `<span style="color:#10b981;">+$${amount.toFixed(2)} USD</span>`;
    document.getElementById('prev_res_bal').style.color = '#10b981';
  } else {
    resBal = curBal - amount;
    document.getElementById('prev_op_label').innerText = 'Clawback Deduction:';
    document.getElementById('prev_op_amount').innerHTML = `<span style="color:#f43f5e;">-$${amount.toFixed(2)} USD</span>`;
    document.getElementById('prev_res_bal').style.color = resBal < 0 ? '#f43f5e' : '#38bdf8';
  }
  document.getElementById('prev_res_bal').innerText = '$' + resBal.toFixed(2) + ' USD';
}

function confirmBalanceAdjustment(e) {
  const sel = document.getElementById('adj_author_id');
  const opt = sel ? sel.options[sel.selectedIndex] : null;
  const name = opt ? (opt.getAttribute('data-name') || 'the selected creator') : 'the selected creator';
  const amount = parseFloat(document.getElementById('adj_amount').value || 0);
  const action = document.querySelector('input[name="action"]:checked') ? document.querySelector('input[name="action"]:checked').value : 'credit';

  const prompt = (action === 'credit')
    ? `Are you sure you want to CREDIT +$${amount.toFixed(2)} USD to ${name}?`
    : `⚠️ CAUTION: Are you sure you want to DEDUCT / CLAWBACK -$${amount.toFixed(2)} USD from ${name}?`;

  return confirm(prompt);
}

// Live Search Creator Selector in Balance Adjustment Form
function filterCreatorDropdown(query) {
  const q = (query || '').toLowerCase().trim();
  const select = document.getElementById('adj_author_id');
  const countEl = document.getElementById('creator_match_count');
  if (!select) return;
  
  let matches = 0;
  const options = select.querySelectorAll('option');
  options.forEach((opt, idx) => {
    if (idx === 0) {
      opt.style.display = '';
      return;
    }
    const tokens = (opt.getAttribute('data-tokens') || opt.textContent || '').toLowerCase();
    if (!q || tokens.includes(q)) {
      opt.style.display = '';
      matches++;
    } else {
      opt.style.display = 'none';
    }
  });

  if (countEl) {
    countEl.textContent = q ? `${matches} matching creators` : `${options.length - 1} creators available`;
  }
}

// Royalty Pool Calculations & Editable Sync
function updatePeriodLabel(cycle) {
  const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  const now = new Date();
  const monthName = months[now.getMonth()];
  const year = now.getFullYear();
  const labelInput = document.getElementById('pool_period_label');
  if (!labelInput) return;

  if (cycle === 'this_month') {
    labelInput.value = `${monthName} ${year} Author Royalty Pool`;
  } else if (cycle === 'this_week') {
    labelInput.value = `Week ${getWeekNumber(now)} (${monthName} ${year}) Creator Distribution`;
  } else if (cycle === 'bi_weekly') {
    labelInput.value = `Bi-Weekly Pool (${monthName} 1-15, ${year})`;
  } else if (cycle === 'quarterly') {
    const q = Math.floor(now.getMonth() / 3) + 1;
    labelInput.value = `Q${q} ${year} Platform Royalty Pool`;
  }
}

function getWeekNumber(d) {
  d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
  d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
  const yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
  return Math.ceil((((d - yearStart) / 86400000) + 1)/7);
}

let adminPermissionNonMonetized = false;

function selectAllPoolAuthors(checked) {
  document.querySelectorAll('.pool-author-row').forEach(row => {
    // If not showing non-monetized and row is hidden, don't check it unless checked is false
    if (!adminPermissionNonMonetized && row.getAttribute('data-monetized') !== '1') {
      const chk = row.querySelector('.pool-author-chk');
      if (chk) chk.checked = false;
      return;
    }
    const chk = row.querySelector('.pool-author-chk');
    if (chk) chk.checked = checked;
  });
  recalculateRoyaltyPool();
}

function deselectNonMonetizedPoolAuthors() {
  document.querySelectorAll('.pool-author-row').forEach(row => {
    if (row.getAttribute('data-monetized') !== '1') {
      const chk = row.querySelector('.pool-author-chk');
      if (chk) chk.checked = false;
    }
  });
  recalculateRoyaltyPool();
}

function selectOnlyMonetizedPoolAuthors() {
  document.querySelectorAll('.pool-author-row').forEach(row => {
    const isMon = row.getAttribute('data-monetized') === '1';
    const chk = row.querySelector('.pool-author-chk');
    if (chk) {
      chk.checked = isMon;
    }
  });
  recalculateRoyaltyPool();
}

function filterPoolMonetizedOnly() {
  adminPermissionNonMonetized = false;
  document.querySelectorAll('.pool-author-row').forEach(row => {
    const isMon = row.getAttribute('data-monetized') === '1';
    const chk = row.querySelector('.pool-author-chk');
    if (!isMon) {
      if (chk) chk.checked = false;
      row.style.display = 'none';
    } else {
      if (chk) chk.checked = true;
      row.style.display = '';
    }
  });

  const banner = document.getElementById('admin_non_monetized_banner');
  if (banner) banner.style.display = 'none';

  const btnMon = document.getElementById('btn_pool_filter_monetized');
  const btnAll = document.getElementById('btn_pool_filter_all');
  if (btnMon) {
    btnMon.className = 'ts-btn ts-btn-primary ts-btn-sm';
    btnMon.style.background = 'linear-gradient(135deg, #10b981, #059669)';
    btnMon.style.color = '#ffffff';
  }
  if (btnAll) {
    btnAll.className = 'ts-btn ts-btn-outline ts-btn-sm';
    btnAll.style.background = 'transparent';
    btnAll.style.color = '#f59e0b';
  }

  recalculateRoyaltyPool();
}

function toggleAdminPermissionNonMonetized() {
  adminPermissionNonMonetized = !adminPermissionNonMonetized;
  
  document.querySelectorAll('.pool-author-row').forEach(row => {
    const isMon = row.getAttribute('data-monetized') === '1';
    if (!isMon) {
      row.style.display = adminPermissionNonMonetized ? '' : 'none';
      const chk = row.querySelector('.pool-author-chk');
      if (!adminPermissionNonMonetized && chk) {
        chk.checked = false;
      }
    }
  });

  const banner = document.getElementById('admin_non_monetized_banner');
  if (banner) banner.style.display = adminPermissionNonMonetized ? 'flex' : 'none';

  const btnMon = document.getElementById('btn_pool_filter_monetized');
  const btnAll = document.getElementById('btn_pool_filter_all');
  if (btnMon) {
    btnMon.className = adminPermissionNonMonetized ? 'ts-btn ts-btn-outline ts-btn-sm' : 'ts-btn ts-btn-primary ts-btn-sm';
    btnMon.style.background = adminPermissionNonMonetized ? 'transparent' : 'linear-gradient(135deg, #10b981, #059669)';
    btnMon.style.color = adminPermissionNonMonetized ? '#cbd5e1' : '#ffffff';
  }
  if (btnAll) {
    btnAll.className = adminPermissionNonMonetized ? 'ts-btn ts-btn-primary ts-btn-sm' : 'ts-btn ts-btn-outline ts-btn-sm';
    btnAll.style.background = adminPermissionNonMonetized ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'transparent';
    btnAll.style.color = adminPermissionNonMonetized ? '#ffffff' : '#f59e0b';
  }

  recalculateRoyaltyPool();
}

function resetCalculatedPoolAmounts() {
  document.querySelectorAll('.pool-author-row').forEach(row => {
    row.removeAttribute('data-manual-edit');
  });
  recalculateRoyaltyPool();
}

function recalculateRoyaltyPool() {
  const grossRev = Math.max(0, parseFloat(document.getElementById('pool_gross_revenue').value || 0));
  const cutPct = Math.min(100, Math.max(0, parseFloat(document.getElementById('pool_platform_cut_pct').value || 0)));
  const baseCpm = Math.max(0.01, parseFloat(document.getElementById('pool_base_cpm').value || 0.80));

  // 1. Deduct Platform Maintenance Cut First
  const maintenanceCut = grossRev * (cutPct / 100);

  // 2. Net Remaining Amount to Distribute
  const netDistributablePool = Math.max(0, grossRev - maintenanceCut);

  // Collect checked authors and views
  let totalCheckedViews = 0;
  let activeAuthorCount = 0;
  const activeRows = [];

  document.querySelectorAll('.pool-author-row').forEach(row => {
    const chk = row.querySelector('.pool-author-chk');
    const views = parseInt(row.getAttribute('data-views') || '0', 10);
    const isMonetized = row.getAttribute('data-monetized') === '1';

    // If row is hidden and not in admin permission mode, skip
    if (row.style.display === 'none' && !adminPermissionNonMonetized && !isMonetized) {
      if (chk) chk.checked = false;
    }

    if (chk && chk.checked) {
      totalCheckedViews += views;
      activeAuthorCount++;
      activeRows.push({ row, views, isMonetized });
    } else {
      const amtInput = row.querySelector('.pool-editable-amount');
      if (amtInput && !row.hasAttribute('data-manual-edit')) {
        amtInput.value = "0.00";
      }
      const shareEl = row.querySelector('.row-view-share');
      if (shareEl) shareEl.textContent = "0.0%";
      const cpmEl = row.querySelector('.row-pool-cpm');
      if (cpmEl) cpmEl.textContent = "$0.00 CPM";
    }
  });

  // Effective CPM across all checked views for this net pool
  const effectivePoolCpm = (totalCheckedViews > 0) ? (netDistributablePool / (totalCheckedViews / 1000)) : 0;

  let calculatedDisbursalTotal = 0;
  activeRows.forEach(({ row, views, isMonetized }) => {
    // Exact proportional view-share of the remaining distributable pool
    const shareFraction = (totalCheckedViews > 0) ? (views / totalCheckedViews) : 0;
    const sharePct = (shareFraction * 100).toFixed(1);
    
    // Author gets exactly their proportional share of net distributable pool
    const allocatedPayout = netDistributablePool * shareFraction;
    const authorCpm = (views > 0) ? (allocatedPayout / (views / 1000)) : effectivePoolCpm;

    const shareEl = row.querySelector('.row-view-share');
    const cpmEl = row.querySelector('.row-pool-cpm');
    const amtInput = row.querySelector('.pool-editable-amount');

    if (shareEl) shareEl.textContent = sharePct + '%';
    if (cpmEl) cpmEl.textContent = '$' + authorCpm.toFixed(2) + ' CPM';

    if (amtInput && !row.hasAttribute('data-manual-edit')) {
      amtInput.value = allocatedPayout.toFixed(2);
    }

    const currentVal = parseFloat(amtInput ? amtInput.value : allocatedPayout) || 0;
    calculatedDisbursalTotal += currentVal;
  });

  // Retained treasury reserve
  const reserveRetained = Math.max(0, netDistributablePool - calculatedDisbursalTotal);

  // Update Telemetry Display Ribbon
  const dispGross = document.getElementById('disp_gross_rev');
  if (dispGross) dispGross.textContent = '$' + grossRev.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
  const dispMaint = document.getElementById('disp_maintenance_cut');
  if (dispMaint) dispMaint.textContent = '$' + maintenanceCut.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
  const dispPool = document.getElementById('disp_creator_pool');
  if (dispPool) dispPool.textContent = '$' + netDistributablePool.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
  const dispDisb = document.getElementById('disp_total_disburse');
  if (dispDisb) dispDisb.textContent = '$' + calculatedDisbursalTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
  const dispRes = document.getElementById('disp_reserve_retained');
  if (dispRes) dispRes.textContent = '$' + reserveRetained.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
  const dispCpm = document.getElementById('disp_effective_cpm');
  if (dispCpm) dispCpm.textContent = '$' + effectivePoolCpm.toFixed(2) + ' CPM';
  const dispAuth = document.getElementById('disp_authors_count');
  if (dispAuth) dispAuth.textContent = `${activeAuthorCount} authors included`;
  const dispViews = document.getElementById('disp_total_views_sum');
  if (dispViews) dispViews.textContent = `Across ${totalCheckedViews.toLocaleString()} total reads`;

  // Update Call-To-Action Summary
  const summaryEl = document.getElementById('cta_distribution_summary');
  if (summaryEl) {
    summaryEl.innerHTML = `Disbursing <strong>$${calculatedDisbursalTotal.toFixed(2)} USD</strong> across <strong>${activeAuthorCount} creators</strong>. Platform retains <strong>$${maintenanceCut.toFixed(2)}</strong> maintenance cut and <strong>$${reserveRetained.toFixed(2)}</strong> treasury reserve.`;
  }
}

function updateManualDisbursalTotal() {
  let manualTotal = 0;
  let count = 0;
  document.querySelectorAll('.pool-author-row').forEach(row => {
    const chk = row.querySelector('.pool-author-chk');
    const amtInput = row.querySelector('.pool-editable-amount');
    if (chk && chk.checked && amtInput) {
      row.setAttribute('data-manual-edit', 'true');
      manualTotal += Math.max(0, parseFloat(amtInput.value || 0));
      count++;
    }
  });

  const grossRev = Math.max(0, parseFloat(document.getElementById('pool_gross_revenue').value || 0));
  const cutPct = Math.min(100, Math.max(0, parseFloat(document.getElementById('pool_platform_cut_pct').value || 0)));
  const maintenanceCut = grossRev * (cutPct / 100);
  const netDistributablePool = Math.max(0, grossRev - maintenanceCut);
  const reserveRetained = Math.max(0, netDistributablePool - manualTotal);

  document.getElementById('disp_total_disburse').textContent = '$' + manualTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
  document.getElementById('disp_reserve_retained').textContent = '$' + reserveRetained.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
  document.getElementById('disp_authors_count').textContent = `${count} authors (Manual Custom Adjustments)`;

  const summaryEl = document.getElementById('cta_distribution_summary');
  if (summaryEl) {
    summaryEl.innerHTML = `Disbursing <strong>$${manualTotal.toFixed(2)} USD</strong> (custom adjusted) across <strong>${count} creators</strong>. Platform retains <strong>$${maintenanceCut.toFixed(2)}</strong> maintenance cut and <strong>$${reserveRetained.toFixed(2)}</strong> treasury reserve.`;
  }
}

function confirmPoolDistribution(e) {
  let count = 0;
  let total = 0;
  document.querySelectorAll('.pool-author-row').forEach(row => {
    const chk = row.querySelector('.pool-author-chk');
    const amtInput = row.querySelector('.pool-editable-amount');
    if (chk && chk.checked && amtInput) {
      const amt = parseFloat(amtInput.value || 0);
      if (amt > 0) {
        count++;
        total += amt;
      }
    }
  });

  if (count === 0 || total <= 0) {
    alert('Please include at least one author with a payout amount greater than $0.00.');
    e.preventDefault();
    return false;
  }

  const periodLabel = document.getElementById('pool_period_label').value;
  const msg = `⚡ CONFIRM CREATOR ROYALTY POOL DISBURSAL:\n\n` +
              `• Period: ${periodLabel}\n` +
              `• Recipient Creators: ${count}\n` +
              `• Total Disbursal: $${total.toFixed(2)} USD\n\n` +
              `This will credit each creator's balance immediately and create immutable double-entry ledger records. Proceed?`;
  return confirm(msg);
}

// Initial calculation on page load: filter to monetized only by default
document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('royaltyPoolTable')) {
    filterPoolMonetizedOnly();
  }
});
</script>

<!-- Decline Monetization Modal -->
<div id="declineMonetizationModal" class="admin-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 500px; padding: 2rem; background: #0f172a; border: 1px solid rgba(239,68,68,0.3); border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.8);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
        ❌ Decline Monetization Application
      </h3>
      <button type="button" onclick="closeDeclineMonetizationModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">&times;</button>
    </div>

    <p style="font-size: 0.85rem; color: #94a3b8; margin: 0 0 1rem 0;">
      Decline application for <strong id="decline_author_name" style="color: #f8fafc;"></strong>. An in-app notification with your feedback will be dispatched to the creator.
    </p>

    <form id="decline_monetization_form" method="POST">
      <input type="hidden" name="author_id" id="decline_author_id" value="">
      <input type="hidden" name="action" value="reject">

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label" style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 0.35rem; font-weight: 600;">Feedback / Reason for Decision</label>
        <textarea name="reason" rows="3" class="admin-modal-textarea" placeholder="e.g. Please publish 2 more technical architecture articles and increase community engagement before reapplying." style="width: 100%; background: #070a13; border: 1px solid rgba(148,163,184,0.2); border-radius: 6px; padding: 0.65rem 0.75rem; color: #f8fafc; font-size: 0.85rem;" required></textarea>
      </div>

      <div style="flex-wrap: wrap; align-items: center; display: flex; justify-content: flex-end; gap: 0.5rem;">
        <button type="button" onclick="closeDeclineMonetizationModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #ef4444; border-color: #ef4444; font-weight: 700;">
          Confirm Decline ❌
        </button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
