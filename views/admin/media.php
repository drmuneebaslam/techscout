<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">DIGITAL ASSET MANAGEMENT (DAM) &bull; CMS STORAGE</span>
        <span style="font-size: 0.8rem; color: #64748b;">Images, Videos, Audio, Documents, Metadata &amp; Alt Tags</span>
      </div>
      <h1 style="font-size: 1.8rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        CMS Media Library
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="openUploadModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
        <i data-lucide="upload-cloud"></i> Upload New Media 🚀
      </button>
      <button type="button" onclick="openFolderModal()" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="folder-plus"></i> New Folder
      </button>
    </div>
  </div>

  <!-- Asset Type Stat Counters -->
  <div class="admin-stat-grid" style="margin-bottom: 2rem;">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Assets Stored</div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= count($media) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Across all categorized folders</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Images &amp; Diagrams</div>
      <div class="admin-stat-val" style="color: #10b981;">
        <?= count(array_filter($media, fn($m) => $m['file_type'] === 'image')) ?>
      </div>
      <div style="font-size: 0.8rem; color: #34d399;">PNG, JPG, WebP, SVG, GIF</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Audio &amp; Video Streams</div>
      <div class="admin-stat-val" style="color: #ec4899;">
        <?= count(array_filter($media, fn($m) => in_array($m['file_type'], ['video', 'audio']))) ?>
      </div>
      <div style="font-size: 0.8rem; color: #f472b6;">MP4, WebM, MP3, WAV, Ogg</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">PDFs &amp; Whitepapers</div>
      <div class="admin-stat-val" style="color: #f59e0b;">
        <?= count(array_filter($media, fn($m) => $m['file_type'] === 'document')) ?>
      </div>
      <div style="font-size: 0.8rem; color: #fbbf24;">PDF, DOCX, ZIP, JSON, CSV</div>
    </div>
  </div>

  <!-- Filter & Search Strip -->
  <div class="ts-card" style="background: rgba(13,19,34,0.7); margin-bottom: 2rem; padding: 1.25rem; border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      
      <!-- Type Filter Tabs -->
      <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
        <a href="<?= url('/admin/media') ?>" class="ts-btn ts-btn-sm <?= empty($currentType) || $currentType === 'all' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
          All Media
        </a>
        <a href="<?= url('/admin/media?type=image') ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'image' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
          🖼️ Images
        </a>
        <a href="<?= url('/admin/media?type=video') ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'video' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
          🎥 Videos
        </a>
        <a href="<?= url('/admin/media?type=audio') ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'audio' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
          🎙️ Audio
        </a>
        <a href="<?= url('/admin/media?type=document') ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'document' ? 'ts-btn-primary' : 'ts-btn-outline' ?>">
          📄 Documents
        </a>
      </div>

      <!-- Search & Folder Filter -->
      <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Folder:</span>
          <select onchange="location.href='<?= url('/admin/media?type=' . ($currentType ?? 'all')) ?>&folder=' + this.value" class="admin-modal-select" style="padding: 0.35rem 0.75rem; font-size: 0.82rem;">
            <option value="all" <?= empty($currentFolder) || $currentFolder === 'all' ? 'selected' : '' ?>>All Folders</option>
            <?php foreach ($folders as $f): ?>
              <option value="<?= htmlspecialchars($f) ?>" <?= ($currentFolder ?? '') === $f ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($f)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="position: relative;">
          <input type="text" id="mediaSearchInput" placeholder="Search filenames, alt text, tags..." onkeyup="filterMediaGrid()" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 6px; padding: 0.45rem 0.85rem; color: #f8fafc; font-size: 0.82rem; width: 220px;">
        </div>
      </div>
    </div>
  </div>

  <!-- Media Grid Showcase -->
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 3rem;" id="mediaGrid">
    <?php if (!empty($media)): ?>
      <?php foreach ($media as $item): ?>
        <?php
          $fileUrl = $item['file_path'];
          $isExternal = str_starts_with($fileUrl, 'http://') || str_starts_with($fileUrl, 'https://');
          $fullUrl = $isExternal ? $fileUrl : url($fileUrl);
          $sizeFormatted = $item['file_size'] > 1048576 ? round($item['file_size'] / 1048576, 2) . ' MB' : round($item['file_size'] / 1024, 1) . ' KB';
        ?>
        <div class="ts-card media-item-card" data-filename="<?= strtolower(htmlspecialchars($item['filename'] . ' ' . $item['alt_text'] . ' ' . $item['folder'])) ?>" style="padding: 0.85rem; background: rgba(13,21,39,0.7); display: flex; flex-direction: column; justify-content: space-between;">
          
          <!-- Thumbnail / Preview Area -->
          <div style="height: 150px; background: #05070b; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative; border: 1px solid rgba(148,163,184,0.1); margin-bottom: 0.75rem;">
            <?php if ($item['file_type'] === 'image'): ?>
              <img src="<?= htmlspecialchars($fullUrl) ?>" alt="<?= htmlspecialchars($item['alt_text'] ?? $item['filename']) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <?php elseif ($item['file_type'] === 'video'): ?>
              <div style="text-align: center; color: #ec4899;">
                <i data-lucide="video" style="width: 36px; height: 36px; margin-bottom: 0.25rem;"></i>
                <div style="font-size: 0.72rem; font-family: var(--admin-font-mono);">MP4 / Video</div>
              </div>
            <?php elseif ($item['file_type'] === 'audio'): ?>
              <div style="text-align: center; color: #38bdf8;">
                <i data-lucide="music" style="width: 36px; height: 36px; margin-bottom: 0.25rem;"></i>
                <div style="font-size: 0.72rem; font-family: var(--admin-font-mono);">Audio Clip</div>
              </div>
            <?php else: ?>
              <div style="text-align: center; color: #f59e0b;">
                <i data-lucide="file-text" style="width: 36px; height: 36px; margin-bottom: 0.25rem;"></i>
                <div style="font-size: 0.72rem; font-family: var(--admin-font-mono);">Document / PDF</div>
              </div>
            <?php endif; ?>

            <span class="ts-badge ts-badge-cyan" style="position: absolute; top: 8px; left: 8px; font-size: 0.65rem; padding: 0.1rem 0.35rem; background: rgba(15,23,42,0.85);">
              <?= strtoupper($item['folder']) ?>
            </span>
          </div>

          <!-- Metadata & Details -->
          <div>
            <div style="font-weight: 700; color: #f8fafc; font-size: 0.88rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 0.25rem;" title="<?= htmlspecialchars($item['filename']) ?>">
              <?= htmlspecialchars($item['filename']) ?>
            </div>
            
            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; margin-bottom: 0.6rem;">
              <span><?= $sizeFormatted ?></span>
              <span><?= date('M j, Y', strtotime($item['created_at'])) ?></span>
            </div>

            <?php if (!empty($item['alt_text'])): ?>
              <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.6rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                <span style="color: #64748b;">Alt:</span> <?= htmlspecialchars($item['alt_text']) ?>
              </div>
            <?php endif; ?>

            <!-- Action Strip -->
            <div style="display: flex; gap: 0.35rem; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.6rem;">
              <button type="button" onclick='openMediaDetailModal(<?= json_encode($item) ?>, "<?= htmlspecialchars($fullUrl) ?>")' class="ts-btn ts-btn-outline ts-btn-sm" style="flex: 1; font-size: 0.72rem; padding: 0.25rem 0.4rem; color: #38bdf8;">
                <i data-lucide="info" style="width: 12px; height: 12px;"></i> Inspect
              </button>
              <button type="button" onclick="copyMediaUrl('<?= htmlspecialchars($fullUrl) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.4rem; color: #10b981;" title="Copy Public URL">
                <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
              </button>
              <form action="<?= url('/admin/media/delete/' . $item['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this media asset?');" style="display: inline;">
                <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.4rem; color: #ef4444;" title="Delete File">
                  <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                </button>
              </form>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: #64748b;" class="ts-card">
        <i data-lucide="folder-search" style="width: 48px; height: 48px; margin-bottom: 0.75rem; color: #475569;"></i>
        <div style="font-size: 1.1rem; color: #f8fafc; font-weight: 700; margin-bottom: 0.25rem;">No media files found in this category.</div>
        <div style="font-size: 0.85rem; margin-bottom: 1.25rem;">Upload images, videos, audio clips, or whitepapers to populate your CMS digital asset library.</div>
        <button type="button" onclick="openUploadModal()" class="ts-btn ts-btn-primary ts-btn-sm">
          <i data-lucide="upload-cloud"></i> Upload First Asset
        </button>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: UPLOAD MEDIA -->
