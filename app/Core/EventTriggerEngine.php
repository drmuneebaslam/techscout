<?php
namespace App\Core;

/**
 * Enterprise Custom Event Mail Trigger & Duplicate Protection Engine
 * 
 * Automatically intercepts platform events (user signup, payout processing, article publication, etc.),
 * evaluates admin-configured template mappings, enforces strict duplicate delivery prevention,
 * and dispatches transactional emails.
 */
class EventTriggerEngine {

    /**
     * List of all core system event triggers and their default template keys.
     */
    public static function getBuiltInEvents(): array {
        return [
            'user_registered' => [
                'name' => 'User Account Registration',
                'description' => 'Fired immediately when a new user registers on the platform.',
                'default_template' => 'welcome',
                'single_delivery' => true
            ],
            'email_verification_requested' => [
                'name' => 'Email Verification OTP Code',
                'description' => 'Fired when a user requests an email verification PIN.',
                'default_template' => 'email_verification',
                'single_delivery' => false
            ],
            'account_activated' => [
                'name' => 'Account Verified & Activated',
                'description' => 'Fired when a user confirms their email address.',
                'default_template' => 'account_activated',
                'single_delivery' => true
            ],
            'login_security_alert' => [
                'name' => 'New Device Login Alert',
                'description' => 'Fired when a user logs in from a new IP or browser.',
                'default_template' => 'login_alert',
                'single_delivery' => false
            ],
            'password_reset_requested' => [
                'name' => 'Password Reset Requested',
                'description' => 'Fired when a user requests a password recovery link.',
                'default_template' => 'password_reset',
                'single_delivery' => false
            ],
            'payout_requested' => [
                'name' => 'Creator Payout Requested',
                'description' => 'Fired when a creator submits a withdrawal claim.',
                'default_template' => 'payout_requested',
                'single_delivery' => false
            ],
            'payout_approved' => [
                'name' => 'Creator Payout Approved & Disbursed',
                'description' => 'Fired when treasury approves and transfers funds for a payout claim.',
                'default_template' => 'payout_approved',
                'single_delivery' => false
            ],
            'payout_rejected' => [
                'name' => 'Creator Payout Rejected / Needs Revision',
                'description' => 'Fired when a payout claim is rejected and credits are refunded.',
                'default_template' => 'payout_rejected',
                'single_delivery' => false
            ],
            'tip_received' => [
                'name' => 'Reader Patronage Tip Received',
                'description' => 'Fired when a community reader sends a tip to an author.',
                'default_template' => 'tip_received',
                'single_delivery' => false
            ],
            'article_submitted' => [
                'name' => 'Article Submitted for Peer Review',
                'description' => 'Fired when an author submits a new article draft.',
                'default_template' => 'article_submitted',
                'single_delivery' => false
            ],
            'article_approved' => [
                'name' => 'Article Published & Live',
                'description' => 'Fired when editorial approves and publishes an article.',
                'default_template' => 'article_approved',
                'single_delivery' => false
            ],
            'editorial_revisions_requested' => [
                'name' => 'Editorial Revisions Requested',
                'description' => 'Fired when editors request changes on a submission.',
                'default_template' => 'editorial_revisions',
                'single_delivery' => false
            ],
            'author_approved' => [
                'name' => 'Author Application Approved',
                'description' => 'Fired when an author application is accepted.',
                'default_template' => 'author_approved',
                'single_delivery' => true
            ],
            'ticket_reply_received' => [
                'name' => 'Support Ticket Staff Response',
                'description' => 'Fired when support staff replies to a helpdesk ticket.',
                'default_template' => 'ticket_reply',
                'single_delivery' => false
            ],
            'payout_method_linked' => [
                'name' => 'Creator Payout Method Linked',
                'description' => 'Fired when a creator links a new payout destination (Bank, PayPal, etc.).',
                'default_template' => 'payout_method_linked',
                'single_delivery' => false
            ],
            'payout_method_removed' => [
                'name' => 'Creator Payout Method Removed',
                'description' => 'Fired when a creator removes an existing payment method.',
                'default_template' => 'payout_method_removed',
                'single_delivery' => false
            ],
            'author_profile_updated' => [
                'name' => 'Creator Guild Profile Updated',
                'description' => 'Fired when an author updates their public portfolio or specialties.',
                'default_template' => 'author_profile_updated',
                'single_delivery' => false
            ],
            'campaign_submission_received' => [
                'name' => 'Hackathon Campaign Submission Received',
                'description' => 'Fired when a project entry is submitted to an AI challenge.',
                'default_template' => 'campaign_submission_received',
                'single_delivery' => false
            ],
            'account_email_verify' => [
                'name' => 'Email Address Verification',
                'description' => 'Fired when a user initiates verification with an activation link or code.',
                'default_template' => 'account_email_verify',
                'single_delivery' => false
            ],
            'magic_login_link' => [
                'name' => 'Passwordless Magic Login Link',
                'description' => 'Fired when a member requests a passwordless 1-click login link.',
                'default_template' => 'magic_login_link',
                'single_delivery' => false
            ],
            'new_device_login_alert' => [
                'name' => 'Unfamiliar Device Sign-in Alert',
                'description' => 'Fired when an account access event occurs from an unrecognized device or IP.',
                'default_template' => 'new_device_login_alert',
                'single_delivery' => false
            ],
            'security_password_changed' => [
                'name' => 'Security: Password Updated Confirmation',
                'description' => 'Fired immediately following a successful account password alteration.',
                'default_template' => 'security_password_changed',
                'single_delivery' => false
            ],
            'security_email_changed' => [
                'name' => 'Security: Primary Email Changed',
                'description' => 'Fired when a member updates their account email address.',
                'default_template' => 'security_email_changed',
                'single_delivery' => false
            ],
            'data_export_ready' => [
                'name' => 'GDPR / Privacy Data Archive Ready',
                'description' => 'Fired when requested user data export archive is ready for secure download.',
                'default_template' => 'data_export_ready',
                'single_delivery' => false
            ],
            'article_trending_alert' => [
                'name' => 'Editorial: Article Trending Milestone',
                'description' => 'Fired when an author publication achieves significant velocity or top rankings.',
                'default_template' => 'article_trending_alert',
                'single_delivery' => false
            ],
            'editorial_review_started' => [
                'name' => 'Editorial: Staff Review Started',
                'description' => 'Fired when an editor picks up an author draft for peer evaluation.',
                'default_template' => 'editorial_review_started',
                'single_delivery' => false
            ],
            'article_archived_notice' => [
                'name' => 'Editorial: Publication Archived',
                'description' => 'Fired when an article has been retired or moved to archival status.',
                'default_template' => 'article_archived_notice',
                'single_delivery' => false
            ],
            'comment_mention_alert' => [
                'name' => 'Community: @Mention in Discussion',
                'description' => 'Fired when a user or author is tagged in an insight discussion thread.',
                'default_template' => 'comment_mention_alert',
                'single_delivery' => false
            ],
            'new_follower_alert' => [
                'name' => 'Community: New Author Subscriber',
                'description' => 'Fired when a reader begins following an author or intelligence analyst.',
                'default_template' => 'new_follower_alert',
                'single_delivery' => false
            ],
            'tip_sent_confirmation' => [
                'name' => 'Patronage: Tip Receipt Confirmation',
                'description' => 'Fired to the supporter acknowledging a reader tip donation.',
                'default_template' => 'tip_sent_confirmation',
                'single_delivery' => false
            ],
            'creator_tier_promoted' => [
                'name' => 'Creator: Guild Tier Milestone Achieved',
                'description' => 'Fired when an author or contributor levels up their creator status tier.',
                'default_template' => 'creator_tier_promoted',
                'single_delivery' => false
            ],
            'payout_failed_alert' => [
                'name' => 'Creator: Payout Transfer Failed / Action Needed',
                'description' => 'Fired if bank or payment gateway processing encounters a disbursement issue.',
                'default_template' => 'payout_failed_alert',
                'single_delivery' => false
            ],
            'weekly_digest_roundup' => [
                'name' => 'Marketing: Weekly Intelligence Digest Roundup',
                'description' => 'Fired on weekly dispatch containing top articles, tools, and community spotlights.',
                'default_template' => 'weekly_digest_roundup',
                'single_delivery' => false
            ]
        ];
    }

