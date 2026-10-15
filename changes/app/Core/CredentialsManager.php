<?php
namespace App\Core;

class CredentialsManager {
    private static function getFilePath(): string {
        return dirname(__DIR__, 2) . '/credentials.txt';
    }

    public static function initRegistry(): void {
        try {
            $path = self::getFilePath();
            if (!file_exists($path)) {
                $dir = dirname($path);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                $header = "================================================================================\n" .
                          "TECHSCOUT USER CREDENTIALS & ACCOUNT ACCESS REGISTRY\n" .
                          "Auto-generated and synchronized on registration, password resets & admin updates\n" .
                          "================================================================================\n\n" .
                          str_pad("ID", 5) . " | " . str_pad("ROLE", 12) . " | " . str_pad("USERNAME", 20) . " | " . str_pad("EMAIL", 32) . " | PASSWORD\n" .
                          str_repeat("-", 95) . "\n" .
                          str_pad("1", 5) . " | " . str_pad("SUPER_ADMIN", 12) . " | " . str_pad("superadmin", 20) . " | " . str_pad("admin@techscout.online", 32) . " | Admin@123456\n" .
                          str_pad("2", 5) . " | " . str_pad("AUTHOR", 12) . " | " . str_pad("alexvance", 20) . " | " . str_pad("alex@techscout.online", 32) . " | Author@123456\n" .
                          str_pad("3", 5) . " | " . str_pad("AUTHOR", 12) . " | " . str_pad("sarah_dev", 20) . " | " . str_pad("sarah@techscout.online", 32) . " | Author@123456\n" .
                          str_pad("4", 5) . " | " . str_pad("AUTHOR", 12) . " | " . str_pad("david_chen", 20) . " | " . str_pad("david@techscout.online", 32) . " | Author@123456\n" .
                          str_pad("5", 5) . " | " . str_pad("AUTHOR", 12) . " | " . str_pad("elena_ai", 20) . " | " . str_pad("elena@techscout.online", 32) . " | Author@123456\n" .
                          str_pad("6", 5) . " | " . str_pad("AUTHOR", 12) . " | " . str_pad("marcus_db", 20) . " | " . str_pad("marcus@techscout.online", 32) . " | Author@123456\n" .
                          str_pad("7", 5) . " | " . str_pad("AUTHOR", 12) . " | " . str_pad("maya_sec", 20) . " | " . str_pad("maya@techscout.online", 32) . " | Author@123456\n" .
                          str_pad("8", 5) . " | " . str_pad("MEMBER", 12) . " | " . str_pad("kai_systems", 20) . " | " . str_pad("kai@techscout.online", 32) . " | Author@123456\n" .
                          str_pad("9", 5) . " | " . str_pad("MEMBER", 12) . " | " . str_pad("rachel_k8s", 20) . " | " . str_pad("rachel@techscout.online", 32) . " | Author@123456\n" .
                          str_pad("10", 5) . " | " . str_pad("MEMBER", 12) . " | " . str_pad("sam_llm", 20) . " | " . str_pad("sam@techscout.online", 32) . " | Author@123456\n\n";
                @file_put_contents($path, $header, LOCK_EX);
            }
        } catch (\Throwable $e) {}
    }

    public static function logCredential(int|string $id, string $role, string $username, string $email, string $password, string $actionType = 'REGISTERED'): void {
        try {
            self::initRegistry();
            $path = self::getFilePath();
            $date = date('Y-m-d H:i:s');
            $entry = "[" . $date . "] [" . strtoupper($actionType) . "] " .
                     str_pad("ID: " . $id, 8) . " | " .
                     str_pad("Role: " . strtoupper($role), 18) . " | " .
                     str_pad("User: @" . $username, 24) . " | " .
                     str_pad("Email: " . $email, 36) . " | " .
                     "Password: " . $password . "\n";
            @file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {}
    }
}
