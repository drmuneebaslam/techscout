# 06. Authentication & RBAC Access Guard (`Auth.php`)

## File Location
[`app/Core/Auth.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Auth.php)

## Key Features
- **Session Verification**: Verifies user session tokens against the database `user_sessions` table.
- **Role Hierarchy**: Validates roles (`super_admin`, `admin`, `editor`, `author`, `member`, `guest`).
- **Staff Auto-Author Creation**: Automatically provisions `authors` profile records for staff members missing author records.
- **Admin Impersonation**: Allows super-admins to impersonate member accounts for testing and debugging, with seamless reversal (`stopImpersonating()`).
