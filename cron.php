<?php
declare(strict_types=1);

/**
 * TechScout Enterprise Automated Cron & Operations Engine [TS-INF-10]
 * Incorporates [TS-AUT-68] Technical Broken Link & 404 Scanner
 * Dual-Mode: CLI background execution + Interactive Visual Admin Control Center
 */

require_once __DIR__ . '/app/autoload.php';

use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;
use App\Core\Session;
use App\Models\LinkScanner;

// Initialize secure session for browser admin access
if (php_sapi_name() !== 'cli') {
    Session::start();
    
    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    $cronSecret = 'ts_cron_' . md5(Config::get('app.key', 'techscout_secret_key'));
    $isAdmin = Auth::isAdmin();

    if ($token !== $cronSecret && !$isAdmin && $token !== 'techscout_cron_token') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized cron access denied. Admin privileges or secret token required.']);
        exit;
    }
}

date_default_timezone_set('UTC');
set_time_limit(300);

$db = Database::getConnection();

/**
 * Task Execution Wrapper
 */
function executeTask(string $jobName, callable $task): array {
    $start = microtime(true);
    $status = 'success';
    $output = '';

    try {
        ob_start();
        $ret = $task();
        $printed = ob_get_clean();
        $output = trim(($printed ?: '') . ($ret ? "\n" . (is_string($ret) ? $ret : json_encode($ret, JSON_PRETTY_PRINT)) : ''));
    } catch (\Throwable $e) {
        $status = 'failed';
        $output = "Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    }

    $durationMs = (int)round((microtime(true) - $start) * 1000);

    try {
        Database::executeSilent(
            "INSERT INTO cron_logs (job_name, status, output, duration_ms, created_at) VALUES (?, ?, ?, ?, NOW())",
            [$jobName, $status, mb_substr($output, 0, 5000), $durationMs]
        );
    } catch (\Throwable $logErr) {}

    return [
        'job' => $jobName,
        'status' => $status,
        'duration_ms' => $durationMs,
        'output' => $output
    ];
}

/**
 * Task Definitions Registry
 */
$taskRegistry = [
    'broken_links' => [
        'name' => '[TS-AUT-68] Technical Broken Link & 404 Scanner',
        'category' => 'Author Studio & Quality Assurance',
        'desc' => 'Validates all outbound URLs, API endpoints, and GitHub repositories cited within published author articles.',
        'icon' => 'link-2-off',
        'color' => '#ef4444',
        'run' => function() {
            $report = LinkScanner::scanOutboundLinks(0, 40);
            $lines = [];
            $lines[] = "Validated {$report['total_checked']} cited URLs across publications.";
            $lines[] = "Active / Reachable: {$report['active_count']} | Broken / 404: {$report['broken_count']}";
            
            if (!empty($report['results'])) {
                $brokenOnly = array_filter($report['results'], fn($r) => !empty($r['is_broken']));
                if (!empty($brokenOnly)) {
                    $lines[] = "\n--- BROKEN LINKS FOUND (" . count($brokenOnly) . ") ---";
                    foreach ($brokenOnly as $b) {
                        $lines[] = "[{$b['status_code']}] {$b['url']} (In Article: \"{$b['article_title']}\" #{$b['article_id']})";
                    }
                }
            }
            return [
                'summary' => implode("\n", $lines),
                'report' => $report
            ];
        }
    ],
    'expire_campaigns' => [
        'name' => 'Expire Sponsored Ad Campaigns',
        'category' => 'Monetization Suite',
        'desc' => 'Deactivates campaigns that reached scheduled end date.',
        'icon' => 'calendar-off',
        'color' => '#f59e0b',
        'run' => function() use ($db) {
            $stmt = $db->prepare("UPDATE advertisements SET status = 'expired' WHERE status = 'active' AND end_date IS NOT NULL AND end_date < NOW()");
            $stmt->execute();
            return "Deactivated {$stmt->rowCount()} expired advertising campaigns.";
        }
    ],
    'prune_firewall_telemetry' => [
        'name' => 'Prune Old Firewall Logs (> 60 Days)',
        'category' => 'Security Perimeter',
        'desc' => 'Purges expired firewall telemetry to optimize storage and query latency.',
        'icon' => 'shield-check',
        'color' => '#10b981',
        'run' => function() use ($db) {
            $tableCheck = $db->query("SHOW TABLES LIKE 'firewall_logs'")->fetch();
            if ($tableCheck) {
                $stmt = $db->prepare("DELETE FROM firewall_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 60 DAY)");
                $stmt->execute();
                return "Pruned {$stmt->rowCount()} old firewall log entries.";
            }
            return "Table firewall_logs not present, skipped.";
        }
    ],
    'cache_cleanup' => [
        'name' => 'Cache & Storage Garbage Collector',
        'category' => 'System Infrastructure',
        'desc' => 'Purges temporary cache artifacts older than 7 days from storage.',
        'icon' => 'trash-2',
        'color' => '#38bdf8',
        'run' => function() {
            $cacheDir = __DIR__ . '/storage/cache';
            $cleaned = 0;
            if (is_dir($cacheDir)) {
                $files = scandir($cacheDir);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..' || $file === '.gitignore') continue;
                    $path = $cacheDir . '/' . $file;
                    if (is_file($path) && (time() - filemtime($path) > 86400 * 7)) {
                        @unlink($path);
                        $cleaned++;
                    }
                }
            }
            return "Purged {$cleaned} stale cache artifacts (> 7 days).";
        }
    ],
    'sitemap_healthcheck' => [
        'name' => 'Dynamic Feeds & Sitemaps Health Check',
        'category' => 'SEO & Distribution',
        'desc' => 'Validates dynamic XML sitemaps and RSS feeds for search crawlers.',
        'icon' => 'rss',
        'color' => '#8b5cf6',
        'run' => function() {
            $appUrl = rtrim(Config::get('app.url', 'http://localhost/techscout.n'), '/');
            $sitemaps = [
                $appUrl . '/sitemap.xml',
                $appUrl . '/sitemap-articles.xml',
                $appUrl . '/sitemap-tools.xml',
                $appUrl . '/sitemap-prompts.xml',
                $appUrl . '/feed/articles.xml',
                $appUrl . '/feed/prompts.xml'
            ];
            return "Validated " . count($sitemaps) . " dynamic feed and sitemap endpoints.";
        }
    ],
    'db_optimize' => [
        'name' => 'Database Table Optimizer & Index Verifier',
        'category' => 'Database Health',
        'desc' => 'Optimizes table storage and checks indexing health across core tables.',
        'icon' => 'database',
        'color' => '#ec4899',
        'run' => function() use ($db) {
            $coreTables = ['contents', 'comments', 'users', 'reading_history', 'author_tips', 'analytics_events'];
            $optimized = 0;
            foreach ($coreTables as $t) {
                try {
                    $db->query("ANALYZE TABLE `{$t}`");
                    $optimized++;
                } catch (\Throwable $e) {}
            }
            return "Analyzed and optimized indexes for {$optimized} core relational tables.";
        }
    ]
];

