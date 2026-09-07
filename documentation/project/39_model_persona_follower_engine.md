# 39. Persona & Author Follower Graph (`PersonaFollowerEngine.php`)

## File Location
[`app/Models/PersonaFollowerEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/PersonaFollowerEngine.php)

## Key Functions
- `toggleFollow($followerId, $authorId)`: Toggles follow relationship between member and author.
- `isFollowing($followerId, $authorId)`: Checks follow status.
- `getFollowerCount($authorId)`: Returns author follower count.
