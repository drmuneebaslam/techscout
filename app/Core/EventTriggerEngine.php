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
     * List of all supported system event triggers and their default template keys.
     */
    public static function getSupportedEvents(): array {
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
            ]
        ];
    }

    /**
     * Resolves configured template key for an event.
     * Admins can reassign event mappings via site_settings or custom event mapping.
     */
    public static function getMappedTemplate(string $eventKey): string {
        $events = self::getSupportedEvents();
        $defaultTmpl = $events[$eventKey]['default_template'] ?? 'community_broadcast';

        try {
            $settingKey = 'event_mail_map_' . $eventKey;
            $customMapped = Database::fetch("SELECT key_value FROM site_settings WHERE key_name = ?", [$settingKey]);
            if (!empty($customMapped['key_value'])) {
                return trim($customMapped['key_value']);
            }
        } catch (\Throwable $e) {}

        return $defaultTmpl;
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
