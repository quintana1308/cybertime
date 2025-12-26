<?php
/**
 * API: Heartbeat del cliente (señal de vida)
 * POST /api/client/heartbeat.php
 */

require_once __DIR__ . '/../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/response.php';

header('Content-Type: application/json');

validate_http_method('POST');

$pc_id = $_POST['pc_id'] ?? null;

if (!$pc_id) {
    json_error('Falta parámetro requerido: pc_id');
}

try {
    $db = get_db_connection();
    
    // Actualizar último heartbeat
    $stmt = $db->prepare("UPDATE pcs SET last_heartbeat = NOW() WHERE id = :id");
    $stmt->execute(['id' => $pc_id]);
    
    if ($stmt->rowCount() === 0) {
        json_error('PC no encontrada', null, 404);
    }
    
    json_success(['pc_id' => $pc_id], 'Heartbeat registrado');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error en heartbeat: ' . $e->getMessage());
    json_server_error('Error al registrar heartbeat');
}
