<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Auth;

class RBAC {
    /**
     * Complete Master Catalog of System Capabilities & Permission Nodes
     */
    public static function getPermissionCatalog(): array {
        return [
            'Financial & Revenue Subsystems' => [
                'revenue.view' => 'View Revenue Dashboards & Financial Metrics',
                'revenue.payouts' => 'Review & Process Author Payout Claims',
                'revenue.ledger' => 'View Universal Double-Entry Financial Ledger',
                'revenue.adjust_balance' => 'Execute Admin Credit & Debit / Clawback Operations',
                'revenue.tipping' => 'View Reader Tipping & Micro-Sponsorship Streams',
                'revenue.referrals' => 'Audit Creator Ambassador Commissions & Multi-Tier Bonuses',
                'revenue.settings' => 'Modify Global Revenue Share & CPM Multiplier Parameters'
            ],
            'Editorial & Publications Governance' => [
                'articles.view' => 'View Full Internal Article Repository',
                'articles.create' => 'Create & Publish Platform Official Publications',
                'articles.edit' => 'Edit Any Published Article or Revision Draft',
                'articles.publish' => 'Approve & Publish Pending Submissions',
                'articles.request_changes' => 'Request Editorial Revisions & Send Notes to Authors',
                'articles.delete' => 'Delete / Unpublish Articles',
                'articles.feature' => 'Set Featured Articles & Feed Hero Spotlight',
                'articles.plagiarism_scan' => 'Trigger N-Gram Fingerprinting & Code Plagiarism Scans'
            ],
            'Creator Studio & Author Management' => [
                'authors.view' => 'View All Creator Profiles & Internal Analytics',
                'authors.verify' => 'Approve Author Verification Badges & Pioneer Status',
                'authors.monetization' => 'Approve / Decline Monetization Partner Applications',
                'authors.tier_override' => 'Manually Override Author Progression Tiers (Bronze to Elite)',
                'authors.collaborations' => 'Audit Co-Authoring Contracts & Split Disbursals',
                'authors.suspend' => 'Suspend / Ban Creator Publishing Privileges'
            ],
            'Trust, Safety & Community Moderation' => [
                'community.comments' => 'Moderate, Hide & Delete Community Comments',
                'community.reports' => 'Review & Resolve User Reported Content & Flags',
                'community.moderation' => 'Issue User Strikes, Warnings & Account Bans',
                'community.reputation' => 'Adjust User Reputation XP & Engagement Scores',
                'plagiarism.view' => 'Inspect Plagiarism & Content Fingerprint Reports',
                'plagiarism.override' => 'Override Flagged Plagiarism & Clear Submissions'
            ],
            'Support & Customer Experience' => [
                'support.tickets' => 'View All Customer & Creator Support Tickets',
                'support.reply' => 'Reply to Inquiries & Attach File Proofs',
                'support.escalate' => 'Reassign & Escalate Priority Tickets',
                'support.macros' => 'Create, Edit & Delete Canned Response Macros',
                'support.internal_notes' => 'Post & View Private Internal Staff Notes',
                'support.kb' => 'Manage Support Knowledge Base Articles'
            ],
            'Cybersecurity, Firewall & Session Management' => [
                'security.firewall' => 'Manage IP Blocklists & WAF Perimeter Rules',
                'security.lockouts' => 'View & Unlock Brute-Force IP Lockouts',
                'security.anomalies' => 'Inspect & Resolve Security Anomaly Telemetry Streams',
                'security.sessions' => 'Inspect & Terminate Active User Remote Sessions'
            ],
            'System Architecture & Database Disaster Recovery' => [
                'system.rbac' => 'Manage Admin Roles, Capabilities & Staff Assignments',
                'system.logs' => 'View Enterprise Audit Logs & State Diff Timeline',
                'system.backups' => 'Generate One-Click SQL Backups & Download Snapshots',
                'system.restore' => 'Execute Disaster Recovery Snapshot Restores',
                'system.maintenance' => 'Toggle Read-Only & Full Platform Maintenance Modes',
                'system.flags' => 'Toggle System Feature Flags & Beta Modules',
                'cms.settings' => 'Modify Platform Branding, SEO & Navigation Menus'
            ]
        ];
    }

    /**
     * Check if the current (or specified) user holds a required capability
     */
    public static function hasPermission(string $permissionKey, ?int $userId = null): bool {
        $user = $userId ? Database::fetch("SELECT * FROM users WHERE id = ?", [$userId]) : Auth::user();
        if (!$user) return false;

        $role = $user['role'] ?? '';
        $roleId = (int)($user['admin_role_id'] ?? 0);

        // Superadmin and unrestricted Admin have universal global bypass across all capabilities
        if (in_array($role, ['super_admin', 'superadmin'], true) || ($role === 'admin' && $roleId <= 0)) {
            return true;
        }

        // If user has no specific admin role ID, deny permission
        if ($roleId <= 0) {
            return false;
        }

        try {
            $has = Database::fetch(
                "SELECT 1 FROM admin_role_permissions WHERE role_id = ? AND permission_key = ? LIMIT 1",
                [$roleId, $permissionKey]
            );
            return !empty($has);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get all active roles with assigned permissions
     */
    public static function getRoles(): array {
        $roles = Database::query("SELECT * FROM admin_roles ORDER BY is_system DESC, id ASC");
        foreach ($roles as &$r) {
            $perms = Database::query("SELECT permission_key FROM admin_role_permissions WHERE role_id = ?", [$r['id']]);
            $r['permissions'] = array_column($perms, 'permission_key');
            $userCount = Database::fetch("SELECT COUNT(*) as c FROM users WHERE admin_role_id = ? OR (role = ? AND ? = 1)", [$r['id'], $r['role_key'], $r['id']]);
            $r['user_count'] = (int)($userCount['c'] ?? 0);
        }
        return $roles;
    }

    /**
     * Save / Update a Custom Role
     */
    public static function saveRole(string $name, string $roleKey, string $description, array $permissions, ?int $roleId = null): int {
        if ($roleId) {
            Database::execute(
                "UPDATE admin_roles SET name = ?, description = ? WHERE id = ?",
                [$name, $description, $roleId]
            );
        } else {
            Database::execute(
                "INSERT INTO admin_roles (name, role_key, description, is_system, created_at) VALUES (?, ?, ?, 0, NOW())",
                [$name, $roleKey, $description]
            );
            $roleId = (int)Database::lastInsertId();
        }

        // Sync permissions
        Database::execute("DELETE FROM admin_role_permissions WHERE role_id = ?", [$roleId]);
        foreach ($permissions as $p) {
            Database::execute("INSERT INTO admin_role_permissions (role_id, permission_key) VALUES (?, ?)", [$roleId, $p]);
        }

        AuditLog::record('admin.role_saved', 'admin_roles', $roleId, [
            'name' => $name,
            'role_key' => $roleKey,
            'permission_count' => count($permissions)
        ]);

        return $roleId;
    }

    /**
     * Assign Role to User
     */
    public static function assignUserRole(int $userId, ?int $roleId): bool {
        return Database::execute("UPDATE users SET admin_role_id = ? WHERE id = ?", [$roleId, $userId]);
    }
}
