<?php
namespace App\Core;

class Config {
    public static array $settings = [
        'app_name' => 'TechScout',
        'app_tagline' => 'The Frontier of Tech, Scouted for Builders.',
        'app_url' => 'https://funwithcoding.free.nf',
        'primary_color' => '#38bdf8',
        'support_email' => 'support@techscout.ai',
        'site_copyright' => '© 2026 TechScout Inc. All rights reserved.',
        'default_theme' => 'dark',
        'logo_url' => '',
        'favicon_url' => '',

        // Hosted InfinityFree Production Database
        'db_host' => 'sql306.infinityfree.com',
        'db_port' => '3306',
        'db_name' => 'if0_40102772_techscoutmainafterallchanges',
        'db_user' => 'if0_40102772',
        'db_pass' => 'Fp9QFWfj5fm',

        // Localhost Fallback Credentials (Used automatically when run locally from FTP backup)
        'local_db_host' => '127.0.0.1',
        'local_db_port' => '3306',
        'local_db_name' => 'techscout_db',
        'local_db_user' => 'root',
        'local_db_pass' => '',
        'local_app_url' => 'http://localhost/techscout.n',

        'session_lifetime' => 86400 * 7,
        'environment' => 'production' // 'development' or 'production'
    ];

    /**
     * Determines whether current execution environment is a local development machine
     */
    public static function isLocalEnvironment(): bool {
        if (php_sapi_name() === 'cli') {
            return true;
        }
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
        $hostClean = strtolower(explode(':', $host)[0]);
        if (in_array($hostClean, ['localhost', '127.0.0.1', '::1'], true) || str_ends_with($hostClean, '.test') || str_ends_with($hostClean, '.local')) {
            return true;
        }
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
        if (in_array($remoteAddr, ['127.0.0.1', '::1'], true)
            || str_starts_with($remoteAddr, '192.168.')
            || str_starts_with($remoteAddr, '10.')
            || str_starts_with($remoteAddr, '172.16.')
            || str_starts_with($remoteAddr, '172.17.')
            || str_starts_with($remoteAddr, '172.18.')) {
            return true;
        }
        return false;
    }

