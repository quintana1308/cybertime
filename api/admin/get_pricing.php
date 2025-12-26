<?php
/**
 * API: Obtener tarifas de precios
 * GET /api/admin/get_pricing.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

try {
    require_once __DIR__ . '/../../config.php';
    require_once INCLUDES_DIR . '/db.php';
    require_once INCLUDES_DIR . '/response.php';
    require_once INCLUDES_DIR . '/auth.php';
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error al cargar archivos: ' . $e->getMessage()]);
    exit;
}

header('Content-Type: application/json');

try {
    if (!check_session()) {
        json_unauthorized('Sesión no válida');
        exit;
    }

    validate_http_method('GET');

    $pricing_id = $_GET['id'] ?? null;

    $db = get_db_connection();
    
    // Si se proporciona ID, obtener una tarifa específica
    if ($pricing_id) {
        $stmt = $db->prepare("
            SELECT id, name, minutes, price, is_active, display_order
            FROM pricing
            WHERE id = :id
        ");
        $stmt->execute(['id' => $pricing_id]);
        $pricing = $stmt->fetch();
        
        if (!$pricing) {
            json_error('Tarifa no encontrada', null, 404);
            exit;
        }
        
        json_success($pricing, 'Tarifa obtenida correctamente');
    } else {
        // Obtener todas las tarifas activas
        $stmt = $db->query("
            SELECT id, name, minutes, price, is_active, display_order
            FROM pricing
            WHERE is_active = 1
            ORDER BY display_order, minutes
        ");
        
        $pricing = $stmt->fetchAll();
        
        json_success($pricing, 'Tarifas obtenidas correctamente');
    }
    
} catch (Exception $e) {
    if (function_exists('log_message')) {
        log_message('ERROR', 'Error al obtener tarifas: ' . $e->getMessage());
    }
    json_server_error('Error al obtener tarifas: ' . $e->getMessage());
}
