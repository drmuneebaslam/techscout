# 42. Security Anomaly & Threat Engine (`SecurityAnomalyEngine.php`)

## File Location
[`app/Models/SecurityAnomalyEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SecurityAnomalyEngine.php)

## Key Functions
- `analyzeRequest($ip, $userId, $route)`: Scans request rate spikes, credential stuffing attempts, and SQL injection patterns.
- `triggerAutomaticBan($ip, $reason)`: Imposes emergency IP ban when threat thresholds are exceeded.
