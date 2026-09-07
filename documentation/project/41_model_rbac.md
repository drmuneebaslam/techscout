# 41. Role-Based Access Control Matrix (`RBAC.php`)

## File Location
[`app/Models/RBAC.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/RBAC.php)

## Key Functions
- `hasPermission($role, $permissionKey)`: Validates user role permissions against administrative permissions matrix (`manage_users`, `manage_mail`, `process_payouts`, `configure_firewall`, `view_audit_logs`).
- `getRolePermissions($role)`: Returns list of allowed permission keys.
