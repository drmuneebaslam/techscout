<?php
$title = ($template ? 'Edit Template: ' . htmlspecialchars($template['name']) : 'Create Custom HTML Email Template') . ' | TechScout Mail Studio';
require __DIR__ . '/../layouts/admin_header.php';

$allVariableCategories = [
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

<div class="admin-content-inner" style="max-width: 1550px; margin: 0 auto;">
  <!-- Page Header Strip -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <a href="<?= url('/admin/mail?tab=templates') ?>" style="color: #38bdf8; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
          &larr; Back to Mail &amp; Template Studio
        </a>
        <span style="color: #64748b;">&bull;</span>
        <span class="ts-badge ts-badge-purple" style="font-size: 0.68rem; font-weight: 800;">FULL-PAGE CANVAS &amp; LIVE SANDBOX</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        <?= $template ? '✏️ Edit Template: ' . htmlspecialchars($template['name'] ?? '') : '➕ Create New HTML Email Template' ?>
      </h1>
      <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.2rem 0 0 0;">
        Interactive 2-column workspace for drafting responsive HTML layouts with live real-time visual simulation.
      </p>
    </div>

    <div style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">
      <div style="display: flex; gap: 0.25rem; background: #05070b; padding: 4px; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
        <button type="button" onclick="setStandaloneViewMode('split')" class="ts-btn ts-btn-sm ts-btn-primary" id="btn-sa-split" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">🌓 Split View</button>
        <button type="button" onclick="setStandaloneViewMode('editor')" class="ts-btn ts-btn-sm ts-btn-outline" id="btn-sa-editor" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">📝 Editor Only</button>
        <button type="button" onclick="setStandaloneViewMode('preview')" class="ts-btn ts-btn-sm ts-btn-outline" id="btn-sa-preview" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">👁️ Preview Only</button>
      </div>
      <button type="submit" form="mailTemplateEditorForm" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 800; padding: 0.65rem 1.75rem;">
        <i data-lucide="save"></i> Save Template 💾
      </button>
    </div>
  </div>

  <form action="<?= url('/admin/mail/template/save') ?>" method="POST" id="mailTemplateEditorForm">
    <input type="hidden" name="id" value="<?= htmlspecialchars($template['id'] ?? '') ?>">

    <!-- Top Metadata Configuration Grid -->
    <div class="admin-table-wrap" style="padding: 1.5rem; margin-bottom: 1.25rem; background: rgba(15,23,42,0.85);">
      <h3 style="font-size: 1.05rem; color: #f8fafc; margin: 0 0 1rem 0; font-weight: 700; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.5rem;">
        ⚙️ Template Parameters &amp; Outbound Account Binding
      </h3>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Template Unique Key <span style="color: #ef4444;">*</span></label>
          <input type="text" name="template_key" value="<?= htmlspecialchars($template['template_key'] ?? '') ?>" placeholder="e.g. weekly_ai_roundup" class="admin-modal-input" style="font-family: monospace;" required>
        </div>

        <div>
          <label class="admin-modal-label">Display Name <span style="color: #ef4444;">*</span></label>
          <input type="text" name="name" value="<?= htmlspecialchars($template['name'] ?? '') ?>" placeholder="e.g. ⚡ Weekly AI Digest" class="admin-modal-input" required>
        </div>

        <div>
          <label class="admin-modal-label">Assigned Mail Outbound Account Profile</label>
          <select name="assigned_credential_id" class="admin-modal-select">
            <option value="">-- Platform Default Outbound Provider --</option>
            <?php foreach ($mailCredentials as $mc): ?>
              <option value="<?= $mc['id'] ?>" <?= (!empty($template['assigned_credential_id']) && (int)$template['assigned_credential_id'] === (int)$mc['id']) ? 'selected' : '' ?>>
                🔑 <?= htmlspecialchars($mc['name']) ?> (<?= htmlspecialchars($mc['from_email']) ?> &bull; <?= strtoupper($mc['provider']) ?>) <?= !empty($mc['is_default']) ? '★ [DEFAULT]' : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
        <div>
          <label class="admin-modal-label">Default Email Subject Line <span style="color: #ef4444;">*</span></label>
          <input type="text" name="subject" id="sa_subject_input" oninput="updateStandaloneLivePreview()" value="<?= htmlspecialchars($template['subject'] ?? '') ?>" placeholder="e.g. TechScout Intelligence Update: {{headline}}" class="admin-modal-input" required>
        </div>

        <div>
          <label class="admin-modal-label">Template Notes / Description</label>
          <input type="text" name="description" value="<?= htmlspecialchars($template['description'] ?? '') ?>" placeholder="e.g. Sent to users when new models drop..." class="admin-modal-input">
        </div>
      </div>
    </div>

    <!-- 2-COLUMN WORKSPACE: LEFT EDITOR & PALETTE + RIGHT LIVE IFRAME PREVIEW -->
    <div id="sa-workspace-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
      
      <!-- LEFT COLUMN: VARIABLE PALETTE, BLOCK COMPONENTS & CODE EDITOR -->
      <div id="sa-col-editor">
        <!-- 10 Categories Dynamic Tag Palette Accordion -->
        <details open style="margin-bottom: 0.85rem; background: #05070b; border: 1px solid rgba(168,85,247,0.3); border-radius: 8px; padding: 0.65rem 0.85rem;">
          <summary style="cursor: pointer; font-size: 0.82rem; font-weight: 700; color: #c084fc; display: flex; align-items: center; justify-content: space-between; user-select: none;">
            <span style="display: flex; align-items: center; gap: 0.4rem;">
              <i data-lucide="variable" style="width: 14px; height: 14px;"></i>
              Comprehensive Dynamic Variables Palette (10 Categories)
            </span>
            <span style="font-size: 0.7rem; color: #a855f7;">Click tag to insert ▾</span>
          </summary>

          <div style="margin-top: 0.75rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.75rem; max-height: 240px; overflow-y: auto;">
            <?php foreach ($allVariableCategories as $cat => $vars): ?>
              <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.1); border-radius: 6px; padding: 0.45rem;">
                <div style="font-size: 0.68rem; font-weight: 700; color: #c084fc; text-transform: uppercase; margin-bottom: 0.3rem; letter-spacing: 0.5px;"><?= htmlspecialchars($cat) ?></div>
                <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                  <?php foreach ($vars as $tag => $desc): ?>
                    <button type="button" onclick="insertVariable('<?= $tag ?>')" style="display: flex; justify-content: space-between; align-items: center; text-align: left; background: rgba(168,85,247,0.06); border: 1px solid rgba(168,85,247,0.2); padding: 0.18rem 0.4rem; border-radius: 4px; color: #cbd5e1; font-size: 0.68rem; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.background='rgba(168,85,247,0.2)'" onmouseout="this.style.background='rgba(168,85,247,0.06)'" title="Insert <?= $tag ?>: <?= htmlspecialchars($desc) ?>">
                      <code style="color: #c084fc; font-weight: 700;"><?= htmlspecialchars($tag) ?></code>
                      <span style="color: #94a3b8; font-size: 0.62rem; margin-left: 0.3rem; max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($desc) ?></span>
                    </button>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </details>

        <!-- Block Component Library Toolbar -->
        <div style="margin-bottom: 0.85rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-size: 0.8rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.35rem;">
              <i data-lucide="blocks" style="width: 15px; height: 15px; color: #6366f1;"></i> 
              <span>Universal Block Component Library</span>
            </div>
            <span style="font-size: 0.7rem; color: #64748b;">1-Click insert responsive email blocks</span>
          </div>
          <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
            <button type="button" onclick="insertBlock('hero')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.72rem; color: #4f46e5; border-color: #c7d2fe; background: #f5f3ff;">✨ Hero Header</button>
            <button type="button" onclick="insertBlock('callout_info')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.72rem; color: #0284c7; border-color: #bae6fd; background: #f0f9ff;">ℹ️ Info Callout</button>
            <button type="button" onclick="insertBlock('callout_success')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.72rem; color: #059669; border-color: #a7f3d0; background: #ecfdf5;">✅ Success Box</button>
            <button type="button" onclick="insertBlock('callout_warning')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.72rem; color: #d97706; border-color: #fde68a; background: #fffbeb;">⚠️ Warning Box</button>
            <button type="button" onclick="insertBlock('cta_primary')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.72rem; color: #4338ca; border-color: #c7d2fe; background: #eef2ff;">🔘 Primary CTA Button</button>
            <button type="button" onclick="insertBlock('cta_green')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.72rem; color: #047857; border-color: #a7f3d0; background: #ecfdf5;">💰 Disbursal Button</button>
            <button type="button" onclick="insertBlock('datagrid')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.72rem; color: #334155; border-color: #cbd5e1; background: #f8fafc;">📊 Key-Value Data Grid</button>
            <button type="button" onclick="insertBlock('otp_box')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.72rem; color: #4f46e5; border-color: #c7d2fe; background: #f8fafc;">🔑 OTP PIN Badge</button>
          </div>
        </div>

        <!-- HTML Code & Visual WYSIWYG Editor Workspace -->
        <div class="admin-table-wrap" style="padding: 1.25rem; background: rgba(15,23,42,0.85);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; gap: 0.35rem; align-items: center;">
              <button type="button" onclick="switchSaEditMode('wysiwyg')" id="btn-sa-mode-wysiwyg" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.75rem; background: #10b981; border-color: #10b981;">🎨 Visual WYSIWYG Editor</button>
              <button type="button" onclick="switchSaEditMode('html')" id="btn-sa-mode-html" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem;">💻 Raw HTML Code</button>
            </div>

            <div style="display: flex; gap: 0.35rem;">
              <select onchange="if(this.value){ insertVariable(this.value); this.value=''; }" class="admin-modal-select" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; color: #10b981; width: auto; max-width: 240px; font-weight: 700;">
                <option value="">⚡ Insert Dynamic Token... ▾</option>
                <?php foreach ($allVariableCategories as $vCat => $vList): ?>
                  <optgroup label="<?= htmlspecialchars($vCat) ?>">
                    <?php foreach ($vList as $vTag => $vDesc): ?>
                      <option value="<?= htmlspecialchars($vTag) ?>"><?= htmlspecialchars($vTag) ?> - <?= htmlspecialchars($vDesc) ?></option>
                    <?php endforeach; ?>
                  </optgroup>
                <?php endforeach; ?>
              </select>
              <button type="button" onclick="loadStarterBoilerplate()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #38bdf8;">
                Boilerplate ⚡
              </button>
            </div>
          </div>

          <!-- WYSIWYG Formatting Buttons Toolbar -->
          <div id="sa_wysiwyg_toolbar" style="display: flex; flex-wrap: wrap; gap: 0.25rem; align-items: center; background: #05070b; padding: 0.4rem; border-radius: 6px; border: 1px solid rgba(148,163,184,0.15); margin-bottom: 0.5rem;">
            <button type="button" onclick="execSaWysiwyg('bold')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.75rem; font-weight: 800;" title="Bold"><b>B</b></button>
            <button type="button" onclick="execSaWysiwyg('italic')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.75rem; font-style: italic;" title="Italic"><i>I</i></button>
            <button type="button" onclick="execSaWysiwyg('underline')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.75rem; text-decoration: underline;" title="Underline"><u>U</u></button>
            <button type="button" onclick="execSaWysiwyg('strikeThrough')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.75rem; text-decoration: line-through;" title="Strikethrough"><s>S</s></button>
            <span style="color: rgba(148,163,184,0.3);">|</span>
            <select onchange="execSaWysiwyg('formatBlock', this.value); this.value='';" class="admin-modal-select" style="padding: 0.15rem 0.4rem; font-size: 0.72rem; width: auto; background: #0b0f19;">
              <option value="">Header ▾</option>
              <option value="H1">H1 Main Heading</option>
              <option value="H2">H2 Sub Heading</option>
              <option value="H3">H3 Section Title</option>
              <option value="P">Paragraph</option>
              <option value="BLOCKQUOTE">Blockquote</option>
            </select>
            <span style="color: rgba(148,163,184,0.3);">|</span>
            <button type="button" onclick="execSaWysiwyg('justifyLeft')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem;" title="Align Left">⬅️</button>
            <button type="button" onclick="execSaWysiwyg('justifyCenter')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem;" title="Align Center">↔️</button>
            <button type="button" onclick="execSaWysiwyg('justifyRight')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem;" title="Align Right">➡️</button>
            <span style="color: rgba(148,163,184,0.3);">|</span>
            <button type="button" onclick="execSaWysiwyg('insertUnorderedList')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem;" title="Bullet List">• List</button>
            <button type="button" onclick="execSaWysiwyg('insertOrderedList')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem;" title="Numbered List">1. List</button>
            <span style="color: rgba(148,163,184,0.3);">|</span>
            <button type="button" onclick="promptSaLink()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.4rem; font-size: 0.72rem; color: #38bdf8;" title="Insert Link">🔗 Link</button>
            <button type="button" onclick="insertSaCtaButton()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.4rem; font-size: 0.72rem; color: #10b981;" title="Insert Primary CTA Button">🔘 Button</button>
            <button type="button" onclick="execSaWysiwyg('removeFormat')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.35rem; font-size: 0.72rem; color: #ef4444;" title="Clear Formatting">🧹 Clear</button>
          </div>

          <!-- Visual WYSIWYG Editable Canvas -->
          <div id="sa_wysiwyg_container" style="background: #ffffff; color: #0f172a; border: 1px solid rgba(16,185,129,0.3); border-radius: 8px; padding: 1.25rem; min-height: 440px; max-height: 520px; overflow-y: auto;">
            <div id="sa_wysiwyg_editor" contenteditable="true" oninput="syncSaWysiwygToCode()" style="outline: none; min-height: 420px; font-family: 'Inter', sans-serif; font-size: 0.95rem; line-height: 1.6;">
              <?= $template ? ($template['html_content'] ?? ($template['html_body'] ?? '')) : '<h2 style="color:#0f172a;">Hello {{name}}!</h2><p>Start composing your email content visually...</p>' ?>
            </div>
          </div>

          <textarea name="html_content" id="templateHtmlBody" oninput="syncSaCodeToWysiwyg()" rows="22" class="admin-modal-input" style="display: none; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; line-height: 1.5; white-space: pre; background: #05070b; border-color: rgba(99,102,241,0.35); height: 480px; resize: vertical;" placeholder="<h2 style='color:#0f172a;'>Hello {{name}}!</h2><p>{{message}}</p>" required><?= htmlspecialchars($template['html_content'] ?? ($template['html_body'] ?? '')) ?></textarea>
        </div>
      </div>

      <!-- RIGHT COLUMN: REAL-TIME LIVE EMAIL PREVIEW IFRAME -->
      <div id="sa-col-preview">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <label class="admin-modal-label" style="margin: 0; color: #38bdf8; display: flex; align-items: center; gap: 0.35rem;">
            <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Live Delivery Simulation Canvas (Light Mode)
          </label>
          <span style="font-size: 0.72rem; color: #64748b;">Recipient: Alex Rivera</span>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; height: 680px; display: flex; flex-direction: column; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
          <!-- Simulated Email Client Header -->
          <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 0.6rem 0.85rem; font-size: 0.78rem;">
            <div style="color: #0f172a; font-weight: 700;" id="saSimSubject">Subject: TechScout Intelligence Update</div>
            <div style="color: #64748b; font-size: 0.7rem; margin-top: 0.15rem;">
              From: <strong style="color: #334155;">TechScout Intelligence</strong> &lt;notifications@techscout.online&gt; &bull; To: alex@example.com
            </div>
          </div>

          <!-- Rendered Iframe (Pure Light Mode Canvas with Grid Lines) -->
          <iframe id="standaloneLivePreviewFrame" style="width: 100%; height: 100%; border: none; background: #ffffff; flex: 1;"></iframe>
        </div>
      </div>
    </div>

    <!-- Bottom Action Bar -->
    <div style="padding: 1.25rem 2rem; background: rgba(15,23,42,0.95); border: 1px solid rgba(16,185,129,0.4); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 3rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
      <div>
        <div style="font-weight: 700; color: #f8fafc; font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="check-circle" style="color: #10b981;"></i> Ready to deploy HTML email template?
        </div>
        <div style="font-size: 0.82rem; color: #94a3b8; margin-top: 0.2rem;">
          Template edits take effect instantly across platform triggers and broadcast composers.
        </div>
      </div>

      <div style="display: flex; gap: 0.75rem; align-items: center;">
        <a href="<?= url('/admin/mail?tab=templates') ?>" class="ts-btn ts-btn-outline" style="color: #94a3b8;">
          Cancel &amp; Return
        </a>
        <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 800; padding: 0.75rem 2.25rem; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(16,185,129,0.35);">
          <i data-lucide="save"></i> Save Template 💾
        </button>
      </div>
    </div>
  </form>
