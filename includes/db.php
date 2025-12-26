<?php
/**
 * CYBERTIME - Conexión a Base de Datos
 * Manejo de conexión PDO a MariaDB
 */

require_once __DIR__ . '/../config.php';

/**
 * Obtener conexión a la base de datos
 * 
 * @return PDO Objeto de conexión PDO
 * @throws Exception Si falla la conexión
 */
function get_db_connection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            error_log("Error de conexión a base de datos: " . $e->getMessage());
            throw new Exception("Error al conectar con la base de datos");
        }
    }
    
    return $pdo;
}

/**
 * Cerrar conexión a la base de datos
 */
function close_db_connection() {
    $pdo = null;
}
