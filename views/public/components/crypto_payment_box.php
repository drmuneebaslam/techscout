<?php
// Dynamic Crypto Addresses from Site Settings
$cryptoBtc = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_btc_address'")['key_value'] ?? '';
$cryptoEth = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_eth_address'")['key_value'] ?? '';
$cryptoUsdt = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_usdt_address'")['key_value'] ?? '';
$cryptoSol = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_sol_address'")['key_value'] ?? '';
$cryptoNotes = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'crypto_payment_instructions'")['key_value'] ?? 'Transfer exact USD equivalent to any wallet address below. Confirm network type carefully before dispatching.';
?>

<div style="background: rgba(245,158,11,0.04); border: 1px solid rgba(245,158,11,0.25); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
  <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
    <span class="ts-badge ts-badge-gold" style="font-weight: 800;">OFFICIAL CRYPTO TREASURY</span>
    <span class="ts-badge ts-badge-cyan">DECENTRALIZED SETTLEMENT</span>
  </div>
  <h3 style="font-size: 1.15rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">
    🪙 Cryptocurrency Payment Details
  </h3>
  <p style="color: var(--ts-text-secondary); font-size: 0.82rem; margin: 0 0 1.25rem 0; line-height: 1.5;">
    <?= htmlspecialchars($cryptoNotes) ?>
  </p>

  <!-- Crypto Wallet Addresses List -->
  <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
    
    <!-- USDT TRC-20 / ERC-20 -->
    <?php if (!empty($cryptoUsdt)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 8px; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; font-weight: 800;">USDT (TRC-20 / ERC-20)</span>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoUsdt) ?>
          </div>
          <input type="hidden" id="addr_usdt" value="<?= htmlspecialchars($cryptoUsdt) ?>">
        </div>
        <button type="button" onclick="copyCryptoAddress('addr_usdt', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">
          <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
        </button>
      </div>
    <?php endif; ?>

    <!-- Bitcoin (BTC) -->
    <?php if (!empty($cryptoBtc)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 8px; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <span class="ts-badge ts-badge-gold" style="font-size: 0.68rem; font-weight: 800;">Bitcoin (BTC Native)</span>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoBtc) ?>
          </div>
          <input type="hidden" id="addr_btc" value="<?= htmlspecialchars($cryptoBtc) ?>">
        </div>
        <button type="button" onclick="copyCryptoAddress('addr_btc', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">
          <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
        </button>
      </div>
    <?php endif; ?>

    <!-- Ethereum (ETH / ERC-20) -->
    <?php if (!empty($cryptoEth)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 8px; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <span class="ts-badge ts-badge-indigo" style="font-size: 0.68rem; font-weight: 800;">Ethereum (ETH / ERC-20)</span>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoEth) ?>
          </div>
          <input type="hidden" id="addr_eth" value="<?= htmlspecialchars($cryptoEth) ?>">
        </div>
        <button type="button" onclick="copyCryptoAddress('addr_eth', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">
          <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
        </button>
      </div>
    <?php endif; ?>

    <!-- Solana (SOL) -->
    <?php if (!empty($cryptoSol)): ?>
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 8px; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; font-weight: 800;">Solana (SOL Native)</span>
          <div style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-primary); word-break: break-all; margin-top: 0.25rem;">
            <?= htmlspecialchars($cryptoSol) ?>
          </div>
          <input type="hidden" id="addr_sol" value="<?= htmlspecialchars($cryptoSol) ?>">
        </div>
        <button type="button" onclick="copyCryptoAddress('addr_sol', this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">
          <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
        </button>
      </div>
    <?php endif; ?>

  </div>

  <!-- TXID & Payment Verification Fields -->
  <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
    <div>
      <label class="ts-form-label">Payment Coin Used</label>
      <select name="crypto_currency" class="ts-form-input">
        <option value="USDT">USDT (Tether)</option>
        <option value="BTC">Bitcoin (BTC)</option>
        <option value="ETH">Ethereum (ETH)</option>
        <option value="SOL">Solana (SOL)</option>
        <option value="OTHER">Other / Wire</option>
      </select>
    </div>
    <div>
      <label class="ts-form-label">Transaction Hash / TXID *</label>
      <input type="text" name="crypto_txid" class="ts-form-input" placeholder="Paste on-chain transaction hash or transfer ID..." style="font-family: var(--ts-font-mono);" required>
    </div>
  </div>
</div>
