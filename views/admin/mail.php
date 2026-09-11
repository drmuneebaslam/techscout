<?php
  require __DIR__ . '/../layouts/admin_header.php';

  // Fetch complete template dictionary with all 28+ templates, assigned credentials and default bodies
  $allTemplates = \App\Core\Mailer::getTemplates();
  $templateLibrary = [];
  foreach ($allTemplates as $k => $t) {
      $templateLibrary[$k] = [
          'name' => (!empty($t['is_custom']) ? '✨ ' : '') . ($t['name'] ?? ucwords(str_replace('_', ' ', $k))),
          'subject' => $t['subject'] ?? '',
          'description' => $t['description'] ?? '',
          'vars' => $t['vars'] ?? ['name', 'action_url'],
          'html' => $t['html_content'] ?? \App\Core\Mailer::getDefaultTemplateBody($k),
          'assigned_credential_id' => !empty($t['assigned_credential_id']) ? (int)$t['assigned_credential_id'] : null,
          'credential_name' => $t['credential_name'] ?? null,
          'is_custom' => !empty($t['is_custom']),
          'id' => (int)($t['id'] ?? 0)
      ];
  }

  // Master Reference Dictionary of all supported variables
  $availableVariables = [
      'User & Recipient' => [
          '{{name}}' => 'Recipient display name (e.g. Alex Rivera)',
          '{{username}}' => 'Recipient username handle (e.g. alex_dev)',
          '{{email}}' => 'Recipient email address (e.g. alex@example.com)',
          '{{role}}' => 'Platform user role (e.g. Author, Member, Admin)',
          '{{user_id}}' => 'Numeric database user identifier (e.g. 42)'
      ],
      'Navigation & URLs' => [
          '{{action_url}}' => 'Primary destination Call-to-Action button link',
          '{{app_url}}' => 'Base application home URL (e.g. https://techscout.online)',
          '{{login_url}}' => 'Direct sign-in portal link (/login)',
          '{{register_url}}' => 'New user account registration portal link (/register)',
          '{{appeal_url}}' => 'Support / appeal portal link (/support)',
          '{{unsubscribe_url}}' => 'Notification preferences & unsubscribe URL'
      ],
      'Content & Editorial' => [
          '{{headline}}' => 'Main subject headline or announcement title',
          '{{message}}' => 'Dynamic custom body message text / paragraph',
          '{{article_title}}' => 'Title of submitted or published technical article',
          '{{editorial_notes}}' => 'Editorial feedback and reviewer revision request notes',
          '{{commenter_name}}' => 'Name of community member replying in discussions',
          '{{comment_snippet}}' => 'Excerpt of new reply on discussion thread',
          '{{collection_title}}' => 'Title of shared bookmark stack or technical blueprint',
          '{{sender_name}}' => 'Sender name sharing resource stack'
      ],
      'Campaigns & Hackathons' => [
          '{{campaign_title}}' => 'Title of AI Hackathon or Developer Sprint',
          '{{prize_pool}}' => 'Prize money amount available for challenge',
          '{{deadline}}' => 'Challenge submission deadline timestamp'
      ],
      'Creator Rewards & Treasury' => [
          '{{amount}}' => 'Gross transaction or withdrawal amount in USD',
          '{{net_amount}}' => 'Net disbursed or credited amount in USD',
          '{{fee_deducted}}' => 'Deducted platform or banking transaction fee',
          '{{payout_id}}' => 'Sequential payout claim identifier (#1001)',
          '{{ref_no}}' => 'Unique disbursement reference code',
          '{{method_type}}' => 'Payout method (Bank Wire, PayPal, Stripe, Crypto)',
          '{{destination}}' => 'Masked account destination info',
          '{{transaction_ref}}' => 'External bank or blockchain transaction hash',
          '{{tipper_name}}' => 'Community patron awarding a tip',
          '{{month}}' => 'Statement calendar month (e.g. September 2026)',
          '{{total_reads}}' => 'Total read count across creator publications',
          '{{tips_count}}' => 'Total number of patronage tips received',
          '{{milestone_title}}' => 'Creator achievement badge title'
      ],
      'Support & Helpdesk' => [
          '{{ticket_number}}' => 'Unique numeric support ticket reference (e.g. 1042)',
          '{{ticket_id}}' => 'Support ticket ID (alias for ticket_number)',
          '{{ticket_subject}}' => 'Original support ticket inquiry subject',
          '{{reply_message}}' => 'Technical support staff response body',
          '{{inquiry_subject}}' => 'Subject matter of contact desk message'
      ],
      'Security & Verification' => [
          '{{verification_code}}' => 'Email confirmation PIN code',
          '{{reset_code}}' => 'Password reset verification PIN code',
          '{{two_factor_otp}}' => 'Two-Factor Authentication 6-digit PIN',
          '{{expires_in}}' => 'Passcode expiration duration (e.g. 15 minutes)',
          '{{ip_address}}' => 'Client IP address triggered during security event',
          '{{browser}}' => 'Client browser and device user agent string',
          '{{timestamp}}' => 'Precise formatted event timestamp',
          '{{suspension_reason}}' => 'Policy violation explanation for suspension'
      ],
      'Branding & System' => [
          '{{site_name}}' => 'Platform brand name (TechScout)',
          '{{support_email}}' => 'Official support contact email (support@techscout.n)',
          '{{year}}' => 'Current 4-digit calendar year (e.g. ' . date('Y') . ')',
          '{{date}}' => 'Current localized date (e.g. ' . date('F j, Y') . ')'
      ]
  ];
