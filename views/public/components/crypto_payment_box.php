<?php
// Dynamic Crypto Addresses and Pricing Tiers from Site Settings
$cryptoBtc = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'platform_wallet_btc'")['key_value'] ?? \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_btc_address'")['key_value'] ?? 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh';
$cryptoEth = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'platform_wallet_eth'")['key_value'] ?? \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_eth_address'")['key_value'] ?? '0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1';
$cryptoUsdt = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'platform_wallet_usdt'")['key_value'] ?? \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_usdt_address'")['key_value'] ?? '0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1';
$cryptoSol = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'platform_wallet_sol'")['key_value'] ?? \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_sol_address'")['key_value'] ?? '7xKXtg2CW87d97TXJSDpbD5jBkheTqA83TZRuJosgAsU';
$cryptoNotes = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_payment_instructions'")['key_value'] ?? 'Transfer exact USD equivalent to any official platform wallet below. All transactions undergo on-chain blockchain API verification and require mandatory administrative approval prior to activation.';

$tier7Price = (float)(\App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'ad_tier_7_price'")['key_value'] ?? 49);
$tier30Price = (float)(\App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'ad_tier_30_price'")['key_value'] ?? 149);
$tier90Price = (float)(\App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'ad_tier_90_price'")['key_value'] ?? 299);
?>

<div style="background: rgba(245,158,11,0.04); border: 1px solid rgba(245,158,11,0.25); border-radius: 14px; padding: 1.75rem; margin-bottom: 2rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem;">
      <span class="ts-badge ts-badge-gold" style="font-weight: 800;">OFFICIAL CRYPTO TREASURY</span>
      <span class="ts-badge ts-badge-cyan">DECENTRALIZED SETTLEMENT</span>
    </div>
    <!-- TS-CR-10 CoinGecko Spot Exchange Rate Badge -->
    <div id="coingecko-spot-rate-pill" style="font-size: 0.78rem; font-family: var(--ts-font-mono); color: #34d399; background: rgba(16,185,129,0.1); padding: 0.2rem 0.6rem; border-radius: 20px; border: 1px solid rgba(16,185,129,0.3);">
      ⚡ Live Crypto Rates: 1 BTC ≈ $64,250 &bull; 1 ETH ≈ $3,450 &bull; 1 SOL ≈ $145
    </div>
  </div>

  <h3 style="font-size: 1.25rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">
    🪙 Cryptocurrency Treasury Payment &amp; Duration Calculator
  </h3>
  <p style="color: var(--ts-text-secondary); font-size: 0.85rem; margin: 0 0 1.5rem 0; line-height: 1.5;">
    <?= htmlspecialchars($cryptoNotes) ?>
  </p>

  <!-- Hidden Fields for chosen duration and price -->
  <input type="hidden" name="duration_tier" id="selected_duration_tier" value="30">
  <input type="hidden" name="pricing_amount_usd" id="selected_payment_amount_usd" value="<?= $tier30Price ?>">

  <!-- TS-CR-11 Duration Tier Selector with Live Pricing & Discounts -->
  <div style="margin-bottom: 1.5rem; background: var(--ts-bg-surface-elevated); padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid var(--ts-border-main);">
    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--ts-text-primary); margin-bottom: 0.65rem;">
      Select Sponsorship Duration Tier (Amounts &amp; QR Codes Update Automatically)
    </label>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;" id="durationTierGrid">
      <label id="tier_card_7" onclick="selectDurationTier(7, <?= $tier7Price ?>)" style="display: flex; flex-direction: column; padding: 0.85rem; border-radius: 10px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); cursor: pointer; text-align: center; transition: all 0.2s ease;">
        <input type="radio" name="duration_tier_radio" value="7" style="display: none;">
        <strong style="color: var(--ts-text-primary); font-size: 0.95rem;">7 Days (Trial)</strong>
        <span style="font-size: 1.1rem; font-weight: 800; color: var(--ts-text-primary); margin: 0.25rem 0;">$<?= number_format($tier7Price, 0) ?></span>
        <span style="font-size: 0.72rem; color: var(--ts-text-secondary);">Standard Rate</span>
      </label>
      <label id="tier_card_30" onclick="selectDurationTier(30, <?= $tier30Price ?>)" style="display: flex; flex-direction: column; padding: 0.85rem; border-radius: 10px; border: 2px solid var(--ts-primary); background: rgba(56,189,248,0.06); cursor: pointer; text-align: center; transition: all 0.2s ease;">
        <input type="radio" name="duration_tier_radio" value="30" checked style="display: none;">
        <strong style="color: var(--ts-text-primary); font-size: 0.95rem;">30 Days (Standard)</strong>
        <span style="font-size: 1.1rem; font-weight: 800; color: #38bdf8; margin: 0.25rem 0;">$<?= number_format($tier30Price, 0) ?></span>
        <span style="font-size: 0.72rem; color: #10b981; font-weight: 700;">Most Popular 🔥</span>
      </label>
      <label id="tier_card_90" onclick="selectDurationTier(90, <?= $tier90Price ?>)" style="display: flex; flex-direction: column; padding: 0.85rem; border-radius: 10px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); cursor: pointer; text-align: center; transition: all 0.2s ease;">
        <input type="radio" name="duration_tier_radio" value="90" style="display: none;">
        <strong style="color: var(--ts-text-primary); font-size: 0.95rem;">90 Days (Enterprise)</strong>
        <span style="font-size: 1.1rem; font-weight: 800; color: #ec4899; margin: 0.25rem 0;">$<?= number_format($tier90Price, 0) ?></span>
        <span style="font-size: 0.72rem; color: #ec4899; font-weight: 700;">30% Bulk Saver</span>
      </label>
    </div>
  </div>

  <!-- Live Payment Amount Summary Banner -->
  <div style="background: linear-gradient(135deg, rgba(56,189,248,0.1) 0%, rgba(99,102,241,0.1) 100%); border: 1px solid rgba(56,189,248,0.3); border-radius: 12px; padding: 1.15rem 1.35rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700; color: var(--ts-text-muted); letter-spacing: 0.05em;">
        Selected Campaign Duration &amp; Due Total
      </div>
      <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.25rem;">
        <span id="display_due_usd" style="font-size: 1.85rem; font-weight: 900; color: #38bdf8; font-family: var(--ts-font-mono);">$<?= number_format($tier30Price, 0) ?></span>
        <span id="display_duration_label" style="font-size: 0.88rem; color: var(--ts-text-primary); font-weight: 600;">(30 Days Active Placement)</span>
      </div>
    </div>
    <div style="text-align: right;">
      <div style="font-size: 0.75rem; color: var(--ts-text-secondary); margin-bottom: 0.25rem;">Exact Crypto Required:</div>
      <div id="crypto_required_pills" style="display: flex; gap: 0.4rem; flex-wrap: wrap; justify-content: flex-end;">
        <span class="ts-badge ts-badge-green" id="req_usdt" style="font-family: var(--ts-font-mono); font-size: 0.78rem;"><?= $tier30Price ?> USDT</span>
        <span class="ts-badge ts-badge-gold" id="req_btc" style="font-family: var(--ts-font-mono); font-size: 0.78rem;"><?= number_format($tier30Price / 64250, 5) ?> BTC</span>
        <span class="ts-badge ts-badge-indigo" id="req_eth" style="font-family: var(--ts-font-mono); font-size: 0.78rem;"><?= number_format($tier30Price / 3450, 4) ?> ETH</span>
        <span class="ts-badge ts-badge-cyan" id="req_sol" style="font-family: var(--ts-font-mono); font-size: 0.78rem;"><?= number_format($tier30Price / 145, 3) ?> SOL</span>
      </div>
    </div>
  </div>

  <!-- Crypto Wallet Addresses List with Dynamic BIP-21 / EIP-681 QR Code Generator -->
  <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
    
    <!-- USDT TRC-20 / ERC-20 -->
    <?php if (!empty($cryptoUsdt)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 0.85rem 1.15rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div style="flex: 1; min-width: 240px;">
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; font-weight: 800;">USDT (TRC-20 / ERC-20)</span>
            <span id="usdt_amount_tag" style="font-size: 0.75rem; font-family: var(--ts-font-mono); color: #10b981; font-weight: 700;"><?= $tier30Price ?> USDT</span>
          </div>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoUsdt) ?>
          </div>
          <input type="hidden" id="addr_usdt" value="<?= htmlspecialchars($cryptoUsdt) ?>">
        </div>
        <div style="display: flex; gap: 0.4rem; align-items: center;">
          <button type="button" onclick="openDynamicCryptoQr('USDT')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.75rem; color: #38bdf8;">
            <i data-lucide="qr-code" style="width: 13px; height: 13px;"></i> QR Code (Amount Included)
          </button>
          <button type="button" onclick="copyCryptoText('addr_usdt', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">
            <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Address
          </button>
          <button type="button" onclick="copyExactAmount('USDT', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.65rem; color: #10b981;">
            <i data-lucide="dollar-sign" style="width: 12px; height: 12px;"></i> Amount
          </button>
        </div>
      </div>
    <?php endif; ?>

    <!-- Bitcoin (BTC) -->
    <?php if (!empty($cryptoBtc)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 0.85rem 1.15rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div style="flex: 1; min-width: 240px;">
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="ts-badge ts-badge-gold" style="font-size: 0.68rem; font-weight: 800;">Bitcoin (BTC Native)</span>
            <span id="btc_amount_tag" style="font-size: 0.75rem; font-family: var(--ts-font-mono); color: #f59e0b; font-weight: 700;"><?= number_format($tier30Price / 64250, 5) ?> BTC</span>
          </div>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoBtc) ?>
          </div>
          <input type="hidden" id="addr_btc" value="<?= htmlspecialchars($cryptoBtc) ?>">
        </div>
        <div style="display: flex; gap: 0.4rem; align-items: center;">
          <button type="button" onclick="openDynamicCryptoQr('BTC')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.75rem; color: #f59e0b;">
            <i data-lucide="qr-code" style="width: 13px; height: 13px;"></i> QR Code (BIP-21 Amount)
          </button>
          <button type="button" onclick="copyCryptoText('addr_btc', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">
            <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Address
          </button>
          <button type="button" onclick="copyExactAmount('BTC', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.65rem; color: #f59e0b;">
            <i data-lucide="dollar-sign" style="width: 12px; height: 12px;"></i> Amount
          </button>
        </div>
      </div>
    <?php endif; ?>

    <!-- Ethereum (ETH / ERC-20) -->
    <?php if (!empty($cryptoEth)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 0.85rem 1.15rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div style="flex: 1; min-width: 240px;">
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="ts-badge ts-badge-indigo" style="font-size: 0.68rem; font-weight: 800;">Ethereum (ETH / ERC-20)</span>
            <span id="eth_amount_tag" style="font-size: 0.75rem; font-family: var(--ts-font-mono); color: #818cf8; font-weight: 700;"><?= number_format($tier30Price / 3450, 4) ?> ETH</span>
          </div>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoEth) ?>
          </div>
          <input type="hidden" id="addr_eth" value="<?= htmlspecialchars($cryptoEth) ?>">
        </div>
        <div style="display: flex; gap: 0.4rem; align-items: center;">
          <button type="button" onclick="openDynamicCryptoQr('ETH')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.75rem; color: #818cf8;">
            <i data-lucide="qr-code" style="width: 13px; height: 13px;"></i> QR Code (EIP-681 Amount)
          </button>
          <button type="button" onclick="copyCryptoText('addr_eth', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">
            <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Address
          </button>
          <button type="button" onclick="copyExactAmount('ETH', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.65rem; color: #818cf8;">
            <i data-lucide="dollar-sign" style="width: 12px; height: 12px;"></i> Amount
          </button>
        </div>
      </div>
    <?php endif; ?>

    <!-- Solana (SOL) -->
    <?php if (!empty($cryptoSol)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 0.85rem 1.15rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div style="flex: 1; min-width: 240px;">
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; font-weight: 800;">Solana (SOL Native)</span>
            <span id="sol_amount_tag" style="font-size: 0.75rem; font-family: var(--ts-font-mono); color: #38bdf8; font-weight: 700;"><?= number_format($tier30Price / 145, 3) ?> SOL</span>
          </div>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoSol) ?>
          </div>
          <input type="hidden" id="addr_sol" value="<?= htmlspecialchars($cryptoSol) ?>">
        </div>
        <div style="display: flex; gap: 0.4rem; align-items: center;">
          <button type="button" onclick="openDynamicCryptoQr('SOL')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.75rem; color: #38bdf8;">
            <i data-lucide="qr-code" style="width: 13px; height: 13px;"></i> QR Code (Solana Pay Amount)
          </button>
          <button type="button" onclick="copyCryptoText('addr_sol', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">
            <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Address
          </button>
          <button type="button" onclick="copyExactAmount('SOL', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.65rem; color: #38bdf8;">
            <i data-lucide="dollar-sign" style="width: 12px; height: 12px;"></i> Amount
          </button>
        </div>
      </div>
    <?php endif; ?>

  </div>

  <!-- TXID & Payment Verification Fields -->
  <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
    <div>
      <label class="ts-form-label">Payment Coin Used</label>
      <select name="crypto_currency" id="crypto_currency_select" class="ts-form-input">
        <option value="USDT">USDT (Tether)</option>
        <option value="BTC">Bitcoin (BTC)</option>
        <option value="ETH">Ethereum (ETH)</option>
        <option value="SOL">Solana (SOL)</option>
        <option value="OTHER">Other / Wire</option>
      </select>
    </div>
    <div>
      <label class="ts-form-label">Transaction Hash / TXID * (Requires Mandatory Admin Review)</label>
      <input type="text" name="crypto_txid" class="ts-form-input" placeholder="Paste on-chain transaction hash or transfer ID..." style="font-family: var(--ts-font-mono);" required>
    </div>
  </div>
</div>

<!-- Modal for Dynamic BIP-21 / EIP-681 QR Code with Integrated Amount -->
<div id="cryptoQrModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-card" style="width: 100%; max-width: 460px; padding: 2rem; border-radius: 16px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); text-align: center;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h3 id="qr_modal_coin_name" style="font-size: 1.25rem; color: var(--ts-text-primary); margin: 0;">Scan to Pay</h3>
      <button onclick="document.getElementById('cryptoQrModal').style.display='none'" style="background: none; border: none; color: var(--ts-text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <!-- Amount Badge inside QR Modal -->
    <div style="background: rgba(56,189,248,0.1); border: 1px solid rgba(56,189,248,0.3); border-radius: 8px; padding: 0.65rem 1rem; margin-bottom: 1.25rem;">
      <span style="font-size: 0.75rem; color: var(--ts-text-secondary); text-transform: uppercase; font-weight: 700;">Required Exact Transfer Amount</span>
      <div id="qr_modal_amount_headline" style="font-size: 1.35rem; font-weight: 800; color: #38bdf8; font-family: var(--ts-font-mono); margin-top: 0.15rem;">
        149 USDT ($149.00 USD)
      </div>
    </div>
    
    <div style="background: #ffffff; padding: 1.25rem; border-radius: 12px; display: inline-block; margin-bottom: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
      <img id="qr_modal_img" src="" alt="Crypto QR Code" style="width: 210px; height: 210px; display: block;">
    </div>

    <div style="font-size: 0.72rem; color: var(--ts-text-muted); margin-bottom: 0.4rem;">Platform Wallet Address:</div>
    <div style="font-family: var(--ts-font-mono); font-size: 0.78rem; color: #38bdf8; word-break: break-all; margin-bottom: 1.25rem; background: var(--ts-bg-surface-elevated); padding: 0.5rem; border-radius: 6px;" id="qr_modal_address"></div>

    <div style="display: flex; gap: 0.5rem;">
      <a id="qr_modal_deeplink" href="#" class="ts-btn ts-btn-primary" style="flex: 1; font-size: 0.88rem; text-decoration: none;">
        Open in Web3 Wallet ↗
      </a>
      <button type="button" onclick="document.getElementById('cryptoQrModal').style.display='none'" class="ts-btn ts-btn-outline" style="font-size: 0.88rem;">
        Close
      </button>
    </div>
  </div>
</div>

<script>
// Live pricing calculation variables
const cryptoRates = {
  BTC: 64250,
  ETH: 3450,
  SOL: 145,
  USDT: 1
};

let currentDurationDays = 30;
let currentAmountUsd = <?= $tier30Price ?>;

function selectDurationTier(days, price) {
  currentDurationDays = days;
  currentAmountUsd = price;

  document.getElementById('selected_duration_tier').value = days;
  document.getElementById('selected_payment_amount_usd').value = price;

  // Update Tier Cards UI
  [7, 30, 90].forEach(d => {
    const card = document.getElementById('tier_card_' + d);
    if (!card) return;
    if (d === days) {
      card.style.borderColor = 'var(--ts-primary)';
      card.style.background = 'rgba(56,189,248,0.06)';
      card.style.borderWidth = '2px';
    } else {
      card.style.borderColor = 'var(--ts-border-main)';
      card.style.background = 'var(--ts-bg-surface)';
      card.style.borderWidth = '1px';
    }
  });

  // Update Summary Display
  const dueUsdElem = document.getElementById('display_due_usd');
  if (dueUsdElem) dueUsdElem.innerText = '$' + price.toLocaleString();

  const labelElem = document.getElementById('display_duration_label');
  if (labelElem) labelElem.innerText = '(' + days + ' Days Active Placement)';

  // Calculate Crypto Equivalents
  const usdtVal = price.toFixed(2);
  const btcVal = (price / cryptoRates.BTC).toFixed(5);
  const ethVal = (price / cryptoRates.ETH).toFixed(4);
  const solVal = (price / cryptoRates.SOL).toFixed(3);

  // Update Required Pills
  const pillUsdt = document.getElementById('req_usdt');
  if (pillUsdt) pillUsdt.innerText = usdtVal + ' USDT';

  const pillBtc = document.getElementById('req_btc');
  if (pillBtc) pillBtc.innerText = btcVal + ' BTC';

  const pillEth = document.getElementById('req_eth');
  if (pillEth) pillEth.innerText = ethVal + ' ETH';

  const pillSol = document.getElementById('req_sol');
  if (pillSol) pillSol.innerText = solVal + ' SOL';

  // Update Tag Badges
  const tagUsdt = document.getElementById('usdt_amount_tag');
  if (tagUsdt) tagUsdt.innerText = usdtVal + ' USDT';

  const tagBtc = document.getElementById('btc_amount_tag');
  if (tagBtc) tagBtc.innerText = btcVal + ' BTC';

  const tagEth = document.getElementById('eth_amount_tag');
  if (tagEth) tagEth.innerText = ethVal + ' ETH';

  const tagSol = document.getElementById('sol_amount_tag');
  if (tagSol) tagSol.innerText = solVal + ' SOL';
}

function openDynamicCryptoQr(coin) {
  let address = '';
  let amountStr = '';
  let deeplink = '';

  if (coin === 'BTC') {
    address = document.getElementById('addr_btc')?.value || '';
    const btcAmount = (currentAmountUsd / cryptoRates.BTC).toFixed(5);
    amountStr = btcAmount + ' BTC ($' + currentAmountUsd + ' USD)';
    deeplink = 'bitcoin:' + address + '?amount=' + btcAmount + '&label=TechScout+Sponsorship';
  } else if (coin === 'ETH') {
    address = document.getElementById('addr_eth')?.value || '';
    const ethAmount = (currentAmountUsd / cryptoRates.ETH).toFixed(4);
    amountStr = ethAmount + ' ETH ($' + currentAmountUsd + ' USD)';
    deeplink = 'ethereum:' + address + '?value=' + ethAmount + '&label=TechScout+Sponsorship';
  } else if (coin === 'SOL') {
    address = document.getElementById('addr_sol')?.value || '';
    const solAmount = (currentAmountUsd / cryptoRates.SOL).toFixed(3);
    amountStr = solAmount + ' SOL ($' + currentAmountUsd + ' USD)';
    deeplink = 'solana:' + address + '?amount=' + solAmount + '&label=TechScout+Sponsorship';
  } else {
    // USDT
    address = document.getElementById('addr_usdt')?.value || '';
    amountStr = currentAmountUsd.toFixed(2) + ' USDT ($' + currentAmountUsd + ' USD)';
    deeplink = address;
  }

  document.getElementById('qr_modal_coin_name').innerText = 'Scan to Pay ' + coin;
  document.getElementById('qr_modal_amount_headline').innerText = amountStr;
  document.getElementById('qr_modal_address').innerText = address;
  document.getElementById('qr_modal_deeplink').href = deeplink;

  // Generate dynamic QR code encoding the exact payment URI with amount
  const qrData = deeplink || address;
  const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' + encodeURIComponent(qrData);
  document.getElementById('qr_modal_img').src = qrUrl;

  document.getElementById('cryptoQrModal').style.display = 'flex';
}

function copyCryptoText(id, btn) {
  const input = document.getElementById(id);
  if (!input) return;
  navigator.clipboard.writeText(input.value).then(() => {
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="check" style="width:12px;height:12px;"></i> Copied!';
    if (window.lucide) lucide.createIcons();
    setTimeout(() => { btn.innerHTML = origHtml; if (window.lucide) lucide.createIcons(); }, 2000);
  });
}

function copyExactAmount(coin, btn) {
  let amt = '';
  if (coin === 'BTC') amt = (currentAmountUsd / cryptoRates.BTC).toFixed(5);
  else if (coin === 'ETH') amt = (currentAmountUsd / cryptoRates.ETH).toFixed(4);
  else if (coin === 'SOL') amt = (currentAmountUsd / cryptoRates.SOL).toFixed(3);
  else amt = currentAmountUsd.toFixed(2);

  navigator.clipboard.writeText(amt).then(() => {
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="check" style="width:12px;height:12px;"></i> ' + amt + '!';
    if (window.lucide) lucide.createIcons();
    setTimeout(() => { btn.innerHTML = origHtml; if (window.lucide) lucide.createIcons(); }, 2000);
  });
}

function updateCryptoCalculation(duration) {
  const dur = parseInt(duration, 10);
  let price = <?= $tier30Price ?>;
  if (dur === 7) price = <?= $tier7Price ?>;
  else if (dur === 90) price = <?= $tier90Price ?>;
  selectDurationTier(dur, price);
}
</script>