</div>

<script>
function insertVariable(tag) {
  const textarea = document.getElementById('templateHtmlBody');
  if (!textarea) return;
  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const text = textarea.value;
  textarea.value = text.substring(0, start) + tag + text.substring(end);
  textarea.selectionStart = textarea.selectionEnd = start + tag.length;
  textarea.focus();
  updateStandaloneLivePreview();
}

function insertBlock(type) {
  const textarea = document.getElementById('templateHtmlBody');
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
  updateStandaloneLivePreview();
}

function updateStandaloneLivePreview() {
  const textarea = document.getElementById('templateHtmlBody');
  const subjectInput = document.getElementById('sa_subject_input');
  const simSubject = document.getElementById('saSimSubject');
  const frame = document.getElementById('standaloneLivePreviewFrame');

  if (!textarea || !frame) return;

  let subject = (subjectInput && subjectInput.value) ? subjectInput.value : 'TechScout Intelligence Update';
  if (simSubject) simSubject.textContent = 'Subject: ' + subject;

  let rawHtml = textarea.value || '';
  let renderedHtml = rawHtml
    .replace(/\{\{name\}\}/g, 'Alex Rivera')
    .replace(/\{\{username\}\}/g, 'alex_dev')
    .replace(/\{\{action_url\}\}/g, window.location.origin)
    .replace(/\{\{headline\}\}/g, subject)
    .replace(/\{\{message\}\}/g, 'This is simulated dynamic message body content previewed live in real-time...')
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
    <div style="font-size:22px;font-weight:900;color:#0f172a;letter-spacing:-0.5px;">Tech<span style="color:#6366f1;">Scout</span> <span style="font-size:11px;background:#6366f1;color:#ffffff;padding:2px 7px;border-radius:4px;vertical-align:middle;margin-left:4px;letter-spacing:1px;font-weight:700;">EDITOR PREVIEW</span></div>
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

function setStandaloneViewMode(mode) {
  const colEditor = document.getElementById('sa-col-editor');
  const colPreview = document.getElementById('sa-col-preview');
  const grid = document.getElementById('sa-workspace-grid');

  document.getElementById('btn-sa-split').className = 'ts-btn ts-btn-sm ts-btn-outline';
  document.getElementById('btn-sa-editor').className = 'ts-btn ts-btn-sm ts-btn-outline';
  document.getElementById('btn-sa-preview').className = 'ts-btn ts-btn-sm ts-btn-outline';
  document.getElementById(`btn-sa-${mode}`).className = 'ts-btn ts-btn-sm ts-btn-primary';

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

function loadStarterBoilerplate() {
  const current = document.getElementById('templateHtmlBody').value.trim();
  if (current && !confirm('Replace current editor contents with modern email boilerplate?')) {
    return;
  }
  document.getElementById('templateHtmlBody').value = `<h2 style="color:#0f172a;font-size:22px;font-weight:800;margin:0 0 16px 0;">{{headline}}</h2>
<p style="color:#334155;font-size:15px;line-height:1.65;margin:0 0 20px 0;">Hello <strong>{{name}}</strong>, here is your customized intelligence update.</p>
<div style="background:#f0f9ff;border:1px solid #bae6fd;border-left:4px solid #0284c7;border-radius:8px;padding:16px;color:#0369a1;font-size:14px;margin:20px 0;">
  <strong>💡 Notice:</strong> {{message}}
</div>
<div style="text-align:center;margin:28px 0;">
  <a href="{{action_url}}" style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#2563eb);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;">Access Dashboard &rarr;</a>
</div>`;
  updateStandaloneLivePreview();
}

document.addEventListener('DOMContentLoaded', () => {
  updateStandaloneLivePreview();
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>


<div class="admin-content-inner" style="max-width: 1400px; margin: 0 auto;">
  <!-- Page Header Strip -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1.25rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <a href="<?= url('/admin/mail') ?>" style="color: #38bdf8; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
          &larr; Back to Mail &amp; Template Studio
        </a>
        <span style="color: #64748b;">&bull;</span>
        <span class="ts-badge ts-badge-purple" style="font-size: 0.68rem; font-weight: 800;">UNCONSTRAINED FULL-PAGE CANVAS</span>
      </div>
      <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        <?= $template ? '✏️ Edit Template: ' . htmlspecialchars($template['name']) : '➕ Create New HTML Email Template' ?>
      </h1>
      <p style="color: #94a3b8; font-size: 0.88rem; margin: 0.25rem 0 0 0;">
        Full-screen scrollable workspace for drafting responsive HTML email layouts, dynamic parameters, and direct credential bindings.
      </p>
    </div>

    <div style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">
      <button type="button" onclick="previewStandaloneHtml()" class="ts-btn ts-btn-outline" style="color: #38bdf8; border-color: rgba(56,189,248,0.35); font-weight: 700;">
        <i data-lucide="eye"></i> Live HTML Preview 👁️
      </button>
      <button type="submit" form="mailTemplateEditorForm" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 800; padding: 0.6rem 1.75rem;">
        <i data-lucide="save"></i> Save Template 💾
      </button>
    </div>
  </div>

  <form action="<?= url('/admin/mail/template/save') ?>" method="POST" id="mailTemplateEditorForm">
    <input type="hidden" name="id" value="<?= htmlspecialchars($template['id'] ?? '') ?>">

    <!-- Top Metadata Configuration Grid -->
    <div class="admin-table-wrap" style="padding: 1.75rem; margin-bottom: 1.75rem; background: rgba(15,23,42,0.85);">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0; font-weight: 700; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.75rem;">
        ⚙️ Template Parameters &amp; Outbound Account
      </h3>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label class="admin-modal-label">Template Unique Key <span style="color: #ef4444;">*</span></label>
          <input type="text" name="template_key" value="<?= htmlspecialchars($template['template_key'] ?? '') ?>" placeholder="e.g. weekly_ai_roundup" class="admin-modal-input" style="font-family: monospace;" required>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Used by backend triggers and Mailer dispatch calls.</div>
        </div>

        <div>
          <label class="admin-modal-label">Display Name <span style="color: #ef4444;">*</span></label>
          <input type="text" name="name" value="<?= htmlspecialchars($template['name'] ?? '') ?>" placeholder="e.g. ⚡ Weekly AI Digest" class="admin-modal-input" required>
        </div>

        <div>
          <label class="admin-modal-label">Assigned Mail Outbound Account</label>
          <select name="assigned_credential_id" class="admin-modal-select">
            <option value="">-- Platform Default Outbound Provider --</option>
            <?php foreach ($mailCredentials as $mc): ?>
              <option value="<?= $mc['id'] ?>" <?= (!empty($template['assigned_credential_id']) && (int)$template['assigned_credential_id'] === (int)$mc['id']) ? 'selected' : '' ?>>
                🔑 <?= htmlspecialchars($mc['name']) ?> (<?= htmlspecialchars($mc['from_email']) ?> &bull; <?= strtoupper($mc['provider']) ?>) <?= !empty($mc['is_default']) ? '★ [DEFAULT]' : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Manual binding for this template.</div>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem;">
        <div>
          <label class="admin-modal-label">Default Email Subject Line <span style="color: #ef4444;">*</span></label>
          <input type="text" name="subject" value="<?= htmlspecialchars($template['subject'] ?? '') ?>" placeholder="e.g. TechScout Intelligence Update: {{headline}}" class="admin-modal-input" required>
        </div>

        <div>
          <label class="admin-modal-label">Template Notes / Description</label>
          <input type="text" name="description" value="<?= htmlspecialchars($template['description'] ?? '') ?>" placeholder="e.g. Sent to users when new models drop..." class="admin-modal-input">
        </div>
      </div>
    </div>

    <!-- Dynamic Tag Palette Bar -->
    <div style="background: rgba(13,19,34,0.9); border: 1px solid rgba(168,85,247,0.3); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.75rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <span style="font-size: 0.85rem; font-weight: 700; color: #c084fc; display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="variable" style="width: 16px; height: 16px;"></i> Click-to-Copy Dynamic Variables Palette
        </span>
        <span style="font-size: 0.75rem; color: #94a3b8;">Click any variable pill to copy into clipboard or insert at cursor:</span>
      </div>

      <div style="display: flex; flex-wrap: wrap; gap: 0.45rem;">
        <?php 
          $sampleVars = [
            '{{name}}' => 'Recipient Name',
            '{{email}}' => 'Recipient Email',
            '{{action_url}}' => 'Primary Call-to-Action Link',
            '{{headline}}' => 'Dynamic Broadcast Headline',
            '{{message}}' => 'Body Paragraph Content',
            '{{verification_code}}' => '6-Digit Auth OTP PIN',
            '{{reset_url}}' => 'Password Reset Secure Link',
            '{{amount}}' => 'Payout / Reward Amount',
            '{{article_title}}' => 'Publication Title',
            '{{site_name}}' => 'TechScout Platform Name',
            '{{year}}' => 'Current Year (e.g. 2026)'
          ];
          foreach ($sampleVars as $tag => $label):
        ?>
          <button type="button" onclick="insertVariable('<?= $tag ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-family: monospace; font-size: 0.75rem; padding: 0.25rem 0.6rem; border-color: rgba(168,85,247,0.3); color: #c084fc;" title="<?= $label ?>">
            <?= $tag ?> <span style="opacity: 0.7; font-family: sans-serif; font-size: 0.68rem;">(<?= $label ?>)</span>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Full-Screen Scrollable HTML Code Editor -->
    <div class="admin-table-wrap" style="padding: 1.75rem; margin-bottom: 2rem; background: rgba(15,23,42,0.85);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="code-2" style="color: #38bdf8;"></i> Responsive HTML Template Body
          </h3>
          <p style="font-size: 0.8rem; color: #94a3b8; margin: 0.2rem 0 0 0;">
            Completely unconstrained height — write full table layouts with inline CSS, responsive styles, and web-safe fonts.
          </p>
        </div>

        <div style="display: flex; gap: 0.5rem;">
          <button type="button" onclick="loadStarterBoilerplate()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; color: #38bdf8;">
            Insert Modern Dark Boilerplate ⚡
          </button>
        </div>
      </div>

      <textarea name="html_body" id="templateHtmlBody" rows="32" class="admin-modal-input" style="font-family: 'JetBrains Mono', monospace; font-size: 0.9rem; line-height: 1.6; white-space: pre; background: #05070b; border-color: rgba(99,102,241,0.35); min-height: 550px; resize: vertical;" placeholder="<!DOCTYPE html><html><head>...</head><body>...</body></html>" required><?= htmlspecialchars($template['html_body'] ?? '') ?></textarea>
    </div>

    <!-- Bottom Action Bar (Sticky or Scrollable) -->
    <div style="padding: 1.5rem 2rem; background: rgba(15,23,42,0.95); border: 1px solid rgba(16,185,129,0.4); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 3rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
      <div>
        <div style="font-weight: 700; color: #f8fafc; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="check-circle" style="color: #10b981;"></i> Ready to deploy email template?
        </div>
        <div style="font-size: 0.82rem; color: #94a3b8; margin-top: 0.2rem;">
          Changes are immediately available to the automated system and custom broadcast composer.
        </div>
      </div>

      <div style="display: flex; gap: 0.75rem; align-items: center;">
        <a href="<?= url('/admin/mail') ?>" class="ts-btn ts-btn-outline" style="color: #94a3b8;">
          Cancel &amp; Return
        </a>
        <button type="button" onclick="previewStandaloneHtml()" class="ts-btn ts-btn-outline" style="color: #38bdf8; border-color: rgba(56,189,248,0.4);">
          Preview 👁️
        </button>
        <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 800; padding: 0.75rem 2.25rem; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(16,185,129,0.35);">
          <i data-lucide="save"></i> Save Template 💾
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Modal for Live Preview in New Window / Floating Drawer -->
<div id="fullPreviewModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-box" style="max-width: 820px; width: 95vw; height: 90vh; display: flex; flex-direction: column; background: #0b0f19; border: 1px solid rgba(56,189,248,0.4);">
    <div class="admin-modal-header" style="padding: 1rem 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); display: flex; justify-content: space-between; align-items: center;">
      <div style="font-weight: 700; color: #f8fafc; font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="monitor" style="color: #38bdf8;"></i> Responsive Email Sandbox Preview
      </div>
      <button type="button" onclick="document.getElementById('fullPreviewModal').style.display = 'none'" class="admin-modal-close" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">✕</button>
    </div>
    <div style="flex: 1; background: #ffffff; padding: 0;">
      <iframe id="previewIframe" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>
  </div>
</div>

<script>
function insertVariable(tag) {
  const textarea = document.getElementById('templateHtmlBody');
  if (!textarea) return;
  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const text = textarea.value;
  textarea.value = text.substring(0, start) + tag + text.substring(end);
  textarea.selectionStart = textarea.selectionEnd = start + tag.length;
  textarea.focus();
}

function previewStandaloneHtml() {
  const html = document.getElementById('templateHtmlBody').value;
  const iframe = document.getElementById('previewIframe');
  const modal = document.getElementById('fullPreviewModal');
  
  // Sample interpolation for preview
  const interpolated = html
    .replace(/\{\{name\}\}/g, 'Alex Rivers')
    .replace(/\{\{email\}\}/g, 'alex.rivers@example.com')
    .replace(/\{\{action_url\}\}/g, 'https://techscout.online/verify?token=sample_demo')
    .replace(/\{\{headline\}\}/g, 'DeepSeek-V3 Benchmark Telemetry Released')
    .replace(/\{\{message\}\}/g, 'This is a live rendered preview of your email template with simulated variables.')
    .replace(/\{\{verification_code\}\}/g, '849201')
    .replace(/\{\{amount\}\}/g, '$145.00 USD')
    .replace(/\{\{article_title\}\}/g, 'Optimizing High-Throughput Relational Systems')
    .replace(/\{\{site_name\}\}/g, 'TechScout')
    .replace(/\{\{year\}\}/g, '2026');

  modal.style.display = 'flex';
  iframe.contentWindow.document.open();
  iframe.contentWindow.document.write(interpolated);
  iframe.contentWindow.document.close();
}

function loadStarterBoilerplate() {
  const current = document.getElementById('templateHtmlBody').value.trim();
  if (current && !confirm('Replace current editor contents with modern dark email boilerplate?')) {
    return;
  }
  document.getElementById('templateHtmlBody').value = `<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{headline}}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #030712; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #f9fafb;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #030712; padding: 40px 20px;">
    <tr>
      <td align="center">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #0f172a; border-radius: 16px; border: 1px solid #1e293b; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
          <!-- Header Banner -->
          <tr>
            <td style="padding: 32px 40px; background: linear-gradient(135deg, #1e1b4b, #0f172a); border-bottom: 1px solid #312e81;">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td>
                    <div style="font-size: 20px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                      TechScout <span style="color: #38bdf8;">HQ</span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px;">
                      High-Performance Engineering Platform
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Main Body -->
          <tr>
            <td style="padding: 40px;">
              <h1 style="margin: 0 0 16px 0; font-size: 22px; font-weight: 700; color: #f8fafc; line-height: 1.3;">
                {{headline}}
              </h1>
              
              <p style="margin: 0 0 24px 0; font-size: 15px; line-height: 1.6; color: #94a3b8;">
                Hello {{name}},
              </p>
              
              <p style="margin: 0 0 24px 0; font-size: 15px; line-height: 1.6; color: #cbd5e1;">
                {{message}}
              </p>

              <!-- Call to Action Button -->
              <table border="0" cellspacing="0" cellpadding="0" style="margin: 32px 0;">
                <tr>
                  <td align="center" style="border-radius: 8px; background: linear-gradient(135deg, #38bdf8, #6366f1);">
                    <a href="{{action_url}}" target="_blank" style="font-size: 14px; font-weight: 700; color: #ffffff; text-decoration: none; padding: 14px 28px; display: inline-block;">
                      Access Platform &rarr;
                    </a>
                  </td>
                </tr>
              </table>

              <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #64748b;">
                If you did not initiate this request, you can safely ignore this automated dispatch.
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding: 24px 40px; background-color: #090d16; border-top: 1px solid #1e293b; text-align: center;">
              <p style="margin: 0; font-size: 12px; color: #64748b;">
                &copy; {{year}} TechScout Platform. All rights reserved.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>`;
}

// Standalone Visual WYSIWYG Editor Sync Functions
function switchSaEditMode(mode) {
  const wysiwygCont = document.getElementById('sa_wysiwyg_container');
  const wysiwygTool = document.getElementById('sa_wysiwyg_toolbar');
  const htmlInput = document.getElementById('templateHtmlBody');
  const btnWysiwyg = document.getElementById('btn-sa-mode-wysiwyg');
  const btnHtml = document.getElementById('btn-sa-mode-html');

  if (mode === 'wysiwyg') {
    syncSaCodeToWysiwyg();
    if (wysiwygCont) wysiwygCont.style.display = 'block';
    if (wysiwygTool) wysiwygTool.style.display = 'flex';
    if (htmlInput) htmlInput.style.display = 'none';
    if (btnWysiwyg) { btnWysiwyg.className = 'ts-btn ts-btn-sm ts-btn-primary'; btnWysiwyg.style.background='#10b981'; }
    if (btnHtml) { btnHtml.className = 'ts-btn ts-btn-sm ts-btn-outline'; btnHtml.style.background='transparent'; }
  } else {
    syncSaWysiwygToCode();
    if (wysiwygCont) wysiwygCont.style.display = 'none';
    if (wysiwygTool) wysiwygTool.style.display = 'none';
    if (htmlInput) htmlInput.style.display = 'block';
    if (btnWysiwyg) { btnWysiwyg.className = 'ts-btn ts-btn-sm ts-btn-outline'; btnWysiwyg.style.background='transparent'; }
    if (btnHtml) { btnHtml.className = 'ts-btn ts-btn-sm ts-btn-primary'; btnHtml.style.background='#10b981'; }
  }
}

function execSaWysiwyg(cmd, value = null) {
  document.execCommand(cmd, false, value);
  syncSaWysiwygToCode();
}

function syncSaWysiwygToCode() {
  const wysiwyg = document.getElementById('sa_wysiwyg_editor');
  const htmlInput = document.getElementById('templateHtmlBody');
  if (wysiwyg && htmlInput) {
    htmlInput.value = wysiwyg.innerHTML;
    if (typeof updateStandaloneLivePreview === 'function') {
      updateStandaloneLivePreview();
    }
  }
}

function syncSaCodeToWysiwyg() {
  const wysiwyg = document.getElementById('sa_wysiwyg_editor');
  const htmlInput = document.getElementById('templateHtmlBody');
  if (wysiwyg && htmlInput) {
    wysiwyg.innerHTML = htmlInput.value || '<p>Start typing template content...</p>';
    if (typeof updateStandaloneLivePreview === 'function') {
      updateStandaloneLivePreview();
    }
  }
}

function promptSaLink() {
  const url = prompt('Enter URL link:', 'https://');
  if (url) execSaWysiwyg('createLink', url);
}

function insertSaCtaButton() {
  const btnHtml = '<div style="margin:20px 0; text-align:center;"><a href="{{action_url}}" style="display:inline-block; background:linear-gradient(135deg,#10b981,#059669); color:#ffffff; padding:12px 28px; border-radius:8px; font-weight:bold; text-decoration:none; font-size:15px; box-shadow:0 4px 12px rgba(16,185,129,0.3);">Primary Call-to-Action Button &rarr;</a></div>';
  const wysiwyg = document.getElementById('sa_wysiwyg_editor');
  if (wysiwyg) {
    wysiwyg.focus();
    document.execCommand('insertHTML', false, btnHtml);
    syncSaWysiwygToCode();
  }
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
