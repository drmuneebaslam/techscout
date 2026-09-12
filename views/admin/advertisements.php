<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
  <div>
    <h1 style="font-size: 2rem; color: var(--admin-text-primary); margin: 0 0 0.25rem 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
      Ad Placements &amp; Monetization Control Center
    </h1>
    <p style="color: var(--admin-text-secondary); font-size: 0.92rem; margin: 0;">Manage display slots, popunders, sponsor contracts, crypto platform wallets, and creator tip ledgers.</p>
  </div>

  <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
    <button onclick="openNewAdModal()" class="admin-btn admin-btn-primary">
      <i data-lucide="plus-circle"></i> Create Ad Campaign
    </button>
    <a href="<?= url('/sponsor') ?>" target="_blank" class="admin-btn admin-btn-outline" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
      <i data-lucide="external-link"></i> Public Sponsor Portal ↗
    </a>
  </div>
</div>

<!-- Ad Creation & Editing Modal (Dual Theme) -->
<div id="adFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-card" style="width: 100%; max-width: 720px; padding: 2rem; border-radius: 16px; border: 1px solid var(--admin-border-main); max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h2 id="modalAdTitle" style="font-size: 1.35rem; color: var(--admin-text-primary); margin: 0;">Create Ad Placement</h2>
      <button onclick="document.getElementById('adFormModal').style.display='none'" style="background: none; border: none; color: var(--admin-text-tertiary); font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form id="adminAdForm" action="<?= url('/admin/advertisements/save') ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="ad_id" value="">

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-form-label" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">Campaign / Sponsor Name *</label>
        <input type="text" name="title" id="ad_title" placeholder="e.g. AWS Cloud Credits Sponsorship" class="admin-input" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">Slot Location</label>
          <select name="slot_location" id="ad_slot_location" class="admin-select">
            <option value="header_banner">Header Banner (Top)</option>
            <option value="article_inline">Article Inline (Between sections)</option>
            <option value="sidebar_sticky">Sidebar Sticky (Right)</option>
            <option value="category_featured">Category Featured</option>
            <option value="popunder_direct">Direct Link / Popunder Trigger</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">Contract Budget (USD)</label>
          <input type="number" step="0.01" name="cost_usd" id="ad_cost_usd" placeholder="299.00" class="admin-input">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">Destination Target URL *</label>
          <input type="url" name="target_url" id="ad_target_url" placeholder="https://..." class="admin-input" required>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">Status</label>
          <select name="status" id="ad_status" class="admin-select">
            <option value="active">Active</option>
            <option value="paused">Paused</option>
            <option value="completed">Completed</option>
          </select>
        </div>
      </div>

      <!-- MISTAKE-23 Compliance: Direct File Upload Button + Image URL with Preview -->
      <div style="margin-bottom: 1.5rem; background: var(--admin-bg-surface); padding: 1.25rem; border-radius: 12px; border: 1px dashed var(--admin-border-main);">
        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.5rem;">
          Banner Creative Image (Upload Button or Direct URL)
        </label>
        
        <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap;">
          <input type="file" id="ad_file_input" accept="image/*" onchange="handleAdCreativeUpload(this)" style="display: none;">
          <button type="button" onclick="document.getElementById('ad_file_input').click()" class="admin-btn admin-btn-outline" style="font-size: 0.82rem;">
            <i data-lucide="upload"></i> Upload Image Creative
          </button>
          <span id="ad_upload_status" style="font-size: 0.78rem; color: var(--admin-text-secondary);">PNG, JPG, WebP supported</span>
        </div>

        <input type="text" name="image_url" id="ad_image_url" placeholder="Or enter direct image URL (https://... or /storage/uploads/...)" class="admin-input" oninput="previewAdImage(this.value)">

        <div id="ad_preview_wrap" style="display: none; margin-top: 1rem; text-align: center;">
          <img id="ad_preview_img" src="" alt="Ad Creative Preview" style="max-height: 140px; max-width: 100%; border-radius: 8px; border: 1px solid var(--admin-border-main); object-fit: contain;">
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('adFormModal').style.display='none'" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary">Save Campaign 🚀</button>
      </div>
    </form>
  </div>
</div>

