# 43. Active Session & Device Registry (`SessionManager.php`)

## File Location
[`app/Models/SessionManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SessionManager.php)

## Key Functions
- `registerSession($userId, $token, $ip, $userAgent)`: Stores active user session details in `user_sessions`.
- `getUserSessions($userId)`: Lists active devices and browser sessions for user settings pages.
- `revokeSession($sessionId)`: Revokes specific session token.
- `revokeAllOtherSessions($userId, $currentSessionToken)`: Emergency remote sign-out across all devices.
