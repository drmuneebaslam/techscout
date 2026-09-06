# TechScout — Notifications & Media Processing Pipeline

> **Tasks Covered**: 555–570 (Phase 20 — Notifications Engine) & 571–585 (Phase 21 — Media Processing System)  
> **Key Capabilities**: Real-time In-App Notification Center, WebP/AVIF Compression Pipeline, Responsive Multi-Resolution Thumbnailing, and Orphan Media Cleanup.

---

## 1. Notification Engine Architecture (Tasks 555–570)

The Notification Engine keeps authors, editors, and members informed in real time across the platform:

```
                            EVENT TRIGGERED
        (e.g., Comment Reply, New Follower, Article Approved)
                                   │
                                   ▼
                   [1. Event Dispatcher & Payload]
                                   │
                                   ▼
                 [2. User Notification Preferences Check]
                 - Check `email_notifications` (enabled/disabled)
                 - Check `in_app_notifications` (enabled/disabled)
                                   │
                   ┌───────────────┴───────────────┐
                   ▼                               ▼
        [3. In-App Notification]         [4. Async Mailer Queue]
        Insert into `notifications`       Dispatch HTML email alert
        Update unread counter badge       with deep link
```

### Supported Notification Event Types:
1. `comment_reply` (Task 562): When someone replies to your comment.
2. `article_approved` (Task 564): When an editor publishes an author's submitted draft.
3. `article_rejected` (Task 565): When feedback/revisions are required on a submission.
4. `new_follower` (Task 563): When a reader follows an author profile.
5. `milestone_reached` (Task 568): When an article hits 10,000 views or author unlocks a new reputation level.
6. `credit_earned` (Task 566): When non-cash promo credits are deposited into author wallet.

### Task 556: In-App Notification Center UI
- Accessible via the top navbar bell icon `[ 🔔 3 ]`.
- Dropdown drawer shows unread alerts with time ago, avatar of triggering user, and one-click mark all as read.

---

## 2. Media Upload & Processing Pipeline (Tasks 571–585)

```
                       AUTHOR UPLOADS IMAGE
                  (JPEG, PNG, WebP up to 10MB)
                               │
                               ▼
               [1. Security & MIME-Type Validation]
               - Verify actual binary headers (finfo_file)
               - Block executable extensions (.php, .exe, .sh)
                               │
                               ▼
                 [2. Image Processing (GD / Imagick)]
                 - Strip sensitive EXIF metadata (GPS, Camera)
                 - Auto-orient based on EXIF tag
                               │
                               ▼
               [3. Multi-Resolution & WebP Conversion]
               - Hero / High-Res: 1200x675 (WebP @ 82% quality)
               - Card Thumbnail:   600x338 (WebP @ 80% quality)
               - Square Thumbnail: 200x200 (WebP @ 80% quality)
                               │
                               ▼
                  [4. File Storage & URL Generator]
              /uploads/articles/{year}/{month}/{unique_hash}.webp
```

### Task 572 & 573: WebP Compression Algorithm (PHP)
```php
function processAndCompressImage(string $sourcePath, string $targetDir, string $fileBaseName): array {
    $imgInfo = getimagesize($sourcePath);
    $mime = $imgInfo['mime'] ?? '';
    
    // Load image resource based on MIME
    switch ($mime) {
        case 'image/jpeg': $src = imagecreatefromjpeg($sourcePath); break;
        case 'image/png':  $src = imagecreatefrompng($sourcePath);  break;
        case 'image/webp': $src = imagecreatefromwebp($sourcePath); break;
        default: throw new Exception('Unsupported image format.');
    }
    
    $origW = imagesx($src);
    $origH = imagesy($src);
    
    $sizes = [
        'lg' => ['w' => 1200, 'h' => (int)($origH * (1200 / $origW))],
        'md' => ['w' => 600,  'h' => (int)($origH * (600 / $origW))],
        'sm' => ['w' => 200,  'h' => 200, 'crop' => true]
    ];
    
    $results = [];
    foreach ($sizes as $key => $dim) {
        $dst = imagecreatetruecolor($dim['w'], $dim['h']);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $dim['w'], $dim['h'], $origW, $origH);
        
        $destFile = "{$targetDir}/{$fileBaseName}-{$key}.webp";
        imagewebp($dst, $destFile, 82);
        imagedestroy($dst);
        $results[$key] = $destFile;
    }
    imagedestroy($src);
    return $results;
}
```

### Task 585: Orphaned Media Cleanup Cron Job
- Automated weekly cron searches `/uploads/` for image files not referenced in `contents`, `authors`, or `advertisements`.
- Orphaned temporary files older than 7 days are safely archived and purged to conserve disk storage.
