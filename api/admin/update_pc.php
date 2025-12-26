<?php
/**
 * API: Actualizar PC existente
 * POST /api/admin/update_pc.php
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

$pc_id = $_POST['pc_id'] ?? null;
$name = sanitize_input($_POST['name'] ?? '');
$ip_address = sanitize_input($_POST['ip_address'] ?? '');
$mac_address = sanitize_input($_POST['mac_address'] ?? '');
$location = sanitize_input($_POST['location'] ?? '');
$specifications = sanitize_input($_POST['specifications'] ?? '');
$notes = sanitize_input($_POST['notes'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (!$pc_id || empty($name)) {
    json_error('Faltan parámetros requeridos');
}

try {
    $db = get_db_connection();
    
    // Verificar que la PC existe
    $stmt = $db->prepare("SELECT id, name FROM pcs WHERE id = :id");
    $stmt->execute(['id' => $pc_id]);
    $pc = $stmt->fetch();
    
    if (!$pc) {
        json_error('PC no encontrada', null, 404);
    }
    
    // Verificar si el nombre ya existe en otra PC
    $stmt = $db->prepare("SELECT id FROM pcs WHERE name = :name AND id != :id");
    $stmt->execute(['name' => $name, 'id' => $pc_id]);
    if ($stmt->fetch()) {
        json_error('Ya existe otra PC con ese nombre');
    }
    
    // Actualizar PC
    $stmt = $db->prepare("
        UPDATE pcs 
        SET name = :name,
            ip_address = :ip_address,
            mac_address = :mac_address,
            location = :location,
            specifications = :specifications,
            notes = :notes,
            is_active = :is_active
        WHERE id = :id
    ");
    
    $stmt->execute([
        'name' => $name,
        'ip_address' => $ip_address ?: null,
        'mac_address' => $mac_address ?: null,
        'location' => $location ?: null,
        'specifications' => $specifications ?: null,
        'notes' => $notes ?: null,
        'is_active' => $is_active,
        'id' => $pc_id
    ]);
    
    log_message('INFO', "PC actualizada: {$name}", ['pc_id' => $pc_id, 'user_id' => $_SESSION['user_id']]);
    
    json_success([
        'pc_id' => $pc_id,
        'name' => $name
    ], 'PC actualizada correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al actualizar PC: ' . $e->getMessage());
    json_server_error('Error al actualizar PC');
}
