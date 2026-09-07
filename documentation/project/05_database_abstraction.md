# 05. Database Abstraction Layer (`Database.php`)

## File Location & Metadata
- **File Path**: [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php)
- **Primary Role**: PDO Singleton Connection Manager, Auto-Healing Database & Table Creator, Live Migration Runner, and Prepared Statement Executor.
- **Dependencies**: [`database/schema.sql`](file:///c:/xampp/htdocs/techscout.n/database/schema.sql), [`database/seeds.sql`](file:///c:/xampp/htdocs/techscout.n/database/seeds.sql), [`database/database_upgrade_v2.sql`](file:///c:/xampp/htdocs/techscout.n/database/database_upgrade_v2.sql).

---

## Architectural Principles & Design Patterns

### Singleton Pattern Implementation
To prevent connection exhaustion and overhead, `Database.php` enforces the Singleton design pattern. A single PDO instance is instantiated and reused across the entire HTTP request lifecycle.

```php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct() {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $dbname = getenv('DB_NAME') ?: 'techscout_db';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';

        // Step 1: Connect to MySQL Server (without database selected)
        $dsnNoDb = "mysql:host={$host};port={$port};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $tempPdo = new PDO($dsnNoDb, $user, $pass, $options);
            // Auto-Create Database if missing
            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            die("Database Connection Error: " . $e->getMessage());
        }

        // Step 2: Connect to target Database
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
        $this->connection = new PDO($dsn, $user, $pass, $options);

        // Force Session Timezone to PST (+05:00)
        $this->connection->exec("SET time_zone = '+05:00'");

        // Step 3: Self-Healing Table Check & Auto-Migration
        $this->ensureTablesExist();
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}
```

---

## Self-Healing & Migration Engine Mechanics

### 1. Missing Database Auto-Creation
When TechScout is deployed on a fresh XAMPP installation, the MySQL database `techscout_db` may not yet exist. `Database.php` connects to the MySQL server root, issues `CREATE DATABASE IF NOT EXISTS`, and seamlessly connects without crashing.

### 2. Schema Auto-Seeding (`schema.sql` & `seeds.sql`)
During initial startup, `Database.php` checks if critical core tables (such as `contents`, `users`, `site_settings`) exist. If absent:
1. Parses `database/schema.sql` into individual SQL statements and executes DDL table creations.
2. Parses `database/seeds.sql` to populate default categories, topics, super-admin accounts, and site settings.

### 3. Live Incremental Schema Migrations
`Database.php` maintains schema version consistency by executing incremental SQL upgrade scripts (`database_upgrade_v2.sql`, `help_legal_upgrade.sql`, `mail_tracking_upgrade.sql`).

```php
private function ensureTablesExist(): void {
    if (!$this->tableExists('contents')) {
        $schemaSql = file_get_contents(__DIR__ . '/../../database/schema.sql');
        $this->connection->exec($schemaSql);
        
        if (file_exists(__DIR__ . '/../../database/seeds.sql')) {
            $seedsSql = file_get_contents(__DIR__ . '/../../database/seeds.sql');
            $this->connection->exec($seedsSql);
        }
    }

    // Run incremental migrations
    if (!$this->tableExists('co_authors')) {
        if (file_exists(__DIR__ . '/../../database/database_upgrade_v2.sql')) {
            $v2Sql = file_get_contents(__DIR__ . '/../../database/database_upgrade_v2.sql');
            $this->connection->exec($v2Sql);
        }
    }
}
```

---

## Complete Method Reference

| Method Signature | Return Type | Description & Purpose |
| :--- | :--- | :--- |
| `getInstance()` | `Database` | Returns singleton Database instance. |
| `getConnection()` | `PDO` | Returns underlying PDO connection object. |
| `query(string $sql, array $params = [])` | `PDOStatement` | Prepares and executes SQL statement. |
| `fetch(string $sql, array $params = [])` | `array\|false` | Fetches a single record as associative array. |
| `fetchAll(string $sql, array $params = [])` | `array` | Fetches all matching records as array of associative arrays. |
| `execute(string $sql, array $params = [])` | `bool` | Executes INSERT, UPDATE, or DELETE query. |
| `lastInsertId()` | `string` | Returns auto-increment ID of last inserted row. |
| `beginTransaction()` | `bool` | Starts PDO database transaction. |
| `commit()` | `bool` | Commits active transaction. |
| `rollBack()` | `bool` | Rolls back active transaction on error. |
| `tableExists(string $tableName)` | `bool` | Checks if a table exists in target database. |

---

## Detailed QA Test Suite for Database Layer

### Test Case 1: Auto-Healing Table Recreation
- **QA Test ID**: `TC-DB-001`
- **Target Function**: `Database::ensureTablesExist()`
- **Preconditions**: Local XAMPP MySQL running.
- **Execution Steps**:
  1. Open phpMyAdmin or MySQL CLI: `mysql -u root -e "DROP TABLE techscout_db.co_authors;"`
  2. Reload any page: `http://localhost/techscout.n/`
  3. Verify table recreation in MySQL: `SHOW TABLES LIKE 'co_authors';`
- **Expected Result**: Table `co_authors` automatically recreated by `Database.php` migration check.
- **Assertion**: `tableExists('co_authors') === true`.

### Test Case 2: Prepared Statement Parameter Binding & SQL Injection Defense
- **QA Test ID**: `TC-DB-002`
- **Target Function**: `Database::fetch()`
- **Execution Steps**:
  1. Pass malicious payload parameter: `1' OR '1'='1`
     ```php
     $user = $db->fetch("SELECT * FROM users WHERE email = ?", ["' OR '1'='1"]);
     ```
- **Expected Result**: PDO prepared statements escape payload as literal string, returning `false` (no match found). No SQL Syntax Error or unauthorized bypass occurs.
