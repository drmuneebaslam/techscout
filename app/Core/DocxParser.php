<?php
declare(strict_types=1);

namespace App\Core;

use ZipArchive;
use DOMDocument;
use DOMElement;
use Throwable;

/**
 * Pure PHP Microsoft Word (.docx) Parser
 * Extracts Title, Summary, Headings, Paragraphs, Lists, Code Blocks, Tables, Links, and Media Images into clean HTML.
 */
class DocxParser {

    /**
     * Parse a .docx file and return structured document data
     *
     * @param string $filePath Absolute path to the .docx file
     * @param string|null $uploadDir Directory to save extracted images
     * @param string|null $publicUploadUrl Web accessible URL for saved images
     * @return array{success: bool, title: string, summary: string, body_html: string, word_count: int, error?: string}
     */
    public static function parse(string $filePath, ?string $uploadDir = null, ?string $publicUploadUrl = null): array {
        if (!file_exists($filePath)) {
            return ['success' => false, 'title' => '', 'summary' => '', 'body_html' => '', 'word_count' => 0, 'error' => 'File does not exist.'];
        }

        $relsXml = null;
        $docXml = null;
        $extractedImages = []; // relativePath => binaryData

        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($filePath) === true) {
                $relsXml = $zip->getFromName('word/_rels/document.xml.rels') ?: null;
                $docXml = $zip->getFromName('word/document.xml') ?: null;

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (str_starts_with($filename, 'word/media/')) {
                        $extractedImages[$filename] = $zip->getFromIndex($i);
                    }
                }
                $zip->close();
            }
        }

        // Fallback: If ZipArchive not present or failed, extract via temporary extraction or stream
        if (!$docXml) {
            $tempExtractDir = sys_get_temp_dir() . '/ts_docx_' . bin2hex(random_bytes(6));
            @mkdir($tempExtractDir, 0755, true);

            // Try PowerShell Expand-Archive on Windows or unzip on Linux
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                @exec("powershell.exe -NoProfile -NonInteractive -Command \"Expand-Archive -LiteralPath '" . addslashes($filePath) . "' -DestinationPath '" . addslashes($tempExtractDir) . "' -Force\"");
            } else {
                @exec("unzip -q -o " . escapeshellarg($filePath) . " -d " . escapeshellarg($tempExtractDir));
            }

            $docPath = $tempExtractDir . '/word/document.xml';
            $relsPath = $tempExtractDir . '/word/_rels/document.xml.rels';
            $mediaPath = $tempExtractDir . '/word/media';

            if (file_exists($docPath)) {
                $docXml = @file_get_contents($docPath);
            }
            if (file_exists($relsPath)) {
                $relsXml = @file_get_contents($relsPath);
            }
            if (is_dir($mediaPath)) {
                $mediaFiles = @scandir($mediaPath) ?: [];
                foreach ($mediaFiles as $mf) {
                    if ($mf !== '.' && $mf !== '..') {
                        $mFullPath = $mediaPath . '/' . $mf;
                        if (is_file($mFullPath)) {
                            $extractedImages['word/media/' . $mf] = @file_get_contents($mFullPath);
                        }
                    }
                }
            }

            // Cleanup temp directory
            if (is_dir($tempExtractDir)) {
                $it = new \RecursiveDirectoryIterator($tempExtractDir, \RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($files as $file) {
                    if ($file->isDir()) {
                        @rmdir($file->getRealPath());
                    } else {
                        @unlink($file->getRealPath());
                    }
                }
                @rmdir($tempExtractDir);
            }
        }

        if (!$docXml) {
            return ['success' => false, 'title' => '', 'summary' => '', 'body_html' => '', 'word_count' => 0, 'error' => 'Unable to read Microsoft Word document XML.'];
        }

        // Set default upload directory
        if ($uploadDir === null) {
            $uploadDir = dirname(__DIR__, 2) . '/assets/uploads/articles';
        }
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        if ($publicUploadUrl === null) {
            $publicUploadUrl = Config::url('/assets/uploads/articles');
        }

        // 1. Parse Relationships (hyperlinks & image media targets)
        $rels = [];
        if ($relsXml) {
            try {
                $relsDom = new DOMDocument();
                @$relsDom->loadXML($relsXml);
                foreach ($relsDom->getElementsByTagName('Relationship') as $rel) {
                    $id = $rel->getAttribute('Id');
                    $target = $rel->getAttribute('Target');
                    $type = $rel->getAttribute('Type');
                    $rels[$id] = [
                        'target' => $target,
                        'type' => $type,
                        'is_external' => $rel->getAttribute('TargetMode') === 'External'
                    ];
                }
            } catch (Throwable $e) {
                // Ignore rels parse errors
            }
        }

        // 2. Save Embedded Media Images
        $imageMap = []; // relId => publicUrl
        foreach ($extractedImages as $filename => $fileData) {
            if (!empty($fileData)) {
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $cleanName = 'docx_' . bin2hex(random_bytes(8)) . '.' . ($ext ?: 'png');
                $destination = $uploadDir . '/' . $cleanName;
                @file_put_contents($destination, $fileData);

                $relativeMediaTarget = 'media/' . basename($filename);
                foreach ($rels as $relId => $relData) {
                    if ($relData['target'] === $relativeMediaTarget || basename($relData['target']) === basename($filename)) {
                        $imageMap[$relId] = rtrim($publicUploadUrl, '/') . '/' . $cleanName;
                    }
                }
            }
        }

        $dom = new DOMDocument();
        @$dom->loadXML($docXml, LIBXML_NOERROR | LIBXML_NOWARNING);

        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            return ['success' => false, 'title' => '', 'summary' => '', 'body_html' => '', 'word_count' => 0, 'error' => 'No body element found in document.'];
        }

        $htmlBlocks = [];
        $extractedTitle = '';
        $extractedSummary = '';
        $currentListType = null; // 'ul' or 'ol'
        $listItems = [];

        $flushList = function() use (&$htmlBlocks, &$currentListType, &$listItems) {
            if (!empty($listItems) && $currentListType) {
                $htmlBlocks[] = "<{$currentListType}>\n  " . implode("\n  ", $listItems) . "\n</{$currentListType}>";
                $listItems = [];
                $currentListType = null;
            }
        };

        foreach ($body->childNodes as $node) {
            if (!($node instanceof DOMElement)) {
                continue;
            }

            $nodeName = $node->localName ?? $node->nodeName;

            // Handle Paragraphs
            if ($nodeName === 'p') {
                $style = self::getParagraphStyle($node);
                $isListItem = self::isListItem($node);

                if ($isListItem) {
                    $listType = 'ul';
                    if ($currentListType !== null && $currentListType !== $listType) {
                        $flushList();
                    }
                    $currentListType = $listType;
                    $itemHtml = self::parseParagraphInline($node, $rels, $imageMap);
                    if (trim(strip_tags($itemHtml)) !== '') {
                        $listItems[] = '<li>' . $itemHtml . '</li>';
                    }
                    continue;
                } else {
                    $flushList();
                }

                $rawText = self::getNodeText($node);
                $inlineHtml = self::parseParagraphInline($node, $rels, $imageMap);

                if (trim($rawText) === '' && !str_contains($inlineHtml, '<img') && !str_contains($inlineHtml, '<figure')) {
                    continue; // Skip empty paragraphs
                }

                // Detect Heading Styles
                if (preg_match('/heading\s*1|title/i', $style)) {
                    if (empty($extractedTitle)) {
                        $extractedTitle = trim($rawText);
                    }
                    $htmlBlocks[] = '<h2>' . $inlineHtml . '</h2>';
                } elseif (preg_match('/heading\s*2/i', $style)) {
                    $htmlBlocks[] = '<h2>' . $inlineHtml . '</h2>';
                } elseif (preg_match('/heading\s*3/i', $style)) {
                    $htmlBlocks[] = '<h3>' . $inlineHtml . '</h3>';
                } elseif (preg_match('/heading\s*4|heading\s*5/i', $style)) {
                    $htmlBlocks[] = '<h4>' . $inlineHtml . '</h4>';
                } elseif (preg_match('/code|source|pre/i', $style)) {
                    $htmlBlocks[] = '<pre><code>' . htmlspecialchars($rawText) . '</code></pre>';
                } elseif (preg_match('/quote|blockquote/i', $style)) {
                    $htmlBlocks[] = '<blockquote><p>' . $inlineHtml . '</p></blockquote>';
                } else {
                    if (empty($extractedSummary) && strlen(trim($rawText)) > 25 && $rawText !== $extractedTitle) {
                        $extractedSummary = mb_substr(trim($rawText), 0, 300);
                    }
                    $htmlBlocks[] = '<p>' . $inlineHtml . '</p>';
                }
            }
            // Handle Tables
            elseif ($nodeName === 'tbl') {
                $flushList();
                $tableHtml = self::parseTable($node, $rels, $imageMap);
                if (!empty($tableHtml)) {
                    $htmlBlocks[] = $tableHtml;
                }
            }
        }

        $flushList();

        $finalHtml = implode("\n\n", $htmlBlocks);
        $cleanText = strip_tags($finalHtml);
        $wordCount = str_word_count($cleanText);

        if (empty($extractedTitle)) {
            $extractedTitle = pathinfo($filePath, PATHINFO_FILENAME);
            $extractedTitle = ucwords(str_replace(['_', '-'], ' ', $extractedTitle));
        }

        if (empty($extractedSummary) && !empty($cleanText)) {
            $extractedSummary = mb_substr(trim(preg_replace('/\s+/', ' ', $cleanText)), 0, 250) . '...';
        }

        return [
            'success' => true,
            'title' => $extractedTitle,
            'summary' => $extractedSummary,
            'body_html' => $finalHtml,
            'word_count' => $wordCount
        ];
    }

    /**
     * Parse inline runs, bold, italic, links, and drawings inside a paragraph
     */
    private static function parseParagraphInline(DOMElement $pNode, array $rels, array $imageMap): string {
        $html = '';

        foreach ($pNode->childNodes as $child) {
            if (!($child instanceof DOMElement)) {
                continue;
            }

            $childName = $child->localName ?? $child->nodeName;

            // Hyperlink
            if ($childName === 'hyperlink') {
                $rId = $child->getAttribute('r:id') ?: $child->getAttribute('id');
                $targetUrl = '#';
                $isExternal = false;
                if ($rId && isset($rels[$rId])) {
                    $targetUrl = $rels[$rId]['target'];
                    $isExternal = !empty($rels[$rId]['is_external']) || str_starts_with($targetUrl, 'http');
                }

                $linkText = '';
                foreach ($child->getElementsByTagName('t') as $tNode) {
                    $linkText .= $tNode->nodeValue;
                }

                if (trim($linkText) !== '') {
                    $extAttrs = $isExternal ? ' target="_blank" rel="noopener noreferrer"' : '';
                    $html .= '<a href="' . htmlspecialchars($targetUrl) . '"' . $extAttrs . '>' . htmlspecialchars($linkText) . '</a>';
                }
                continue;
            }

            // Run element <w:r>
            if ($childName === 'r') {
                $rText = '';
                $isBold = false;
                $isItalic = false;
                $isUnderline = false;
                $isCode = false;

                // Inspect Run Properties <w:rPr>
                $rPr = $child->getElementsByTagName('rPr')->item(0);
                if ($rPr) {
                    if ($rPr->getElementsByTagName('b')->length > 0) $isBold = true;
                    if ($rPr->getElementsByTagName('i')->length > 0) $isItalic = true;
                    if ($rPr->getElementsByTagName('u')->length > 0) $isUnderline = true;
                    $rFonts = $rPr->getElementsByTagName('rFonts')->item(0);
                    if ($rFonts) {
                        $ascii = $rFonts->getAttribute('w:ascii') ?: $rFonts->getAttribute('ascii');
                        if ($ascii && preg_match('/consolas|courier|mono|fira|jetbrains/i', $ascii)) {
                            $isCode = true;
                        }
                    }
                }

                // Check text nodes
                foreach ($child->childNodes as $sub) {
                    if (!($sub instanceof DOMElement)) continue;
                    $subName = $sub->localName ?? $sub->nodeName;
                    if ($subName === 't') {
                        $rText .= $sub->nodeValue;
                    } elseif ($subName === 'br') {
                        $rText .= "\n";
                    } elseif ($subName === 'tab') {
                        $rText .= "    ";
                    } elseif ($subName === 'drawing') {
                        // Embedded image
                        $imgHtml = self::parseDrawing($sub, $rels, $imageMap);
                        if (!empty($imgHtml)) {
                            $html .= $imgHtml;
                        }
                    }
                }

                if ($rText !== '') {
                    $piece = htmlspecialchars($rText);
                    if ($isCode) $piece = '<code>' . $piece . '</code>';
                    if ($isBold) $piece = '<strong>' . $piece . '</strong>';
                    if ($isItalic) $piece = '<em>' . $piece . '</em>';
                    if ($isUnderline) $piece = '<u>' . $piece . '</u>';
                    $html .= $piece;
                }
            }

            // Direct drawing element in paragraph
            if ($childName === 'drawing') {
                $imgHtml = self::parseDrawing($child, $rels, $imageMap);
                if (!empty($imgHtml)) {
                    $html .= $imgHtml;
                }
            }
        }

        return $html;
    }

    /**
     * Parse drawing/image node
     */
    private static function parseDrawing(DOMElement $drawingNode, array $rels, array $imageMap): string {
        $blips = $drawingNode->getElementsByTagName('blip');
        if ($blips->length === 0) {
            return '';
        }

        $blip = $blips->item(0);
        $embedId = $blip->getAttribute('r:embed') ?: $blip->getAttribute('embed');
        if ($embedId && isset($imageMap[$embedId])) {
            $src = htmlspecialchars($imageMap[$embedId]);
            return '<figure style="margin: 1.5rem 0; text-align: center;"><img src="' . $src . '" alt="Document Illustration" style="max-width: 100%; border-radius: 8px; border: 1px solid rgba(148,163,184,0.2);"><figcaption style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.4rem; font-style: italic;">Extracted Figure</figcaption></figure>';
        }

        return '';
    }

    /**
     * Parse Word Table <w:tbl>
     */
    private static function parseTable(DOMElement $tblNode, array $rels, array $imageMap): string {
        $rows = $tblNode->getElementsByTagName('tr');
        if ($rows->length === 0) {
            return '';
        }

        $html = "<table class=\"word-table\" style=\"width: 100%; border-collapse: collapse; margin: 1.5rem 0;\">\n";
        $isFirstRow = true;

        foreach ($rows as $row) {
            $html .= "  <tr>\n";
            $cells = $row->getElementsByTagName('tc');
            foreach ($cells as $cell) {
                $cellText = '';
                foreach ($cell->getElementsByTagName('p') as $p) {
                    $inline = self::parseParagraphInline($p, $rels, $imageMap);
                    if ($inline !== '') {
                        $cellText .= ($cellText !== '' ? '<br>' : '') . $inline;
                    }
                }
                $tag = $isFirstRow ? 'th' : 'td';
                $html .= "    <{$tag} style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">" . ($cellText !== '' ? $cellText : '&nbsp;') . "</{$tag}>\n";
            }
            $html .= "  </tr>\n";
            $isFirstRow = false;
        }

        $html .= "</table>";
        return $html;
    }

    private static function getParagraphStyle(DOMElement $pNode): string {
        $pPr = $pNode->getElementsByTagName('pPr')->item(0);
        if ($pPr) {
            $pStyle = $pPr->getElementsByTagName('pStyle')->item(0);
            if ($pStyle) {
                return $pStyle->getAttribute('w:val') ?: $pStyle->getAttribute('val') ?: '';
            }
        }
        return '';
    }

    private static function isListItem(DOMElement $pNode): bool {
        $pPr = $pNode->getElementsByTagName('pPr')->item(0);
        if ($pPr) {
            return $pPr->getElementsByTagName('numPr')->length > 0;
        }
        return false;
    }

    private static function getNodeText(DOMElement $node): string {
        $text = '';
        foreach ($node->getElementsByTagName('t') as $t) {
            $text .= $t->nodeValue;
        }
        return $text;
    }
}
