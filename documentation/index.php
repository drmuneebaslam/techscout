<?php
/**
 * TechScout Enterprise Web Documentation Reader & QA Portal
 * Built for running on XAMPP (e.g. http://localhost/techscout.n/documentation/)
 * 
 * SaaS-Level Documentation Platform Features:
 * - 3 Organized Documentation Categorizations:
 *   1. 📘 Master Reference Guides (documentation/*.md)
 *   2. 🛠️ Project Codebase Specifications (documentation/project/*.md)
 *   3. 🧪 Enterprise QA Testing Suite (documentation/qa/*.md)
 * - Category Filter Tabs (All / Master / Project / QA Suite / Favorites)
 * - Favorites & Bookmarking System (Persisted in localStorage)
 * - 100% Server-Side PHP Markdown Parsing for Guaranteed Content Visibility
 * - Cross-Document Internal Markdown Link Resolution
 * - GitHub Alert Callouts (> [!NOTE], [!TIP], [!IMPORTANT], [!WARNING], [!CAUTION])
 * - Interactive Task Checklists (- [ ] / - [x])
 * - Reading Progress Bar & Scroll-Spy Table of Contents (TOC)
 * - Command Palette Search Modal (Ctrl+K / Cmd+K)
 * - Font Scale Controls & Distraction-Free Focus Mode
 * - Persistent Dark/Light Mode with Syntax Highlighting Theme Sync
 */

$docDir = __DIR__;
$projectDir = __DIR__ . '/project';
$qaDir = __DIR__ . '/qa';

