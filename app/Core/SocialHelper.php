<?php
namespace App\Core;

class SocialHelper {
    public static function getAvailablePlatforms(): array {
        return [
            'github' => [
                'label' => 'GitHub',
                'icon' => 'github',
                'color' => '#f8fafc',
                'placeholder' => 'https://github.com/username'
            ],
            'twitter' => [
                'label' => 'X (Twitter)',
                'icon' => 'twitter',
                'color' => '#38bdf8',
                'placeholder' => 'https://x.com/username'
            ],
            'linkedin' => [
                'label' => 'LinkedIn',
                'icon' => 'linkedin',
                'color' => '#0a66c2',
                'placeholder' => 'https://linkedin.com/in/username'
            ],
            'discord' => [
                'label' => 'Discord',
                'icon' => 'discord',
                'color' => '#5865f2',
                'placeholder' => 'https://discord.gg/... or @handle'
            ],
            'telegram' => [
                'label' => 'Telegram',
                'icon' => 'telegram',
                'color' => '#229ed9',
                'placeholder' => 'https://t.me/username'
            ],
            'website' => [
                'label' => 'Portfolio / Website',
                'icon' => 'globe',
                'color' => '#10b981',
                'placeholder' => 'https://myportfolio.dev'
            ],
            'youtube' => [
                'label' => 'YouTube',
                'icon' => 'youtube',
                'color' => '#ef4444',
                'placeholder' => 'https://youtube.com/@channel'
            ],
            'twitch' => [
                'label' => 'Twitch',
                'icon' => 'twitch',
                'color' => '#a855f7',
                'placeholder' => 'https://twitch.tv/username'
            ],
            'substack' => [
                'label' => 'Substack / Newsletter',
                'icon' => 'mail',
                'color' => '#ff6719',
                'placeholder' => 'https://username.substack.com'
            ],
            'medium' => [
                'label' => 'Medium',
                'icon' => 'book-open',
                'color' => '#f8fafc',
                'placeholder' => 'https://medium.com/@username'
            ],
            'hashnode' => [
                'label' => 'Hashnode / Dev.to',
                'icon' => 'code-2',
                'color' => '#2962ff',
                'placeholder' => 'https://hashnode.com/@username'
            ],
            'huggingface' => [
                'label' => 'Hugging Face',
                'icon' => 'smile',
                'color' => '#ffd21e',
                'placeholder' => 'https://huggingface.co/username'
            ],
            'threads' => [
                'label' => 'Threads',
                'icon' => 'at-sign',
                'color' => '#f8fafc',
                'placeholder' => 'https://threads.net/@username'
            ],
            'mastodon' => [
                'label' => 'Mastodon',
                'icon' => 'at-sign',
                'color' => '#6364ff',
                'placeholder' => 'https://mastodon.social/@username'
            ],
            'reddit' => [
                'label' => 'Reddit',
                'icon' => 'reddit',
                'color' => '#ff4500',
                'placeholder' => 'https://reddit.com/u/username'
            ],
            'instagram' => [
                'label' => 'Instagram',
                'icon' => 'instagram',
                'color' => '#ec4899',
                'placeholder' => 'https://instagram.com/username'
            ],
            'kaggle' => [
                'label' => 'Kaggle',
                'icon' => 'database',
                'color' => '#20beff',
                'placeholder' => 'https://kaggle.com/username'
            ],
            'npm' => [
                'label' => 'NPM / Package',
                'icon' => 'package',
                'color' => '#cb3837',
                'placeholder' => 'https://npmjs.com/~username'
            ],
            'custom' => [
                'label' => 'Custom / Other Link',
                'icon' => 'link',
                'color' => '#38bdf8',
                'placeholder' => 'https://example.com'
            ]
        ];
    }

    /**
     * Normalize raw social links from string or array into a uniform list of items
     */
    public static function normalizeLinks(mixed $raw): array {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($raw)) {
            return [];
        }

        $platforms = self::getAvailablePlatforms();
        $normalized = [];

