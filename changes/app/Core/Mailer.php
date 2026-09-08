<?php
namespace App\Core;

class Mailer {
    private static ?array $settings = null;

    public static function getSettings(): array {
        if (self::$settings === null) {
            self::$settings = [];
            try {
                $rows = Database::query("SELECT key_name, key_value FROM site_settings WHERE key_name LIKE 'mail_%'");
                foreach ($rows as $r) {
                    self::$settings[$r['key_name']] = $r['key_value'];
                }
            } catch (\Throwable $e) {}
        }
        return self::$settings;
    }

    public static function getTemplates(): array {
        $builtIn = [
            'welcome' => [
                'name' => '🚀 Welcome to TechScout Network',
                'subject' => 'Welcome to TechScout - AI & Software Engineering Intelligence',
                'description' => 'Sent to new users upon account registration and onboarding.',
                'vars' => ['name', 'username', 'action_url', 'login_url'],
                'is_custom' => false
            ],
            'email_verification' => [
                'name' => '📧 Sign Up Email Verification & OTP PIN',
                'subject' => 'Verify Your TechScout Account: {{verification_code}}',
                'description' => 'Sent to new users during sign up with verification code/link.',
                'vars' => ['name', 'verification_code', 'action_url', 'email', 'expires_in'],
                'is_custom' => false
            ],
            'account_activated' => [
                'name' => '✅ Account Verified & Activated',
                'subject' => 'Your TechScout Account is Ready & Verified',
                'description' => 'Sent upon successful email confirmation and account activation.',
                'vars' => ['name', 'username', 'action_url', 'login_url'],
                'is_custom' => false
            ],
            'login_alert' => [
                'name' => '🔐 Login & Security Device Alert',
                'subject' => 'Security Alert: New Sign-In to Your TechScout Account',
                'description' => 'Sent upon successful login to verify authorized access.',
                'vars' => ['name', 'ip_address', 'browser', 'timestamp', 'action_url'],
                'is_custom' => false
            ],
            'password_reset' => [
                'name' => '🔒 Security: Password Reset & Access PIN',
                'subject' => 'Security Notice: Reset Your TechScout Account Password',
                'description' => 'Sent when a password reset is requested.',
                'vars' => ['name', 'reset_code', 'action_url', 'ip_address', 'browser'],
                'is_custom' => false
            ],
            'article_approved' => [
                'name' => '🎉 Publication Approved & Live',
                'subject' => 'Your Article is Now Live on TechScout!',
                'description' => 'Sent to authors when their technical article is approved by the editorial team.',
                'vars' => ['name', 'article_title', 'action_url'],
                'is_custom' => false
            ],
            'editorial_revisions' => [
                'name' => '📋 Editorial Revision Request',
                'subject' => 'Action Required: Editorial Revisions for Your Submission',
                'description' => 'Sent when editorial staff requests adjustments from an author.',
                'vars' => ['name', 'article_title', 'editorial_notes', 'action_url'],
                'is_custom' => false
            ],
            'author_approved' => [
                'name' => '🌟 Creator / Author Application Approved',
                'subject' => 'Congratulations! You are now a Verified TechScout Author',
                'description' => 'Sent when an author application is accepted.',
                'vars' => ['name', 'badge_title', 'action_url'],
                'is_custom' => false
            ],
            'ticket_reply' => [
                'name' => '🎫 Support Ticket Staff Response',
                'subject' => 'New Staff Response on Support Ticket #{{ticket_number}}',
                'description' => 'Sent when staff replies to a user support ticket.',
                'vars' => ['name', 'ticket_number', 'ticket_subject', 'reply_message', 'action_url'],
                'is_custom' => false
            ],
            'contact_reply' => [
                'name' => '💬 Official Reply to Your Contact Inquiry',
                'subject' => 'TechScout Team Response to Your Inquiry',
                'description' => 'Sent when operations team replies to a contact form message.',
                'vars' => ['name', 'inquiry_subject', 'reply_message', 'action_url'],
                'is_custom' => false
            ],
            'community_broadcast' => [
                'name' => '📢 Platform Announcement & Intelligence Digest',
                'subject' => 'TechScout Intelligence Digest & Platform Updates',
                'description' => 'Sent to broadcast newsletters and platform-wide announcements.',
                'vars' => ['name', 'headline', 'announcement_body', 'action_url'],
                'is_custom' => false
            ],
            'security_alert' => [
                'name' => '🛡️ Security Alert: Password Updated',
                'subject' => 'Security Notice: Account Password Changed',
                'description' => 'Sent when an account password is changed or updated by admin.',
                'vars' => ['name', 'change_time', 'action_url'],
                'is_custom' => false
            ],
            'payout_requested' => [
                'name' => '💸 Creator Payout Withdrawal Requested',
                'subject' => 'Withdrawal Request Received: ${{amount}} USD (#{{payout_id}})',
                'description' => 'Sent to creators immediately when they submit a rewards withdrawal claim.',
                'vars' => ['name', 'amount', 'net_amount', 'fee_deducted', 'payout_id', 'ref_no', 'method_type', 'destination', 'action_url'],
                'is_custom' => false
            ],
            'payout_approved' => [
                'name' => '💰 Creator Payout Processed & Transferred',
                'subject' => 'Payment Sent! ${{amount}} USD has been transferred to your account 🎉',
                'description' => 'Sent to creators when their payout request is approved and funds are disbursed.',
                'vars' => ['name', 'amount', 'net_amount', 'fee_deducted', 'payout_id', 'transaction_ref', 'receipt_url', 'action_url'],
                'is_custom' => false
            ],
            'payout_rejected' => [
                'name' => '⚠️ Creator Payout Update / Action Required',
                'subject' => 'Important Update: Regarding Your Payout Request #{{payout_id}}',
                'description' => 'Sent to creators when a payout request requires revision or is rejected (credits 100% refunded).',
                'vars' => ['name', 'amount', 'payout_id', 'admin_notes', 'action_url'],
                'is_custom' => false
            ],
            'tip_received' => [
                'name' => '🎁 Reader Tip & Patronage Notification',
                'subject' => 'You received a ${{amount}} USD tip from {{tipper_name}}! ☕',
                'description' => 'Sent to creators when a community member or subscriber awards a direct tip.',
                'vars' => ['name', 'tipper_name', 'amount', 'net_amount', 'article_title', 'message', 'action_url'],
                'is_custom' => false
            ],
            'author_application_received' => [
                'name' => '📝 Creator Guild Application Received',
                'subject' => 'We Received Your Author Application! 🌟',
                'description' => 'Sent to applicant confirming their author submission was received by the editorial board.',
                'vars' => ['name', 'username', 'action_url'],
                'is_custom' => false
            ],
            'article_submitted' => [
                'name' => '📤 Article Submission in Editorial Queue',
                'subject' => 'Article Received: {{article_title}} is Under Review',
                'description' => 'Sent to authors when they submit a new technical article for peer review.',
                'vars' => ['name', 'article_title', 'action_url'],
                'is_custom' => false
            ],
            'article_rejected' => [
                'name' => '❌ Article Submission Editorial Notice',
                'subject' => 'Update on Your Submission: {{article_title}}',
                'description' => 'Sent to authors when a submission is not accepted, with editorial guidance and notes.',
                'vars' => ['name', 'article_title', 'editorial_notes', 'action_url'],
                'is_custom' => false
            ],
            'comment_reply_alert' => [
                'name' => '💬 Discussion: New Reply to Your Post',
                'subject' => '{{commenter_name}} replied to your comment on {{article_title}}',
                'description' => 'Sent when another community member replies to a comment or technical inquiry.',
                'vars' => ['name', 'commenter_name', 'article_title', 'comment_snippet', 'action_url'],
                'is_custom' => false
            ],
            'bookmark_collection_shared' => [
                'name' => '📑 Technical Blueprint Collection Shared',
                'subject' => 'Check Out this Curated Technical Stack: {{collection_title}}',
                'description' => 'Sent when a user or colleague shares a bookmark stack or prompt blueprint.',
                'vars' => ['name', 'collection_title', 'sender_name', 'action_url'],
                'is_custom' => false
            ],
            'monthly_creator_earnings_statement' => [
                'name' => '📊 Creator Monthly Intelligence & Earnings Statement',
                'subject' => 'Your {{month}} Creator Statement: ${{amount}} USD Earned',
                'description' => 'Monthly performance digest summarizing views, reader tips, and accrued rewards.',
                'vars' => ['name', 'month', 'amount', 'total_reads', 'tips_count', 'action_url'],
                'is_custom' => false
            ],
            'hackathon_campaign_invite' => [
                'name' => '🏆 AI Hackathon & Scout Campaign Invitation',
                'subject' => 'Exclusive Invite: Compete in the {{campaign_title}} (Prize Pool: ${{prize_pool}})',
                'description' => 'Sent to creators and developers inviting them to live hackathon challenges.',
                'vars' => ['name', 'campaign_title', 'prize_pool', 'deadline', 'action_url'],
                'is_custom' => false
            ],
            'security_two_factor_otp' => [
                'name' => '🔑 Two-Factor Authentication (2FA) Security PIN',
                'subject' => 'Your TechScout Security PIN: {{two_factor_otp}}',
                'description' => 'Sent for two-factor login challenges and sensitive settings authorization.',
                'vars' => ['name', 'two_factor_otp', 'expires_in', 'ip_address'],
                'is_custom' => false
            ],
            'user_account_suspended' => [
                'name' => '⚠️ Account Governance Suspension Notice',
                'subject' => 'Important: Your TechScout Account Has Been Suspended',
                'description' => 'Sent when governance team suspends an account due to policy strikes.',
                'vars' => ['name', 'suspension_reason', 'appeal_url'],
                'is_custom' => false
            ],
            'user_account_reactivated' => [
                'name' => '🟢 Account Reinstated & Operational',
                'subject' => 'Welcome Back: Your TechScout Account is Restored',
                'description' => 'Sent when an account suspension or review hold is lifted.',
                'vars' => ['name', 'action_url'],
                'is_custom' => false
            ],
            'author_milestone_badge' => [
                'name' => '🎖️ Creator Milestone & Guild Honors Awarded',
                'subject' => 'Congratulations! You unlocked the {{milestone_title}} Milestone 🌟',
                'description' => 'Sent when an author achieves readership milestones or community awards.',
                'vars' => ['name', 'milestone_title', 'action_url'],
                'is_custom' => false
            ],
            'newsletter_breaking_ai_radar' => [
                'name' => '🚨 Flash AI Radar: Breaking Model & Tech Release',
                'subject' => '⚡ Breaking Radar: {{headline}}',
                'description' => 'Flash alert sent for emergency security vulnerabilities or major frontier AI releases.',
                'vars' => ['name', 'headline', 'message', 'action_url'],
                'is_custom' => false
            ]
        ];

        // Merge database templates and custom assigned credentials
        try {
            $customs = Database::query("SELECT mt.*, mc.name as credential_name, mc.provider as credential_provider FROM mail_templates mt LEFT JOIN mail_credentials mc ON mt.assigned_credential_id = mc.id ORDER BY mt.id DESC");
            foreach ($customs as $ct) {
                $key = $ct['template_key'];
                if (isset($builtIn[$key])) {
                    $builtIn[$key]['assigned_credential_id'] = !empty($ct['assigned_credential_id']) ? (int)$ct['assigned_credential_id'] : null;
                    $builtIn[$key]['credential_name'] = $ct['credential_name'] ?? null;
                    if (!empty($ct['subject'])) $builtIn[$key]['subject'] = $ct['subject'];
                    if (!empty($ct['html_content'])) $builtIn[$key]['html_content'] = $ct['html_content'];
                    if (!empty($ct['description'])) $builtIn[$key]['description'] = $ct['description'];
                } else {
                    $builtIn[$key] = [
                        'id' => (int)$ct['id'],
                        'name' => $ct['name'],
                        'subject' => $ct['subject'],
                        'description' => $ct['description'] ?? 'Custom Admin Created HTML Email Template',
                        'html_content' => $ct['html_content'],
                        'assigned_credential_id' => !empty($ct['assigned_credential_id']) ? (int)$ct['assigned_credential_id'] : null,
                        'credential_name' => $ct['credential_name'] ?? null,
                        'vars' => ['name', 'username', 'email', 'action_url', 'headline', 'message', 'site_name', 'year'],
                        'is_custom' => true
                    ];
                }
            }
        } catch (\Throwable $e) {}

        return $builtIn;
    }

