<?php
/**
 * API: Obtener tarifas de precios
 * GET /api/admin/get_pricing.php
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

validate_http_method('GET');

try {
    $db = get_db_connection();
    
    $stmt = $db->query("
        SELECT id, name, minutes, price, is_active, display_order
        FROM pricing
        WHERE is_active = 1
        ORDER BY display_order, minutes
    ");
    
    $pricing = $stmt->fetchAll();
    
    json_success($pricing, 'Tarifas obtenidas correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener tarifas: ' . $e->getMessage());
    json_server_error('Error al obtener tarifas');
}
