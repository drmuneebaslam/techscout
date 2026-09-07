# 30. Author Creator Profile Model (`Author.php`)

## File Location
[`app/Models/Author.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Author.php)

## Key Functions
- `getByUserId($userId)`: Retrieves author profile joined with user record.
- `getBySlug($slug)`: Resolves author by URL slug.
- `addReputation($authorId, $points)`: Increments author reputation score and triggers tier progression evaluation.
- `updatePromoCredits($authorId, $amount, $reason)`: Manages promotional credit balances for ad campaigns.
