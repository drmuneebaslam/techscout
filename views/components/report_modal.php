<!-- Unified Universal Entity Report Modal (Theme-Aware & Robust) -->
<div id="universalReportModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 999999; align-items: center; justify-content: center; padding: 1.5rem;" onclick="if(event.target===this) closeReportModal();">
  <div class="ts-card" style="width: 100%; max-width: 520px; padding: 2rem; background: var(--ts-card-bg); border: 1px solid rgba(239,68,68,0.4); box-shadow: 0 25px 60px rgba(0,0,0,0.6); border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 0.6rem;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(239,68,68,0.15); display: flex; align-items: center; justify-content: center; color: #ef4444; flex-shrink: 0;">
          <i data-lucide="shield-alert" style="width: 20px; height: 20px;"></i>
        </div>
        <div>
          <h3 style="font-size: 1.15rem; color: var(--ts-text-primary); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">Submit Content Report</h3>
          <span id="repTargetDisplay" style="font-size: 0.75rem; color: var(--ts-text-tertiary);">Article / Tool / Comment</span>
        </div>
      </div>
      <button type="button" onclick="closeReportModal()" style="background: none; border: none; color: var(--ts-text-tertiary); font-size: 1.6rem; cursor: pointer; line-height: 1;">&times;</button>
    </div>

    <form id="universalReportForm" onsubmit="handleReportSubmit(event)">
      <input type="hidden" name="entity_type" id="repEntityType" value="">
      <input type="hidden" name="entity_id" id="repEntityId" value="">
      <input type="hidden" name="entity_title" id="repEntityTitle" value="">
      <input type="hidden" name="entity_url" id="repEntityUrl" value="">

      <div style="margin-bottom: 1.1rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.4rem;">
          Reason for Report <span style="color: #ef4444;">*</span>
        </label>
        <select name="reason" id="repReason" style="width: 100%; background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.65rem 0.85rem; border-radius: 6px; color: var(--ts-text-primary); font-size: 0.9rem;" required>
          <option value="spam">Spam / Unsolicited Promotion / Bot Content</option>
          <option value="broken_or_misleading">Broken Link / Misleading Information / False Claims</option>
          <option value="plagiarism_copyright">Plagiarism / Stolen Content / Copyright Infringement</option>
          <option value="inappropriate_harmful">Inappropriate / Harassment / Harmful Content</option>
          <option value="malicious_prompt_injection">Malicious Code / Jailbreak / Prompt Injection</option>
          <option value="other">Other Concern / Policy Violation</option>
        </select>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.4rem;">
          Additional Details &amp; Context (Optional)
        </label>
        <textarea name="details" id="repDetails" rows="3" placeholder="Explain specifically what is inaccurate, violated, or broken..." style="width: 100%; background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.65rem 0.85rem; border-radius: 6px; color: var(--ts-text-primary); font-size: 0.85rem; resize: vertical; line-height: 1.5;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="closeReportModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" id="repSubmitBtn" class="ts-btn ts-btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; font-weight: 700; border: none; padding: 0.5rem 1.25rem;">
          Submit Report to Staff
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openReportModal(entityType, entityId, entityTitle = '', entityUrl = '') {
  document.getElementById('repEntityType').value = entityType;
  document.getElementById('repEntityId').value = entityId;
  document.getElementById('repEntityTitle').value = entityTitle || document.title;
  document.getElementById('repEntityUrl').value = entityUrl || window.location.href;
  document.getElementById('repTargetDisplay').innerText = `${entityType.toUpperCase()}: ${entityTitle || ('ID #' + entityId)}`;
  document.getElementById('repDetails').value = '';
  document.getElementById('universalReportModal').style.display = 'flex';
  if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeReportModal() {
  document.getElementById('universalReportModal').style.display = 'none';
}

async function handleReportSubmit(e) {
  e.preventDefault();
  const btn = document.getElementById('repSubmitBtn');
  btn.disabled = true;
  btn.innerText = 'Submitting report...';

  const baseUrl = (typeof getTsApiBase === 'function') ? getTsApiBase() : '';
  const formData = new FormData(document.getElementById('universalReportForm'));

  try {
    const res = await fetch(`${baseUrl}/api/v1/report`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      body: formData
    });

    let data;
    const contentType = res.headers.get('content-type') || '';
    if (contentType.includes('application/json')) {
      data = await res.json();
    } else {
      const text = await res.text();
      try {
        data = JSON.parse(text);
      } catch (parseErr) {
        throw new Error(res.statusText || 'Unable to submit report.');
      }
    }

    if (data.success) {
      if (typeof showToast === 'function') {
        showToast(data.message || 'Report submitted to staff.', 'success');
      } else {
        alert(data.message || 'Report submitted successfully.');
      }
      closeReportModal();
    } else {
      if (typeof showToast === 'function') {
        showToast(data.error || 'Failed to submit report.', 'warning');
      } else {
        alert(data.error || 'Failed to submit report.');
      }
    }
  } catch (err) {
    console.error('Report submission error:', err);
    if (typeof showToast === 'function') {
      showToast('Unable to submit report. Please try again.', 'error');
    } else {
      alert('Unable to submit report. Please try again.');
    }
  } finally {
    btn.disabled = false;
    btn.innerText = 'Submit Report to Staff';
  }
}
</script>