<!-- 4 Navigation Sub-Tabs -->
<div style="display: flex; gap: 0.5rem; border-bottom: 1px solid var(--admin-border-main); margin-bottom: 1.75rem; overflow-x: auto;">
  <a href="<?= url('/admin/advertisements?tab=ads') ?>" class="ts-subtab-link <?= (($activeTab ?? 'ads') === 'ads') ? 'active' : '' ?>">
    📢 Display Banners &amp; Ad Slots (<?= count($ads ?? []) ?>)
  </a>
  <a href="<?= url('/admin/advertisements?tab=sponsored') ?>" class="ts-subtab-link <?= (($activeTab ?? '') === 'sponsored') ? 'active' : '' ?>">
    🪙 Sponsored Submissions &amp; Crypto Reviews (<?= count($sponsoredSubmissions ?? []) ?>)
    <?php 
      $pendingCount = count(array_filter($sponsoredSubmissions ?? [], fn($s) => ($s['status'] ?? '') === 'pending_review' || ($s['status'] ?? '') === 'pending'));
    ?>
    <?php if ($pendingCount > 0): ?>
      <span class="admin-pill-badge badge-warning" style="font-size: 0.65rem;"><?= $pendingCount ?> PENDING</span>
    <?php endif; ?>
  </a>
  <a href="<?= url('/admin/advertisements?tab=settings') ?>" class="ts-subtab-link <?= (($activeTab ?? '') === 'settings') ? 'active' : '' ?>">
    ⚙️ Platform Pricing &amp; Crypto Wallets
  </a>
  <a href="<?= url('/admin/advertisements?tab=tips') ?>" class="ts-subtab-link <?= (($activeTab ?? '') === 'tips' || ($activeTab ?? '') === 'payouts') ? 'active' : '' ?>">
    💰 Creator Tips &amp; Payouts Ledger
    <?php
      $pendingTipsCount = count(array_filter($authorTips ?? [], fn($t) => ($t['status'] ?? '') === 'pending'));
    ?>
    <?php if ($pendingTipsCount > 0): ?>
      <span class="admin-pill-badge badge-cyan" style="font-size: 0.65rem;"><?= $pendingTipsCount ?> PENDING</span>
    <?php endif; ?>
  </a>
</div>

<!-- TAB 1: DISPLAY BANNERS & POPUNDERS -->
<?php if (($activeTab ?? 'ads') === 'ads'): ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Campaign &amp; Slot</th>
          <th>Target URL</th>
          <th>Impressions</th>
          <th>Clicks</th>
          <th>CTR</th>
          <th>Status</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($ads)): ?>
          <?php foreach ($ads as $ad): ?>
            <tr>
              <td>
                <div style="font-weight: 700; color: var(--admin-text-primary); font-size: 0.95rem;"><?= htmlspecialchars($ad['title']) ?></div>
                <div style="font-size: 0.75rem; color: #818cf8; margin-top: 0.15rem;"><?= htmlspecialchars($ad['slot_location']) ?></div>
              </td>
              <td>
                <a href="<?= htmlspecialchars($ad['target_url']) ?>" target="_blank" style="color: #38bdf8; font-size: 0.85rem; text-decoration: none;">
                  <?= htmlspecialchars(substr($ad['target_url'], 0, 35)) ?>... ↗
                </a>
              </td>
              <td style="font-family: var(--admin-font-mono); color: var(--admin-text-secondary);"><?= number_format((int)$ad['impression_count']) ?></td>
              <td style="font-family: var(--admin-font-mono); color: var(--admin-text-secondary);"><?= number_format((int)$ad['click_count']) ?></td>
              <td style="font-family: var(--admin-font-mono); color: #38bdf8; font-weight: 700;">
                <?= $ad['impression_count'] > 0 ? round(($ad['click_count'] / $ad['impression_count']) * 100, 2) : 0 ?>%
              </td>
              <td>
                <span class="admin-pill-badge <?= $ad['status'] === 'active' ? 'badge-success' : 'badge-warning' ?>">
                  <?= ucfirst(htmlspecialchars($ad['status'])) ?>
                </span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.5rem;">
                  <button onclick="editAd(<?= htmlspecialchars(json_encode($ad), ENT_QUOTES, 'UTF-8') ?>)" class="admin-btn admin-btn-outline admin-btn-sm">Edit</button>
                  <form action="<?= url('/admin/advertisements/delete/' . $ad['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this ad campaign?');" style="margin: 0;">
                    <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">Del</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align: center; padding: 3rem; color: var(--admin-text-secondary);">No active direct display campaigns configured.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<!-- TAB 2: SPONSORED SUBMISSIONS & CRYPTO REVIEWS -->