    public static function getTemplateLibrary(): array {
        return self::getTemplates();
    }

    public static function assignTemplateCredential(string $templateKey, ?int $credentialId): bool {
        $key = preg_replace('/[^a-z0-9_-]/i', '_', strtolower(trim($templateKey)));
        if (empty($key)) return false;

        $templates = self::getTemplates();
        $tmpl = $templates[$key] ?? null;
        $name = $tmpl['name'] ?? ucwords(str_replace('_', ' ', $key));
        $subject = $tmpl['subject'] ?? 'TechScout Platform Notification';
        $desc = $tmpl['description'] ?? null;
        $html = $tmpl['html_content'] ?? self::getDefaultTemplateBody($key);

        try {
            return Database::execute(
                "INSERT INTO mail_templates (template_key, name, subject, description, html_content, assigned_credential_id, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW()) 
                 ON DUPLICATE KEY UPDATE assigned_credential_id = VALUES(assigned_credential_id), updated_at = NOW()",
                [$key, $name, $subject, $desc, $html, $credentialId ?: null]
            );
        } catch (\Throwable $e) {
            error_log("Failed to assign template credential: " . $e->getMessage());
            return false;
        }
    }

    public static function saveCustomTemplate(string $key, string $name, string $subject, ?string $description, string $htmlContent, ?int $assignedCredentialId = null): bool {
        $key = preg_replace('/[^a-z0-9_-]/i', '_', strtolower(trim($key)));
        if (empty($key) || empty($name) || empty($htmlContent)) return false;
        try {
            Database::execute("INSERT INTO mail_templates (template_key, name, subject, description, html_content, assigned_credential_id, updated_at) 
                               VALUES (?, ?, ?, ?, ?, ?, NOW()) 
                               ON DUPLICATE KEY UPDATE name = VALUES(name), subject = VALUES(subject), description = VALUES(description), html_content = VALUES(html_content), assigned_credential_id = VALUES(assigned_credential_id), updated_at = NOW()", [
                $key, $name, $subject, $description, $htmlContent, $assignedCredentialId ?: null
            ]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function deleteCustomTemplate(int $id): bool {
        try {
            return Database::execute("DELETE FROM mail_templates WHERE id = ?", [$id]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function getLogById(int $id): ?array {
        try {
            return Database::fetch("SELECT * FROM mail_logs WHERE id = ?", [$id]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function sanitizeLightMode(string $html): string {
        $replaces = [
            'background:#0f172a' => 'background:#ffffff',
            'background-color:#0f172a' => 'background-color:#ffffff',
            'background: #0f172a' => 'background: #ffffff',
            'background:#05070b' => 'background:#f8fafc',
            'background-color:#05070b' => 'background-color:#f8fafc',
            'background: #05070b' => 'background: #f8fafc',
            'background:#1e293b' => 'background:#f1f5f9',
            'background: #1e293b' => 'background: #f1f5f9',
            'color:#f8fafc' => 'color:#0f172a',
            'color: #f8fafc' => 'color: #0f172a',
            'color:#cbd5e1' => 'color:#334155',
            'color: #cbd5e1' => 'color: #334155',
            'color:#94a3b8' => 'color:#475569',
            'color: #94a3b8' => 'color: #475569',
            'color:#64748b' => 'color:#475569',
            'color: #64748b' => 'color: #475569',
            'rgba(99,102,241,0.3)' => '#e0e7ff',
            'rgba(56,189,248,0.3)' => '#bae6fd',
            'rgba(16,185,129,0.3)' => '#a7f3d0'
        ];
        return str_ireplace(array_keys($replaces), array_values($replaces), $html);
    }

    public static function wrapUniversalEnvelope(string $innerContent, string $subject, array $options = []): string {
        // If content is already a full HTML document, return as is (with light-mode sanitization)
        if (stripos($innerContent, '<!DOCTYPE') !== false || stripos($innerContent, '<html') !== false) {
            return self::sanitizeLightMode($innerContent);
        }

        $appUrl = rtrim(Config::get('app_url') ?: 'http://localhost/techscout.n', '/');
        $siteName = Config::get('app_name', 'TechScout');
        $cleanInner = self::sanitizeLightMode($innerContent);
        $attachmentsHtml = $options['attachments_html'] ?? '';

        return "<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>" . htmlspecialchars($subject) . "</title>
</head>
<body style='margin:0;padding:0;background-color:#ffffff;background-image:linear-gradient(to right, rgba(99,102,241,0.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.08) 1px, transparent 1px);background-size:32px 32px;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;color:#0f172a;'>
  <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color:#ffffff;background-image:linear-gradient(to right, rgba(99,102,241,0.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.08) 1px, transparent 1px);background-size:32px 32px;padding:36px 12px;'>
    <tr>
      <td align='center'>
        <table width='100%' border='0' cellspacing='0' cellpadding='0' style='max-width:600px;background-color:#ffffff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 10px 25px -5px rgba(0,0,0,0.07), 0 8px 10px -6px rgba(0,0,0,0.05);overflow:hidden;'>
          <!-- Header Branding with Universal Grid Accent -->
          <tr>
            <td style='background:#ffffff;background-image:linear-gradient(to right, rgba(99,102,241,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.05) 1px, transparent 1px);background-size:24px 24px;padding:26px 32px;border-bottom:2px solid #6366f1;text-align:center;'>
              <a href='" . $appUrl . "' style='text-decoration:none;'>
                <span style='font-size:24px;font-weight:900;letter-spacing:-0.5px;color:#0f172a;'>Tech<span style='color:#6366f1;'>Scout</span></span>
              </a>
              <div style='color:#64748b;font-size:11px;margin-top:4px;letter-spacing:1.5px;text-transform:uppercase;font-weight:600;'>Developer Intelligence Network</div>
            </td>
          </tr>
          <!-- Body Content Area -->
          <tr>
            <td style='padding:36px 32px;color:#1e293b;font-size:15px;line-height:1.65;'>
              " . $cleanInner . "
              " . $attachmentsHtml . "
            </td>
          </tr>
          <!-- Footer -->
          <tr>
            <td style='background-color:#f8fafc;padding:24px 32px;border-top:1px solid #e2e8f0;text-align:center;'>
              <p style='color:#64748b;font-size:12px;margin:0 0 6px 0;'>
                &copy; " . date('Y') . " " . htmlspecialchars($siteName) . " Platform &bull; Built for builders, engineers, and creators.
              </p>
              <p style='color:#64748b;font-size:11px;margin:0;'>
                You received this email because you are an active member, creator, or subscriber.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>";
    }

    public static function renderTemplate(string $templateKey, array $data = [], array $attachments = []): array {
        $templates = self::getTemplates();
        $tmpl = $templates[$templateKey] ?? $templates['community_broadcast'];

        $appUrl = rtrim(Config::get('app_url') ?: 'http://localhost/techscout.n', '/');
        $siteName = Config::get('app_name', 'TechScout');
        $name = htmlspecialchars($data['name'] ?? 'Engineer');
        $username = htmlspecialchars($data['username'] ?? strtolower(preg_replace('/[^a-z0-9]/i', '', $name)));
        $email = htmlspecialchars($data['email'] ?? 'user@techscout.n');
        $role = htmlspecialchars($data['role'] ?? 'Member');
        
        $actionUrl = $data['action_url'] ?? $appUrl;
        if (!str_starts_with($actionUrl, 'http')) {
            $actionUrl = $appUrl . (str_starts_with($actionUrl, '/') ? $actionUrl : '/' . $actionUrl);
        }

        $defaults = [
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'user_id' => (string)($data['user_id'] ?? '1'),
            'action_url' => $actionUrl,
            'app_url' => $appUrl,
            'login_url' => $appUrl . '/login',
            'register_url' => $appUrl . '/register',
            'unsubscribe_url' => $appUrl . '/settings/notifications',
            'site_name' => $siteName,
            'year' => (string)date('Y'),
            'date' => (string)date('F j, Y'),
            'support_email' => 'support@techscout.n',
            'headline' => (string)($data['headline'] ?? 'Platform Intelligence Announcement'),
            'message' => (string)($data['message'] ?? 'Platform update details.'),
            'article_title' => (string)($data['article_title'] ?? 'Technical Publication'),
            'editorial_notes' => (string)($data['editorial_notes'] ?? 'Editorial team review notes.'),
            'ticket_number' => (string)($data['ticket_number'] ?? ($data['ticket_id'] ?? '1001')),
            'ticket_id' => (string)($data['ticket_id'] ?? ($data['ticket_number'] ?? '1001')),
            'ticket_subject' => (string)($data['ticket_subject'] ?? 'Technical Support Request'),
            'reply_message' => (string)($data['reply_message'] ?? 'Update on your ticket request.'),
            'reset_code' => (string)($data['reset_code'] ?? 'TS-' . rand(100000, 999999)),
            'ip_address' => (string)($data['ip_address'] ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')),
            'browser' => (string)($data['browser'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Web Browser')),
            'timestamp' => (string)date('Y-m-d H:i:s T'),
            'verification_code' => (string)($data['verification_code'] ?? rand(100000, 999999)),
            'expires_in' => '15 minutes',
            'badge_title' => 'Verified TechScout Author 🌟',
            'inquiry_subject' => (string)($data['inquiry_subject'] ?? 'General Inquiry'),
            'announcement_body' => (string)($data['announcement_body'] ?? ($data['message'] ?? 'Platform news.')),
            'amount' => (string)($data['amount'] ?? '0.00'),
            'net_amount' => (string)($data['net_amount'] ?? ($data['amount'] ?? '0.00')),
            'fee_deducted' => (string)($data['fee_deducted'] ?? '0.00'),
            'payout_id' => (string)($data['payout_id'] ?? '1001'),
            'ref_no' => (string)($data['ref_no'] ?? 'PAY-' . strtoupper(bin2hex(random_bytes(4)))),
            'method_type' => (string)($data['method_type'] ?? 'Bank Wire Transfer'),
            'destination' => (string)($data['destination'] ?? 'Verified Account'),
            'transaction_ref' => (string)($data['transaction_ref'] ?? 'TXN-' . strtoupper(bin2hex(random_bytes(4)))),
            'receipt_url' => (string)($data['receipt_url'] ?? ''),
            'tipper_name' => (string)($data['tipper_name'] ?? ($data['donor_name'] ?? 'Community Supporter')),
            'commenter_name' => (string)($data['commenter_name'] ?? 'Fellow Engineer'),
            'comment_snippet' => (string)($data['comment_snippet'] ?? 'Great technical analysis on model fine-tuning.'),
            'collection_title' => (string)($data['collection_title'] ?? 'Frontier AI Developer Stacks'),
            'sender_name' => (string)($data['sender_name'] ?? 'A TechScout Colleague'),
            'month' => (string)($data['month'] ?? date('F Y')),
            'total_reads' => (string)($data['total_reads'] ?? '12,480'),
            'tips_count' => (string)($data['tips_count'] ?? '8'),
            'campaign_title' => (string)($data['campaign_title'] ?? 'Autonomous Agent Hackathon 2026'),
            'prize_pool' => (string)($data['prize_pool'] ?? '10,000'),
            'deadline' => (string)($data['deadline'] ?? date('F j, Y', strtotime('+14 days'))),
            'two_factor_otp' => (string)($data['two_factor_otp'] ?? rand(100000, 999999)),
            'suspension_reason' => (string)($data['suspension_reason'] ?? 'Violation of Community Terms of Service'),
            'appeal_url' => $appUrl . '/support',
            'milestone_title' => (string)($data['milestone_title'] ?? '10,000 Verified Readers')
        ];

        $merged = array_merge($defaults, $data);

        // Subject Replacements
        $subject = $data['subject'] ?? $tmpl['subject'];
        foreach ($merged as $k => $v) {
            if (is_scalar($v)) {
                $subject = str_replace('{{' . $k . '}}', (string)$v, $subject);
            }
        }

        // Body Content
        $body = $data['body_html'] ?? ($tmpl['html_content'] ?? ($tmpl['html'] ?? ''));
        if (empty($body)) {
            $body = self::getDefaultTemplateBody($templateKey);
        }
        foreach ($merged as $k => $v) {
            if (is_scalar($v)) {
                $body = str_replace('{{' . $k . '}}', (string)$v, $body);
            }
        }

        // Format Attachments Box if present
        $attachmentsHtml = '';
        $itemsList = [];

        if (!empty($attachments)) {
            if (isset($attachments['links']) || isset($attachments['files'])) {
                if (!empty($attachments['links']) && is_array($attachments['links'])) {
                    foreach ($attachments['links'] as $l) {
                        if (!empty($l['url'])) {
                            $t = !empty($l['title']) ? $l['title'] : $l['url'];
                            $itemsList[] = "<li><a href='{$l['url']}' style='color:#38bdf8;text-decoration:none;font-weight:600;' target='_blank'>🔗 {$t} ↗</a></li>";
                        }
                    }
                }
                if (!empty($attachments['files']) && is_array($attachments['files'])) {
                    foreach ($attachments['files'] as $f) {
                        if (!empty($f['url'])) {
                            $fn = !empty($f['name']) ? $f['name'] : basename($f['url']);
                            $sz = !empty($f['size']) ? " ({$f['size']})" : '';
                            $itemsList[] = "<li><a href='{$f['url']}' style='color:#059669;text-decoration:none;font-weight:600;' target='_blank'>📄 {$fn}{$sz} ⬇</a></li>";
                        }
                    }
                }
            } else {
                foreach ($attachments as $att) {
                    if (isset($att['title']) && isset($att['url'])) {
                        $itemsList[] = "<li><a href='{$att['url']}' style='color:#38bdf8;text-decoration:none;font-weight:600;' target='_blank'>🔗 {$att['title']} ↗</a></li>";
                    } elseif (isset($att['name']) && isset($att['url'])) {
                        $sz = !empty($att['size']) ? " ({$att['size']})" : '';
                        $itemsList[] = "<li><a href='{$att['url']}' style='color:#059669;text-decoration:none;font-weight:600;' target='_blank'>📄 {$att['name']}{$sz} ⬇</a></li>";
                    } elseif (isset($att['name'])) {
                        $itemsList[] = "<li style='color:#334155;font-weight:600;'>📎 " . htmlspecialchars($att['name']) . " (Attached to email)</li>";
                    }
                }
            }

            if (!empty($itemsList)) {
                $attachmentsHtml .= "<div style='margin-top:24px;padding:18px;background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;'>";
                $attachmentsHtml .= "<div style='color:#0f172a;font-size:13px;font-weight:700;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;'>📎 Attached Files &amp; Resources:</div>";
                $attachmentsHtml .= "<ul style='margin:0;padding-left:20px;color:#334155;font-size:13px;line-height:1.6;'>" . implode('', $itemsList) . "</ul></div>";
            }
        }

        // Apply Universal Envelope Wrapper
        $html = self::wrapUniversalEnvelope($body, $subject, ['attachments_html' => $attachmentsHtml]);

        return [
            'subject' => $subject,
            'html' => $html
        ];
    }

    public static function getDefaultTemplateBody(string $templateKey): string {
        return match ($templateKey) {
            'welcome' => "
                <h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Welcome aboard TechScout, {{name}}! 🚀</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>You have joined the frontier community for technical journalism, AI tool indexing, custom GPT exploration, and creator monetization.</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:24px;'>
                  <div style='color:#4f46e5;font-weight:700;font-size:14px;margin-bottom:8px;'>⚡ Quickstart actions:</div>
                  <ul style='color:#475569;font-size:14px;line-height:1.65;margin:0;padding-left:20px;'>
                    <li>Explore curated technical breakdowns &amp; blueprints</li>
                    <li>Subscribe to your favorite technology topics</li>
                    <li>Publish technical guides and earn creator reputation</li>
                  </ul>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(79,70,229,0.25);'>Explore TechScout Intelligence &rarr;</a>
                </div>",

            'email_verification' => "
                <h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Verify Your Email Address 📧</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, please verify your email address to activate your account:</p>
                <div style='text-align:center;background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #cbd5e1;border-radius:10px;padding:22px;margin:24px 0;'>
                  <div style='font-size:12px;color:#64748b;letter-spacing:1px;text-transform:uppercase;font-weight:700;'>Verification PIN Code (Expires in {{expires_in}})</div>
                  <div style='font-size:34px;font-weight:900;color:#4f46e5;letter-spacing:8px;margin-top:8px;font-family:monospace;'>{{verification_code}}</div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(79,70,229,0.25);'>Confirm &amp; Verify Email &rarr;</a>
                </div>",

            'account_activated' => "
                <h2 style='color:#059669;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Account Successfully Verified! ✅</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, your email address has been verified and your TechScout account is fully active.</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:24px;'>
                  <div style='color:#059669;font-weight:700;font-size:14px;margin-bottom:6px;'>✓ Member Permissions Unlocked</div>
                  <div style='color:#475569;font-size:14px;'>You can now join architectural discussions, save custom prompt blueprints, and explore the AI tool radar.</div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#059669,#10b981);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(5,150,105,0.25);'>Go to Your Dashboard &rarr;</a>
                </div>",

            'login_alert' => "
                <h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>New Sign-In Security Alert 🔐</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, we detected a new sign-in to your TechScout account:</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:24px;'>
                  <div style='display:grid;gap:8px;font-size:14px;color:#334155;'>
                    <div><strong style='color:#64748b;'>Time:</strong> <span style='color:#0f172a;font-weight:600;'>{{timestamp}}</span></div>
                    <div><strong style='color:#64748b;'>IP Address:</strong> <code style='color:#4f46e5;background:#eef2ff;padding:2px 6px;border-radius:4px;'>{{ip_address}}</code></div>
                    <div><strong style='color:#64748b;'>Client Device:</strong> <span style='color:#0f172a;'>{{browser}}</span></div>
                  </div>
                </div>
                <div style='text-align:center;margin:24px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:#dc2626;color:#ffffff;font-weight:700;text-decoration:none;padding:11px 26px;border-radius:8px;font-size:14px;box-shadow:0 4px 12px rgba(220,38,38,0.25);'>Secure My Account &rarr;</a>
                </div>",

            'password_reset' => "
                <h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Reset Your Password 🔒</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 16px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>We received a request to reset your password for your TechScout account. Click the button below to securely choose a new password:</p>
                <div style='text-align:center;margin:30px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:14px 34px;border-radius:8px;font-size:16px;box-shadow:0 6px 20px rgba(79,70,229,0.3);letter-spacing:0.3px;'>Click Here to Reset Password &rarr;</a>
                </div>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;margin-bottom:24px;'>
                  <div style='font-size:13px;color:#64748b;margin-bottom:8px;font-weight:600;'>Can't click the button? Copy and paste this secure link directly into your browser:</div>
                  <div style='word-break:break-all;font-size:13px;color:#2563eb;font-family:monospace;background:#ffffff;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;'>
                    <a href='{{action_url}}' style='color:#2563eb;text-decoration:underline;'>{{action_url}}</a>
                  </div>
                </div>
                <p style='color:#64748b;font-size:13px;line-height:1.6;margin:0;'>This recovery link is valid for 2 hours. If you did not request this password reset, please disregard this email &mdash; your account remains completely secure.</p>",

            'security_alert' => "
                <h2 style='color:#dc2626;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Security Perimeter Notice 🛡️</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, your TechScout account password or credential profile was recently updated at <strong>{{change_time}}</strong>.</p>
                <div style='background:#fef2f2;border:1px solid #fecaca;border-left:4px solid #dc2626;padding:16px;border-radius:8px;margin-bottom:24px;color:#991b1b;font-size:14px;line-height:1.6;'>
                  If you initiated this change, no further action is required. If you did NOT perform this action, revoke all active sessions and reset your password immediately.
                </div>
                <div style='text-align:center;margin:24px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:#dc2626;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(220,38,38,0.25);'>Review Account Security &rarr;</a>
                </div>",

            'payout_requested' => "
                <h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Creator Payout Claim Submitted 💸</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, we received your withdrawal claim for royalty credits. Your request is queued for financial compliance review.</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:24px;'>
                  <div style='display:grid;gap:8px;font-size:14px;color:#334155;'>
                    <div><strong style='color:#64748b;'>Payout Identifier:</strong> <span style='color:#0f172a;font-weight:600;'>#{{payout_id}}</span> (Ref: <code>{{ref_no}}</code>)</div>
                    <div><strong style='color:#64748b;'>Gross Requested:</strong> \${{amount}} USD</div>
                    <div><strong style='color:#64748b;'>Platform &amp; Bank Fees:</strong> -\${{fee_deducted}} USD</div>
                    <div><strong style='color:#059669;'>Net Disbursable:</strong> <strong style='font-size:16px;color:#059669;'>\${{net_amount}} USD</strong></div>
                    <div><strong style='color:#64748b;'>Destination Method:</strong> {{method_type}} &bull; {{destination}}</div>
                    <div><strong style='color:#64748b;'>Status:</strong> <span style='background:#fffbeb;color:#d97706;padding:3px 10px;border-radius:4px;font-weight:700;font-size:12px;border:1px solid #fde68a;'>⏳ PENDING COMPLIANCE</span></div>
                  </div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(79,70,229,0.25);'>Track Rewards Dashboard &rarr;</a>
                </div>",

            'payout_approved' => "
                <h2 style='color:#059669;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Creator Payout Transferred &amp; Disbursed! 💰</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, your withdrawal claim <strong>#{{payout_id}}</strong> has been approved by the treasury desk and funds have been dispatched!</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:24px;'>
                  <div style='display:grid;gap:8px;font-size:14px;color:#334155;'>
                    <div><strong style='color:#64748b;'>Disbursed Amount:</strong> <strong style='font-size:18px;color:#059669;'>\${{net_amount}} USD</strong></div>
                    <div><strong style='color:#64748b;'>Transaction Reference:</strong> <code style='color:#4f46e5;'>{{transaction_ref}}</code></div>
                    <div><strong style='color:#64748b;'>Destination:</strong> {{destination}}</div>
                    <div><strong style='color:#64748b;'>Status:</strong> <span style='background:#ecfdf5;color:#059669;padding:3px 10px;border-radius:4px;font-weight:700;font-size:12px;border:1px solid #a7f3d0;'>✅ COMPLETED &amp; DISBURSED</span></div>
                  </div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:#059669;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(5,150,105,0.25);'>View Royalty Balance Ledger &rarr;</a>
                </div>",

            'payout_rejected' => "
                <h2 style='color:#d97706;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Payout Claim Update ⚠️</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, your recent withdrawal claim <strong>#{{payout_id}}</strong> could not be completed and requires revision.</p>
                <div style='background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #d97706;padding:16px;border-radius:8px;margin-bottom:20px;'>
                  <div style='color:#b45309;font-weight:700;font-size:13px;margin-bottom:4px;'>TREASURY COMPLIANCE NOTE:</div>
                  <div style='color:#92400e;font-size:14px;line-height:1.6;'>{{admin_notes}}</div>
                </div>
                <div style='background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:24px;color:#475569;font-size:13px;'>
                  <strong style='color:#059669;'>✓ Funds Protection Guarantee:</strong> 100% of your deducted credits (\${{amount}} USD) have been automatically restored to your active royalty balance.
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:#d97706;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(217,119,6,0.25);'>Update Payment Destination &rarr;</a>
                </div>",

            'tip_received' => "
                <h2 style='color:#7c3aed;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Reader Patronage Tip Received! 🎁</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, a community reader has awarded your work with a direct creator tip!</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:24px;'>
                  <div style='display:grid;gap:8px;font-size:14px;color:#334155;'>
                    <div><strong style='color:#64748b;'>Supporter:</strong> <strong>{{tipper_name}}</strong></div>
                    <div><strong style='color:#64748b;'>Gross Tip:</strong> \${{amount}} USD</div>
                    <div><strong style='color:#059669;'>Net Credited:</strong> <strong style='font-size:16px;color:#059669;'>+\${{net_amount}} USD</strong></div>
                    <div><strong style='color:#64748b;'>Target Article:</strong> {{article_title}}</div>
                  </div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#7c3aed,#db2777);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(124,58,237,0.25);'>Open Author Studio &rarr;</a>
                </div>",

            'article_approved' => "
                <h2 style='color:#059669;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Congratulations! Your submission is now LIVE! 🎉</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Our editorial board has verified and approved your technical publication <strong>&ldquo;{{article_title}}&rdquo;</strong>. It is now indexed across the TechScout global network.</p>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:#059669;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(5,150,105,0.25);'>View Live Publication &rarr;</a>
                </div>",

            'editorial_revisions' => "
                <h2 style='color:#d97706;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Action Required: Editorial Revisions Requested 📋</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Our editorial team has reviewed your submission <strong>&ldquo;{{article_title}}&rdquo;</strong> and requested the following adjustments before publication:</p>
                <div style='background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #d97706;padding:16px;border-radius:8px;color:#92400e;font-size:14px;line-height:1.6;margin-bottom:24px;'>
                  {{editorial_notes}}
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:#d97706;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(217,119,6,0.25);'>Open Editor to Make Changes &rarr;</a>
                </div>",

            'author_approved' => "
                <h2 style='color:#7c3aed;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Welcome to the TechScout Creator Network! 🌟</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Your application for creator publishing privileges has been approved! You can now publish deep-dive technical articles, submit verified AI tools, and earn creator rewards.</p>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#7c3aed,#db2777);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(124,58,237,0.25);'>Launch Author Studio &rarr;</a>
                </div>",

            'ticket_reply' => "
                <h2 style='color:#0284c7;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Support Helpdesk Response 🎫</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>A member of our engineering support team has provided an update on your ticket <strong>#{{ticket_number}}</strong>:</p>
                <div style='background:#f0f9ff;border:1px solid #bae6fd;border-left:4px solid #0284c7;border-radius:8px;padding:16px;color:#0369a1;font-size:14px;line-height:1.65;margin-bottom:24px;'>
                  {{reply_message}}
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:#0284c7;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(2,132,199,0.25);'>View Support Ticket &rarr;</a>
                </div>",

            'contact_reply' => "
                <h2 style='color:#0284c7;font-size:22px;font-weight:800;margin:0 0 16px 0;'>TechScout Operations Response 💬</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>In response to your inquiry regarding <strong>&ldquo;{{inquiry_subject}}&rdquo;</strong>:</p>
                <div style='background:#f0f9ff;border:1px solid #bae6fd;border-left:4px solid #0284c7;border-radius:8px;padding:16px;color:#0369a1;font-size:14px;line-height:1.65;margin-bottom:24px;'>
                  {{reply_message}}
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(79,70,229,0.25);'>Visit TechScout &rarr;</a>
                </div>",

            'author_application_received' => "
                <h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Author Application Received 📝</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong> (@{{username}}),</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Thank you for applying to join the TechScout Creator Network! Our editorial board is reviewing your portfolio, technical writing samples, and expertise areas.</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:24px;'>
                  <div style='color:#4f46e5;font-weight:700;font-size:14px;margin-bottom:6px;'>⏱️ What to expect next:</div>
                  <ul style='color:#475569;font-size:14px;line-height:1.65;margin:0;padding-left:20px;'>
                    <li>Review turnaround is typically 24-48 business hours.</li>
                    <li>Once approved, you will unlock the Creator Studio, article drafting, and author monetization.</li>
                  </ul>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(79,70,229,0.25);'>View Application Status &rarr;</a>
                </div>",

            'article_submitted' => "
                <h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Article Submitted for Review 📑</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Your technical submission <strong>&ldquo;{{article_title}}&rdquo;</strong> has been placed into the TechScout editorial review queue.</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:24px;'>
                  <div style='color:#475569;font-size:14px;line-height:1.65;'>Our technical reviewers verify code snippets, architectural accuracy, and formatting before publication. You will receive an alert as soon as the review concludes.</div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(79,70,229,0.25);'>Open Creator Studio &rarr;</a>
                </div>",

            'article_rejected' => "
                <h2 style='color:#dc2626;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Submission Review Notice ✍️</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Thank you for submitting <strong>&ldquo;{{article_title}}&rdquo;</strong>. After editorial review, we are unable to publish this draft in its current format.</p>
                <div style='background:#fef2f2;border:1px solid #fecaca;border-left:4px solid #dc2626;padding:16px;border-radius:8px;margin-bottom:24px;'>
                  <div style='color:#dc2626;font-weight:700;font-size:13px;margin-bottom:4px;'>EDITORIAL BOARD FEEDBACK:</div>
                  <div style='color:#991b1b;font-size:14px;line-height:1.6;'>{{editorial_notes}}</div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:#dc2626;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(220,38,38,0.25);'>Revise &amp; Re-submit Draft &rarr;</a>
                </div>",

            'comment_reply_alert' => "
                <h2 style='color:#0284c7;font-size:22px;font-weight:800;margin:0 0 16px 0;'>New Technical Discussion Reply 💬</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, <strong>{{commenter_name}}</strong> replied to your discussion on <strong>&ldquo;{{article_title}}&rdquo;</strong>:</p>
                <div style='background:#f0f9ff;border-left:4px solid #0284c7;border:1px solid #bae6fd;padding:16px;border-radius:8px;margin-bottom:24px;color:#0369a1;font-size:14px;line-height:1.65;'>
                  &ldquo;{{comment_snippet}}&rdquo;
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(79,70,229,0.25);'>View &amp; Reply in Discussion &rarr;</a>
                </div>",

            'bookmark_collection_shared' => "
                <h2 style='color:#7c3aed;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Curated Technical Collection Shared 📑</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'><strong>{{sender_name}}</strong> has shared a curated TechScout blueprint stack with you: <strong>&ldquo;{{collection_title}}&rdquo;</strong>.</p>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#7c3aed,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(124,58,237,0.25);'>Inspect Curated Stack &rarr;</a>
                </div>",

            'monthly_creator_earnings_statement' => "
                <h2 style='color:#059669;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Creator Performance &amp; Royalty Statement 📊</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, here is your creator performance summary for <strong>{{month}}</strong>:</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:24px;'>
                  <div style='display:grid;gap:8px;font-size:14px;color:#334155;'>
                    <div><strong style='color:#64748b;'>Total Readers &amp; Views:</strong> <strong style='color:#0f172a;'>{{total_reads}}</strong></div>
                    <div><strong style='color:#64748b;'>Reader Patronage Tips:</strong> <strong style='color:#0f172a;'>{{tips_count}}</strong></div>
                    <div><strong style='color:#059669;'>Net Accrued Royalties:</strong> <strong style='font-size:18px;color:#059669;'>\${{amount}} USD</strong></div>
                  </div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:#059669;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(5,150,105,0.25);'>Open Treasury &amp; Payouts &rarr;</a>
                </div>",

            'hackathon_campaign_invite' => "
                <h2 style='color:#d97706;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Developer Challenge &amp; Hackathon Invitation 🏆</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, you are invited to compete in the upcoming TechScout engineering sprint:</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:24px;'>
                  <div style='display:grid;gap:8px;font-size:14px;color:#334155;'>
                    <div><strong style='color:#64748b;'>Sprint Challenge:</strong> <strong style='color:#d97706;'>{{campaign_title}}</strong></div>
                    <div><strong style='color:#64748b;'>Prize Pool:</strong> <strong style='font-size:16px;color:#059669;'>\${{prize_pool}} USD</strong></div>
                    <div><strong style='color:#64748b;'>Submission Deadline:</strong> {{deadline}}</div>
                  </div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#d97706,#dc2626);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(217,119,6,0.25);'>View Challenge Rules &amp; Register &rarr;</a>
                </div>",

            'security_two_factor_otp' => "
                <h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Security Authorization PIN 🔑</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, enter the following one-time passcode (OTP) to authorize your security request:</p>
                <div style='text-align:center;background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #cbd5e1;border-radius:10px;padding:22px;margin:24px 0;'>
                  <div style='font-size:12px;color:#64748b;letter-spacing:1px;text-transform:uppercase;font-weight:700;'>Two-Factor Passcode (Expires in {{expires_in}})</div>
                  <div style='font-size:34px;font-weight:900;color:#4f46e5;letter-spacing:8px;margin-top:8px;font-family:monospace;'>{{two_factor_otp}}</div>
                  <div style='font-size:12px;color:#64748b;margin-top:8px;'>Requested from IP: {{ip_address}}</div>
                </div>",

            'user_account_suspended' => "
                <h2 style='color:#dc2626;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Account Suspension Notice ⚠️</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Your TechScout account has been temporarily suspended due to a breach of our community and publishing standards.</p>
                <div style='background:#fef2f2;border:1px solid #fecaca;border-left:4px solid #dc2626;padding:16px;border-radius:8px;margin-bottom:24px;'>
                  <div style='color:#dc2626;font-weight:700;font-size:13px;margin-bottom:4px;'>REASON FOR ACTION:</div>
                  <div style='color:#991b1b;font-size:14px;line-height:1.6;'>{{suspension_reason}}</div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{appeal_url}}' style='display:inline-block;background:#dc2626;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(220,38,38,0.25);'>Contact Support &amp; Submit Appeal &rarr;</a>
                </div>",

            'user_account_reactivated' => "
                <h2 style='color:#059669;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Account Restored &amp; Operational 🟢</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Following a compliance review, your TechScout account privileges have been fully restored. All features, bookmarks, and author tools are accessible again.</p>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:#059669;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(5,150,105,0.25);'>Log In to Your Dashboard &rarr;</a>
                </div>",

            'author_milestone_badge' => "
                <h2 style='color:#d97706;font-size:22px;font-weight:800;margin:0 0 16px 0;'>Creator Milestone Unlocked! 🎖️</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Congratulations! Your contributions have achieved the <strong>&ldquo;{{milestone_title}}&rdquo;</strong> honor on TechScout.</p>
                <div style='text-align:center;background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #cbd5e1;border-radius:10px;padding:20px;margin:20px 0;'>
                  <div style='font-size:36px;margin-bottom:6px;'>🌟</div>
                  <div style='font-size:18px;font-weight:800;color:#d97706;'>{{milestone_title}}</div>
                  <div style='font-size:13px;color:#64748b;margin-top:6px;'>Your author profile badge has been updated to reflect this milestone.</div>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#d97706,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(217,119,6,0.25);'>View Profile &amp; Badges &rarr;</a>
                </div>",

            'newsletter_breaking_ai_radar' => "
                <h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>⚡ Flash AI Radar: {{headline}}</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border-left:4px solid #4f46e5;border:1px solid #e2e8f0;padding:18px;border-radius:8px;margin-bottom:24px;color:#1e293b;font-size:15px;line-height:1.65;'>
                  {{message}}
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(79,70,229,0.25);'>Read Full Analysis on TechScout &rarr;</a>
                </div>",

            default => "
                <h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>{{headline}}</h2>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>,</p>
                <p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>{{message}}</p>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(79,70,229,0.25);'>Read Full Update &rarr;</a>
                </div>"
        };
    }

    public static function getTemplateCategory(?string $templateKey): string {
        return match ($templateKey) {
            'welcome', 'email_verification', 'account_activated', 'login_alert', 'password_reset', 'security_alert' => 'auth_security',
            'payout_requested', 'payout_approved', 'payout_rejected', 'tip_received' => 'billing_payouts',
            'article_approved', 'editorial_revisions', 'author_approved' => 'notifications_editorial',
            'ticket_reply', 'contact_reply' => 'support_tickets',
            default => 'marketing_broadcast'
        };
    }

    public static function sendTemplate(string $templateKey, string $toEmail, ?string $toName = null, array $data = [], array $attachments = [], ?int $overrideCredentialId = null): bool {
        $rendered = self::renderTemplate($templateKey, $data, $attachments);
        return self::send(
            $toEmail,
            $toName ?: ($data['name'] ?? null),
            $rendered['subject'],
            $rendered['html'],
            $templateKey,
            $attachments,
            null,
            null,
            $overrideCredentialId
        );
    }

    public static function getSenderProfiles(): array {
        $settings = self::getSettings();
        return [
            'noreply' => [
                'key' => 'noreply',
                'name' => $settings['mail_sender_noreply_name'] ?? 'TechScout System Notifications',
                'email' => $settings['mail_sender_noreply_email'] ?? 'noreply@techscout.n',
                'desc' => 'Automated account confirmations, OTPs, password resets'
            ],
            'editorial' => [
                'key' => 'editorial',
                'name' => $settings['mail_sender_editorial_name'] ?? 'TechScout Editorial Board',
                'email' => $settings['mail_sender_editorial_email'] ?? 'editorial@techscout.n',
                'desc' => 'Publication approvals, peer review feedback, revision notes'
            ],
            'newsletter' => [
                'key' => 'newsletter',
                'name' => $settings['mail_sender_newsletter_name'] ?? 'TechScout Intelligence Digest',
                'email' => $settings['mail_sender_newsletter_email'] ?? 'newsletter@techscout.n',
                'desc' => 'Weekly curated digests, trending articles, announcements'
            ],
            'support' => [
                'key' => 'support',
                'name' => $settings['mail_sender_support_name'] ?? 'TechScout Helpdesk & Support',
                'email' => $settings['mail_sender_support_email'] ?? 'support@techscout.n',
                'desc' => 'Ticket replies, account inquiries, billing assistance'
            ],
            'security' => [
                'key' => 'security',
                'name' => $settings['mail_sender_security_name'] ?? 'TechScout Security Perimeter',
                'email' => $settings['mail_sender_security_email'] ?? 'security@techscout.n',
                'desc' => 'Security alerts, suspicious login warnings, WAF blocks'
            ],
            'payouts' => [
                'key' => 'payouts',
                'name' => $settings['mail_sender_payouts_name'] ?? 'TechScout Treasury & Payouts',
                'email' => $settings['mail_sender_payouts_email'] ?? 'payouts@techscout.n',
                'desc' => 'Author rewards disbursements, withdrawal receipts, tip alerts'
            ]
        ];
    }

    public static function send(string $toEmail, ?string $toName, string $subject, string $htmlContent, ?string $templateKey = null, array $attachments = [], ?string $overrideFromEmail = null, ?string $overrideFromName = null, ?int $overrideCredentialId = null): bool {
        $cfg = self::getSettings();
        $driver = $cfg['mail_driver'] ?? 'log';
        $category = self::getTemplateCategory($templateKey);

        $fromEmail = !empty($overrideFromEmail) ? $overrideFromEmail : ($cfg['mail_from_address'] ?? 'notifications@techscout.online');
        $fromName = !empty($overrideFromName) ? $overrideFromName : ($cfg['mail_from_name'] ?? 'TechScout Intelligence');
        $status = 'sent';
        $errorMessage = null;

        // Manual Credential Assignment Resolution (No automated category guessing)
        $credProfile = null;

        // 1. Explicit override credential chosen manually by admin or caller
        if (!empty($overrideCredentialId) && $overrideCredentialId > 0) {
            try {
                $credProfile = Database::fetch("SELECT * FROM mail_credentials WHERE id = ? AND status = 'active'", [(int)$overrideCredentialId]);
            } catch (\Throwable $e) {}
        }

        // 2. Template-assigned credential profile (manually mapped by admin per template)
        if (!$credProfile && !empty($templateKey)) {
            try {
                $tmpl = Database::fetch("SELECT assigned_credential_id FROM mail_templates WHERE template_key = ?", [$templateKey]);
                if (!empty($tmpl['assigned_credential_id'])) {
                    $credProfile = Database::fetch("SELECT * FROM mail_credentials WHERE id = ? AND status = 'active'", [(int)$tmpl['assigned_credential_id']]);
                }
            } catch (\Throwable $e) {}
        }

        // 3. Platform default credential profile (designated by admin: is_default = 1)
        if (!$credProfile) {
            try {
                $credProfile = Database::fetch("SELECT * FROM mail_credentials WHERE is_default = 1 AND status = 'active' LIMIT 1");
            } catch (\Throwable $e) {}
        }

        // 4. Fallback to first active credential profile
        if (!$credProfile) {
            try {
                $credProfile = Database::fetch("SELECT * FROM mail_credentials WHERE status = 'active' ORDER BY id ASC LIMIT 1");
            } catch (\Throwable $e) {}
        }

        // 5. If still no profile, construct virtual profile from global site_settings
        if (!$credProfile) {
            $credProfile = [
                'id' => null,
                'name' => 'Global Platform Config',
                'provider' => $cfg['mail_api_provider'] ?? ($driver === 'smtp' ? 'smtp' : 'python_api'),
                'from_name' => $fromName,
                'from_email' => $fromEmail,
                'reply_to_email' => $fromEmail,
                'api_endpoint' => $cfg['mail_api_endpoint'] ?? null,
                'api_key' => $cfg['mail_api_key'] ?? null,
                'smtp_host' => $cfg['mail_smtp_host'] ?? 'smtp.gmail.com',
                'smtp_port' => !empty($cfg['mail_smtp_port']) ? (int)$cfg['mail_smtp_port'] : 587,
                'smtp_username' => $cfg['mail_smtp_username'] ?? null,
                'smtp_password' => $cfg['mail_smtp_password'] ?? null,
                'smtp_encryption' => $cfg['mail_smtp_encryption'] ?? 'tls',
                'payload_format' => 'standard_json'
            ];
        }

        // Universal Smart Variable Resolver: Ensure no raw {{variables}} ever leak to recipients in subject or body
        $appUrl = rtrim(Config::get('app_url') ?: 'http://localhost/techscout.n', '/');
        $siteName = Config::get('app_name', 'TechScout');
        $displayName = $toName ?: explode('@', $toEmail)[0];
        $cleanUsername = strtolower(preg_replace('/[^a-z0-9]/i', '', $displayName));
        
        $unsubToken = bin2hex(hash('sha256', $toEmail . 'ts_unsub_salt_2026', true));
        $unsubUrl = $appUrl . '/newsletter/unsubscribe?email=' . urlencode($toEmail) . '&token=' . $unsubToken;

        $universalVars = [
            '{{name}}' => htmlspecialchars($displayName),
            '{{username}}' => htmlspecialchars($cleanUsername),
            '{{email}}' => htmlspecialchars($toEmail),
            '{{recipient_email}}' => htmlspecialchars($toEmail),
            '{{headline}}' => 'Frontier Tech & Engineering Blueprints',
            '{{article_title}}' => 'Technical Deep Dive & Architecture Insights',
            '{{message}}' => 'Platform intelligence update.',
            '{{digest_summary}}' => '• Curated technical radar and architecture updates.',
            '{{action_url}}' => $appUrl,
            '{{action_text}}' => 'Explore Platform →',
            '{{app_url}}' => $appUrl,
            '{{site_name}}' => $siteName,
            '{{unsubscribe_url}}' => $unsubUrl,
            '{{year}}' => date('Y'),
            '{{date}}' => date('F j, Y'),
            '{{support_email}}' => 'support@' . (parse_url($appUrl, PHP_URL_HOST) ?: 'techscout.online')
        ];

        if (str_contains($subject, '{{')) {
            $subject = str_replace(array_keys($universalVars), array_values($universalVars), $subject);
            $subject = preg_replace('/\{\{[a-zA-Z0-9_-]+\}\}/', '', $subject);
            $subject = trim(preg_replace('/\s+/', ' ', $subject));
        }

        if (str_contains($htmlContent, '{{')) {
            $htmlContent = str_replace(array_keys($universalVars), array_values($universalVars), $htmlContent);
            $htmlContent = preg_replace('/\{\{[a-zA-Z0-9_-]+\}\}/', '', $htmlContent);
        }

        // Strict Attachment Sanitization: Filter out non-file arrays, empty structures, or link lists
        $cleanAttachments = [];
        if (!empty($attachments) && is_array($attachments)) {
            $candidateFiles = isset($attachments['files']) && is_array($attachments['files']) ? $attachments['files'] : (isset($attachments['links']) ? [] : $attachments);
            foreach ($candidateFiles as $k => $att) {
                if (!is_array($att)) continue;
                if (!empty($att['content']) || (!empty($att['path']) && file_exists($att['path']))) {
                    $cleanAttachments[] = [
                        'name' => $att['name'] ?? 'attachment_' . (is_numeric($k) ? ($k + 1) : $k),
                        'content' => !empty($att['content']) ? $att['content'] : file_get_contents($att['path']),
                        'type' => $att['type'] ?? 'application/octet-stream'
                    ];
                }
            }
        }
        $attachments = $cleanAttachments;

        // Generate Comprehensive Tracking Token & Inject Telemetry (1x1 Pixel & Click Tracking)
        $trackingToken = bin2hex(random_bytes(16));
        $htmlContent = self::injectTracking($htmlContent, $trackingToken, $appUrl, ($category === 'newsletter' ? 'newsletter' : 'general'));

        if ($credProfile) {
            $fromName = !empty($overrideFromName) ? $overrideFromName : ($credProfile['from_name'] ?? $fromName);
            $fromEmail = !empty($overrideFromEmail) ? $overrideFromEmail : ($credProfile['from_email'] ?? $fromEmail);
            $replyToEmail = !empty($credProfile['reply_to_email']) ? $credProfile['reply_to_email'] : $fromEmail;
            $driver = $credProfile['provider'] === 'smtp' ? 'smtp' : $credProfile['provider'];

            $smtpHost = $credProfile['host'] ?? $credProfile['smtp_host'] ?? ($cfg['mail_smtp_host'] ?? 'smtp.gmail.com');
            $smtpPort = (int)($credProfile['port'] ?? $credProfile['smtp_port'] ?? ($cfg['mail_smtp_port'] ?? 587));
            $smtpUser = $credProfile['username'] ?? $credProfile['smtp_username'] ?? ($cfg['mail_smtp_username'] ?? null);
            $smtpPass = $credProfile['password'] ?? $credProfile['smtp_password'] ?? ($cfg['mail_smtp_password'] ?? null);
            $smtpEnc  = $credProfile['encryption'] ?? $credProfile['smtp_encryption'] ?? ($cfg['mail_smtp_encryption'] ?? 'tls');

            if (!empty($smtpPass) && (strpos($smtpHost, 'gmail.com') !== false || strpos((string)$smtpUser, '@gmail.com') !== false)) {
                $smtpPass = str_replace(' ', '', $smtpPass);
            }

            $isCustomApi = in_array($credProfile['provider'], ['python_api', 'pythonanywhere_api', 'custom_rest', 'resend', 'brevo', 'sendgrid', 'mailgun', 'postmark', 'ses']) || !empty($credProfile['api_endpoint']);

            if ($isCustomApi) {
                try {
                    $endpoint = !empty($credProfile['api_endpoint']) ? $credProfile['api_endpoint'] : (!empty($cfg['mail_api_endpoint']) ? $cfg['mail_api_endpoint'] : 'https://api.resend.com/emails');
                    $timeout = !empty($credProfile['request_timeout']) ? (int)$credProfile['request_timeout'] : 15;
                    $payloadFormat = $credProfile['payload_format'] ?? 'standard_json';
                    $apiKey = $credProfile['api_key'] ?? ($cfg['mail_api_key'] ?? '');

                    $headers = ['Content-Type: application/json', 'Accept: application/json'];

                    if ($payloadFormat === 'brevo_compatible' || $credProfile['provider'] === 'brevo' || strpos($endpoint, 'brevo.com') !== false) {
                        if (empty($credProfile['api_endpoint'])) {
                            $endpoint = 'https://api.brevo.com/v3/smtp/email';
                        }
                        $headers[] = 'api-key: ' . $apiKey;
                        $payload = [
                            'sender' => ['name' => $fromName, 'email' => $fromEmail],
                            'to' => [['email' => $toEmail, 'name' => $toName ?: $toEmail]],
                            'subject' => $subject,
                            'htmlContent' => $htmlContent
                        ];
                        if (!empty($replyToEmail)) {
                            $payload['replyTo'] = ['email' => $replyToEmail, 'name' => $fromName];
                        }
                    } elseif ($payloadFormat === 'resend_compatible' || $credProfile['provider'] === 'resend' || strpos($endpoint, 'resend.com') !== false) {
                        if (empty($credProfile['api_endpoint'])) {
                            $endpoint = 'https://api.resend.com/emails';
                        }
                        $headers[] = 'Authorization: Bearer ' . $apiKey;
                        $payload = [
                            'from' => $fromName . ' <' . $fromEmail . '>',
                            'to' => [$toEmail],
                            'subject' => $subject,
                            'html' => $htmlContent
                        ];
                        if (!empty($replyToEmail)) {
                            $payload['reply_to'] = $replyToEmail;
                        }
                    } elseif ($payloadFormat === 'sendgrid_compatible' || $credProfile['provider'] === 'sendgrid' || strpos($endpoint, 'sendgrid.com') !== false) {
                        if (empty($credProfile['api_endpoint'])) {
                            $endpoint = 'https://api.sendgrid.com/v3/mail/send';
                        }
                        $headers[] = 'Authorization: Bearer ' . $apiKey;
                        $payload = [
                            'personalizations' => [['to' => [['email' => $toEmail, 'name' => $toName ?: '']]]],
                            'from' => ['email' => $fromEmail, 'name' => $fromName],
                            'subject' => $subject,
                            'content' => [['type' => 'text/html', 'value' => $htmlContent]]
                        ];
                    } else {
                        $traceId = 'trc_mail_' . bin2hex(random_bytes(8));
                        $idempotencyKey = hash('sha256', $toEmail . '|' . $subject . '|' . date('YmdH'));

                        $payload = [
                            'event' => 'mail.dispatch',
                            'trace_id' => $traceId,
                            'idempotency_key' => $idempotencyKey,
                            'category' => $category,
                            'outbound_proxy' => $cfg['mail_outbound_proxy_url'] ?? null,
                            'to_email' => $toEmail,
                            'to_name' => $toName,
                            'from_email' => $fromEmail,
                            'from_name' => $fromName,
                            'reply_to' => $replyToEmail,
                            'subject' => $subject,
                            'html_content' => $htmlContent,
                            'text_content' => strip_tags($htmlContent),
                            'template_key' => $templateKey,
                            'attachments' => $attachments,
                            'timestamp_iso' => gmdate('c'),
                            'timestamp_epoch' => time(),
                            'credentials' => [
                                'provider' => $credProfile['provider'],
                                'credential_id' => !empty($credProfile['id']) ? (int)$credProfile['id'] : null,
                                'credential_name' => $credProfile['name'] ?? 'Default Profile',
                                'from_name' => $fromName,
                                'from_email' => $fromEmail,
                                'reply_to_email' => $replyToEmail,
                                'api_endpoint' => $credProfile['api_endpoint'] ?? null,
                                'api_key' => $apiKey,
                                'smtp_host' => $smtpHost,
                                'smtp_port' => $smtpPort,
                                'smtp_username' => $smtpUser,
                                'smtp_password' => $smtpPass,
                                'smtp_encryption' => $smtpEnc,
                            ],
                            'system_context' => [
                                'app_name' => 'TechScout',
                                'app_version' => '2.5.0',
                                'environment' => defined('APP_ENV') ? APP_ENV : 'production',
                                'server_host' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                                'client_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                                'client_ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'TechScout/Mailer',
                                'triggered_by_user_id' => Auth::id() ?? null,
                                'php_version' => PHP_VERSION,
                            ]
                        ];

                        $signingSecret = !empty($apiKey) ? $apiKey : ($cfg['mail_webhook_secret'] ?? ($cfg['mail_api_key'] ?? 'techscout_webhook_secure_secret_2026'));
                        if (!empty($signingSecret)) {
                            $headers[] = 'Authorization: Bearer ' . $signingSecret;
                            $headers[] = 'X-API-Key: ' . $signingSecret;
                            $headers[] = 'X-Service-Auth: ' . $signingSecret;
                            $sig = hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $signingSecret);
                            $headers[] = 'X-TechScout-Signature: sha256=' . $sig;
                        }
                        $headers[] = 'X-TechScout-Event: mail.dispatch';
                        $headers[] = 'X-TechScout-Delivery: ' . uniqid('del_');
                        $headers[] = 'X-TechScout-Trace: ' . $traceId;
                    }

                    if (!empty($credProfile['custom_headers_json'])) {
                        $customH = json_decode($credProfile['custom_headers_json'], true);
                        if (is_array($customH)) {
                            foreach ($customH as $hKey => $hVal) {
                                $headers[] = "{$hKey}: {$hVal}";
                            }
                        }
                    }

                    $jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlErr = curl_error($ch);
                    curl_close($ch);

                    if ($httpCode >= 200 && $httpCode < 300) {
                        $status = 'sent';
                    } else {
                        $status = 'failed';
                        $errorMessage = "HTTP {$httpCode}: " . ($curlErr ? "Curl error: {$curlErr}" : (string)$response);
                    }
                } catch (\Throwable $ex) {
                    $status = 'failed';
                    $errorMessage = $ex->getMessage();
                }
            } elseif ($credProfile['provider'] === 'smtp') {
                try {
                    $smtpRes = \App\Core\SmtpClient::sendMail($smtpHost, $smtpPort, $smtpUser, $smtpPass, $smtpEnc, $fromEmail, $fromName, $toEmail, $toName, $subject, $htmlContent, $replyToEmail, $attachments);
                    if ($smtpRes['success']) {
                        $status = 'sent';
                    } else {
                        $status = 'failed';
                        $errorMessage = $smtpRes['error'] ?? 'SMTP dispatch failed';
                        if (function_exists('mail')) {
                            $nativeHeaders = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: {$fromName} <{$fromEmail}>\r\n" . (!empty($replyToEmail) ? "Reply-To: {$replyToEmail}\r\n" : "") . "X-Mailer: PHP/" . phpversion() . "\r\n";
                            if (@mail($toEmail, $subject, $htmlContent, $nativeHeaders)) {
                                $status = 'sent';
                                $errorMessage = null;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $status = 'failed';
                    $errorMessage = $e->getMessage();
                    if (function_exists('mail')) {
                        $nativeHeaders = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: {$fromName} <{$fromEmail}>\r\n" . (!empty($replyToEmail) ? "Reply-To: {$replyToEmail}\r\n" : "") . "X-Mailer: PHP/" . phpversion() . "\r\n";
                        if (@mail($toEmail, $subject, $htmlContent, $nativeHeaders)) {
                            $status = 'sent';
                            $errorMessage = null;
                        }
                    }
                }
            }
        } elseif ($driver === 'api') {
            $apiKey = $cfg['mail_api_key'] ?? '';
            $endpoint = $cfg['mail_api_endpoint'] ?? 'https://api.resend.com/emails';
            if (empty($apiKey)) {
                $status = 'failed';
                $errorMessage = 'API Key is empty in Mail Settings.';
            } else {
                try {
                    $payload = ['from' => $fromName . ' <' . $fromEmail . '>', 'to' => [$toEmail], 'subject' => $subject, 'html' => $htmlContent];
                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $apiKey]);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlErr = curl_error($ch);
                    curl_close($ch);
                    if ($httpCode >= 200 && $httpCode < 300) {
                        $status = 'sent';
                    } else {
                        $status = 'failed';
                        $errorMessage = "HTTP " . $httpCode . ": " . ($curlErr ? "Curl error: {$curlErr}" : (string)$response);
                    }
                } catch (\Throwable $ex) {
                    $status = 'failed';
                    $errorMessage = $ex->getMessage();
                }
            }
        } elseif ($driver === 'smtp') {
            $smtpHost = $cfg['mail_smtp_host'] ?? 'smtp.gmail.com';
            $smtpPort = (int)($cfg['mail_smtp_port'] ?? 587);
            $smtpUser = $cfg['mail_smtp_username'] ?? null;
            $smtpPass = $cfg['mail_smtp_password'] ?? null;
            $smtpEnc  = $cfg['mail_smtp_encryption'] ?? 'tls';
            if (!empty($smtpPass) && (strpos($smtpHost, 'gmail.com') !== false || strpos((string)$smtpUser, '@gmail.com') !== false)) {
                $smtpPass = str_replace(' ', '', $smtpPass);
            }
            try {
                $smtpRes = \App\Core\SmtpClient::sendMail($smtpHost, $smtpPort, $smtpUser, $smtpPass, $smtpEnc, $fromEmail, $fromName, $toEmail, $toName, $subject, $htmlContent, $fromEmail, $attachments);
                if ($smtpRes['success']) {
                    $status = 'sent';
                } else {
                    $status = 'failed';
                    $errorMessage = $smtpRes['error'] ?? 'SMTP dispatch failed';
                    if (function_exists('mail')) {
                        $nativeHeaders = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: {$fromName} <{$fromEmail}>\r\n";
                        if (@mail($toEmail, $subject, $htmlContent, $nativeHeaders)) {
                            $status = 'sent';
                            $errorMessage = null;
                        }
                    }
                }
            } catch (\Throwable $e) {
                $status = 'failed';
                $errorMessage = $e->getMessage();
                if (function_exists('mail')) {
                    $nativeHeaders = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: {$fromName} <{$fromEmail}>\r\n";
                    if (@mail($toEmail, $subject, $htmlContent, $nativeHeaders)) {
                        $status = 'sent';
                        $errorMessage = null;
                    }
                }
            }
        } else {
            $status = 'sent';
            $errorMessage = 'Simulated Delivery (Driver: Local Log)';
        }

        // Record in mail_logs table with full body_html, tracking_token & attachments_json (fail-safe)
        try {
            $attachmentsJson = json_encode(!empty($attachments) ? $attachments : []);
            $credName = !empty($credProfile['name']) ? $credProfile['name'] : strtoupper($driver);

            // 1. Try modern schema with tracking_token, from_email, from_name, credential_name
            $inserted = Database::executeSilent(
                "INSERT INTO mail_logs (to_email, to_name, from_email, from_name, subject, template_key, body_html, attachments_json, driver, credential_name, tracking_token, status, error_message, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [$toEmail, $toName, $fromEmail, $fromName, $subject, $templateKey, $htmlContent, $attachmentsJson, $driver, $credName, $trackingToken, $status, $errorMessage]
            );

            if (!$inserted) {
                // Fallback to legacy schema if needed
                Database::executeSilent(
                    "INSERT INTO mail_logs (to_email, to_name, subject, template_key, body_html, attachments_json, driver, status, error_message, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                    [$toEmail, $toName, $subject, $templateKey, $htmlContent, $attachmentsJson, $driver, $status, $errorMessage]
                );
            }
        } catch (\Throwable $e) {
            // Absolute fail-safe: logging failure must never disrupt dispatch
        }

        // Emit real-time webhook event to all subscribed endpoints
        try {
            $mailEvent = ($status === 'sent') ? 'mail.sent' : 'mail.failed';
            WebhookEngine::dispatch($mailEvent, [
                'to_email' => $toEmail,
                'to_name' => $toName,
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'subject' => $subject,
                'template_key' => $templateKey,
                'driver' => $driver,
                'tracking_token' => $trackingToken,
                'status' => $status,
                'error_message' => $errorMessage,
                'timestamp' => time()
            ]);
        } catch (\Throwable $e) {}

        return ($status === 'sent');
    }

    /**
     * Injects open tracking pixel and rewrites clickable links for click tracking.
     * Supports both general platform emails (/mail/track/...) and newsletter broadcasts (/newsletter/track/...).
     */
    public static function injectTracking(string $htmlContent, string $trackingToken, ?string $appUrl = null, string $type = 'general'): string {
        $appUrl = $appUrl ?: rtrim(Config::get('app_url') ?: 'http://localhost/techscout.n', '/');
        $baseRoute = ($type === 'newsletter') ? '/newsletter/track' : '/mail/track';

        // 1. Rewrite <a href="..."> links for click tracking (skip unsubscribe & existing tracking endpoints)
        $trackedHtml = preg_replace_callback('/<a\s+([^>]*?)href=(["\'])(https?:\/\/[^"\']+)\2([^>]*)>/i', function($matches) use ($appUrl, $trackingToken, $baseRoute) {
            $before = $matches[1];
            $url = $matches[3];
            $after = $matches[4];
            if (str_contains($url, '/unsubscribe') || str_contains($url, '/track/')) {
                return $matches[0];
            }
            $trackedUrl = $appUrl . $baseRoute . '/click/' . urlencode($trackingToken) . '?url=' . urlencode($url);
            return '<a ' . $before . 'href="' . $trackedUrl . '"' . $after . '>';
        }, $htmlContent);

        // 2. Inject transparent 1x1 tracking pixel before </body> or at end
        $pixelUrl = $appUrl . $baseRoute . '/open/' . urlencode($trackingToken);
        $pixelTag = '<img src="' . $pixelUrl . '" width="1" height="1" style="display:none;width:1px;height:1px;max-height:0;overflow:hidden;" alt="" />';

        if (stripos($trackedHtml, '</body>') !== false) {
            $trackedHtml = str_ireplace('</body>', $pixelTag . '</body>', $trackedHtml);
        } else {
            $trackedHtml .= $pixelTag;
        }

        return $trackedHtml;
    }

    /**
     * High-speed parallel batch dispatcher using non-blocking curl_multi.
     * Dispatches multiple personalized emails simultaneously in ~1-2 seconds.
     * 
     * @param array $messages List of message items with to_email, to_name, subject, html_content, etc.
     * @param int|null $overrideCredentialId Optional specific mail credential ID
     * @return array Summary with sent, failed, total, duration_ms, and details
     */
    public static function sendBatch(array $messages, ?int $overrideCredentialId = null): array {
        if (empty($messages)) {
            return ['sent' => 0, 'failed' => 0, 'total' => 0, 'duration_ms' => 0, 'details' => []];
        }

        $startTime = microtime(true);
        $cfg = self::getSettings();
        $appUrl = rtrim(Config::get('app_url') ?: 'http://localhost/techscout.n', '/');

        // Resolve credential profile
        $credProfile = null;
        if (!empty($overrideCredentialId) && $overrideCredentialId > 0) {
            try {
                $credProfile = Database::fetch("SELECT * FROM mail_credentials WHERE id = ? AND status = 'active'", [(int)$overrideCredentialId]);
            } catch (\Throwable $e) {}
        }
        if (!$credProfile) {
            try {
                $credProfile = Database::fetch("SELECT * FROM mail_credentials WHERE is_default = 1 AND status = 'active' LIMIT 1");
            } catch (\Throwable $e) {}
        }
        if (!$credProfile) {
            try {
                $credProfile = Database::fetch("SELECT * FROM mail_credentials WHERE status = 'active' ORDER BY id ASC LIMIT 1");
            } catch (\Throwable $e) {}
        }
        if (!$credProfile) {
            $credProfile = [
                'id' => null,
                'name' => 'Global Platform Config',
                'provider' => $cfg['mail_api_provider'] ?? 'python_api',
                'from_name' => $cfg['mail_from_name'] ?? 'TechScout Intelligence',
                'from_email' => $cfg['mail_from_address'] ?? 'notifications@techscout.online',
                'reply_to_email' => $cfg['mail_from_address'] ?? 'notifications@techscout.online',
                'api_endpoint' => $cfg['mail_api_endpoint'] ?? null,
                'api_key' => $cfg['mail_api_key'] ?? null,
                'payload_format' => 'standard_json'
            ];
        }

        $fromName = $credProfile['from_name'] ?? ($cfg['mail_from_name'] ?? 'TechScout Intelligence');
        $fromEmail = $credProfile['from_email'] ?? ($cfg['mail_from_address'] ?? 'notifications@techscout.online');
        $replyToEmail = $credProfile['reply_to_email'] ?? $fromEmail;

        // Auto-ensure delivery logs table exists
        try {
            Database::execute("CREATE TABLE IF NOT EXISTS newsletter_delivery_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                issue_id BIGINT UNSIGNED NULL,
                campaign_title VARCHAR(255) NOT NULL DEFAULT 'Direct Dispatch',
                subscriber_id BIGINT UNSIGNED NULL,
                to_email VARCHAR(191) NOT NULL,
                to_name VARCHAR(150) NULL,
                tracking_token VARCHAR(64) NOT NULL,
                status ENUM('delivered', 'failed', 'opened', 'clicked') NOT NULL DEFAULT 'delivered',
                error_message TEXT NULL,
                open_count INT UNSIGNED NOT NULL DEFAULT 0,
                opened_at DATETIME NULL,
                click_count INT UNSIGNED NOT NULL DEFAULT 0,
                clicked_at DATETIME NULL,
                last_clicked_url VARCHAR(500) NULL,
                user_ip VARCHAR(45) NULL,
                user_agent VARCHAR(255) NULL,
                dispatched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_token (tracking_token),
                KEY idx_email (to_email),
                KEY idx_issue (issue_id),
                KEY idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Throwable $e) {}

        $isCustomApi = in_array($credProfile['provider'], ['python_api', 'pythonanywhere_api', 'custom_rest', 'resend', 'brevo', 'sendgrid', 'mailgun', 'postmark', 'ses']) || !empty($credProfile['api_endpoint']);

        $results = [];
        $sentCount = 0;
        $failCount = 0;

        // Prepare messages with tracking tokens and pixel injection
        $prepared = [];
        foreach ($messages as $idx => $msg) {
            $toEmail = trim((string)$msg['to_email']);
            $toName = trim((string)($msg['to_name'] ?? ''));
            $subject = (string)$msg['subject'];
            $html = (string)$msg['html_content'];
            $token = !empty($msg['tracking_token']) ? (string)$msg['tracking_token'] : bin2hex(random_bytes(16));
            $issueId = !empty($msg['issue_id']) ? (int)$msg['issue_id'] : null;
            $subId = !empty($msg['subscriber_id']) ? (int)$msg['subscriber_id'] : null;
            $campTitle = !empty($msg['campaign_title']) ? (string)$msg['campaign_title'] : 'Newsletter Broadcast';

            $trackedHtml = self::injectTracking($html, $token, $appUrl);

            $prepared[$idx] = [
                'to_email' => $toEmail,
                'to_name' => $toName,
                'subject' => $subject,
                'html' => $trackedHtml,
                'token' => $token,
                'issue_id' => $issueId,
                'subscriber_id' => $subId,
                'campaign_title' => $campTitle
            ];
        }

        if ($isCustomApi && function_exists('curl_multi_init')) {
            // ================================================================
            // HIGH-SPEED PARALLEL MULTI-CURL DISPATCH
            // ================================================================
            $endpoint = !empty($credProfile['api_endpoint']) ? $credProfile['api_endpoint'] : (!empty($cfg['mail_api_endpoint']) ? $cfg['mail_api_endpoint'] : 'https://api.brevo.com/v3/smtp/email');
            $payloadFormat = $credProfile['payload_format'] ?? 'standard_json';
            $apiKey = $credProfile['api_key'] ?? ($cfg['mail_api_key'] ?? '');

            $mh = curl_multi_init();
            $handles = [];

            foreach ($prepared as $idx => $item) {
                $headers = ['Content-Type: application/json', 'Accept: application/json'];

                if ($payloadFormat === 'brevo_compatible' || $credProfile['provider'] === 'brevo' || strpos($endpoint, 'brevo.com') !== false) {
                    if (empty($credProfile['api_endpoint'])) $endpoint = 'https://api.brevo.com/v3/smtp/email';
                    $headers[] = 'api-key: ' . $apiKey;
                    $payload = [
                        'sender' => ['name' => $fromName, 'email' => $fromEmail],
                        'to' => [['email' => $item['to_email'], 'name' => $item['to_name'] ?: $item['to_email']]],
                        'subject' => $item['subject'],
                        'htmlContent' => $item['html']
                    ];
                    if (!empty($replyToEmail)) $payload['replyTo'] = ['email' => $replyToEmail, 'name' => $fromName];
                } elseif ($payloadFormat === 'resend_compatible' || $credProfile['provider'] === 'resend' || strpos($endpoint, 'resend.com') !== false) {
                    if (empty($credProfile['api_endpoint'])) $endpoint = 'https://api.resend.com/emails';
                    $headers[] = 'Authorization: Bearer ' . $apiKey;
                    $payload = [
                        'from' => $fromName . ' <' . $fromEmail . '>',
                        'to' => [$item['to_email']],
                        'subject' => $item['subject'],
                        'html' => $item['html']
                    ];
                    if (!empty($replyToEmail)) $payload['reply_to'] = $replyToEmail;
                } else {
                    $headers[] = 'Authorization: Bearer ' . $apiKey;
                    $headers[] = 'X-API-Key: ' . $apiKey;
                    $payload = [
                        'to_email' => $item['to_email'],
                        'to_name' => $item['to_name'],
                        'from_email' => $fromEmail,
                        'from_name' => $fromName,
                        'reply_to' => $replyToEmail,
                        'subject' => $item['subject'],
                        'html_content' => $item['html']
                    ];
                }

                $ch = curl_init($endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                curl_multi_add_handle($mh, $ch);
                $handles[$idx] = $ch;
            }

            // Run concurrent execution
            $running = null;
            do {
                $mrc = curl_multi_exec($mh, $running);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

            while ($running && $mrc == CURLM_OK) {
                if (curl_multi_select($mh, 0.5) != -1) {
                    do {
                        $mrc = curl_multi_exec($mh, $running);
                    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                }
            }

            // Harvest results
            foreach ($handles as $idx => $ch) {
                $response = curl_multi_getcontent($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlErr = curl_error($ch);
                $item = $prepared[$idx];

                $delivered = ($httpCode >= 200 && $httpCode < 300);
                $errMsg = $delivered ? null : "HTTP {$httpCode}: " . ($curlErr ?: (string)$response);

                if ($delivered) {
                    $sentCount++;
                } else {
                    $failCount++;
                }

                $logStatus = $delivered ? 'delivered' : 'failed';

                // Record in delivery logs
                try {
                    Database::executeSilent(
                        "INSERT INTO newsletter_delivery_logs 
                        (issue_id, campaign_title, subscriber_id, to_email, to_name, tracking_token, status, error_message, dispatched_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                        [$item['issue_id'], $item['campaign_title'], $item['subscriber_id'], $item['to_email'], $item['to_name'], $item['token'], $logStatus, $errMsg]
                    );
                } catch (\Throwable $e) {}

                // Record in mail_logs
                try {
                    Database::executeSilent("INSERT INTO mail_logs (to_email, to_name, subject, template_key, body_html, driver, status, error_message, created_at) VALUES (?, ?, ?, 'newsletter_broadcast', ?, ?, ?, ?, NOW())", [
                        $item['to_email'], $item['to_name'], $item['subject'], $item['html'], $credProfile['provider'], $delivered ? 'sent' : 'failed', $errMsg
                    ]);
                } catch (\Throwable $e) {}

                $results[] = [
                    'email' => $item['to_email'],
                    'status' => $logStatus,
                    'error' => $errMsg,
                    'tracking_token' => $item['token']
                ];

                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
            }

            curl_multi_close($mh);
        } else {
            // Fallback to sequential dispatch
            foreach ($prepared as $item) {
                $sent = self::send($item['to_email'], $item['to_name'], $item['subject'], $item['html'], 'newsletter_broadcast', [], $fromEmail, $fromName, $overrideCredentialId);
                $logStatus = $sent ? 'delivered' : 'failed';
                $errMsg = $sent ? null : 'Dispatch delivery failed';

                if ($sent) $sentCount++; else $failCount++;

                try {
                    Database::executeSilent(
                        "INSERT INTO newsletter_delivery_logs 
                        (issue_id, campaign_title, subscriber_id, to_email, to_name, tracking_token, status, error_message, dispatched_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                        [$item['issue_id'], $item['campaign_title'], $item['subscriber_id'], $item['to_email'], $item['to_name'], $item['token'], $logStatus, $errMsg]
                    );
                } catch (\Throwable $e) {}

                $results[] = [
                    'email' => $item['to_email'],
                    'status' => $logStatus,
                    'error' => $errMsg,
                    'tracking_token' => $item['token']
                ];
            }
        }

        $elapsedMs = round((microtime(true) - $startTime) * 1000);

        return [
            'sent' => $sentCount,
            'failed' => $failCount,
            'total' => count($messages),
            'duration_ms' => $elapsedMs,
            'details' => $results
        ];
    }
}
