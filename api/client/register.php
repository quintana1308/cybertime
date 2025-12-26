<?php
/**
 * API: Registrar nueva PC cliente
 * POST /api/client/register.php
 */

require_once __DIR__ . '/../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/response.php';

header('Content-Type: application/json');

validate_http_method('POST');

$ip_address = $_POST['ip_address'] ?? get_client_ip();
$mac_address = $_POST['mac_address'] ?? null;

try {
    $db = get_db_connection();
    
    // Verificar si ya existe una PC con esta IP
    $stmt = $db->prepare("SELECT id, name FROM pcs WHERE ip_address = :ip");
    $stmt->execute(['ip' => $ip_address]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        json_success([
            'pc_id' => $existing['id'],
            'pc_name' => $existing['name'],
            'already_registered' => true
        ], 'PC ya registrada');
    }
    
    // Generar nombre automático
    $stmt = $db->query("SELECT COUNT(*) as total FROM pcs");
    $count = $stmt->fetch()['total'];
    $pc_name = 'PC-' . str_pad($count + 1, 2, '0', STR_PAD_LEFT);
    
    // Registrar nueva PC
    $stmt = $db->prepare("
        INSERT INTO pcs (name, ip_address, mac_address, status, is_active)
        VALUES (:name, :ip_address, :mac_address, 'disponible', 1)
    ");
    
    $stmt->execute([
        'name' => $pc_name,
        'ip_address' => $ip_address,
        'mac_address' => $mac_address
    ]);
    
    $pc_id = $db->lastInsertId();
    
    log_message('INFO', "Nueva PC registrada: {$pc_name}", ['pc_id' => $pc_id, 'ip' => $ip_address]);
    
    json_success([
        'pc_id' => $pc_id,
        'pc_name' => $pc_name,
        'already_registered' => false
    ], 'PC registrada correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al registrar PC: ' . $e->getMessage());
    json_server_error('Error al registrar PC');
}
