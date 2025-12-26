<?php
/**
 * API: Obtener estado de la PC cliente
 * GET /api/client/status.php?pc_id=1
 */

require_once __DIR__ . '/../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/response.php';

header('Content-Type: application/json');

validate_http_method('GET');

$pc_id = $_GET['pc_id'] ?? null;

if (!$pc_id) {
    json_error('Falta parámetro requerido: pc_id');
}

try {
    $db = get_db_connection();
    
    // Obtener PC y sesión activa
    $stmt = $db->prepare("
        SELECT 
            p.id,
            p.name,
            p.status,
            s.id as session_id,
            s.client_name,
            s.assigned_time,
            s.remaining_time,
            s.start_time,
            s.status as session_status,
            TIMESTAMPDIFF(SECOND, s.start_time, NOW()) as elapsed_seconds
        FROM pcs p
        LEFT JOIN sessions s ON p.id = s.pc_id AND s.status IN ('activa', 'pausada')
        WHERE p.id = :id AND p.is_active = 1
    ");
    
    $stmt->execute(['id' => $pc_id]);
    $pc = $stmt->fetch();
    
    if (!$pc) {
        json_error('PC no encontrada', null, 404);
    }
    
    $response = [
        'pc_id' => $pc['id'],
        'pc_name' => $pc['name'],
        'status' => $pc['status'],
        'is_locked' => true,
        'session_id' => null,
        'remaining_time' => 0,
        'assigned_time' => 0,
        'client_name' => null
    ];
    
    // Si tiene sesión activa
    if ($pc['session_id'] && $pc['session_status'] === 'activa') {
        $elapsed = $pc['elapsed_seconds'];
        $remaining = max(0, $pc['assigned_time'] - $elapsed);
        
        // Si el tiempo se agotó, finalizar sesión
        if ($remaining <= 0) {
            $update_stmt = $db->prepare("
                UPDATE sessions 
                SET status = 'finalizada', end_time = NOW(), remaining_time = 0
                WHERE id = :id
            ");
            $update_stmt->execute(['id' => $pc['session_id']]);
            
            $update_pc_stmt = $db->prepare("UPDATE pcs SET status = 'disponible' WHERE id = :id");
            $update_pc_stmt->execute(['id' => $pc_id]);
            
            $response['status'] = 'disponible';
            $response['is_locked'] = true;
        } else {
            // Actualizar tiempo restante en BD
            $update_stmt = $db->prepare("UPDATE sessions SET remaining_time = :remaining WHERE id = :id");
            $update_stmt->execute(['remaining' => $remaining, 'id' => $pc['session_id']]);
            
            $response['is_locked'] = false;
            $response['session_id'] = $pc['session_id'];
            $response['remaining_time'] = $remaining;
            $response['assigned_time'] = $pc['assigned_time'];
            $response['client_name'] = $pc['client_name'];
        }
    } elseif ($pc['session_id'] && $pc['session_status'] === 'pausada') {
        $response['status'] = 'pausada';
        $response['is_locked'] = true;
        $response['session_id'] = $pc['session_id'];
        $response['remaining_time'] = $pc['remaining_time'];
        $response['assigned_time'] = $pc['assigned_time'];
    }
    
    json_success($response, 'Estado obtenido correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener estado de PC: ' . $e->getMessage());
    json_server_error('Error al obtener estado');
}