?>

  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">SYSTEM MAIL &amp; API INTEGRATION HUB</span>
        <span style="font-size: 0.8rem; color: #64748b;">Transactional Delivery, Multi-Provider Credentials (Brevo/Resend/SMTP), System Templates &amp; Delivery Logs</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Mailing Studio &amp; System Mail Engine
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/newsletters') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #ec4899, #8b5cf6); font-weight: 700;">
        <i data-lucide="mail"></i> Open Newsletter Center ↗
      </a>
      <a href="<?= url('/admin/system') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="settings"></i> System Settings
      </a>
      <button type="button" onclick="switchMailTab('compose')" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.4);">
        <i data-lucide="send"></i> Compose System Mail ⚡
      </button>
    </div>
  </div>

  <!-- Metric Stat Grid -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Emails Dispatched</div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= number_format($stats['total_sent'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Delivered across platform</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Dispatched Today</div>
      <div class="admin-stat-val" style="color: #10b981;"><?= number_format($stats['today_count'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Active broadcasts</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Registered Members</div>
      <div class="admin-stat-val" style="color: #a855f7;"><?= number_format(count($users ?? [])) ?></div>
      <div style="font-size: 0.8rem; color: #c084fc;">Subscribers in roster</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Active Delivery Driver</div>
      <div class="admin-stat-val" style="color: #ec4899; font-size: 1.45rem; text-transform: uppercase;">
        <?= htmlspecialchars($settings['mail_driver'] ?? 'LOG') ?>
      </div>
      <div style="font-size: 0.8rem; color: #f472b6;">Provider: <?= htmlspecialchars(strtoupper($settings['mail_api_provider'] ?? 'RESEND')) ?></div>
    </div>
  </div>

  <!-- Navigation Tabs -->
  <div style="display: flex; gap: 0.5rem; margin-bottom: 1.75rem; flex-wrap: wrap;">
    <button type="button" onclick="switchMailTab('compose')" class="ts-btn ts-btn-sm ts-btn-primary mail-tab-btn" id="mtab-btn-compose">
      ✉️ System Dispatch &amp; Live Studio
    </button>
    <button type="button" onclick="switchMailTab('credentials')" class="ts-btn ts-btn-sm ts-btn-outline mail-tab-btn" id="mtab-btn-credentials">
      🔑 Mail Credentials &amp; Profiles (<?= count($mailCredentials ?? []) ?>)
    </button>
    <button type="button" onclick="switchMailTab('users')" class="ts-btn ts-btn-sm ts-btn-outline mail-tab-btn" id="mtab-btn-users">
      👥 User &amp; Email Directory (<?= count($users ?? []) ?>)
    </button>
    <button type="button" onclick="switchMailTab('templates')" class="ts-btn ts-btn-sm ts-btn-outline mail-tab-btn" id="mtab-btn-templates">
      📑 HTML Template Studio (<?= count($templateLibrary) ?>)
    </button>
    <button type="button" onclick="switchMailTab('logs')" class="ts-btn ts-btn-sm ts-btn-outline mail-tab-btn" id="mtab-btn-logs">
      📜 Sent Delivery Logs (<?= count($mailLogs ?? []) ?>)
    </button>
    <button type="button" onclick="switchMailTab('config')" class="ts-btn ts-btn-sm ts-btn-outline mail-tab-btn" id="mtab-btn-config">
      ⚙️ API &amp; SMTP Provider Setup
    </button>
    <button type="button" onclick="switchMailTab('triggers')" class="ts-btn ts-btn-sm ts-btn-outline mail-tab-btn" id="mtab-btn-triggers">
      ⚡ Event Triggers &amp; Automations (<?= count($eventTriggers ?? []) ?>)
    </button>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB 1: COMPOSE & SEND NEWSLETTER / EMAIL WITH LIVE PREVIEW & TEMPLATES -->
  <!-- ========================================================================= -->
  <div class="mail-tab-content" id="mail-tab-compose">
    <div class="admin-table-wrap" style="padding: 2rem; border-color: rgba(99,102,241,0.35);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 1rem;">
        <div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="send" style="color: #38bdf8;"></i> Compose &amp; Dispatch Newsletter
          </h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">
            Select any template to instantly inject its HTML into the editor, customize live with instant visual preview, and deliver.
          </p>
        </div>

        <!-- View Mode Switcher -->
        <div style="display: flex; gap: 0.35rem; background: #05070b; padding: 4px; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
          <button type="button" onclick="setViewMode('split')" class="ts-btn ts-btn-sm ts-btn-primary" id="btn-view-split" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">
            🌓 Split View
          </button>
          <button type="button" onclick="setViewMode('editor')" class="ts-btn ts-btn-sm ts-btn-outline" id="btn-view-editor" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">
            📝 Editor Only
          </button>
          <button type="button" onclick="setViewMode('preview')" class="ts-btn ts-btn-sm ts-btn-outline" id="btn-view-preview" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">
            👁️ Preview Only
          </button>
        </div>
      </div>

      <form action="<?= url('/admin/mail/send') ?>" method="POST" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
          
          <!-- Recipient Selector -->
          <div>
            <label class="admin-modal-label">Recipient Target Group</label>
            <select name="recipient_type" id="mail_recipient_type" onchange="toggleRecipientInput(this.value)" class="admin-modal-select">
              <optgroup label="Broad Audience">
                <option value="all_users">All Registered Members (Full Broadcast)</option>
                <option value="subscribers">Newsletter Subscribers (Opted-in List)</option>
                <option value="role">Target By Specific Role (Hierarchy Filter)</option>
              </optgroup>
              <optgroup label="Creators & Authors">
                <option value="all_authors">All Authors (Creators & Contributors)</option>
                <option value="verified_authors">Verified Guild Authors Only (Expert / Pro)</option>
              </optgroup>
              <optgroup label="Staff & Governance">
                <option value="staff">Staff & Administrators (Admins & Moderators)</option>
                <option value="strikes">Users with Active Warning Strikes (Editorial Notice)</option>
                <option value="inactive">Inactive Accounts (Dormant 30+ Days)</option>
              </optgroup>
              <optgroup label="Targeted & Direct">
                <option value="single">Single Registered User</option>
                <option value="selected_users">Selected Roster Users (from Directory Tab)</option>
                <option value="custom_email">Custom Raw Email Addresses (Comma Separated)</option>
              </optgroup>
            </select>
          </div>

          <!-- Single User Selector -->
          <div id="box_single_user" style="display: none;">
            <label class="admin-modal-label">Select Registered User</label>
            <select name="user_id" id="mail_user_id" class="admin-modal-select">
              <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>">@<?= htmlspecialchars($u['username']) ?> &bull; <?= htmlspecialchars($u['email']) ?> (<?= strtoupper($u['role']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Selected Users Container -->
          <div id="box_selected_users" style="display: none;">
            <label class="admin-modal-label">Selected Specific Recipients</label>
            <div style="background: #05070b; border: 1px solid rgba(99,102,241,0.3); border-radius: 8px; padding: 0.5rem 0.75rem; color: #cbd5e1; font-size: 0.85rem; display: flex; align-items: center; justify-content: space-between;">
              <span id="selected_users_display_count">0 users selected</span>
              <button type="button" onclick="switchMailTab('users')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #38bdf8;">Modify Selection ↗</button>
            </div>
            <input type="hidden" name="selected_user_ids" id="selected_user_ids_input" value="">
          </div>

          <!-- Role Selector -->
          <div id="box_role_select" style="display: none;">
            <label class="admin-modal-label">Select User Role</label>
            <select name="target_role" class="admin-modal-select">
              <option value="member">Members (Standard Users)</option>
              <option value="author">Authors (Verified Creators)</option>
              <option value="moderator">Moderators</option>
              <option value="admin">Administrators</option>
              <option value="super_admin">Super Admins</option>
            </select>
          </div>

          <!-- Custom Email Input -->
          <div id="box_custom_email" style="display: none;">
            <label class="admin-modal-label">Enter Recipient Email(s) - One or comma separated</label>
            <textarea name="custom_email" placeholder="dev1@example.com, dev2@example.com&#10;subscriber@scale.ai" rows="2" class="admin-modal-textarea" style="font-family: monospace; font-size: 0.85rem;"></textarea>
          </div>
        </div>

        <!-- Manual Mail Credential & Sender Profile Selector -->
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
          <div>
            <label class="admin-modal-label" style="display: flex; align-items: center; justify-content: space-between;">
              <span>Outbound Mail Account / Credential (Manual Selection)</span>
              <span style="font-size: 0.7rem; color: #38bdf8; text-transform: none; font-weight: normal;">No Auto-Routing</span>
            </label>
            <select name="mail_credential_id" id="mail_credential_id" class="admin-modal-select" style="border-color: rgba(56,189,248,0.4);">
              <option value="">-- Template Assigned / Platform Default Account --</option>
              <?php foreach ($mailCredentials as $mc): ?>
                <option value="<?= $mc['id'] ?>" <?= !empty($mc['is_default']) ? 'selected' : '' ?>>
                  🔑 <?= htmlspecialchars($mc['name']) ?> (<?= htmlspecialchars($mc['from_email']) ?> &bull; <?= strtoupper($mc['provider']) ?>) <?= !empty($mc['is_default']) ? '★ Default' : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">
              Directly chooses which added Gmail SMTP or API account dispatches this mail.
            </div>
          </div>

          <div>
            <label class="admin-modal-label">Sender Identity Override (Optional)</label>
            <select name="sender_profile" id="mail_sender_profile" onchange="toggleCustomSender(this.value)" class="admin-modal-select">
              <option value="auto">Use Selected Account From Address</option>
              <option value="newsletter">📢 Newsletter &amp; Broadcasts (newsletter@techscout.n)</option>
              <option value="editorial">✍️ Editorial Board &amp; Reviews (editorial@techscout.n)</option>
              <option value="support">🎫 Helpdesk &amp; Support (support@techscout.n)</option>
              <option value="security">🔒 Security Perimeter (security@techscout.n)</option>
              <option value="payouts">💸 Treasury &amp; Payouts (payouts@techscout.n)</option>
              <option value="noreply">🤖 System Automated (noreply@techscout.n)</option>
              <option value="custom">⚙️ Custom From Name &amp; Email Override...</option>
            </select>

            <div id="box_custom_sender" style="display: none; margin-top: 0.5rem;">
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <input type="text" name="custom_from_name" placeholder="From Name" class="admin-modal-input" style="font-size: 0.8rem; padding: 0.4rem 0.6rem;">
                <input type="email" name="custom_from_email" placeholder="From Email" class="admin-modal-input" style="font-size: 0.8rem; padding: 0.4rem 0.6rem;">
              </div>
            </div>
          </div>
        </div>

        <!-- Template Selector -->
        <div style="margin-bottom: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <label class="admin-modal-label" style="margin: 0;">Template Preset (Auto-Injects HTML into Editor)</label>
            <span style="font-size: 0.75rem; color: #38bdf8;">Selecting loads template HTML into the send box</span>
          </div>
          <select name="template_key" id="mail_template_key" onchange="loadTemplateToEditor(this.value)" class="admin-modal-select" style="color: #38bdf8; font-weight: 700; font-size: 0.95rem;">
            <?php foreach ($templateLibrary as $tKey => $tVal): ?>
              <option value="<?= htmlspecialchars($tKey) ?>" <?= $tKey === 'community_broadcast' ? 'selected' : '' ?>>
                <?= htmlspecialchars($tVal['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label class="admin-modal-label">Email Subject Line</label>
          <input type="text" name="subject" id="mail_subject_input" oninput="updateLivePreview()" placeholder="e.g. TechScout Weekly Digest: Frontier AI Models &amp; Architecture Benchmarks" class="admin-modal-input" required>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label class="admin-modal-label">Call to Action Button Link URL</label>
          <input type="text" name="action_url" id="mail_action_url_input" oninput="updateLivePreview()" value="<?= url('/blog') ?>" placeholder="<?= url('/blog') ?>" class="admin-modal-input">
        </div>

        <!-- SPLIT WORKSPACE: CODE EDITOR + LIVE EMAIL PREVIEW -->
        <div id="workspace-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
          
          <!-- LEFT COLUMN: HTML / MESSAGE EDITOR -->
          <div id="col-editor">
            <!-- Dynamic Variables & Tags Dropdown Matrix -->
            <details style="margin-bottom: 0.85rem; background: #05070b; border: 1px solid rgba(56,189,248,0.25); border-radius: 8px; padding: 0.65rem 0.85rem;">
              <summary style="cursor: pointer; font-size: 0.82rem; font-weight: 700; color: #38bdf8; display: flex; align-items: center; justify-content: space-between; user-select: none;">
                <span style="display: flex; align-items: center; gap: 0.4rem;">
                  <i data-lucide="code" style="width: 14px; height: 14px;"></i>
                  Dynamic Variables Palette (Click tag to insert into Editor)
                </span>
                <span style="font-size: 0.7rem; color: #64748b;">10 Categories &bull; 60+ Tokens ▾</span>
              </summary>
              <div style="margin-top: 0.75rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.75rem; max-height: 220px; overflow-y: auto;">
                <?php foreach ($availableVariables as $cat => $vars): ?>
                  <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 6px; padding: 0.5rem;">
                    <div style="font-size: 0.7rem; font-weight: 700; color: #38bdf8; text-transform: uppercase; margin-bottom: 0.35rem; letter-spacing: 0.5px;">
                      <?= htmlspecialchars($cat) ?>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                      <?php foreach ($vars as $tag => $desc): ?>
                        <button type="button" onclick="insertTag('<?= $tag ?>')" style="display: flex; justify-content: space-between; align-items: center; text-align: left; background: rgba(56,189,248,0.06); border: 1px solid rgba(56,189,248,0.15); padding: 0.2rem 0.4rem; border-radius: 4px; color: #cbd5e1; font-size: 0.7rem; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.background='rgba(56,189,248,0.2)'" onmouseout="this.style.background='rgba(56,189,248,0.06)'" title="Insert <?= $tag ?>: <?= htmlspecialchars($desc) ?>">
                          <code style="color: #38bdf8; font-weight: 700;"><?= htmlspecialchars($tag) ?></code>
                          <span style="color: #64748b; font-size: 0.65rem; margin-left: 0.4rem; max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($desc) ?></span>
                        </button>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </details>

            <!-- Dynamic Variable Value Overrides Panel -->
            <div style="margin-bottom: 0.85rem; background: #05070b; border: 1px solid rgba(56,189,248,0.25); border-radius: 8px; padding: 0.75rem 0.85rem;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                <span style="font-size: 0.82rem; font-weight: 700; color: #38bdf8; display: flex; align-items: center; gap: 0.4rem;">
                  <i data-lucide="sliders" style="width: 14px; height: 14px;"></i> Custom Variable Value Overrides
                </span>
                <button type="button" onclick="addVariableOverrideRow('', '')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                  + Add Override
                </button>
              </div>
              <div style="font-size: 0.72rem; color: #94a3b8; margin-bottom: 0.5rem;">
                Assign custom values to any dynamic tags (e.g. <code>{{headline}}</code>, <code>{{amount}}</code>, or any custom tag). Overrides apply to both dispatched emails and live preview.
              </div>
              <div id="var_overrides_container" style="display: flex; flex-direction: column; gap: 0.45rem;">
                <!-- Injected via JavaScript -->
              </div>
            </div>

            <!-- Snippet Inserter Toolbar -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem; flex-wrap: wrap; gap: 0.5rem;">
              <label class="admin-modal-label" style="margin: 0;">HTML Body Editor</label>
              <div style="display: flex; gap: 0.3rem; flex-wrap: wrap;">
                <button type="button" onclick="insertTag('{{name}}')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; font-family: monospace;" title="Insert Recipient Name">+ {{name}}</button>
                <button type="button" onclick="insertTag('{{username}}')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; font-family: monospace;" title="Insert Recipient Username">+ {{username}}</button>
                <button type="button" onclick="insertTag('{{action_url}}')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; font-family: monospace;" title="Insert CTA URL">+ {{action_url}}</button>
                <button type="button" onclick="insertSnippet('button')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #38bdf8;" title="Insert Gradient Button">+ Button</button>
                <button type="button" onclick="insertSnippet('card')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #f59e0b;" title="Insert Highlight Card">+ Card</button>
                <button type="button" onclick="insertSnippet('code')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #34d399;" title="Insert Code Block">+ Code</button>
              </div>
            </div>

            <textarea name="body_html" id="mail_body_textarea" oninput="updateLivePreview()" rows="14" placeholder="Enter HTML content or customized text..." class="admin-modal-textarea" style="font-family: var(--admin-font-mono); font-size: 0.85rem; height: 380px; line-height: 1.5;"></textarea>
          </div>

          <!-- RIGHT COLUMN: LIVE RENDERED EMAIL PREVIEW -->
          <div id="col-preview">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
              <label class="admin-modal-label" style="margin: 0; color: #38bdf8; display: flex; align-items: center; gap: 0.35rem;">
                <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Live Delivery Simulation
              </label>
              <span style="font-size: 0.72rem; color: #64748b;">Simulated Recipient: Alex Rivera</span>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; height: 420px; display: flex; flex-direction: column; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
              <!-- Simulated Email Client Header -->
              <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 0.6rem 0.85rem; font-size: 0.78rem;">
                <div style="color: #0f172a; font-weight: 700;" id="simSubject">Subject: TechScout Intelligence Update</div>
                <div style="color: #64748b; font-size: 0.7rem; margin-top: 0.15rem;">
                  From: <strong style="color: #334155;">TechScout Intelligence</strong> &lt;notifications@techscout.online&gt; &bull; To: alex@example.com
                </div>
              </div>

              <!-- Rendered Iframe (Pure Light Mode Canvas with Grid Lines) -->
              <iframe id="livePreviewFrame" style="width: 100%; height: 100%; border: none; background: #ffffff; flex: 1;"></iframe>
            </div>
          </div>
        </div>

        <!-- ATTACHMENTS SYSTEM (FILES & LINKS) -->
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(56,189,248,0.3); border-radius: 10px; padding: 1.5rem; margin-bottom: 1.75rem;">
          <h4 style="font-size: 1rem; color: #38bdf8; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="paperclip"></i> Email Attachments Engine (Files &amp; Links)
          </h4>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <!-- File Uploads -->
            <div>
              <label class="admin-modal-label">📎 Upload Document / Image Attachments</label>
              <input type="file" name="attachment_files[]" multiple class="admin-modal-input" style="padding: 0.5rem;">
              <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.35rem;">Attach PDF whitepapers, diagrams, ZIP source archives, or guides.</div>
            </div>

            <!-- Link Attachments -->
            <div>
              <label class="admin-modal-label">🔗 Attached Resource Links (One per line: Title | URL)</label>
              <textarea name="attachment_links" rows="3" placeholder="API Docs | https://docs.techscout.n&#10;GitHub Repo | https://github.com/techscout" class="admin-modal-textarea" style="font-family: monospace; font-size: 0.82rem;"></textarea>
              <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.35rem;">Format: <code>Link Title | https://example.com</code></div>
            </div>
          </div>
        </div>

        <button type="submit" class="ts-btn ts-btn-primary" style="padding: 0.8rem 2.75rem; font-weight: 700; font-size: 0.95rem; background: linear-gradient(135deg, #6366f1, #38bdf8);">
          <i data-lucide="send"></i> Dispatch Email Broadcast 🚀
        </button>
      </form>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB 2: USER & EMAIL DIRECTORY -->
  <!-- ========================================================================= -->
  <div class="mail-tab-content" id="mail-tab-users" style="display: none;">
    <div class="admin-table-wrap" style="padding: 1.5rem; margin-bottom: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="users" style="color: #38bdf8;"></i> Registered Users &amp; Audience Directory (<?= count($users ?? []) ?>)
          </h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0.2rem 0 0 0;">View verified emails, roles, filter by platform status, and dispatch direct newsletters or batch communications.</p>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
          <!-- Role Filter -->
          <select id="userDirectoryRoleFilter" onchange="filterUserDirectory()" class="admin-modal-select" style="min-width: 140px; padding: 0.4rem 0.75rem; font-size: 0.82rem;">
            <option value="all">All Roles (<?= count($users ?? []) ?>)</option>
            <option value="member">Members</option>
            <option value="author">Authors / Creators</option>
            <option value="moderator">Moderators</option>
            <option value="admin">Administrators</option>
            <option value="super_admin">Super Admins</option>
          </select>

          <!-- Search Filter -->
          <input type="text" id="userDirectorySearch" onkeyup="filterUserDirectory()" placeholder="Filter username or email..." class="admin-modal-input" style="min-width: 220px; padding: 0.4rem 0.8rem;">
        </div>
      </div>

      <!-- Batch Action Toolbar (When Users Selected) -->
      <div id="userDirectoryBatchBar" style="display: none; background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.4); border-radius: 8px; padding: 0.75rem 1.25rem; margin-bottom: 1rem; align-items: center; justify-content: space-between;">
        <div style="color: #cbd5e1; font-size: 0.88rem; font-weight: 600;">
          <span id="batchSelectedCountBadge" style="background: #6366f1; color: #fff; padding: 0.2rem 0.6rem; border-radius: 9999px; margin-right: 0.5rem; font-size: 0.78rem;">0</span>
          recipients currently selected for newsletter dispatch
        </div>
        <div style="display: flex; gap: 0.5rem;">
          <button type="button" onclick="composeToSelectedUsers()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
            ✉️ Compose to Selected Recipients 🚀
          </button>
          <button type="button" onclick="clearSelectedUsers()" class="ts-btn ts-btn-outline ts-btn-sm">
            Clear Selection
          </button>
        </div>
      </div>

      <table class="admin-table" id="usersDirectoryTable">
        <thead>
          <tr>
            <th style="width: 40px;">
              <input type="checkbox" id="select_all_dir_users" onchange="toggleSelectAllUsers(this.checked)" style="accent-color: #6366f1; width: 16px; height: 16px; cursor: pointer;">
            </th>
            <th>User</th>
            <th>Email Address</th>
            <th>Platform Role</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($users)): ?>
            <?php foreach ($users as $u): ?>
              <tr data-role="<?= strtolower(htmlspecialchars($u['role'])) ?>">
                <td>
                  <input type="checkbox" class="dir-user-chk" value="<?= $u['id'] ?>" data-email="<?= htmlspecialchars($u['email']) ?>" data-username="<?= htmlspecialchars($u['username']) ?>" onchange="updateSelectedUserCount()" style="accent-color: #6366f1; width: 16px; height: 16px; cursor: pointer;">
                </td>
                <td>
                  <strong style="color: #f8fafc; font-size: 0.9rem;">@<?= htmlspecialchars($u['username']) ?></strong>
                  <div style="font-size: 0.72rem; color: #64748b;">User ID: #<?= $u['id'] ?></div>
                </td>
                <td>
                  <span style="color: #38bdf8; font-family: monospace; font-size: 0.85rem;"><?= htmlspecialchars($u['email']) ?></span>
                </td>
                <td>
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; text-transform: uppercase;">
                    <?= htmlspecialchars($u['role']) ?>
                  </span>
                </td>
                <td>
                  <span class="ts-badge ts-badge-green" style="font-size: 0.7rem;">ACTIVE</span>
                </td>
                <td>
                  <button type="button" onclick="composeToSingleUser(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['email'])) ?>', '<?= htmlspecialchars(addslashes($u['username'])) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.72rem;">
                    ✉️ Compose Email
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB: MAIL PROVIDER CREDENTIALS & MULTI-API PROFILES (CRUD) -->
  <!-- ========================================================================= -->
  <div class="mail-tab-content" id="mail-tab-credentials" style="display: none;">
    <div class="admin-table-wrap">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="key" style="color: #38bdf8;"></i> Outbound Mail Credentials &amp; Custom Python APIs
          </h3>
          <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.25rem 0 0 0;">
            Configure your standard SMTP servers (Gmail, Mailtrap, Hostinger) or connect your <strong>Custom Python Microservice / PythonAnywhere API</strong> with full endpoint links, API keys, and custom headers.
          </p>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <button type="button" onclick="openAddCredentialModal('python_api')" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #38bdf8, #6366f1); font-weight: 700;">
            <i data-lucide="terminal"></i> 🐍 Add Python API / REST Endpoint +
          </button>
          <button type="button" onclick="openAddCredentialModal('smtp')" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.4); font-weight: 700;">
            <i data-lucide="server"></i> Add Standard SMTP +
          </button>
        </div>
      </div>

      <!-- MANUAL ASSIGNMENT ARCHITECTURE & GMAIL SMTP + IMPROVMX INTEGRATION -->
      <div style="background: rgba(15,23,42,0.9); border: 1px solid rgba(56,189,248,0.35); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
          <div>
            <h4 style="font-size: 1.15rem; color: #38bdf8; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
              <i data-lucide="shield-check"></i> Manual Credential Assignment &amp; Gmail SMTP + ImprovMX
            </h4>
            <p style="color: #94a3b8; font-size: 0.84rem; margin: 0.35rem 0 0 0; line-height: 1.5;">
              Zero auto-routing heuristics &mdash; you manually assign each template or broadcast to your specific mail credentials added below.
              No proxy server is required as Gmail SMTP with App Passwords connects directly over TLS socket.
            </p>
          </div>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem;">MANUAL ASSIGNMENT &bull; NO PROXY REQUIRED</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
          <!-- Card 1: Gmail SMTP App Passwords -->
          <div style="background: #05070b; border: 1px solid rgba(56,189,248,0.2); border-radius: 8px; padding: 1rem;">
            <div style="color: #38bdf8; font-weight: 700; font-size: 0.88rem; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
              <span>🔑</span> 1. Gmail SMTP with App Passwords
            </div>
            <div style="font-size: 0.78rem; color: #94a3b8; line-height: 1.5;">
              Enable 2-Step Verification on Google &rarr; Generate 16-character App Password &rarr; Add credential with <code style="color:#38bdf8;">smtp.gmail.com:587 (TLS)</code>. Direct socket client authenticates and sends reliably.
            </div>
          </div>

          <!-- Card 2: ImprovMX 25 Mails -->
          <div style="background: #05070b; border: 1px solid rgba(16,185,129,0.2); border-radius: 8px; padding: 1rem;">
            <div style="color: #34d399; font-weight: 700; font-size: 0.88rem; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
              <span>📨</span> 2. ImprovMX Free Custom Domain (25 Mails)
            </div>
            <div style="font-size: 0.78rem; color: #94a3b8; line-height: 1.5;">
              Add your domain to <strong>ImprovMX.com</strong> &rarr; Forward up to 25 custom aliases (e.g. <code style="color:#34d399;">payouts@domain.com</code>) to your Gmail &rarr; Add as "Send mail as" in Gmail settings.
            </div>
          </div>

          <!-- Card 3: Manual Assignment -->
          <div style="background: #05070b; border: 1px solid rgba(168,85,247,0.2); border-radius: 8px; padding: 1rem;">
            <div style="color: #c084fc; font-weight: 700; font-size: 0.88rem; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
              <span>🎯</span> 3. Manual Template &amp; Send Mapping
            </div>
            <div style="font-size: 0.78rem; color: #94a3b8; line-height: 1.5;">
              In the <strong>Templates tab</strong>, map each email template directly to your added credential. In the <strong>Compose tab</strong>, pick any credential from the dropdown to dispatch.
            </div>
          </div>
        </div>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Profile Name &amp; Provider</th>
            <th>Sender Identity</th>
            <th>Connection Coordinates</th>
            <th>Status &amp; Default</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($mailCredentials)): ?>
            <?php foreach ($mailCredentials as $mc): ?>
              <tr>
                <td>
                  <div style="font-weight: 700; color: #f8fafc; font-size: 0.95rem;">
                    <?= htmlspecialchars($mc['name']) ?>
                  </div>
                  <div style="margin-top: 0.25rem; display: flex; align-items: center; gap: 0.4rem;">
                    <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; text-transform: uppercase;">
                      <?= htmlspecialchars($mc['provider']) ?>
                    </span>
                    <?php if ($mc['is_default']): ?>
                      <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">★ SYSTEM DEFAULT</span>
                    <?php endif; ?>
                  </div>
                </td>

                <td>
                  <div style="font-weight: 600; color: #cbd5e1; font-size: 0.88rem;"><?= htmlspecialchars($mc['from_name']) ?></div>
                  <div style="font-size: 0.78rem; color: #38bdf8; font-family: var(--admin-font-mono); margin-top: 0.15rem;">
                    <?= htmlspecialchars($mc['from_email']) ?>
                  </div>
                </td>

                <td style="font-size: 0.82rem; color: #94a3b8; font-family: var(--admin-font-mono);">
                  <?php if ($mc['provider'] === 'smtp'): ?>
                    <div>Host: <strong style="color: #cbd5e1;"><?= htmlspecialchars($mc['host'] ?? '127.0.0.1') ?></strong>:<?= (int)($mc['port'] ?? 587) ?></div>
                    <div style="color: #64748b; font-size: 0.72rem; margin-top: 0.15rem;">TLS/SSL: <?= strtoupper(htmlspecialchars($mc['encryption'] ?? 'TLS')) ?> &bull; User: <?= htmlspecialchars($mc['username'] ?? '—') ?></div>
                  <?php else: ?>
                    <div style="color: #a855f7;">Endpoint: <?= htmlspecialchars($mc['api_endpoint'] ?? 'Cloud API') ?></div>
                    <div style="color: #64748b; font-size: 0.72rem; margin-top: 0.15rem;">Key: <?= !empty($mc['api_key']) ? substr($mc['api_key'], 0, 8) . '••••••••' : 'Configured in env' ?></div>
                  <?php endif; ?>
                </td>

                <td>
                  <span class="ts-badge <?= $mc['status'] === 'active' ? 'ts-badge-green' : 'ts-badge-amber' ?>" style="font-size: 0.72rem;">
                    <?= strtoupper(htmlspecialchars($mc['status'])) ?>
                  </span>
                </td>

                <td>
                  <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                    <button type="button" onclick="openTestCredModal(<?= $mc['id'] ?>, '<?= htmlspecialchars(addslashes($mc['name'])) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; color: #38bdf8;" title="Send Live Verification Ping">
                      ⚡ Test
                    </button>
                    <?php if (!$mc['is_default']): ?>
                      <form action="<?= url('/admin/mail/credentials/set-default/' . $mc['id']) ?>" method="POST" style="display: inline;">
                        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; color: #10b981;" title="Set as Platform Default Provider">
                          ★ Default
                        </button>
                      </form>
                    <?php endif; ?>
                    <button type="button" onclick='openEditCredentialModal(<?= json_encode($mc) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; color: #cbd5e1;">
                      ✏️ Edit
                    </button>
                    <form action="<?= url('/admin/mail/credentials/duplicate/' . $mc['id']) ?>" method="POST" style="display: inline;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; color: #a855f7; border-color: rgba(168,85,247,0.3);" title="Duplicate Profile">
                        📋 Duplicate
                      </button>
                    </form>
                    <form action="<?= url('/admin/mail/credentials/delete/' . $mc['id']) ?>" method="POST" onsubmit="return confirm('Delete mail credential profile \'<?= htmlspecialchars(addslashes($mc['name'])) ?>\'?');" style="display: inline;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #ef4444;" title="Delete Profile">
                        🗑
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align: center; padding: 2.5rem; color: #64748b;">No mail provider credentials saved yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB 3: HTML TEMPLATE STUDIO & CUSTOM TEMPLATE BUILDER -->
  <!-- ========================================================================= -->
  <div class="mail-tab-content" id="mail-tab-templates" style="display: none;">
    <!-- Top Action Header & Live Filters -->
    <div style="background: rgba(15,23,42,0.6); padding: 1.25rem 1.5rem; border-radius: 10px; border: 1px solid rgba(168,85,247,0.25); margin-bottom: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="layout-template" style="color: #c084fc;"></i> HTML Email Template Studio
          </h3>
          <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.25rem 0 0 0;">
            Configure outbound accounts for all <?= count($templateLibrary) ?> system templates without page reloads. Save all mappings together using the button below.
          </p>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
          <a href="<?= url('/admin/mail/template/editor') ?>" target="_blank" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #a855f7, #6366f1); border-color: #a855f7; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
            <i data-lucide="plus-circle"></i> Create Template (New Tab) ➕
          </a>
          <button type="submit" form="batchTemplatesCredentialsForm" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #10b981; border-color: #10b981; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
            <i data-lucide="save"></i> Save Mappings 💾
          </button>
        </div>
      </div>

      <!-- Live Search & Category Filter Bar -->
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; padding-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1);">
        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;" id="tmplCategoryFilters">
          <button type="button" onclick="filterTemplateCategory('all', this)" class="ts-btn ts-btn-sm ts-btn-primary tmpl-cat-btn" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">All (<?= count($templateLibrary) ?>)</button>
          <button type="button" onclick="filterTemplateCategory('auth', this)" class="ts-btn ts-btn-sm ts-btn-outline tmpl-cat-btn" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">Auth &amp; Security</button>
          <button type="button" onclick="filterTemplateCategory('payout', this)" class="ts-btn ts-btn-sm ts-btn-outline tmpl-cat-btn" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">Rewards &amp; Payouts</button>
          <button type="button" onclick="filterTemplateCategory('editorial', this)" class="ts-btn ts-btn-sm ts-btn-outline tmpl-cat-btn" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">Editorial &amp; Community</button>
          <button type="button" onclick="filterTemplateCategory('custom', this)" class="ts-btn ts-btn-sm ts-btn-outline tmpl-cat-btn" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">Custom Only</button>
        </div>

        <div style="min-width: 260px;">
          <input type="text" id="tmplSearchInput" oninput="filterTemplateCardsLive(this.value)" placeholder="Search templates by name, key, subject..." class="admin-modal-input" style="font-size: 0.78rem; padding: 0.35rem 0.75rem; background: #0b0f19;">
        </div>
      </div>
    </div>

    <!-- Batch Form Wrapping All Template Cards -->
    <form action="<?= url('/admin/mail/templates/batch-save-credentials') ?>" method="POST" id="batchTemplatesCredentialsForm" onsubmit="submitBatchTemplateCredentials(event)">
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 320px), 1fr)); gap: 1.25rem;" id="templatesGridContainer">
        <?php foreach ($templateLibrary as $key => $tmpl): ?>
          <div class="ts-card tmpl-grid-card" data-key="<?= htmlspecialchars($key) ?>" data-custom="<?= !empty($tmpl['is_custom']) ? '1' : '0' ?>" data-search="<?= htmlspecialchars(strtolower($key . ' ' . ($tmpl['name'] ?? '') . ' ' . ($tmpl['subject'] ?? '') . ' ' . ($tmpl['description'] ?? ''))) ?>" style="background: rgba(13,19,34,0.85); border: 1px solid rgba(99,102,241,0.25); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                <span class="ts-badge ts-badge-cyan" style="font-family: var(--admin-font-mono); font-size: 0.7rem;"><?= htmlspecialchars($key) ?></span>
                <?php if (!empty($tmpl['is_custom'])): ?>
                  <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">CUSTOM</span>
                <?php else: ?>
                  <span class="ts-badge ts-badge-green" style="font-size: 0.65rem;">CORE SYSTEM</span>
                <?php endif; ?>
              </div>
              <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0 0 0.4rem 0;"><?= htmlspecialchars($tmpl['name']) ?></h4>
              <p style="font-size: 0.85rem; color: #94a3b8; line-height: 1.45; margin: 0 0 0.85rem 0;"><?= htmlspecialchars($tmpl['description'] ?? '') ?></p>
              <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.65rem;">
                <strong>Subject:</strong> <em><?= htmlspecialchars($tmpl['subject']) ?></em>
              </div>

              <!-- Outbound Account Selector (NO automatic page refresh) -->
              <div style="margin-bottom: 0.85rem; padding: 0.5rem 0.65rem; background: rgba(5,7,11,0.8); border: 1px solid rgba(56,189,248,0.2); border-radius: 6px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                  <span style="font-size: 0.72rem; color: #94a3b8; font-weight: 600;">Outbound Account:</span>
                  <span class="tmpl-status-badge-<?= $key ?>" style="font-size: 0.62rem;">
                    <?php if (!empty($tmpl['assigned_credential_id'])): ?>
                      <span class="ts-badge ts-badge-cyan">Manual Mapped</span>
                    <?php else: ?>
                      <span class="ts-badge ts-badge-amber">Platform Default</span>
                    <?php endif; ?>
                  </span>
                </div>
                <select name="assigned_credentials[<?= htmlspecialchars($key) ?>]" onchange="onTemplateCredChanged('<?= $key ?>')" class="admin-modal-select tmpl-cred-select" style="font-size: 0.74rem; padding: 0.25rem 0.45rem; background: #0b0f19;">
                  <option value="">-- Platform Default Provider --</option>
                  <?php foreach ($mailCredentials as $mc): ?>
                    <option value="<?= $mc['id'] ?>" <?= (!empty($tmpl['assigned_credential_id']) && (int)$tmpl['assigned_credential_id'] === (int)$mc['id']) ? 'selected' : '' ?>>
                      🔑 <?= htmlspecialchars($mc['name']) ?> (<?= htmlspecialchars($mc['from_email']) ?> &bull; <?= strtoupper($mc['provider']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div style="border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
              <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                <button type="button" onclick="loadTemplateToEditor('<?= $key ?>'); switchMailTab('compose');" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; background: #6366f1; border-color: #6366f1;">
                  Insert 🚀
                </button>
                <button type="button" onclick="previewTemplateModal('<?= $key ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #38bdf8;" title="Preview Email Rendering">
                  👁️ Preview
                </button>
                <a href="<?= url('/admin/mail/template/editor?key=' . urlencode($key)) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #c084fc; border-color: rgba(192,132,252,0.4);" title="Edit in Full-Page New Tab">
                  ✏️ Edit (New Tab) ↗
                </a>
                <button type="button" onclick="populateTemplateForm('<?= $key ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #94a3b8;" title="Quick Edit Modal">
                  Modal
                </button>
              </div>

              <?php if (!empty($tmpl['is_custom']) && !empty($tmpl['id'])): ?>
                <button type="button" onclick="if(confirm('Permanently delete custom template \'<?= htmlspecialchars(addslashes($tmpl['name'])) ?>\'?')) { document.getElementById('del-tmpl-form-<?= $tmpl['id'] ?>').submit(); }" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #ef4444;" title="Delete Template">
                  🗑️
                </button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Save Button At The End Of The Page (No refresh per click) -->
      <div style="margin-top: 2rem; padding: 1.5rem 2rem; background: rgba(15,23,42,0.9); border: 1px solid rgba(16,185,129,0.35); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <div>
          <div style="font-weight: 700; color: #f8fafc; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="check-circle" style="color: #10b981; width: 20px; height: 20px;"></i> Save All Template Provider Assignments
          </div>
          <div style="font-size: 0.82rem; color: #94a3b8; margin-top: 0.25rem;">
            Commit changes across all <?= count($templateLibrary) ?> templates in a single action without refreshing on every click.
          </div>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
          <span id="tmplUnsavedChangesNotice" style="display: none; font-size: 0.78rem; color: #f59e0b; font-weight: 700; background: rgba(245,158,11,0.12); padding: 0.3rem 0.65rem; border-radius: 6px; border: 1px solid rgba(245,158,11,0.3);">
            ⚠️ Unsaved Changes
          </span>
          <button type="submit" id="btnSaveAllTemplatesBottom" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 800; padding: 0.75rem 2.25rem; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(16,185,129,0.35);">
            <i data-lucide="save"></i> Save All Template Mappings 💾
          </button>
        </div>
      </div>
    </form>

    <!-- Hidden delete forms for custom templates -->
    <?php foreach ($templateLibrary as $tmpl): ?>
      <?php if (!empty($tmpl['is_custom']) && !empty($tmpl['id'])): ?>
        <form id="del-tmpl-form-<?= $tmpl['id'] ?>" action="<?= url('/admin/mail/template/delete/' . $tmpl['id']) ?>" method="POST" style="display: none;"></form>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB 4: SENT DELIVERY LOGS & SENT EMAIL INSPECTOR (MINIMIZED SCANNABLE TABLE) -->
  <!-- ========================================================================= -->
  <div class="mail-tab-content" id="mail-tab-logs" style="display: none;">
    <div class="admin-table-wrap">
      <!-- Minimalist Controls Strip -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0.75rem 1rem; flex-wrap: wrap; gap: 0.75rem; border-bottom: 1px solid rgba(148,163,184,0.1);">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="scroll" style="color: #38bdf8;"></i> Sent Delivery Records
          </h3>
          <p style="color: #64748b; font-size: 0.8rem; margin: 0.15rem 0 0 0;">
            Click <strong>Inspect 👁️</strong> on any record for full sender identity, account routing, error traces, and rendered HTML body.
          </p>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
          <input type="text" id="mailLogSearchInput" oninput="filterMailLogsLive()" placeholder="Search recipient or subject..." class="admin-modal-input" style="font-size: 0.78rem; padding: 0.35rem 0.65rem; width: 220px; background: #0b0f19;">
          
          <select id="mailLogStatusSelect" onchange="filterMailLogsLive()" class="admin-modal-select" style="font-size: 0.78rem; padding: 0.35rem 0.65rem; width: 130px; background: #0b0f19;">
            <option value="all">All (<?= count($mailLogs ?? []) ?>)</option>
            <option value="sent">Sent Only</option>
            <option value="failed">Failed Only</option>
          </select>

          <?php if (!empty($mailLogs)): ?>
            <form action="<?= url('/admin/mail/logs/clear') ?>" method="POST" onsubmit="return confirm('Permanently clear all recorded mail delivery logs?');" style="margin: 0;">
              <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.74rem; color: #ef4444; border-color: rgba(239,68,68,0.3); padding: 0.35rem 0.65rem;" title="Clear all logs">
                🗑️ Clear
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <!-- Minimized, Fast & Compact Table -->
      <table class="admin-table" id="mailLogsTable">
        <thead>
          <tr>
            <th style="width: 110px;">Status</th>
            <th>Recipient</th>
            <th>Subject &amp; Template</th>
            <th style="width: 140px;">Sender / Channel</th>
            <th style="width: 155px;">Dispatched</th>
            <th style="width: 90px; text-align: center;">Action</th>
          </tr>
        </thead>
        <tbody id="mailLogsTableBody">
          <?php if (!empty($mailLogs)): ?>
            <?php foreach ($mailLogs as $log): ?>
              <tr class="mail-log-row" 
                  data-recipient="<?= htmlspecialchars(strtolower($log['to_email'] ?? '')) ?>" 
                  data-subject="<?= htmlspecialchars(strtolower($log['subject'] ?? '')) ?>" 
                  data-status="<?= htmlspecialchars(strtolower($log['status'] ?? 'sent')) ?>"
                  data-opened="<?= !empty($log['open_count']) ? '1' : '0' ?>"
                  data-clicked="<?= !empty($log['click_count']) ? '1' : '0' ?>">
                <td>
                  <div style="display: flex; flex-direction: column; gap: 3px; align-items: flex-start;">
                    <?php if ($log['status'] === 'sent'): ?>
                      <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; padding: 2px 7px;">SENT ✓</span>
                    <?php else: ?>
                      <span class="strike-badge" style="font-size: 0.68rem; padding: 2px 7px;" title="<?= htmlspecialchars($log['error_message'] ?? '') ?>">FAILED ❌</span>
                    <?php endif; ?>
                    <div style="display: flex; gap: 3px; align-items: center; flex-wrap: wrap;">
                      <?php if (!empty($log['open_count'])): ?>
                        <span class="ts-badge" style="background: rgba(168,85,247,0.18); color: #c084fc; font-size: 0.65rem; padding: 1px 5px;" title="Opened <?= (int)$log['open_count'] ?> time(s)">👁️ <?= (int)$log['open_count'] ?></span>
                      <?php endif; ?>
                      <?php if (!empty($log['click_count'])): ?>
                        <span class="ts-badge" style="background: rgba(6,182,212,0.18); color: #22d3ee; font-size: 0.65rem; padding: 1px 5px;" title="Clicked <?= (int)$log['click_count'] ?> time(s)">🔗 <?= (int)$log['click_count'] ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td>
                  <div style="font-weight: 600; color: #f8fafc; font-size: 0.85rem; line-height: 1.3;"><?= htmlspecialchars($log['to_email']) ?></div>
                  <?php if (!empty($log['to_name'])): ?>
                    <div style="font-size: 0.72rem; color: #64748b;"><?= htmlspecialchars($log['to_name']) ?></div>
                  <?php endif; ?>
                </td>
                <td style="max-width: 280px; overflow: hidden; text-overflow: ellipsis;">
                  <div style="font-size: 0.83rem; font-weight: 600; color: #cbd5e1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($log['subject']) ?></div>
                  <div style="font-size: 0.7rem; color: #64748b; font-family: var(--admin-font-mono); margin-top: 1px;">
                    Template: <span style="color: #38bdf8;"><?= htmlspecialchars($log['template_key'] ?? 'custom') ?></span>
                  </div>
                </td>
                <td>
                  <span class="ts-badge" style="background: rgba(99,102,241,0.15); color: #818cf8; font-size: 0.7rem; font-family: var(--admin-font-mono); padding: 2px 7px;">
                    <?= htmlspecialchars($log['credential_name'] ?? strtoupper($log['driver'] ?? 'LOG')) ?>
                  </span>
                </td>
                <td style="font-family: var(--admin-font-mono); font-size: 0.76rem; color: #94a3b8; white-space: nowrap;">
                  <?= \App\Core\DateHelper::formatDateTime($log['created_at']) ?>
                </td>
                <td style="text-align: center; white-space: nowrap;">
                  <div style="display: inline-flex; gap: 0.35rem; align-items: center; justify-content: center;">
                    <button type="button" onclick="inspectSentMail(<?= $log['id'] ?>)" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.74rem; background: #6366f1; border-color: #6366f1;" title="Inspect Mail">
                      Inspect 👁️
                    </button>
                    <form action="<?= url('/admin/mail/log/delete/' . $log['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this mail delivery log record?');" style="display: inline; margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.74rem; color: #ef4444; border-color: rgba(239,68,68,0.3);" title="Delete Log Record">
                        🗑️
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">No email delivery logs recorded yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB 5: API & SMTP PROVIDER CONFIGURATION -->
  <!-- ========================================================================= -->
  <div class="mail-tab-content" id="mail-tab-config" style="display: none;">
    <div class="admin-table-wrap" style="padding: 2rem; border-color: rgba(99,102,241,0.3); margin-bottom: 2rem;">
      <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="settings" style="color: #a855f7;"></i> Mail Provider API &amp; SMTP Configuration
      </h3>

      <form action="<?= url('/admin/mail/settings') ?>" method="POST">
        <!-- Master Enable Toggle -->
        <div style="background: rgba(15,23,42,0.7); border: 1px solid rgba(99,102,241,0.25); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <label style="display: flex; align-items: center; gap: 0.75rem; color: #f8fafc; font-size: 1rem; cursor: pointer;">
            <input type="checkbox" name="mail_enabled" value="1" <?= (!empty($settings['mail_enabled'])) ? 'checked' : '' ?> style="width: 20px; height: 20px; accent-color: #10b981;">
            <div>
              <strong>Enable Live Outbound Mail Dispatch</strong>
              <div style="font-size: 0.8rem; color: #94a3b8;">If unchecked, emails are safely logged to the database table without sending network packets.</div>
            </div>
          </label>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
          <div>
            <label class="admin-modal-label">Primary Mail Driver</label>
            <select name="mail_driver" class="admin-modal-select">
              <option value="api" <?= ($settings['mail_driver'] ?? '') === 'api' ? 'selected' : '' ?>>RESTful API (Resend / SendGrid / Webhook) - Recommended</option>
              <option value="smtp" <?= ($settings['mail_driver'] ?? '') === 'smtp' ? 'selected' : '' ?>>Standard SMTP Relay (Gmail, Postmark, AWS SES)</option>
              <option value="log" <?= ($settings['mail_driver'] ?? 'log') === 'log' ? 'selected' : '' ?>>Local Database Log (Simulated / Dev)</option>
            </select>
          </div>

          <div>
            <label class="admin-modal-label">API Provider</label>
            <select name="mail_api_provider" class="admin-modal-select">
              <option value="custom_pythonanywhere" <?= ($settings['mail_api_provider'] ?? '') === 'custom_pythonanywhere' ? 'selected' : '' ?>>🐍 Custom PythonAnywhere Email API (Flask Microservice)</option>
              <option value="resend" <?= ($settings['mail_api_provider'] ?? 'resend') === 'resend' ? 'selected' : '' ?>>Resend (api.resend.com)</option>
              <option value="sendgrid" <?= ($settings['mail_api_provider'] ?? '') === 'sendgrid' ? 'selected' : '' ?>>SendGrid API v3</option>
              <option value="custom_api" <?= ($settings['mail_api_provider'] ?? '') === 'custom_api' ? 'selected' : '' ?>>Custom JSON Webhook Endpoint</option>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
          <div>
            <label class="admin-modal-label">Sender From Email Address</label>
            <input type="email" name="mail_from_address" value="<?= htmlspecialchars($settings['mail_from_address'] ?? 'notifications@techscout.online') ?>" placeholder="notifications@techscout.online" class="admin-modal-input" required>
          </div>

          <div>
            <label class="admin-modal-label">Sender Display Name</label>
            <input type="text" name="mail_from_name" value="<?= htmlspecialchars($settings['mail_from_name'] ?? 'TechScout Intelligence') ?>" placeholder="TechScout Intelligence" class="admin-modal-input" required>
          </div>
        </div>

        <!-- API Provider Credentials -->
        <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(56,189,248,0.25); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
          <h4 style="color: #38bdf8; margin: 0 0 1rem 0; font-size: 1rem;">Provider API Keys (Resend / SendGrid / Webhook)</h4>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div>
              <label class="admin-modal-label">API Key / Bearer Token</label>
              <input type="password" name="mail_api_key" value="<?= htmlspecialchars($settings['mail_api_key'] ?? '') ?>" placeholder="re_xxxxxxxxxxxxxx or SG.xxxxxx" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
            </div>
            <div>
              <label class="admin-modal-label">API Endpoint URL</label>
              <input type="url" name="mail_api_endpoint" value="<?= htmlspecialchars($settings['mail_api_endpoint'] ?? 'https://api.resend.com/emails') ?>" placeholder="https://api.resend.com/emails" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
            </div>
          </div>
        </div>

        <!-- Direct SMTP Settings (Gmail / Custom SMTP) -->
        <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(16,185,129,0.25); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
              <h4 style="color: #34d399; margin: 0; font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="server"></i> ✉️ Global Gmail / SMTP Relay Parameters
              </h4>
              <p style="color: #94a3b8; font-size: 0.82rem; margin: 0.25rem 0 0 0;">
                Used as the global fallback SMTP server if no template-specific credential profile is selected. For Gmail, use host <code>smtp.gmail.com</code>, port <code>587</code>, TLS encryption, and a 16-character Google App Password.
              </p>
            </div>
            <span class="ts-badge ts-badge-green" style="font-size: 0.7rem;">DIRECT TLS SOCKET CLIENT</span>
          </div>

          <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">SMTP Host</label>
              <input type="text" name="mail_smtp_host" value="<?= htmlspecialchars($settings['mail_smtp_host'] ?? 'smtp.gmail.com') ?>" placeholder="smtp.gmail.com" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
            </div>
            <div>
              <label class="admin-modal-label">SMTP Port</label>
              <input type="number" name="mail_smtp_port" value="<?= htmlspecialchars($settings['mail_smtp_port'] ?? '587') ?>" placeholder="587" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Encryption</label>
              <select name="mail_smtp_encryption" class="admin-modal-select">
                <option value="tls" <?= ($settings['mail_smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                <option value="ssl" <?= ($settings['mail_smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                <option value="none" <?= ($settings['mail_smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>None / Plain</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div>
              <label class="admin-modal-label">SMTP Username (Gmail Address)</label>
              <input type="text" name="mail_smtp_username" value="<?= htmlspecialchars($settings['mail_smtp_username'] ?? '') ?>" placeholder="yourname@gmail.com" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
            </div>
            <div>
              <label class="admin-modal-label">SMTP Password (16-character Google App Password)</label>
              <input type="password" name="mail_smtp_password" value="<?= htmlspecialchars($settings['mail_smtp_password'] ?? '') ?>" placeholder="abcd efgh ijkl mnop" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
              <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.25rem;">Generated from Google Account &rarr; Security &rarr; 2-Step Verification &rarr; App Passwords.</div>
            </div>
          </div>
        </div>

        <button type="submit" class="ts-btn ts-btn-primary" style="padding: 0.75rem 2rem; font-weight: 700;">
          <i data-lucide="save"></i> Save Mail Settings 🚀
        </button>
      </form>
    </div>

    <!-- Live Test Email Box -->
    <div class="admin-table-wrap" style="padding: 2rem; border-color: rgba(52,211,153,0.3);">
      <h3 style="font-size: 1.15rem; color: #34d399; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="check-circle"></i> Send Instant Verification Test Email
      </h3>
      <form action="<?= url('/admin/mail/test') ?>" method="POST" style="display: flex; gap: 0.75rem; max-width: 550px;">
        <input type="email" name="test_email" placeholder="your_email@example.com" class="admin-modal-input" style="flex: 1;" required>
        <button type="submit" class="ts-btn ts-btn-primary" style="background: #10b981; border-color: #10b981; font-weight: 700; white-space: nowrap;">
          Send Test ⚡
        </button>
      </form>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB 6: EVENT TRIGGERS & AUTOMATION MANAGEMENT -->
  <!-- ========================================================================= -->
  <div class="mail-tab-content" id="mail-tab-triggers" style="display: none;">
    <div class="admin-table-wrap" style="padding: 2rem; border-color: rgba(168,85,247,0.35); margin-bottom: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 1rem;">
        <div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="zap" style="color: #c084fc;"></i> System Event Triggers &amp; Email Automations
          </h3>
          <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.25rem 0 0 0;">
            Map any system event (user signup, payout approval, article submission, etc.) to any custom HTML email template and configure duplicate delivery protection.
          </p>
        </div>

        <div style="display: flex; gap: 0.5rem;">
          <button type="button" onclick="openTestEventTriggerModal('user_registered', 'User Account Registration')" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #a855f7, #6366f1); font-weight: 700;">
            ⚡ Test Fire Event Trigger
          </button>
        </div>
      </div>

      <!-- Table Matrix of Event Triggers -->
      <table class="admin-table">
        <thead>
          <tr>
            <th>System Event &amp; Trigger Key</th>
            <th>Event Description</th>
            <th>Delivery Policy</th>
            <th>Assigned HTML Template</th>
            <th style="text-align: center;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($eventTriggers)): ?>
            <?php foreach ($eventTriggers as $eKey => $eInfo): ?>
              <tr>
                <td>
                  <div style="font-weight: 700; color: #f8fafc; font-size: 0.92rem; display: flex; align-items: center; gap: 0.35rem;">
                    <span>⚡ <?= htmlspecialchars($eInfo['name']) ?></span>
                  </div>
                  <code style="font-size: 0.72rem; color: #c084fc; font-family: monospace; margin-top: 2px; display: inline-block;">
                    <?= htmlspecialchars($eKey) ?>
                  </code>
                </td>

                <td style="font-size: 0.82rem; color: #cbd5e1; max-width: 320px;">
                  <?= htmlspecialchars($eInfo['description']) ?>
                </td>

                <td>
                  <?php if (!empty($eInfo['single_delivery'])): ?>
                    <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;" title="Prevents sending duplicate emails for the same event to the same user">
                      🔒 SINGLE DELIVERY ONLY
                    </span>
                  <?php else: ?>
                    <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">
                      🔄 MULTI-DISPATCH ALLOWED
                    </span>
                  <?php endif; ?>
                </td>

                <td>
                  <select id="event_tmpl_select_<?= htmlspecialchars($eKey) ?>" class="admin-modal-select" style="padding: 0.35rem 0.65rem; font-size: 0.82rem; border-color: rgba(168,85,247,0.4); color: #c084fc; font-weight: 700;">
                    <?php foreach ($templateLibrary as $tKey => $tVal): ?>
                      <option value="<?= htmlspecialchars($tKey) ?>" <?= ($eInfo['mapped_template'] === $tKey) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tVal['name']) ?> (<?= htmlspecialchars($tKey) ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </td>

                <td style="text-align: center;">
                  <div style="display: flex; gap: 0.35rem; justify-content: center;">
                    <button type="button" onclick="saveEventTriggerMapping('<?= htmlspecialchars($eKey) ?>', 'event_tmpl_select_<?= htmlspecialchars($eKey) ?>')" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.72rem; background: #a855f7; border-color: #a855f7; font-weight: 700;">
                      Save 💾
                    </button>
                    <button type="button" onclick="openTestEventTriggerModal('<?= htmlspecialchars($eKey) ?>', '<?= htmlspecialchars(addslashes($eInfo['name'])) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.72rem; color: #38bdf8; border-color: rgba(56,189,248,0.35);" title="Test Fire Trigger">
                      Test ⚡
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Custom Triggers Header & Table -->
      <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(148,163,184,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
          <div>
            <h4 style="font-size: 1.1rem; color: #38bdf8; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
              <i data-lucide="compass"></i> Dynamic Custom &amp; URL Pattern Triggers
            </h4>
            <p style="color: #94a3b8; font-size: 0.82rem; margin: 0.2rem 0 0 0;">
              Configure custom URL pattern triggers (e.g. <code>/admin/revenue/process-payout</code>, <code>/auth/register</code>) or custom event keys directly from the UI without writing PHP code.
            </p>
          </div>
          <button type="button" onclick="openCreateCustomTriggerModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #0284c7, #38bdf8); color: #fff; font-weight: 700;">
            ➕ Add Custom Trigger
          </button>
        </div>

        <table class="admin-table">
          <thead>
            <tr>
              <th>Trigger Name &amp; Type</th>
              <th>Pattern / Event Key</th>
              <th>Assigned Template</th>
              <th>Subject Line Override</th>
              <th>Policy</th>
              <th style="text-align: center;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($customTriggers)): ?>
              <?php foreach ($customTriggers as $ct): ?>
                <tr>
                  <td>
                    <div style="font-weight: 700; color: #f8fafc; font-size: 0.9rem;">
                      <?= htmlspecialchars($ct['name']) ?>
                    </div>
                    <span class="ts-badge <?= ($ct['type'] === 'url_pattern') ? 'ts-badge-cyan' : 'ts-badge-pink' ?>" style="font-size: 0.65rem;">
                      <?= ($ct['type'] === 'url_pattern') ? '🌐 URL PATTERN' : '⚡ CUSTOM EVENT' ?>
                    </span>
                  </td>
                  <td>
                    <code style="font-size: 0.75rem; color: #38bdf8; font-family: monospace;">
                      <?= htmlspecialchars($ct['key_or_pattern']) ?>
                    </code>
                  </td>
                  <td>
                    <span style="font-weight: 700; color: #c084fc; font-size: 0.82rem;">
                      <?= htmlspecialchars($ct['template_key']) ?>
                    </span>
                  </td>
                  <td style="font-size: 0.8rem; color: #94a3b8;">
                    <?= !empty($ct['subject_override']) ? htmlspecialchars($ct['subject_override']) : '<em style="opacity:0.6;">(Default template subject)</em>' ?>
                  </td>
                  <td>
                    <?= !empty($ct['single_delivery']) ? '<span class="ts-badge ts-badge-cyan" style="font-size:0.65rem;">🔒 SINGLE</span>' : '<span class="ts-badge ts-badge-green" style="font-size:0.65rem;">🔄 MULTI</span>' ?>
                  </td>
                  <td style="text-align: center;">
                    <div style="display: flex; gap: 0.35rem; justify-content: center;">
                      <button type="button" onclick="openTestEventTriggerModal('<?= htmlspecialchars(addslashes($ct['key_or_pattern'])) ?>', '<?= htmlspecialchars(addslashes($ct['name'])) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; color: #38bdf8;">Test ⚡</button>
                      <button type="button" onclick="deleteCustomTrigger('<?= htmlspecialchars($ct['id']) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; color: #ef4444; border-color: rgba(239,68,68,0.3);">Delete 🗑️</button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; color: #64748b; padding: 1.5rem;">
                  No custom dynamic triggers created yet. Click "Add Custom Trigger" above to link URL patterns to templates!
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Developer & Admin Architecture Guide Box -->
    <div style="background: rgba(15,23,42,0.9); border: 1px solid rgba(56,189,248,0.35); border-radius: 12px; padding: 1.75rem;">
      <h4 style="font-size: 1.1rem; color: #38bdf8; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="book-open"></i> Developer Guide: How &amp; Where to Create Custom Triggers &amp; Assign Templates
      </h4>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem; font-size: 0.85rem; color: #cbd5e1; line-height: 1.6;">
        <!-- Step 1 -->
        <div style="background: #05070b; border: 1px solid rgba(56,189,248,0.2); border-radius: 8px; padding: 1.1rem;">
          <strong style="color: #38bdf8; display: block; margin-bottom: 0.4rem; font-size: 0.92rem;">
            1. Assigning Templates to Triggers in UI
          </strong>
          Select any system event in the table above and choose your desired HTML email template from the dropdown menu. Click <strong>Save 💾</strong>. The mapping is saved dynamically in the <code>site_settings</code> table under <code>event_mail_map_{event_key}</code>.
        </div>

        <!-- Step 2 -->
        <div style="background: #05070b; border: 1px solid rgba(168,85,247,0.2); border-radius: 8px; padding: 1.1rem;">
          <strong style="color: #c084fc; display: block; margin-bottom: 0.4rem; font-size: 0.92rem;">
            2. Triggering Events in PHP Code
          </strong>
          Call <code>\App\Core\EventTriggerEngine::fire($eventKey, $payload)</code> from any controller, model, or service in the backend:
          <pre style="background: #030712; border: 1px solid rgba(148,163,184,0.15); padding: 0.6rem; border-radius: 6px; font-family: monospace; font-size: 0.72rem; color: #38bdf8; margin-top: 0.4rem; overflow-x: auto;"><code>\App\Core\EventTriggerEngine::fire('payout_approved', [
  'email' => $author['email'],
  'name' => $author['name'],
  'amount' => '$150.00',
  'payout_id' => '1042',
  'transaction_ref' => 'tx_987654321'
]);</code></pre>
        </div>

        <!-- Step 3 -->
        <div style="background: #05070b; border: 1px solid rgba(16,185,129,0.2); border-radius: 8px; padding: 1.1rem;">
          <strong style="color: #34d399; display: block; margin-bottom: 0.4rem; font-size: 0.92rem;">
            3. Registering New Custom Event Keys
          </strong>
          To add a new custom trigger key to the platform, simply add its entry to <code>getSupportedEvents()</code> in <code>app/Core/EventTriggerEngine.php</code> or fire custom keys directly. The engine automatically checks single-delivery protection!
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CREATE & EDIT HTML EMAIL TEMPLATE -->
<!-- ========================================================================= -->
<!-- ========================================================================= -->
<!-- MODAL: CREATE & EDIT HTML EMAIL TEMPLATE (FULL SCREEN 2-COLUMN DISPLAY) -->
<!-- ========================================================================= -->
<div id="templateEditorModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-box modal-purple" style="width: 98vw; max-width: 1550px; height: 94vh; max-height: 94vh; margin: 2vh auto; display: flex; flex-direction: column; background: #0b0f19; border: 1px solid rgba(168,85,247,0.4); box-shadow: 0 20px 60px rgba(0,0,0,0.8);">
    <div class="admin-modal-header" style="padding-bottom: 0.75rem; border-bottom: 1px solid rgba(148,163,184,0.15);">
      <div>
        <h3 class="admin-modal-title" style="color: #c084fc; font-size: 1.25rem;" id="tmplBuilderTitle">
          <i data-lucide="layout-template"></i> Create &amp; Save Custom HTML Email Template
        </h3>
        <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.2rem;">
          Design responsive HTML templates with 10 categories of dynamic tokens &amp; real-time visual preview iframe.
        </div>
      </div>
      
      <div style="display: flex; gap: 0.75rem; align-items: center;">
        <div style="display: flex; gap: 0.25rem; background: #05070b; padding: 4px; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
          <button type="button" onclick="setModalTmplViewMode('split')" class="ts-btn ts-btn-sm ts-btn-primary" id="btn-tmpl-modal-split" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">🌓 Split View</button>
          <button type="button" onclick="setModalTmplViewMode('editor')" class="ts-btn ts-btn-sm ts-btn-outline" id="btn-tmpl-modal-editor" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">📝 Editor Only</button>
          <button type="button" onclick="setModalTmplViewMode('preview')" class="ts-btn ts-btn-sm ts-btn-outline" id="btn-tmpl-modal-preview" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">👁️ Preview Only</button>
        </div>
        <button type="button" onclick="closeTemplateEditorModal()" class="admin-modal-close" title="Close Modal">✕</button>
      </div>
    </div>

    <form action="<?= url('/admin/mail/template/save') ?>" method="POST" id="templateBuilderForm" style="display: flex; flex-direction: column; flex: 1; min-height: 0; overflow-y: auto; padding: 0.75rem;">
      <input type="hidden" name="id" id="tmpl_id_input" value="">

      <!-- Top Metadata Strip -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.85rem; margin-bottom: 0.85rem; background: rgba(15,23,42,0.6); padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1);">
        <div>
          <label class="admin-modal-label">Template Key (Unique Identifier)</label>
          <input type="text" name="template_key" id="tmpl_key_input" placeholder="e.g. weekly_ai_roundup" class="admin-modal-input" style="font-family: monospace;" required>
        </div>
        <div>
          <label class="admin-modal-label">Template Display Name</label>
          <input type="text" name="name" id="tmpl_name_input" placeholder="e.g. ⚡ Weekly AI Digest" class="admin-modal-input" required>
        </div>
        <div>
          <label class="admin-modal-label">Default Subject Line</label>
          <input type="text" name="subject" id="tmpl_subject_input" oninput="updateTmplModalPreview()" placeholder="e.g. TechScout Intelligence Update: {{headline}}" class="admin-modal-input" required>
        </div>
        <div>
          <label class="admin-modal-label">Assigned Mail Credential Profile</label>
          <select name="assigned_credential_id" id="tmpl_cred_id_input" class="admin-modal-select">
            <option value="">-- Default Outbound System Provider --</option>
            <?php foreach ($mailCredentials as $mc): ?>
              <option value="<?= $mc['id'] ?>"><?= htmlspecialchars($mc['name']) ?> (<?= htmlspecialchars(strtoupper($mc['provider'])) ?>) <?= $mc['is_default'] ? '★ [DEFAULT]' : '' ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="grid-column: span 2;">
          <label class="admin-modal-label">Template Description / Notes</label>
          <input type="text" name="description" id="tmpl_desc_input" placeholder="e.g. Broadcast update for new models..." class="admin-modal-input">
        </div>
      </div>

      <!-- SPLIT 2-COLUMN WORKSPACE: LEFT EDITOR & PALETTE + RIGHT LIVE IFRAME PREVIEW -->
      <div id="tmpl-modal-workspace-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; flex: 1; min-height: 0;">
        
        <!-- LEFT COLUMN: ALL 10 CATEGORIES PALETTE, BLOCK COMPONENTS & CODE EDITOR -->
        <div id="tmpl-modal-col-editor" style="display: flex; flex-direction: column; overflow-y: auto; padding-right: 0.25rem;">
          <!-- Collapsible 10 Categories Dynamic Variables Matrix -->
          <details open style="margin-bottom: 0.75rem; background: #05070b; border: 1px solid rgba(168,85,247,0.3); border-radius: 8px; padding: 0.55rem 0.75rem;">
            <summary style="cursor: pointer; font-size: 0.8rem; font-weight: 700; color: #c084fc; display: flex; align-items: center; justify-content: space-between; user-select: none;">
              <span style="display: flex; align-items: center; gap: 0.4rem;">
                <i data-lucide="variable" style="width: 14px; height: 14px;"></i>
                Dynamic Variables Palette (10 Categories &bull; 60+ Tokens)
              </span>
              <span style="font-size: 0.7rem; color: #a855f7;">Click tag to insert ▾</span>
            </summary>
            <div style="margin-top: 0.6rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; max-height: 200px; overflow-y: auto; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.6rem;">
              <?php foreach ($availableVariables as $cat => $vars): ?>
                <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.1); border-radius: 6px; padding: 0.4rem;">
                  <div style="font-size: 0.68rem; font-weight: 700; color: #c084fc; text-transform: uppercase; margin-bottom: 0.3rem; letter-spacing: 0.5px;"><?= htmlspecialchars($cat) ?></div>
                  <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                    <?php foreach ($vars as $tag => $desc): ?>
                      <button type="button" onclick="insertTmplTag('<?= $tag ?>')" style="display: flex; justify-content: space-between; align-items: center; text-align: left; background: rgba(168,85,247,0.06); border: 1px solid rgba(168,85,247,0.2); padding: 0.15rem 0.35rem; border-radius: 4px; color: #cbd5e1; font-size: 0.68rem; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.background='rgba(168,85,247,0.2)'" onmouseout="this.style.background='rgba(168,85,247,0.06)'" title="Insert <?= $tag ?>: <?= htmlspecialchars($desc) ?>">
                        <code style="color: #c084fc; font-weight: 700;"><?= htmlspecialchars($tag) ?></code>
                        <span style="color: #94a3b8; font-size: 0.62rem; margin-left: 0.3rem; max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($desc) ?></span>
                      </button>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </details>

          <!-- Modern Block Component Library -->
          <div style="margin-bottom: 0.75rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.65rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem; flex-wrap: wrap; gap: 0.5rem;">
              <div style="font-size: 0.78rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.35rem;">
                <i data-lucide="blocks" style="width: 14px; height: 14px; color: #6366f1;"></i> 
                <span>Universal Block Component Library</span>
              </div>
              <span style="font-size: 0.68rem; color: #64748b;">1-Click insert responsive components</span>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 0.3rem;">
              <button type="button" onclick="insertBlockComponent('hero')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.7rem; color: #4f46e5; border-color: #c7d2fe; background: #f5f3ff;">✨ Hero Header</button>
              <button type="button" onclick="insertBlockComponent('callout_info')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.7rem; color: #0284c7; border-color: #bae6fd; background: #f0f9ff;">ℹ️ Info Callout</button>
              <button type="button" onclick="insertBlockComponent('callout_success')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.7rem; color: #059669; border-color: #a7f3d0; background: #ecfdf5;">✅ Success Box</button>
              <button type="button" onclick="insertBlockComponent('callout_warning')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.7rem; color: #d97706; border-color: #fde68a; background: #fffbeb;">⚠️ Warning Box</button>
              <button type="button" onclick="insertBlockComponent('cta_primary')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.7rem; color: #4338ca; border-color: #c7d2fe; background: #eef2ff;">🔘 Primary CTA</button>
              <button type="button" onclick="insertBlockComponent('cta_green')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.7rem; color: #047857; border-color: #a7f3d0; background: #ecfdf5;">💰 Disbursal CTA</button>
              <button type="button" onclick="insertBlockComponent('datagrid')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.7rem; color: #334155; border-color: #cbd5e1; background: #f8fafc;">📊 Data Grid</button>
              <button type="button" onclick="insertBlockComponent('otp_box')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.7rem; color: #4f46e5; border-color: #c7d2fe; background: #f8fafc;">🔑 OTP Badge</button>
            </div>
          </div>

          <!-- Visual Editor Mode Switcher & Rich Formatting Toolbar -->
          <div style="background: rgba(15,23,42,0.9); border: 1px solid rgba(168,85,247,0.3); border-radius: 8px 8px 0 0; padding: 0.5rem 0.75rem; display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
              <div style="display: flex; gap: 0.25rem;">
                <button type="button" onclick="switchTmplEditMode('wysiwyg')" id="btn-tmpl-mode-wysiwyg" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; background: #a855f7; border-color: #a855f7;">🎨 Visual WYSIWYG Editor</button>
                <button type="button" onclick="switchTmplEditMode('html')" id="btn-tmpl-mode-html" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.72rem; padding: 0.2rem 0.55rem;">💻 Raw HTML Code</button>
              </div>
              
              <!-- Dynamic Variable Selector Dropdown -->
              <select onchange="if(this.value){ insertTmplTag(this.value); this.value=''; }" class="admin-modal-select" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; color: #c084fc; width: auto; max-width: 240px; font-weight: 700; border-color: rgba(168,85,247,0.4);">
                <option value="">⚡ Insert Dynamic Token... ▾</option>
                <?php foreach ($availableVariables as $vCat => $vList): ?>
                  <optgroup label="<?= htmlspecialchars($vCat) ?>">
                    <?php foreach ($vList as $vTag => $vDesc): ?>
                      <option value="<?= htmlspecialchars($vTag) ?>"><?= htmlspecialchars($vTag) ?> - <?= htmlspecialchars($vDesc) ?></option>
                    <?php endforeach; ?>
                  </optgroup>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- WYSIWYG Formatting Buttons Bar -->
            <div id="tmpl_wysiwyg_toolbar" style="display: flex; flex-wrap: wrap; gap: 0.25rem; align-items: center; background: #05070b; padding: 0.35rem; border-radius: 6px; border: 1px solid rgba(148,163,184,0.15);">
              <button type="button" onclick="execTmplWysiwyg('bold')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.75rem; font-weight: 800;" title="Bold"><b>B</b></button>
              <button type="button" onclick="execTmplWysiwyg('italic')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.75rem; font-style: italic;" title="Italic"><i>I</i></button>
              <button type="button" onclick="execTmplWysiwyg('underline')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.75rem; text-decoration: underline;" title="Underline"><u>U</u></button>
              <button type="button" onclick="execTmplWysiwyg('strikeThrough')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.75rem; text-decoration: line-through;" title="Strikethrough"><s>S</s></button>
              <span style="color: rgba(148,163,184,0.3);">|</span>
              <select onchange="execTmplWysiwyg('formatBlock', this.value); this.value='';" class="admin-modal-select" style="padding: 0.15rem 0.4rem; font-size: 0.72rem; width: auto; background: #0b0f19;">
                <option value="">Header ▾</option>
                <option value="H1">H1 Main Heading</option>
                <option value="H2">H2 Sub Heading</option>
                <option value="H3">H3 Section Title</option>
                <option value="P">Paragraph</option>
                <option value="BLOCKQUOTE">Blockquote</option>
              </select>
              <span style="color: rgba(148,163,184,0.3);">|</span>
              <button type="button" onclick="execTmplWysiwyg('justifyLeft')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem;" title="Align Left">⬅️</button>
              <button type="button" onclick="execTmplWysiwyg('justifyCenter')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem;" title="Align Center">↔️</button>
              <button type="button" onclick="execTmplWysiwyg('justifyRight')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem;" title="Align Right">➡️</button>
              <span style="color: rgba(148,163,184,0.3);">|</span>
              <button type="button" onclick="execTmplWysiwyg('insertUnorderedList')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem;" title="Bullet List">• List</button>
              <button type="button" onclick="execTmplWysiwyg('insertOrderedList')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem;" title="Numbered List">1. List</button>
              <span style="color: rgba(148,163,184,0.3);">|</span>
              <button type="button" onclick="promptTmplLink()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.4rem; font-size: 0.72rem; color: #38bdf8;" title="Insert Link">🔗 Link</button>
              <button type="button" onclick="insertTmplCtaButton()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.4rem; font-size: 0.72rem; color: #a855f7;" title="Insert Primary CTA Button">🔘 Button</button>
              <button type="button" onclick="execTmplWysiwyg('removeFormat')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem; color: #ef4444;" title="Clear Formatting">🧹 Clear</button>
            </div>
          </div>

          <!-- Visual WYSIWYG Editable Canvas -->
          <div id="tmpl_wysiwyg_container" style="background: #ffffff; color: #0f172a; border: 1px solid rgba(168,85,247,0.3); border-top: none; border-radius: 0 0 8px 8px; padding: 1rem; min-height: 280px; max-height: 480px; overflow-y: auto; flex: 1;">
            <div id="tmpl_wysiwyg_editor" contenteditable="true" oninput="syncWysiwygToCode()" style="outline: none; min-height: 260px; font-family: 'Inter', sans-serif; font-size: 0.92rem; line-height: 1.6;">
              <h2 style="color: #0f172a; margin-top: 0;">Hello {{name}}!</h2>
              <p style="color: #475569;">Welcome to TechScout Intelligence. We are thrilled to have you on board.</p>
              <p>{{message}}</p>
            </div>
          </div>

          <!-- HTML Code Editor Textarea (Hidden when in WYSIWYG mode) -->
          <textarea name="html_content" id="tmpl_html_input" oninput="syncCodeToWysiwyg()" rows="14" placeholder="<h2 style='color:#0f172a;'>Hello {{name}}!</h2><p>{{message}}</p>" class="admin-modal-textarea" style="display: none; font-family: var(--admin-font-mono); font-size: 0.84rem; flex: 1; min-height: 280px; resize: vertical; border-radius: 0 0 8px 8px; border-top: none;" required></textarea>
        </div>

        <!-- RIGHT COLUMN: REAL-TIME RENDERED LIVE HTML PREVIEW IFRAME -->
        <div id="tmpl-modal-col-preview" style="display: flex; flex-direction: column;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
            <label class="admin-modal-label" style="margin: 0; color: #38bdf8; display: flex; align-items: center; gap: 0.35rem;">
              <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Live Real-Time Visual Preview Sandbox
            </label>
            <span style="font-size: 0.7rem; color: #64748b;">Simulated Recipient: Alex Rivera</span>
          </div>

          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; flex: 1; min-height: 420px; display: flex; flex-direction: column; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <!-- Simulated Email Header -->
            <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 0.5rem 0.75rem; font-size: 0.76rem;">
              <div style="color: #0f172a; font-weight: 700;" id="tmplModalSimSubject">Subject: TechScout Intelligence Update</div>
              <div style="color: #64748b; font-size: 0.68rem; margin-top: 0.15rem;">
                From: <strong style="color: #334155;">TechScout Intelligence</strong> &lt;notifications@techscout.online&gt; &bull; To: alex@example.com
              </div>
            </div>

            <!-- Frame Canvas (Pure Light Mode Grid Envelope) -->
            <iframe id="tmplModalLivePreviewFrame" style="width: 100%; height: 100%; border: none; background: #ffffff; flex: 1;"></iframe>
          </div>
        </div>
      </div>

      <div class="admin-modal-footer" style="padding-top: 0.75rem; margin-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.15); display: flex; justify-content: flex-end; gap: 0.5rem;">
        <button type="button" onclick="closeTemplateEditorModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" id="tmplSubmitBtn" class="ts-btn ts-btn-primary" style="background: #a855f7; border-color: #a855f7; font-weight: 700; padding: 0.6rem 1.75rem;">
          <i data-lucide="save"></i> Save HTML Template 💾
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: TEST FIRE EVENT TRIGGER -->
<!-- ========================================================================= -->
<div id="testEventTriggerModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-box modal-purple" style="max-width: 520px; width: 95%;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #c084fc;" id="testEventTriggerTitle">⚡ Test Fire Event Trigger</h3>
      <button type="button" onclick="closeTestEventTriggerModal()" class="admin-modal-close">✕</button>
    </div>
    <form id="testEventTriggerForm" onsubmit="submitTestEventTrigger(event)">
      <input type="hidden" id="test_event_key" value="">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Target System Event Key</label>
        <input type="text" id="test_event_key_display" readonly class="admin-modal-input" style="font-family: monospace; color: #c084fc; font-weight: 700; background: #05070b;">
      </div>
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Simulated Recipient Name</label>
        <input type="text" id="test_event_recipient_name" value="Alex Rivera" class="admin-modal-input" required>
      </div>
      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Recipient Email Address <span style="color:#ef4444;">*</span></label>
        <input type="email" id="test_event_recipient_email" placeholder="tester@example.com" class="admin-modal-input" required>
        <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.25rem;">Dispatches transactional email payload directly through configured event trigger mapping.</div>
      </div>
      <div id="test_event_result" style="display: none; margin-bottom: 1.25rem; font-size: 0.85rem; padding: 0.65rem; border-radius: 6px;"></div>
      <div class="admin-modal-footer">
        <button type="button" onclick="closeTestEventTriggerModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" id="btnTestEventSubmit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #a855f7; border-color: #a855f7; font-weight: 700;">
          Fire Event Trigger 🚀
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CREATE CUSTOM AUTOMATION TRIGGER -->
<!-- ========================================================================= -->
<!-- MODAL: CREATE CUSTOM AUTOMATION TRIGGER -->
<!-- ========================================================================= -->
<div id="createCustomTriggerModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-box modal-cyan" style="max-width: 760px; width: 95%;">
    <div class="admin-modal-header" style="border-bottom: 1px solid rgba(56,189,248,0.2); padding-bottom: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.6rem;">
        <span style="font-size: 1.4rem;">⚡</span>
        <div>
          <h3 class="admin-modal-title" style="color: #38bdf8; font-size: 1.25rem;">Automation Trigger Studio</h3>
          <p style="margin: 2px 0 0 0; font-size: 0.8rem; color: #94a3b8;">
            Configure real-time event automations and URL pattern triggers without writing any PHP code.
          </p>
        </div>
      </div>
      <button type="button" onclick="closeCreateCustomTriggerModal()" class="admin-modal-close">✕</button>
    </div>

    <!-- Modal Internal Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-top: 1rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <button type="button" id="cmodal-tab-btn-config" onclick="switchCustomTriggerModalTab('config')" class="ts-btn ts-btn-sm ts-btn-primary" style="font-weight: 700;">
        ⚙️ Trigger Configuration
      </button>
      <button type="button" id="cmodal-tab-btn-tutorial" onclick="switchCustomTriggerModalTab('tutorial')" class="ts-btn ts-btn-sm ts-btn-outline" style="color: #38bdf8; border-color: rgba(56,189,248,0.4); font-weight: 700;">
        📚 Tutorial &amp; Live Samples
      </button>
    </div>

    <!-- TAB 1: TRIGGER CONFIGURATION -->
    <div id="cmodal-tab-config" style="padding-top: 1.25rem;">
      <form id="createCustomTriggerForm" onsubmit="submitCreateCustomTrigger(event)">
        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label">Trigger Name <span style="color:#ef4444;">*</span></label>
          <input type="text" id="ct_name" placeholder="e.g. Instant Payout Disbursement Notification" class="admin-modal-input" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label class="admin-modal-label">Trigger Engine Type</label>
            <select id="ct_type" class="admin-modal-select" onchange="toggleCustomTriggerTypeNotice(this.value)">
              <option value="url_pattern">🌐 Specific URL Path Pattern (No Code)</option>
              <option value="event">⚡ Custom System Event Key</option>
            </select>
          </div>
          <div>
            <label class="admin-modal-label">Pattern or Event Key <span style="color:#ef4444;">*</span></label>
            <input type="text" id="ct_key_or_pattern" placeholder="e.g. /admin/revenue/process-payout" class="admin-modal-input" style="font-family: monospace;" required>
          </div>
        </div>

        <div id="ct_type_notice" style="font-size: 0.78rem; color: #38bdf8; background: rgba(56,189,248,0.08); border: 1px solid rgba(56,189,248,0.25); border-radius: 8px; padding: 0.65rem 0.85rem; margin-bottom: 1.15rem; line-height: 1.5;">
          Matches requested route path (e.g. <code>/admin/revenue/process-payout</code> or <code>process-payout</code>) and automatically dispatches the assigned HTML template with session user context!
        </div>

        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label">Assigned Target HTML Email Template <span style="color:#ef4444;">*</span></label>
          <select id="ct_template_key" class="admin-modal-select" required>
            <?php foreach ($templateLibrary as $tKey => $tVal): ?>
              <option value="<?= htmlspecialchars($tKey) ?>">
                <?= htmlspecialchars($tVal['name']) ?> (<?= htmlspecialchars($tKey) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label">Subject Line Override (Optional)</label>
          <input type="text" id="ct_subject_override" placeholder="Leave blank to use the template default subject" class="admin-modal-input">
        </div>

        <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.85rem; margin-bottom: 1.25rem;">
          <label style="display: flex; align-items: center; gap: 0.65rem; color: #cbd5e1; font-size: 0.84rem; cursor: pointer; user-select: none;">
            <input type="checkbox" id="ct_single_delivery" value="1" style="width: 17px; height: 17px; accent-color: #38bdf8;">
            <span><strong>🔒 Enforce Single Delivery Only</strong> &mdash; Automatically protects against duplicate emails sent to the same recipient for this trigger.</span>
          </label>
        </div>

        <div class="admin-modal-footer" style="padding-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.15);">
          <button type="button" onclick="closeCreateCustomTriggerModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
          <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #0284c7, #38bdf8); color: #fff; font-weight: 700; padding: 0.5rem 1.25rem;">
            Save Automation Trigger 🚀
          </button>
        </div>
      </form>
    </div>

    <!-- TAB 2: TUTORIAL & LIVE SAMPLES -->
    <div id="cmodal-tab-tutorial" style="display: none; padding-top: 1.25rem; max-height: 65vh; overflow-y: auto;">
      <div style="background: rgba(56,189,248,0.06); border: 1px solid rgba(56,189,248,0.2); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
        <h4 style="color: #38bdf8; font-size: 0.95rem; margin: 0 0 0.35rem 0; display: flex; align-items: center; gap: 0.4rem;">
          <span>💡</span> What is a Trigger in TechScout?
        </h4>
        <p style="color: #cbd5e1; font-size: 0.82rem; line-height: 1.6; margin: 0;">
          Triggers allow you to connect any platform action directly to an email template without touching PHP code. When a user hits a specific URL pattern or triggers an event, TechScout automatically prepares the template, fills in user variables, and delivers it through your assigned SMTP or Python API credentials.
        </p>
      </div>

      <!-- Quick Preset Samples -->
      <h5 style="color: #f8fafc; font-size: 0.88rem; margin: 0 0 0.75rem 0; text-transform: uppercase; letter-spacing: 0.05em;">
        ⚡ Ready-to-Use Trigger Samples (Click to Pre-Fill)
      </h5>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem;">
        <!-- Sample 1 -->
        <div style="background: #05070b; border: 1px solid rgba(16,185,129,0.3); border-radius: 8px; padding: 0.85rem; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
              <strong style="color: #34d399; font-size: 0.85rem;">💳 Payment Method Linked</strong>
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.62rem;">EVENT KEY</span>
            </div>
            <p style="font-size: 0.78rem; color: #94a3b8; line-height: 1.45; margin: 0 0 0.65rem 0;">
              Fires when an author connects a Bank, PayPal, SEPA, or USDC crypto wallet.
            </p>
          </div>
          <button type="button" onclick="prefillTriggerSample('payout_method_linked', 'Payment Method Linked Confirmation', 'event', 'payout_method_linked', 'Payment Method Linked to Your Account 💳', true)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #34d399; font-size: 0.72rem; border-color: rgba(16,185,129,0.4);">
            Use This Sample &rarr;
          </button>
        </div>

        <!-- Sample 2 -->
        <div style="background: #05070b; border: 1px solid rgba(56,189,248,0.3); border-radius: 8px; padding: 0.85rem; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
              <strong style="color: #38bdf8; font-size: 0.85rem;">🌐 Payout Processed URL</strong>
              <span class="ts-badge ts-badge-green" style="font-size: 0.62rem;">URL PATTERN</span>
            </div>
            <p style="font-size: 0.78rem; color: #94a3b8; line-height: 1.45; margin: 0 0 0.65rem 0;">
              Listens for admin payout approvals on <code>/admin/revenue/process-payout</code>.
            </p>
          </div>
          <button type="button" onclick="prefillTriggerSample('/admin/revenue/process-payout', 'Disbursement URL Trigger', 'url_pattern', 'payout_approved', 'Your Payout Has Been Processed & Disbursed! 🎉', false)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; font-size: 0.72rem; border-color: rgba(56,189,248,0.4);">
            Use This Sample &rarr;
          </button>
        </div>

        <!-- Sample 3 -->
        <div style="background: #05070b; border: 1px solid rgba(168,85,247,0.3); border-radius: 8px; padding: 0.85rem; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
              <strong style="color: #c084fc; font-size: 0.85rem;">🏆 Hackathon Submission</strong>
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.62rem;">EVENT KEY</span>
            </div>
            <p style="font-size: 0.78rem; color: #94a3b8; line-height: 1.45; margin: 0 0 0.65rem 0;">
              Sends receipt confirmation when a creator submits a project to a live sprint.
            </p>
          </div>
          <button type="button" onclick="prefillTriggerSample('campaign_submission_received', 'Campaign Submission Receipt', 'event', 'campaign_submission_received', 'Project Submitted: We received your challenge entry!', false)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #c084fc; font-size: 0.72rem; border-color: rgba(168,85,247,0.4);">
            Use This Sample &rarr;
          </button>
        </div>

        <!-- Sample 4 -->
        <div style="background: #05070b; border: 1px solid rgba(244,63,94,0.3); border-radius: 8px; padding: 0.85rem; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
              <strong style="color: #fb7185; font-size: 0.85rem;">👤 Creator Profile Updated</strong>
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.62rem;">EVENT KEY</span>
            </div>
            <p style="font-size: 0.78rem; color: #94a3b8; line-height: 1.45; margin: 0 0 0.65rem 0;">
              Sends confirmation whenever an author modifies their bio or social links.
            </p>
          </div>
          <button type="button" onclick="prefillTriggerSample('author_profile_updated', 'Author Profile Sync Notice', 'event', 'author_profile_updated', 'Creator Guild: Your Author Profile Was Updated', false)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #fb7185; font-size: 0.72rem; border-color: rgba(244,63,94,0.4);">
            Use This Sample &rarr;
          </button>
        </div>
      </div>

      <!-- Step-by-Step Tutorial Card -->
      <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1.25rem;">
        <h4 style="color: #f8fafc; font-size: 0.95rem; margin: 0 0 0.75rem 0;">
          📖 Step-by-Step Setup Guide
        </h4>
        <ol style="margin: 0; padding-left: 1.25rem; font-size: 0.82rem; color: #cbd5e1; line-height: 1.75;">
          <li><strong>Step 1: Choose Trigger Type</strong> &mdash; Select <em>URL Path Pattern</em> if you want emails sent when any user or admin visits a URL. Select <em>Event Key</em> to hook into core application lifecycle events.</li>
          <li><strong>Step 2: Enter Pattern or Key</strong> &mdash; Specify the URL fragment (e.g. <code>/contact</code>, <code>/author/save</code>) or the event key name.</li>
          <li><strong>Step 3: Select Template</strong> &mdash; Choose which pre-designed HTML template should be rendered and dispatched.</li>
          <li><strong>Step 4: Configure Subject &amp; Deduplication</strong> &mdash; Optionally override the email subject line and enable single delivery if users should only ever receive this once.</li>
          <li><strong>Step 5: Click Save</strong> &mdash; The engine activates immediately. You can test fire it using the <strong>Test ⚡</strong> button in the table.</li>
        </ol>
      </div>

      <div style="text-align: right; margin-top: 1.25rem;">
        <button type="button" onclick="switchCustomTriggerModalTab('config')" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">
          &larr; Return to Configuration Form
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: SENT EMAIL VIEWER & INSPECTOR -->
<!-- ========================================================================= -->
<!-- ========================================================================= -->
<!-- MODAL: SENT EMAIL EXECUTIVE TELEMETRY HUD & SANDBOX -->
<!-- ========================================================================= -->
<div id="sentMailModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 940px; width: 95%; max-height: 94vh; display: flex; flex-direction: column;">
    <div class="admin-modal-header" style="padding-bottom: 0.65rem;">
      <div style="flex: 1; min-width: 0; margin-right: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
          <span class="ts-badge ts-badge-green" id="inspectStatusBadge" style="font-size: 0.72rem; padding: 2px 8px;">SENT ✓</span>
          <span class="ts-badge ts-badge-cyan" id="inspectTemplateKey" style="font-size: 0.7rem; font-family: var(--admin-font-mono);">Template: custom</span>
          <span class="ts-badge" style="background: rgba(168,85,247,0.2); color: #c084fc; font-size: 0.7rem;" id="inspectDriverBadge">Driver: API</span>
          <span id="inspectOpenBadge" class="ts-badge" style="font-size: 0.7rem;">👁️ UNOPENED</span>
          <span id="inspectClickBadge" class="ts-badge" style="font-size: 0.7rem;">🔗 NO CLICKS</span>
        </div>
        <h3 class="admin-modal-title" style="color: #38bdf8; font-size: 1.15rem; word-break: break-word;" id="inspectSubject">
          Email Subject
        </h3>
      </div>
      <button type="button" onclick="closeInspectModal()" class="admin-modal-close" title="Close Modal">✕</button>
    </div>

    <!-- Tracking Token Bar -->
    <div style="background: rgba(15,23,42,0.85); border: 1px solid rgba(99,102,241,0.25); border-radius: 8px; padding: 0.45rem 0.85rem; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
      <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Tracking Token:</span>
        <code id="inspectTrackingToken" style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #38bdf8; background: rgba(56,189,248,0.1); padding: 2px 6px; border-radius: 4px;">--</code>
        <button type="button" onclick="copyTrackingToken()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #38bdf8;" title="Copy tracking token">
          📋 Copy
        </button>
      </div>
      <div style="font-size: 0.72rem; color: #94a3b8;" id="inspectSummaryCounters">
        Opens: <strong style="color: #c084fc;" id="inspectOpenSummary">0</strong> &bull; Clicks: <strong style="color: #22d3ee;" id="inspectClickSummary">0</strong>
      </div>
    </div>

    <!-- Executive Telemetry Grid (6 Rich Metadata HUD Cards) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.55rem; margin-bottom: 0.75rem;">
      <!-- Card 1: Sender & Channel -->
      <div style="background: rgba(15,23,42,0.7); border: 1px solid rgba(56,189,248,0.2); border-radius: 8px; padding: 0.55rem 0.75rem;">
        <div style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Sent By (Sender)</div>
        <div style="color: #38bdf8; font-weight: 600; font-size: 0.8rem; margin-top: 0.15rem; word-break: break-all;" id="inspectSenderEmail">system</div>
        <div style="font-size: 0.68rem; color: #94a3b8; margin-top: 0.1rem;" id="inspectSenderAccount">Account: Platform Default</div>
      </div>

      <!-- Card 2: Recipient -->
      <div style="background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.55rem 0.75rem;">
        <div style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Delivered To (Recipient)</div>
        <div style="color: #f8fafc; font-weight: 600; font-size: 0.8rem; margin-top: 0.15rem; word-break: break-all;" id="inspectRecipientFull">user@example.com</div>
        <div style="font-size: 0.68rem; color: #64748b; margin-top: 0.1rem;" id="inspectRecipientName">Direct Dispatch</div>
      </div>

      <!-- Card 3: Open Engagement Telemetry -->
      <div style="background: rgba(15,23,42,0.7); border: 1px solid rgba(168,85,247,0.25); border-radius: 8px; padding: 0.55rem 0.75rem;">
        <div style="font-size: 0.65rem; color: #c084fc; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: flex; justify-content: space-between;">
          <span>Open Telemetry</span>
          <span id="inspectOpenCountBadge" style="font-size: 0.68rem; font-weight: 800;">0x</span>
        </div>
        <div style="color: #f8fafc; font-size: 0.76rem; margin-top: 0.15rem;" id="inspectOpenedAtText">Not yet opened</div>
        <div style="font-size: 0.68rem; color: #94a3b8; margin-top: 0.1rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" id="inspectReaderClient">Reader: --</div>
      </div>

      <!-- Card 4: Click Engagement Telemetry -->
      <div style="background: rgba(15,23,42,0.7); border: 1px solid rgba(6,182,212,0.25); border-radius: 8px; padding: 0.55rem 0.75rem;">
        <div style="font-size: 0.65rem; color: #22d3ee; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: flex; justify-content: space-between;">
          <span>Click Telemetry</span>
          <span id="inspectClickCountBadge" style="font-size: 0.68rem; font-weight: 800;">0x</span>
        </div>
        <div style="color: #f8fafc; font-size: 0.76rem; margin-top: 0.15rem;" id="inspectClickedAtText">No clicks recorded</div>
        <div style="font-size: 0.68rem; color: #94a3b8; margin-top: 0.1rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" id="inspectLastUrlText">URL: None</div>
      </div>

      <!-- Card 5: Coordinates -->
      <div style="background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.55rem 0.75rem;">
        <div style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Coordinates</div>
        <div style="color: #cbd5e1; font-size: 0.76rem; margin-top: 0.15rem;" id="inspectLogId">Log #0</div>
        <div style="font-size: 0.68rem; color: #94a3b8; margin-top: 0.1rem;" id="inspectMsgId">Transport: Outbound Relay</div>
      </div>

      <!-- Card 6: Timing -->
      <div style="background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.55rem 0.75rem;">
        <div style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Dispatch Timestamp</div>
        <div style="color: #cbd5e1; font-family: var(--admin-font-mono); font-size: 0.76rem; margin-top: 0.15rem;" id="inspectTimestampExact">--:--:--</div>
        <div style="font-size: 0.68rem; color: #10b981; margin-top: 0.1rem;">✓ Localized Timezone</div>
      </div>
    </div>

    <!-- Error Diagnostics Banner (Conditional) -->
    <div id="inspectErrorAlert" style="display: none; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-left: 4px solid #ef4444; border-radius: 8px; padding: 0.65rem 0.85rem; margin-bottom: 0.65rem; font-size: 0.8rem; color: #fecaca;">
      <strong style="color: #ef4444; display: block; margin-bottom: 0.15rem;">❌ Dispatch Exception / Failure Trace:</strong>
      <div id="inspectErrorText" style="font-family: var(--admin-font-mono); font-size: 0.75rem; word-break: break-all;"></div>
    </div>

    <!-- Attachments Display -->
    <div id="inspectAttachmentsBox" style="display: none; background: #04070e; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.5rem 0.85rem; margin-bottom: 0.65rem; font-size: 0.78rem;">
      <strong style="color: #38bdf8; display: block; margin-bottom: 0.2rem;">📎 Attached Resources:</strong>
      <div id="inspectAttachmentsList"></div>
    </div>

    <!-- Subtab Navigation & Action Toolbar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem; flex-wrap: wrap; gap: 0.4rem;">
      <div style="display: flex; gap: 0.35rem;">
        <button type="button" onclick="switchInspectSubTab('preview')" id="btnInspectSubPreview" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">
          👁️ Email Sandbox (Light Mode)
        </button>
        <button type="button" onclick="switchInspectSubTab('json')" id="btnInspectSubJson" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">
          💻 Technical Payload (JSON)
        </button>
      </div>

      <div style="display: flex; gap: 0.35rem;">
        <button type="button" onclick="copyInspectHtml()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; color: #38bdf8;" title="Copy complete HTML body to clipboard">
          📋 Copy HTML
        </button>
        <button type="button" onclick="recomposeFromInspect()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; color: #a855f7;" title="Open in Compose tab with these fields pre-filled">
          ✉️ Re-Dispatch in Studio
        </button>
        <button type="button" onclick="openInspectFullscreen()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; color: #10b981;" title="View email rendering in new browser window">
          ↗️ Fullscreen
        </button>
      </div>
    </div>

    <!-- Email Sandbox Frame (Pure Light Mode Canvas with Grid Lines) -->
    <div id="inspectPreviewContainer" style="flex: 1; min-height: 380px; background: #ffffff; border: 1px solid rgba(148,163,184,0.25); border-radius: 10px; overflow: hidden; position: relative;">
      <iframe id="inspectFrame" style="width: 100%; height: 100%; min-height: 380px; border: none; background: #ffffff;"></iframe>
    </div>

    <!-- Raw Technical JSON Payload View (Hidden by default) -->
    <div id="inspectJsonContainer" style="display: none; flex: 1; min-height: 380px; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 10px; overflow: auto; padding: 1rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
        <span style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Full Database Record Telemetry:</span>
        <button type="button" onclick="copyInspectJson()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; color: #38bdf8;">📋 Copy JSON</button>
      </div>
      <pre id="inspectRawJson" style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #38bdf8; margin: 0; white-space: pre-wrap; word-break: break-all;"></pre>
    </div>

    <div class="admin-modal-footer" style="margin-top: 0.65rem; padding-top: 0.65rem;">
      <button type="button" onclick="closeInspectModal()" class="ts-btn ts-btn-outline ts-btn-sm">Close Inspector</button>
    </div>
  </div>
</div>

<script>
// JSON Dictionary of all templates (var avoids fatal identifier already declared syntax error on re-navigation)
var MAIL_TEMPLATES = window.MAIL_TEMPLATES = <?= json_encode($templateLibrary, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

var switchMailTab = window.switchMailTab = function switchMailTab(tab) {
  document.querySelectorAll('.mail-tab-btn').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById(`mtab-btn-${tab}`);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.mail-tab-content').forEach(c => c.style.display = 'none');
  const target = document.getElementById(`mail-tab-${tab}`);
  if (target) {
    target.style.display = 'block';
  }

  try {
    const url = new URL(window.location.href);
    url.searchParams.set('tab', tab);
    window.history.replaceState(null, '', url.toString());
  } catch (e) {}

  if (window.lucide) {
    try { lucide.createIcons(); } catch (e) {}
  }
}

function toggleRecipientInput(type) {
  const boxSingle = document.getElementById('box_single_user');
  const boxCustom = document.getElementById('box_custom_email');
  const boxRole = document.getElementById('box_role_select');
  const boxSelected = document.getElementById('box_selected_users');

  if (boxSingle) boxSingle.style.display = (type === 'single') ? 'block' : 'none';
  if (boxCustom) boxCustom.style.display = (type === 'custom_email') ? 'block' : 'none';
  if (boxRole) boxRole.style.display = (type === 'role') ? 'block' : 'none';
  if (boxSelected) boxSelected.style.display = (type === 'selected_users') ? 'block' : 'none';
}

function toggleCustomSender(val) {
  const customBox = document.getElementById('box_custom_sender');
  if (customBox) customBox.style.display = (val === 'custom') ? 'block' : 'none';
}

function loadTemplateToEditor(key) {
  const tmpl = MAIL_TEMPLATES[key];
  if (!tmpl) return;

  const select = document.getElementById('mail_template_key');
  if (select) select.value = key;

  const subjectInput = document.getElementById('mail_subject_input');
  if (subjectInput) {
    subjectInput.value = tmpl.subject || '';
  }

  const textarea = document.getElementById('mail_body_textarea');
  if (textarea) {
    textarea.value = tmpl.html || '';
  }

  // Pre-select template's manually assigned credential if designated by admin
  const credSelect = document.getElementById('mail_credential_id');
  if (credSelect && tmpl.assigned_credential_id) {
    credSelect.value = tmpl.assigned_credential_id;
  }

  updateLivePreview();
}

function insertTag(tag) {
  const textarea = document.getElementById('mail_body_textarea');
  if (!textarea) return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const text = textarea.value;
  textarea.value = text.substring(0, start) + tag + text.substring(end);
  textarea.selectionStart = textarea.selectionEnd = start + tag.length;
  textarea.focus();
  updateLivePreview();
}

function insertSnippet(type) {
  let snippet = '';
  if (type === 'button') {
    snippet = `<div style="text-align:center;margin:24px 0;"><a href="{{action_url}}" style="display:inline-block;background:linear-gradient(135deg,#6366f1,#38bdf8);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;">Action Button &rarr;</a></div>`;
  } else if (type === 'card') {
    snippet = `<div style="background:#0f172a;border:1px solid rgba(99,102,241,0.3);border-radius:8px;padding:16px;margin:20px 0;"><strong style="color:#38bdf8;display:block;margin-bottom:6px;">⚡ Key Highlight:</strong><p style="color:#cbd5e1;font-size:14px;margin:0;">Highlight note content goes here...</p></div>`;
  } else if (type === 'code') {
    snippet = `<pre style="background:#030712;border:1px solid rgba(148,163,184,0.2);padding:14px;border-radius:6px;color:#38bdf8;font-family:monospace;font-size:13px;overflow-x:auto;"><code>// Example code snippet\nconst response = await fetch('/api/v1/scout');</code></pre>`;
  }
  insertTag(snippet);
}

function addVariableOverrideRow(key = '', val = '') {
  const container = document.getElementById('var_overrides_container');
  if (!container) return;
  const rowId = 'var_row_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
  const div = document.createElement('div');
  div.id = rowId;
  div.style.cssText = 'display: grid; grid-template-columns: 140px 1fr 32px; gap: 0.5rem; align-items: center;';
  div.innerHTML = `
    <input type="text" name="var_override_keys[]" value="${key}" placeholder="{{variable}}" class="admin-modal-input var-override-key" style="font-size: 0.76rem; padding: 0.3rem 0.5rem; font-family: monospace;" oninput="updateLivePreview()">
    <input type="text" name="var_override_vals[]" value="${val}" placeholder="Override custom value" class="admin-modal-input var-override-val" style="font-size: 0.76rem; padding: 0.3rem 0.5rem;" oninput="updateLivePreview()">
    <button type="button" onclick="removeVariableOverrideRow('${rowId}')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem; color: #ef4444; border-color: rgba(239,68,68,0.3); justify-content: center;" title="Remove Override">✕</button>
  `;
  container.appendChild(div);
  updateLivePreview();
}

function removeVariableOverrideRow(rowId) {
  const el = document.getElementById(rowId);
  if (el) el.remove();
  updateLivePreview();
}

function updateLivePreview() {
  const textarea = document.getElementById('mail_body_textarea');
  const subjectInput = document.getElementById('mail_subject_input');
  const actionUrlInput = document.getElementById('mail_action_url_input');
  const simSubject = document.getElementById('simSubject');
  const frame = document.getElementById('livePreviewFrame');

  if (!textarea || !frame) return;

  // Collect custom variable overrides
  const keyInputs = document.querySelectorAll('.var-override-key');
  const valInputs = document.querySelectorAll('.var-override-val');
  const overrides = {};
  keyInputs.forEach((ki, idx) => {
    let k = ki.value.trim().replace(/^\{\{|\}\}$/g, '');
    let v = valInputs[idx] ? valInputs[idx].value : '';
    if (k) overrides[k] = v;
  });

  let subject = (subjectInput && subjectInput.value) ? subjectInput.value : 'TechScout Intelligence Update';
  for (const [k, v] of Object.entries(overrides)) {
    const reg = new RegExp('\\{\\{' + k + '\\}\\}', 'g');
    subject = subject.replace(reg, v);
  }

  if (simSubject) simSubject.textContent = 'Subject: ' + subject;

  const actionUrl = (actionUrlInput && actionUrlInput.value) ? actionUrlInput.value : window.location.origin;

  let rawHtml = textarea.value || '';
  let renderedHtml = rawHtml;

  // Apply custom variable overrides first
  for (const [k, v] of Object.entries(overrides)) {
    const reg = new RegExp('\\{\\{' + k + '\\}\\}', 'g');
    renderedHtml = renderedHtml.replace(reg, v);
  }

  // Then interpolate standard simulated tags
  renderedHtml = renderedHtml
    .replace(/\{\{name\}\}/g, 'Alex Rivera')
    .replace(/\{\{username\}\}/g, 'alex_dev')
    .replace(/\{\{action_url\}\}/g, actionUrl)
    .replace(/\{\{headline\}\}/g, subject)
    .replace(/\{\{message\}\}/g, 'Here is the customized communication content previewed live in real-time...');

  // Wrap inside modern TechScout email template shell (Clean White Background + Grid Lines)
  const fullEnvelope = `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body {
  margin: 0;
  padding: 24px 12px;
  background-color: #ffffff;
  background-image: linear-gradient(to right, rgba(99,102,241,0.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.08) 1px, transparent 1px);
  background-size: 32px 32px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  color: #0f172a;
}
.card {
  max-width: 580px;
  margin: 0 auto;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.07);
  overflow: hidden;
}
.head {
  background: #ffffff;
  background-image: linear-gradient(to right, rgba(99,102,241,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.05) 1px, transparent 1px);
  background-size: 24px 24px;
  padding: 22px 28px;
  border-bottom: 2px solid #6366f1;
  text-align: center;
}
.body {
  padding: 28px 26px;
  color: #1e293b;
  font-size: 15px;
  line-height: 1.65;
}
.foot {
  border-top: 1px solid #e2e8f0;
  padding: 16px;
  text-align: center;
  font-size: 12px;
  color: #64748b;
  background: #f8fafc;
}
</style>
</head>
<body>
<div class="card">
  <div class="head">
    <div style="font-size:22px;font-weight:900;color:#0f172a;letter-spacing:-0.5px;">Tech<span style="color:#6366f1;">Scout</span> <span style="font-size:11px;background:#6366f1;color:#ffffff;padding:2px 7px;border-radius:4px;vertical-align:middle;margin-left:4px;letter-spacing:1px;font-weight:700;">DISPATCH</span></div>
    <div style="font-size:11px;color:#64748b;letter-spacing:1px;text-transform:uppercase;font-weight:600;margin-top:4px;">Developer Intelligence Network</div>
  </div>
  <div class="body">
    ${renderedHtml}
  </div>
  <div class="foot">
    &copy; ${new Date().getFullYear()} TechScout &bull; Developer Intelligence Network
  </div>
</div>
</body>
</html>`;

  try {
    const doc = frame.contentWindow.document;
    doc.open();
    doc.write(fullEnvelope);
    doc.close();
  } catch (e) {}
}

function setViewMode(mode) {
  const colEditor = document.getElementById('col-editor');
  const colPreview = document.getElementById('col-preview');
  const grid = document.getElementById('workspace-grid');

  document.getElementById('btn-view-split').className = 'ts-btn ts-btn-sm ts-btn-outline';
  document.getElementById('btn-view-editor').className = 'ts-btn ts-btn-sm ts-btn-outline';
  document.getElementById('btn-view-preview').className = 'ts-btn ts-btn-sm ts-btn-outline';
  document.getElementById(`btn-view-${mode}`).className = 'ts-btn ts-btn-sm ts-btn-primary';

  if (mode === 'split') {
    grid.style.gridTemplateColumns = '1fr 1fr';
    colEditor.style.display = 'block';
    colPreview.style.display = 'block';
  } else if (mode === 'editor') {
    grid.style.gridTemplateColumns = '1fr';
    colEditor.style.display = 'block';
    colPreview.style.display = 'none';
  } else if (mode === 'preview') {
    grid.style.gridTemplateColumns = '1fr';
    colEditor.style.display = 'none';
    colPreview.style.display = 'block';
  }
}

function closeInspectModal() {
  const modal = document.getElementById('sentMailModal');
  if (modal) modal.style.display = 'none';
}

function composeToSingleUser(userId, email, username) {
  switchMailTab('compose');
  const typeSelect = document.getElementById('mail_recipient_type');
  if (typeSelect) {
    typeSelect.value = 'single';
    toggleRecipientInput('single');
  }
  const userSelect = document.getElementById('mail_user_id');
  if (userSelect) userSelect.value = userId;
}

function toggleSelectAllUsers(checked) {
  const chks = document.querySelectorAll('.dir-user-chk');
  chks.forEach(c => {
    const tr = c.closest('tr');
    if (!tr || tr.style.display !== 'none') {
      c.checked = checked;
    }
  });
  updateSelectedUserCount();
}

function updateSelectedUserCount() {
  const chks = document.querySelectorAll('.dir-user-chk:checked');
  const count = chks.length;
  const batchBar = document.getElementById('userDirectoryBatchBar');
  const badge = document.getElementById('batchSelectedCountBadge');
  const displayCount = document.getElementById('selected_users_display_count');
  const hiddenInput = document.getElementById('selected_user_ids_input');

  if (badge) badge.textContent = count;
  if (displayCount) displayCount.textContent = count + ' user' + (count === 1 ? '' : 's') + ' selected';

  const ids = Array.from(chks).map(c => c.value);
  if (hiddenInput) hiddenInput.value = ids.join(',');

  if (batchBar) {
    batchBar.style.display = count > 0 ? 'flex' : 'none';
  }
}

function clearSelectedUsers() {
  const chks = document.querySelectorAll('.dir-user-chk');
  chks.forEach(c => c.checked = false);
  const selectAll = document.getElementById('select_all_dir_users');
  if (selectAll) selectAll.checked = false;
  updateSelectedUserCount();
}

function composeToSelectedUsers() {
  const chks = document.querySelectorAll('.dir-user-chk:checked');
  if (chks.length === 0) {
    alert('Please select at least one user from the directory.');
    return;
  }
  switchMailTab('compose');
  const typeSelect = document.getElementById('mail_recipient_type');
  if (typeSelect) {
    typeSelect.value = 'selected_users';
    toggleRecipientInput('selected_users');
  }
}

function filterUserDirectory() {
  const searchInput = document.getElementById('userDirectorySearch');
  const roleSelect = document.getElementById('userDirectoryRoleFilter');
  const searchFilter = (searchInput ? searchInput.value : '').toLowerCase().trim();
  const roleFilter = roleSelect ? roleSelect.value.toLowerCase() : 'all';
  const table = document.getElementById('usersDirectoryTable');
  if (!table) return;
  const trs = table.getElementsByTagName('tr');

  for (let i = 1; i < trs.length; i++) {
    const tr = trs[i];
    const text = tr.textContent.toLowerCase();
    const role = (tr.getAttribute('data-role') || '').toLowerCase();
    
    const matchesSearch = !searchFilter || text.includes(searchFilter);
    const matchesRole = (roleFilter === 'all') || (role === roleFilter);

    tr.style.display = (matchesSearch && matchesRole) ? '' : 'none';
  }
}

let currentInspectedLog = null;

function inspectSentMail(logId) {
  fetch('<?= url('/admin/mail/log/') ?>' + logId)
    .then(r => r.json())
    .then(data => {
      if (data.success && data.log) {
        const log = data.log;
        currentInspectedLog = log;

        // Subject & badges
        document.getElementById('inspectSubject').textContent = log.subject || '(No Subject)';
        
        const statusBadge = document.getElementById('inspectStatusBadge');
        if (statusBadge) {
          if (log.status === 'sent') {
            statusBadge.className = 'ts-badge ts-badge-green';
            statusBadge.textContent = 'SENT ✓';
          } else {
            statusBadge.className = 'strike-badge';
            statusBadge.textContent = 'FAILED ❌';
          }
        }

        const tmplBadge = document.getElementById('inspectTemplateKey');
        if (tmplBadge) {
          tmplBadge.textContent = 'Template: ' + (log.template_key || 'direct_dispatch');
        }

        const driverBadge = document.getElementById('inspectDriverBadge');
        if (driverBadge) {
          driverBadge.textContent = 'Driver: ' + (log.driver || 'LOG').toUpperCase();
        }

        // Tracking Token & Engagement Badges
        const tokenEl = document.getElementById('inspectTrackingToken');
        if (tokenEl) tokenEl.textContent = log.tracking_token || 'N/A';

        const openBadge = document.getElementById('inspectOpenBadge');
        if (openBadge) {
          if (log.open_count && parseInt(log.open_count) > 0) {
            openBadge.className = 'ts-badge';
            openBadge.style.background = 'rgba(168,85,247,0.25)';
            openBadge.style.color = '#c084fc';
            openBadge.textContent = `👁️ OPENED ${log.open_count}x`;
          } else {
            openBadge.className = 'ts-badge';
            openBadge.style.background = 'rgba(148,163,184,0.15)';
            openBadge.style.color = '#94a3b8';
            openBadge.textContent = '👁️ UNOPENED';
          }
        }

        const clickBadge = document.getElementById('inspectClickBadge');
        if (clickBadge) {
          if (log.click_count && parseInt(log.click_count) > 0) {
            clickBadge.className = 'ts-badge';
            clickBadge.style.background = 'rgba(6,182,212,0.25)';
            clickBadge.style.color = '#22d3ee';
            clickBadge.textContent = `🔗 CLICKED ${log.click_count}x`;
          } else {
            clickBadge.className = 'ts-badge';
            clickBadge.style.background = 'rgba(148,163,184,0.15)';
            clickBadge.style.color = '#94a3b8';
            clickBadge.textContent = '🔗 NO CLICKS';
          }
        }

        const openSum = document.getElementById('inspectOpenSummary');
        if (openSum) openSum.textContent = log.open_count || 0;
        const clickSum = document.getElementById('inspectClickSummary');
        if (clickSum) clickSum.textContent = log.click_count || 0;

        // Open Telemetry Card
        const openCountBadge = document.getElementById('inspectOpenCountBadge');
        if (openCountBadge) openCountBadge.textContent = `${log.open_count || 0}x`;
        const openedAtText = document.getElementById('inspectOpenedAtText');
        if (openedAtText) openedAtText.textContent = log.opened_at ? `First: ${log.opened_at}` : 'Not yet opened';
        const readerClient = document.getElementById('inspectReaderClient');
        if (readerClient) {
          readerClient.textContent = log.user_ip ? `IP: ${log.user_ip} ${log.user_agent ? '• ' + log.user_agent.substring(0, 30) + '...' : ''}` : 'Reader: Pending First Open';
        }

        // Click Telemetry Card
        const clickCountBadge = document.getElementById('inspectClickCountBadge');
        if (clickCountBadge) clickCountBadge.textContent = `${log.click_count || 0}x`;
        const clickedAtText = document.getElementById('inspectClickedAtText');
        if (clickedAtText) clickedAtText.textContent = log.clicked_at ? `First: ${log.clicked_at}` : 'No clicks recorded';
        const lastUrlText = document.getElementById('inspectLastUrlText');
        if (lastUrlText) {
          lastUrlText.innerHTML = log.last_clicked_url ? `<a href="${log.last_clicked_url}" target="_blank" style="color:#22d3ee;text-decoration:underline;">${log.last_clicked_url} ↗</a>` : 'URL: None';
        }

        // Raw JSON Record
        const rawJsonEl = document.getElementById('inspectRawJson');
        if (rawJsonEl) {
          rawJsonEl.textContent = JSON.stringify(log, null, 2);
        }

        // Sender HUD Card
        const senderEmail = document.getElementById('inspectSenderEmail');
        if (senderEmail) {
          senderEmail.textContent = log.from_email || 'System Default';
        }
        const senderAcc = document.getElementById('inspectSenderAccount');
        if (senderAcc) {
          senderAcc.textContent = 'Account: ' + (log.credential_name ? log.credential_name : 'Platform Default Outbound');
        }

        // Recipient HUD Card
        const recipFull = document.getElementById('inspectRecipientFull');
        if (recipFull) {
          recipFull.textContent = log.to_email || 'Unknown';
        }
        const recipName = document.getElementById('inspectRecipientName');
        if (recipName) {
          recipName.textContent = log.to_name ? `Name: ${log.to_name}` : 'Direct Recipient';
        }

        // Coordinates HUD Card
        const logIdEl = document.getElementById('inspectLogId');
        if (logIdEl) {
          logIdEl.textContent = `Log Record #${log.id}`;
        }
        const msgIdEl = document.getElementById('inspectMsgId');
        if (msgIdEl) {
          msgIdEl.textContent = log.message_id ? `MsgID: ${log.message_id}` : 'Transport: Outbound Relay';
        }

        // Timing HUD Card
        const timeEl = document.getElementById('inspectTimestampExact');
        if (timeEl) {
          timeEl.textContent = log.created_at || '--:--:--';
        }

        // Failure Diagnostic Alert
        const errAlert = document.getElementById('inspectErrorAlert');
        const errText = document.getElementById('inspectErrorText');
        if (errAlert && errText) {
          if (log.status !== 'sent' && log.error_message) {
            errAlert.style.display = 'block';
            errText.textContent = log.error_message;
          } else {
            errAlert.style.display = 'none';
            errText.textContent = '';
          }
        }

        // Attachments
        const attBox = document.getElementById('inspectAttachmentsBox');
        const attList = document.getElementById('inspectAttachmentsList');
        if (attBox && attList) {
          if (log.attachments && (log.attachments.links?.length || log.attachments.files?.length)) {
            attBox.style.display = 'block';
            let html = '';
            if (log.attachments.links?.length) {
              html += '<div style="margin-bottom:4px;"><strong>Links:</strong> ';
              log.attachments.links.forEach(l => {
                html += `<a href="${l.url}" target="_blank" style="color:#38bdf8;margin-right:8px;">${l.title} ↗</a> `;
              });
              html += '</div>';
            }
            if (log.attachments.files?.length) {
              html += '<div><strong>Files:</strong> ';
              log.attachments.files.forEach(f => {
                html += `<a href="${f.url}" target="_blank" style="color:#34d399;margin-right:8px;">📄 ${f.name} (${f.size || ''})</a> `;
              });
              html += '</div>';
            }
            attList.innerHTML = html;
          } else {
            attBox.style.display = 'none';
          }
        }

        // Render HTML inside iframe (Pure Light Mode)
        const frame = document.getElementById('inspectFrame');
        if (frame) {
          const doc = frame.contentWindow.document;
          doc.open();
          doc.write(log.body_html || '<div style="color:#64748b;padding:24px;font-family:sans-serif;text-align:center;">No HTML body stored for this log entry.</div>');
          doc.close();
        }

        // Reset to preview subtab
        switchInspectSubTab('preview');

        document.getElementById('sentMailModal').style.display = 'flex';
        if (window.lucide) {
          try { lucide.createIcons(); } catch (e) {}
        }
      }
    })
    .catch(e => {
      alert('Could not retrieve email log details.');
    });
}

function switchInspectSubTab(tab) {
  const btnPrev = document.getElementById('btnInspectSubPreview');
  const btnJson = document.getElementById('btnInspectSubJson');
  const prevBox = document.getElementById('inspectPreviewContainer');
  const jsonBox = document.getElementById('inspectJsonContainer');

  if (tab === 'json') {
    if (btnPrev) { btnPrev.className = 'ts-btn ts-btn-outline ts-btn-sm'; }
    if (btnJson) { btnJson.className = 'ts-btn ts-btn-primary ts-btn-sm'; }
    if (prevBox) prevBox.style.display = 'none';
    if (jsonBox) jsonBox.style.display = 'block';
  } else {
    if (btnPrev) { btnPrev.className = 'ts-btn ts-btn-primary ts-btn-sm'; }
    if (btnJson) { btnJson.className = 'ts-btn ts-btn-outline ts-btn-sm'; }
    if (prevBox) prevBox.style.display = 'block';
    if (jsonBox) jsonBox.style.display = 'none';
  }
}

function copyTrackingToken() {
  if (!currentInspectedLog || !currentInspectedLog.tracking_token) {
    alert('No tracking token recorded for this message.');
    return;
  }
  navigator.clipboard.writeText(currentInspectedLog.tracking_token).then(() => {
    alert('Tracking Token copied to clipboard! 📋');
  });
}

function copyInspectJson() {
  if (!currentInspectedLog) return;
  navigator.clipboard.writeText(JSON.stringify(currentInspectedLog, null, 2)).then(() => {
    alert('Full telemetry log JSON copied to clipboard! 📋');
  });
}

function copyInspectHtml() {
  if (!currentInspectedLog || !currentInspectedLog.body_html) {
    alert('No HTML content available to copy.');
    return;
  }
  navigator.clipboard.writeText(currentInspectedLog.body_html).then(() => {
    alert('Email HTML copied to clipboard! 📋');
  }).catch(() => {
    const ta = document.createElement('textarea');
    ta.value = currentInspectedLog.body_html;
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    alert('Email HTML copied to clipboard! 📋');
  });
}

function recomposeFromInspect() {
  if (!currentInspectedLog) return;
  closeInspectModal();
  switchMailTab('compose');

  const recipientType = document.getElementById('mail_recipient_type');
  const customEmailInput = document.getElementById('mail_custom_email');
  const subjectInput = document.getElementById('mail_subject_input');
  const bodyTextarea = document.getElementById('mail_body_textarea');

  if (recipientType) {
    recipientType.value = 'custom_email';
    toggleRecipientInput('custom_email');
  }
  if (customEmailInput && currentInspectedLog.to_email) {
    customEmailInput.value = currentInspectedLog.to_email;
  }
  if (subjectInput && currentInspectedLog.subject) {
    subjectInput.value = 'Re: ' + currentInspectedLog.subject;
  }
  if (bodyTextarea && currentInspectedLog.body_html) {
    bodyTextarea.value = currentInspectedLog.body_html;
  }
  updateLivePreview();
}

function openInspectFullscreen() {
  if (!currentInspectedLog || !currentInspectedLog.body_html) {
    alert('No HTML content available to view.');
    return;
  }
  const w = window.open('', '_blank');
  if (w) {
    w.document.open();
    w.document.write(currentInspectedLog.body_html);
    w.document.close();
  }
}

// Mail Logs Filtering
function filterMailLogsLive() {
  const q = (document.getElementById('mailLogSearchInput')?.value || '').toLowerCase().trim();
  const status = document.getElementById('mailLogStatusSelect')?.value || 'all';
  const rows = document.querySelectorAll('.mail-log-row');

  rows.forEach(row => {
    const recip = row.getAttribute('data-recipient') || '';
    const subj = row.getAttribute('data-subject') || '';
    const rowStatus = row.getAttribute('data-status') || 'sent';

    const matchesText = !q || recip.includes(q) || subj.includes(q);
    const matchesStatus = (status === 'all') || (rowStatus === status);

    row.style.display = (matchesText && matchesStatus) ? '' : 'none';
  });
}

// Template Studio Filtering & Batch Save
function filterTemplateCardsLive(q) {
  const query = (q || '').toLowerCase().trim();
  const cards = document.querySelectorAll('.tmpl-grid-card');
  cards.forEach(card => {
    const searchData = card.getAttribute('data-search') || '';
    card.style.display = (!query || searchData.includes(query)) ? 'flex' : 'none';
  });
}

function filterTemplateCategory(cat, btn) {
  document.querySelectorAll('.tmpl-cat-btn').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  if (btn) {
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
  }

  const cards = document.querySelectorAll('.tmpl-grid-card');
  cards.forEach(card => {
    const key = card.getAttribute('data-key') || '';
    const isCustom = card.getAttribute('data-custom') === '1';

    let show = false;
    if (cat === 'all') {
      show = true;
    } else if (cat === 'custom') {
      show = isCustom;
    } else if (cat === 'auth') {
      show = key.includes('welcome') || key.includes('password') || key.includes('verify') || key.includes('security') || key.includes('login') || key.includes('alert') || key.includes('strike');
    } else if (cat === 'payout') {
      show = key.includes('payout') || key.includes('reward') || key.includes('bounty') || key.includes('claim');
    } else if (cat === 'editorial') {
      show = key.includes('digest') || key.includes('newsletter') || key.includes('broadcast') || key.includes('article') || key.includes('comment') || key.includes('mention');
    }
    card.style.display = show ? 'flex' : 'none';
  });
}

function onTemplateCredChanged(key) {
  const notice = document.getElementById('tmplUnsavedChangesNotice');
  if (notice) notice.style.display = 'inline-block';
}

function submitBatchTemplateCredentials(event) {
  event.preventDefault();
  const form = document.getElementById('batchTemplatesCredentialsForm');
  if (!form) return;

  const btnTop = document.querySelector('button[form="batchTemplatesCredentialsForm"]');
  const btnBottom = document.getElementById('btnSaveAllTemplatesBottom');
  const oldBottomHtml = btnBottom ? btnBottom.innerHTML : '';
  if (btnBottom) {
    btnBottom.disabled = true;
    btnBottom.innerHTML = '<i data-lucide="loader"></i> Saving Mappings... ⏳';
    if (window.lucide) lucide.createIcons();
  }
  if (btnTop) btnTop.disabled = true;

  const formData = new FormData(form);

  fetch(form.action, {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (btnBottom) {
      btnBottom.disabled = false;
      btnBottom.innerHTML = oldBottomHtml;
      if (window.lucide) lucide.createIcons();
    }
    if (btnTop) btnTop.disabled = false;

    if (data.success) {
      const notice = document.getElementById('tmplUnsavedChangesNotice');
      if (notice) notice.style.display = 'none';

      // Update badges dynamically
      for (const [k, cId] of Object.entries(data.assigned || {})) {
        const badge = document.querySelector(`.tmpl-status-badge-${k}`);
        if (badge) {
          badge.innerHTML = cId ? '<span class="ts-badge ts-badge-cyan">Manual Mapped</span>' : '<span class="ts-badge ts-badge-amber">Platform Default</span>';
        }
      }

      alert('Template mappings successfully saved! ✓ (' + (data.count || 0) + ' templates)');
    } else {
      alert('Failed to save mappings: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(err => {
    if (btnBottom) {
      btnBottom.disabled = false;
      btnBottom.innerHTML = oldBottomHtml;
      if (window.lucide) lucide.createIcons();
    }
    if (btnTop) btnTop.disabled = false;
    alert('Network error saving template mappings.');
  });
}

function openCreateTemplateModal() {
  resetTemplateForm();
  document.getElementById('tmplBuilderTitle').innerHTML = '<i data-lucide="layout-template"></i> Create & Save Custom HTML Email Template';
  document.getElementById('tmplSubmitBtn').innerHTML = '<i data-lucide="save"></i> Save HTML Template 💾';
  document.getElementById('templateEditorModal').style.display = 'flex';
  updateTmplModalPreview();
  if (window.lucide) lucide.createIcons();
}

function closeTemplateEditorModal() {
  document.getElementById('templateEditorModal').style.display = 'none';
}

function populateTemplateForm(key) {
  const tmpl = MAIL_TEMPLATES[key];
  if (!tmpl) return;

  document.getElementById('tmpl_key_input').value = key;
  document.getElementById('tmpl_name_input').value = tmpl.name ? tmpl.name.replace(/^✨\s*/, '') : '';
  document.getElementById('tmpl_subject_input').value = tmpl.subject || '';
  document.getElementById('tmpl_desc_input').value = tmpl.description || '';
  document.getElementById('tmpl_html_input').value = tmpl.html || '';
  document.getElementById('tmpl_id_input').value = tmpl.id || '';
  if (document.getElementById('tmpl_cred_id_input')) {
    document.getElementById('tmpl_cred_id_input').value = tmpl.assigned_credential_id || '';
  }

  document.getElementById('tmplBuilderTitle').innerHTML = '<i data-lucide="edit-3"></i> Edit Template: ' + (tmpl.name || key);
  document.getElementById('tmplSubmitBtn').innerHTML = '<i data-lucide="save"></i> Update Template 💾';
  document.getElementById('templateEditorModal').style.display = 'flex';
  updateTmplModalPreview();
  if (window.lucide) lucide.createIcons();
}

function resetTemplateForm() {
  document.getElementById('templateBuilderForm').reset();
  document.getElementById('tmpl_id_input').value = '';
  document.getElementById('tmpl_key_input').value = '';
  document.getElementById('tmpl_name_input').value = '';
  document.getElementById('tmpl_subject_input').value = '';
  document.getElementById('tmpl_desc_input').value = '';
  document.getElementById('tmpl_html_input').value = '';
  if (document.getElementById('tmpl_cred_id_input')) {
    document.getElementById('tmpl_cred_id_input').value = '';
  }
  updateTmplModalPreview();
}

function insertTmplTag(tag) {
  const textarea = document.getElementById('tmpl_html_input');
  if (!textarea) return;
  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const text = textarea.value;
  textarea.value = text.substring(0, start) + tag + text.substring(end);
  textarea.selectionStart = textarea.selectionEnd = start + tag.length;
  textarea.focus();
  updateTmplModalPreview();
}

function insertBlockComponent(type) {
  const textarea = document.getElementById('tmpl_html_input');
  if (!textarea) return;

  const blocks = {
    hero: `\n<h2 style='color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;'>{{headline}}</h2>\n<p style='color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;'>Hello <strong>{{name}}</strong>, here is your latest intelligence briefing from TechScout.</p>\n`,
    callout_info: `\n<div style='background:#f0f9ff;border:1px solid #bae6fd;border-left:4px solid #0284c7;border-radius:8px;padding:16px;color:#0369a1;font-size:14px;line-height:1.65;margin:20px 0;'>\n  <strong style='color:#0284c7;'>💡 Technical Briefing:</strong> {{message}}\n</div>\n`,
    callout_success: `\n<div style='background:#ecfdf5;border:1px solid #a7f3d0;border-left:4px solid #059669;border-radius:8px;padding:16px;color:#065f46;font-size:14px;line-height:1.65;margin:20px 0;'>\n  <strong style='color:#059669;'>✓ Verified & Approved:</strong> Your submission or transaction has been processed successfully.\n</div>\n`,
    callout_warning: `\n<div style='background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #d97706;border-radius:8px;padding:16px;color:#92400e;font-size:14px;line-height:1.65;margin:20px 0;'>\n  <strong style='color:#d97706;'>⚠️ Attention Required:</strong> Please review and adjust the requested parameters.\n</div>\n`,
    cta_primary: `\n<div style='text-align:center;margin:28px 0;'>\n  <a href='{{action_url}}' style='display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(79,70,229,0.25);'>Explore TechScout Intelligence &rarr;</a>\n</div>\n`,
    cta_green: `\n<div style='text-align:center;margin:28px 0;'>\n  <a href='{{action_url}}' style='display:inline-block;background:#059669;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;box-shadow:0 4px 12px rgba(5,150,105,0.25);'>View Disbursed Payout Ledger &rarr;</a>\n</div>\n`,
    datagrid: `\n<div style='background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin:20px 0;'>\n  <div style='display:grid;gap:8px;font-size:14px;color:#334155;'>\n    <div><strong style='color:#64748b;'>Reference ID:</strong> <span style='color:#0f172a;font-weight:600;'>#{{payout_id}}</span></div>\n    <div><strong style='color:#64748b;'>Transaction Amount:</strong> <strong style='font-size:16px;color:#059669;'>\${{amount}} USD</strong></div>\n    <div><strong style='color:#64748b;'>Status:</strong> <span style='background:#ecfdf5;color:#059669;padding:2px 8px;border-radius:4px;font-weight:700;font-size:12px;border:1px solid #a7f3d0;'>COMPLETED</span></div>\n  </div>\n</div>\n`,
    otp_box: `\n<div style='text-align:center;background:#f8fafc;background-image:linear-gradient(to right, rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.04) 1px, transparent 1px);background-size:16px 16px;border:1px solid #cbd5e1;border-radius:10px;padding:22px;margin:24px 0;'>\n  <div style='font-size:12px;color:#64748b;letter-spacing:1px;text-transform:uppercase;font-weight:700;'>One-Time Authorization PIN (Expires in {{expires_in}})</div>\n  <div style='font-size:34px;font-weight:900;color:#4f46e5;letter-spacing:8px;margin-top:8px;font-family:monospace;'>{{verification_code}}</div>\n</div>\n`
  };

  const snippet = blocks[type] || '';
  if (!snippet) return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const text = textarea.value;
  textarea.value = text.substring(0, start) + snippet + text.substring(end);
  textarea.selectionStart = textarea.selectionEnd = start + snippet.length;
  textarea.focus();
  updateTmplModalPreview();
}

function updateTmplModalPreview() {
  const textarea = document.getElementById('tmpl_html_input');
  const subjectInput = document.getElementById('tmpl_subject_input');
  const simSubject = document.getElementById('tmplModalSimSubject');
  const frame = document.getElementById('tmplModalLivePreviewFrame');

  if (!textarea || !frame) return;

  let subject = (subjectInput && subjectInput.value) ? subjectInput.value : 'TechScout Intelligence Update';
  if (simSubject) simSubject.textContent = 'Subject: ' + subject;

  let rawHtml = textarea.value || '';
  let renderedHtml = rawHtml
    .replace(/\{\{name\}\}/g, 'Alex Rivera')
    .replace(/\{\{username\}\}/g, 'alex_dev')
    .replace(/\{\{action_url\}\}/g, window.location.origin)
    .replace(/\{\{headline\}\}/g, subject)
    .replace(/\{\{message\}\}/g, 'Here is the customized communication content previewed live in real-time...')
    .replace(/\{\{verification_code\}\}/g, '948201')
    .replace(/\{\{amount\}\}/g, '250.00')
    .replace(/\{\{payout_id\}\}/g, '1001')
    .replace(/\{\{site_name\}\}/g, 'TechScout')
    .replace(/\{\{year\}\}/g, new Date().getFullYear());

  const fullEnvelope = `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body {
  margin: 0;
  padding: 24px 12px;
  background-color: #ffffff;
  background-image: linear-gradient(to right, rgba(99,102,241,0.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.08) 1px, transparent 1px);
  background-size: 32px 32px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  color: #0f172a;
}
.card {
  max-width: 580px;
  margin: 0 auto;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.07);
  overflow: hidden;
}
.head {
  background: #ffffff;
  background-image: linear-gradient(to right, rgba(99,102,241,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.05) 1px, transparent 1px);
  background-size: 24px 24px;
  padding: 22px 28px;
  border-bottom: 2px solid #6366f1;
  text-align: center;
}
.body {
  padding: 28px 26px;
  color: #1e293b;
  font-size: 15px;
  line-height: 1.65;
}
.foot {
  border-top: 1px solid #e2e8f0;
  padding: 16px;
  text-align: center;
  font-size: 12px;
  color: #64748b;
  background: #f8fafc;
}
</style>
</head>
<body>
<div class="card">
  <div class="head">
    <div style="font-size:22px;font-weight:900;color:#0f172a;letter-spacing:-0.5px;">Tech<span style="color:#6366f1;">Scout</span> <span style="font-size:11px;background:#6366f1;color:#ffffff;padding:2px 7px;border-radius:4px;vertical-align:middle;margin-left:4px;letter-spacing:1px;font-weight:700;">TEMPLATE PREVIEW</span></div>
    <div style="font-size:11px;color:#64748b;letter-spacing:1px;text-transform:uppercase;font-weight:600;margin-top:4px;">Developer Intelligence Network</div>
  </div>
  <div class="body">
    ${renderedHtml}
  </div>
  <div class="foot">
    &copy; ${new Date().getFullYear()} TechScout &bull; Developer Intelligence Network
  </div>
</div>
</body>
</html>`;

  try {
    const doc = frame.contentWindow.document;
    doc.open();
    doc.write(fullEnvelope);
    doc.close();
  } catch (e) {}
}

function setModalTmplViewMode(mode) {
  const colEditor = document.getElementById('tmpl-modal-col-editor');
  const colPreview = document.getElementById('tmpl-modal-col-preview');
  const grid = document.getElementById('tmpl-modal-workspace-grid');

  document.getElementById('btn-tmpl-modal-split').className = 'ts-btn ts-btn-sm ts-btn-outline';
  document.getElementById('btn-tmpl-modal-editor').className = 'ts-btn ts-btn-sm ts-btn-outline';
  document.getElementById('btn-tmpl-modal-preview').className = 'ts-btn ts-btn-sm ts-btn-outline';
  document.getElementById(`btn-tmpl-modal-${mode}`).className = 'ts-btn ts-btn-sm ts-btn-primary';

  if (mode === 'split') {
    grid.style.gridTemplateColumns = '1fr 1fr';
    colEditor.style.display = 'flex';
    colPreview.style.display = 'flex';
  } else if (mode === 'editor') {
    grid.style.gridTemplateColumns = '1fr';
    colEditor.style.display = 'flex';
    colPreview.style.display = 'none';
  } else if (mode === 'preview') {
    grid.style.gridTemplateColumns = '1fr';
    colEditor.style.display = 'none';
    colPreview.style.display = 'flex';
  }
}

function saveEventTriggerMapping(eventKey, selectId) {
  const select = document.getElementById(selectId);
  if (!select) return;
  const templateKey = select.value;

  const formData = new FormData();
  formData.append('event_key', eventKey);
  formData.append('template_key', templateKey);

  fetch('<?= url('/admin/mail/event-triggers/save') ?>', {
    method: 'POST',
    body: formData,
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert(`Event trigger '${eventKey}' mapped to template '${templateKey}' successfully! 💾`);
    } else {
      alert(data.message || 'Failed to update mapping.');
    }
  })
  .catch(() => alert('Network error updating event trigger mapping.'));
}

function openTestEventTriggerModal(eventKey, eventName) {
  document.getElementById('test_event_key').value = eventKey;
  document.getElementById('test_event_key_display').value = `${eventKey} (${eventName || ''})`;
  document.getElementById('test_event_result').style.display = 'none';
  document.getElementById('testEventTriggerModal').style.display = 'flex';
}

function closeTestEventTriggerModal() {
  document.getElementById('testEventTriggerModal').style.display = 'none';
}

function submitTestEventTrigger(e) {
  e.preventDefault();
  const eventKey = document.getElementById('test_event_key').value;
  const recipName = document.getElementById('test_event_recipient_name').value;
  const recipEmail = document.getElementById('test_event_recipient_email').value;
  const resBox = document.getElementById('test_event_result');
  const btn = document.getElementById('btnTestEventSubmit');

  if (!recipEmail) {
    alert('Please enter a valid recipient email address.');
    return;
  }

  resBox.style.display = 'block';
  resBox.className = 'ts-badge ts-badge-cyan';
  resBox.innerText = 'Firing event trigger & resolving template... ⏳';
  if (btn) btn.disabled = true;

  const formData = new FormData();
  formData.append('event_key', eventKey);
  formData.append('test_name', recipName);
  formData.append('test_email', recipEmail);

  fetch('<?= url('/admin/mail/event-triggers/test') ?>', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (btn) btn.disabled = false;
    resBox.className = data.success ? 'ts-badge ts-badge-green' : 'ts-badge ts-badge-pink';
    resBox.innerText = data.message || (data.success ? 'Event fired successfully!' : 'Failed.');
  })
  .catch(() => {
    if (btn) btn.disabled = false;
    resBox.className = 'ts-badge ts-badge-pink';
    resBox.innerText = 'Network error during trigger test fire.';
  });
}

// Custom Dynamic Trigger Functions
function openCreateCustomTriggerModal() {
  const modal = document.getElementById('createCustomTriggerModal');
  if (modal) {
    document.getElementById('createCustomTriggerForm').reset();
    modal.style.display = 'flex';
  }
}

function closeCreateCustomTriggerModal() {
  const modal = document.getElementById('createCustomTriggerModal');
  if (modal) modal.style.display = 'none';
}

function switchCustomTriggerModalTab(tab) {
  const configTab = document.getElementById('cmodal-tab-config');
  const tutorialTab = document.getElementById('cmodal-tab-tutorial');
  const btnConfig = document.getElementById('cmodal-tab-btn-config');
  const btnTutorial = document.getElementById('cmodal-tab-btn-tutorial');

  if (tab === 'tutorial') {
    if (configTab) configTab.style.display = 'none';
    if (tutorialTab) tutorialTab.style.display = 'block';
    if (btnConfig) {
      btnConfig.className = 'ts-btn ts-btn-sm ts-btn-outline';
      btnConfig.style.color = '#94a3b8';
      btnConfig.style.borderColor = 'rgba(148,163,184,0.3)';
    }
    if (btnTutorial) {
      btnTutorial.className = 'ts-btn ts-btn-sm ts-btn-primary';
      btnTutorial.style.color = '#fff';
      btnTutorial.style.background = 'linear-gradient(135deg, #0284c7, #38bdf8)';
    }
  } else {
    if (configTab) configTab.style.display = 'block';
    if (tutorialTab) tutorialTab.style.display = 'none';
    if (btnConfig) {
      btnConfig.className = 'ts-btn ts-btn-sm ts-btn-primary';
      btnConfig.style.color = '#fff';
      btnConfig.style.background = '';
    }
    if (btnTutorial) {
      btnTutorial.className = 'ts-btn ts-btn-sm ts-btn-outline';
      btnTutorial.style.color = '#38bdf8';
      btnTutorial.style.borderColor = 'rgba(56,189,248,0.4)';
    }
  }
}

function prefillTriggerSample(patternOrKey, name, type, templateKey, subject, singleDelivery) {
  const nameInput = document.getElementById('ct_name');
  const typeSelect = document.getElementById('ct_type');
  const keyInput = document.getElementById('ct_key_or_pattern');
  const tmplSelect = document.getElementById('ct_template_key');
  const subjInput = document.getElementById('ct_subject_override');
  const singleCheckbox = document.getElementById('ct_single_delivery');

  if (nameInput) nameInput.value = name;
  if (typeSelect) {
    typeSelect.value = type;
    toggleCustomTriggerTypeNotice(type);
  }
  if (keyInput) keyInput.value = patternOrKey;
  if (tmplSelect) tmplSelect.value = templateKey;
  if (subjInput) subjInput.value = subject || '';
  if (singleCheckbox) singleCheckbox.checked = !!singleDelivery;

  switchCustomTriggerModalTab('config');
}

function toggleCustomTriggerTypeNotice(type) {
  const notice = document.getElementById('ct_type_notice');
  const input = document.getElementById('ct_key_or_pattern');
  if (notice && input) {
    if (type === 'url_pattern') {
      notice.innerHTML = 'Matches requested route path (e.g. <code>/admin/revenue/process-payout</code> or <code>process-payout</code>) and automatically dispatches assigned HTML template!';
      input.placeholder = 'e.g. /admin/revenue/process-payout';
    } else {
      notice.innerHTML = 'Fires when <code>\\App\\Core\\EventTriggerEngine::fire("key", $payload)</code> is called in PHP code!';
      input.placeholder = 'e.g. custom_user_signup';
    }
  }
}

function submitCreateCustomTrigger(e) {
  e.preventDefault();
  const name = document.getElementById('ct_name').value.trim();
  const type = document.getElementById('ct_type').value;
  const keyOrPattern = document.getElementById('ct_key_or_pattern').value.trim();
  const templateKey = document.getElementById('ct_template_key').value;
  const subjectOverride = document.getElementById('ct_subject_override').value.trim();
  const singleDelivery = document.getElementById('ct_single_delivery').checked ? '1' : '';

  if (!name || !keyOrPattern || !templateKey) {
    alert('Please complete all required fields.');
    return;
  }

  const formData = new FormData();
  formData.append('name', name);
  formData.append('type', type);
  formData.append('key_or_pattern', keyOrPattern);
  formData.append('template_key', templateKey);
  formData.append('subject_override', subjectOverride);
  formData.append('single_delivery', singleDelivery);

  fetch('<?= url('/admin/mail/trigger/create') ?>', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: formData
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      alert(d.message || 'Custom trigger saved successfully!');
      closeCreateCustomTriggerModal();
      window.location.reload();
    } else {
      alert(d.message || 'Failed to save custom trigger.');
    }
  })
  .catch(err => {
    alert('Network error while saving custom trigger.');
  });
}

function deleteCustomTrigger(id) {
  if (!confirm('Are you sure you want to delete this custom trigger?')) return;
  const formData = new FormData();
  formData.append('id', id);

  fetch('<?= url('/admin/mail/trigger/delete') ?>', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: formData
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      window.location.reload();
    } else {
      alert(d.message || 'Failed to delete custom trigger.');
    }
  })
  .catch(err => alert('Network error.'));
}

// Visual WYSIWYG Editor Sync Functions
function switchTmplEditMode(mode) {
  const wysiwygCont = document.getElementById('tmpl_wysiwyg_container');
  const wysiwygTool = document.getElementById('tmpl_wysiwyg_toolbar');
  const htmlInput = document.getElementById('tmpl_html_input');
  const btnWysiwyg = document.getElementById('btn-tmpl-mode-wysiwyg');
  const btnHtml = document.getElementById('btn-tmpl-mode-html');

  if (mode === 'wysiwyg') {
    syncCodeToWysiwyg();
    if (wysiwygCont) wysiwygCont.style.display = 'block';
    if (wysiwygTool) wysiwygTool.style.display = 'flex';
    if (htmlInput) htmlInput.style.display = 'none';
    if (btnWysiwyg) { btnWysiwyg.className = 'ts-btn ts-btn-sm ts-btn-primary'; btnWysiwyg.style.background='#a855f7'; }
    if (btnHtml) { btnHtml.className = 'ts-btn ts-btn-sm ts-btn-outline'; btnHtml.style.background='transparent'; }
  } else {
    syncWysiwygToCode();
    if (wysiwygCont) wysiwygCont.style.display = 'none';
    if (wysiwygTool) wysiwygTool.style.display = 'none';
    if (htmlInput) htmlInput.style.display = 'block';
    if (btnWysiwyg) { btnWysiwyg.className = 'ts-btn ts-btn-sm ts-btn-outline'; btnWysiwyg.style.background='transparent'; }
    if (btnHtml) { btnHtml.className = 'ts-btn ts-btn-sm ts-btn-primary'; btnHtml.style.background='#a855f7'; }
  }
}

function execTmplWysiwyg(cmd, value = null) {
  document.execCommand(cmd, false, value);
  syncWysiwygToCode();
}

function syncWysiwygToCode() {
  const wysiwyg = document.getElementById('tmpl_wysiwyg_editor');
  const htmlInput = document.getElementById('tmpl_html_input');
  if (wysiwyg && htmlInput) {
    htmlInput.value = wysiwyg.innerHTML;
    if (typeof updateTmplModalPreview === 'function') {
      updateTmplModalPreview();
    }
  }
}

function syncCodeToWysiwyg() {
  const wysiwyg = document.getElementById('tmpl_wysiwyg_editor');
  const htmlInput = document.getElementById('tmpl_html_input');
  if (wysiwyg && htmlInput) {
    wysiwyg.innerHTML = htmlInput.value || '<p>Start typing template content...</p>';
    if (typeof updateTmplModalPreview === 'function') {
      updateTmplModalPreview();
    }
  }
}

function promptTmplLink() {
  const url = prompt('Enter URL link:', 'https://');
  if (url) execTmplWysiwyg('createLink', url);
}

function insertTmplCtaButton() {
  const btnHtml = '<div style="margin:20px 0; text-align:center;"><a href="{{action_url}}" style="display:inline-block; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#ffffff; padding:12px 28px; border-radius:8px; font-weight:bold; text-decoration:none; font-size:15px; box-shadow:0 4px 12px rgba(99,102,241,0.3);">Primary Call-to-Action Button &rarr;</a></div>';
  const wysiwyg = document.getElementById('tmpl_wysiwyg_editor');
  if (wysiwyg) {
    wysiwyg.focus();
    document.execCommand('insertHTML', false, btnHtml);
    syncWysiwygToCode();
  }
}

function previewTemplateModal(key) {
  const tmpl = MAIL_TEMPLATES[key];
  if (!tmpl) return;

  document.getElementById('tmplPreviewModalTitle').innerText = tmpl.name || key;
  document.getElementById('tmplPreviewSubject').innerText = 'Subject: ' + (tmpl.subject || '');

  const frame = document.getElementById('tmplPreviewIframe');
  const actionUrl = window.location.origin;
  let rawHtml = tmpl.html || '<p>No content preview available.</p>';
  let renderedHtml = rawHtml
    .replace(/\{\{name\}\}/g, 'Devin Vance')
    .replace(/\{\{username\}\}/g, 'devin_systems')
    .replace(/\{\{action_url\}\}/g, actionUrl)
    .replace(/\{\{headline\}\}/g, tmpl.subject || 'Engineering Update')
    .replace(/\{\{message\}\}/g, 'This is simulated dynamic message body content previewing the live template layout...')
    .replace(/background:\s*#0f172a/gi, 'background:#ffffff')
    .replace(/background-color:\s*#0f172a/gi, 'background-color:#ffffff')
    .replace(/background:\s*#05070b/gi, 'background:#f8fafc')
    .replace(/color:\s*#f8fafc/gi, 'color:#0f172a')
    .replace(/color:\s*#cbd5e1/gi, 'color:#334155')
    .replace(/color:\s*#94a3b8/gi, 'color:#475569');

  const fullEnvelope = `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body {
  margin: 0;
  padding: 24px 12px;
  background-color: #ffffff;
  background-image: linear-gradient(to right, rgba(99,102,241,0.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.08) 1px, transparent 1px);
  background-size: 32px 32px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  color: #0f172a;
}
.card {
  max-width: 580px;
  margin: 0 auto;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.07);
  overflow: hidden;
}
.head {
  background: #ffffff;
  background-image: linear-gradient(to right, rgba(99,102,241,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(99,102,241,0.05) 1px, transparent 1px);
  background-size: 24px 24px;
  padding: 22px 28px;
  border-bottom: 2px solid #6366f1;
  text-align: center;
}
.body {
  padding: 28px 26px;
  color: #1e293b;
  font-size: 15px;
  line-height: 1.65;
}
.foot {
  border-top: 1px solid #e2e8f0;
  padding: 16px;
  text-align: center;
  font-size: 12px;
  color: #64748b;
  background: #f8fafc;
}
</style>
</head>
<body>
<div class="card">
  <div class="head">
    <div style="font-size:22px;font-weight:900;color:#0f172a;letter-spacing:-0.5px;">Tech<span style="color:#6366f1;">Scout</span> <span style="font-size:11px;background:#6366f1;color:#ffffff;padding:2px 7px;border-radius:4px;vertical-align:middle;margin-left:4px;letter-spacing:1px;font-weight:700;">PREVIEW</span></div>
    <div style="font-size:11px;color:#64748b;letter-spacing:1px;text-transform:uppercase;font-weight:600;margin-top:4px;">Developer Intelligence Network</div>
  </div>
  <div class="body">
    ${renderedHtml}
  </div>
  <div class="foot">
    &copy; ${new Date().getFullYear()} TechScout &bull; Developer Intelligence Network
  </div>
</div>
</body>
</html>`;

  try {
    const doc = frame.contentWindow.document;
    doc.open();
    doc.write(fullEnvelope);
    doc.close();
  } catch (e) {}

  document.getElementById('templatePreviewModal').style.display = 'flex';
}

function closeTemplatePreviewModal() {
  document.getElementById('templatePreviewModal').style.display = 'none';
}

// Mail Credentials Modals and JS
function openAddCredentialModal(provider = 'python_api') {
  document.getElementById('credForm').reset();
  document.getElementById('cred_id').value = '';
  document.getElementById('cred_provider').value = provider;
  document.getElementById('cred_request_timeout').value = 15;
  document.getElementById('cred_payload_format').value = 'standard_json';
  document.getElementById('cred_custom_headers_json').value = '{\n  "X-API-Key": "YOUR_SECRET_TOKEN",\n  "X-Client-ID": "techscout"\n}';
  document.getElementById('credModalTitle').innerText = provider === 'smtp' ? 'Add Standard SMTP Server Credential' : 'Add Outbound Python API / REST Endpoint';
  toggleProviderFields(provider);
  document.getElementById('mailCredModal').style.display = 'flex';
}

function openEditCredentialModal(c) {
  document.getElementById('cred_id').value = c.id || '';
  document.getElementById('cred_name').value = c.name || '';
  document.getElementById('cred_provider').value = c.provider || 'python_api';
  document.getElementById('cred_from_name').value = c.from_name || '';
  document.getElementById('cred_from_email').value = c.from_email || '';
  document.getElementById('cred_reply_to_email').value = c.reply_to_email || '';
  document.getElementById('cred_host').value = c.host || '';
  document.getElementById('cred_port').value = c.port || 587;
  document.getElementById('cred_encryption').value = c.encryption || 'tls';
  document.getElementById('cred_username').value = c.username || '';
  document.getElementById('cred_password').value = c.password || '';
  document.getElementById('cred_api_key').value = c.api_key || '';
  document.getElementById('cred_api_endpoint').value = c.api_endpoint || '';
  document.getElementById('cred_custom_headers_json').value = c.custom_headers_json || '';
  document.getElementById('cred_request_timeout').value = c.request_timeout || 15;
  document.getElementById('cred_payload_format').value = c.payload_format || 'standard_json';
  document.getElementById('cred_webhook_url').value = c.webhook_url || '';
  document.getElementById('cred_notes').value = c.notes || '';
  document.getElementById('cred_is_default').checked = parseInt(c.is_default, 10) === 1;
  document.getElementById('cred_status').value = c.status || 'active';
  document.getElementById('credModalTitle').innerText = 'Edit Mail Credential: ' + (c.name || '');
  toggleProviderFields(c.provider || 'python_api');
  document.getElementById('mailCredModal').style.display = 'flex';
}

function closeMailCredModal() {
  document.getElementById('mailCredModal').style.display = 'none';
}

function toggleProviderFields(provider) {
  const isSmtp = provider === 'smtp';
  const isPythonOrCustom = ['python_api', 'pythonanywhere_api', 'custom_rest'].includes(provider);
  
  document.getElementById('box_smtp_fields').style.display = isSmtp ? 'block' : 'none';
  document.getElementById('box_api_fields').style.display = isSmtp ? 'none' : 'block';
  document.getElementById('box_python_advanced').style.display = isPythonOrCustom ? 'block' : 'none';
  document.getElementById('box_python_boilerplate').style.display = isPythonOrCustom ? 'block' : 'none';
}

function openTestCredModal(credId, credName) {
  document.getElementById('test_cred_id').value = credId;
  document.getElementById('testCredModalTitle').innerText = '⚡ Test Connection: ' + credName;
  document.getElementById('test_cred_result').style.display = 'none';
  document.getElementById('testCredModal').style.display = 'flex';
}

function closeTestCredModal() {
  document.getElementById('testCredModal').style.display = 'none';
}

function submitCredTest() {
  const credId = document.getElementById('test_cred_id').value;
  const email = document.getElementById('test_cred_email').value;
  const resBox = document.getElementById('test_cred_result');

  if (!email) {
    alert('Please enter a recipient test email address.');
    return;
  }

  resBox.style.display = 'block';
  resBox.className = 'ts-badge ts-badge-cyan';
  resBox.innerText = 'Testing connection & sending dispatch... ⏳';

  const formData = new FormData();
  formData.append('credential_id', credId);
  formData.append('test_email', email);

  fetch('<?= url('/admin/mail/credentials/test') ?>', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(d => {
    resBox.className = d.success ? 'ts-badge ts-badge-green' : 'ts-badge ts-badge-pink';
    resBox.innerText = d.message || (d.success ? 'Success!' : 'Failed.');
  })
  .catch(err => {
    resBox.className = 'ts-badge ts-badge-pink';
    resBox.innerText = 'Network request error during test.';
  });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  const currentKey = document.getElementById('mail_template_key')?.value || 'community_broadcast';
  loadTemplateToEditor(currentKey);

  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab');
  if (tab) {
    switchMailTab(tab);
  }
});

// Global Window Scope Function Binding (Guarantees button onclick handlers always resolve)
window.switchMailTab = switchMailTab;
window.toggleRecipientInput = toggleRecipientInput;
window.toggleCustomSender = toggleCustomSender;
window.loadTemplateToEditor = loadTemplateToEditor;
window.previewTemplateModal = previewTemplateModal;
window.closeTemplatePreviewModal = closeTemplatePreviewModal;
window.closeTemplateEditorModal = closeTemplateEditorModal;
window.inspectSentMail = inspectSentMail;
window.closeInspectModal = closeInspectModal;
window.switchInspectSubTab = switchInspectSubTab;
window.copyTrackingToken = copyTrackingToken;
window.copyInspectHtml = copyInspectHtml;
window.copyInspectJson = copyInspectJson;
window.recomposeFromInspect = recomposeFromInspect;
window.openInspectFullscreen = openInspectFullscreen;
window.openAddCredentialModal = openAddCredentialModal;
window.closeMailCredModal = closeMailCredModal;
window.openTestCredModal = openTestCredModal;
window.closeTestCredModal = closeTestCredModal;
window.submitCredTest = submitCredTest;
window.insertTag = insertTag;
window.insertTmplTag = insertTmplTag;
window.insertBlockComponent = insertBlockComponent;
window.insertSnippet = insertSnippet;
window.addVariableOverrideRow = addVariableOverrideRow;
window.removeVariableOverrideRow = removeVariableOverrideRow;
window.composeToSelectedUsers = composeToSelectedUsers;
window.clearSelectedUsers = clearSelectedUsers;
window.composeToSingleUser = composeToSingleUser;
window.filterTemplateCategory = filterTemplateCategory;
window.populateTemplateForm = populateTemplateForm;
window.setViewMode = setViewMode;
window.filterUserDirectory = filterUserDirectory;
window.filterMailLogsLive = filterMailLogsLive;
window.filterTemplateCardsLive = filterTemplateCardsLive;
window.onTemplateCredChanged = onTemplateCredChanged;
window.submitBatchTemplateCredentials = submitBatchTemplateCredentials;
window.openCreateTemplateModal = openCreateTemplateModal;
window.resetTemplateForm = resetTemplateForm;
window.toggleProviderFields = toggleProviderFields;
window.toggleSelectAllUsers = toggleSelectAllUsers;
window.updateSelectedUserCount = updateSelectedUserCount;
window.updateTmplModalPreview = updateTmplModalPreview;
window.setModalTmplViewMode = setModalTmplViewMode;
window.saveEventTriggerMapping = saveEventTriggerMapping;
window.openTestEventTriggerModal = openTestEventTriggerModal;
window.closeTestEventTriggerModal = closeTestEventTriggerModal;
window.submitTestEventTrigger = submitTestEventTrigger;
</script>

<!-- Add / Edit Mail Credential Modal -->
<div id="mailCredModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 780px; width: 95%; max-height: 90vh; overflow-y: auto;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;" id="credModalTitle"><i data-lucide="key"></i> Mail Credential &amp; Python API Profile</h3>
      <button type="button" onclick="closeMailCredModal()" class="admin-modal-close">✕</button>
    </div>
    <form action="<?= url('/admin/mail/credentials/save') ?>" method="POST" id="credForm">
      <input type="hidden" name="id" id="cred_id" value="">
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Profile Friendly Name <span style="color:#ef4444;">*</span></label>
          <input type="text" name="name" id="cred_name" placeholder="e.g. My PythonAnywhere Mail Microservice" class="admin-modal-input" required>
        </div>
        <div>
          <label class="admin-modal-label">Provider / Engine Driver <span style="color:#ef4444;">*</span></label>
          <select name="provider" id="cred_provider" onchange="toggleProviderFields(this.value)" class="admin-modal-select" style="color: #38bdf8; font-weight: 700;">
            <option value="python_api">🐍 Python API / Custom REST Microservice</option>
            <option value="pythonanywhere_api">🐍 PythonAnywhere Hosted Flask/FastAPI Endpoint</option>
            <option value="custom_rest">🌐 Generic Custom HTTP REST API</option>
            <option value="resend">⚡ Resend Cloud API</option>
            <option value="sendgrid">📬 SendGrid REST API v3</option>
            <option value="ses">☁️ Amazon Simple Email Service (SES)</option>
            <option value="mailgun">🎯 Mailgun REST API</option>
            <option value="postmark">✉️ Postmark API</option>
            <option value="smtp">⚙️ Standard SMTP Server (Mailtrap / Custom)</option>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">From Name <span style="color:#ef4444;">*</span></label>
          <input type="text" name="from_name" id="cred_from_name" placeholder="TechScout Dispatch" class="admin-modal-input" required>
        </div>
        <div>
          <label class="admin-modal-label">From Email <span style="color:#ef4444;">*</span></label>
          <input type="email" name="from_email" id="cred_from_email" placeholder="dispatch@techscout.online" class="admin-modal-input" required>
        </div>
        <div>
          <label class="admin-modal-label">Reply-To Email (Optional)</label>
          <input type="email" name="reply_to_email" id="cred_reply_to_email" placeholder="support@techscout.online" class="admin-modal-input">
        </div>
      </div>

      <!-- SMTP Details Box -->
      <div id="box_smtp_fields" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(56,189,248,0.2); border-radius: 8px; padding: 1.25rem; margin-bottom: 1rem;">
        <h4 style="color: #38bdf8; font-size: 0.92rem; margin: 0 0 0.75rem 0;">SMTP Server Coordinates</h4>
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
          <div>
            <label class="admin-modal-label">Host Address</label>
            <input type="text" name="host" id="cred_host" placeholder="smtp.mailtrap.io" class="admin-modal-input">
          </div>
          <div>
            <label class="admin-modal-label">Port</label>
            <input type="number" name="port" id="cred_port" value="587" class="admin-modal-input">
          </div>
          <div>
            <label class="admin-modal-label">Encryption</label>
            <select name="encryption" id="cred_encryption" class="admin-modal-select">
              <option value="tls">TLS</option>
              <option value="ssl">SSL</option>
              <option value="none">None</option>
            </select>
          </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
          <div>
            <label class="admin-modal-label">Username</label>
            <input type="text" name="username" id="cred_username" placeholder="smtp_user" class="admin-modal-input">
          </div>
          <div>
            <label class="admin-modal-label">Password</label>
            <input type="password" name="password" id="cred_password" placeholder="••••••••" class="admin-modal-input">
          </div>
        </div>
      </div>

      <!-- Cloud / Python API Box -->
      <div id="box_api_fields" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(168,85,247,0.25); border-radius: 8px; padding: 1.25rem; margin-bottom: 1rem;">
        <h4 style="color: #c084fc; font-size: 0.92rem; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="terminal"></i> API Endpoint Coordinates &amp; Secret Keys
        </h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
          <div>
            <label class="admin-modal-label">API Endpoint URL <span style="color:#ef4444;">*</span></label>
            <input type="url" name="api_endpoint" id="cred_api_endpoint" placeholder="https://myuser.pythonanywhere.com/api/v1/send-mail" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
          </div>
          <div>
            <label class="admin-modal-label">API Key / Secret Bearer Token</label>
            <input type="password" name="api_key" id="cred_api_key" placeholder="Bearer secret or custom token" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
          </div>
        </div>

        <!-- Python & Custom Microservice Advanced Options -->
        <div id="box_python_advanced" style="border-top: 1px solid rgba(148,163,184,0.12); padding-top: 0.75rem; margin-top: 0.75rem;">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
            <div>
              <label class="admin-modal-label">Payload Format Schema</label>
              <select name="payload_format" id="cred_payload_format" class="admin-modal-select">
                <option value="standard_json">Standard JSON (to_email, subject, html_content, attachments)</option>
                <option value="resend_compatible">Resend Compatible JSON (from, to[], subject, html)</option>
                <option value="sendgrid_compatible">SendGrid v3 Compatible JSON</option>
              </select>
            </div>
            <div>
              <label class="admin-modal-label">Request Timeout (Seconds)</label>
              <input type="number" name="request_timeout" id="cred_request_timeout" value="15" min="3" max="60" class="admin-modal-input">
            </div>
          </div>

          <div style="margin-bottom: 0.75rem;">
            <label class="admin-modal-label">Custom HTTP Headers (JSON Object Dictionary)</label>
            <textarea name="custom_headers_json" id="cred_custom_headers_json" rows="3" placeholder='{&#10;  "X-API-Key": "YOUR_SECRET_KEY",&#10;  "X-Client-ID": "techscout"&#10;}' class="admin-modal-textarea" style="font-family: var(--admin-font-mono); font-size: 0.8rem;"></textarea>
            <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.25rem;">Custom HTTP headers sent with each POST request to your Python API.</div>
          </div>

          <div>
            <label class="admin-modal-label">Delivery Receipt Webhook Callback URL (Optional)</label>
            <input type="url" name="webhook_url" id="cred_webhook_url" placeholder="https://api.mysite.com/mail-receipt" class="admin-modal-input">
          </div>
        </div>
      </div>

      <!-- Boilerplate Python Script Accordion -->
      <div id="box_python_boilerplate" style="margin-bottom: 1rem;">
        <details style="background: #05070b; border: 1px solid rgba(56,189,248,0.25); border-radius: 8px; padding: 0.65rem 0.85rem;">
          <summary style="cursor: pointer; font-size: 0.82rem; font-weight: 700; color: #38bdf8; user-select: none;">
            🐍 View Sample Python Flask / FastAPI Microservice Code Boilerplate
          </summary>
          <div style="margin-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.75rem;">
            <p style="color: #94a3b8; font-size: 0.78rem; margin: 0 0 0.5rem 0;">Copy and paste this Python script into your PythonAnywhere, AWS Lambda, or VPS to create an instant email microservice:</p>
            <pre style="background: #030712; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem; border-radius: 6px; font-family: monospace; font-size: 0.72rem; color: #38bdf8; overflow-x: auto; max-height: 220px;"><code>from flask import Flask, request, jsonify
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

app = Flask(__name__)

API_SECRET_KEY = "YOUR_SECRET_TOKEN"

@app.route('/api/v1/send-mail', methods=['POST'])
def send_mail():
    auth_header = request.headers.get('Authorization', '')
    api_key_header = request.headers.get('X-API-Key', '')
    if auth_header != f"Bearer {API_SECRET_KEY}" and api_key_header != API_SECRET_KEY:
        return jsonify({'success': False, 'error': 'Unauthorized'}), 401
    
    data = request.get_json(force=True)
    to_email = data.get('to_email')
    subject = data.get('subject')
    html_content = data.get('html_content')
    from_email = data.get('from_email', 'noreply@mysite.com')
    from_name = data.get('from_name', 'TechScout')

    # Example: Send via standard Python smtplib / local sendmail / SendGrid
    msg = MIMEMultipart('alternative')
    msg['Subject'] = subject
    msg['From'] = f"{from_name} &lt;{from_email}&gt;"
    msg['To'] = to_email
    msg.attach(MIMEText(html_content, 'html'))

    # with smtplib.SMTP_SSL('smtp.gmail.com', 465) as server:
    #     server.login("your@gmail.com", "app_password")
    #     server.sendmail(from_email, to_email, msg.as_string())

    return jsonify({'success': True, 'message': f'Email delivered to {to_email}'}), 200

if __name__ == '__main__':
    app.run(port=5000)</code></pre>
          </div>
        </details>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; align-items: center;">
        <div>
          <label class="admin-modal-label">Status</label>
          <select name="status" id="cred_status" class="admin-modal-select">
            <option value="active">Active (Production Ready)</option>
            <option value="testing">Testing (Development)</option>
            <option value="inactive">Inactive (Disabled)</option>
          </select>
        </div>
        <div style="padding-top: 1.2rem;">
          <label style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; font-size: 0.88rem; cursor: pointer;">
            <input type="checkbox" name="is_default" id="cred_is_default" value="1" style="width: 16px; height: 16px; accent-color: #10b981;">
            <span><strong>Set as Platform Default Provider</strong></span>
          </label>
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Internal Notes / Description (Optional)</label>
        <input type="text" name="notes" id="cred_notes" placeholder="e.g. Python microservice hosted on PythonAnywhere server in EU region" class="admin-modal-input">
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeMailCredModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; color: #000; font-weight: 700;">Save Credential Profile ✓</button>
      </div>
    </form>
  </div>
</div>

<!-- Test Connection Modal -->
<div id="testCredModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 480px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;" id="testCredModalTitle"><i data-lucide="zap"></i> Test Connection</h3>
      <button type="button" onclick="closeTestCredModal()" class="admin-modal-close">✕</button>
    </div>
    <input type="hidden" id="test_cred_id" value="">
    <div style="margin-bottom: 1.25rem;">
      <label class="admin-modal-label">Recipient Email Address</label>
      <input type="email" id="test_cred_email" placeholder="tester@example.com" class="admin-modal-input" required>
    </div>
    <div id="test_cred_result" style="display: none; margin-bottom: 1.25rem; font-size: 0.85rem; padding: 0.6rem;"></div>
    <div class="admin-modal-footer">
      <button type="button" onclick="closeTestCredModal()" class="ts-btn ts-btn-outline ts-btn-sm">Close</button>
      <button type="button" onclick="submitCredTest()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #10b981; border-color: #10b981; font-weight: 700;">Send Test Ping ⚡</button>
    </div>
  </div>
</div>

<!-- Template Live Preview Modal -->
<div id="templatePreviewModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-purple" style="max-width: 680px; width: 95%;">
    <div class="admin-modal-header">
      <div>
        <h3 class="admin-modal-title" style="color: #c084fc;" id="tmplPreviewModalTitle">Template Preview</h3>
        <div id="tmplPreviewSubject" style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.2rem;"></div>
      </div>
      <button type="button" onclick="closeTemplatePreviewModal()" class="admin-modal-close">✕</button>
    </div>

    <div style="padding: 1rem 0;">
      <iframe id="tmplPreviewIframe" style="width: 100%; height: 460px; border: none; border-radius: 8px; background: #04070e;"></iframe>
    </div>

    <div class="admin-modal-footer">
      <button type="button" onclick="closeTemplatePreviewModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #a855f7; border-color: #a855f7;">Close Preview</button>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
