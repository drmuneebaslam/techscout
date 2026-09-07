# 32. Multi-Author Co-Authorship Engine (`CoAuthorEngine.php`)

## File Location
[`app/Models/CoAuthorEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CoAuthorEngine.php)

## Key Functions
- `addCoAuthor($articleId, $authorId, $splitPercent)`: Associates co-authors with articles and defines revenue percentage splits.
- `getCoAuthors($articleId)`: Retrieves co-author lists for article layout displays.
- `removeCoAuthor($articleId, $authorId)`: Revokes co-authorship association.
