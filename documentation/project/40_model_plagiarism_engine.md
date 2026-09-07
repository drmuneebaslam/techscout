# 40. Plagiarism Detection Engine (`PlagiarismEngine.php`)

## File Location
[`app/Models/PlagiarismEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/PlagiarismEngine.php)

## Key Functions
- `scanArticleContent($text, $excludeArticleId)`: Uses n-gram token hashing to compare article text against published articles, calculating similarity percentage scores.
- `flagPlagiarizedContent($articleId, $similarityScore)`: Flags high-similarity articles for editorial review.
