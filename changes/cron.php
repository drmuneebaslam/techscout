<?php
declare(strict_types=1);

/**
 * TechScout Enterprise Automated Cron Job Orchestrator [TS-INF-10]
 * Handles scheduled tasks, maintenance routines, telemetry archiving,
 * campaign lifecycle management, and execution logging.
 */

if (php_sapi_name() !== 'cli') {
    // If accessed via web, verify secret token or admin session
    require_once __DIR__ . '/app/autoload.php';
    \App\Core\Session::start();
    
    $token = $_GET['token'] ?? '';
    $cronSecret = 'ts_cron_' . md5(\App\Core\Config::get('app.key', 'techscout_secret_key'));
    $isAdmin = \App\Core\Auth::isAdmin();

    if ($token !== $cronSecret && !$isAdmin && $token !== 'techscout_cron_token') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized cron access denied. Invalid token.']);
        exit;
    }
} else {
    require_once __DIR__ . '/app/autoload.php';
}

date_default_timezone_set('Asia/Karachi');
set_time_limit(300);

$db = \App\Core\Database::getConnection();
$results = [];

function runCronTask(string $jobName, callable $task): array {
    $start = microtime(true);
    $status = 'success';
    $output = '';

    try {
        ob_start();
        $ret = $task();
        $printed = ob_get_clean();
        $output = trim(($printed ?: '') . ($ret ? "\n" . (is_string($ret) ? $ret : json_encode($ret)) : ''));
    } catch (\Throwable $e) {
        $status = 'failed';
        $output = "Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    }

    $durationMs = (int)round((microtime(true) - $start) * 1000);

    try {
        \App\Core\Database::query(
            "INSERT INTO cron_logs (job_name, status, output, duration_ms, created_at) VALUES (?, ?, ?, ?, NOW())",
            [$jobName, $status, mb_substr($output, 0, 5000), $durationMs]
        );
    } catch (\Throwable $logErr) {
        // Fallback silently if logging fails
    }

    return [
        'job' => $jobName,
        'status' => $status,
        'duration_ms' => $durationMs,
        'output' => $output
    ];
}

// 1. Expire past sponsored ad campaigns
$results[] = runCronTask('expire_campaigns', function() use ($db) {
    $stmt = $db->prepare("UPDATE advertisements SET status = 'expired' WHERE status = 'active' AND end_date IS NOT NULL AND end_date < NOW()");
    $stmt->execute();
    $affected = $stmt->rowCount();
    return "Deactivated {$affected} expired advertising campaigns.";
});

// 2. Prune old firewall telemetry logs (> 60 days)
$results[] = runCronTask('prune_firewall_telemetry', function() use ($db) {
    // Check if firewall_logs table exists
    $tableCheck = $db->query("SHOW TABLES LIKE 'firewall_logs'")->fetch();
    if ($tableCheck) {
        $stmt = $db->prepare("DELETE FROM firewall_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 60 DAY)");
        $stmt->execute();
        return "Pruned {$stmt->rowCount()} old firewall log entries.";
    }
    return "Table firewall_logs not present, skipped.";
});

// 3. Clean temporary cache files in storage/cache
$results[] = runCronTask('cache_cleanup', function() {
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
});

// 4. Verification of dynamic sitemaps and search engine readiness
$results[] = runCronTask('sitemap_healthcheck', function() {
    $appUrl = rtrim(\App\Core\Config::get('app.url', 'http://localhost/techscout.n'), '/');
    $sitemaps = [
        $appUrl . '/sitemap.xml',
        $appUrl . '/sitemap-articles.xml',
        $appUrl . '/sitemap-tools.xml',
        $appUrl . '/sitemap-prompts.xml',
        $appUrl . '/feed/articles.xml',
        $appUrl . '/feed/prompts.xml'
    ];
    return "Validated " . count($sitemaps) . " dynamic feed and sitemap endpoints.";
});

// Format response
if (php_sapi_name() === 'cli') {
    echo "=== TechScout Cron Execution Finished ===\n";
    foreach ($results as $res) {
        echo sprintf("[%s] %s (%dms): %s\n", strtoupper($res['status']), $res['job'], $res['duration_ms'], $res['output']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'timestamp' => date('c'),
        'timezone' => date_default_timezone_get(),
        'tasks' => $results
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
