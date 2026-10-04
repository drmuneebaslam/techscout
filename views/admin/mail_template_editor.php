<?php
$title = ($template ? 'Edit Template: ' . htmlspecialchars($template['name'] ?? '') : 'Create Custom HTML Email Template') . ' | TechScout Mail Studio';
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
        '{{mentioner_name}}' => 'User who mentioned recipient in discussion',
        '{{follower_name}}' => 'New user following author profile',
        '{{collection_title}}' => 'Title of shared bookmark stack or technical blueprint',
        '{{sender_name}}' => 'Sender name sharing resource stack'
    ],
    'Campaigns & Hackathons' => [
        '{{campaign_title}}' => 'Title of AI Hackathon or Developer Sprint',
        '{{project_title}}' => 'Title of submitted project entry',
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
        '{{creator_name}}' => 'Author receiving reader tip or patronage',
        '{{creator_tier}}' => 'Creator Guild status tier (Rising, Partner, Elite)',
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
        '{{device_name}}' => 'Recognized client device model or browser',
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

<!-- Embedded Notification Toast Container -->
<div id="saToastBox" style="flex-wrap: wrap; align-items: center; position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 8px; pointer-events: none;"></div>

<div class="admin-content-inner mail-studio-shell">
  <!-- Page Header Strip -->
  <div class="mail-studio-header-strip">
    <div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <a href="<?= url('/admin/mail?tab=templates') ?>" class="mail-studio-back-link">
          &larr; Back to Mail &amp; Template Studio
        </a>
        <span style="opacity: 0.5;">&bull;</span>
        <span class="ts-badge ts-badge-purple">FULL-SCREEN TEMPLATE STUDIO</span>
      </div>
      <h1 class="mail-studio-page-title">
        <?= $template ? '✏️ Edit Template: ' . htmlspecialchars($template['name'] ?? '') : '➕ Create Custom HTML Email Template' ?>
      </h1>
      <p class="mail-studio-page-subtitle">
        Responsive email studio with 1-click blocks, dynamic token insertion, and real-time live light delivery preview.
      </p>
    </div>

    <div style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">
      <div class="mail-studio-toolbar-cluster">
        <button type="button" onclick="setStandaloneViewMode('split')" class="ts-btn ts-btn-sm ts-btn-primary" id="btn-sa-split">🌓 Split View</button>
        <button type="button" onclick="setStandaloneViewMode('editor')" class="ts-btn ts-btn-sm ts-btn-outline" id="btn-sa-editor">📝 Editor Only</button>
        <button type="button" onclick="setStandaloneViewMode('preview')" class="ts-btn ts-btn-sm ts-btn-outline" id="btn-sa-preview">👁️ Preview Only</button>
      </div>
      <button type="submit" form="mailTemplateEditorForm" class="ts-btn ts-btn-primary" style="font-weight: 800; padding: 0.65rem 1.75rem;">
        <i data-lucide="save"></i> Save Template 💾
      </button>
    </div>
  </div>

  <form action="<?= url('/admin/mail/template/save') ?>" method="POST" id="mailTemplateEditorForm">
    <input type="hidden" name="id" value="<?= htmlspecialchars($template['id'] ?? '') ?>">

    <!-- Top Metadata Configuration Card -->
    <div class="mail-studio-card">
      <h3 class="mail-studio-section-title">
        <span>⚙️</span> Template Configuration &amp; Credential Binding
      </h3>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Template Unique Key <span style="color: #ef4444;">*</span></label>
          <input type="text" name="template_key" value="<?= htmlspecialchars($template['template_key'] ?? '') ?>" placeholder="e.g. weekly_ai_roundup" class="admin-modal-input" style="font-family: var(--admin-font-mono);" required>
        </div>

        <div>
          <label class="admin-modal-label">Display Name <span style="color: #ef4444;">*</span></label>
          <input type="text" name="name" value="<?= htmlspecialchars($template['name'] ?? '') ?>" placeholder="e.g. ⚡ Weekly AI Digest" class="admin-modal-input" required>
        </div>

        <div>
          <label class="admin-modal-label">Assigned Outbound Sender Account</label>
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
          <input type="text" name="description" value="<?= htmlspecialchars($template['description'] ?? '') ?>" placeholder="e.g. Dispatched when new models drop..." class="admin-modal-input">
        </div>
      </div>
    </div>

    <!-- 2-COLUMN WORKSPACE: LEFT EDITOR & PALETTE + RIGHT LIVE IFRAME PREVIEW -->
    <div id="sa-workspace-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
      
      <!-- LEFT COLUMN: VARIABLE PALETTE, BLOCK COMPONENTS & CODE EDITOR -->
      <div id="sa-col-editor">
        
        <!-- 10 Categories Dynamic Tag Palette Accordion -->
        <details open class="mail-studio-palette-accordion">
          <summary class="mail-studio-palette-summary">
            <span style="display: flex; align-items: center; gap: 0.45rem;">
              <i data-lucide="variable" style="width: 15px; height: 15px;"></i>
              Dynamic Variables Palette (10 Categories &bull; 60+ Tokens)
            </span>
            <span style="font-size: 0.72rem; font-weight: 600;">Click token to insert into caret ▾</span>
          </summary>

          <div style="margin-top: 0.75rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; border-top: 1px solid var(--admin-border-subtle); padding-top: 0.75rem; max-height: 230px; overflow-y: auto;">
            <?php foreach ($allVariableCategories as $cat => $vars): ?>
              <div class="mail-studio-palette-cat">
                <div class="mail-studio-cat-title"><?= htmlspecialchars($cat) ?></div>
                <div style="flex-wrap: wrap; align-items: center; display: flex; flex-direction: column; gap: 0.25rem;">
                  <?php foreach ($vars as $tag => $desc): ?>
                    <button type="button" onclick="insertVariable('<?= $tag ?>')" class="mail-studio-token-btn" title="Click to insert <?= $tag ?>: <?= htmlspecialchars($desc) ?>">
                      <code class="mail-studio-token-code"><?= htmlspecialchars($tag) ?></code>
                      <span style="font-size: 0.62rem; opacity: 0.8; margin-left: 0.35rem; max-width: 125px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($desc) ?></span>
                    </button>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </details>

        <!-- Modern Block Component Library (14 Responsive Email Blocks) -->
        <div class="mail-studio-blocks-card">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.55rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="flex-wrap: wrap; align-items: center; font-size: 0.82rem; font-weight: 800; color: var(--admin-text-primary); display: flex; align-items: center; gap: 0.4rem;">
              <i data-lucide="blocks" style="width: 16px; height: 16px; color: var(--admin-primary);"></i> 
              <span>1-Click Universal Responsive Email Blocks</span>
            </div>
            <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; font-weight: 700;">⚡ 14 Blocks</span>
          </div>
          
          <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
            <button type="button" onclick="insertBlock('hero')" class="mail-studio-block-btn" title="Insert Responsive Hero Header Banner">✨ Hero Header</button>
            <button type="button" onclick="insertBlock('cta_primary')" class="mail-studio-block-btn" title="Insert Primary Deep Indigo Action CTA">🔘 Primary CTA</button>
            <button type="button" onclick="insertBlock('cta_green')" class="mail-studio-block-btn" title="Insert Emerald Disbursal / Financial CTA">💰 Disbursal CTA</button>
            <button type="button" onclick="insertBlock('cta_secondary')" class="mail-studio-block-btn" title="Insert Light Outline Action Button">🔗 Outline Button</button>
            <button type="button" onclick="insertBlock('callout_info')" class="mail-studio-block-btn" title="Insert Sky Blue Technical Info Box">ℹ️ Info Callout</button>
            <button type="button" onclick="insertBlock('callout_success')" class="mail-studio-block-btn" title="Insert Mint Verified Success Box">✅ Success Box</button>
            <button type="button" onclick="insertBlock('callout_warning')" class="mail-studio-block-btn" title="Insert Warm Attention Warning Box">⚠️ Warning Box</button>
            <button type="button" onclick="insertBlock('callout_critical')" class="mail-studio-block-btn" title="Insert Urgent Security Alert Box">🚨 Critical Alert</button>
            <button type="button" onclick="insertBlock('datagrid')" class="mail-studio-block-btn" title="Insert 2-Column Responsive Data Grid">📊 Data Grid</button>
            <button type="button" onclick="insertBlock('otp_box')" class="mail-studio-block-btn" title="Insert Large Monospace OTP Verification Badge">🔑 OTP PIN Badge</button>
            <button type="button" onclick="insertBlock('two_col_card')" class="mail-studio-block-btn" title="Insert 2-Column Stat Cards">⚖️ 2-Col Cards</button>
            <button type="button" onclick="insertBlock('quote')" class="mail-studio-block-btn" title="Insert Formatted Community Quote">💬 Quote</button>
            <button type="button" onclick="insertBlock('checklist')" class="mail-studio-block-btn" title="Insert Styled Checklist">📋 Checklist</button>
            <button type="button" onclick="insertBlock('divider')" class="mail-studio-block-btn" title="Insert Elegant Section Divider">➖ Divider</button>
          </div>
        </div>

        <!-- HTML Code & Visual WYSIWYG Editor Workspace -->
        <div class="mail-studio-editor-box">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.6rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; align-items: center;">
              <button type="button" onclick="switchSaEditMode('wysiwyg')" id="btn-sa-mode-wysiwyg" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.75rem;">🎨 Visual WYSIWYG Editor</button>
              <button type="button" onclick="switchSaEditMode('html')" id="btn-sa-mode-html" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem;">💻 Raw HTML Code</button>
            </div>

            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.4rem; align-items: center;">
              <select onchange="if(this.value){ insertVariable(this.value); this.value=''; }" class="admin-modal-select" style="padding: 0.25rem 0.5rem; font-size: 0.72rem; width: auto; max-width: 220px; font-weight: 700;">
                <option value="">⚡ Quick Token Insert... ▾</option>
                <?php foreach ($allVariableCategories as $vCat => $vList): ?>
                  <optgroup label="<?= htmlspecialchars($vCat) ?>">
                    <?php foreach ($vList as $vTag => $vDesc): ?>
                      <option value="<?= htmlspecialchars($vTag) ?>"><?= htmlspecialchars($vTag) ?> - <?= htmlspecialchars($vDesc) ?></option>
                    <?php endforeach; ?>
                  </optgroup>
                <?php endforeach; ?>
              </select>
              <button type="button" onclick="loadStarterBoilerplate()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; font-weight: 700;" title="Load Clean White Responsive Email Starter Boilerplate">
                ⚡ Light Boilerplate
              </button>
            </div>
          </div>

          <!-- WYSIWYG Formatting Buttons Toolbar -->
          <div id="sa_wysiwyg_toolbar" class="mail-studio-wysiwyg-bar">
            <button type="button" onclick="execSaWysiwyg('bold')" class="mail-studio-action-btn" title="Bold"><b>B</b></button>
            <button type="button" onclick="execSaWysiwyg('italic')" class="mail-studio-action-btn" style="font-style: italic;" title="Italic"><i>I</i></button>
            <button type="button" onclick="execSaWysiwyg('underline')" class="mail-studio-action-btn" style="text-decoration: underline;" title="Underline"><u>U</u></button>
            <button type="button" onclick="execSaWysiwyg('strikeThrough')" class="mail-studio-action-btn" style="text-decoration: line-through;" title="Strikethrough"><s>S</s></button>
            <span style="opacity: 0.3;">|</span>
            <select onchange="execSaWysiwyg('formatBlock', this.value); this.value='';" class="admin-modal-select" style="padding: 0.15rem 0.4rem; font-size: 0.72rem; width: auto;">
              <option value="">Header ▾</option>
              <option value="H1">H1 Main Heading</option>
              <option value="H2">H2 Sub Heading</option>
              <option value="H3">H3 Section Title</option>
              <option value="P">Paragraph</option>
              <option value="BLOCKQUOTE">Blockquote</option>
            </select>
            <span style="opacity: 0.3;">|</span>
            <button type="button" onclick="execSaWysiwyg('justifyLeft')" class="mail-studio-action-btn" title="Align Left">⬅️</button>
            <button type="button" onclick="execSaWysiwyg('justifyCenter')" class="mail-studio-action-btn" title="Align Center">↔️</button>
            <button type="button" onclick="execSaWysiwyg('justifyRight')" class="mail-studio-action-btn" title="Align Right">➡️</button>
            <span style="opacity: 0.3;">|</span>
            <button type="button" onclick="execSaWysiwyg('insertUnorderedList')" class="mail-studio-action-btn" title="Bullet List">• List</button>
            <button type="button" onclick="execSaWysiwyg('insertOrderedList')" class="mail-studio-action-btn" title="Numbered List">1. List</button>
            <span style="opacity: 0.3;">|</span>
            <button type="button" onclick="promptSaLink()" class="mail-studio-action-btn" title="Insert Link">🔗 Link</button>
            <button type="button" onclick="insertBlock('cta_primary')" class="mail-studio-action-btn" title="Insert Action CTA Button">🔘 Button</button>
            <button type="button" onclick="execSaWysiwyg('removeFormat')" class="mail-studio-action-btn" style="color: #ef4444;" title="Clear Formatting">🧹 Clear</button>
          </div>

          <!-- Visual WYSIWYG Editable Canvas (Viewport + White Document Sheet) -->
          <div id="sa_wysiwyg_container" class="mail-studio-viewport">
            <div id="sa_wysiwyg_editor" contenteditable="true" oninput="syncSaWysiwygToCode()" class="mail-email-document-sheet">
              <?= $template ? ($template['html_content'] ?? ($template['html_body'] ?? '')) : '' ?>
            </div>
          </div>

          <!-- Raw HTML Code Editor (Hidden when in WYSIWYG mode) -->
          <textarea name="html_content" id="templateHtmlBody" oninput="syncSaCodeToWysiwyg()" rows="22" class="mail-studio-code-editor" placeholder="<h2 style='color:#0f172a;'>Hello {{name}}!</h2><p>{{message}}</p>" required><?= htmlspecialchars($template['html_content'] ?? ($template['html_body'] ?? '')) ?></textarea>
        </div>
      </div>

      <!-- RIGHT COLUMN: REAL-TIME LIVE EMAIL PREVIEW IFRAME -->
      <div id="sa-col-preview">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <label class="admin-modal-label" style="margin: 0; color: var(--admin-primary); display: flex; align-items: center; gap: 0.35rem; font-weight: 700;">
            <i data-lucide="eye" style="width: 15px; height: 15px;"></i> Live Delivery Simulation Canvas (Light Mode)
          </label>
          <span style="font-size: 0.72rem; color: var(--admin-text-tertiary);">Simulated Recipient: Alex Rivera</span>
        </div>

        <div class="mail-studio-preview-card">
          <!-- Simulated Email Client Header -->
          <div class="mail-studio-preview-header">
            <div class="mail-studio-preview-subject" id="saSimSubject">Subject: TechScout Intelligence Update</div>
            <div class="mail-studio-preview-meta">
              From: <strong style="color: var(--admin-text-primary);">TechScout Intelligence</strong> &lt;notifications@techscout.online&gt; &bull; To: alex@example.com
            </div>
          </div>

          <!-- Rendered Iframe (Pure Light Mode Canvas with White Envelope) -->
          <iframe id="standaloneLivePreviewFrame" style="width: 100%; height: 100%; border: none; background: #ffffff; flex: 1;"></iframe>
        </div>
      </div>
    </div>

    <!-- Bottom Action Bar -->
    <div class="mail-studio-bottom-bar">
      <div>
        <div style="flex-wrap: wrap; align-items: center; font-weight: 800; color: var(--admin-text-primary); font-size: 1rem; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="check-circle" style="color: #10b981;"></i> Ready to deploy HTML email template?
        </div>
        <div style="font-size: 0.8rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">
          Template updates take effect instantly across platform automated triggers and broadcast composers.
        </div>
      </div>

      <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.75rem; align-items: center;">
        <a href="<?= url('/admin/mail?tab=templates') ?>" class="ts-btn ts-btn-outline">
          Cancel &amp; Return
        </a>
        <button type="submit" class="ts-btn ts-btn-primary" style="font-weight: 800; padding: 0.75rem 2.25rem; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="save"></i> Save Template 💾
        </button>
      </div>
    </div>
  </form>
</div>

<script>
// Micro-toast helper
function showSaToast(msg) {
  const box = document.getElementById('saToastBox');
  if (!box) return;
  const t = document.createElement('div');
  t.style.cssText = 'background: #0f172a; color: #ffffff; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; box-shadow: 0 8px 24px rgba(0,0,0,0.25); display: flex; align-items: center; gap: 8px; pointer-events: auto; animation: saFadeIn 0.2s ease forwards;';
  t.innerHTML = msg;
  box.appendChild(t);
  setTimeout(() => {
    t.style.opacity = '0';
    t.style.transition = 'opacity 0.3s ease';
    setTimeout(() => t.remove(), 300);
  }, 2200);
}

// 1-Click Dynamic Tag Insertion
function insertVariable(tag) {
  const wysiwygCont = document.getElementById('sa_wysiwyg_container');
  const wysiwygEditor = document.getElementById('sa_wysiwyg_editor');
  const textarea = document.getElementById('templateHtmlBody');
  const isWysiwyg = wysiwygCont && wysiwygCont.style.display !== 'none';

  if (isWysiwyg && wysiwygEditor) {
    wysiwygEditor.focus();
    const sel = window.getSelection();
    let inserted = false;
    if (sel && sel.rangeCount > 0 && wysiwygEditor.contains(sel.anchorNode)) {
      const range = sel.getRangeAt(0);
      range.deleteContents();
      const node = document.createTextNode(tag);
      range.insertNode(node);
      range.setStartAfter(node);
      range.collapse(true);
      sel.removeAllRanges();
      sel.addRange(range);
      inserted = true;
    }
    if (!inserted) {
      wysiwygEditor.innerHTML += ' ' + tag + ' ';
    }
    syncSaWysiwygToCode();
  } else if (textarea) {
    const start = textarea.selectionStart || 0;
    const end = textarea.selectionEnd || 0;
    const text = textarea.value;
    textarea.value = text.substring(0, start) + tag + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + tag.length;
    textarea.focus();
    syncSaCodeToWysiwyg();
  }
  showSaToast(`Token <code>${tag}</code> inserted! ⚡`);
}

// 1-Click Universal Responsive Email Blocks Library (14 Blocks)
function insertBlock(type) {
  const wysiwygCont = document.getElementById('sa_wysiwyg_container');
  const wysiwygEditor = document.getElementById('sa_wysiwyg_editor');
  const textarea = document.getElementById('templateHtmlBody');
  const isWysiwyg = wysiwygCont && wysiwygCont.style.display !== 'none';

  const blocks = {
    hero: `\n<div style="text-align:center;padding:24px 16px;background:linear-gradient(180deg,#f8fafc,#ffffff);border-radius:12px;border:1px solid #e2e8f0;margin:16px 0;">\n  <span style="display:inline-block;padding:4px 12px;background:#eef2ff;color:#4f46e5;font-size:11px;font-weight:700;border-radius:20px;letter-spacing:0.5px;text-transform:uppercase;margin-bottom:12px;">🌟 Platform Intelligence</span>\n  <h2 style="color:#0f172a;font-size:24px;font-weight:800;line-height:1.3;margin:0 0 10px 0;letter-spacing:-0.5px;">{{headline}}</h2>\n  <p style="color:#475569;font-size:15px;line-height:1.6;margin:0;max-width:480px;display:inline-block;">Hello <strong>{{name}}</strong>, here is your latest personalized briefing from TechScout.</p>\n</div>\n`,
    callout_info: `\n<div style="background:#f0f9ff;border:1px solid #bae6fd;border-left:4px solid #0284c7;border-radius:8px;padding:16px 18px;color:#0369a1;font-size:14px;line-height:1.6;margin:18px 0;">\n  <div style="font-weight:700;color:#0284c7;margin-bottom:4px;">💡 Technical Briefing:</div>\n  <div style="color:#0f172a;">{{message}}</div>\n</div>\n`,
    callout_success: `\n<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #16a34a;border-radius:8px;padding:16px 18px;color:#15803d;font-size:14px;line-height:1.6;margin:18px 0;">\n  <div style="font-weight:700;color:#16a34a;margin-bottom:4px;">✅ Verified &amp; Confirmed:</div>\n  <div style="color:#0f172a;">Your submission or transaction has been processed successfully.</div>\n</div>\n`,
    callout_warning: `\n<div style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #d97706;border-radius:8px;padding:16px 18px;color:#b45309;font-size:14px;line-height:1.6;margin:18px 0;">\n  <div style="font-weight:700;color:#d97706;margin-bottom:4px;">⚠️ Action Required:</div>\n  <div style="color:#0f172a;">Please review your account parameters or adjust your submission details.</div>\n</div>\n`,
    callout_critical: `\n<div style="background:#fef2f2;border:1px solid #fecaca;border-left:4px solid #dc2626;border-radius:8px;padding:16px 18px;color:#b91c1c;font-size:14px;line-height:1.6;margin:18px 0;">\n  <div style="font-weight:700;color:#dc2626;margin-bottom:4px;">🚨 Critical Security Alert:</div>\n  <div style="color:#0f172a;">A new security event occurred from IP <strong>{{ip_address}}</strong> ({{browser}}). If this was not you, please secure your account immediately.</div>\n</div>\n`,
    cta_primary: `\n<div style="text-align:center;margin:28px 0;">\n  <a href="{{action_url}}" style="display:inline-block;background:#4f46e5;color:#ffffff;font-weight:700;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:15px;box-shadow:0 4px 14px rgba(79,70,229,0.25);letter-spacing:0.2px;">Explore TechScout Intelligence &rarr;</a>\n</div>\n`,
    cta_green: `\n<div style="text-align:center;margin:28px 0;">\n  <a href="{{action_url}}" style="display:inline-block;background:#059669;color:#ffffff;font-weight:700;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:15px;box-shadow:0 4px 14px rgba(5,150,105,0.25);letter-spacing:0.2px;">View Disbursed Payout Ledger &rarr;</a>\n</div>\n`,
    cta_secondary: `\n<div style="text-align:center;margin:20px 0;">\n  <a href="{{action_url}}" style="display:inline-block;background:#ffffff;color:#334155;border:1px solid #cbd5e1;font-weight:600;text-decoration:none;padding:10px 24px;border-radius:8px;font-size:14px;">Review Activity Details &rarr;</a>\n</div>\n`,
    datagrid: `\n<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:18px 20px;margin:20px 0;">\n  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;border-collapse:collapse;">\n    <tr style="border-bottom:1px solid #e2e8f0;">\n      <td style="padding:10px 0;color:#64748b;font-weight:500;">Reference ID</td>\n      <td style="padding:10px 0;text-align:right;color:#0f172a;font-weight:700;font-family:monospace;">#{{payout_id}}</td>\n    </tr>\n    <tr style="border-bottom:1px solid #e2e8f0;">\n      <td style="padding:10px 0;color:#64748b;font-weight:500;">Transaction Amount</td>\n      <td style="padding:10px 0;text-align:right;color:#059669;font-weight:800;font-size:16px;">\${{amount}} USD</td>\n    </tr>\n    <tr>\n      <td style="padding:10px 0;color:#64748b;font-weight:500;">Status</td>\n      <td style="padding:10px 0;text-align:right;"><span style="display:inline-block;background:#ecfdf5;color:#059669;font-weight:700;font-size:11px;padding:3px 10px;border-radius:4px;border:1px solid #a7f3d0;text-transform:uppercase;">COMPLETED</span></td>\n    </tr>\n  </table>\n</div>\n`,
    otp_box: `\n<div style="text-align:center;background:#f8fafc;border:1px solid #cbd5e1;border-radius:10px;padding:22px;margin:24px 0;">\n  <div style="font-size:12px;color:#64748b;letter-spacing:1px;text-transform:uppercase;font-weight:700;">One-Time Authorization Code (Valid {{expires_in}})</div>\n  <div style="font-size:36px;font-weight:900;color:#4f46e5;letter-spacing:10px;margin:10px 0 6px 0;font-family:Consolas,Monaco,monospace;">{{verification_code}}</div>\n  <div style="font-size:12px;color:#94a3b8;">Do not share this confidential verification PIN with anyone.</div>\n</div>\n`,
    two_col_card: `\n<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;border-collapse:separate;border-spacing:12px 0;">\n  <tr>\n    <td width="50%" valign="top" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;padding:16px;">\n      <div style="font-size:12px;font-weight:700;color:#6366f1;text-transform:uppercase;margin-bottom:6px;">📈 Performance</div>\n      <div style="font-size:20px;font-weight:800;color:#0f172a;">{{total_reads}}</div>\n      <div style="font-size:12px;color:#64748b;margin-top:2px;">Total Article Reads</div>\n    </td>\n    <td width="50%" valign="top" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;padding:16px;">\n      <div style="font-size:12px;font-weight:700;color:#059669;text-transform:uppercase;margin-bottom:6px;">💎 Earnings</div>\n      <div style="font-size:20px;font-weight:800;color:#059669;">\${{amount}}</div>\n      <div style="font-size:12px;color:#64748b;margin-top:2px;">Disbursed to date</div>\n    </td>\n  </tr>\n</table>\n`,
    quote: `\n<blockquote style="margin:20px 0;padding:16px 20px;background:#f8fafc;border-left:4px solid #6366f1;border-radius:0 8px 8px 0;color:#334155;font-size:14px;line-height:1.6;font-style:italic;">\n  &ldquo;{{comment_snippet}}&rdquo;\n  <div style="margin-top:8px;font-weight:700;color:#0f172a;font-style:normal;font-size:13px;">&mdash; {{commenter_name}}</div>\n</blockquote>\n`,
    checklist: `\n<div style="margin:20px 0;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:16px 20px;">\n  <div style="font-weight:700;color:#0f172a;margin-bottom:10px;font-size:14px;">Next Recommended Steps:</div>\n  <div style="flex-wrap: wrap; align-items: center; display:flex;align-items:flex-start;gap:8px;margin-bottom:8px;font-size:14px;color:#334155;">\n    <span style="color:#10b981;font-weight:bold;">✓</span> Complete your author profile and developer bio\n  </div>\n  <div style="flex-wrap: wrap; align-items: center; display:flex;align-items:flex-start;gap:8px;margin-bottom:8px;font-size:14px;color:#334155;">\n    <span style="color:#10b981;font-weight:bold;">✓</span> Connect your preferred creator payout method\n  </div>\n  <div style="flex-wrap: wrap; align-items: center; display:flex;align-items:flex-start;gap:8px;font-size:14px;color:#334155;">\n    <span style="color:#10b981;font-weight:bold;">✓</span> Submit your first technical breakdown or AI workflow\n  </div>\n</div>\n`,
    divider: `\n<hr style="border:none;border-top:1px solid #e2e8f0;margin:28px 0;">\n`
  };

  const snippet = blocks[type] || '';
  if (!snippet) return;

  if (isWysiwyg && wysiwygEditor) {
    wysiwygEditor.focus();
    const sel = window.getSelection();
    let inserted = false;
    if (sel && sel.rangeCount > 0 && wysiwygEditor.contains(sel.anchorNode)) {
      try {
        inserted = document.execCommand('insertHTML', false, snippet);
      } catch (e) { inserted = false; }
    }
    if (!inserted) {
      const div = document.createElement('div');
      div.innerHTML = snippet;
      if (sel && sel.rangeCount > 0 && wysiwygEditor.contains(sel.anchorNode)) {
        const range = sel.getRangeAt(0);
        range.deleteContents();
        const frag = document.createDocumentFragment();
        let child, lastChild;
        while ((child = div.firstChild)) {
          lastChild = frag.appendChild(child);
        }
        range.insertNode(frag);
        if (lastChild) {
          range.setStartAfter(lastChild);
          range.collapse(true);
          sel.removeAllRanges();
          sel.addRange(range);
        }
      } else {
        wysiwygEditor.innerHTML += snippet;
      }
    }
    syncSaWysiwygToCode();
  } else if (textarea) {
    const start = textarea.selectionStart || 0;
    const end = textarea.selectionEnd || 0;
    const text = textarea.value;
    textarea.value = text.substring(0, start) + snippet + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + snippet.length;
    textarea.focus();
    syncSaCodeToWysiwyg();
  }
  showSaToast('Responsive email block inserted! ✨');
}

// Starter Clean White Responsive Boilerplate
function loadStarterBoilerplate() {
  const textarea = document.getElementById('templateHtmlBody');
  const wysiwyg = document.getElementById('sa_wysiwyg_editor');
  const current = (textarea ? textarea.value : '').trim();
  if (current && !confirm('Replace current editor contents with modern clean white responsive boilerplate?')) {
    return;
  }
  const cleanBoilerplate = `<div style="text-align:center;padding:24px 16px;background:linear-gradient(180deg,#f8fafc,#ffffff);border-radius:12px;border:1px solid #e2e8f0;margin:0 0 20px 0;">
  <span style="display:inline-block;padding:4px 12px;background:#eef2ff;color:#4f46e5;font-size:11px;font-weight:700;border-radius:20px;letter-spacing:0.5px;text-transform:uppercase;margin-bottom:12px;">🌟 Intelligence Dispatch</span>
  <h2 style="color:#0f172a;font-size:24px;font-weight:800;line-height:1.3;margin:0 0 10px 0;letter-spacing:-0.5px;">{{headline}}</h2>
  <p style="color:#475569;font-size:15px;line-height:1.6;margin:0;max-width:480px;display:inline-block;">Hello <strong>{{name}}</strong>, here is your customized briefing from TechScout.</p>
</div>

<div style="color:#334155;font-size:15px;line-height:1.65;margin:20px 0;">
  <p>We are writing to keep you up-to-date with essential developer intelligence and platform activity.</p>
  <p>{{message}}</p>
</div>

<div style="background:#f0f9ff;border:1px solid #bae6fd;border-left:4px solid #0284c7;border-radius:8px;padding:16px 18px;color:#0369a1;font-size:14px;line-height:1.6;margin:22px 0;">
  <strong style="color:#0284c7;">💡 Technical Briefing:</strong> You can manage your notification preferences and credentials anytime from your account settings.
</div>

<div style="text-align:center;margin:32px 0;">
  <a href="{{action_url}}" style="display:inline-block;background:#4f46e5;color:#ffffff;font-weight:700;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:15px;box-shadow:0 4px 14px rgba(79,70,229,0.25);">Explore Dashboard &rarr;</a>
</div>

<div style="border-top:1px solid #e2e8f0;padding-top:18px;margin-top:28px;font-size:13px;color:#64748b;line-height:1.6;">
  Need assistance? Contact our engineering team at <a href="mailto:{{support_email}}" style="color:#4f46e5;text-decoration:none;">{{support_email}}</a>.
</div>`;

  if (textarea) textarea.value = cleanBoilerplate;
  if (wysiwyg) wysiwyg.innerHTML = cleanBoilerplate;
  updateStandaloneLivePreview();
  showSaToast('Clean white boilerplate loaded! ⚡');
}

// Live Real-Time Delivery Simulation Preview (Pure Light Mode Grid Envelope)
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
    .replace(/\{\{total_reads\}\}/g, '14,820')
    .replace(/\{\{commenter_name\}\}/g, 'Sarah Jenkins')
    .replace(/\{\{comment_snippet\}\}/g, 'Great breakdown on quantized transformer inference!')
    .replace(/\{\{expires_in\}\}/g, '15 minutes')
    .replace(/\{\{support_email\}\}/g, 'support@techscout.online')
    .replace(/\{\{site_name\}\}/g, 'TechScout')
    .replace(/\{\{year\}\}/g, new Date().getFullYear());

  const fullEnvelope = `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

// Split / Editor / Preview View Modes
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

// WYSIWYG vs Raw HTML Switching & Bi-directional Synchronization
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
    if (btnWysiwyg) { btnWysiwyg.className = 'ts-btn ts-btn-sm ts-btn-primary'; btnWysiwyg.removeAttribute('style'); btnWysiwyg.style.fontSize = '0.75rem'; }
    if (btnHtml) { btnHtml.className = 'ts-btn ts-btn-sm ts-btn-outline'; btnHtml.removeAttribute('style'); btnHtml.style.fontSize = '0.75rem'; }
  } else {
    syncSaWysiwygToCode();
    if (wysiwygCont) wysiwygCont.style.display = 'none';
    if (wysiwygTool) wysiwygTool.style.display = 'none';
    if (htmlInput) htmlInput.style.display = 'block';
    if (btnWysiwyg) { btnWysiwyg.className = 'ts-btn ts-btn-sm ts-btn-outline'; btnWysiwyg.removeAttribute('style'); btnWysiwyg.style.fontSize = '0.75rem'; }
    if (btnHtml) { btnHtml.className = 'ts-btn ts-btn-sm ts-btn-primary'; btnHtml.removeAttribute('style'); btnHtml.style.fontSize = '0.75rem'; }
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
    updateStandaloneLivePreview();
  }
}

function syncSaCodeToWysiwyg() {
  const wysiwyg = document.getElementById('sa_wysiwyg_editor');
  const htmlInput = document.getElementById('templateHtmlBody');
  if (wysiwyg && htmlInput) {
    wysiwyg.innerHTML = htmlInput.value || '';
    updateStandaloneLivePreview();
  }
}

function promptSaLink() {
  const url = prompt('Enter URL destination:', 'https://');
  if (url) execSaWysiwyg('createLink', url);
}

document.addEventListener('DOMContentLoaded', () => {
  const textarea = document.getElementById('templateHtmlBody');
  const wysiwyg = document.getElementById('sa_wysiwyg_editor');
  if (wysiwyg && textarea) {
    if (!wysiwyg.innerHTML.trim() && !textarea.value.trim()) {
      loadStarterBoilerplate();
    } else if (textarea.value.trim() && !wysiwyg.innerHTML.trim()) {
      syncSaCodeToWysiwyg();
    }
  }
  updateStandaloneLivePreview();
});
</script>

<style>
@keyframes saFadeIn {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
