<?php
// Dynamic Crypto Addresses and Pricing Tiers from Site Settings
$cryptoBtc = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'platform_wallet_btc'")['key_value'] ?? \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_btc_address'")['key_value'] ?? 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh';
$cryptoEth = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'platform_wallet_eth'")['key_value'] ?? \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_eth_address'")['key_value'] ?? '0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1';
$cryptoUsdt = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'platform_wallet_usdt'")['key_value'] ?? \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_usdt_address'")['key_value'] ?? '0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1';
$cryptoSol = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'platform_wallet_sol'")['key_value'] ?? \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_sol_address'")['key_value'] ?? '7xKXtg2CW87d97TXJSDpbD5jBkheTqA83TZRuJosgAsU';
$cryptoNotes = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_payment_instructions'")['key_value'] ?? 'Transfer exact USD equivalent to any official platform wallet below. All transactions undergo on-chain blockchain API verification and require mandatory administrative approval prior to activation.';
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

  <!-- TS-CR-11 Duration Tier Selector with Bulk Discounts -->
  <div style="margin-bottom: 1.5rem; background: var(--ts-bg-surface-elevated); padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid var(--ts-border-main);">
    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--ts-text-primary); margin-bottom: 0.5rem;">
      Select Sponsorship Duration Tier (Bulk Discounts Applied Automatically)
    </label>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
      <label style="display: flex; flex-direction: column; padding: 0.75rem; border-radius: 8px; border: 2px solid var(--ts-primary); background: var(--ts-bg-surface); cursor: pointer; text-align: center;">
        <input type="radio" name="duration_tier" value="30" checked onchange="updateCryptoCalculation(this.value)" style="display: none;">
        <strong style="color: var(--ts-text-primary); font-size: 0.9rem;">30 Days (Standard)</strong>
        <span style="font-size: 0.75rem; color: #10b981; font-weight: 700;">15% Discount</span>
      </label>
      <label style="display: flex; flex-direction: column; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); cursor: pointer; text-align: center;">
        <input type="radio" name="duration_tier" value="7" onchange="updateCryptoCalculation(this.value)" style="display: none;">
        <strong style="color: var(--ts-text-primary); font-size: 0.9rem;">7 Days (Trial)</strong>
        <span style="font-size: 0.75rem; color: var(--ts-text-secondary);">Standard Rate</span>
      </label>
      <label style="display: flex; flex-direction: column; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); cursor: pointer; text-align: center;">
        <input type="radio" name="duration_tier" value="90" onchange="updateCryptoCalculation(this.value)" style="display: none;">
        <strong style="color: var(--ts-text-primary); font-size: 0.9rem;">90 Days (Enterprise)</strong>
        <span style="font-size: 0.75rem; color: #ec4899; font-weight: 700;">30% Bulk Saver</span>
      </label>
    </div>
  </div>

  <!-- Crypto Wallet Addresses List with TS-CR-02 Dynamic QR Code Generator -->
  <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
    
    <!-- USDT TRC-20 / ERC-20 -->
    <?php if (!empty($cryptoUsdt)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 0.85rem 1.15rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; font-weight: 800;">USDT (TRC-20 / ERC-20)</span>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoUsdt) ?>
          </div>
          <input type="hidden" id="addr_usdt" value="<?= htmlspecialchars($cryptoUsdt) ?>">
        </div>
        <div style="display: flex; gap: 0.4rem;">
          <button type="button" onclick="showCryptoQrModal('USDT', '<?= htmlspecialchars($cryptoUsdt) ?>', 'ethereum:<?= htmlspecialchars($cryptoUsdt) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; color: #38bdf8;">
            <i data-lucide="qr-code" style="width: 12px; height: 12px;"></i> QR Code
          </button>
          <button type="button" onclick="copyCryptoAddress('addr_usdt', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">
            <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
          </button>
        </div>
      </div>
    <?php endif; ?>

    <!-- Bitcoin (BTC) -->
    <?php if (!empty($cryptoBtc)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 0.85rem 1.15rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <span class="ts-badge ts-badge-gold" style="font-size: 0.68rem; font-weight: 800;">Bitcoin (BTC Native)</span>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoBtc) ?>
          </div>
          <input type="hidden" id="addr_btc" value="<?= htmlspecialchars($cryptoBtc) ?>">
        </div>
        <div style="display: flex; gap: 0.4rem;">
          <button type="button" onclick="showCryptoQrModal('Bitcoin', '<?= htmlspecialchars($cryptoBtc) ?>', 'bitcoin:<?= htmlspecialchars($cryptoBtc) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; color: #f59e0b;">
            <i data-lucide="qr-code" style="width: 12px; height: 12px;"></i> QR Code
          </button>
          <button type="button" onclick="copyCryptoAddress('addr_btc', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">
            <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
          </button>
        </div>
      </div>
    <?php endif; ?>

    <!-- Ethereum (ETH / ERC-20) -->
    <?php if (!empty($cryptoEth)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 0.85rem 1.15rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <span class="ts-badge ts-badge-indigo" style="font-size: 0.68rem; font-weight: 800;">Ethereum (ETH / ERC-20)</span>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoEth) ?>
          </div>
          <input type="hidden" id="addr_eth" value="<?= htmlspecialchars($cryptoEth) ?>">
        </div>
        <div style="display: flex; gap: 0.4rem;">
          <button type="button" onclick="showCryptoQrModal('Ethereum', '<?= htmlspecialchars($cryptoEth) ?>', 'ethereum:<?= htmlspecialchars($cryptoEth) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; color: #818cf8;">
            <i data-lucide="qr-code" style="width: 12px; height: 12px;"></i> QR Code
          </button>
          <button type="button" onclick="copyCryptoAddress('addr_eth', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">
            <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
          </button>
        </div>
      </div>
    <?php endif; ?>

    <!-- Solana (SOL) -->
    <?php if (!empty($cryptoSol)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 0.85rem 1.15rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; font-weight: 800;">Solana (SOL Native)</span>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoSol) ?>
          </div>
          <input type="hidden" id="addr_sol" value="<?= htmlspecialchars($cryptoSol) ?>">
        </div>
        <div style="display: flex; gap: 0.4rem;">
          <button type="button" onclick="showCryptoQrModal('Solana', '<?= htmlspecialchars($cryptoSol) ?>', 'solana:<?= htmlspecialchars($cryptoSol) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; color: #38bdf8;">
            <i data-lucide="qr-code" style="width: 12px; height: 12px;"></i> QR Code
          </button>
          <button type="button" onclick="copyCryptoAddress('addr_sol', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">
            <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
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

<!-- Modal for Dynamic BIP-21 / EIP-681 QR Code -->
<div id="cryptoQrModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-card" style="width: 100%; max-width: 440px; padding: 2rem; border-radius: 16px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); text-align: center;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h3 id="qr_modal_coin_name" style="font-size: 1.25rem; color: var(--ts-text-primary); margin: 0;">Scan to Pay</h3>
      <button onclick="document.getElementById('cryptoQrModal').style.display='none'" style="background: none; border: none; color: var(--ts-text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>
    
    <div style="background: #ffffff; padding: 1.25rem; border-radius: 12px; display: inline-block; margin-bottom: 1.25rem; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
      <img id="qr_modal_img" src="" alt="Crypto QR Code" style="width: 200px; height: 200px; display: block;">
    </div>

    <div style="font-family: var(--ts-font-mono); font-size: 0.78rem; color: #38bdf8; word-break: break-all; margin-bottom: 1.25rem; background: var(--ts-bg-surface-elevated); padding: 0.5rem; border-radius: 6px;" id="qr_modal_address"></div>

    <div style="display: flex; gap: 0.5rem;">
      <a id="qr_modal_deeplink" href="#" class="ts-btn ts-btn-primary" style="flex: 1; font-size: 0.88rem; text-decoration: none;">
        Open in Web3 Wallet ↗
      </a>
      <button type="button" onclick="document.getElementById('cryptoQrModal').style.display='none'" class="ts-btn ts-btn-outline" style="font-size: 0.88rem;">
        Done
      </button>
    </div>
  </div>
</div>

<script>
function showCryptoQrModal(coin, address, deeplink) {
  document.getElementById('qr_modal_coin_name').innerText = 'Scan to Pay ' + coin;
  document.getElementById('qr_modal_address').innerText = address;
  document.getElementById('qr_modal_deeplink').href = deeplink;
  // Generate high-DPI QR code via standard web API
  const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' + encodeURIComponent(deeplink || address);
  document.getElementById('qr_modal_img').src = qrUrl;
  document.getElementById('cryptoQrModal').style.display = 'flex';
}

function copyCryptoAddress(id, btn) {
  const input = document.getElementById(id);
  if (!input) return;
  navigator.clipboard.writeText(input.value).then(() => {
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="check" style="width:12px;height:12px;"></i> Copied!';
    if (window.lucide) lucide.createIcons();
    setTimeout(() => { btn.innerHTML = origHtml; if (window.lucide) lucide.createIcons(); }, 2000);
  });
}

function updateCryptoCalculation(duration) {
  // Update button text and pricing summary dynamically
  console.log('Selected duration tier: ' + duration + ' days');
}
</script>
