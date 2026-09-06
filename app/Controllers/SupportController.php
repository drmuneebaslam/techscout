<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Core\Auth;
use App\Core\Session;
use App\Core\NotificationHelper;

class SupportController {

    private function processTicketAttachment(): ?string {
        if (!isset($_FILES['attachment_file']) || $_FILES['attachment_file']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES['attachment_file'];
        $maxSize = 25 * 1024 * 1024; // 25MB
        if ($file['size'] > $maxSize) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'zip', 'log', 'txt', 'csv', 'json', 'md', 'tar', 'gz'];
        if (!in_array($ext, $allowed, true)) {
            return null;
        }

        $targetDir = dirname(__DIR__, 2) . '/assets/uploads/tickets';
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $newFilename = 'ticket_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetPath = $targetDir . '/' . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/assets/uploads/tickets/' . $newFilename;
        }

        return null;
    }

    public function index(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('/auth/login?redirect=' . urlencode('/support'));
            return;
        }

        $userId = (int)$user['id'];
        $currentStatus = (string)$request->query('status', 'all');

        $sql = "SELECT st.*, 
                       (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = st.id) as message_count,
                       (SELECT message FROM ticket_messages WHERE ticket_id = st.id ORDER BY created_at DESC LIMIT 1) as last_message
                FROM support_tickets st 
                WHERE st.user_id = ?";
        $params = [$userId];

        if ($currentStatus !== 'all' && in_array($currentStatus, ['open', 'in_progress', 'waiting_user', 'resolved', 'closed'], true)) {
            $sql .= " AND st.status = ?";
            $params[] = $currentStatus;
        }

        $sql .= " ORDER BY (st.status = 'open') DESC, st.last_reply_at DESC";
        $tickets = Database::query($sql, $params);

        $categories = \App\Models\SupportEngine::getCategories(true);

        Response::view('support/index', [
            'title' => 'Support Desk & Engineering Tickets | TechScout',
            'tickets' => $tickets,
            'user' => $user,
            'currentStatus' => $currentStatus,
            'categories' => $categories
        ]);
    }

    public function create(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('/auth/login');
            return;
        }

        $userId = (int)$user['id'];
        $subject = mb_substr(trim((string)$request->post('subject', '')), 0, 150);
        $categorySlug = trim((string)$request->post('category', 'technical'));
        $subcategory = trim((string)$request->post('subcategory', ''));
        $priorityRaw = strtolower((string)$request->post('priority', ''));
        $message = mb_substr(trim((string)$request->post('message', '')), 0, 2500);
        
        $uploadedFileUrl = $this->processTicketAttachment();
        $attachment = $uploadedFileUrl ?: trim((string)$request->post('attachment_url', ''));

        if (empty($subject) || empty($message)) {
            Session::flash('error', 'Subject and detailed message are required.');
            Response::redirect('/support');
            return;
        }

        // Get category info from database
        $catInfo = \App\Models\SupportEngine::getCategory($categorySlug);
        $department = $catInfo ? $catInfo['department'] : 'Engineering & Systems';
        $categoryName = $catInfo ? $catInfo['name'] : ucfirst($categorySlug);
        
        // Priority resolution: request priority -> subcategory priority -> category default -> 'medium'
        $priority = 'medium';
        $slaHours = $catInfo['sla_hours'] ?? 24;

        if (!empty($subcategory) && !empty($catInfo['subcategories'])) {
            foreach ($catInfo['subcategories'] as $sub) {
                if (($sub['slug'] ?? '') === $subcategory || ($sub['name'] ?? '') === $subcategory) {
                    if (!empty($sub['priority'])) $priority = $sub['priority'];
                    if (!empty($sub['sla_hours'])) $slaHours = (int)$sub['sla_hours'];
                    $subcategory = $sub['name']; // Store clean name
                    break;
                }
            }
        }

        if (in_array($priorityRaw, ['low', 'medium', 'high', 'urgent'], true)) {
            $priority = $priorityRaw;
        } elseif (empty($priority) && !empty($catInfo['default_priority'])) {
            $priority = $catInfo['default_priority'];
        }

        $ticketNum = 'TS-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        $slaDueAt = \App\Models\SupportEngine::calculateSlaDue($priority, $slaHours);

        Database::execute(
            "INSERT INTO support_tickets 
             (ticket_number, user_id, department, category, subcategory, priority, sla_due_at, subject, status, last_reply_at, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'open', NOW(), NOW())",
            [
                $ticketNum,
                $userId,
                $department,
                $categoryName,
                $subcategory ?: 'General',
                $priority,
                $slaDueAt,
                $subject
            ]
        );
        $ticketId = (int)Database::lastInsertId();

        Database::execute("INSERT INTO ticket_messages (ticket_id, user_id, is_staff, message, attachment_url, created_at) VALUES (?, ?, 0, ?, ?, NOW())", [
            $ticketId, $userId, $message, $attachment
        ]);

        NotificationHelper::send($userId, 'system', "Support Ticket #{$ticketNum} Created", "Our technical support and operations staff have received your inquiry. Expected SLA resolution: {$slaDueAt}", "/support/ticket/{$ticketNum}");

        Session::flash('success', "Support Ticket #{$ticketNum} opened successfully.");
        Response::redirect('/support/ticket/' . $ticketNum);
    }

    public function show(Request $request, array $params): void {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('/auth/login');
            return;
        }

        $ticketNum = $params['ticket_number'] ?? '';
        $ticket = Database::fetch("SELECT st.*, u.username, u.email, COALESCE(a.display_name, u.username) as user_display_name, a.avatar_url 
                                  FROM support_tickets st 
                                  JOIN users u ON st.user_id = u.id 
                                  LEFT JOIN authors a ON u.id = a.user_id 
                                  WHERE st.ticket_number = ?", [$ticketNum]);

        if (!$ticket) {
            http_response_code(404);
            Response::view('errors/404', ['title' => 'Ticket Not Found | TechScout']);
            return;
        }

        $isAdmin = in_array($user['role'] ?? '', ['admin', 'super_admin', 'superadmin', 'editor', 'moderator'], true);
        if ((int)$ticket['user_id'] !== (int)$user['id'] && !$isAdmin) {
            http_response_code(403);
            Response::view('errors/404', ['title' => 'Access Restricted | TechScout']);
            return;
        }

        $messages = Database::query("SELECT tm.*, u.username, u.role as user_role, COALESCE(a.display_name, u.username) as display_name, a.avatar_url 
                                    FROM ticket_messages tm 
                                    JOIN users u ON tm.user_id = u.id 
                                    LEFT JOIN authors a ON u.id = a.user_id 
                                    WHERE tm.ticket_id = ? 
                                    ORDER BY tm.created_at ASC", [$ticket['id']]);

        $internalNotes = $isAdmin ? \App\Models\SupportEngine::getInternalNotes((int)$ticket['id']) : [];
        $cannedMacros = $isAdmin ? \App\Models\SupportEngine::getMacros() : [];

        Response::view('support/ticket', [
            'title' => "Ticket #{$ticket['ticket_number']}: {$ticket['subject']} | Support Desk",
            'ticket' => $ticket,
            'messages' => $messages,
            'internalNotes' => $internalNotes,
            'cannedMacros' => $cannedMacros,
            'isAdmin' => $isAdmin
        ]);
    }

    public function reply(Request $request, array $params): void {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('/auth/login');
            return;
        }

        $ticketNum = $params['ticket_number'] ?? '';
        $ticket = Database::fetch("SELECT * FROM support_tickets WHERE ticket_number = ?", [$ticketNum]);

        if (!$ticket) {
            Session::flash('error', 'Ticket not found.');
            Response::redirect('/support');
            return;
        }

        $isAdmin = in_array($user['role'] ?? '', ['admin', 'super_admin', 'superadmin', 'editor', 'moderator'], true);
        if ((int)$ticket['user_id'] !== (int)$user['id'] && !$isAdmin) {
            Session::flash('error', 'Unauthorized.');
            Response::redirect('/support');
            return;
        }

        $message = trim((string)$request->post('message', ''));
        
        $uploadedFileUrl = $this->processTicketAttachment();
        $attachment = $uploadedFileUrl ?: trim((string)$request->post('attachment_url', ''));
        
        $isInternal = $isAdmin && $request->post('is_internal_note') ? 1 : 0;
        $newStatus = $request->post('status') ? (string)$request->post('status') : ($isAdmin ? 'waiting_user' : 'open');
        $priority = $isAdmin && $request->post('priority') ? (string)$request->post('priority') : $ticket['priority'];

        if (!empty($message) || !empty($attachment)) {
            $msgContent = !empty($message) ? $message : ($uploadedFileUrl ? '[Attached File: ' . basename($uploadedFileUrl) . ']' : '[Attachment URL]');
            Database::execute("INSERT INTO ticket_messages (ticket_id, user_id, is_staff, is_internal_note, message, attachment_url, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())", [
                $ticket['id'], (int)$user['id'], $isAdmin ? 1 : 0, $isInternal, $msgContent, $attachment
            ]);

            Database::execute("UPDATE support_tickets SET status = ?, priority = ?, last_reply_at = NOW(), updated_at = NOW() WHERE id = ?", [
                $newStatus, $priority, $ticket['id']
            ]);

            // Notify counterpart
            if ($isAdmin && !$isInternal) {
                NotificationHelper::send((int)$ticket['user_id'], 'ticket_reply', "Staff Replied to Ticket #{$ticketNum}", "Support team has sent an update regarding: {$ticket['subject']}", "/support/ticket/{$ticketNum}");
            } elseif (!$isAdmin) {
                // Notify admin staff
                $admins = Database::query("SELECT id FROM users WHERE role IN ('admin', 'superadmin', 'editor')");
                foreach ($admins as $adm) {
                    NotificationHelper::send((int)$adm['id'], 'ticket_reply', "User Replied on Ticket #{$ticketNum}", "{$user['username']} replied on: {$ticket['subject']}", "/support/ticket/{$ticketNum}");
                }
            }

            Session::flash('success', $isInternal ? 'Internal staff note recorded.' : 'Response posted successfully with attachments.');
        }

        Response::redirect('/support/ticket/' . $ticketNum);
    }

    // ------------------------------------------------------------------------
    // API & ADMIN CATEGORY / TAXONOMY MANAGEMENT
    // ------------------------------------------------------------------------

    public function apiCategories(Request $request): void {
        header('Content-Type: application/json');
        $categories = \App\Models\SupportEngine::getCategories(true);
        echo json_encode([
            'status' => 'success',
            'categories' => $categories
        ]);
        exit;
    }

    public function adminCategories(Request $request): void {
        $user = Auth::user();
        if (!$user || !in_array($user['role'] ?? '', ['admin', 'super_admin', 'superadmin'], true)) {
            Response::redirect('/admin');
            return;
        }

        $categories = \App\Models\SupportEngine::getCategories(false);
        Response::view('admin/support_categories', [
            'title' => 'Support Category & SLA Architecture | Admin Hub',
            'categories' => $categories
        ]);
    }

    public function saveCategory(Request $request): void {
        $user = Auth::user();
        if (!$user || !in_array($user['role'] ?? '', ['admin', 'super_admin', 'superadmin'], true)) {
            Response::json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            return;
        }

        $id = (int)$request->post('id', 0) ?: null;
        $department = trim((string)$request->post('department', 'Engineering & Systems'));
        $name = trim((string)$request->post('name', ''));
        $slug = trim((string)$request->post('slug', ''));
        $description = trim((string)$request->post('description', ''));
        $defaultPriority = trim((string)$request->post('default_priority', 'medium'));
        $slaHours = (int)$request->post('sla_hours', 24);
        $sortOrder = (int)$request->post('sort_order', 0);
        $isActive = (int)$request->post('is_active', 1);

        $subcategoriesRaw = $request->post('subcategories');
        $subcategories = [];
        if (is_array($subcategoriesRaw)) {
            $subcategories = $subcategoriesRaw;
        } elseif (is_string($subcategoriesRaw)) {
            $decoded = json_decode($subcategoriesRaw, true);
            if (is_array($decoded)) $subcategories = $decoded;
        }

        if (empty($name)) {
            Session::flash('error', 'Category name is required.');
            Response::redirect('/admin/support/categories');
            return;
        }

        \App\Models\SupportEngine::saveCategory([
            'id' => $id,
            'department' => $department,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'subcategories' => $subcategories,
            'default_priority' => $defaultPriority,
            'sla_hours' => $slaHours,
            'sort_order' => $sortOrder,
            'is_active' => $isActive
        ]);

        Session::flash('success', 'Support department & category hierarchy updated successfully.');
        Response::redirect('/admin/support/categories');
    }

    public function deleteCategory(Request $request, array $params): void {
        $user = Auth::user();
        if (!$user || !in_array($user['role'] ?? '', ['admin', 'super_admin', 'superadmin'], true)) {
            Response::redirect('/admin');
            return;
        }

        $id = (int)($params['id'] ?? 0);
        if ($id > 0) {
            \App\Models\SupportEngine::deleteCategory($id);
            Session::flash('success', 'Support category deleted.');
        }

        Response::redirect('/admin/support/categories');
    }

    public function addSubcategory(Request $request, array $params): void {
        $user = Auth::user();
        if (!$user || !in_array($user['role'] ?? '', ['admin', 'super_admin', 'superadmin'], true)) {
            Response::redirect('/admin');
            return;
        }

        $catId = (int)($params['id'] ?? 0);
        $name = trim((string)$request->post('name', ''));
        $slug = trim((string)$request->post('slug', ''));
        $priority = trim((string)$request->post('priority', 'medium'));
        $slaHours = (int)$request->post('sla_hours', 24);

        if ($catId > 0 && !empty($name)) {
            $cat = \App\Models\SupportEngine::getCategory($catId);
            if ($cat) {
                if (empty($slug)) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $name), '_'));
                }
                $subcats = $cat['subcategories'] ?? [];
                $existingIndex = -1;
                foreach ($subcats as $idx => $s) {
                    if (($s['slug'] ?? '') === $slug) {
                        $existingIndex = $idx;
                        break;
                    }
                }
                $newSub = [
                    'name' => $name,
                    'slug' => $slug,
                    'priority' => $priority,
                    'sla_hours' => $slaHours
                ];
                if ($existingIndex >= 0) {
                    $subcats[$existingIndex] = $newSub;
                } else {
                    $subcats[] = $newSub;
                }

                \App\Models\SupportEngine::saveCategory([
                    'id' => $cat['id'],
                    'department' => $cat['department'],
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'description' => $cat['description'],
                    'subcategories' => $subcats,
                    'default_priority' => $cat['default_priority'],
                    'sla_hours' => $cat['sla_hours'],
                    'sort_order' => $cat['sort_order'],
                    'is_active' => $cat['is_active']
                ]);
                Session::flash('success', "Subcategory '{$name}' added to category '{$cat['name']}'.");
            }
        }
        Response::redirect('/admin/support/categories');
    }

    public function deleteSubcategory(Request $request, array $params): void {
        $user = Auth::user();
        if (!$user || !in_array($user['role'] ?? '', ['admin', 'super_admin', 'superadmin'], true)) {
            Response::redirect('/admin');
            return;
        }

        $catId = (int)($params['id'] ?? 0);
        $slug = trim((string)$request->post('slug', $request->query('slug', '')));

        if ($catId > 0 && !empty($slug)) {
            $cat = \App\Models\SupportEngine::getCategory($catId);
            if ($cat) {
                $subcats = $cat['subcategories'] ?? [];
                $filtered = array_values(array_filter($subcats, fn($s) => ($s['slug'] ?? '') !== $slug && ($s['name'] ?? '') !== $slug));
                \App\Models\SupportEngine::saveCategory([
                    'id' => $cat['id'],
                    'department' => $cat['department'],
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'description' => $cat['description'],
                    'subcategories' => $filtered,
                    'default_priority' => $cat['default_priority'],
                    'sla_hours' => $cat['sla_hours'],
                    'sort_order' => $cat['sort_order'],
                    'is_active' => $cat['is_active']
                ]);
                Session::flash('success', "Subcategory removed.");
            }
        }
        Response::redirect('/admin/support/categories');
    }
}