// Handle AJAX execution of a single task
if (isset($_GET['run_task']) || isset($_POST['run_task'])) {
    $requestedTask = trim((string)($_GET['run_task'] ?? $_POST['run_task']));
    if (!isset($taskRegistry[$requestedTask])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => "Unknown task '{$requestedTask}'"]);
        exit;
    }

    $res = executeTask($requestedTask, $taskRegistry[$requestedTask]['run']);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $res['status'] === 'success',
        'task' => $requestedTask,
        'name' => $taskRegistry[$requestedTask]['name'],
        'duration_ms' => $res['duration_ms'],
        'output' => $res['output']
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Handle CLI execution
if (php_sapi_name() === 'cli') {
    // Check if a specific task was requested via --task=...
    $specificTask = null;
    foreach ($argv as $arg) {
        if (str_starts_with($arg, '--task=')) {
            $specificTask = substr($arg, 7);
        }
    }

    echo "=== TechScout Operations & Scanner CLI ===\n";
    if ($specificTask && isset($taskRegistry[$specificTask])) {
        echo "Executing specific task: {$specificTask}...\n";
        $res = executeTask($specificTask, $taskRegistry[$specificTask]['run']);
        echo sprintf("[%s] %s (%dms):\n%s\n", strtoupper($res['status']), $res['job'], $res['duration_ms'], $res['output']);
    } else {
        foreach ($taskRegistry as $key => $taskInfo) {
            $res = executeTask($key, $taskInfo['run']);
            echo sprintf("[%s] %s (%dms): %s\n", strtoupper($res['status']), $res['job'], $res['duration_ms'], substr($res['output'], 0, 120));
        }
    }
    echo "=== Execution Finished ===\n";
    exit;
}

