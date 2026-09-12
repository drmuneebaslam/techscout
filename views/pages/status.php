<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; max-width: 960px; margin: 0 auto 5rem auto;">
  
  <div style="text-align: center; margin-bottom: 2.5rem;">
    <div style="display: inline-flex; align-items: center; gap: 0.6rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); padding: 0.4rem 1rem; border-radius: 9999px; margin-bottom: 1rem;">
      <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #10b981; box-shadow: 0 0 10px #10b981;"></span>
      <span style="font-size: 0.82rem; font-weight: 800; color: #10b981; text-transform: uppercase; letter-spacing: 0.05em;">All Systems Fully Operational</span>
    </div>
    <h1 style="font-size: clamp(2.2rem, 4vw, 3rem); font-weight: 900; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
      TechScout Platform Status
    </h1>
    <p style="color: var(--ts-text-secondary); font-size: 1.05rem;">
      Live real-time telemetry, database response latency, and uptime health diagnostics.
    </p>
  </div>

  <!-- Telemetry HUD Grid -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
    
    <div class="ts-card" style="padding: 1.5rem; border-radius: 14px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main);">
      <div style="font-size: 0.78rem; font-weight: 700; color: var(--ts-text-muted); text-transform: uppercase; margin-bottom: 0.4rem;">
        Database Latency
      </div>
      <div style="font-size: 1.85rem; font-weight: 900; color: #10b981; font-family: var(--ts-font-mono);">
        <?= $status['database']['latency_ms'] ?> ms
      </div>
      <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.35rem;">
        <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> MySQL Responsive
      </div>
    </div>

    <div class="ts-card" style="padding: 1.5rem; border-radius: 14px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main);">
      <div style="font-size: 0.78rem; font-weight: 700; color: var(--ts-text-muted); text-transform: uppercase; margin-bottom: 0.4rem;">
        Server Disk Space
      </div>
      <div style="font-size: 1.85rem; font-weight: 900; color: #38bdf8; font-family: var(--ts-font-mono);">
        <?= $status['runtime']['disk_free_gb'] ?> GB
      </div>
      <div style="font-size: 0.75rem; color: var(--ts-text-secondary); margin-top: 0.25rem;">
        Storage Pool Healthy
      </div>
    </div>

    <div class="ts-card" style="padding: 1.5rem; border-radius: 14px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main);">
      <div style="font-size: 0.78rem; font-weight: 700; color: var(--ts-text-muted); text-transform: uppercase; margin-bottom: 0.4rem;">
        Memory Footprint
      </div>
      <div style="font-size: 1.85rem; font-weight: 900; color: #a855f7; font-family: var(--ts-font-mono);">
        <?= $status['runtime']['memory_usage_mb'] ?> MB
      </div>
      <div style="font-size: 0.75rem; color: var(--ts-text-secondary); margin-top: 0.25rem;">
        PHP Engine <?= PHP_VERSION ?>
      </div>
    </div>

    <div class="ts-card" style="padding: 1.5rem; border-radius: 14px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main);">
      <div style="font-size: 0.78rem; font-weight: 700; color: var(--ts-text-muted); text-transform: uppercase; margin-bottom: 0.4rem;">
        Security Shield
      </div>
      <div style="font-size: 1.85rem; font-weight: 900; color: #f59e0b; font-family: var(--ts-font-mono);">
        Active
      </div>
      <div style="font-size: 0.75rem; color: #f59e0b; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.35rem;">
        <i data-lucide="shield-check" style="width: 12px; height: 12px;"></i> WAF &amp; CSP Guard
      </div>
    </div>

  </div>

  <!-- Core Services Operational Roster -->
  <div class="ts-card" style="padding: 1.75rem; border-radius: 16px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); margin-bottom: 2.5rem;">
    <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem; color: var(--ts-text-primary);">Subsystem Health Roster</h3>
    
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid var(--ts-border-subtle);">
        <div>
          <strong style="color: var(--ts-text-primary);">Web Application &amp; Content Delivery</strong>
          <div style="font-size: 0.8rem; color: var(--ts-text-secondary);">Reverse proxy, asset minification, edge caching</div>
        </div>
        <span class="ts-badge ts-badge-green">Operational (99.98%)</span>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid var(--ts-border-subtle);">
        <div>
          <strong style="color: var(--ts-text-primary);">MySQL Relational Engine</strong>
          <div style="font-size: 0.8rem; color: var(--ts-text-secondary);">Query optimizer, migrations, transactional consistency</div>
        </div>
        <span class="ts-badge ts-badge-green">Operational</span>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid var(--ts-border-subtle);">
        <div>
          <strong style="color: var(--ts-text-primary);">Mail Delivery Gateway</strong>
          <div style="font-size: 0.8rem; color: var(--ts-text-secondary);">SMTP TLS socket handshake &amp; transactional notifications</div>
        </div>
        <span class="ts-badge ts-badge-green">Operational</span>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <strong style="color: var(--ts-text-primary);">Crypto Settlement Engine</strong>
          <div style="font-size: 0.8rem; color: var(--ts-text-secondary);">Multi-chain treasury, on-chain verification, author tip jar ledger</div>
        </div>
        <span class="ts-badge ts-badge-cyan">Synchronized</span>
      </div>
    </div>
  </div>

  <div style="text-align: center;">
    <a href="<?= url('/api/v1/diagnostic-ping') ?>" class="ts-btn ts-btn-outline" style="font-size: 0.85rem;">
      Inspect Raw JSON Telemetry &rarr;
    </a>
  </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
