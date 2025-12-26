<?php
/**
 * API: Asignar tiempo a una PC
 * POST /api/admin/assign_time.php
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
validate_http_method('POST');

// Obtener datos
$pc_id = $_POST['pc_id'] ?? null;
$time_minutes = $_POST['time_minutes'] ?? null;
$client_name = $_POST['client_name'] ?? null;

// Validar parámetros
if (!$pc_id || !$time_minutes) {
    json_error('Faltan parámetros requeridos: pc_id, time_minutes');
}

if (!is_numeric($pc_id) || !is_numeric($time_minutes)) {
    json_error('Parámetros inválidos');
}

$time_minutes = (int)$time_minutes;
if ($time_minutes < 1) {
    json_error('El tiempo debe ser mayor a 0 minutos');
}

try {
    $db = get_db_connection();
    
    // Verificar que la PC existe y está disponible
    $stmt = $db->prepare("SELECT id, name, status FROM pcs WHERE id = :id AND is_active = 1");
    $stmt->execute(['id' => $pc_id]);
    $pc = $stmt->fetch();
    
    if (!$pc) {
        json_error('PC no encontrada o inactiva', null, 404);
    }
    
    if ($pc['status'] !== 'disponible') {
        json_error('La PC no está disponible. Estado actual: ' . $pc['status']);
    }
    
    // Verificar que no tenga sesión activa
    $stmt = $db->prepare("SELECT id FROM sessions WHERE pc_id = :pc_id AND status IN ('activa', 'pausada')");
    $stmt->execute(['pc_id' => $pc_id]);
    if ($stmt->fetch()) {
        json_error('La PC ya tiene una sesión activa');
    }
    
    // Convertir minutos a segundos
    $time_seconds = $time_minutes * 60;
    
    // Iniciar transacción
    $db->beginTransaction();
    
    // Crear sesión
    $stmt = $db->prepare("
        INSERT INTO sessions (pc_id, client_name, assigned_time, remaining_time, start_time, status, created_by)
        VALUES (:pc_id, :client_name, :assigned_time, :remaining_time, NOW(), 'activa', :created_by)
    ");
    
    $stmt->execute([
        'pc_id' => $pc_id,
        'client_name' => $client_name,
        'assigned_time' => $time_seconds,
        'remaining_time' => $time_seconds,
        'created_by' => $_SESSION['user_id']
    ]);
    
    $session_id = $db->lastInsertId();
    
    // Actualizar estado de la PC
    $stmt = $db->prepare("UPDATE pcs SET status = 'en_uso' WHERE id = :id");
    $stmt->execute(['id' => $pc_id]);
    
    // Registrar en time_logs
    $stmt = $db->prepare("
        INSERT INTO time_logs (session_id, action, time_after, time_change, created_by)
        VALUES (:session_id, 'asignar', :time_after, :time_change, :created_by)
    ");
    
    $stmt->execute([
        'session_id' => $session_id,
        'time_after' => $time_seconds,
        'time_change' => $time_seconds,
        'created_by' => $_SESSION['user_id']
    ]);
    
    $db->commit();
    
    log_message('INFO', "Tiempo asignado a PC {$pc['name']}: {$time_minutes} minutos", [
        'pc_id' => $pc_id,
        'session_id' => $session_id,
        'user_id' => $_SESSION['user_id']
    ]);
    
    json_success([
        'session_id' => $session_id,
        'pc_id' => $pc_id,
        'pc_name' => $pc['name'],
        'assigned_time' => $time_seconds,
        'client_name' => $client_name
    ], 'Tiempo asignado correctamente');
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    log_message('ERROR', 'Error al asignar tiempo: ' . $e->getMessage());
    json_server_error('Error al asignar tiempo');
}
