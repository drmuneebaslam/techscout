<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Top Bar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink">COMMUNITY AUDIENCE</span>
        <span style="font-size: 0.8rem; color: #64748b;">Subscribers, Radar Follows & Creator Graphs</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0;">Audience Intelligence & Subscribers Roster</h1>
    </div>

    <div style="display: flex; gap: 0.75rem;">
      <a href="<?= url('/admin/categories') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="layers"></i> Manage Categories
      </a>
      <a href="<?= url('/admin/users') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="users"></i> Manage Users
      </a>
    </div>
  </div>

  <!-- Metric Stat Grid -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Active Follows</div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= number_format($stats['total_subs']) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Across all platform entities</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Topic Subscribers</div>
      <div class="admin-stat-val" style="color: #a855f7;"><?= number_format($stats['topic_subs']) ?></div>
      <div style="font-size: 0.8rem; color: #c084fc;">Personalized feed radars</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Author Followers</div>
      <div class="admin-stat-val" style="color: #ec4899;"><?= number_format($stats['author_subs']) ?></div>
      <div style="font-size: 0.8rem; color: #f472b6;">Creator community graphs</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Category Follows</div>
      <div class="admin-stat-val" style="color: #34d399;"><?= number_format($stats['category_subs']) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Direct taxonomy watchers</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Engaged Users</div>
      <div class="admin-stat-val" style="color: #f59e0b;"><?= number_format($stats['unique_users']) ?></div>
      <div style="font-size: 0.8rem; color: #fbbf24;">Unique subscribers</div>
    </div>
  </div>

  <!-- Navigation Tabs -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      <button type="button" onclick="switchAudienceTab('topics')" id="aud-tab-topics" class="ts-btn ts-btn-sm ts-btn-primary">
        <i data-lucide="layers" style="width: 14px;"></i> Topic Subscribers (<?= count($topics) ?>)
      </button>
      <button type="button" onclick="switchAudienceTab('authors')" id="aud-tab-authors" class="ts-btn ts-btn-sm ts-btn-outline">
        <i data-lucide="feather" style="width: 14px;"></i> Author Followers (<?= count($authors) ?>)
      </button>
      <button type="button" onclick="switchAudienceTab('categories')" id="aud-tab-categories" class="ts-btn ts-btn-sm ts-btn-outline">
        <i data-lucide="folder-tree" style="width: 14px;"></i> Category Watchers (<?= count($categories) ?>)
      </button>
      <button type="button" onclick="switchAudienceTab('stream')" id="aud-tab-stream" class="ts-btn ts-btn-sm ts-btn-outline">
        <i data-lucide="activity" style="width: 14px;"></i> Live Follow Activity Feed
      </button>
    </div>
  </div>

  <!-- PANE 1: Topic Subscribers Table -->
  <div id="pane-topics" class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Topic & Brand Domain</th>
          <th>Lucide Icon</th>
          <th>Subscribers Count</th>
          <th>Personalized Feed Radar</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($topics)): ?>
          <?php foreach ($topics as $t): ?>
            <tr>
              <td>
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                  <div style="width: 12px; height: 12px; border-radius: 50%; background: <?= htmlspecialchars($t['color_hex'] ?: '#6366f1') ?>;"></div>
                  <strong style="color: #f8fafc; font-size: 1rem;"><?= htmlspecialchars($t['name']) ?></strong>
                </div>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem; font-family: monospace;">/topic/<?= htmlspecialchars($t['slug']) ?></div>
              </td>
              <td>
                <span class="ts-badge" style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.15); color: <?= htmlspecialchars($t['color_hex'] ?: '#6366f1') ?>;">
                  <i data-lucide="<?= htmlspecialchars($t['icon_name'] ?: 'layers') ?>" style="width: 14px; height: 14px; display: inline; vertical-align: middle;"></i>
                  <?= htmlspecialchars($t['icon_name'] ?: 'layers') ?>
                </span>
              </td>
              <td>
                <span style="font-size: 1.1rem; font-weight: 800; color: #38bdf8;">
                  <?= number_format((int)$t['subscriber_count']) ?>
                </span>
                <span style="font-size: 0.75rem; color: #64748b;">followers</span>
              </td>
              <td>
                <a href="<?= url('/topic/' . $t['slug']) ?>" target="_blank" style="color: #a5b4fc; font-size: 0.85rem; text-decoration: none;">
                  View Live Feed ↗
                </a>
              </td>
              <td>
                <button type="button" onclick="inspectSubscribers('topic', <?= (int)$t['id'] ?>, '<?= htmlspecialchars(addslashes($t['name'])) ?>')" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.3rem 0.75rem; font-size: 0.78rem; font-weight: 700;">
                  <i data-lucide="users" style="width: 13px;"></i> View Subscribers (<?= (int)$t['subscriber_count'] ?>) 👥
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">No topics created yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- PANE 2: Author Followers Table -->
  <div id="pane-authors" class="admin-table-wrap" style="display: none;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Author & Creator</th>
          <th>Role / Badge</th>
          <th>Follower Count</th>
          <th>Author Profile</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($authors)): ?>
          <?php foreach ($authors as $a): ?>
            <tr>
              <td>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <?php if (!empty($a['avatar_url'])): ?>
                    <img src="<?= htmlspecialchars(image_url($a['avatar_url'])) ?>" alt="" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;">
                  <?php else: ?>
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #ec4899, #6366f1); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff;">
                      <?= strtoupper(substr($a['display_name'] ?? 'A', 0, 1)) ?>
                    </div>
                  <?php endif; ?>
                  <div>
                    <strong style="color: #f8fafc; font-size: 0.98rem;"><?= htmlspecialchars($a['display_name']) ?></strong>
                    <div style="font-size: 0.78rem; color: #38bdf8;"><?= htmlspecialchars($a['headline'] ?: 'Technical Scout') ?></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="ts-badge ts-badge-pink" style="font-size: 0.7rem;"><?= htmlspecialchars($a['verification_badge'] ?: 'VERIFIED') ?></span>
              </td>
              <td>
                <span style="font-size: 1.1rem; font-weight: 800; color: #ec4899;">
                  <?= number_format((int)$a['follower_count']) ?>
                </span>
                <span style="font-size: 0.75rem; color: #64748b;">followers</span>
              </td>
              <td>
                <a href="<?= url('/profile/' . $a['slug']) ?>" target="_blank" style="color: #a5b4fc; font-size: 0.85rem; text-decoration: none;">
                  /profile/<?= htmlspecialchars($a['slug']) ?> ↗
                </a>
              </td>
              <td>
                <button type="button" onclick="inspectSubscribers('author', <?= (int)$a['id'] ?>, '<?= htmlspecialchars(addslashes($a['display_name'])) ?>')" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.3rem 0.75rem; font-size: 0.78rem; font-weight: 700;">
                  <i data-lucide="users" style="width: 13px;"></i> View Followers (<?= (int)$a['follower_count'] ?>) 👥
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">No authors found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- PANE 3: Category Subscribers Table -->
  <div id="pane-categories" class="admin-table-wrap" style="display: none;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Category Name & Slug</th>
          <th>Parent Topic</th>
          <th>Target Entity</th>
          <th>Subscribers Count</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($categories)): ?>
          <?php foreach ($categories as $c): ?>
            <tr>
              <td>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <div style="width: 10px; height: 10px; border-radius: 50%; background: <?= htmlspecialchars($c['color_hex'] ?: '#38bdf8') ?>;"></div>
                  <strong style="color: #f8fafc;"><?= htmlspecialchars($c['name']) ?></strong>
                </div>
                <span style="font-size: 0.75rem; color: #64748b; font-family: monospace;">/blog/category/<?= htmlspecialchars($c['slug']) ?></span>
              </td>
              <td>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;"><?= htmlspecialchars($c['topic_name'] ?: 'General') ?></span>
              </td>
              <td>
                <span class="ts-badge" style="font-size: 0.65rem;"><?= strtoupper(htmlspecialchars($c['applicable_to'] ?: 'ALL')) ?></span>
              </td>
              <td>
                <span style="font-size: 1.05rem; font-weight: 800; color: #34d399;">
                  <?= number_format((int)$c['subscriber_count']) ?>
                </span>
                <span style="font-size: 0.75rem; color: #64748b;">watchers</span>
              </td>
              <td>
                <button type="button" onclick="inspectSubscribers('category', <?= (int)$c['id'] ?>, '<?= htmlspecialchars(addslashes($c['name'])) ?>')" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.3rem 0.75rem; font-size: 0.78rem; font-weight: 700;">
                  <i data-lucide="users" style="width: 13px;"></i> View Subscribers (<?= (int)$c['subscriber_count'] ?>) 👥
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- PANE 4: Live Follow Activity Feed -->
  <div id="pane-stream" class="admin-table-wrap" style="display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h3 style="font-size: 1.1rem; color: #f8fafc; margin: 0;">Recent Subscription Events</h3>
      <input type="text" oninput="filterStreamTable(this.value)" placeholder="Search user, target, or email..." style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.5rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem; width: 280px;">
    </div>
    <table class="admin-table" id="streamTable">
      <thead>
        <tr>
          <th>User / Subscriber</th>
          <th>Email</th>
          <th>Subscription Type</th>
          <th>Subscribed Target</th>
          <th>Subscribed Date</th>
          <th style="width: 80px; text-align: center;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($recentSubs)): ?>
          <?php foreach ($recentSubs as $s): ?>
            <tr>
              <td>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <div style="width: 28px; height: 28px; border-radius: 50%; background: #1e293b; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; color: #38bdf8;">
                    <?= strtoupper(substr($s['user_name'] ?? $s['username'] ?? 'U', 0, 1)) ?>
                  </div>
                  <div>
                    <strong style="color: #f8fafc; font-size: 0.9rem;"><?= htmlspecialchars($s['user_name'] ?? $s['username']) ?></strong>
                    <span style="font-size: 0.75rem; color: #64748b; font-family: monospace;">@<?= htmlspecialchars($s['username']) ?></span>
                  </div>
                </div>
              </td>
              <td style="color: #38bdf8; font-size: 0.85rem;"><?= htmlspecialchars($s['user_email']) ?></td>
              <td>
                <?php if ($s['subscribable_type'] === 'topic'): ?>
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;">TOPIC RADAR</span>
                <?php elseif ($s['subscribable_type'] === 'author'): ?>
                  <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem;">AUTHOR FOLLOWER</span>
                <?php else: ?>
                  <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">CATEGORY WATCH</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="<?= url($s['target_url']) ?>" target="_blank" style="color: #f8fafc; font-weight: 600; text-decoration: underline;">
                  <?= htmlspecialchars($s['target_title']) ?> ↗
                </a>
              </td>
              <td style="font-size: 0.8rem; color: #64748b; font-family: monospace;">
                <?= \App\Core\DateHelper::format($s['created_at']) ?>
              </td>
              <td style="text-align: center;">
                <form action="<?= url('/admin/subscribers/record/delete/' . $s['id']) ?>" method="POST" onsubmit="return confirm('Delete this follower subscription record?');" style="display: inline; margin: 0;">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.72rem; color: #ef4444; border-color: rgba(239,68,68,0.3);" title="Remove Subscription">
                    🗑️
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">No subscription records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Detailed Subscriber Roster Inspection Modal -->
<div id="subscribersListModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(10px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 780px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.4); box-shadow: 0 25px 60px rgba(0,0,0,0.85);">
    
    <!-- Modal Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <div>
        <h2 id="modalSubTitle" style="font-size: 1.35rem; color: #f8fafc; margin: 0;">Subscribers List</h2>
        <span id="modalSubMeta" style="font-size: 0.82rem; color: #38bdf8;">Topic Audience Intelligence</span>
      </div>
      <button onclick="document.getElementById('subscribersListModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.75rem; cursor: pointer; line-height: 1;">&times;</button>
    </div>

    <!-- Live Search Inside Modal -->
    <div style="margin-bottom: 1.25rem;">
      <input type="text" id="modalSubFilterInput" oninput="filterModalSubs(this.value)" placeholder="Filter subscribers by name, username or email..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;">
    </div>

    <!-- Subscriber Items Container -->
    <div id="modalSubListContainer" style="display: flex; flex-direction: column; gap: 0.75rem; max-height: 480px; overflow-y: auto;">
      <div style="text-align: center; padding: 2rem; color: #64748b;">
        <i data-lucide="loader" class="ts-spin" style="width: 28px; height: 28px; color: var(--ts-primary);"></i>
        <div style="margin-top: 0.5rem;">Loading subscribers roster...</div>
      </div>
    </div>

    <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 1rem;">
      <button type="button" onclick="document.getElementById('subscribersListModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm">Close</button>
    </div>
  </div>
