<?php
/**
 * API: Cambiar contraseña de usuario
 * POST /api/admin/change_password.php
 */

session_start();
require_once __DIR__ . '/../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/response.php';
require_once INCLUDES_DIR . '/auth.php';

header('Content-Type: application/json');

if (!check_session()) {
    json_unauthorized('Sesión no válida');
}

if (!is_admin()) {
    json_unauthorized('Solo administradores pueden cambiar contraseñas');
}

validate_http_method('POST');

$user_id = $_POST['user_id'] ?? null;
$new_password = $_POST['new_password'] ?? '';

if (!$user_id || empty($new_password)) {
    json_error('Faltan parámetros requeridos');
}

if (strlen($new_password) < 6) {
    json_error('La contraseña debe tener al menos 6 caracteres');
}

try {
    $db = get_db_connection();
    
    // Verificar que el usuario existe
    $stmt = $db->prepare("SELECT id, username FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        json_error('Usuario no encontrado', null, 404);
    }
    
    // Hash de la nueva contraseña
    $password_hash = hash_password($new_password);
    
    // Actualizar contraseña
    $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
    $stmt->execute([
        'password' => $password_hash,
        'id' => $user_id
    ]);
    
    log_message('INFO', "Contraseña cambiada para usuario: {$user['username']}", ['user_id' => $user_id, 'changed_by' => $_SESSION['user_id']]);
    
    json_success([
        'user_id' => $user_id
    ], 'Contraseña cambiada correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al cambiar contraseña: ' . $e->getMessage());
    json_server_error('Error al cambiar contraseña');
}
