<?php
/**
 * API: Activar/Desactivar tarifa
 * POST /api/admin/toggle_pricing.php
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

$pricing_id = $_POST['pricing_id'] ?? null;
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : null;

if (!$pricing_id || $is_active === null) {
    json_error('Faltan parámetros requeridos');
}

try {
    $db = get_db_connection();
    
    // Verificar que la tarifa existe
    $stmt = $db->prepare("SELECT id, name FROM pricing WHERE id = :id");
    $stmt->execute(['id' => $pricing_id]);
    $pricing = $stmt->fetch();
    
    if (!$pricing) {
        json_error('Tarifa no encontrada', null, 404);
    }
    
    // Actualizar estado
    $stmt = $db->prepare("UPDATE pricing SET is_active = :is_active WHERE id = :id");
    $stmt->execute([
        'is_active' => $is_active,
        'id' => $pricing_id
    ]);
    
    $action = $is_active ? 'activada' : 'desactivada';
    log_message('INFO', "Tarifa {$action}: {$pricing['name']}", ['pricing_id' => $pricing_id, 'user_id' => $_SESSION['user_id']]);
    
    json_success([
        'pricing_id' => $pricing_id,
        'is_active' => $is_active
    ], "Tarifa {$action} correctamente");
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al cambiar estado de tarifa: ' . $e->getMessage());
    json_server_error('Error al cambiar estado de tarifa');
}