    /**
     * List of all supported system event triggers, merging core platform events
     * with admin-created dynamic custom event keys from site_settings.
     */
    public static function getSupportedEvents(): array {
        $events = self::getBuiltInEvents();
        try {
            $customTriggers = self::getCustomTriggers();
            foreach ($customTriggers as $ct) {
                if (isset($ct['is_active']) && !$ct['is_active']) continue;
                $key = trim((string)($ct['key_or_pattern'] ?? ''));
                if ($key && ($ct['type'] ?? 'event') === 'event') {
                    $events[$key] = [
                        'name' => trim((string)($ct['name'] ?? ucwords(str_replace('_', ' ', $key)))),
                        'description' => !empty($ct['description']) ? trim($ct['description']) : 'Dynamic event automation trigger configured visually via Admin Studio.',
                        'default_template' => trim((string)($ct['template_key'] ?? 'community_broadcast')),
                        'single_delivery' => !empty($ct['single_delivery']),
                        'is_custom' => true,
                        'subject_override' => trim((string)($ct['subject_override'] ?? ''))
                    ];
                }
            }
        } catch (\Throwable $e) {}
        return $events;
    }

    /**
     * Get all admin-configured custom triggers (Event Key & URL Pattern triggers).
     */
    public static function getCustomTriggers(): array {
        try {
            $row = Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'custom_mail_triggers'");
            if (!empty($row['key_value'])) {
                $decoded = json_decode($row['key_value'], true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        } catch (\Throwable $e) {}
        return [];
    }

    /**
     * Save or Update a Custom Dynamic Trigger (Event or URL Pattern based).
     */
    public static function saveCustomTrigger(array $data): array {
        $triggers = self::getCustomTriggers();
        $id = !empty($data['id']) ? trim($data['id']) : ('trig_' . time() . '_' . substr(bin2hex(random_bytes(3)), 0, 6));

        $newTrigger = [
            'id' => $id,
            'name' => trim((string)($data['name'] ?? 'Custom Automation Trigger')),
            'type' => in_array($data['type'] ?? '', ['event', 'url_pattern'], true) ? $data['type'] : 'event',
            'key_or_pattern' => trim((string)($data['key_or_pattern'] ?? '')),
            'description' => trim((string)($data['description'] ?? '')),
            'template_key' => trim((string)($data['template_key'] ?? 'community_broadcast')),
            'subject_override' => trim((string)($data['subject_override'] ?? '')),
            'recipient_rule' => trim((string)($data['recipient_rule'] ?? 'payload')),
            'single_delivery' => !empty($data['single_delivery']),
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if (empty($newTrigger['key_or_pattern'])) {
            return ['success' => false, 'error' => 'Trigger key or URL pattern is required.'];
        }

        // Replace or Append
        $found = false;
        foreach ($triggers as $idx => $t) {
            if ($t['id'] === $id || ($t['key_or_pattern'] === $newTrigger['key_or_pattern'] && $t['type'] === $newTrigger['type'])) {
                $triggers[$idx] = array_merge($t, $newTrigger);
                $found = true;
                break;
            }
        }
        if (!$found) {
            $triggers[] = $newTrigger;
        }

        $jsonStr = json_encode($triggers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        Database::execute(
            "INSERT INTO site_settings (key_name, key_value, updated_at) VALUES ('custom_mail_triggers', ?, NOW()) 
             ON DUPLICATE KEY UPDATE key_value = ?, updated_at = NOW()",
            [$jsonStr, $jsonStr]
        );

        // Auto-synchronize event_mail_map for seamless event routing without manual mapping
        if ($newTrigger['type'] === 'event' && !empty($newTrigger['template_key']) && !empty($newTrigger['key_or_pattern'])) {
            try {
                $mapKey = 'event_mail_map_' . $newTrigger['key_or_pattern'];
                Database::execute(
                    "INSERT INTO site_settings (key_name, key_value, updated_at) VALUES (?, ?, NOW()) 
                     ON DUPLICATE KEY UPDATE key_value = ?, updated_at = NOW()",
                    [$mapKey, $newTrigger['template_key'], $newTrigger['template_key']]
                );
            } catch (\Throwable $e) {}
        }

        return ['success' => true, 'trigger' => $newTrigger];
    }

    /**
     * Delete a Custom Dynamic Trigger by ID.
     */
    public static function deleteCustomTrigger(string $id): bool {
        $triggers = self::getCustomTriggers();
        $targetTrigger = null;
        foreach ($triggers as $t) {
            if (($t['id'] ?? '') === $id) {
                $targetTrigger = $t;
                break;
            }
        }

        $filtered = array_filter($triggers, fn($t) => ($t['id'] ?? '') !== $id);
        $jsonStr = json_encode(array_values($filtered), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        Database::execute(
            "INSERT INTO site_settings (key_name, key_value, updated_at) VALUES ('custom_mail_triggers', ?, NOW()) 
             ON DUPLICATE KEY UPDATE key_value = ?, updated_at = NOW()",
            [$jsonStr, $jsonStr]
        );

        if ($targetTrigger && ($targetTrigger['type'] ?? '') === 'event' && !empty($targetTrigger['key_or_pattern'])) {
            try {
                Database::execute("DELETE FROM site_settings WHERE key_name = ?", ['event_mail_map_' . $targetTrigger['key_or_pattern']]);
            } catch (\Throwable $e) {}
        }

        return true;
    }

    /**
     * Evaluates incoming request URL against registered URL-pattern triggers and dispatches mapped emails automatically.
     */
    public static function evaluateUrlTriggers(string $requestUri, array $payload = []): int {
        $customTriggers = self::getCustomTriggers();
        $dispatchedCount = 0;

        foreach ($customTriggers as $trig) {
            if (empty($trig['is_active']) || ($trig['type'] ?? '') !== 'url_pattern' || empty($trig['key_or_pattern'])) {
                continue;
            }

            $pattern = trim($trig['key_or_pattern']);
            if (str_contains($requestUri, $pattern) || fnmatch("*{$pattern}*", $requestUri)) {
                $toEmail = trim((string)($payload['email'] ?? ($payload['to_email'] ?? ($payload['user_email'] ?? ''))));
                if (empty($toEmail)) {
                    $currUser = Auth::user();
                    $toEmail = $currUser['email'] ?? '';
                    if (empty($payload['name'])) {
                        $payload['name'] = $currUser['display_name'] ?? ($currUser['username'] ?? 'Member');
                    }
                }

                if (!empty($toEmail)) {
                    $tmplKey = $trig['template_key'] ?? 'community_broadcast';
                    $toName = $payload['name'] ?? ($payload['to_name'] ?? 'Valued User');
                    
                    if (!empty($trig['subject_override'])) {
                        $payload['subject_override'] = $trig['subject_override'];
                    }

                    if (!empty($trig['single_delivery'])) {
                        if (self::isDuplicateDelivery($toEmail, $tmplKey)) {
                            continue;
                        }
                    }

                    $sent = Mailer::sendTemplate($tmplKey, $toEmail, $toName, $payload);
                    if ($sent) $dispatchedCount++;
                }
            }
        }

        return $dispatchedCount;
    }

    /**
     * Resolves configured template key for an event.
     * Admins can reassign event mappings via site_settings or custom event mapping.
     */
    public static function getMappedTemplate(string $eventKey): string {
        try {
            $settingKey = 'event_mail_map_' . $eventKey;
            $customMapped = Database::fetch("SELECT key_value FROM site_settings WHERE key_name = ?", [$settingKey]);
            if (!empty($customMapped['key_value'])) {
                return trim($customMapped['key_value']);
            }
        } catch (\Throwable $e) {}

        // Check custom triggers configured in site_settings
        try {
            $customTriggers = self::getCustomTriggers();
            foreach ($customTriggers as $ct) {
                if (($ct['key_or_pattern'] ?? '') === $eventKey && !empty($ct['template_key'])) {
                    return trim($ct['template_key']);
                }
            }
        } catch (\Throwable $e) {}

        $events = self::getSupportedEvents();
        return $events[$eventKey]['default_template'] ?? 'community_broadcast';
    }

    /**
     * Checks whether an email has already been sent to a recipient for a specific template or event.
     * Prevents duplicate deliveries (e.g. welcome email sent twice to same user).
     */
    public static function isDuplicateDelivery(string $toEmail, string $templateKey, ?string $uniqueEntityId = null): bool {
        if (empty($toEmail) || empty($templateKey)) return false;

        try {
            $sql = "SELECT id FROM mail_logs WHERE to_email = ? AND template_key = ? AND status = 'sent'";
            $params = [trim($toEmail), trim($templateKey)];

            if (!empty($uniqueEntityId)) {
                $sql .= " AND body_html LIKE ?";
                $params[] = '%' . $uniqueEntityId . '%';
            }

            $existing = Database::fetch($sql, $params);
            return !empty($existing);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Fires an event trigger, resolves target template, checks duplicate constraints, and dispatches mail.
     */
    public static function fire(string $eventKey, array $payload, bool $forceAllowDuplicate = false): bool {
        $events = self::getSupportedEvents();
        $eventInfo = $events[$eventKey] ?? null;

        $toEmail = trim((string)($payload['email'] ?? ($payload['to_email'] ?? '')));
        $toName = trim((string)($payload['name'] ?? ($payload['to_name'] ?? '')));

        if (empty($toEmail)) return false;

        $templateKey = self::getMappedTemplate($eventKey);
        $singleDelivery = $eventInfo['single_delivery'] ?? false;
        $entityId = $payload['payout_id'] ?? ($payload['article_id'] ?? ($payload['ticket_id'] ?? null));

        if (empty($payload['subject_override']) && !empty($eventInfo['subject_override'])) {
            $payload['subject_override'] = $eventInfo['subject_override'];
        }

        // Duplicate Delivery Protection Check
        if (!$forceAllowDuplicate && $singleDelivery) {
            if (self::isDuplicateDelivery($toEmail, $templateKey)) {
                error_log("EventTriggerEngine: Skipped duplicate delivery of '{$templateKey}' to {$toEmail}");
                return true; // Marked as handled without re-sending
            }
        }

        // Dispatch via Mailer
        return Mailer::sendTemplate($templateKey, $toEmail, $toName, $payload);
    }
}
