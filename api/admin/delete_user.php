<?php
/**
 * API: Eliminar usuario
 * POST /api/admin/delete_user.php
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
    json_unauthorized('Solo administradores pueden eliminar usuarios');
}

validate_http_method('POST');

$user_id = $_POST['user_id'] ?? null;

if (!$user_id) {
    json_error('Falta parámetro requerido: user_id');
}

// No permitir eliminarse a sí mismo
if ($user_id == $_SESSION['user_id']) {
    json_error('No puedes eliminar tu propia cuenta');
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
    
    // Verificar si tiene sesiones asociadas
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM sessions WHERE created_by = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $sessions_count = $stmt->fetch()['total'];
    
    if ($sessions_count > 0) {
        json_error('No se puede eliminar un usuario con sesiones registradas. Desactívalo en su lugar.');
    }
    
    // Eliminar usuario
    $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    
    log_message('INFO', "Usuario eliminado: {$user['username']}", ['user_id' => $user_id, 'deleted_by' => $_SESSION['user_id']]);
    
    json_success([
        'user_id' => $user_id
    ], 'Usuario eliminado correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al eliminar usuario: ' . $e->getMessage());
    json_server_error('Error al eliminar usuario');
}
