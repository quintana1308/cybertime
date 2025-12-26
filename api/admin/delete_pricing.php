<?php
/**
 * API: Eliminar tarifa
 * POST /api/admin/delete_pricing.php
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
    json_unauthorized('Solo administradores pueden eliminar tarifas');
}

validate_http_method('POST');

$pricing_id = $_POST['pricing_id'] ?? null;

if (!$pricing_id) {
    json_error('Falta parámetro requerido: pricing_id');
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
    
    // Eliminar tarifa
    $stmt = $db->prepare("DELETE FROM pricing WHERE id = :id");
    $stmt->execute(['id' => $pricing_id]);
    
    log_message('INFO', "Tarifa eliminada: {$pricing['name']}", ['pricing_id' => $pricing_id, 'user_id' => $_SESSION['user_id']]);
    
    json_success([
        'pricing_id' => $pricing_id
    ], 'Tarifa eliminada correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al eliminar tarifa: ' . $e->getMessage());
    json_server_error('Error al eliminar tarifa');
}
