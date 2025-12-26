<?php
/**
 * API: Activar/Desactivar usuario
 * POST /api/admin/toggle_user.php
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
    json_unauthorized('Solo administradores pueden activar/desactivar usuarios');
}

validate_http_method('POST');

$user_id = $_POST['user_id'] ?? null;
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : null;

if (!$user_id || $is_active === null) {
    json_error('Faltan parámetros requeridos');
}

// No permitir desactivarse a sí mismo
if ($user_id == $_SESSION['user_id'] && !$is_active) {
    json_error('No puedes desactivar tu propia cuenta');
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
    
    // Actualizar estado
    $stmt = $db->prepare("UPDATE users SET is_active = :is_active WHERE id = :id");
    $stmt->execute([
        'is_active' => $is_active,
        'id' => $user_id
    ]);
    
    $action = $is_active ? 'activado' : 'desactivado';
    log_message('INFO', "Usuario {$action}: {$user['username']}", ['user_id' => $user_id, 'changed_by' => $_SESSION['user_id']]);
    
    json_success([
        'user_id' => $user_id,
        'is_active' => $is_active
    ], "Usuario {$action} correctamente");
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al cambiar estado de usuario: ' . $e->getMessage());
    json_server_error('Error al cambiar estado de usuario');
}
