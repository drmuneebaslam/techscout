# 08. Firewall, Rate Limiting & Threat Defense (`Firewall.php`)

## File Location
[`app/Core/Firewall.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Firewall.php)

## Key Features
- **IP Rate Limiter**: Implements time-decay rate-limiting windows tracking request counts per IP address.
- **Blacklist Enforcement**: Checks IP address against the `banned_ips` database table.
- **Dynamic Ban Action**: Automatically blocks malicious IPs attempting brute-force logins or spam submissions.
