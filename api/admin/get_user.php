<?php
/**
 * API: Obtener detalles de un usuario
 * GET /api/admin/get_user.php?id=1
 */

session_start();
require_once __DIR__ . '/../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/response.php';
require_once INCLUDES_DIR . '/auth.php';

header('Content-Type: application/json');

if (!check_session()) {
    json_unauthorized('Sesión no válida');
}

if (!is_admin()) {
    json_unauthorized('Solo administradores pueden ver usuarios');
}

validate_http_method('GET');

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    json_error('Falta parámetro requerido: id');
}

try {
    $db = get_db_connection();
    
    $stmt = $db->prepare("
        SELECT id, username, full_name, email, role, is_active, last_login, created_at
        FROM users
        WHERE id = :id
    ");
    
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        json_error('Usuario no encontrado', null, 404);
    }
    
    json_success($user, 'Usuario obtenido correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener usuario: ' . $e->getMessage());
    json_server_error('Error al obtener usuario');
}
