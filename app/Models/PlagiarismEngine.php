<?php
namespace App\Models;

use App\Core\Database;

class PlagiarismEngine {
    const SHINGLE_SIZE = 4; // 4-word shingles

    /**
     * Tokenize text into normalized shingles / n-grams
     */
    public static function createShingles(string $text, int $n = self::SHINGLE_SIZE): array {
        // Strip HTML tags and normalize whitespace
        $plain = strip_tags($text);
        $clean = strtolower(preg_replace('/[^\w\s]/u', '', $plain));
        $words = preg_split('/\s+/', trim($clean), -1, PREG_SPLIT_NO_EMPTY);

        $shingles = [];
        $count = count($words);
        if ($count < $n) {
            return !empty($words) ? [implode(' ', $words)] : [];
        }

        for ($i = 0; $i <= $count - $n; $i++) {
            $shingle = implode(' ', array_slice($words, $i, $n));
            $shingles[hash('crc32b', $shingle)] = $shingle;
        }

        return $shingles;
    }

    /**
     * Scan an article for plagiarism against all published contents in repository
     */
    public static function scanContent(int $contentId, ?int $scannedByUserId = null): array {
        $target = Database::fetch("SELECT id, title, slug, body_html, summary FROM contents WHERE id = ?", [$contentId]);
        if (!$target) {
            throw new \RuntimeException("Target content not found.");
        }

        $targetText = ($target['title'] ?? '') . "\n" . ($target['summary'] ?? '') . "\n" . ($target['body_html'] ?? '');
        $targetShingles = self::createShingles($targetText);
        $totalTargetShingles = count($targetShingles);

        if ($totalTargetShingles === 0) {
            return [
                'originality_score' => 100.0,
                'duplicate_ratio' => 0.0,
                'verdict' => 'clean',
                'matching_sources' => [],
                'total_shingles' => 0
            ];
        }

        // Fetch other published contents
        $otherContents = Database::query(
            "SELECT id, title, slug, body_html, summary, author_id 
             FROM contents 
             WHERE id != ? AND status = 'published' AND body_html IS NOT NULL AND CHAR_LENGTH(body_html) > 50",
            [$contentId]
        );

        $matchingSources = [];
        $allMatchingShingles = [];

        foreach ($otherContents as $other) {
            $otherText = ($other['title'] ?? '') . "\n" . ($other['summary'] ?? '') . "\n" . ($other['body_html'] ?? '');
            $otherShingles = self::createShingles($otherText);
            
            if (empty($otherShingles)) continue;

            $intersection = array_intersect_key($targetShingles, $otherShingles);
            $intersectCount = count($intersection);

            if ($intersectCount > 0) {
                // Jaccard similarity
                $unionCount = count($targetShingles) + count($otherShingles) - $intersectCount;
                $jaccardPercent = $unionCount > 0 ? round(($intersectCount / $unionCount) * 100, 2) : 0;
                $overlapOnTargetPercent = round(($intersectCount / $totalTargetShingles) * 100, 2);

                if ($overlapOnTargetPercent >= 5.0) {
                    $matchingSources[] = [
                        'content_id' => $other['id'],
                        'title' => $other['title'],
                        'slug' => $other['slug'],
                        'overlap_percent' => $overlapOnTargetPercent,
                        'jaccard_similarity' => $jaccardPercent,
                        'matching_shingle_count' => $intersectCount,
                        'sample_snippets' => array_slice(array_values($intersection), 0, 3)
                    ];

                    foreach ($intersection as $k => $v) {
                        $allMatchingShingles[$k] = $v;
                    }
                }
            }
        }

        // Sort matching sources by overlap
        usort($matchingSources, fn($a, $b) => $b['overlap_percent'] <=> $a['overlap_percent']);

        $totalMatchingCount = count($allMatchingShingles);
        $duplicateRatio = round(($totalMatchingCount / max(1, $totalTargetShingles)) * 100, 2);
        $originalityScore = max(0, min(100, 100.00 - $duplicateRatio));

        // Determine Verdict
        if ($duplicateRatio < 15.0) {
            $verdict = 'clean';
        } elseif ($duplicateRatio < 30.0) {
            $verdict = 'acceptable';
        } elseif ($duplicateRatio < 55.0) {
            $verdict = 'suspicious';
        } else {
            $verdict = 'flagged_plagiarism';
        }

        // Save scan in database
        Database::execute(
            "INSERT INTO plagiarism_scans (content_id, originality_score, duplicate_ratio, verdict, matched_content_ids, overlapping_shingles, total_shingles, matching_shingles, scanned_by, scanned_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                $contentId,
                $originalityScore,
                $duplicateRatio,
                $verdict,
                json_encode(array_column($matchingSources, 'content_id')),
                json_encode(array_slice(array_values($allMatchingShingles), 0, 10)),
                $totalTargetShingles,
                $totalMatchingCount,
                $scannedByUserId
            ]
        );

        $scanId = (int)Database::lastInsertId();

        return [
            'scan_id' => $scanId,
            'content_id' => $contentId,
            'originality_score' => $originalityScore,
            'duplicate_ratio' => $duplicateRatio,
            'verdict' => $verdict,
            'matching_sources' => $matchingSources,
            'total_shingles' => $totalTargetShingles,
            'matching_shingles' => $totalMatchingCount,
            'overlapping_snippets' => array_slice(array_values($allMatchingShingles), 0, 10)
        ];
    }

    /**
     * Get Latest Scan for an Article
     */
    public static function getLatestScan(int $contentId): ?array {
        $scan = Database::fetch("SELECT * FROM plagiarism_scans WHERE content_id = ? ORDER BY id DESC LIMIT 1", [$contentId]);
        if ($scan) {
            $scan['matched_content_ids'] = json_decode($scan['matched_content_ids'] ?? '[]', true);
            $scan['overlapping_shingles'] = json_decode($scan['overlapping_shingles'] ?? '[]', true);
        }
        return $scan;
    }
}
