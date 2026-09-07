# 34. Content Entity Model (`Content.php`)

## File Location
[`app/Models/Content.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Content.php)

## Key Functions
- `getAll($type, $filters, $limit, $offset)`: Multi-entity content fetcher for articles, tools, prompts, GPTs, campaigns, and jobs.
- `getBySlug($slug)`: Resolves single content record with author profile joins.
- `incrementViews($contentId)`: Increments article pageview counter.
- `toggleUpvote($contentId, $userId)`: Handles user upvote toggling.