<?php elseif (($activeTab ?? '') === 'sponsored'): ?>
  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
      <div>
        <h3 style="font-size: 1.15rem; color: var(--admin-text-primary); margin: 0 0 0.25rem 0;">Sponsored Tools &amp; Posts Verification Queue</h3>
        <p style="color: var(--admin-text-secondary); font-size: 0.82rem; margin: 0;">Verify advertiser crypto transaction hashes (TXIDs) on-chain and approve placements.</p>
      </div>
      <a href="<?= url('/sponsor') ?>" target="_blank" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        View Public Sponsor Page ↗
      </a>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Type &amp; Title</th>
          <th>Sponsor Brand &amp; Contact</th>
          <th>Amount &amp; Crypto TXID</th>
          <th>Submitted At</th>
          <th>Status</th>
          <th style="width: 180px; text-align: right;">Review Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($sponsoredSubmissions)): ?>
          <?php foreach ($sponsoredSubmissions as $sub): ?>
            <?php
              $coin = strtoupper($sub['crypto_currency'] ?? 'USDT');
              $txid = $sub['crypto_txid'] ?? '';
              $explorerUrl = '#';
              if ($coin === 'BTC') $explorerUrl = 'https://mempool.space/tx/' . urlencode($txid);
              elseif ($coin === 'ETH') $explorerUrl = 'https://etherscan.io/tx/' . urlencode($txid);
              elseif ($coin === 'SOL') $explorerUrl = 'https://solscan.io/tx/' . urlencode($txid);
              else $explorerUrl = 'https://etherscan.io/tx/' . urlencode($txid);
            ?>
            <tr>
              <td>
                <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem;">
                  <span class="admin-pill-badge <?= $sub['type'] === 'tool' ? 'badge-cyan' : 'badge-warning' ?>" style="font-size: 0.65rem;">
                    <?= strtoupper(htmlspecialchars($sub['type'])) ?>
                  </span>
                  <strong style="color: var(--admin-text-primary); font-size: 0.92rem;"><?= htmlspecialchars($sub['title']) ?></strong>
                </div>
                <?php if (!empty($sub['target_url'])): ?>
                  <a href="<?= htmlspecialchars($sub['target_url']) ?>" target="_blank" style="color: #38bdf8; font-size: 0.78rem; text-decoration: none;">
                    <?= htmlspecialchars(substr($sub['target_url'], 0, 40)) ?>... ↗
                  </a>
                <?php endif; ?>
              </td>
              <td>
                <div style="font-weight: 600; color: var(--admin-text-primary); font-size: 0.85rem;"><?= htmlspecialchars($sub['author_name']) ?></div>
                <div style="font-size: 0.78rem; color: var(--admin-text-secondary);"><?= htmlspecialchars($sub['author_email']) ?></div>
              </td>
              <td>
                <div style="font-weight: 800; color: #10b981; font-family: var(--admin-font-mono); font-size: 0.9rem;">
                  $<?= number_format((float)$sub['pricing_amount_usd'], 2) ?> USD (<?= htmlspecialchars($sub['crypto_currency']) ?>)
                </div>
                <div style="display: flex; align-items: center; gap: 0.35rem; margin-top: 0.2rem;">
                  <code style="font-size: 0.72rem; color: #38bdf8; background: rgba(56,189,248,0.08); padding: 0.15rem 0.4rem; border-radius: 4px; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($sub['crypto_txid']) ?>">
                    <?= htmlspecialchars($sub['crypto_txid']) ?>
                  </code>
                  <a href="<?= htmlspecialchars($explorerUrl) ?>" target="_blank" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.65rem; padding: 0.1rem 0.35rem; color: #818cf8;" title="View on Blockchain Explorer">
                    Verify ↗
                  </a>
                </div>
              </td>
              <td>
                <?= \App\Core\DateHelper::render($sub['created_at'], 'M j, Y • g:i A', 'datetime') ?>
              </td>
              <td>
                <span class="admin-pill-badge <?= $sub['status'] === 'approved' ? 'badge-success' : ($sub['status'] === 'rejected' ? 'badge-danger' : 'badge-warning') ?>">
                  <?= ucfirst(str_replace('_', ' ', htmlspecialchars($sub['status']))) ?>
                </span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.4rem; justify-content: flex-end;">
                  <?php if ($sub['status'] !== 'approved'): ?>
                    <form action="<?= url('/admin/sponsored/approve/' . $sub['id']) ?>" method="POST" style="margin: 0;">
                      <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.3); font-size: 0.72rem; padding: 0.2rem 0.5rem;" title="Approve &amp; Activate Placement">
                        Approve ✓
                      </button>
                    </form>
                  <?php endif; ?>
                  <?php if ($sub['status'] !== 'rejected'): ?>
                    <form action="<?= url('/admin/sponsored/reject/' . $sub['id']) ?>" method="POST" style="margin: 0;">
                      <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 0.72rem; padding: 0.2rem 0.5rem;" title="Mark as Rejected">
                        Reject ✗
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align: center; padding: 3rem; color: var(--admin-text-secondary);">No sponsored submissions received yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<!-- TAB 3: PLATFORM PRICING & CRYPTO WALLETS -->
<?php elseif (($activeTab ?? '') === 'settings'): ?>
  <div class="admin-card" style="padding: 2rem; border-radius: 16px; border: 1px solid var(--admin-border-main); max-width: 900px; margin: 0 auto;">
    <div style="border-bottom: 1px solid var(--admin-border-main); padding-bottom: 1rem; margin-bottom: 1.5rem;">
      <h3 style="font-size: 1.35rem; color: var(--admin-text-primary); margin: 0 0 0.35rem 0;">
        ⚙️ Platform Pricing Engine &amp; Crypto Treasury Wallets
      </h3>
      <p style="color: var(--admin-text-secondary); font-size: 0.88rem; margin: 0;">
        Configure sponsored pricing durations, default direct/popunder ad targets, platform crypto deposit addresses, and author tip split fees.
      </p>
    </div>

    <form action="<?= url('/admin/advertisements/settings') ?>" method="POST">
      <!-- 1. Direct & Popunder Ad URLs -->
      <div style="margin-bottom: 2rem;">
        <h4 style="font-size: 1rem; color: var(--admin-text-primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="link-2" style="width: 18px; color: #818cf8;"></i> Global Popunder &amp; Direct Ad Trigger URLs
        </h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Default Direct Link Ad URL</label>
            <input type="url" name="ad_direct_link_url" value="<?= htmlspecialchars($platformSettings['ad_direct_link_url'] ?? 'https://omg10.com/4/11737440') ?>" class="admin-input" required>
            <span style="font-size: 0.75rem; color: var(--admin-text-secondary);">Triggered when visitors click "Unlock &amp; Copy Blueprint" on single prompts.</span>
          </div>
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Default Popunder Ad URL</label>
            <input type="url" name="ad_popunder_url" value="<?= htmlspecialchars($platformSettings['ad_popunder_url'] ?? 'https://omg10.com/4/11737440') ?>" class="admin-input" required>
            <span style="font-size: 0.75rem; color: var(--admin-text-secondary);">Used for background tab popunders across directory listings.</span>
          </div>
        </div>
      </div>

      <!-- 2. Duration Pricing Tiers -->
      <div style="margin-bottom: 2rem;">
        <h4 style="font-size: 1rem; color: var(--admin-text-primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="tag" style="width: 18px; color: #10b981;"></i> Sponsored Placement Duration Pricing (USD)
        </h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">7 Days Standard Tier ($)</label>
            <input type="number" step="0.01" name="ad_tier_7_price" value="<?= htmlspecialchars($platformSettings['ad_tier_7_price'] ?? '29.00') ?>" class="admin-input" required>
          </div>
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">30 Days Tier ($ - 15% Bulk Discount)</label>
            <input type="number" step="0.01" name="ad_tier_30_price" value="<?= htmlspecialchars($platformSettings['ad_tier_30_price'] ?? '89.00') ?>" class="admin-input" required>
          </div>
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">90 Days Enterprise ($ - 30% Bulk Discount)</label>
            <input type="number" step="0.01" name="ad_tier_90_price" value="<?= htmlspecialchars($platformSettings['ad_tier_90_price'] ?? '199.00') ?>" class="admin-input" required>
          </div>
        </div>
      </div>

      <!-- 3. Central Platform Crypto Treasury Wallets -->
      <div style="margin-bottom: 2rem;">
        <h4 style="font-size: 1rem; color: var(--admin-text-primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="wallet" style="width: 18px; color: #f59e0b;"></i> Platform Central Crypto Treasury Addresses
        </h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">USDT (Tether - ERC-20 / TRC-20)</label>
            <input type="text" name="platform_wallet_usdt" value="<?= htmlspecialchars($platformSettings['platform_wallet_usdt'] ?? '0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1') ?>" class="admin-input" style="font-family: var(--admin-font-mono);">
          </div>
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Bitcoin (BTC Native)</label>
            <input type="text" name="platform_wallet_btc" value="<?= htmlspecialchars($platformSettings['platform_wallet_btc'] ?? 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh') ?>" class="admin-input" style="font-family: var(--admin-font-mono);">
          </div>
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Ethereum (ETH / ERC-20)</label>
            <input type="text" name="platform_wallet_eth" value="<?= htmlspecialchars($platformSettings['platform_wallet_eth'] ?? '0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1') ?>" class="admin-input" style="font-family: var(--admin-font-mono);">
          </div>
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Solana (SOL Native)</label>
            <input type="text" name="platform_wallet_sol" value="<?= htmlspecialchars($platformSettings['platform_wallet_sol'] ?? '7xKXtg2CW87d97TXJSDpbD5jBkheTqA83TZRuJosgAsU') ?>" class="admin-input" style="font-family: var(--admin-font-mono);">
          </div>
        </div>
      </div>

      <!-- 4. Author Tip Platform Split & Payout Rules -->
      <div style="margin-bottom: 2rem;">
        <h4 style="font-size: 1rem; color: var(--admin-text-primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="percent" style="width: 18px; color: #ec4899;"></i> Creator Tip Jar Platform Split &amp; Minimum Payout Threshold
        </h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Platform Fee Percentage (%)</label>
            <input type="number" step="0.5" min="0" max="50" name="author_tip_platform_fee_pct" value="<?= htmlspecialchars($platformSettings['author_tip_platform_fee_pct'] ?? '5') ?>" class="admin-input" required>
            <span style="font-size: 0.75rem; color: var(--admin-text-secondary);">Currently set to 5% platform fee (95% goes directly to creator).</span>
          </div>
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Minimum Author Withdrawal Threshold ($ USD)</label>
            <input type="number" step="1" min="10" name="author_min_payout_threshold" value="<?= htmlspecialchars($platformSettings['author_min_payout_threshold'] ?? '50.00') ?>" class="admin-input" required>
            <span style="font-size: 0.75rem; color: var(--admin-text-secondary);">Creators must accrue at least $50 before requesting crypto payouts.</span>
          </div>
        </div>
      </div>

      <button type="submit" class="admin-btn admin-btn-primary" style="padding: 0.75rem 1.75rem; font-weight: 700;">
        Save All Monetization &amp; Wallet Settings 🚀
      </button>
    </form>
  </div>

