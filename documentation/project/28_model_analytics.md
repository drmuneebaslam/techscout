# 28. Analytics Model (`Analytics.php`)

## File Location
[`app/Models/Analytics.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Analytics.php)

## Key Functions
- `recordPageView($url, $userId, $ip, $referrer)`: Logs traffic visits to `page_views` database table.
- `getAuthorDashboardStats($authorId)`: Computes total pageviews, unique readers, upvotes, and daily traffic graphs for creator dashboards.
- `getSiteOverviewStats()`: Aggregates total platform traffic metrics for administrative dashboards.
