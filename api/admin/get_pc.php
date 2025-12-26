<?php
/**
 * API: Obtener detalles de una PC
 * GET /api/admin/get_pc.php?id=1
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

$pc_id = $_GET['id'] ?? null;

if (!$pc_id) {
    json_error('Falta parámetro requerido: id');
}

try {
    $db = get_db_connection();
    
    $stmt = $db->prepare("
        SELECT 
            id, name, ip_address, mac_address, status, location,
            specifications, notes, is_active, last_heartbeat, created_at, updated_at
        FROM pcs
        WHERE id = :id
    ");
    
    $stmt->execute(['id' => $pc_id]);
    $pc = $stmt->fetch();
    
    if (!$pc) {
        json_error('PC no encontrada', null, 404);
    }
    
    json_success($pc, 'PC obtenida correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener PC: ' . $e->getMessage());
    json_server_error('Error al obtener PC');
}