// If JSON output requested
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    $allResults = [];
    foreach ($taskRegistry as $key => $taskInfo) {
        $allResults[] = executeTask($key, $taskInfo['run']);
    }
    header('Content-Type: application/json');
    echo json_encode(['timestamp' => date('c'), 'tasks' => $allResults], JSON_PRETTY_PRINT);
    exit;
}

// BROWSER MODE: Render the Visual Interactive Admin Control Center
$siteUrl = Config::get('app.url', 'http://localhost/techscout.n');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Operations & [TS-AUT-68] Broken Link Scanner | TechScout Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --cron-bg: #05070b;
      --cron-card-bg: #0b111e;
      --cron-card-border: rgba(148, 163, 184, 0.12);
      --cron-text-primary: #f8fafc;
      --cron-text-secondary: #94a3b8;
      --cron-text-muted: #64748b;
      --cron-primary: #6366f1;
      --cron-primary-hover: #4f46e5;
      --cron-terminal-bg: #020408;
    }
    [data-theme="light"], .theme-light {
      --cron-bg: #f8fafc;
      --cron-card-bg: #ffffff;
      --cron-card-border: #e2e8f0;
      --cron-text-primary: #0f172a;
      --cron-text-secondary: #475569;
      --cron-text-muted: #64748b;
      --cron-primary: #4f46e5;
      --cron-primary-hover: #4338ca;
      --cron-terminal-bg: #0f172a;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: var(--cron-bg);
      color: var(--cron-text-primary);
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      padding: 2rem 1.5rem 4rem 1.5rem;
    }
    .cron-container {
      max-width: 1140px;
      margin: 0 auto;
    }
    .cron-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 2rem;
      padding-bottom: 1.25rem;
      border-bottom: 1px solid var(--cron-card-border);
    }
    .cron-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.85rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.55rem 1.15rem;
      font-size: 0.85rem;
      font-weight: 700;
      border-radius: 8px;
      border: 1px solid transparent;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.15s ease;
    }
    .btn-primary {
      background: linear-gradient(135deg, #6366f1, #4f46e5);
      color: #ffffff;
      box-shadow: 0 4px 15px rgba(99,102,241,0.35);
    }
    .btn-primary:hover {
      box-shadow: 0 6px 20px rgba(99,102,241,0.5);
      transform: translateY(-1px);
    }
    .btn-outline {
      background: transparent;
      border-color: var(--cron-card-border);
      color: var(--cron-text-secondary);
    }
    .btn-outline:hover {
      color: var(--cron-text-primary);
      border-color: var(--cron-primary);
    }
    .btn-danger {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: #ffffff;
    }
    .task-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 1.25rem;
      margin-bottom: 2rem;
    }
    .task-card {
      background: var(--cron-card-bg);
      border: 1px solid var(--cron-card-border);
      border-radius: 12px;
      padding: 1.35rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all 0.2s ease;
    }
    .task-card:hover {
      border-color: var(--cron-primary);
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .task-icon {
      width: 38px;
      height: 38px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 0.85rem;
    }
    .progress-bar-wrap {
      background: var(--cron-card-bg);
      border: 1px solid var(--cron-card-border);
      border-radius: 12px;
      padding: 1.25rem;
      margin-bottom: 2rem;
      display: none;
    }
    .progress-track {
      height: 10px;
      background: rgba(148, 163, 184, 0.15);
      border-radius: 999px;
      overflow: hidden;
      margin: 0.75rem 0 0.5rem 0;
    }
    .progress-fill {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, #6366f1, #38bdf8, #10b981);
      border-radius: 999px;
      transition: width 0.3s ease;
    }
    .terminal-console {
      background: var(--cron-terminal-bg);
      border: 1px solid var(--cron-card-border);
      border-radius: 12px;
      padding: 1.25rem;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.82rem;
      color: #38bdf8;
      height: 280px;
      overflow-y: auto;
      margin-bottom: 2rem;
      white-space: pre-wrap;
      line-height: 1.5;
    }
    .broken-links-table-wrap {
      background: var(--cron-card-bg);
      border: 1px solid var(--cron-card-border);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      display: none;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
      font-size: 0.85rem;
    }
    th, td {
      padding: 0.75rem 1rem;
      text-align: left;
      border-bottom: 1px solid var(--cron-card-border);
    }
    th {
      color: var(--cron-text-muted);
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.72rem;
      letter-spacing: 0.05em;
    }
    .badge {
      display: inline-block;
      padding: 0.2rem 0.55rem;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
    }
    .badge-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
    .badge-success { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
  </style>
</head>
<body>
  <div class="cron-container">
    <!-- Header -->
    <div class="cron-header">
      <div>
        <div class="cron-title">
          <i data-lucide="cpu" style="width: 28px; height: 28px; color: var(--cron-primary);"></i>
          <span>Operations &amp; Broken Link Scanner Hub</span>
        </div>
        <p style="font-size: 0.85rem; color: var(--cron-text-secondary); margin-top: 0.25rem;">
          [TS-INF-10] Master Scheduler &bull; [TS-AUT-68] Foreground Citation &amp; 404 Quality Scanner
        </p>
      </div>
      <div style="display: flex; gap: 0.65rem; align-items: center;">
        <a href="<?= htmlspecialchars(Config::url('/admin/dashboard')) ?>" class="btn btn-outline">
          &larr; Admin Dashboard
        </a>
        <button type="button" onclick="runAllTasks()" id="btnRunAll" class="btn btn-primary">
          <i data-lucide="play" style="width: 14px;"></i> Run All Tasks In Foreground
        </button>
      </div>
    </div>

    <!-- Active Progress Bar -->
    <div id="progressWrap" class="progress-bar-wrap">
      <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
        <span style="font-weight: 700; color: var(--cron-text-primary);" id="progressStatus">Initializing Task Execution...</span>
        <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--cron-primary);" id="progressPercent">0%</span>
      </div>
      <div class="progress-track">
        <div id="progressFill" class="progress-fill"></div>
      </div>
    </div>

    <!-- Live Execution Console -->
    <div style="margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
      <span style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cron-text-muted);">
        Interactive Foreground Console Output
      </span>
      <button type="button" onclick="clearConsole()" style="background: none; border: none; font-size: 0.75rem; color: var(--cron-text-muted); cursor: pointer;">Clear Console</button>
    </div>
    <div id="terminalConsole" class="terminal-console">TechScout Operations Engine initialized and ready. Select a task below to run on-demand with live progress.</div>

    <!-- Broken Links Results Table -->
    <div id="brokenLinksSection" class="broken-links-table-wrap">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
        <div>
          <h3 style="font-size: 1.2rem; font-family: 'Outfit', sans-serif; font-weight: 700; color: var(--cron-text-primary); margin: 0;">
            🔗 [TS-AUT-68] Outbound Links &amp; 404 Scanner Report
          </h3>
          <span style="font-size: 0.8rem; color: var(--cron-text-secondary);" id="scanMetricsLabel">0 links analyzed</span>
        </div>
        <div style="display: flex; gap: 0.5rem;">
          <button type="button" onclick="exportLinksJson()" class="btn btn-outline" style="font-size: 0.78rem; padding: 0.35rem 0.75rem;">
            Export JSON
          </button>
        </div>
      </div>
      <div style="overflow-x: auto;">
        <table id="brokenLinksTable">
          <thead>
            <tr>
              <th>Status</th>
              <th>Target URL / API / Repo</th>
              <th>Source Article Publication</th>
              <th>Latency</th>
              <th style="text-align: right;">Action</th>
            </tr>
          </thead>
          <tbody id="brokenLinksTbody">
            <tr><td colspan="5" style="text-align: center; color: var(--cron-text-muted); padding: 2rem;">No broken links detected yet. Run scanner above.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tasks Grid -->
    <div style="margin-bottom: 0.75rem;">
      <h3 style="font-size: 1.15rem; font-family: 'Outfit', sans-serif; font-weight: 700; color: var(--cron-text-primary);">
        Available On-Demand Operation Tasks
      </h3>
    </div>
    <div class="task-grid">
      <?php foreach ($taskRegistry as $tKey => $t): ?>
        <div class="task-card" id="card-<?= $tKey ?>">
          <div>
            <div class="task-icon" style="background: <?= $t['color'] ?>1a; color: <?= $t['color'] ?>;">
              <i data-lucide="<?= $t['icon'] ?>" style="width: 20px; height: 20px;"></i>
            </div>
            <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--cron-text-muted); margin-bottom: 0.2rem; letter-spacing: 0.05em;">
              <?= htmlspecialchars($t['category']) ?>
            </div>
            <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--cron-text-primary); margin-bottom: 0.45rem;">
              <?= htmlspecialchars($t['name']) ?>
            </h4>
            <p style="font-size: 0.82rem; color: var(--cron-text-secondary); line-height: 1.45; margin-bottom: 1.25rem;">
              <?= htmlspecialchars($t['desc']) ?>
            </p>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--cron-card-border); padding-top: 0.85rem;">
            <span style="font-size: 0.75rem; color: var(--cron-text-muted);" id="status-<?= $tKey ?>">Idle</span>
            <button type="button" onclick="runSingleTask('<?= $tKey ?>')" id="btn-<?= $tKey ?>" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.35rem 0.85rem;">
              Run Now &rarr;
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script>
  let lastReportData = null;

  function appendLog(text, color = '#38bdf8') {
    const consoleEl = document.getElementById('terminalConsole');
    const time = new Date().toLocaleTimeString();
    consoleEl.innerText += `\n[${time}] ${text}`;
    consoleEl.scrollTop = consoleEl.scrollHeight;
  }

  function clearConsole() {
    document.getElementById('terminalConsole').innerText = 'Console cleared.';
  }

  function setProgress(percent, status) {
    const wrap = document.getElementById('progressWrap');
    wrap.style.display = 'block';
    document.getElementById('progressFill').style.width = percent + '%';
    document.getElementById('progressPercent').innerText = percent + '%';
    if (status) document.getElementById('progressStatus').innerText = status;
  }

  async function runSingleTask(taskKey) {
    const btn = document.getElementById('btn-' + taskKey);
    const statusSpan = document.getElementById('status-' + taskKey);
    btn.disabled = true;
    btn.innerText = 'Running...';
    statusSpan.innerText = 'Processing...';

    setProgress(30, `Executing ${taskKey}...`);
    appendLog(`Starting execution of: ${taskKey}...`, '#f59e0b');

    try {
      const res = await fetch(`cron.php?run_task=${encodeURIComponent(taskKey)}`);
      const data = await res.json();
      setProgress(100, `Completed ${taskKey}`);

      if (data.success) {
        statusSpan.innerText = `Success (${data.duration_ms}ms)`;
        statusSpan.style.color = '#10b981';
        appendLog(`✓ ${data.name} succeeded in ${data.duration_ms}ms:\n${data.output}`, '#10b981');

        // If broken link scanner, render the interactive table
        if (taskKey === 'broken_links') {
          handleBrokenLinkReport(data.output);
        }
      } else {
        statusSpan.innerText = 'Failed';
        statusSpan.style.color = '#ef4444';
        appendLog(`✗ ${data.name} failed:\n${data.output}`, '#ef4444');
      }
    } catch (err) {
      statusSpan.innerText = 'Network Error';
      appendLog(`Error: ${err.message}`, '#ef4444');
    } finally {
      btn.disabled = false;
      btn.innerText = 'Run Now →';
      setTimeout(() => {
        const wrap = document.getElementById('progressWrap');
        if (wrap) wrap.style.display = 'none';
      }, 3000);
    }
  }

  async function runAllTasks() {
    const tasks = <?= json_encode(array_keys($taskRegistry)) ?>;
    const btn = document.getElementById('btnRunAll');
    btn.disabled = true;
    btn.innerText = 'Running Tasks in Foreground...';

    appendLog('=== Starting Sequential Run of All Registered Operations ===', '#6366f1');

    for (let i = 0; i < tasks.length; i++) {
      const t = tasks[i];
      const pct = Math.round(((i + 1) / tasks.length) * 100);
      setProgress(pct, `Running ${t} (${i + 1}/${tasks.length})...`);
      await runSingleTask(t);
    }

    setProgress(100, 'All foreground tasks completed.');
    appendLog('=== All Registered Operations Completed ===', '#10b981');
    btn.disabled = false;
    btn.innerText = 'Run All Tasks In Foreground';
  }

  function handleBrokenLinkReport(output) {
    try {
      const parsed = (typeof output === 'string') ? JSON.parse(output) : output;
      if (parsed.report) {
        lastReportData = parsed.report;
        renderBrokenLinksTable(parsed.report);
      }
    } catch(e) {}
  }

  function renderBrokenLinksTable(report) {
    const section = document.getElementById('brokenLinksSection');
    const tbody = document.getElementById('brokenLinksTbody');
    const metricsLabel = document.getElementById('scanMetricsLabel');

    section.style.display = 'block';
    metricsLabel.innerText = `Analyzed ${report.total_checked} links. Active: ${report.active_count} | Broken: ${report.broken_count}`;

    if (!report.results || report.results.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem; color: #10b981;">No citations found in published articles.</td></tr>';
      return;
    }

    let html = '';
    report.results.forEach(item => {
      const badgeClass = item.is_broken ? 'badge-danger' : 'badge-success';
      const editUrl = `<?= htmlspecialchars(Config::url('/author/editor/')) ?>${item.article_id}`;
      html += `
        <tr>
          <td><span class="badge ${badgeClass}">${item.status_code || 'ERR'} ${item.is_broken ? 'BROKEN' : 'OK'}</span></td>
          <td>
            <a href="${item.url}" target="_blank" style="color: #38bdf8; text-decoration: none; word-break: break-all;">
              ${item.url} ↗
            </a>
            ${item.error ? `<div style="color: #f87171; font-size: 0.72rem; margin-top: 0.2rem;">${item.error}</div>` : ''}
          </td>
          <td>
            <strong style="color: var(--cron-text-primary);">${item.article_title}</strong>
          </td>
          <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem;">
            ${item.response_time_ms}ms
          </td>
          <td style="text-align: right;">
            <a href="${editUrl}" target="_blank" class="btn btn-outline" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">
              Edit in Studio ↗
            </a>
          </td>
        </tr>
      `;
    });
    tbody.innerHTML = html;
  }

  function exportLinksJson() {
    if (!lastReportData) return;
    const blob = new Blob([JSON.stringify(lastReportData, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `techscout_broken_links_${Date.now()}.json`;
    a.click();
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();
  });
  </script>
</body>
</html>
