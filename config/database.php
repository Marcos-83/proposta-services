<?php
// config/database.php
// Database connection configuration

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'proposta_services');
define('DB_USER', 'root');         // Change to your MySQL user
define('DB_PASS', '');             // Change to your MySQL password
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a PDO connection (singleton pattern)
 */
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // In production, log error and show friendly message
            die(json_encode(['error' => 'Erro de conexão com o banco de dados.']));
        }
    }
    return $pdo;
}
