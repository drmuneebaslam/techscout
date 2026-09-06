<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">DISCOVERY ARCHITECTURE &bull; FRONT-PAGE ENGINE</span>
        <span style="font-size: 0.8rem; color: #64748b;">Configure 15 Homepage Modular Discovery Units</span>
      </div>
      <h1 style="font-size: 1.8rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        CMS Homepage Management
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
        <i data-lucide="external-link"></i> Live Homepage ↗
      </a>
      <button type="submit" form="homepageMasterForm" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
        <i data-lucide="save"></i> Save All Sections ✓
      </button>
    </div>
  </div>

  <!-- Overview Stats -->
  <div class="admin-stat-grid" style="margin-bottom: 2rem;">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Discovery Modules</div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= count($sections) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Configurable frontend blocks</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Active on Frontend</div>
      <div class="admin-stat-val" style="color: #10b981;">
        <?= count(array_filter($sections, fn($s) => (int)$s['is_enabled'] === 1)) ?>
      </div>
      <div style="font-size: 0.8rem; color: #34d399;">Currently visible to users</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Disabled Sections</div>
      <div class="admin-stat-val" style="color: #ef4444;">
        <?= count(array_filter($sections, fn($s) => (int)$s['is_enabled'] === 0)) ?>
      </div>
      <div style="font-size: 0.8rem; color: #f87171;">Hidden from discovery stream</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Layout Strategy</div>
      <div class="admin-stat-val" style="color: #a855f7; font-size: 1.1rem; padding-top: 0.35rem;">Dynamic Drag &amp; Rank</div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Custom sort order pipeline</div>
    </div>
  </div>

  <!-- Sections Accordion Master Form -->
  <form id="homepageMasterForm" action="<?= url('/admin/homepage/save') ?>" method="POST">
    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 3rem;">
      <?php foreach ($sections as $sec): ?>
        <?php 
          $key = $sec['section_key'];
          $cfg = !empty($sec['config_json']) ? (is_array($sec['config_json']) ? $sec['config_json'] : json_decode($sec['config_json'], true)) : [];
          $isEnabled = (int)$sec['is_enabled'] === 1;
        ?>
        <div class="ts-card" style="background: rgba(13,19,34,0.7); border: 1px solid <?= $isEnabled ? 'rgba(99,102,241,0.25)' : 'rgba(148,163,184,0.1)' ?>; padding: 1.25rem; border-radius: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
              <span style="font-family: var(--admin-font-mono); font-size: 0.85rem; font-weight: 700; color: #64748b; background: rgba(15,23,42,0.8); width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
                #<?= $sec['sort_order'] ?>
              </span>
              <div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <strong style="color: #f8fafc; font-size: 1.05rem;"><?= htmlspecialchars($sec['title']) ?></strong>
                  <span class="ts-badge <?= $isEnabled ? 'ts-badge-green' : 'ts-badge-cyan' ?>" style="font-size: 0.68rem;">
                    <?= strtoupper($key) ?>
                  </span>
                </div>
                <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.15rem;"><?= htmlspecialchars($sec['subtitle'] ?? '') ?></div>
              </div>
            </div>

            <!-- Controls (Toggle, Order, Item Limit) -->
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
              <div style="display: flex; align-items: center; gap: 0.4rem;">
                <label style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Order:</label>
                <input type="number" name="sections[<?= $key ?>][sort_order]" value="<?= (int)$sec['sort_order'] ?>" style="width: 60px; background: #05070b; border: 1px solid rgba(148,163,184,0.2); color: #f8fafc; padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.85rem; text-align: center;">
              </div>

              <div style="display: flex; align-items: center; gap: 0.4rem;">
                <label style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Items:</label>
                <input type="number" name="sections[<?= $key ?>][item_limit]" value="<?= (int)$sec['item_limit'] ?>" min="1" max="24" style="width: 60px; background: #05070b; border: 1px solid rgba(148,163,184,0.2); color: #f8fafc; padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.85rem; text-align: center;">
              </div>

              <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; background: rgba(15,23,42,0.8); padding: 0.35rem 0.75rem; border-radius: 8px; border: 1px solid <?= $isEnabled ? 'rgba(16,185,129,0.3)' : 'rgba(148,163,184,0.2)' ?>;">
                <input type="checkbox" name="sections[<?= $key ?>][is_enabled]" value="1" <?= $isEnabled ? 'checked' : '' ?> style="accent-color: #10b981; width: 16px; height: 16px;">
                <span style="font-size: 0.82rem; font-weight: 700; color: <?= $isEnabled ? '#34d399' : '#94a3b8' ?>;">
                  <?= $isEnabled ? 'Active' : 'Disabled' ?>
                </span>
              </label>

              <button type="button" onclick="toggleSecDetails('<?= $key ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                Configure ⚙️
              </button>
            </div>
          </div>

          <!-- Section Settings Drawer -->
          <div id="sec_detail_<?= $key ?>" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(148,163,184,0.1); background: rgba(5,7,11,0.5); padding: 1.25rem; border-radius: 8px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
              <div>
                <label class="admin-modal-label">Display Heading Title</label>
                <input type="text" name="sections[<?= $key ?>][title]" value="<?= htmlspecialchars($sec['title']) ?>" class="admin-modal-input">
              </div>
              <div>
                <label class="admin-modal-label">Subheading Description</label>
                <input type="text" name="sections[<?= $key ?>][subtitle]" value="<?= htmlspecialchars($sec['subtitle'] ?? '') ?>" class="admin-modal-input">
              </div>
              
              <?php if ($key === 'hero'): ?>
                <div>
                  <label class="admin-modal-label">Hero Badge Pill Text</label>
                  <input type="text" name="sections[<?= $key ?>][config][badge_text]" value="<?= htmlspecialchars($cfg['badge_text'] ?? 'Autonomous Intelligence &bull; Pakistani Standard Time UTC+5') ?>" class="admin-modal-input">
                </div>
                <div>
                  <label class="admin-modal-label">Primary CTA Button Label</label>
                  <input type="text" name="sections[<?= $key ?>][config][cta_label]" value="<?= htmlspecialchars($cfg['cta_label'] ?? 'Explore AI Tools &rarr;') ?>" class="admin-modal-input">
                </div>
              <?php elseif ($key === 'trending'): ?>
                <div>
                  <label class="admin-modal-label">Trending Algorithm Window</label>
                  <select name="sections[<?= $key ?>][config][timeframe]" class="admin-modal-select">
                    <option value="24h" <?= ($cfg['timeframe'] ?? '') === '24h' ? 'selected' : '' ?>>Past 24 Hours</option>
                    <option value="7d" <?= ($cfg['timeframe'] ?? '7d') === '7d' ? 'selected' : '' ?>>Past 7 Days (Default)</option>
                    <option value="30d" <?= ($cfg['timeframe'] ?? '') === '30d' ? 'selected' : '' ?>>Past 30 Days</option>
                  </select>
                </div>
              <?php elseif ($key === 'newsletter'): ?>
                <div>
                  <label class="admin-modal-label">Newsletter Incentive Pitch</label>
                  <input type="text" name="sections[<?= $key ?>][config][pitch]" value="<?= htmlspecialchars($cfg['pitch'] ?? 'Join 48,000+ engineers receiving our weekly AI architecture breakdown.') ?>" class="admin-modal-input">
                </div>
              <?php elseif ($key === 'cta'): ?>
                <div>
                  <label class="admin-modal-label">CTA Target Destination</label>
                  <input type="text" name="sections[<?= $key ?>][config][target_url]" value="<?= htmlspecialchars($cfg['target_url'] ?? '/author/apply') ?>" class="admin-modal-input">
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Sticky Bottom Save Action Bar -->
    <div style="position: sticky; bottom: 1rem; background: rgba(9,13,22,0.95); border: 1px solid var(--ts-primary); border-radius: 12px; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 40px rgba(0,0,0,0.8);">
      <div style="color: #cbd5e1; font-size: 0.9rem;">
        Changes take effect immediately on the live discovery homepage.
      </div>
      <div style="display: flex; gap: 0.75rem;">
        <a href="<?= url('/admin/dashboard') ?>" class="ts-btn ts-btn-outline ts-btn-sm">Return to Dashboard</a>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
          Save All Sections ✓
        </button>
      </div>
    </div>
  </form>
</div>

<script>
function toggleSecDetails(key) {
  const el = document.getElementById(`sec_detail_${key}`);
  if (!el) return;
  el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
