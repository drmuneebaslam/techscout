<?php
namespace App\Core;

use Throwable;

class BannerHelper {
    public static function getActiveBanners(): array {
        try {
            $user = Auth::user();
            $role = $user ? $user['role'] : 'guest';

            $rows = Database::query(
                "SELECT * FROM site_announcements 
                 WHERE is_active = 1 
                 AND (start_at IS NULL OR start_at <= NOW()) 
                 AND (end_at IS NULL OR end_at >= NOW()) 
                 ORDER BY id DESC"
            );

            $filtered = [];
            foreach ($rows as $r) {
                $target = $r['target_audience'];
                if ($target === 'all') {
                    $filtered[] = $r;
                } elseif ($target === 'guests' && !$user) {
                    $filtered[] = $r;
                } elseif ($target === 'members' && $user) {
                    $filtered[] = $r;
                } elseif ($target === 'authors' && $user && ($role === 'author' || $role === 'admin' || $role === 'super_admin')) {
                    $filtered[] = $r;
                }
            }

            return $filtered;
        } catch (Throwable $e) {
            return [];
        }
    }
}
