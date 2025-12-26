<?php
/**
 * API: Agregar tiempo adicional a una sesión
 * POST /api/admin/add_time.php
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
$time_minutes = $_POST['time_minutes'] ?? null;

if (!$session_id || !$time_minutes) {
    json_error('Faltan parámetros requeridos: session_id, time_minutes');
}

$time_minutes = (int)$time_minutes;
if ($time_minutes < 1) {
    json_error('El tiempo debe ser mayor a 0 minutos');
}

try {
    $db = get_db_connection();
    
    // Obtener sesión
    $stmt = $db->prepare("
        SELECT s.*, p.name as pc_name
        FROM sessions s
        INNER JOIN pcs p ON s.pc_id = p.id
        WHERE s.id = :id AND s.status IN ('activa', 'pausada')
    ");
    $stmt->execute(['id' => $session_id]);
    $session = $stmt->fetch();
    
    if (!$session) {
        json_error('Sesión no encontrada o ya finalizada', null, 404);
    }
    
    $time_seconds = $time_minutes * 60;
    $time_before = $session['remaining_time'];
    $time_after = $time_before + $time_seconds;
    
    $db->beginTransaction();
    
    // Actualizar sesión
    $stmt = $db->prepare("
        UPDATE sessions 
        SET remaining_time = remaining_time + :time_add,
            assigned_time = assigned_time + :time_add
        WHERE id = :id
    ");
    $stmt->execute([
        'time_add' => $time_seconds,
        'id' => $session_id
    ]);
    
    // Registrar en time_logs
    $stmt = $db->prepare("
        INSERT INTO time_logs (session_id, action, time_before, time_after, time_change, created_by)
        VALUES (:session_id, 'agregar', :time_before, :time_after, :time_change, :created_by)
    ");
    
    $stmt->execute([
        'session_id' => $session_id,
        'time_before' => $time_before,
        'time_after' => $time_after,
        'time_change' => $time_seconds,
        'created_by' => $_SESSION['user_id']
    ]);
    
    $db->commit();
    
    log_message('INFO', "Tiempo agregado a {$session['pc_name']}: {$time_minutes} minutos", [
        'session_id' => $session_id,
        'user_id' => $_SESSION['user_id']
    ]);
    
    json_success([
        'session_id' => $session_id,
        'time_added' => $time_seconds,
        'new_remaining_time' => $time_after
    ], 'Tiempo agregado correctamente');
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    log_message('ERROR', 'Error al agregar tiempo: ' . $e->getMessage());
    json_server_error('Error al agregar tiempo');
}
