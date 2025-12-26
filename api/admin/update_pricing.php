<?php
/**
 * API: Actualizar tarifa existente
 * POST /api/admin/update_pricing.php
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

$pricing_id = $_POST['pricing_id'] ?? null;
$name = sanitize_input($_POST['name'] ?? '');
$minutes = (int)($_POST['minutes'] ?? 0);
$price = (float)($_POST['price'] ?? 0);
$display_order = (int)($_POST['display_order'] ?? 0);
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (!$pricing_id || empty($name) || $minutes < 1 || $price < 0) {
    json_error('Datos inválidos');
}

try {
    $db = get_db_connection();
    
    // Verificar que la tarifa existe
    $stmt = $db->prepare("SELECT id FROM pricing WHERE id = :id");
    $stmt->execute(['id' => $pricing_id]);
    
    if (!$stmt->fetch()) {
        json_error('Tarifa no encontrada', null, 404);
    }
    
    // Actualizar tarifa
    $stmt = $db->prepare("
        UPDATE pricing 
        SET name = :name,
            minutes = :minutes,
            price = :price,
            display_order = :display_order,
            is_active = :is_active
        WHERE id = :id
    ");
    
    $stmt->execute([
        'name' => $name,
        'minutes' => $minutes,
        'price' => $price,
        'display_order' => $display_order,
        'is_active' => $is_active,
        'id' => $pricing_id
    ]);
    
    log_message('INFO', "Tarifa actualizada: {$name}", ['pricing_id' => $pricing_id, 'user_id' => $_SESSION['user_id']]);
    
    json_success([
        'pricing_id' => $pricing_id,
        'name' => $name
    ], 'Tarifa actualizada correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al actualizar tarifa: ' . $e->getMessage());
    json_server_error('Error al actualizar tarifa');
}
