<?php
namespace App\Core;

/**
 * CloudFront & CDN Edge Cache Helper
 * Handles automatic cache invalidation and edge CDN coordination for 10,000+ concurrent users.
 */
class CloudFrontHelper {
    private static ?string $distributionId = null;

    /**
     * Get the configured CloudFront Distribution ID
     */
    public static function getDistributionId(): string {
        if (self::$distributionId !== null) {
            return self::$distributionId;
        }
        self::$distributionId = getenv('AWS_CLOUDFRONT_DISTRIBUTION_ID') 
            ?: (defined('CLOUDFRONT_DISTRIBUTION_ID') ? constant('CLOUDFRONT_DISTRIBUTION_ID') : '');
        return self::$distributionId;
    }

    /**
     * Invalidate specific URL paths on CloudFront CDN
     * 
     * @param array $paths e.g. ['/prompts/*', '/tools/*', '/']
     * @return bool
     */
    public static function invalidatePaths(array $paths): bool {
        if (empty($paths)) {
            return true;
        }

        $distributionId = self::getDistributionId();
        
        // Log invalidation event locally for audit and debugging
        try {
            $callerReference = 'ts_' . time() . '_' . bin2hex(random_bytes(4));
            $pathsJson = json_encode(array_values(array_unique($paths)));
            
            // If AWS CLI is available and distribution ID is configured, trigger aws cloudfront create-invalidation
            if (!empty($distributionId) && function_exists('shell_exec')) {
                $items = implode(' ', array_map('escapeshellarg', $paths));
                $cmd = "aws cloudfront create-invalidation --distribution-id " . escapeshellarg($distributionId) . " --paths {$items} 2>&1";
                @shell_exec($cmd);
            }

            // Record to system_activity_logs or telemetry if available
            if (class_exists('\\App\\Core\\Database')) {
                try {
                    Database::execute(
                        "INSERT INTO system_activity_logs (event_type, description, metadata_json, created_at) VALUES ('cdn_invalidation', ?, ?, NOW())",
                        ["CloudFront Cache Invalidation for " . count($paths) . " paths", $pathsJson]
                    );
                } catch (\Throwable $e) {
                    // Ignore if activity table doesn't have exact schema
                }
            }
            return true;
        } catch (\Throwable $e) {
            error_log("[CloudFrontHelper] Invalidation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Invalidate cache for a specific content entity
     */
    public static function invalidateContent(string $type, string $slug): bool {
        $paths = ['/'];
        switch ($type) {
            case 'prompt':
                $paths[] = '/prompts';
                $paths[] = '/prompts/*';
                $paths[] = '/prompt/' . $slug;
                $paths[] = '/api/v1/prompts*';
                break;
            case 'tool':
            case 'ai_tool':
                $paths[] = '/tools';
                $paths[] = '/tools/*';
                $paths[] = '/tool/' . $slug;
                $paths[] = '/api/v1/tools*';
                break;
            case 'article':
                $paths[] = '/articles';
                $paths[] = '/articles/*';
                $paths[] = '/article/' . $slug;
                $paths[] = '/api/v1/articles*';
                break;
            case 'category':
                $paths[] = '/categories';
                $paths[] = '/category/' . $slug;
                $paths[] = '/c/' . $slug;
                break;
            default:
                $paths[] = '/' . $type . '/' . $slug;
                break;
        }
        return self::invalidatePaths($paths);
    }
}