<!-- TAB 4: CREATOR TIPS & PAYOUTS LEDGER -->
<?php else: ?>
  <div class="admin-table-wrap" style="padding: 1.5rem; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h3 style="font-size: 1.15rem; color: var(--admin-text-primary); margin: 0;">🪙 Incoming Creator Tips Verification Queue</h3>
      <span class="admin-pill-badge badge-cyan"><?= count($authorTips ?? []) ?> Total Tips Logged</span>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Creator Recipient</th>
          <th>Gross Tip</th>
          <th>Platform Fee (5%)</th>
          <th>Net Author Amount</th>
          <th>TXID &amp; Explorer</th>
          <th>Status</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($authorTips)): ?>
          <?php foreach ($authorTips as $tip): ?>
            <tr>
              <td>
                <strong style="color: var(--admin-text-primary);"><?= htmlspecialchars($tip['author_name'] ?? $tip['author_username']) ?></strong>
                <?php if (!empty($tip['article_url'])): ?>
                  <div style="font-size: 0.75rem; color: var(--admin-text-secondary);">
                    <a href="<?= htmlspecialchars($tip['article_url']) ?>" target="_blank" style="color: #38bdf8; text-decoration: none;">View Source Post ↗</a>
                  </div>
                <?php endif; ?>
              </td>
              <td style="font-family: var(--admin-font-mono); font-weight: 700; color: var(--admin-text-primary);">
                $<?= number_format((float)$tip['gross_amount'], 2) ?> <?= htmlspecialchars($tip['currency']) ?>
              </td>
              <td style="font-family: var(--admin-font-mono); color: #f59e0b;">
                -$<?= number_format((float)$tip['platform_fee'], 2) ?>
              </td>
              <td style="font-family: var(--admin-font-mono); font-weight: 800; color: #10b981;">
                +$<?= number_format((float)$tip['net_author_amount'], 2) ?>
              </td>
              <td>
                <code style="font-size: 0.72rem; color: #38bdf8; background: rgba(56,189,248,0.08); padding: 0.15rem 0.4rem; border-radius: 4px; max-width: 140px; overflow: hidden; text-overflow: ellipsis; display: inline-block;">
                  <?= htmlspecialchars($tip['txid'] ?? 'N/A') ?>
                </code>
              </td>
              <td>
                <span class="admin-pill-badge <?= $tip['status'] === 'verified' ? 'badge-success' : ($tip['status'] === 'rejected' ? 'badge-danger' : 'badge-warning') ?>">
                  <?= ucfirst(htmlspecialchars($tip['status'])) ?>
                </span>
              </td>
              <td style="text-align: right;">
                <?php if ($tip['status'] === 'pending'): ?>
                  <div style="display: inline-flex; gap: 0.35rem;">
                    <form action="<?= url('/admin/advertisements/tips/verify/' . $tip['id']) ?>" method="POST" style="margin: 0;">
                      <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.3); font-size: 0.72rem;">Credit Author ✓</button>
                    </form>
                    <form action="<?= url('/admin/advertisements/tips/reject/' . $tip['id']) ?>" method="POST" style="margin: 0;">
                      <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 0.72rem;">Reject</button>
                    </form>
                  </div>
                <?php else: ?>
                  <span style="font-size: 0.75rem; color: var(--admin-text-secondary);">&mdash;</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--admin-text-secondary);">No creator tips logged yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Payout Requests Section -->
  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h3 style="font-size: 1.15rem; color: var(--admin-text-primary); margin: 0;">💸 Author Withdrawal Requests (Threshold: $50+)</h3>
      <span class="admin-pill-badge badge-warning"><?= count($payoutRequests ?? []) ?> Payouts Processed / Pending</span>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Author Member</th>
          <th>Amount Requested</th>
          <th>Destination Wallet</th>
          <th>Requested At</th>
          <th>Status</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($payoutRequests)): ?>
          <?php foreach ($payoutRequests as $pr): ?>
            <tr>
              <td>
                <strong style="color: var(--admin-text-primary);"><?= htmlspecialchars($pr['username']) ?></strong>
                <div style="font-size: 0.75rem; color: var(--admin-text-secondary);"><?= htmlspecialchars($pr['email']) ?></div>
              </td>
              <td style="font-family: var(--admin-font-mono); font-weight: 800; color: #10b981;">
                $<?= number_format((float)$pr['amount'], 2) ?> <?= htmlspecialchars($pr['payout_currency']) ?>
              </td>
              <td>
                <code style="font-size: 0.75rem; color: #818cf8; word-break: break-all;"><?= htmlspecialchars($pr['payout_wallet']) ?></code>
              </td>
              <td>
                <?= \App\Core\DateHelper::render($pr['created_at'], 'M j, Y • g:i A', 'datetime') ?>
              </td>
              <td>
                <span class="admin-pill-badge <?= $pr['status'] === 'completed' ? 'badge-success' : ($pr['status'] === 'rejected' ? 'badge-danger' : 'badge-warning') ?>">
                  <?= ucfirst(htmlspecialchars($pr['status'])) ?>
                </span>
              </td>
              <td style="text-align: right;">
                <?php if ($pr['status'] === 'requested'): ?>
                  <button onclick="openProcessPayoutModal(<?= $pr['id'] ?>, '<?= htmlspecialchars($pr['amount']) ?>', '<?= htmlspecialchars($pr['payout_wallet']) ?>')" class="admin-btn admin-btn-primary admin-btn-sm" style="font-size: 0.72rem;">
                    Complete Payout
                  </button>
                <?php else: ?>
                  <span style="font-size: 0.75rem; color: var(--admin-text-secondary);">TXID: <?= htmlspecialchars(substr($pr['txid'] ?? '', 0, 12)) ?>...</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--admin-text-secondary);">No author withdrawal requests pending.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<!-- Process Payout Modal -->