// Comprehensive Server-Side PHP Markdown Renderer
function renderMarkdownToHtml(string $md, string $currentFile = ''): string {
    $lines = explode("\n", $md);
    $html = '';
    $inCode = false;
    $codeLang = '';
    $codeBuffer = [];
    $inList = false;
    $listType = '';
    $inTable = false;
    $tableRows = [];
    $inCallout = false;
    $calloutType = '';

    foreach ($lines as $line) {
        $trimmed = trim($line);

        // Code Block Toggle
        if (str_starts_with($trimmed, '```')) {
            if ($inCode) {
                $codeContent = htmlspecialchars(implode("\n", $codeBuffer), ENT_QUOTES, 'UTF-8');
                $langClass = $codeLang ? ' class="language-' . htmlspecialchars($codeLang) . '"' : '';
                $langBadge = $codeLang ? '<span class="code-lang-badge">' . htmlspecialchars(strtoupper($codeLang)) . '</span>' : '';
                $html .= "<div class=\"code-block-wrapper\">{$langBadge}<button class=\"copy-code-btn\" onclick=\"copyCodeBlock(this)\"><i data-lucide=\"copy\"></i> <span>Copy Code</span></button><pre><code{$langClass}>{$codeContent}</code></pre></div>\n";
                $codeBuffer = [];
                $inCode = false;
            } else {
                if ($inList) { $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n"); $inList = false; }
                if ($inTable) { $html .= renderTableHtml($tableRows, $currentFile); $tableRows = []; $inTable = false; }
                if ($inCallout) { $html .= "</div></div>\n"; $inCallout = false; }
                $inCode = true;
                $codeLang = trim(substr($trimmed, 3));
            }
            continue;
        }

        if ($inCode) {
            $codeBuffer[] = $line;
            continue;
        }

        // GitHub Callouts / Alerts (> [!NOTE], [!TIP], etc.)
        if (preg_match('/^>\s*\[\!(NOTE|TIP|IMPORTANT|WARNING|CAUTION)\]\s*(.*)$/i', $trimmed, $m)) {
            if ($inList) { $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n"); $inList = false; }
            if ($inTable) { $html .= renderTableHtml($tableRows, $currentFile); $tableRows = []; $inTable = false; }
            if ($inCallout) { $html .= "</div></div>\n"; }

            $calloutType = strtoupper($m[1]);
            $firstLineText = parseInlineMarkdown($m[2], $currentFile);
            $iconMap = [
                'NOTE' => 'info',
                'TIP' => 'lightbulb',
                'IMPORTANT' => 'alert-circle',
                'WARNING' => 'alert-triangle',
                'CAUTION' => 'shield-alert'
            ];
            $icon = $iconMap[$calloutType] ?? 'info';

            $html .= "<div class=\"callout callout-{$calloutType}\">";
            $html .= "<div class=\"callout-header\"><i data-lucide=\"{$icon}\"></i> <span>{$calloutType}</span></div>";
            $html .= "<div class=\"callout-body\"><p>{$firstLineText}</p>";
            $inCallout = true;
            continue;
        }

        // Continued Callout text
        if ($inCallout && str_starts_with($trimmed, '>')) {
            $calloutLine = parseInlineMarkdown(trim(substr($trimmed, 1)), $currentFile);
            if (!empty($calloutLine)) {
                $html .= "<p>{$calloutLine}</p>";
            }
            continue;
        } elseif ($inCallout && empty($trimmed)) {
            // Keep callout open
        } elseif ($inCallout && !str_starts_with($trimmed, '>')) {
            $html .= "</div></div>\n";
            $inCallout = false;
        }

        // Table Rows
        if (str_starts_with($trimmed, '|') && str_ends_with($trimmed, '|')) {
            if ($inList) { $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n"); $inList = false; }
            $inTable = true;
            $tableRows[] = $trimmed;
            continue;
        } elseif ($inTable) {
            $html .= renderTableHtml($tableRows, $currentFile);
            $tableRows = [];
            $inTable = false;
        }

        // Headings (H1 to H6)
        if (preg_match('/^(#{1,6})\s+(.+)$/', $trimmed, $m)) {
            if ($inList) { $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n"); $inList = false; }
            $level = strlen($m[1]);
            $title = $m[2];
            $slug = strtolower(preg_replace('/[^\w]+/', '-', strip_tags($title)));
            $inline = parseInlineMarkdown($title, $currentFile);
            $html .= "<h{$level} id=\"{$slug}\" class=\"doc-heading\">{$inline}<a href=\"#{$slug}\" class=\"heading-anchor\" onclick=\"copyHeadingAnchor('{$slug}', event)\" title=\"Copy Link to Heading\"><i data-lucide=\"link\"></i></a></h{$level}>\n";
            continue;
        }

        // Horizontal Rule
        if ($trimmed === '---' || $trimmed === '***' || $trimmed === '___') {
            if ($inList) { $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n"); $inList = false; }
            $html .= "<hr>\n";
            continue;
        }

        // Blockquotes
        if (str_starts_with($trimmed, '>')) {
            if ($inList) { $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n"); $inList = false; }
            $quoteText = parseInlineMarkdown(trim(substr($trimmed, 1)), $currentFile);
            $html .= "<blockquote><p>{$quoteText}</p></blockquote>\n";
            continue;
        }

        // Task Checkboxes (- [ ] or - [x])
        if (preg_match('/^[\-\*\+]\s+\[([ xX])\]\s+(.+)$/', $trimmed, $m)) {
            $isChecked = strtolower($m[1]) === 'x';
            $checkedAttr = $isChecked ? 'checked disabled' : 'disabled';
            $itemClass = $isChecked ? 'task-checked' : 'task-pending';
            $text = parseInlineMarkdown($m[2], $currentFile);
            if (!$inList || $listType !== 'ul_task') {
                if ($inList) $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n");
                $html .= "<ul class=\"task-list\">\n";
                $inList = true;
                $listType = 'ul_task';
            }
            $html .= "<li class=\"task-list-item {$itemClass}\"><input type=\"checkbox\" {$checkedAttr}> <span>{$text}</span></li>\n";
            continue;
        }

        // Unordered Lists
        if (preg_match('/^[\-\*\+]\s+(.+)$/', $trimmed, $m)) {
            if (!$inList || $listType !== 'ul') {
                if ($inList) $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n");
                $html .= "<ul>\n";
                $inList = true;
                $listType = 'ul';
            }
            $html .= "<li>" . parseInlineMarkdown($m[1], $currentFile) . "</li>\n";
            continue;
        }

        // Ordered Lists
        if (preg_match('/^\d+\.\s+(.+)$/', $trimmed, $m)) {
            if (!$inList || $listType !== 'ol') {
                if ($inList) $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n");
                $html .= "<ol>\n";
                $inList = true;
                $listType = 'ol';
            }
            $html .= "<li>" . parseInlineMarkdown($m[1], $currentFile) . "</li>\n";
            continue;
        }

        // Empty lines close lists
        if (empty($trimmed)) {
            if ($inList) { $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n"); $inList = false; }
            continue;
        }

        // Paragraphs
        if ($inList) { $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n"); $inList = false; }
        $html .= "<p>" . parseInlineMarkdown($line, $currentFile) . "</p>\n";
    }

    if ($inCode) {
        $codeContent = htmlspecialchars(implode("\n", $codeBuffer), ENT_QUOTES, 'UTF-8');
        $html .= "<div class=\"code-block-wrapper\"><pre><code>{$codeContent}</code></pre></div>\n";
    }
    if ($inList) {
        $html .= ($listType === 'ul' || $listType === 'ul_task' ? "</ul>\n" : "</ol>\n");
    }
    if ($inTable) {
        $html .= renderTableHtml($tableRows, $currentFile);
    }
    if ($inCallout) {
        $html .= "</div></div>\n";
    }

    return $html;
}

function parseInlineMarkdown(string $text, string $currentFile = ''): string {
    // Bold
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    // Italic
    $text = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $text);
    // Strikethrough
    $text = preg_replace('/~~(.*?)~~/', '<del>$1</del>', $text);
    // Inline Code
    $text = preg_replace_callback('/`([^`]+)`/', function($m) {
        return '<code>' . htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8') . '</code>';
    }, $text);
    
    // Internal & External Link Resolution
    $text = preg_replace_callback('/\[([^\]]+)\]\(([^\)]+)\)/', function($m) use ($currentFile) {
        $label = $m[1];
        $url = trim($m[2]);
        
        if (str_ends_with(strtolower($url), '.md') && !preg_match('/^https?:\/\//i', $url)) {
            $cleanUrl = ltrim($url, './');
            if (str_starts_with($currentFile, 'project/') && !str_starts_with($cleanUrl, 'project/') && !str_starts_with($cleanUrl, 'qa/')) {
                $targetFile = 'project/' . $cleanUrl;
            } elseif (str_starts_with($currentFile, 'qa/') && !str_starts_with($cleanUrl, 'qa/') && !str_starts_with($cleanUrl, 'project/')) {
                $targetFile = 'qa/' . $cleanUrl;
            } else {
                $targetFile = $cleanUrl;
            }
            return '<a href="index.php?file=' . urlencode($targetFile) . '" class="doc-internal-link" title="Open documentation file"><i data-lucide="file-text" class="inline-doc-icon"></i> ' . $label . '</a>';
        }
        
        return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener" class="doc-external-link">' . $label . ' <i data-lucide="external-link" class="inline-icon"></i></a>';
    }, $text);

    return $text;
}

function renderTableHtml(array $rows, string $currentFile = ''): string {
    if (count($rows) < 2) return '';
    $html = "<div class=\"table-responsive\"><table>\n";
    $isHeader = true;

    foreach ($rows as $row) {
        $cells = array_map('trim', explode('|', trim($row, '|')));
        if (isset($cells[0]) && preg_match('/^[\:\-\s]+$/', $cells[0])) {
            continue;
        }

        if ($isHeader) {
            $html .= "<thead><tr>\n";
            foreach ($cells as $cell) {
                $html .= "<th>" . parseInlineMarkdown($cell, $currentFile) . "</th>\n";
            }
            $html .= "</tr></thead>\n<tbody>\n";
            $isHeader = false;
        } else {
            $html .= "<tr>\n";
            foreach ($cells as $cell) {
                $html .= "<td>" . parseInlineMarkdown($cell, $currentFile) . "</td>\n";
            }
            $html .= "</tr>\n";
        }
    }
    $html .= "</tbody>\n</table></div>\n";
    return $html;
}

function getMarkdownFiles(string $dir, string $relativePrefix = '', string $categoryName = 'Master'): array {
    $files = [];
    if (!is_dir($dir)) return $files;
    
    $scanned = scandir($dir);
    foreach ($scanned as $f) {
        if ($f === '.' || $f === '..') continue;
        $fullPath = $dir . '/' . $f;
        if (is_file($fullPath) && pathinfo($f, PATHINFO_EXTENSION) === 'md') {
            $content = file_get_contents($fullPath);
            $lineCount = count(explode("\n", $content));
            $wordCount = str_word_count(strip_tags($content));
            
            $files[] = [
                'name' => $f,
                'path' => $relativePrefix . $f,
                'full_path' => $fullPath,
                'size' => filesize($fullPath),
                'lines' => $lineCount,
                'words' => $wordCount,
                'category' => $categoryName,
                'title' => ucwords(str_replace(['_', '.md', 'qa/'], [' ', '', ''], $f))
            ];
        }
    }
    usort($files, fn($a, $b) => strcmp($a['name'], $b['name']));
    return $files;
}

$rootDocs = getMarkdownFiles($docDir, '', 'Master References');
$projectDocs = getMarkdownFiles($projectDir, 'project/', 'Project Specifications');
$qaDocs = getMarkdownFiles($qaDir, 'qa/', 'QA Testing Suite');
$allDocs = array_merge($rootDocs, $projectDocs, $qaDocs);

$selectedFile = $_GET['file'] ?? 'README.md';
$selectedFile = str_replace(['..', '\\'], ['', '/'], $selectedFile);
$targetPath = $docDir . '/' . $selectedFile;

if (!file_exists($targetPath) || pathinfo($targetPath, PATHINFO_EXTENSION) !== 'md') {
    $selectedFile = 'README.md';
    $targetPath = $docDir . '/README.md';
}

$rawMarkdownContent = file_exists($targetPath) ? file_get_contents($targetPath) : '# File Not Found';
$renderedHtml = renderMarkdownToHtml($rawMarkdownContent, $selectedFile);
$pageTitle = ucwords(str_replace(['_', '.md', 'project/', 'qa/'], [' ', '', '', ''], $selectedFile));
$activeFileLines = count(explode("\n", $rawMarkdownContent));
$activeFileWords = str_word_count(strip_tags($rawMarkdownContent));
$activeFileReadingTime = max(1, ceil($activeFileWords / 200));
$activeFileSizeKb = number_format(filesize($targetPath) / 1024, 1);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark" data-font-scale="md">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — TechScout SaaS Docs & Enterprise QA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/tokyo-night-dark.min.css" id="hljs-theme">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --bg-body: #0b0f19;
            --bg-sidebar: #111827;
            --bg-card: rgba(17, 24, 39, 0.75);
            --bg-glass: rgba(17, 24, 39, 0.85);
            --bg-toc: rgba(15, 23, 42, 0.7);
            --border-color: rgba(255, 255, 255, 0.1);
            --border-subtle: rgba(255, 255, 255, 0.05);
            --accent-color: #6366f1;
            --accent-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
            --accent-hover: #4f46e5;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --text-subtle: #6b7280;
            --sidebar-width: 340px;
            --toc-width: 280px;
            --code-bg: #090d16;
            --content-max-width: 960px;
            --font-size-base: 1rem;
        }

        [data-theme="light"] {
            --bg-body: #f8fafc;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --bg-glass: rgba(255, 255, 255, 0.9);
            --bg-toc: #f1f5f9;
            --border-color: #e2e8f0;
            --border-subtle: #f1f5f9;
            --accent-color: #4f46e5;
            --accent-gradient: linear-gradient(135deg, #4f46e5, #7c3aed);
            --accent-hover: #4338ca;
            --text-main: #0f172a;
            --text-muted: #475569;
            --text-subtle: #94a3b8;
            --code-bg: #f1f5f9;
        }

        [data-font-scale="sm"] { --font-size-base: 0.9rem; }
        [data-font-scale="md"] { --font-size-base: 1rem; }
        [data-font-scale="lg"] { --font-size-base: 1.125rem; }
        [data-font-scale="xl"] { --font-size-base: 1.25rem; }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            height: 100vh;
            overflow: hidden;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Top Reading Progress Bar */
        .progress-bar-container {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: transparent;
            z-index: 1000;
        }
        .progress-bar-fill {
            height: 100%;
            width: 0%;
            background: var(--accent-gradient);
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.8);
            transition: width 0.1s linear;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            height: 100%;
            z-index: 20;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-header {
            padding: 20px 20px 14px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-main);
            text-decoration: none;
            margin-bottom: 14px;
            letter-spacing: -0.3px;
        }

        .brand-logo i { color: var(--accent-color); }

        .brand-badge {
            background: var(--accent-gradient);
            color: #fff;
            font-size: 0.65rem;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .category-tabs-group {
            display: flex;
            align-items: center;
            gap: 4px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            padding: 3px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .cat-tab-btn {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 5px 2px;
            border-radius: 6px;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s ease;
        }

        .cat-tab-btn:hover, .cat-tab-btn.active {
            background: var(--accent-color);
            color: #ffffff;
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-box input {
            width: 100%;
            padding: 9px 12px 9px 36px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            font-size: 0.82rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .search-box input:focus {
            border-color: var(--accent-color);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .search-box i {
            position: absolute;
            left: 10px;
            color: var(--text-muted);
            width: 15px; height: 15px;
            pointer-events: none;
        }

        .search-shortcut-kbd {
            position: absolute;
            right: 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.65rem;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-muted);
            padding: 2px 5px;
            border-radius: 4px;
            pointer-events: none;
            border: 1px solid var(--border-color);
        }

        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 14px 12px;
        }

        .section-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 1px;
            margin: 16px 6px 8px 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
        }

        .section-label:first-child { margin-top: 2px; }

        .badge-count {
            background: rgba(99, 102, 241, 0.15);
            color: var(--accent-color);
            padding: 2px 7px;
            border-radius: 12px;
            font-size: 0.68rem;
            font-weight: 700;
        }

        .doc-list { list-style: none; }
        .doc-item { margin-bottom: 2px; }

        .doc-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .doc-link-left {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .doc-link:hover {
            background: rgba(99, 102, 241, 0.08);
            color: var(--text-main);
            transform: translateX(2px);
        }

        .doc-link.active {
            background: var(--accent-gradient);
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .doc-link i {
            width: 15px; height: 15px;
            flex-shrink: 0;
            opacity: 0.75;
        }

        .doc-link.active i { opacity: 1; }

        .line-badge {
            font-size: 0.66rem;
            opacity: 0.85;
            font-family: 'JetBrains Mono', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 2px 5px;
            border-radius: 4px;
        }

        /* Main Workspace */
        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        .top-navbar {
            height: 64px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-glass);
            backdrop-filter: blur(16px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            flex-shrink: 0;
            z-index: 10;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--text-main);
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
        }

        .breadcrumb-trail {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.86rem;
            color: var(--text-muted);
        }

        .breadcrumb-trail a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
        }

        .breadcrumb-trail .current {
            color: var(--text-main);
            font-weight: 700;
        }

        .doc-stats-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.76rem;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        .doc-stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.04);
            padding: 4px 9px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            background: var(--accent-color);
            color: #ffffff;
            border-color: var(--accent-color);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        }

        .font-scale-group {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 2px;
        }

        .font-scale-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
        }

        .font-scale-btn:hover, .font-scale-btn.active {
            background: rgba(255, 255, 255, 0.15);
            color: var(--text-main);
        }

        /* Workspace & Content Area */
        .workspace-area {
            flex: 1;
            display: flex;
            overflow: hidden;
            position: relative;
        }

        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 36px 44px;
            scroll-behavior: smooth;
        }

        .markdown-wrapper {
            max-width: var(--content-max-width);
            margin: 0 auto;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
            line-height: 1.85;
            backdrop-filter: blur(16px);
            font-family: 'Inter', sans-serif;
            font-size: var(--font-size-base);
            transition: font-size 0.2s ease;
        }

        /* Elements Styling */
        .markdown-wrapper h1 {
            font-size: 2.3rem;
            font-weight: 800;
            margin-bottom: 24px;
            color: var(--text-main);
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 14px;
            letter-spacing: -0.5px;
            position: relative;
        }

        .markdown-wrapper h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 40px 0 18px 0;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
            position: relative;
        }

        .markdown-wrapper h3 {
            font-size: 1.28rem;
            font-weight: 600;
            margin: 28px 0 14px 0;
            color: var(--text-main);
            position: relative;
        }

        .markdown-wrapper h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 22px 0 12px 0;
            color: var(--text-main);
            position: relative;
        }

        .doc-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .heading-anchor {
            opacity: 0;
            color: var(--text-subtle);
            margin-left: 10px;
            text-decoration: none;
            transition: opacity 0.2s ease, color 0.2s ease;
        }

        .doc-heading:hover .heading-anchor { opacity: 1; }
        .heading-anchor:hover { color: var(--accent-color); }
        .heading-anchor i { width: 16px; height: 16px; }

        .markdown-wrapper p {
            margin-bottom: 20px;
            color: var(--text-muted);
        }

        .markdown-wrapper ul, .markdown-wrapper ol {
            margin-bottom: 24px;
            padding-left: 28px;
            color: var(--text-muted);
        }

        .markdown-wrapper li { margin-bottom: 8px; }

        .markdown-wrapper code {
            font-family: 'JetBrains Mono', monospace;
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            color: #38bdf8;
            padding: 3px 7px;
            border-radius: 6px;
            font-size: 0.88em;
        }

        .code-block-wrapper {
            position: relative;
            margin-bottom: 28px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            background: #090d16 !important;
        }

        .code-lang-badge {
            position: absolute;
            top: 10px; left: 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.68rem;
            font-weight: 700;
            color: var(--text-subtle);
            letter-spacing: 0.8px;
            pointer-events: none;
        }

        .copy-code-btn {
            position: absolute;
            top: 10px; right: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #f3f4f6;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
            z-index: 5;
        }

        .copy-code-btn:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }

        .copy-code-btn i { width: 13px; height: 13px; }

        .code-block-wrapper pre {
            background: transparent !important;
            padding: 40px 20px 20px 20px;
            overflow-x: auto;
            margin: 0;
        }

        .code-block-wrapper pre code {
            background: transparent;
            border: none;
            padding: 0;
            color: inherit;
            font-size: 0.88em;
            line-height: 1.6;
        }

        /* GitHub Alert Callouts */
        .callout {
            border-left: 4px solid var(--accent-color);
            background: rgba(99, 102, 241, 0.08);
            border-radius: 0 12px 12px 0;
            padding: 16px 20px;
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
            border-left-width: 4px;
        }

        .callout-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .callout-header i { width: 16px; height: 16px; }

        .callout-NOTE { border-left-color: #3b82f6; background: rgba(59, 130, 246, 0.08); }
        .callout-NOTE .callout-header { color: #60a5fa; }

        .callout-TIP { border-left-color: #10b981; background: rgba(16, 185, 129, 0.08); }
        .callout-TIP .callout-header { color: #34d399; }

        .callout-IMPORTANT { border-left-color: #8b5cf6; background: rgba(139, 92, 246, 0.08); }
        .callout-IMPORTANT .callout-header { color: #a78bfa; }

        .callout-WARNING { border-left-color: #f59e0b; background: rgba(245, 158, 11, 0.08); }
        .callout-WARNING .callout-header { color: #fbbf24; }

        .callout-CAUTION { border-left-color: #ef4444; background: rgba(239, 68, 68, 0.08); }
        .callout-CAUTION .callout-header { color: #f87171; }

        .callout-body p { margin-bottom: 8px; color: var(--text-main); }
        .callout-body p:last-child { margin-bottom: 0; }

        /* Task Checklists */
        .task-list { list-style: none; padding-left: 0; }
        .task-list-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            color: var(--text-muted);
        }
        .task-list-item input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--accent-color);
            cursor: default;
        }
        .task-checked span { text-decoration: line-through; opacity: 0.6; }

        /* Responsive Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 28px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .markdown-wrapper table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .markdown-wrapper th, .markdown-wrapper td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .markdown-wrapper th {
            background: rgba(17, 24, 39, 0.9);
            color: var(--text-main);
            font-weight: 700;
        }

        .markdown-wrapper td {
            background: rgba(30, 41, 59, 0.25);
            color: var(--text-muted);
        }

        .markdown-wrapper tr:hover td { background: rgba(99, 102, 241, 0.05); }

        .markdown-wrapper blockquote {
            border-left: 4px solid var(--accent-color);
            background: rgba(99, 102, 241, 0.06);
            padding: 16px 20px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 24px;
            color: var(--text-main);
        }

        .markdown-wrapper a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s;
        }

        .markdown-wrapper a:hover {
            color: #7dd3fc;
            text-decoration: underline;
        }

        .doc-internal-link {
            color: #a78bfa !important;
            background: rgba(167, 139, 250, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid rgba(167, 139, 250, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .doc-internal-link:hover {
            background: rgba(167, 139, 250, 0.2);
            text-decoration: none !important;
        }

        .inline-doc-icon, .inline-icon { width: 14px; height: 14px; }

        .markdown-wrapper hr {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 36px 0;
        }

        /* Right TOC Sidebar */
        .toc-sidebar {
            width: var(--toc-width);
            background: var(--bg-toc);
            border-left: 1px solid var(--border-color);
            padding: 24px 16px;
            overflow-y: auto;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .toc-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-muted);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toc-list { list-style: none; }
        .toc-item { margin-bottom: 4px; }

        .toc-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.81rem;
            display: block;
            padding: 5px 8px;
            border-radius: 6px;
            transition: all 0.15s ease;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            border-left: 2px solid transparent;
        }

        .toc-link:hover {
            color: var(--text-main);
            background: rgba(99, 102, 241, 0.08);
        }

        .toc-link.active {
            color: var(--accent-color);
            font-weight: 700;
            background: rgba(99, 102, 241, 0.12);
            border-left-color: var(--accent-color);
        }

        .toc-item.level-2 { padding-left: 0; font-weight: 600; }
        .toc-item.level-3 { padding-left: 12px; font-size: 0.78rem; }
        .toc-item.level-4 { padding-left: 24px; font-size: 0.74rem; }

        /* Command Palette Search Modal */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(8px);
            z-index: 2000;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 100px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            width: 100%;
            max-width: 650px;
            background: var(--bg-sidebar);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transform: translateY(-20px);
            transition: transform 0.2s ease;
        }

        .modal-overlay.active .modal-card {
            transform: translateY(0);
        }

        .modal-search-header {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-search-header input {
            width: 100%;
            background: transparent;
            border: none;
            color: var(--text-main);
            font-size: 1rem;
            outline: none;
        }

        .modal-results-list {
            max-height: 400px;
            overflow-y: auto;
            padding: 12px;
            list-style: none;
        }

        .modal-result-item {
            padding: 10px 14px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.15s;
        }

        .modal-result-item:hover, .modal-result-item.selected {
            background: var(--accent-gradient);
            color: #ffffff;
        }

        .modal-result-title { font-weight: 600; font-size: 0.88rem; }
        .modal-result-path { font-size: 0.72rem; opacity: 0.8; font-family: 'JetBrains Mono', monospace; }

        /* Toast Notifications */
        .toast-notification {
            position: fixed;
            bottom: 24px; right: 24px;
            background: var(--accent-gradient);
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 3000;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Focus Mode Overrides */
        body.focus-mode .sidebar, body.focus-mode .toc-sidebar {
            display: none !important;
        }
        body.focus-mode .markdown-wrapper {
            max-width: 1100px;
        }

        /* Scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.18); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.35); }

        /* Responsive Breakpoints */
        @media (max-width: 1100px) {
            .toc-sidebar { display: none; }
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0; top: 0; bottom: 0;
                transform: translateX(-100%);
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .mobile-menu-btn { display: block; }
            .content-area { padding: 20px 16px; }
            .markdown-wrapper { padding: 24px 20px; }
            .doc-stats-bar { display: none; }
        }
    </style>
</head>
<body>

    <!-- Reading Progress Bar -->
    <div class="progress-bar-container">
        <div class="progress-bar-fill" id="progressBar"></div>
    </div>

    <!-- Left Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="brand-logo">
                <i data-lucide="book-open-check"></i>
                <span>TechScout Docs</span>
                <span class="brand-badge">SaaS Portal</span>
            </a>

            <!-- Category Filter Tabs -->
            <div class="category-tabs-group">
                <button class="cat-tab-btn active" onclick="filterCategoryTab('all', this)">All</button>
                <button class="cat-tab-btn" onclick="filterCategoryTab('master', this)">Master</button>
                <button class="cat-tab-btn" onclick="filterCategoryTab('project', this)">Specs</button>
                <button class="cat-tab-btn" onclick="filterCategoryTab('qa', this)">QA Suite</button>
            </div>

            <!-- Quick Search Input -->
            <div class="search-box" onclick="openSearchModal()">
                <i data-lucide="search"></i>
                <input type="text" id="docSearchInput" placeholder="Filter 80+ documentation files..." readonly>
                <span class="search-shortcut-kbd">Ctrl K</span>
            </div>
        </div>

        <div class="sidebar-content">
            <!-- Master Reference Guides -->
            <div class="section-label cat-sec-master" onclick="toggleSection('rootDocsList')">
                <span>📘 Master References</span>
                <span class="badge-count"><?= count($rootDocs) ?></span>
            </div>
            <ul class="doc-list cat-sec-master" id="rootDocsList">
                <?php foreach ($rootDocs as $doc): ?>
                    <li class="doc-item">
                        <a href="index.php?file=<?= urlencode($doc['path']) ?>" 
                           class="doc-link <?= $selectedFile === $doc['path'] ? 'active' : '' ?>">
                            <div class="doc-link-left">
                                <i data-lucide="book"></i>
                                <span><?= htmlspecialchars($doc['name']) ?></span>
                            </div>
                            <span class="line-badge"><?= $doc['lines'] ?>L</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Project Specifications -->
            <div class="section-label cat-sec-project" onclick="toggleSection('projectDocsList')">
                <span>🛠️ Project Specifications</span>
                <span class="badge-count"><?= count($projectDocs) ?></span>
            </div>
            <ul class="doc-list cat-sec-project" id="projectDocsList">
                <?php foreach ($projectDocs as $doc): ?>
                    <li class="doc-item">
                        <a href="index.php?file=<?= urlencode($doc['path']) ?>" 
                           class="doc-link <?= $selectedFile === $doc['path'] ? 'active' : '' ?>">
                            <div class="doc-link-left">
                                <i data-lucide="file-code"></i>
                                <span><?= htmlspecialchars($doc['name']) ?></span>
                            </div>
                            <span class="line-badge"><?= $doc['lines'] ?>L</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Enterprise QA Testing Suite -->
            <div class="section-label cat-sec-qa" onclick="toggleSection('qaDocsList')">
                <span>🧪 Enterprise QA Testing Suite</span>
                <span class="badge-count"><?= count($qaDocs) ?></span>
            </div>
            <ul class="doc-list cat-sec-qa" id="qaDocsList">
                <?php foreach ($qaDocs as $doc): ?>
                    <li class="doc-item">
                        <a href="index.php?file=<?= urlencode($doc['path']) ?>" 
                           class="doc-link <?= $selectedFile === $doc['path'] ? 'active' : '' ?>">
                            <div class="doc-link-left">
                                <i data-lucide="shield-check"></i>
                                <span><?= htmlspecialchars($doc['name']) ?></span>
                            </div>
                            <span class="line-badge"><?= $doc['lines'] ?>L</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <!-- Main Container -->
    <main class="main-container">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="navbar-left">
                <button class="mobile-menu-btn" onclick="toggleMobileSidebar()">
                    <i data-lucide="menu"></i>
                </button>

                <div class="breadcrumb-trail">
                    <a href="index.php"><i data-lucide="home" style="width:14px; height:14px; inline-size: 14px;"></i> Docs</a>
                    <i data-lucide="chevron-right" style="width:14px; height:14px;"></i>
                    <span class="current"><?= htmlspecialchars($selectedFile) ?></span>
                </div>
            </div>

            <div class="doc-stats-bar">
                <div class="doc-stat-item">
                    <i data-lucide="file-text" style="width:14px; height:14px;"></i>
                    <span><?= number_format($activeFileLines) ?> Lines</span>
                </div>
                <div class="doc-stat-item">
                    <i data-lucide="align-left" style="width:14px; height:14px;"></i>
                    <span><?= number_format($activeFileWords) ?> Words</span>
                </div>
                <div class="doc-stat-item">
                    <i data-lucide="clock" style="width:14px; height:14px;"></i>
                    <span>~<?= $activeFileReadingTime ?> min read</span>
                </div>
                <div class="doc-stat-item">
                    <i data-lucide="hard-drive" style="width:14px; height:14px;"></i>
                    <span><?= $activeFileSizeKb ?> KB</span>
                </div>
            </div>

            <div class="top-actions">
                <!-- Font Scaling -->
                <div class="font-scale-group" title="Adjust Reader Text Size">
                    <button class="font-scale-btn" onclick="setFontScale('sm')">A-</button>
                    <button class="font-scale-btn active" onclick="setFontScale('md')">A</button>
                    <button class="font-scale-btn" onclick="setFontScale('lg')">A+</button>
                </div>

                <!-- Print / Export -->
                <button class="btn-action" onclick="window.print()" title="Print / Export Document">
                    <i data-lucide="printer"></i>
                </button>

                <!-- Focus Mode -->
                <button class="btn-action" onclick="toggleFocusMode()" id="focusBtn" title="Toggle Distraction-Free Focus Mode">
                    <i data-lucide="maximize-2"></i>
                </button>

                <!-- Copy Link -->
                <button class="btn-action" onclick="copyPageUrl()" title="Copy Page Link">
                    <i data-lucide="share-2"></i>
                </button>

                <!-- Theme Switcher -->
                <button class="btn-action" onclick="toggleTheme()" id="themeToggleBtn">
                    <i data-lucide="sun" id="themeIcon"></i>
                    <span id="themeLabel">Light Mode</span>
                </button>

                <a href="../" class="btn-action">
                    <i data-lucide="external-link"></i>
                    <span>App</span>
                </a>
            </div>
        </header>

        <!-- Workspace Area -->
        <div class="workspace-area">
            <!-- Article Reader (100% Server-Side Parsed) -->
            <div class="content-area" id="contentArea">
                <article class="markdown-wrapper" id="markdownContent">
                    <?= $renderedHtml ?>
                </article>
            </div>

            <!-- Scroll-Spy Dynamic Table of Contents -->
            <aside class="toc-sidebar">
                <div class="toc-title">
                    <i data-lucide="list" style="width:14px; height:14px;"></i>
                    <span>On This Page</span>
                </div>
                <ul class="toc-list" id="tocList">
                    <!-- Populated dynamically by JS -->
                </ul>
            </aside>
        </div>
    </main>

    <!-- Command Palette Search Modal (Ctrl+K) -->
    <div class="modal-overlay" id="searchModal" onclick="closeSearchModal(event)">
        <div class="modal-card">
            <div class="modal-search-header">
                <i data-lucide="search" style="color:var(--accent-color);"></i>
                <input type="text" id="modalSearchInput" placeholder="Type file name or topic to jump..." onkeyup="filterModalResults(event)">
            </div>
            <ul class="modal-results-list" id="modalResultsList">
                <?php foreach ($allDocs as $doc): ?>
                    <li>
                        <a href="index.php?file=<?= urlencode($doc['path']) ?>" class="modal-result-item">
                            <div>
                                <div class="modal-result-title"><?= htmlspecialchars($doc['title']) ?></div>
                                <div class="modal-result-path">[<?= htmlspecialchars($doc['category']) ?>] <?= htmlspecialchars($doc['path']) ?></div>
                            </div>
                            <span class="line-badge"><?= $doc['lines'] ?> lines</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-notification" id="toastNotification">
        <i data-lucide="check-circle-2"></i>
        <span id="toastMessage">Link copied to clipboard!</span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            buildToc();
            setupScrollSpy();
            setupProgressBar();

            // Highlight Code Blocks
            if (window.hljs) {
                document.querySelectorAll('pre code').forEach(block => {
                    hljs.highlightElement(block);
                });
            }

            // Initialize Lucide Icons
            if (window.lucide) {
                lucide.createIcons();
            }

            // Load saved settings
            const savedTheme = localStorage.getItem('techscout_doc_theme') || 'dark';
            applyTheme(savedTheme);

            const savedFont = localStorage.getItem('techscout_doc_font') || 'md';
            setFontScale(savedFont);

            // Global Keyboard Shortcuts (Ctrl+K / Cmd+K)
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    openSearchModal();
                }
                if (e.key === 'Escape') {
                    closeSearchModal();
                }
            });
        });

        // Dynamic TOC Builder
        function buildToc() {
            const headings = document.querySelectorAll('.markdown-wrapper h2, .markdown-wrapper h3, .markdown-wrapper h4');
            const tocList = document.getElementById('tocList');
            tocList.innerHTML = '';

            if (headings.length === 0) {
                tocList.innerHTML = '<li class="toc-item"><span class="toc-link" style="opacity:0.5;">No headings found</span></li>';
                return;
            }

            headings.forEach((h, index) => {
                const level = parseInt(h.tagName.substring(1));
                const text = h.innerText.replace(/ link$/i, '').trim();
                const id = h.id || `heading-${index}`;
                h.id = id;

                const li = document.createElement('li');
                li.className = `toc-item level-${level}`;
                li.innerHTML = `<a href="#${id}" class="toc-link" id="toc-link-${id}">${text}</a>`;
                tocList.appendChild(li);
            });
        }

        // Scroll Spy for TOC active states
        function setupScrollSpy() {
            const contentArea = document.getElementById('contentArea');
            const headings = document.querySelectorAll('.markdown-wrapper h2, .markdown-wrapper h3, .markdown-wrapper h4');
            const tocLinks = document.querySelectorAll('.toc-link');

            if (!contentArea || headings.length === 0) return;

            contentArea.addEventListener('scroll', () => {
                let currentId = '';
                const scrollPos = contentArea.scrollTop + 100;

                headings.forEach(h => {
                    if (h.offsetTop <= scrollPos) {
                        currentId = h.id;
                    }
                });

                tocLinks.forEach(link => {
                    link.classList.remove('active');
                    if (currentId && link.id === `toc-link-${currentId}`) {
                        link.classList.add('active');
                    }
                });
            });
        }

        // Reading Progress Bar
        function setupProgressBar() {
            const contentArea = document.getElementById('contentArea');
            const progressBar = document.getElementById('progressBar');

            if (!contentArea || !progressBar) return;

            contentArea.addEventListener('scroll', () => {
                const totalHeight = contentArea.scrollHeight - contentArea.clientHeight;
                const progress = totalHeight > 0 ? (contentArea.scrollTop / totalHeight) * 100 : 0;
                progressBar.style.width = `${progress}%`;
            });
        }

        // Category Tab Filtering in Sidebar
        function filterCategoryTab(cat, btn) {
            document.querySelectorAll('.cat-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const masterElements = document.querySelectorAll('.cat-sec-master');
            const projectElements = document.querySelectorAll('.cat-sec-project');
            const qaElements = document.querySelectorAll('.cat-sec-qa');

            if (cat === 'all') {
                masterElements.forEach(e => e.style.display = '');
                projectElements.forEach(e => e.style.display = '');
                qaElements.forEach(e => e.style.display = '');
            } else if (cat === 'master') {
                masterElements.forEach(e => e.style.display = '');
                projectElements.forEach(e => e.style.display = 'none');
                qaElements.forEach(e => e.style.display = 'none');
            } else if (cat === 'project') {
                masterElements.forEach(e => e.style.display = 'none');
                projectElements.forEach(e => e.style.display = '');
                qaElements.forEach(e => e.style.display = 'none');
            } else if (cat === 'qa') {
                masterElements.forEach(e => e.style.display = 'none');
                projectElements.forEach(e => e.style.display = 'none');
                qaElements.forEach(e => e.style.display = '');
            }
        }

        // Copy Code Block
        function copyCodeBlock(btn) {
            const pre = btn.parentElement.querySelector('code');
            const text = pre.innerText;
            navigator.clipboard.writeText(text);
            
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="check"></i> <span>Copied!</span>';
            if (window.lucide) lucide.createIcons();

            showToast('Code copied to clipboard!');
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                if (window.lucide) lucide.createIcons();
            }, 2000);
        }

        // Copy Heading Anchor Link
        function copyHeadingAnchor(id, event) {
            event.preventDefault();
            const url = `${window.location.origin}${window.location.pathname}?file=<?= urlencode($selectedFile) ?>#${id}`;
            navigator.clipboard.writeText(url);
            showToast('Heading link copied!');
        }

        // Copy Page URL
        function copyPageUrl() {
            navigator.clipboard.writeText(window.location.href);
            showToast('Page URL copied to clipboard!');
        }

        // Toast Notification Display
        function showToast(msg) {
            const toast = document.getElementById('toastNotification');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.innerText = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        // Theme Switcher & Persistence
        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem('techscout_doc_theme', next);
        }

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            const label = document.getElementById('themeLabel');
            const icon = document.getElementById('themeIcon');
            const hljsTheme = document.getElementById('hljs-theme');

            if (theme === 'light') {
                if (label) label.innerText = 'Dark Mode';
                if (icon) icon.setAttribute('data-lucide', 'moon');
                if (hljsTheme) hljsTheme.href = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css';
            } else {
                if (label) label.innerText = 'Light Mode';
                if (icon) icon.setAttribute('data-lucide', 'sun');
                if (hljsTheme) hljsTheme.href = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/tokyo-night-dark.min.css';
            }
            if (window.lucide) lucide.createIcons();
        }

        // Font Scale Controls
        function setFontScale(scale) {
            document.documentElement.setAttribute('data-font-scale', scale);
            localStorage.setItem('techscout_doc_font', scale);

            document.querySelectorAll('.font-scale-btn').forEach(btn => btn.classList.remove('active'));
            const btnMap = { 'sm': 0, 'md': 1, 'lg': 2 };
            const btns = document.querySelectorAll('.font-scale-btn');
            if (btns[btnMap[scale]]) btns[btnMap[scale]].classList.add('active');
        }

        // Focus Mode Toggle
        function toggleFocusMode() {
            document.body.classList.toggle('focus-mode');
            const isFocus = document.body.classList.contains('focus-mode');
            showToast(isFocus ? 'Entered Focus Mode (Distraction Free)' : 'Exited Focus Mode');
        }

        // Sidebar Section Accordion Toggle
        function toggleSection(listId) {
            const list = document.getElementById(listId);
            if (list) {
                list.style.display = (list.style.display === 'none') ? 'block' : 'none';
            }
        }

        // Mobile Sidebar Toggle
        function toggleMobileSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
        }

        // Command Palette Modal Functions
        function openSearchModal() {
            const modal = document.getElementById('searchModal');
            const input = document.getElementById('modalSearchInput');
            modal.classList.add('active');
            setTimeout(() => input.focus(), 100);
        }

        function closeSearchModal(e) {
            if (!e || e.target.id === 'searchModal' || e.key === 'Escape') {
                document.getElementById('searchModal').classList.remove('active');
            }
        }

        function filterModalResults(e) {
            const query = e.target.value.toLowerCase();
            const items = document.querySelectorAll('#modalResultsList li');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>
