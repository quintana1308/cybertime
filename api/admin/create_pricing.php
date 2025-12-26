<?php
/**
 * API: Crear nueva tarifa
 * POST /api/admin/create_pricing.php
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
$minutes = (int)($_POST['minutes'] ?? 0);
$price = (float)($_POST['price'] ?? 0);
$display_order = (int)($_POST['display_order'] ?? 0);
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (empty($name) || $minutes < 1 || $price < 0) {
    json_error('Datos inválidos. Nombre, minutos y precio son requeridos');
}

try {
    $db = get_db_connection();
    
    // Crear tarifa
    $stmt = $db->prepare("
        INSERT INTO pricing (name, minutes, price, display_order, is_active)
        VALUES (:name, :minutes, :price, :display_order, :is_active)
    ");
    
    $stmt->execute([
        'name' => $name,
        'minutes' => $minutes,
        'price' => $price,
        'display_order' => $display_order,
        'is_active' => $is_active
    ]);
    
    $pricing_id = $db->lastInsertId();
    
    log_message('INFO', "Tarifa creada: {$name}", ['pricing_id' => $pricing_id, 'user_id' => $_SESSION['user_id']]);
    
    json_success([
        'pricing_id' => $pricing_id,
        'name' => $name
    ], 'Tarifa creada correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al crear tarifa: ' . $e->getMessage());
    json_server_error('Error al crear tarifa');
}
