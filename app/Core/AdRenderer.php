<?php
declare(strict_types=1);

namespace App\Core;

class AdRenderer {
    /**
     * Render an advertisement slot
     * 
     * @param string $slot header_banner, sidebar_banner, in_content, footer_banner
     * @return string HTML banner output
     */
    public static function renderSlot(string $slot): string {
        try {
            $now = date('Y-m-d H:i:s');
            // Query active ads for this slot
            $ad = Database::fetch(
                "SELECT * FROM advertisements 
                 WHERE slot_location = ? 
                   AND status = 'active' 
                   AND (start_date IS NULL OR start_date <= ?) 
                   AND (end_date IS NULL OR end_date >= ?) 
                 ORDER BY RAND() LIMIT 1",
                [$slot, $now, $now]
            );

            if (!$ad) {
                return '';
            }

            // Increment impression count asynchronously
            try {
                Database::execute(
                    "UPDATE advertisements SET impression_count = impression_count + 1 WHERE id = ?",
                    [(int)$ad['id']]
                );
            } catch (\Throwable $e) {}

            $id = (int)$ad['id'];
            $title = htmlspecialchars($ad['title'] ?? 'Sponsored Partner');
            $clickUrl = Config::url('/ad/click/' . $id);
            $imageUrl = !empty($ad['image_url']) ? image_url($ad['image_url']) : '';
            $targetDisplay = htmlspecialchars(parse_url($ad['target_url'] ?? '', PHP_URL_HOST) ?: 'partner.com');
            $badgeHtml = self::renderBadge($imageUrl, $title);

            // Header Banner layout
            if ($slot === 'header_banner') {
                return <<<HTML
<div class="ts-ad-slot-header" style="max-width: 1320px; margin: 0.75rem auto 1.25rem; padding: 0 1rem;">
  <div style="background: var(--ts-bg-surface-elevated, #0d1527); border: 1px solid var(--ts-border-main, rgba(148,163,184,0.15)); border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1.25rem; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
    <div style="display: flex; align-items: center; gap: 1rem; flex: 1; min-width: 260px;">
      {$badgeHtml}
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
          <span style="font-size: 0.65rem; font-weight: 800; background: rgba(245,158,11,0.15); color: #f59e0b; padding: 0.15rem 0.45rem; border-radius: 4px; letter-spacing: 0.05em; text-transform: uppercase;">Sponsored</span>
          <span style="font-size: 0.75rem; color: var(--ts-text-muted, #94a3b8);">{$targetDisplay}</span>
        </div>
        <a href="{$clickUrl}" target="_blank" rel="noopener sponsored" style="font-size: 0.95rem; font-weight: 700; color: var(--ts-text-primary, #f8fafc); text-decoration: none; display: block; margin-top: 0.2rem;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='var(--ts-text-primary, #f8fafc)'">
          {$title}
        </a>
      </div>
    </div>
    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <a href="{$clickUrl}" target="_blank" rel="noopener sponsored" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.82rem; padding: 0.45rem 1rem; white-space: nowrap;">
        Explore Platform ↗
      </a>
    </div>
  </div>
</div>
HTML;
            }

            // Footer Banner layout
            if ($slot === 'footer_banner') {
                return <<<HTML
<div class="ts-ad-slot-footer" style="max-width: 1320px; margin: 2rem auto; padding: 0 1rem;">
  <div style="background: var(--ts-bg-surface-elevated, #0d1527); border: 1px solid var(--ts-border-main, rgba(148,163,184,0.15)); border-radius: 12px; padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <span style="font-size: 0.68rem; font-weight: 800; background: rgba(56,189,248,0.15); color: #38bdf8; padding: 0.2rem 0.5rem; border-radius: 4px; text-transform: uppercase;">Featured Partner</span>
      <a href="{$clickUrl}" target="_blank" rel="noopener sponsored" style="font-size: 0.95rem; font-weight: 700; color: var(--ts-text-primary, #f8fafc); text-decoration: none;">
        {$title}
      </a>
    </div>
    <a href="{$clickUrl}" target="_blank" rel="noopener sponsored" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.8rem; padding: 0.35rem 0.85rem;">
      Visit {$targetDisplay} ↗
    </a>
  </div>
</div>
HTML;
            }

            // In-content / Generic Banner layout
            return <<<HTML
<div class="ts-ad-slot-inline" style="margin: 1.5rem 0; padding: 1rem; background: var(--ts-bg-surface-elevated, #0d1527); border: 1px solid var(--ts-border-main, rgba(148,163,184,0.15)); border-radius: 10px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
  <div style="display: flex; align-items: center; gap: 0.75rem;">
    <span style="font-size: 0.65rem; font-weight: 800; background: rgba(245,158,11,0.15); color: #f59e0b; padding: 0.15rem 0.45rem; border-radius: 4px; text-transform: uppercase;">Sponsored</span>
    <a href="{$clickUrl}" target="_blank" rel="noopener sponsored" style="font-size: 0.9rem; font-weight: 700; color: var(--ts-text-primary, #f8fafc); text-decoration: none;">
      {$title}
    </a>
  </div>
  <a href="{$clickUrl}" target="_blank" rel="noopener sponsored" style="font-size: 0.82rem; color: #38bdf8; text-decoration: none; font-weight: 600;">
    Learn More ↗
  </a>
</div>
HTML;
        } catch (\Throwable $e) {
            return '';
        }
    }

    private static function renderBadge(string $url, string $alt): string {
        if (!empty($url) && $url !== 'https://funwithcoding.free.nf/') {
            return '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(148,163,184,0.2); flex-shrink: 0;" onerror="this.onerror=null;this.style.display=\'none\';">';
        }
        return '<div style="width: 44px; height: 44px; border-radius: 8px; background: linear-gradient(135deg, #0284c7, #6366f1); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 1.1rem; flex-shrink: 0;">⚡</div>';
    }
}
