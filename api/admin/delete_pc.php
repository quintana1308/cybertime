<?php
/**
 * API: Eliminar PC
 * POST /api/admin/delete_pc.php
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

// Solo administradores pueden eliminar PCs
if (!is_admin()) {
    json_unauthorized('Solo administradores pueden eliminar PCs');
}

validate_http_method('POST');

$pc_id = $_POST['pc_id'] ?? null;

if (!$pc_id) {
    json_error('Falta parámetro requerido: pc_id');
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
    
    // No permitir eliminar si está en uso
    if ($pc['status'] === 'en_uso') {
        json_error('No se puede eliminar una PC que está en uso');
    }
    
    // Verificar si tiene sesiones asociadas
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM sessions WHERE pc_id = :pc_id");
    $stmt->execute(['pc_id' => $pc_id]);
    $sessions_count = $stmt->fetch()['total'];
    
    if ($sessions_count > 0) {
        json_error('No se puede eliminar una PC con sesiones registradas. Desactívala en su lugar.');
    }
    
    // Eliminar PC
    $stmt = $db->prepare("DELETE FROM pcs WHERE id = :id");
    $stmt->execute(['id' => $pc_id]);
    
    log_message('INFO', "PC eliminada: {$pc['name']}", ['pc_id' => $pc_id, 'user_id' => $_SESSION['user_id']]);
    
    json_success([
        'pc_id' => $pc_id
    ], 'PC eliminada correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al eliminar PC: ' . $e->getMessage());
    json_server_error('Error al eliminar PC');
}
