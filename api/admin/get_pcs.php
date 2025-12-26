<?php
/**
 * API: Obtener lista de PCs
 * GET /api/admin/get_pcs.php
 */

session_start();
require_once __DIR__ . '/../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/response.php';
require_once INCLUDES_DIR . '/auth.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!check_session()) {
    json_unauthorized('Sesión no válida');
}

// Validar método
validate_http_method('GET');

try {
    $db = get_db_connection();
    
    $stmt = $db->query("
        SELECT 
            p.id,
            p.name,
            p.status,
            p.location,
            p.ip_address,
            p.last_heartbeat,
            s.id as session_id,
            s.client_name,
            s.remaining_time,
            s.assigned_time,
            s.start_time,
            s.status as session_status,
            TIMESTAMPDIFF(SECOND, s.start_time, NOW()) as elapsed_seconds
        FROM pcs p
        LEFT JOIN sessions s ON p.id = s.pc_id AND s.status IN ('activa', 'pausada')
        WHERE p.is_active = 1
        ORDER BY p.name
    ");
    
    $pcs = $stmt->fetchAll();
    
    // Calcular tiempo restante actualizado
    foreach ($pcs as &$pc) {
        if ($pc['session_id'] && $pc['session_status'] === 'activa') {
            $elapsed = $pc['elapsed_seconds'];
            $remaining = max(0, $pc['assigned_time'] - $elapsed);
            $pc['remaining_time'] = $remaining;
            
            // Si el tiempo se agotó, finalizar sesión
            if ($remaining <= 0) {
                $update_stmt = $db->prepare("
                    UPDATE sessions 
                    SET status = 'finalizada', end_time = NOW(), remaining_time = 0
                    WHERE id = :id
                ");
                $update_stmt->execute(['id' => $pc['session_id']]);
                
                $update_pc_stmt = $db->prepare("UPDATE pcs SET status = 'disponible' WHERE id = :id");
                $update_pc_stmt->execute(['id' => $pc['id']]);
                
                $pc['status'] = 'disponible';
                $pc['session_id'] = null;
            }
        }
    }
    
    json_success($pcs, 'PCs obtenidas correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener PCs: ' . $e->getMessage());
    json_server_error('Error al obtener lista de PCs');
}
