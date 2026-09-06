<?php
/**
 * TechScout Unified Social Sharing & Distribution Hub Component
 * Variables expected:
 *   $shareTitle (string) - Title of article/tool
 *   $shareUrl (string) - Canonical URL to share
 *   $shareSummary (string) - Brief summary
 *   $shareType (string) - 'article', 'ai_tool', 'prompt', 'gpt'
 */
$sTitle = $shareTitle ?? ($article['title'] ?? ($tool['title'] ?? 'TechScout Innovation'));
$sUrl = $shareUrl ?? (isset($article) ? url('/article/' . $article['slug']) : (isset($tool) ? url('/tools/' . $tool['slug']) : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"));
$sSummary = $shareSummary ?? ($article['summary'] ?? ($tool['summary'] ?? ''));

$encodedUrl = urlencode($sUrl);
$encodedTitle = urlencode($sTitle . ' — via @techscout');
$encodedSummary = urlencode($sSummary);
?>

<!-- Social Sharing Distribution Bar -->
<div class="ts-card social-share-box" style="margin: 2.5rem 0; padding: 1.5rem; background: rgba(15,23,42,0.65); border: 1px solid rgba(99,102,241,0.25); border-radius: 12px;">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <div style="width: 38px; height: 38px; border-radius: 8px; background: linear-gradient(135deg, #6366f1, #38bdf8); display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0;">
        <i data-lucide="share-2" style="width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h4 style="margin: 0; font-size: 1rem; color: var(--ts-text-primary); font-weight: 700;">Share this Technical Guide</h4>
        <span style="font-size: 0.78rem; color: var(--ts-text-tertiary);">Help fellow software engineers &amp; researchers discover this knowledge</span>
      </div>
    </div>

    <!-- Social Channel Buttons -->
    <div style="display: flex; gap: 0.45rem; flex-wrap: wrap; align-items: center;">
      <!-- Twitter / 𝕏 -->
      <a href="https://twitter.com/intent/tweet?text=<?= $encodedTitle ?>&url=<?= $encodedUrl ?>" target="_blank" rel="noopener noreferrer" class="ts-btn ts-btn-outline ts-btn-sm share-btn" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; color: #f8fafc; border-color: rgba(255,255,255,0.15);" title="Share on 𝕏 (Twitter)">
        <span style="font-weight: 900; font-family: sans-serif;">𝕏</span> 𝕏 Post
      </a>

      <!-- LinkedIn -->
      <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $encodedUrl ?>" target="_blank" rel="noopener noreferrer" class="ts-btn ts-btn-outline ts-btn-sm share-btn" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="Share on LinkedIn">
        <i data-lucide="linkedin" style="width: 13px; height: 13px;"></i> LinkedIn
      </a>

      <!-- Reddit -->
      <a href="https://reddit.com/submit?url=<?= $encodedUrl ?>&title=<?= $encodedTitle ?>" target="_blank" rel="noopener noreferrer" class="ts-btn ts-btn-outline ts-btn-sm share-btn" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; color: #fb923c; border-color: rgba(251,146,60,0.3);" title="Submit to Reddit">
        <i data-lucide="message-square" style="width: 13px; height: 13px;"></i> Reddit
      </a>

      <!-- Hacker News -->
      <a href="https://news.ycombinator.com/submitlink?u=<?= $encodedUrl ?>&t=<?= $encodedTitle ?>" target="_blank" rel="noopener noreferrer" class="ts-btn ts-btn-outline ts-btn-sm share-btn" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; color: #ff6600; border-color: rgba(255,102,0,0.3);" title="Submit to Hacker News">
        <span style="font-weight: 900; font-family: monospace; font-size: 0.85rem;">Y</span> HN
      </a>

      <!-- WhatsApp -->
      <a href="https://api.whatsapp.com/send?text=<?= $encodedTitle ?>%20<?= $encodedUrl ?>" target="_blank" rel="noopener noreferrer" class="ts-btn ts-btn-outline ts-btn-sm share-btn" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; color: #22c55e; border-color: rgba(34,197,94,0.3);" title="Share on WhatsApp">
        <i data-lucide="send" style="width: 13px; height: 13px;"></i> WhatsApp
      </a>

      <!-- Telegram -->
      <a href="https://t.me/share/url?url=<?= $encodedUrl ?>&text=<?= $encodedTitle ?>" target="_blank" rel="noopener noreferrer" class="ts-btn ts-btn-outline ts-btn-sm share-btn" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; color: #0088cc; border-color: rgba(0,136,204,0.3);" title="Share on Telegram">
        <i data-lucide="paperclip" style="width: 13px; height: 13px;"></i> Telegram
      </a>

      <!-- Copy Link -->
      <button type="button" onclick="copyShareUrl('<?= htmlspecialchars(addslashes($sUrl), ENT_QUOTES) ?>', this)" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; font-weight: 700;" title="Copy direct link to clipboard">
        <i data-lucide="link" style="width: 13px; height: 13px;"></i> <span>Copy Link</span>
      </button>

      <!-- QR Code Modal Trigger -->
      <button type="button" onclick="openQrModal('<?= htmlspecialchars(addslashes($sTitle), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($sUrl), ENT_QUOTES) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.6rem; font-size: 0.78rem; color: #cbd5e1;" title="Show QR Code for mobile scanning">
        <i data-lucide="qr-code" style="width: 13px; height: 13px;"></i>
      </button>

      <!-- Native Mobile Web Share -->
      <button type="button" onclick="triggerWebShare('<?= htmlspecialchars(addslashes($sTitle), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($sSummary), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($sUrl), ENT_QUOTES) ?>')" id="nativeShareBtn" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.6rem; font-size: 0.78rem; display: none;" title="Open device share menu">
        <i data-lucide="share" style="width: 13px; height: 13px;"></i>
      </button>
    </div>
  </div>
</div>

<!-- QR Code Modal -->
<div id="shareQrModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 999999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="ts-card" style="width: 100%; max-width: 380px; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.35); text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.9); border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h3 style="font-size: 1.1rem; color: #f8fafc; margin: 0;">Scan QR Code</h3>
      <button onclick="document.getElementById('shareQrModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <div style="background: #fff; padding: 1rem; border-radius: 10px; display: inline-block; margin-bottom: 1.25rem; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
      <img id="shareQrImage" src="" alt="QR Code" style="width: 180px; height: 180px; display: block;">
    </div>

    <p id="shareQrTitle" style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 1.25rem; word-break: break-word;"></p>

    <button type="button" onclick="document.getElementById('shareQrModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%;">Close</button>
  </div>
