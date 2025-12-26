<?php
/**
 * API: Crear nueva PC
 * POST /api/admin/create_pc.php
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

$name = sanitize_input($_POST['name'] ?? '');
$ip_address = sanitize_input($_POST['ip_address'] ?? '');
$mac_address = sanitize_input($_POST['mac_address'] ?? '');
$location = sanitize_input($_POST['location'] ?? '');
$specifications = sanitize_input($_POST['specifications'] ?? '');
$notes = sanitize_input($_POST['notes'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (empty($name)) {
    json_error('El nombre de la PC es requerido');
}

try {
    $db = get_db_connection();
    
    // Verificar si el nombre ya existe
    $stmt = $db->prepare("SELECT id FROM pcs WHERE name = :name");
    $stmt->execute(['name' => $name]);
    if ($stmt->fetch()) {
        json_error('Ya existe una PC con ese nombre');
    }
    
    // Crear PC
    $stmt = $db->prepare("
        INSERT INTO pcs (name, ip_address, mac_address, location, specifications, notes, is_active, status)
        VALUES (:name, :ip_address, :mac_address, :location, :specifications, :notes, :is_active, 'disponible')
    ");
    
    $stmt->execute([
        'name' => $name,
        'ip_address' => $ip_address ?: null,
        'mac_address' => $mac_address ?: null,
        'location' => $location ?: null,
        'specifications' => $specifications ?: null,
        'notes' => $notes ?: null,
        'is_active' => $is_active
    ]);
    
    $pc_id = $db->lastInsertId();
    
    log_message('INFO', "PC creada: {$name}", ['pc_id' => $pc_id, 'user_id' => $_SESSION['user_id']]);
    
    json_success([
        'pc_id' => $pc_id,
        'name' => $name
    ], 'PC creada correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al crear PC: ' . $e->getMessage());
    json_server_error('Error al crear PC');
}
