<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

class InboxController {
    public function index(Request $request): void {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = (int)$user['id'];
        $userEmail = (string)$user['email'];
        $authorId = (int)($user['author_id'] ?? 0);

        // 1. Fetch Editorial Reviews & Revision Notes for Author's content
        $editorialSubmissions = [];
        if ($authorId > 0) {
            $editorialSubmissions = Database::query("
                SELECT c.*, cat.name as category_name 
                FROM contents c 
                LEFT JOIN categories cat ON c.category_id = cat.id 
                WHERE c.author_id = ? AND (c.status IN ('in_review', 'rejected') OR c.editorial_notes IS NOT NULL)
                ORDER BY c.updated_at DESC
            ", [$authorId]);
        }

        // 2. Fetch Contact Messages & Admin Replies for this User
        $contactInquiries = Database::query("
            SELECT cm.*, u.username as replier_name 
            FROM contact_messages cm 
            LEFT JOIN users u ON cm.replied_by = u.id 
            WHERE cm.user_id = ? OR cm.email = ? 
            ORDER BY cm.created_at DESC
        ", [$userId, $userEmail]);

        // 3. Fetch Support Tickets & Threads
        $supportTickets = Database::query("
            SELECT st.*, 
                   (SELECT COUNT(*) FROM ticket_messages tm WHERE tm.ticket_id = st.id) as message_count,
                   (SELECT tm.message FROM ticket_messages tm WHERE tm.ticket_id = st.id ORDER BY tm.created_at DESC LIMIT 1) as latest_message
            FROM support_tickets st 
            WHERE st.user_id = ? 
            ORDER BY st.last_reply_at DESC
        ", [$userId]);

        // 4. Fetch Author Applications & Feedback
        $authorApps = Database::query("
            SELECT * FROM author_applications 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ", [$userId]);

        $isAuthorOrAdmin = Auth::isAdmin() || Auth::isAuthor() || $authorId > 0 || !empty($editorialSubmissions);
        $canAccessEditorial = $isAuthorOrAdmin;
        $canAccessApplications = true;
        $canAccessContact = true;
        $canAccessTickets = true;

        $edCount = $canAccessEditorial ? count($editorialSubmissions) : 0;
        $ciCount = count($contactInquiries);
        $stCount = count($supportTickets);
        $appCount = count($authorApps);
        $totalCommunicationsCount = $edCount + $ciCount + $stCount + $appCount;

        $activeTab = (string)$request->query('tab', 'all');

        $totalUnreadReplies = 0;
        foreach ($contactInquiries as $ci) {
            if ($ci['status'] === 'replied') $totalUnreadReplies++;
        }
        if ($canAccessEditorial) {
            foreach ($editorialSubmissions as $es) {
                if (!empty($es['editorial_notes'])) $totalUnreadReplies++;
            }
        }

        Response::view('inbox/index', [
            'title' => 'Communications & Editorial Inbox | TechScout',
            'user' => $user,
            'canAccessEditorial' => $canAccessEditorial,
            'canAccessApplications' => $canAccessApplications,
            'canAccessContact' => $canAccessContact,
            'canAccessTickets' => $canAccessTickets,
            'editorialSubmissions' => $editorialSubmissions,
            'contactInquiries' => $contactInquiries,
            'supportTickets' => $supportTickets,
            'authorApps' => $authorApps,
            'totalCommunicationsCount' => $totalCommunicationsCount,
            'activeTab' => $activeTab,
            'totalUnreadReplies' => $totalUnreadReplies
        ]);
    }
}