</div>

<script>
function switchAudienceTab(tab) {
  const panes = {
    'topics': document.getElementById('pane-topics'),
    'authors': document.getElementById('pane-authors'),
    'categories': document.getElementById('pane-categories'),
    'stream': document.getElementById('pane-stream')
  };
  const btns = {
    'topics': document.getElementById('aud-tab-topics'),
    'authors': document.getElementById('aud-tab-authors'),
    'categories': document.getElementById('aud-tab-categories'),
    'stream': document.getElementById('aud-tab-stream')
  };

  Object.keys(panes).forEach(k => {
    if (panes[k]) panes[k].style.display = (k === tab) ? 'block' : 'none';
    if (btns[k]) {
      if (k === tab) {
        btns[k].classList.remove('ts-btn-outline');
        btns[k].classList.add('ts-btn-primary');
      } else {
        btns[k].classList.remove('ts-btn-primary');
        btns[k].classList.add('ts-btn-outline');
      }
    }
  });
}

async function inspectSubscribers(type, id, targetName) {
  const modal = document.getElementById('subscribersListModal');
  const title = document.getElementById('modalSubTitle');
  const meta = document.getElementById('modalSubMeta');
  const container = document.getElementById('modalSubListContainer');
  document.getElementById('modalSubFilterInput').value = '';

  const labelMap = {
    'topic': 'Topic Subscribers',
    'author': 'Author Followers',
    'category': 'Category Watchers'
  };

  title.innerText = `${labelMap[type] || 'Subscribers'}: ${targetName}`;
  meta.innerText = `Fetching audience details for ID #${id}...`;
  container.innerHTML = `
    <div style="text-align: center; padding: 2rem; color: #64748b;">
      <div style="color: #38bdf8; font-size: 0.95rem;">Fetching community subscribers...</div>
    </div>
  `;
  modal.style.display = 'flex';

  try {
    const res = await fetch(`<?= url('/admin/subscribers/json') ?>?type=${type}&id=${id}`);
    const data = await res.json();

    if (data.success) {
      meta.innerText = `Total ${data.count} community member(s) subscribed`;
      if (data.subscribers && data.subscribers.length > 0) {
        let html = '';
        data.subscribers.forEach(sub => {
          const initial = (sub.display_name || sub.username || 'U').charAt(0).toUpperCase();
          html += `
            <div class="modal-sub-card" data-search="${(sub.display_name + ' ' + sub.username + ' ' + sub.email).toLowerCase()}" style="display: flex; justify-content: space-between; align-items: center; background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.12); padding: 0.75rem 1rem; border-radius: 8px;">
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #38bdf8); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 0.9rem;">
                  ${initial}
                </div>
                <div>
                  <div style="font-weight: 700; color: #f8fafc; font-size: 0.95rem;">
                    ${sub.display_name || sub.username}
                    <span style="font-size: 0.75rem; color: #64748b; font-family: monospace; font-weight: normal; margin-left: 0.35rem;">@${sub.username}</span>
                  </div>
                  <div style="font-size: 0.8rem; color: #38bdf8;">${sub.email || ''}</div>
                </div>
              </div>
              <div style="text-align: right;">
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">${(sub.role || 'user').toUpperCase()}</span>
                <div style="font-size: 0.72rem; color: #64748b; font-family: monospace; margin-top: 0.2rem;">
                  Subscribed: ${sub.subscribed_at ? sub.subscribed_at.substring(0, 16) : 'N/A'}
                </div>
              </div>
            </div>
          `;
        });
        container.innerHTML = html;
      } else {
        container.innerHTML = `
          <div style="text-align: center; padding: 2.5rem; color: #64748b;">
            <div style="font-size: 1.1rem; color: #f8fafc; margin-bottom: 0.25rem;">No Active Subscribers Yet</div>
            <p style="font-size: 0.85rem; margin: 0;">Users who click "+ Follow" on this item will appear here.</p>
          </div>
        `;
      }
    } else {
      container.innerHTML = `<div style="text-align: center; color: #ef4444; padding: 1.5rem;">Failed to load subscribers.</div>`;
    }
  } catch (err) {
    container.innerHTML = `<div style="text-align: center; color: #ef4444; padding: 1.5rem;">Network error fetching subscribers.</div>`;
  }
  if (typeof lucide !== 'undefined') lucide.createIcons();
}

function filterModalSubs(query) {
  const q = query.toLowerCase().trim();
  const cards = document.querySelectorAll('#modalSubListContainer .modal-sub-card');
  cards.forEach(c => {
    const text = c.getAttribute('data-search') || '';
    if (!q || text.includes(q)) {
      c.style.display = 'flex';
    } else {
      c.style.display = 'none';
    }
  });
}

function filterStreamTable(query) {
  const q = query.toLowerCase().trim();
  const rows = document.querySelectorAll('#streamTable tbody tr');
  rows.forEach(r => {
    const text = r.innerText.toLowerCase();
    if (!q || text.includes(q)) {
      r.style.display = '';
    } else {
      r.style.display = 'none';
    }
  });
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