<div id="processPayoutModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-card" style="width: 100%; max-width: 500px; padding: 2rem; border-radius: 16px; border: 1px solid var(--admin-border-main);">
    <h3 style="font-size: 1.25rem; color: var(--admin-text-primary); margin: 0 0 1rem 0;">Complete Author Crypto Payout</h3>
    <form id="processPayoutForm" method="POST" action="">
      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Payout Amount</label>
        <div id="payout_modal_amount" style="font-size: 1.2rem; font-weight: 800; color: #10b981; font-family: var(--admin-font-mono);"></div>
      </div>
      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Destination Wallet</label>
        <div id="payout_modal_wallet" style="font-size: 0.85rem; color: #38bdf8; font-family: var(--admin-font-mono); word-break: break-all;"></div>
      </div>
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Blockchain Transfer TXID *</label>
        <input type="text" name="payout_txid" class="admin-input" placeholder="Paste transaction hash after sending crypto..." required style="font-family: var(--admin-font-mono);">
      </div>
      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('processPayoutModal').style.display='none'" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary">Mark as Paid &amp; Notify Creator ✓</button>
      </div>
    </form>
  </div>
</div>

<script>
function openNewAdModal() {
  document.getElementById('adminAdForm').reset();
  document.getElementById('ad_id').value = '';
  document.getElementById('ad_slot_location').value = 'header_banner';
  document.getElementById('ad_status').value = 'active';
  document.getElementById('modalAdTitle').innerText = 'Create New Ad Placement';
  document.getElementById('ad_preview_wrap').style.display = 'none';
  document.getElementById('adFormModal').style.display = 'flex';
}