        // Check if array has a list of items [{platform, url, label}] or is associative ['github' => '...']
        if (isset($raw['items']) && is_array($raw['items'])) {
            $rawList = $raw['items'];
        } elseif (array_is_list($raw)) {
            $rawList = $raw;
        } else {
            // Legacy associative map
            $rawList = [];
            foreach ($raw as $key => $val) {
                if (is_string($val) && !empty(trim($val)) && !in_array($key, ['tax_id_or_ssn', 'tax_country'])) {
                    $rawList[] = [
                        'platform' => $key,
                        'url' => trim($val),
                        'label' => ''
                    ];
                }
            }
        }

        foreach ($rawList as $item) {
            if (!is_array($item)) continue;
            $platformKey = strtolower(trim((string)($item['platform'] ?? 'custom')));
            $url = trim((string)($item['url'] ?? ''));
            if (empty($url)) continue;

            $meta = $platforms[$platformKey] ?? $platforms['custom'];
            $label = trim((string)($item['label'] ?? ''));
            if (empty($label)) {
                $label = $meta['label'];
            }

            // Build proper target URL if handle given
            $targetUrl = $url;
            if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://') && !str_starts_with($url, 'mailto:')) {
                if ($platformKey === 'github') $targetUrl = 'https://github.com/' . ltrim($url, '@');
                elseif ($platformKey === 'twitter') $targetUrl = 'https://x.com/' . ltrim($url, '@');
                elseif ($platformKey === 'linkedin') $targetUrl = 'https://linkedin.com/in/' . ltrim($url, '@');
                elseif ($platformKey === 'telegram') $targetUrl = 'https://t.me/' . ltrim($url, '@');
                elseif ($platformKey === 'reddit') $targetUrl = 'https://reddit.com/u/' . ltrim($url, '@');
                elseif ($platformKey === 'instagram') $targetUrl = 'https://instagram.com/' . ltrim($url, '@');
                elseif ($platformKey === 'medium') $targetUrl = 'https://medium.com/@' . ltrim($url, '@');
                elseif ($platformKey === 'huggingface') $targetUrl = 'https://huggingface.co/' . ltrim($url, '@');
                elseif ($platformKey === 'threads') $targetUrl = 'https://threads.net/@' . ltrim($url, '@');
                else $targetUrl = 'https://' . $url;
            }

            $normalized[] = [
                'platform' => $platformKey,
                'url' => $targetUrl,
                'raw_url' => $url,
                'label' => $label,
                'name' => $meta['label'],
                'icon' => $meta['icon'],
                'color' => $meta['color']
            ];
        }

        return $normalized;
    }

    /**
     * Get authentic high-resolution favicon URL for any site or URL
     */
    public static function getFaviconUrl(string $urlOrDomain): string {
        $domain = trim($urlOrDomain);
        if (str_starts_with($domain, 'http://') || str_starts_with($domain, 'https://')) {
            $parsed = parse_url($domain, PHP_URL_HOST);
            if (!empty($parsed)) $domain = $parsed;
        }
        $domain = preg_replace('/^www\./i', '', $domain);
        if (empty($domain)) $domain = 'techscout.n';
        return "https://www.google.com/s2/favicons?domain=" . urlencode($domain) . "&sz=64";
    }

    /**
     * Render HTML badge pills for author profile
     */
    public static function renderBadges(mixed $raw): string {
        $links = self::normalizeLinks($raw);
        if (empty($links)) return '';

        $html = '<div class="ts-author-socials-bar" style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">';
        foreach ($links as $link) {
            $iconHtml = SvgHelper::render($link['icon'], 14);
            $html .= sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" class="ts-badge ts-author-social-pill" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; border: 1px solid rgba(148,163,184,0.2); background: rgba(15,23,42,0.6); color: #cbd5e1; transition: all 0.2s ease;" onmouseover="this.style.borderColor=\'%s\'; this.style.color=\'%s\'; this.style.transform=\'translateY(-1px)\';" onmouseout="this.style.borderColor=\'rgba(148,163,184,0.2)\'; this.style.color=\'#cbd5e1\'; this.style.transform=\'none\';" title="%s">%s <span>%s</span></a>',
                htmlspecialchars($link['url']),
                htmlspecialchars($link['color']),
                htmlspecialchars($link['color']),
                htmlspecialchars($link['name'] . ': ' . $link['url']),
                $iconHtml,
                htmlspecialchars($link['label'])
            );
        }
        $html .= '</div>';
        return $html;
    }
}