<!-- ========================================================================= -->
<div id="uploadMediaModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 580px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="upload-cloud"></i> Upload Media Asset</h3>
      <button type="button" onclick="closeUploadModal()" class="admin-modal-close">✕</button>
    </div>
    <form action="<?= url('/admin/media/upload') ?>" method="POST" enctype="multipart/form-data">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Choose File (Images, Video, Audio, Documents)</label>
        <input type="file" name="media_file" class="admin-modal-input" style="padding: 0.5rem;" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Destination Folder</label>
          <select name="folder" class="admin-modal-select">
            <option value="general">General</option>
            <option value="articles">Articles &amp; Deep Dives</option>
            <option value="tools">AI Tools &amp; Radar</option>
            <option value="avatars">Avatars &amp; Creators</option>
            <option value="banners">Banners &amp; Ribbons</option>
            <option value="documents">Documents &amp; PDFs</option>
            <option value="audio">Audio &amp; Podcasts</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Asset Type Classification</label>
          <select name="file_type" class="admin-modal-select">
            <option value="image">Image (PNG, JPG, WebP, SVG)</option>
            <option value="video">Video (MP4, WebM)</option>
            <option value="audio">Audio (MP3, WAV, OGG)</option>
            <option value="document">Document (PDF, DOCX, ZIP)</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">SEO Alt Text (Recommended for Images)</label>
        <input type="text" name="alt_text" placeholder="Descriptive image explanation for screen-readers & SEO" class="admin-modal-input">
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Caption / Description</label>
        <input type="text" name="caption" placeholder="Short description or diagram breakdown" class="admin-modal-input">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label class="admin-modal-label">Copyright &amp; License</label>
          <select name="copyright_info" class="admin-modal-select">
            <option value="TechScout Editorial / Unsplash License">Editorial / Unsplash License</option>
            <option value="Creative Commons (CC-BY 4.0)">Creative Commons (CC-BY 4.0)</option>
            <option value="Public Domain">Public Domain</option>
            <option value="Proprietary / Copyrighted">Proprietary / All Rights Reserved</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Attribution &amp; Source Credit</label>
          <input type="text" name="attribution" placeholder="e.g. Photo by Jane Doe on Unsplash" class="admin-modal-input">
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeUploadModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; color: #000; font-weight: 700;">Upload &amp; Register Asset ⚡</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: MEDIA INSPECTOR & EMBED CODE GENERATOR -->
<!-- ========================================================================= -->
<div id="mediaDetailModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 760px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" id="md_title_header" style="color: #38bdf8;">Media Asset Inspector</h3>
      <button type="button" onclick="closeMediaDetailModal()" class="admin-modal-close">✕</button>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 1.5rem; margin-bottom: 1.25rem;">
      <!-- Left: Visual Preview -->
      <div id="md_preview_box" style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1rem; display: flex; align-items: center; justify-content: center; min-height: 200px;">
      </div>

      <!-- Right: Metadata & Editable Fields -->
      <form id="mediaUpdateForm" action="" method="POST">
        <div style="margin-bottom: 0.75rem;">
          <label class="admin-modal-label">Alt Text (SEO)</label>
          <input type="text" name="alt_text" id="md_alt_input" class="admin-modal-input">
        </div>
        <div style="margin-bottom: 0.75rem;">
          <label class="admin-modal-label">Caption</label>
          <input type="text" name="caption" id="md_caption_input" class="admin-modal-input">
        </div>
        <div style="margin-bottom: 0.75rem;">
          <label class="admin-modal-label">Attribution</label>
          <input type="text" name="attribution" id="md_attr_input" class="admin-modal-input">
        </div>
        <div style="margin-bottom: 0.75rem;">
          <label class="admin-modal-label">Folder</label>
          <select name="folder" id="md_folder_select" class="admin-modal-select">
            <option value="general">General</option>
            <option value="articles">Articles</option>
            <option value="tools">Tools</option>
            <option value="avatars">Avatars</option>
            <option value="banners">Banners</option>
            <option value="documents">Documents</option>
            <option value="audio">Audio</option>
          </select>
        </div>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; color: #000; font-weight: 700; width: 100%;">Save Metadata ✓</button>
      </form>
    </div>

    <!-- Embed Code Generator Strip -->
    <div style="background: rgba(15,23,42,0.8); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
      <div style="font-size: 0.8rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem;">One-Click Embed Codes:</div>
      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <button type="button" onclick="copyEmbedCode('url')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #38bdf8;">
          📋 Direct URL
        </button>
        <button type="button" onclick="copyEmbedCode('markdown')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #10b981;">
          📝 Markdown `![]()`
        </button>
        <button type="button" onclick="copyEmbedCode('html')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #ec4899;">
          🌐 HTML `<img>`
        </button>
      </div>
    </div>

    <div class="admin-modal-footer">
      <button type="button" onclick="closeMediaDetailModal()" class="ts-btn ts-btn-outline ts-btn-sm">Close</button>
    </div>
  </div>