function editAd(ad) {
  document.getElementById('adminAdForm').reset();
  document.getElementById('ad_id').value = ad.id;
  document.getElementById('ad_title').value = ad.title || '';
  document.getElementById('ad_slot_location').value = ad.slot_location || 'header_banner';
  document.getElementById('ad_cost_usd').value = ad.cost_usd || '';
  document.getElementById('ad_target_url').value = ad.target_url || '';
  document.getElementById('ad_image_url').value = ad.image_url || '';
  document.getElementById('ad_status').value = ad.status || 'active';
  document.getElementById('modalAdTitle').innerText = 'Edit Ad Campaign #' + ad.id;
  if (ad.image_url) {
    previewAdImage(ad.image_url);
  } else {
    document.getElementById('ad_preview_wrap').style.display = 'none';
  }
  document.getElementById('adFormModal').style.display = 'flex';
}

function previewAdImage(url) {
  const wrap = document.getElementById('ad_preview_wrap');
  const img = document.getElementById('ad_preview_img');
  if (url && url.length > 5) {
    img.src = url;
    wrap.style.display = 'block';
  } else {
    wrap.style.display = 'none';
  }
}

// MISTAKE-23 Direct File Upload Handler
async function handleAdCreativeUpload(fileInput) {
  if (!fileInput.files || !fileInput.files[0]) return;
  const file = fileInput.files[0];
  const formData = new FormData();
  formData.append('ad_image_file', file);

  const statusEl = document.getElementById('ad_upload_status');
  statusEl.innerText = 'Uploading...';
  statusEl.style.color = '#38bdf8';

  try {
    const res = await fetch('<?= url('/admin/advertisements/upload-image') ?>', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success && data.image_url) {
      document.getElementById('ad_image_url').value = data.image_url;
      previewAdImage(data.image_url);
      statusEl.innerText = 'Upload complete! ✓';
      statusEl.style.color = '#10b981';
    } else {
      statusEl.innerText = data.error || 'Upload failed';
      statusEl.style.color = '#ef4444';
    }
  } catch (err) {
    statusEl.innerText = 'Upload error';
    statusEl.style.color = '#ef4444';
  }
}

function openProcessPayoutModal(id, amount, wallet) {
  document.getElementById('processPayoutForm').action = '<?= url('/admin/advertisements/payouts/process/') ?>' + id;
  document.getElementById('payout_modal_amount').innerText = '$' + amount + ' USD';
  document.getElementById('payout_modal_wallet').innerText = wallet;
  document.getElementById('processPayoutModal').style.display = 'flex';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
