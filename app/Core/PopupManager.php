<?php
namespace App\Core;

use App\Core\Database;
use App\Core\Session;

class PopupManager
{
    /**
     * Render the active popup modal HTML and trigger scripts if criteria are met
     */
    public static function renderActivePopup(): string
    {
        try {
            $db = Database::getConnection();
            $now = date('Y-m-d H:i:s');
            
            // Fetch enabled popups within schedule
            $stmt = $db->prepare("
                SELECT * FROM custom_popups 
                WHERE is_active = 1 
                  AND (start_date IS NULL OR start_date <= :now1)
                  AND (end_date IS NULL OR end_date >= :now2)
                ORDER BY id DESC
                LIMIT 5
            ");
            $stmt->execute([':now1' => $now, ':now2' => $now]);
            $popups = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($popups)) {
                return '';
            }

            $user = Session::get('user');
            $isLoggedIn = !empty($user);
            $isDonor = $isLoggedIn && !empty($user['is_donor']);

            $matchingPopup = null;
            foreach ($popups as $popup) {
                $target = $popup['target_audience'] ?? 'all';
                if ($target === 'all') {
                    $matchingPopup = $popup;
                    break;
                }
                if ($target === 'guests_only' && !$isLoggedIn) {
                    $matchingPopup = $popup;
                    break;
                }
                if ($target === 'logged_in_only' && $isLoggedIn) {
                    $matchingPopup = $popup;
                    break;
                }
                if ($target === 'donors_only' && $isDonor) {
                    $matchingPopup = $popup;
                    break;
                }
            }

            if (!$matchingPopup) {
                return '';
            }

            $id = (int)$matchingPopup['id'];
            $badgeText = htmlspecialchars($matchingPopup['badge_text'] ?? 'ANNOUNCEMENT', ENT_QUOTES, 'UTF-8');
            $title = htmlspecialchars($matchingPopup['title'] ?? '', ENT_QUOTES, 'UTF-8');
            $headline = htmlspecialchars($matchingPopup['headline'] ?? '', ENT_QUOTES, 'UTF-8');
            $contentHtml = $matchingPopup['content_html'] ?? '';
            $ctaText = htmlspecialchars($matchingPopup['cta_button_text'] ?? '', ENT_QUOTES, 'UTF-8');
            $ctaUrl = htmlspecialchars($matchingPopup['cta_button_url'] ?? '', ENT_QUOTES, 'UTF-8');
            $secondaryText = htmlspecialchars($matchingPopup['secondary_button_text'] ?? '', ENT_QUOTES, 'UTF-8');
            $secondaryUrl = htmlspecialchars($matchingPopup['secondary_button_url'] ?? '', ENT_QUOTES, 'UTF-8');
            
            $trigger = htmlspecialchars($matchingPopup['display_trigger'] ?? 'delay', ENT_QUOTES, 'UTF-8');
            $delay = (int)($matchingPopup['trigger_delay_seconds'] ?? 3);
            $scrollPct = (int)($matchingPopup['trigger_scroll_percentage'] ?? 50);
            $freq = htmlspecialchars($matchingPopup['frequency'] ?? 'once_per_session', ENT_QUOTES, 'UTF-8');
            $theme = htmlspecialchars($matchingPopup['popup_theme'] ?? 'modern_gradient', ENT_QUOTES, 'UTF-8');

            // Theme class mapping
            $themeClass = 'popup-theme-' . preg_replace('/[^a-z0-9_-]/', '', $theme);

            ob_start();
            ?>
            <!-- TechScout Dynamic Popup Engine -->
            <div id="tsCustomPopupModal" class="ts-popup-overlay <?= $themeClass ?>" 
                 data-popup-id="<?= $id ?>"
                 data-trigger="<?= $trigger ?>"
                 data-delay="<?= $delay ?>"
                 data-scroll="<?= $scrollPct ?>"
                 data-frequency="<?= $freq ?>"
                 style="display: none;">
                <div class="ts-popup-backdrop" onclick="window.TechScoutPopup.close()"></div>
                <div class="ts-popup-card" role="dialog" aria-modal="true" aria-labelledby="tsPopupTitle">
                    <button type="button" class="ts-popup-close-btn" onclick="window.TechScoutPopup.close()" aria-label="Close dialog">
                        &times;
                    </button>

                    <div class="ts-popup-header">
                        <?php if (!empty($badgeText)): ?>
                            <span class="ts-popup-badge"><?= $badgeText ?></span>
                        <?php endif; ?>
                        <?php if (!empty($title)): ?>
                            <h3 id="tsPopupTitle" class="ts-popup-title"><?= $title ?></h3>
                        <?php endif; ?>
                        <?php if (!empty($headline)): ?>
                            <p class="ts-popup-headline"><?= $headline ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="ts-popup-body">
                        <?= $contentHtml ?>
                    </div>

                    <div class="ts-popup-actions">
                        <?php if (!empty($secondaryText) && !empty($secondaryUrl)): ?>
                            <a href="<?= $secondaryUrl ?>" class="ts-popup-btn ts-popup-btn-secondary" onclick="window.TechScoutPopup.close()">
                                <?= $secondaryText ?>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($ctaText) && !empty($ctaUrl)): ?>
                            <a href="<?= $ctaUrl ?>" class="ts-popup-btn ts-popup-btn-primary" onclick="window.TechScoutPopup.trackClick(<?= $id ?>)">
                                <span><?= $ctaText ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <style>
            .ts-popup-overlay {
                position: fixed;
                inset: 0;
                width: 100vw;
                height: 100vh;
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.25rem;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .ts-popup-overlay.active {
                opacity: 1;
                visibility: visible;
            }
            .ts-popup-backdrop {
                position: absolute;
                inset: 0;
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
            }
            .ts-popup-card {
                position: relative;
                z-index: 2;
                max-width: 520px;
                width: 100%;
                background: var(--bg-card, #ffffff);
                border: 1px solid var(--border, rgba(0,0,0,0.12));
                border-radius: 20px;
                box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3);
                padding: 2.25rem;
                transform: scale(0.92) translateY(12px);
                transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
                color: var(--text-main, #1e293b);
                box-sizing: border-box;
            }
            .ts-popup-overlay.active .ts-popup-card {
                transform: scale(1) translateY(0);
            }
            .ts-popup-close-btn {
                position: absolute;
                top: 1.25rem;
                right: 1.25rem;
                background: var(--bg-subtle, rgba(0,0,0,0.06));
                border: 1px solid var(--border, rgba(0,0,0,0.08));
                width: 34px;
                height: 34px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                color: var(--text-muted, #64748b);
                font-size: 1.4rem;
                line-height: 1;
                transition: all 0.2s ease;
            }
            .ts-popup-close-btn:hover {
                background: #ef4444;
                color: #ffffff;
                border-color: #ef4444;
                transform: rotate(90deg);
            }
            .ts-popup-badge {
                display: inline-block;
                padding: 0.25rem 0.75rem;
                font-size: 0.72rem;
                font-weight: 800;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                border-radius: 9999px;
                background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(168,85,247,0.15));
                color: #6366f1;
                border: 1px solid rgba(99,102,241,0.3);
                margin-bottom: 0.75rem;
            }
            .ts-popup-title {
                font-size: 1.45rem;
                font-weight: 800;
                margin: 0 0 0.4rem 0;
                padding-right: 2rem;
                letter-spacing: -0.02em;
                line-height: 1.25;
            }
            .ts-popup-headline {
                font-size: 0.96rem;
                color: var(--text-muted, #64748b);
                margin: 0 0 1.25rem 0;
                line-height: 1.5;
                font-weight: 500;
            }
            .ts-popup-body {
                font-size: 0.95rem;
                line-height: 1.6;
                color: var(--text-secondary, #334155);
                margin-bottom: 1.75rem;
            }
            .ts-popup-body img {
                max-width: 100%;
                height: auto;
                border-radius: 12px;
                margin: 0.75rem 0;
            }
            .ts-popup-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 0.75rem;
                flex-wrap: wrap;
            }
            .ts-popup-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.75rem 1.6rem;
                border-radius: 12px;
                font-size: 0.92rem;
                font-weight: 700;
                text-decoration: none;
                cursor: pointer;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .ts-popup-btn-primary {
                background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
                color: #ffffff !important;
                box-shadow: 0 8px 20px -4px rgba(67, 97, 238, 0.45);
            }
            .ts-popup-btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 25px -4px rgba(67, 97, 238, 0.6);
            }
            .ts-popup-btn-secondary {
                background: var(--bg-subtle, rgba(0,0,0,0.05));
                color: var(--text-secondary, #475569) !important;
                border: 1px solid var(--border, rgba(0,0,0,0.1));
            }
            .ts-popup-btn-secondary:hover {
                background: var(--bg-hover, rgba(0,0,0,0.08));
                transform: translateY(-1px);
            }
            /* Theme variations */
            .popup-theme-dark_glass .ts-popup-card {
                background: rgba(15, 23, 42, 0.94);
                backdrop-filter: blur(16px);
                border-color: rgba(255, 255, 255, 0.12);
                color: #f8fafc;
            }
            .popup-theme-cyber_amber .ts-popup-card {
                border-top: 4px solid #f59e0b;
            }
            .popup-theme-minimal_card .ts-popup-card {
                border-radius: 12px;
                box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            }
            </style>

            <script>
            (function() {
                var modal = document.getElementById('tsCustomPopupModal');
                if (!modal) return;

                var popupId = modal.getAttribute('data-popup-id');
                var freq = modal.getAttribute('data-frequency');
                var trigger = modal.getAttribute('data-trigger');
                var delay = parseInt(modal.getAttribute('data-delay') || '3', 10);
                var scrollPct = parseInt(modal.getAttribute('data-scroll') || '50', 10);

                var storageKey = 'ts_popup_shown_' + popupId;

                // Frequency checks
                if (freq === 'once_ever' && localStorage.getItem(storageKey)) {
                    return;
                }
                if (freq === 'once_per_session' && sessionStorage.getItem(storageKey)) {
                    return;
                }

                window.TechScoutPopup = {
                    show: function() {
                        modal.style.display = 'flex';
                        setTimeout(function() {
                            modal.classList.add('active');
                        }, 20);
                        if (freq === 'once_ever') localStorage.setItem(storageKey, '1');
                        if (freq === 'once_per_session') sessionStorage.setItem(storageKey, '1');
                        try {
                            if (navigator.sendBeacon) {
                                navigator.sendBeacon('/api/v1/popup-impression?id=' + popupId);
                            }
                        } catch(e) {}
                    },
                    close: function() {
                        modal.classList.remove('active');
                        setTimeout(function() {
                            modal.style.display = 'none';
                        }, 300);
                    },
                    trackClick: function(id) {
                        try {
                            if (navigator.sendBeacon) {
                                navigator.sendBeacon('/api/v1/popup-click?id=' + id);
                            }
                        } catch(e) {}
                    }
                };

                if (trigger === 'immediate') {
                    window.TechScoutPopup.show();
                } else if (trigger === 'delay') {
                    setTimeout(function() {
                        window.TechScoutPopup.show();
                    }, Math.max(500, delay * 1000));
                } else if (trigger === 'scroll_depth') {
                    var scrollHandled = false;
                    window.addEventListener('scroll', function() {
                        if (scrollHandled) return;
                        var totalDocHeight = document.documentElement.scrollHeight - window.innerHeight;
                        if (totalDocHeight <= 0) return;
                        var currentPct = (window.scrollY / totalDocHeight) * 100;
                        if (currentPct >= scrollPct) {
                            scrollHandled = true;
                            window.TechScoutPopup.show();
                        }
                    }, { passive: true });
                } else if (trigger === 'exit_intent') {
                    var exitTriggered = false;
                    document.addEventListener('mouseleave', function(e) {
                        if (exitTriggered) return;
                        if (e.clientY <= 10) {
                            exitTriggered = true;
                            window.TechScoutPopup.show();
                        }
                    });
                }
            })();
            </script>
            <?php
            return ob_get_clean();
        } catch (\Throwable $e) {
            error_log('PopupManager error: ' . $e->getMessage());
            return '';
        }
    }
}
