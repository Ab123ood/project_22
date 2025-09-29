<?php
/**
 * Database Connection Manager
 * Handles PDO database connections with proper error handling
 */
class Database
{
    private static ?PDO $pdo = null;

    /**
     * Get database connection instance
     * @return PDO
     * @throws Exception
     */
    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $cfg = $GLOBALS['config']['db'] ?? [];
        $host = $cfg['host'] ?? 'localhost';
        $port = (int)($cfg['port'] ?? 3306);
        $name = $cfg['name'] ?? '';
        $charset = $cfg['charset'] ?? 'utf8mb4';
        $user = $cfg['user'] ?? '';
        $pass = $cfg['pass'] ?? '';

        if (empty($name) || empty($user)) {
            throw new Exception('Database configuration is incomplete');
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE {$charset}_unicode_ci"
        ];

        try {
            self::$pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed. Please check your configuration.');
        }

        return self::$pdo;
    }

    /**
     * Execute a prepared statement
     * @param string $sql SQL query
     * @param array $params Parameters for the query
     * @return PDOStatement
     * @throws Exception
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = self::connection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database query failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw new Exception('Database query failed');
        }
    }

    /**
     * Begin a database transaction
     * @throws Exception
     */
    public static function begin(): void
    {
        try {
            self::connection()->beginTransaction();
        } catch (PDOException $e) {
            error_log('Failed to begin transaction: ' . $e->getMessage());
            throw new Exception('Failed to begin transaction');
        }
    }

    /**
     * Commit the current transaction
     * @throws Exception
     */
    public static function commit(): void
    {
        try {
            if (self::connection()->inTransaction()) {
                self::connection()->commit();
            }
        } catch (PDOException $e) {
            error_log('Failed to commit transaction: ' . $e->getMessage());
            throw new Exception('Failed to commit transaction');
        }
    }

    /**
     * Rollback the current transaction
     * @throws Exception
     */
    public static function rollback(): void
    {
        try {
            if (self::connection()->inTransaction()) {
                self::connection()->rollBack();
            }
        } catch (PDOException $e) {
            error_log('Failed to rollback transaction: ' . $e->getMessage());
            throw new Exception('Failed to rollback transaction');
        }
    }

    /**
     * Check if currently in a transaction
     * @return bool
     */
    public static function inTransaction(): bool
    {
        try {
            return self::connection()->inTransaction();
        } catch (Exception $e) {
            return false;
        }
    }
}
