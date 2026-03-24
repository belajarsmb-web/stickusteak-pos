<?php
/**
 * RestoQwen POS - Database Configuration
 * Provides database connection and helper functions
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'posreato');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// PDO connection
$pdo = null;

/**
 * Get database connection
 * @return PDO Database connection
 */
function getDbConnection() {
    global $pdo;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Execute a SELECT query and return results
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return array Query results
 */
function dbQuery($sql, $params = []) {
    try {
        $stmt = getDbConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Query error: " . $e->getMessage());
        return [];
    }
}

/**
 * Execute an INSERT, UPDATE, or DELETE query
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return int Number of affected rows or last insert ID
 */
function dbExecute($sql, $params = []) {
    try {
        $stmt = getDbConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Execute error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get last inserted ID
 * @return string Last insert ID
 */
function dbLastInsertId() {
    return getDbConnection()->lastInsertId();
}

/**
 * Begin a transaction
 * @return bool Success
 */
function dbBeginTransaction() {
    return getDbConnection()->beginTransaction();
}

/**
 * Commit a transaction
 * @return bool Success
 */
function dbCommit() {
    return getDbConnection()->commit();
}

/**
 * Rollback a transaction
 * @return bool Success
 */
function dbRollback() {
    return getDbConnection()->rollBack();
}
