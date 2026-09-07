# 29. Security Audit Log Model (`AuditLog.php`)

## File Location
[`app/Models/AuditLog.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuditLog.php)

## Key Functions
- `log($action, $entityType, $entityId, $details)`: Records structured audit events (`user.strike_issued`, `payout.processed`, `article.approved`) with IP address and JSON context into `audit_logs`.
- `getClientIp()`: Utility retrieving true client IP address.
- `getRecentLogs($limit)`: Retrieves log feed for admin security audits.
