<?php
/**
 * API: Activar/Desactivar PC
 * POST /api/admin/toggle_pc.php
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

validate_http_method('POST');

$pc_id = $_POST['pc_id'] ?? null;
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : null;

if (!$pc_id || $is_active === null) {
    json_error('Faltan parámetros requeridos');
}

try {
    $db = get_db_connection();
    
    // Verificar que la PC existe
    $stmt = $db->prepare("SELECT id, name, status FROM pcs WHERE id = :id");
    $stmt->execute(['id' => $pc_id]);
    $pc = $stmt->fetch();
    
    if (!$pc) {
        json_error('PC no encontrada', null, 404);
    }
    
    // No permitir desactivar si está en uso
    if (!$is_active && $pc['status'] === 'en_uso') {
        json_error('No se puede desactivar una PC que está en uso');
    }
    
    // Actualizar estado
    $stmt = $db->prepare("UPDATE pcs SET is_active = :is_active WHERE id = :id");
    $stmt->execute([
        'is_active' => $is_active,
        'id' => $pc_id
    ]);
    
    $action = $is_active ? 'activada' : 'desactivada';
    log_message('INFO', "PC {$action}: {$pc['name']}", ['pc_id' => $pc_id, 'user_id' => $_SESSION['user_id']]);
    
    json_success([
        'pc_id' => $pc_id,
        'is_active' => $is_active
    ], "PC {$action} correctamente");
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al cambiar estado de PC: ' . $e->getMessage());
    json_server_error('Error al cambiar estado de PC');
}
