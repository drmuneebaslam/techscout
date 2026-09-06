<?php
namespace App\Models;

use App\Core\Database;
use App\Core\NotificationHelper;

class SupportEngine {

    const SLA_HOURS = [
        'urgent' => 2,
        'high' => 6,
        'medium' => 24,
        'normal' => 24,
        'low' => 48
    ];

    /**
     * Compute SLA due timestamp based on priority or custom hours
     */
    public static function calculateSlaDue(string $priority, ?int $customHours = null): string {
        $hours = $customHours ?: (self::SLA_HOURS[$priority] ?? 24);
        return date('Y-m-d H:i:s', strtotime("+{$hours} hours"));
    }

    /**
     * Fetch support categories
     */
    public static function getCategories(bool $activeOnly = true): array {
        $sql = "SELECT * FROM support_categories " . ($activeOnly ? "WHERE is_active = 1 " : "") . "ORDER BY sort_order ASC, name ASC";
        $categories = Database::query($sql);
        foreach ($categories as &$c) {
            $c['subcategories'] = !empty($c['subcategories_json']) ? json_decode($c['subcategories_json'], true) : [];
        }
        return $categories;
    }

    /**
     * Fetch single category by ID or slug
     */
    public static function getCategory($idOrSlug): ?array {
        if (is_numeric($idOrSlug)) {
            $c = Database::fetch("SELECT * FROM support_categories WHERE id = ?", [(int)$idOrSlug]);
        } else {
            $c = Database::fetch("SELECT * FROM support_categories WHERE slug = ?", [(string)$idOrSlug]);
        }
        if ($c) {
            $c['subcategories'] = !empty($c['subcategories_json']) ? json_decode($c['subcategories_json'], true) : [];
        }
        return $c;
    }

    /**
     * Save or update a support category with subcategories & defaults
     */
    public static function saveCategory(array $data): int {
        $id = !empty($data['id']) ? (int)$data['id'] : null;
        $dept = trim($data['department'] ?? 'Engineering & Systems');
        $name = trim($data['name'] ?? 'General Inquiries');
        $slug = trim($data['slug'] ?? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name)));
        $desc = trim($data['description'] ?? '');
        $subJson = is_array($data['subcategories'] ?? null) ? json_encode($data['subcategories']) : (string)($data['subcategories_json'] ?? '[]');
        $priority = in_array($data['default_priority'] ?? 'medium', ['low', 'medium', 'high', 'urgent'], true) ? $data['default_priority'] : 'medium';
        $sla = !empty($data['sla_hours']) ? (int)$data['sla_hours'] : 24;
        $order = (int)($data['sort_order'] ?? 0);
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        if ($id) {
            Database::execute("
                UPDATE support_categories 
                SET department = ?, name = ?, slug = ?, description = ?, subcategories_json = ?, default_priority = ?, sla_hours = ?, sort_order = ?, is_active = ?, updated_at = NOW()
                WHERE id = ?
            ", [$dept, $name, $slug, $desc, $subJson, $priority, $sla, $order, $isActive, $id]);
            return $id;
        }

        Database::execute("
            INSERT INTO support_categories 
            (department, name, slug, description, subcategories_json, default_priority, sla_hours, sort_order, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [$dept, $name, $slug, $desc, $subJson, $priority, $sla, $order, $isActive]);
        return (int)Database::lastInsertId();
    }

    /**
     * Delete a support category
     */
    public static function deleteCategory(int $id): bool {
        return Database::execute("DELETE FROM support_categories WHERE id = ?", [$id]);
    }

    /**
     * Get all canned macros
     */
    public static function getMacros(): array {
        return Database::query("SELECT * FROM ticket_canned_macros ORDER BY category ASC, usage_count DESC");
    }

    /**
     * Save a canned response macro
     */
    public static function saveMacro(string $category, string $title, string $shortcutCode, string $bodyContent, ?int $macroId = null, ?int $createdBy = null): int {
        if ($macroId) {
            Database::execute(
                "UPDATE ticket_canned_macros SET category = ?, title = ?, shortcut_code = ?, body_content = ? WHERE id = ?",
                [$category, $title, $shortcutCode, $bodyContent, $macroId]
            );
            return $macroId;
        }

        Database::execute(
            "INSERT INTO ticket_canned_macros (category, title, shortcut_code, body_content, created_by, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [$category, $title, $shortcutCode, $bodyContent, $createdBy]
        );

        return (int)Database::lastInsertId();
    }

    /**
     * Increment usage counter of a macro
     */
    public static function useMacro(int $macroId): void {
        Database::execute("UPDATE ticket_canned_macros SET usage_count = usage_count + 1 WHERE id = ?", [$macroId]);
    }

    /**
     * Delete a macro
     */
    public static function deleteMacro(int $macroId): bool {
        return Database::execute("DELETE FROM ticket_canned_macros WHERE id = ?", [$macroId]);
    }

    /**
     * Add an internal private staff note to a ticket
     */
    public static function addInternalNote(int $ticketId, int $staffUserId, string $noteContent): int {
        Database::execute(
            "INSERT INTO ticket_internal_notes (ticket_id, staff_user_id, note_content, created_at)
             VALUES (?, ?, ?, NOW())",
            [$ticketId, $staffUserId, $noteContent]
        );

        return (int)Database::lastInsertId();
    }

    /**
     * Get internal staff notes for a ticket
     */
    public static function getInternalNotes(int $ticketId): array {
        return Database::query(
            "SELECT tin.*, u.username as staff_name, u.role as staff_role 
             FROM ticket_internal_notes tin
             JOIN users u ON tin.staff_user_id = u.id
             WHERE tin.ticket_id = ?
             ORDER BY tin.created_at ASC",
            [$ticketId]
        );
    }

    /**
     * Auto-escalate tickets whose SLA has breached
     */
    public static function checkAndEscalateBreachedTickets(): int {
        $breached = Database::query(
            "SELECT id, subject, priority, user_id, sla_due_at 
             FROM support_tickets 
             WHERE status = 'open' AND is_escalated = 0 AND sla_due_at IS NOT NULL AND sla_due_at < NOW()"
        );

        $escalatedCount = 0;
        foreach ($breached as $t) {
            Database::execute(
                "UPDATE support_tickets SET is_escalated = 1, escalated_at = NOW() WHERE id = ?",
                [$t['id']]
            );
            $escalatedCount++;

            // Notify superadmins
            $superadmins = Database::query("SELECT id FROM users WHERE role = 'superadmin' OR role = 'admin'");
            foreach ($superadmins as $adm) {
                NotificationHelper::send(
                    (int)$adm['id'],
                    'support',
                    "🚨 SLA BREACH ESCALATION: Ticket #{$t['id']}",
                    "Ticket \"{$t['subject']}\" ({$t['priority']}) has breached its resolution SLA.",
                    "/admin/tickets"
                );
            }
        }

        return $escalatedCount;
    }
}