</div>

<script>
let currentActiveMedia = null;

function filterMediaGrid() {
  const query = document.getElementById('mediaSearchInput').value.toLowerCase();
  document.querySelectorAll('.media-item-card').forEach(c => {
    const fn = c.getAttribute('data-filename') || '';
    c.style.display = fn.includes(query) ? 'flex' : 'none';
  });
}

function openUploadModal() {
  document.getElementById('uploadMediaModal').style.display = 'flex';
}
function closeUploadModal() {
  document.getElementById('uploadMediaModal').style.display = 'none';
}

function openMediaDetailModal(item, fullUrl) {
  currentActiveMedia = { item, fullUrl };
  document.getElementById('md_title_header').innerText = item.filename;
  document.getElementById('md_alt_input').value = item.alt_text || '';
  document.getElementById('md_caption_input').value = item.caption || '';
  document.getElementById('md_attr_input').value = item.attribution || '';
  document.getElementById('md_folder_select').value = item.folder || 'general';
  document.getElementById('mediaUpdateForm').action = `<?= url('/admin/media/update/') ?>${item.id}`;

  const prev = document.getElementById('md_preview_box');
  if (item.file_type === 'image') {
    prev.innerHTML = `<img src="${fullUrl}" style="max-width: 100%; max-height: 220px; object-fit: contain; border-radius: 6px;">`;
  } else if (item.file_type === 'audio') {
    prev.innerHTML = `<audio controls src="${fullUrl}" style="width: 100%;"></audio>`;
  } else {
    prev.innerHTML = `<div style="text-align:center; color:#94a3b8;"><i data-lucide="file" style="width:48px; height:48px;"></i><div>${item.filename}</div></div>`;
  }

  document.getElementById('mediaDetailModal').style.display = 'flex';
  if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeMediaDetailModal() {
  document.getElementById('mediaDetailModal').style.display = 'none';
}

function copyMediaUrl(url) {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(url).then(() => {
      showToast('✓ Direct media URL copied to clipboard!', 'success');
    });
  }
}

function copyEmbedCode(type) {
  if (!currentActiveMedia) return;
  const { item, fullUrl } = currentActiveMedia;
  let code = fullUrl;
  if (type === 'markdown') {
    code = `![${item.alt_text || item.filename}](${fullUrl})`;
  } else if (type === 'html') {
    code = `<img src="${fullUrl}" alt="${item.alt_text || item.filename}" loading="lazy" class="techscout-img" />`;
  }
  
  if (navigator.clipboard) {
    navigator.clipboard.writeText(code).then(() => {
      showToast(`✓ ${type.toUpperCase()} embed code copied!`, 'success');
    });
  }
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
