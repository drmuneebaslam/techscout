# 44. Customer Support Ticket Engine (`SupportEngine.php`)

## File Location
[`app/Models/SupportEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SupportEngine.php)

## Key Functions
- `createTicket($userId, $subject, $message, $priority)`: Creates new support ticket thread in `support_tickets`.
- `addReply($ticketId, $userId, $message, $isStaff)`: Appends member or staff response to ticket conversation.
- `updateTicketStatus($ticketId, $status)`: Transitions ticket status (`open`, `pending`, `closed`).
