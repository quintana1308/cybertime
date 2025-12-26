<?php
/**
 * API: Pausar tiempo de una sesión
 * POST /api/admin/pause_time.php
 */

session_start();
require_once __DIR__ . '/../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/response.php';
require_once INCLUDES_DIR . '/auth.php';

header('Content-Type: application/json');

if (!check_session()) {
    json_unauthorized('Sesión no válida');
}

validate_http_method('POST');

$session_id = $_POST['session_id'] ?? null;

if (!$session_id) {
    json_error('Falta parámetro requerido: session_id');
}

try {
    $db = get_db_connection();
    
    $stmt = $db->prepare("
        SELECT s.*, p.name as pc_name
        FROM sessions s
        INNER JOIN pcs p ON s.pc_id = p.id
        WHERE s.id = :id AND s.status = 'activa'
    ");
    $stmt->execute(['id' => $session_id]);
    $session = $stmt->fetch();
    
    if (!$session) {
        json_error('Sesión no encontrada o no está activa', null, 404);
    }
    
    // Calcular tiempo restante actual
    $elapsed = time() - strtotime($session['start_time']);
    $remaining = max(0, $session['assigned_time'] - $elapsed);
    
    $db->beginTransaction();
    
    // Pausar sesión
    $stmt = $db->prepare("
        UPDATE sessions 
        SET status = 'pausada',
            pause_time = NOW(),
            remaining_time = :remaining_time
        WHERE id = :id
    ");
    $stmt->execute([
        'remaining_time' => $remaining,
        'id' => $session_id
    ]);
    
    // Actualizar PC
    $stmt = $db->prepare("UPDATE pcs SET status = 'pausada' WHERE id = :id");
    $stmt->execute(['id' => $session['pc_id']]);
    
    // Registrar en time_logs
    $stmt = $db->prepare("
        INSERT INTO time_logs (session_id, action, time_before, time_after, created_by)
        VALUES (:session_id, 'pausar', :time_before, :time_after, :created_by)
    ");
    
    $stmt->execute([
        'session_id' => $session_id,
        'time_before' => $session['remaining_time'],
        'time_after' => $remaining,
        'created_by' => $_SESSION['user_id']
    ]);
    
    $db->commit();
    
    log_message('INFO', "Sesión pausada: {$session['pc_name']}", ['session_id' => $session_id]);
    
    json_success([
        'session_id' => $session_id,
        'remaining_time' => $remaining
    ], 'Sesión pausada correctamente');
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    log_message('ERROR', 'Error al pausar sesión: ' . $e->getMessage());
    json_server_error('Error al pausar sesión');
}