    /**
     * Dynamically detects current base URL from server environment
     */
    public static function detectBaseUrl(): string {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
            || (!empty($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO']) === 'https')
            || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
            || (!empty($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https'))
            || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443)
            || (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'techscout.online'));
        $protocol = $isHttps ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        return $protocol . '://' . $host . ($scriptDir ? '/' . $scriptDir : '');
    }

    public static function get(string $key, $default = null) {
        $isLocal = self::isLocalEnvironment();

        // Intelligent EC2 Auto-Detection: When running on EC2 production instance,
        // guarantee local MySQL credentials and HTTPS base URL regardless of git sync state
        $dirNormalized = str_replace('\\', '/', __DIR__);
        $isEc2Server = str_starts_with($dirNormalized, '/var/www/techscout') 
            || file_exists('/etc/techscout_ec2')
            || (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'techscout.online'));

        if ($isEc2Server) {
            if ($key === 'db_host') return '127.0.0.1';
            if ($key === 'db_port') return '3306';
            if ($key === 'db_name') return 'techscout_db';
            if ($key === 'db_user') return 'techscout_user';
            if ($key === 'db_pass') return 'TS8nFyML9C@8nF';
            if ($key === 'app_url') return 'https://techscout.online';
            if ($key === 'environment') return 'production';
        }

        // Intelligent bridge: When running on localhost from an FTP backup with InfinityFree credentials,
        // automatically route database credentials to local MySQL fallback
        $remoteHost = self::$settings['db_host'] ?? '';
        $isRemoteHostedDb = str_contains($remoteHost, 'infinityfree.com') 
            || str_contains($remoteHost, 'epizy.com') 
            || str_contains($remoteHost, 'byethost');

        if ($isLocal && $isRemoteHostedDb) {
            if ($key === 'db_host') return self::$settings['local_db_host'] ?? '127.0.0.1';
            if ($key === 'db_port') return self::$settings['local_db_port'] ?? '3306';
            if ($key === 'db_name') return self::$settings['local_db_name'] ?? 'techscout_db';
            if ($key === 'db_user') return self::$settings['local_db_user'] ?? 'root';
            if ($key === 'db_pass') return self::$settings['local_db_pass'] ?? '';
            if ($key === 'app_url') return self::$settings['local_app_url'] ?? self::detectBaseUrl();
            if ($key === 'environment') return 'development';
        }

        return self::$settings[$key] ?? $default;
    }

    public static function set(string $key, $value): void {
        self::$settings[$key] = $value;
    }

    /**
     * Persist updated configuration array cleanly to app/Core/Config.php
     */
    public static function saveConfig(array $newSettings): bool {
        $merged = array_merge(self::$settings, $newSettings);
        self::$settings = $merged;

        $filePath = __DIR__ . '/Config.php';
        if (!is_writable($filePath) && file_exists($filePath)) {
            @chmod($filePath, 0666);
        }

        $exportedArray = "[\n";
        foreach ($merged as $k => $v) {
            $exportedKey = var_export($k, true);
            $exportedVal = var_export($v, true);
            $exportedArray .= "        {$exportedKey} => {$exportedVal},\n";
        }
        $exportedArray .= "    ]";

        $phpCode = '<?php' . "\n" .
'namespace App\Core;' . "\n\n" .
'class Config {' . "\n" .
'    public static array $settings = ' . $exportedArray . ';' . "\n\n" .
'    public static function isLocalEnvironment(): bool {' . "\n" .
'        if (php_sapi_name() === \'cli\') {' . "\n" .
'            return true;' . "\n" .
'        }' . "\n" .
'        $host = $_SERVER[\'HTTP_HOST\'] ?? ($_SERVER[\'SERVER_NAME\'] ?? \'\');' . "\n" .
'        $hostClean = strtolower(explode(\':\', $host)[0]);' . "\n" .
'        if (in_array($hostClean, [\'localhost\', \'127.0.0.1\', \'::1\'], true) || str_ends_with($hostClean, \'.test\') || str_ends_with($hostClean, \'.local\')) {' . "\n" .
'            return true;' . "\n" .
'        }' . "\n" .
'        $remoteAddr = $_SERVER[\'REMOTE_ADDR\'] ?? \'\';' . "\n" .
'        if (in_array($remoteAddr, [\'127.0.0.1\', \'::1\'], true)' . "\n" .
'            || str_starts_with($remoteAddr, \'192.168.\')' . "\n" .
'            || str_starts_with($remoteAddr, \'10.\')' . "\n" .
'            || str_starts_with($remoteAddr, \'172.16.\')' . "\n" .
'            || str_starts_with($remoteAddr, \'172.17.\')' . "\n" .
'            || str_starts_with($remoteAddr, \'172.18.\')) {' . "\n" .
'            return true;' . "\n" .
'        }' . "\n" .
'        return false;' . "\n" .
'    }' . "\n\n" .
'    public static function detectBaseUrl(): string {' . "\n" .
'        $isHttps = (!empty($_SERVER[\'HTTPS\']) && $_SERVER[\'HTTPS\'] !== \'off\')' . "\n" .
'            || (!empty($_SERVER[\'HTTP_X_FORWARDED_PROTO\']) && strtolower($_SERVER[\'HTTP_X_FORWARDED_PROTO\']) === \'https\')' . "\n" .
'            || (!empty($_SERVER[\'HTTP_X_FORWARDED_SSL\']) && strtolower($_SERVER[\'HTTP_X_FORWARDED_SSL\']) === \'on\')' . "\n" .
'            || (!empty($_SERVER[\'HTTP_CF_VISITOR\']) && str_contains($_SERVER[\'HTTP_CF_VISITOR\'], \'https\'))' . "\n" .
'            || ((int)($_SERVER[\'SERVER_PORT\'] ?? 0) === 443);' . "\n" .
'        $protocol = $isHttps ? \'https\' : \'http\';' . "\n" .
'        $host = $_SERVER[\'HTTP_HOST\'] ?? \'localhost\';' . "\n" .
'        $scriptDir = trim(str_replace(\'\\\\\', \'/\', dirname($_SERVER[\'SCRIPT_NAME\'] ?? \'\')), \'/\');' . "\n" .
'        return $protocol . \'://\' . $host . ($scriptDir ? \'/\' . $scriptDir : \'\');' . "\n" .
'    }' . "\n\n" .
'    public static function get(string $key, $default = null) {' . "\n" .
'        $isLocal = self::isLocalEnvironment();' . "\n" .
'        $remoteHost = self::$settings[\'db_host\'] ?? \'\';' . "\n" .
'        $isRemoteHostedDb = str_contains($remoteHost, \'infinityfree.com\')' . "\n" .
'            || str_contains($remoteHost, \'epizy.com\')' . "\n" .
'            || str_contains($remoteHost, \'byethost\');' . "\n\n" .
'        if ($isLocal && $isRemoteHostedDb) {' . "\n" .
'            if ($key === \'db_host\') return self::$settings[\'local_db_host\'] ?? \'127.0.0.1\';' . "\n" .
'            if ($key === \'db_port\') return self::$settings[\'local_db_port\'] ?? \'3306\';' . "\n" .
'            if ($key === \'db_name\') return self::$settings[\'local_db_name\'] ?? \'techscout_db\';' . "\n" .
'            if ($key === \'db_user\') return self::$settings[\'local_db_user\'] ?? \'root\';' . "\n" .
'            if ($key === \'db_pass\') return self::$settings[\'local_db_pass\'] ?? \'\';' . "\n" .
'            if ($key === \'app_url\') return self::$settings[\'local_app_url\'] ?? self::detectBaseUrl();' . "\n" .
'            if ($key === \'environment\') return \'development\';' . "\n" .
'        }' . "\n\n" .
'        return self::$settings[$key] ?? $default;' . "\n" .
'    }' . "\n\n" .
'    public static function set(string $key, $value): void {' . "\n" .
'        self::$settings[$key] = $value;' . "\n" .
'    }' . "\n\n" .
'    public static function url(string $path = \'\'): string {' . "\n" .
'        $path = trim($path);' . "\n" .
'        if ($path === \'\' || $path === \'#\') {' . "\n" .
'            return $path;' . "\n" .
'        }' . "\n" .
'        if (str_starts_with($path, \'http://\') || str_starts_with($path, \'https://\') || str_starts_with($path, \'data:\') || str_starts_with($path, \'mailto:\') || str_starts_with($path, \'javascript:\') || str_starts_with($path, \'tel:\')) {' . "\n" .
'            return $path;' . "\n" .
'        }' . "\n" .
'        $base = rtrim(self::get(\'app_url\', \'https://funwithcoding.free.nf\'), \'/\');' . "\n" .
'        $parsed = parse_url($base);' . "\n" .
'        $basePath = trim($parsed[\'path\'] ?? \'\', \'/\');' . "\n" .
'        if ($basePath !== \'\') {' . "\n" .
'            $prefix = \'/\' . $basePath;' . "\n" .
'            if (str_starts_with($path, $prefix . \'/\')) {' . "\n" .
'                $path = substr($path, strlen($prefix));' . "\n" .
'            } elseif ($path === $prefix) {' . "\n" .
'                $path = \'\';' . "\n" .
'            }' . "\n" .
'        }' . "\n" .
'        $cleanPath = ltrim($path, \'/\');' . "\n" .
'        return $cleanPath ? "{$base}/{$cleanPath}" : $base . \'/\';' . "\n" .
'    }' . "\n" .
'}' . "\n";

        return @file_put_contents($filePath, $phpCode) !== false;
    }

    public static function url(string $path = ''): string {
        $path = trim($path);
        if ($path === '' || $path === '#') {
            return $path;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:') || str_starts_with($path, 'mailto:') || str_starts_with($path, 'javascript:') || str_starts_with($path, 'tel:')) {
            return $path;
        }

        $base = rtrim(self::get('app_url', 'https://funwithcoding.free.nf'), '/');
        
        // Strip duplicate subfolder prefix if already present in path
        $parsed = parse_url($base);
        $basePath = trim($parsed['path'] ?? '', '/');
        if ($basePath !== '') {
            $prefix = '/' . $basePath;
            if (str_starts_with($path, $prefix . '/')) {
                $path = substr($path, strlen($prefix));
            } elseif ($path === $prefix) {
                $path = '';
            }
        }

        $cleanPath = ltrim($path, '/');
        return $cleanPath ? "{$base}/{$cleanPath}" : $base . '/';
    }
}
