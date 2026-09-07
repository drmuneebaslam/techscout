# 33. Threaded Comment Engine (`Comment.php`)

## File Location
[`app/Models/Comment.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Comment.php)

## Key Functions
- `getByContentId($contentId)`: Retrieves hierarchical threaded comments with parent-child nested depth.
- `create($data)`: Stores new member comments with profanity screening.
- `vote($commentId, $userId, $type)`: Toggles comment upvotes/downvotes.
- `updateStatus($commentId, $status)`: Admin moderation status handler (`approved`, `spam`, `hidden`).
