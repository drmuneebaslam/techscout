# 15. RESTful API Controller (`ApiController.php`)

## File Location
[`app/Controllers/ApiController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/ApiController.php)

## Handled JSON Endpoints
- `GET /api/v1/content`: Returns paginated, filtered JSON data for articles, tools, prompts, and GPTs.
- `GET /api/v1/content/{id}`: Returns single content entity details.
- `POST /api/v1/comments`: Saves nested user comment replies.
- `POST /api/v1/reactions`: Toggles upvote reactions on articles, tools, and comments.
- `GET /api/v1/search`: Powers live search autocomplete.
- `POST /api/v1/follow`: Toggles member follow status on author profiles.
- `GET /api/v1/notifications`: Retrieves in-app notification list and unread badge count.
- `POST /api/v1/notifications/read`: Marks notification read.
