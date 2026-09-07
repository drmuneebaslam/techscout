# 36. Member Patronage Creator Tipping Engine (`CreatorTipping.php`)

## File Location
[`app/Models/CreatorTipping.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CreatorTipping.php)

## Key Functions
- `sendTip($senderId, $authorId, $amount, $message)`: Transfers reader tip credits to author balance, creating double-entry financial ledger records.
- `getAuthorTipsReceived($authorId)`: Returns tips history for creator dashboard.
