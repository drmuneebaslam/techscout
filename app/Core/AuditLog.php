<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\AuditLog as ModelAuditLog;

/**
 * AuditLog Bridge / Facade
 * Forwards calls to App\Models\AuditLog to ensure backward compatibility and prevent namespace resolution exceptions.
 */
class AuditLog {
    public static function log(string $action, ?string $entityType = null, ?int $entityId = null, array $metadata = []): bool {
        return ModelAuditLog::log($action, $entityType, $entityId, $metadata);
    }

    public static function record(string $action, ?string $entityType = null, ?int $entityId = null, array $metadata = []): bool {
        return ModelAuditLog::record($action, $entityType, $entityId, $metadata);
    }

    public static function getRecent(int $limit = 50): array {
        return ModelAuditLog::getRecent($limit);
    }

    public static function getFiltered(?int $userId = null, ?string $action = null, ?string $entityType = null, int $limit = 100): array {
        return ModelAuditLog::getFiltered($userId, $action, $entityType, $limit);
    }

    public static function getDistinctActions(): array {
        return ModelAuditLog::getDistinctActions();
    }
}
