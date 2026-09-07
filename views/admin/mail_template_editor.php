<?php
$title = ($template ? 'Edit Template: ' . htmlspecialchars($template['name']) : 'Create Custom HTML Email Template') . ' | TechScout Mail Studio';
require __DIR__ . '/../layouts/admin_header.php';
?>

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
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