</div>

<!-- Floating Copy Confirmation Toast -->
<div id="copyToast" style="display: none; position: fixed; bottom: 2rem; right: 2rem; background: linear-gradient(135deg, #10b981, #059669); color: #fff; padding: 0.75rem 1.25rem; border-radius: 8px; font-size: 0.88rem; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,0.4); z-index: 9999999; align-items: center; gap: 0.5rem; animation: tsSlideUp 0.2s ease;">
  <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
  <span id="copyToastText">Link copied to clipboard! 📋</span>
</div>

<script>
// Check if Web Share API is available
if (navigator.share) {
  const nativeBtn = document.getElementById('nativeShareBtn');
  if (nativeBtn) nativeBtn.style.display = 'inline-flex';
}

function triggerWebShare(title, text, url) {
  if (navigator.share) {
    navigator.share({ title: title, text: text, url: url }).catch(() => {});
  }
}

function copyShareUrl(url, btn) {
  navigator.clipboard.writeText(url).then(() => {
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span>Copied! ✓</span>';
    btn.style.background = '#10b981';
    btn.style.borderColor = '#10b981';

    const toast = document.getElementById('copyToast');
    if (toast) {
      toast.style.display = 'flex';
      if (window.lucide) lucide.createIcons();
      setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }

    setTimeout(() => {
      btn.innerHTML = originalText;
      btn.style.background = '';
      btn.style.borderColor = '';
      if (window.lucide) lucide.createIcons();
    }, 2000);
  }).catch(() => {
    prompt('Copy article link:', url);
  });
}

function openQrModal(title, url) {
  document.getElementById('shareQrTitle').innerText = title;
  document.getElementById('shareQrImage').src = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(url);
  document.getElementById('shareQrModal').style.display = 'flex';
}
</script>
