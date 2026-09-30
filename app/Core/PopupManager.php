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
            $now = date('Y-m-d H:i:s');
            
            // Fetch enabled popups within schedule using resilient Database helper
            $popups = Database::query("
                SELECT * FROM custom_popups 
                WHERE is_active = 1 
                  AND (start_date IS NULL OR start_date = '' OR start_date = '0000-00-00 00:00:00' OR start_date <= ?)
                  AND (end_date IS NULL OR end_date = '' OR end_date = '0000-00-00 00:00:00' OR end_date >= ?)
                ORDER BY id DESC
                LIMIT 10
            ", [$now, $now]);

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
            $badgeText = htmlspecialchars($matchingPopup['badge_text'] ?? 'FEATURED ANNOUNCEMENT', ENT_QUOTES, 'UTF-8');
            $title = htmlspecialchars($matchingPopup['title'] ?? '', ENT_QUOTES, 'UTF-8');
            $headline = htmlspecialchars($matchingPopup['headline'] ?? '', ENT_QUOTES, 'UTF-8');
            $contentHtml = $matchingPopup['content_html'] ?? '';
            $ctaText = htmlspecialchars($matchingPopup['cta_button_text'] ?? 'Explore Platform ↗', ENT_QUOTES, 'UTF-8');
            $ctaUrl = htmlspecialchars($matchingPopup['cta_button_url'] ?? '/', ENT_QUOTES, 'UTF-8');
            $secondaryText = htmlspecialchars($matchingPopup['secondary_button_text'] ?? '', ENT_QUOTES, 'UTF-8');
            $secondaryUrl = htmlspecialchars($matchingPopup['secondary_button_url'] ?? '', ENT_QUOTES, 'UTF-8');
            
            $trigger = htmlspecialchars($matchingPopup['display_trigger'] ?? 'delay', ENT_QUOTES, 'UTF-8');
            $delay = max(0, (int)($matchingPopup['trigger_delay_seconds'] ?? 3));
            $scrollPct = max(5, min(95, (int)($matchingPopup['trigger_scroll_percentage'] ?? 50)));
            $freq = htmlspecialchars($matchingPopup['frequency'] ?? 'once_per_session', ENT_QUOTES, 'UTF-8');
            $theme = htmlspecialchars($matchingPopup['popup_theme'] ?? 'modern_gradient', ENT_QUOTES, 'UTF-8');

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
                <div class="ts-popup-backdrop" onclick="window.TechScoutPopup && window.TechScoutPopup.close()"></div>
                <div class="ts-popup-card" role="dialog" aria-modal="true" aria-labelledby="tsPopupHeadline">
                    <button type="button" class="ts-popup-close-btn" onclick="window.TechScoutPopup && window.TechScoutPopup.close()" aria-label="Close dialog">
                        &times;
                    </button>

                    <div class="ts-popup-header">
                        <?php if (!empty($badgeText)): ?>
                            <span class="ts-popup-badge"><?= $badgeText ?></span>
                        <?php endif; ?>
                        <?php if (!empty($headline)): ?>
                            <h3 id="tsPopupHeadline" class="ts-popup-headline"><?= $headline ?></h3>
                        <?php endif; ?>
                        <?php if (!empty($title) && $title !== $headline): ?>
                            <div class="ts-popup-subtitle"><?= $title ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="ts-popup-body">
                        <?= $contentHtml ?>
                    </div>

                    <div class="ts-popup-actions">
                        <?php if (!empty($secondaryText)): ?>
                            <a href="<?= !empty($secondaryUrl) ? $secondaryUrl : 'javascript:void(0)' ?>" class="ts-popup-btn ts-popup-btn-secondary" onclick="window.TechScoutPopup && window.TechScoutPopup.close()">
                                <?= $secondaryText ?>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($ctaText) && !empty($ctaUrl)): ?>
                            <a href="<?= $ctaUrl ?>" class="ts-popup-btn ts-popup-btn-primary" onclick="window.TechScoutPopup && window.TechScoutPopup.trackClick(<?= $id ?>)">
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
                z-index: 9999999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.25rem;
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .ts-popup-overlay.active {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }
            .ts-popup-backdrop {
                position: absolute;
                inset: 0;
                background: rgba(10, 15, 29, 0.75);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
            }
            .ts-popup-card {
                position: relative;
                z-index: 2;
                max-width: 520px;
                width: 100%;
                border-radius: 20px;
                box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5);
                padding: 2.25rem;
                transform: scale(0.92) translateY(16px);
                transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
                box-sizing: border-box;
            }
            .ts-popup-overlay.active .ts-popup-card {
                transform: scale(1) translateY(0);
            }
            .ts-popup-close-btn {
                position: absolute;
                top: 1.25rem;
                right: 1.25rem;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.15);
                width: 34px;
                height: 34px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                color: inherit;
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
                margin-bottom: 0.75rem;
            }
            .ts-popup-headline {
                font-size: 1.4rem;
                font-weight: 800;
                margin: 0 0 0.4rem 0;
                padding-right: 2rem;
                letter-spacing: -0.02em;
                line-height: 1.3;
            }
            .ts-popup-subtitle {
                font-size: 0.88rem;
                opacity: 0.8;
                margin: 0 0 1rem 0;
                font-weight: 600;
            }
            .ts-popup-body {
                font-size: 0.95rem;
                line-height: 1.6;
                margin-bottom: 1.75rem;
                opacity: 0.95;
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
                border: none;
            }
            .ts-popup-btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 25px -4px rgba(67, 97, 238, 0.6);
            }
            .ts-popup-btn-secondary {
                background: rgba(255, 255, 255, 0.08);
                color: inherit !important;
                border: 1px solid rgba(255, 255, 255, 0.15);
            }
            .ts-popup-btn-secondary:hover {
                background: rgba(255, 255, 255, 0.15);
                transform: translateY(-1px);
            }

            /* Theme: Modern Indigo Gradient (Default) */
            .popup-theme-modern_gradient .ts-popup-card {
                background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
                border: 1px solid rgba(165, 180, 252, 0.3);
                color: #ffffff;
            }
            .popup-theme-modern_gradient .ts-popup-badge {
                background: rgba(255, 255, 255, 0.15);
                color: #ffffff;
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            /* Theme: Sleek Obsidian Glass */
            .popup-theme-dark_glass .ts-popup-card {
                background: rgba(15, 23, 42, 0.94);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(56, 189, 248, 0.3);
                color: #f8fafc;
            }
            .popup-theme-dark_glass .ts-popup-badge {
                background: rgba(56, 189, 248, 0.15);
                color: #38bdf8;
                border: 1px solid rgba(56, 189, 248, 0.35);
            }

            /* Theme: Cyber Amber Alert */
            .popup-theme-cyber_amber .ts-popup-card {
                background: #0f172a;
                border: 2px solid #f59e0b;
                color: #fef3c7;
            }
            .popup-theme-cyber_amber .ts-popup-badge {
                background: rgba(245, 158, 11, 0.2);
                color: #f59e0b;
                border: 1px solid rgba(245, 158, 11, 0.4);
            }
            .popup-theme-cyber_amber .ts-popup-btn-primary {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                color: #0f172a !important;
                font-weight: 800;
            }

            /* Theme: Crisp Minimal Card (Light / Dark Adaptive) */
            .popup-theme-minimal_card .ts-popup-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                color: #0f172a;
            }
            .popup-theme-minimal_card .ts-popup-close-btn {
                background: rgba(0, 0, 0, 0.05);
                border-color: rgba(0, 0, 0, 0.1);
                color: #64748b;
            }
            .popup-theme-minimal_card .ts-popup-badge {
                background: rgba(99, 102, 241, 0.1);
                color: #6366f1;
                border: 1px solid rgba(99, 102, 241, 0.25);
            }
            .popup-theme-minimal_card .ts-popup-btn-secondary {
                background: #f1f5f9;
                color: #334155 !important;
                border-color: #cbd5e1;
            }

            [data-theme="light"] .popup-theme-minimal_card .ts-popup-card,
            .theme-light .popup-theme-minimal_card .ts-popup-card {
                background: #ffffff;
                color: #0f172a;
                border: 1px solid #e2e8f0;
            }
            </style>

            <script>
            (function() {
                var modal = document.getElementById('tsCustomPopupModal');
                if (!modal) return;

                var popupId = modal.getAttribute('data-popup-id');
                var freq = modal.getAttribute('data-frequency') || 'once_per_session';
                var trigger = modal.getAttribute('data-trigger') || 'delay';
                var delay = parseInt(modal.getAttribute('data-delay') || '3', 10);
                var scrollPct = parseInt(modal.getAttribute('data-scroll') || '50', 10);

                var storageKey = 'ts_popup_shown_' + popupId;
                var urlParams = new URLSearchParams(window.location.search);
                var isForced = urlParams.has('test_popup') || urlParams.has('preview_popup') || urlParams.has('force_popup') || urlParams.has('popup');
                var isReset = urlParams.has('reset_popup');

                if (isReset) {
                    try {
                        localStorage.removeItem(storageKey);
                        sessionStorage.removeItem(storageKey);
                    } catch(e) {}
                }

                window.TechScoutPopup = {
                    show: function(force) {
                        modal.style.display = 'flex';
                        setTimeout(function() {
                            modal.classList.add('active');
                        }, 20);
                        if (!force && freq === 'once_ever') {
                            try { localStorage.setItem(storageKey, '1'); } catch(e) {}
                        }
                        if (!force && freq === 'once_per_session') {
                            try { sessionStorage.setItem(storageKey, '1'); } catch(e) {}
                        }
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
                        }, 320);
                    },
                    reset: function() {
                        try {
                            localStorage.removeItem(storageKey);
                            sessionStorage.removeItem(storageKey);
                            console.log('TechScout popup frequency cooldown reset for ID #' + popupId);
                        } catch(e) {}
                    },
                    test: function() {
                        this.show(true);
                    },
                    trackClick: function(id) {
                        try {
                            if (navigator.sendBeacon) {
                                navigator.sendBeacon('/api/v1/popup-click?id=' + id);
                            }
                        } catch(e) {}
                    }
                };

                // Automated trigger evaluation
                if (isForced) {
                    setTimeout(function() {
                        window.TechScoutPopup.show(true);
                    }, 400);
                    return;
                }

                // Check frequency restrictions
                if (freq === 'once_ever') {
                    try {
                        if (localStorage.getItem(storageKey)) return;
                    } catch(e) {}
                } else if (freq === 'once_per_session') {
                    try {
                        if (sessionStorage.getItem(storageKey)) return;
                    } catch(e) {}
                }

                if (trigger === 'immediate') {
                    setTimeout(function() {
                        window.TechScoutPopup.show(false);
                    }, 500);
                } else if (trigger === 'delay') {
                    setTimeout(function() {
                        window.TechScoutPopup.show(false);
                    }, Math.max(800, delay * 1000));
                } else if (trigger === 'scroll_depth') {
                    var scrollHandled = false;
                    window.addEventListener('scroll', function() {
                        if (scrollHandled) return;
                        var totalDocHeight = document.documentElement.scrollHeight - window.innerHeight;
                        if (totalDocHeight <= 0) return;
                        var currentPct = (window.scrollY / totalDocHeight) * 100;
                        if (currentPct >= scrollPct) {
                            scrollHandled = true;
                            window.TechScoutPopup.show(false);
                        }
                    }, { passive: true });
                } else if (trigger === 'exit_intent') {
                    var exitTriggered = false;
                    document.addEventListener('mouseleave', function(e) {
                        if (exitTriggered) return;
                        if (e.clientY <= 10) {
                            exitTriggered = true;
                            window.TechScoutPopup.show(false);
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
