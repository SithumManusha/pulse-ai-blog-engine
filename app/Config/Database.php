<?php
namespace App\Config;

use App\Helpers\Env;
use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): ?PDO {
        if (self::$instance === null) {
            $connection = Env::get('DB_CONNECTION', 'sqlite');

            if ($connection === 'sqlite') {
                $dbPath = __DIR__ . '/../../database/blog.sqlite';
                try {
                    self::$instance = new PDO("sqlite:" . $dbPath, null, null, [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);

                    // Verify posts table exists; if not, auto-seed database seamlessly
                    $check = self::$instance->query("SELECT name FROM sqlite_master WHERE type='table' AND name='posts'")->fetch();
                    if (!$check) {
                        require_once __DIR__ . '/../../database/init_sqlite.php';
                    }

                    return self::$instance;
                } catch (PDOException $e) {
                    error_log("SQLite connection error: " . $e->getMessage());
                }
            }

            // Fallback or explicit MySQL
            $host = Env::get('DB_HOST', '127.0.0.1');
            $port = Env::get('DB_PORT', '3306');
            $db   = Env::get('DB_NAME', 'ai_blog_app');
            $user = Env::get('DB_USER', 'root');
            $pass = Env::get('DB_PASS', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // If MySQL fails, fallback to SQLite automatically so app never crashes!
                $dbPath = __DIR__ . '/../../database/blog.sqlite';
                if (file_exists($dbPath)) {
                    try {
                        self::$instance = new PDO("sqlite:" . $dbPath, null, null, [
                            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        ]);
                        return self::$instance;
                    } catch (PDOException $e2) {
                        error_log("Fallback SQLite error: " . $e2->getMessage());
                    }
                }
                error_log("Database connection error: " . $e->getMessage());
                return null;
            }
        }

        return self::$instance;
    }
}
