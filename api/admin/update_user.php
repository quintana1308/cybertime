<?php
/**
 * API: Actualizar usuario existente
 * POST /api/admin/update_user.php
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

if (!is_admin()) {
    json_unauthorized('Solo administradores pueden actualizar usuarios');
}

validate_http_method('POST');

$user_id = $_POST['user_id'] ?? null;
$username = sanitize_input($_POST['username'] ?? '');
$full_name = sanitize_input($_POST['full_name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$role = $_POST['role'] ?? 'operator';
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (!$user_id || empty($username) || empty($full_name)) {
    json_error('Datos inválidos');
}

if (!in_array($role, ['admin', 'operator'])) {
    json_error('Rol inválido');
}

try {
    $db = get_db_connection();
    
    // Verificar que el usuario existe
    $stmt = $db->prepare("SELECT id, username FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        json_error('Usuario no encontrado', null, 404);
    }
    
    // Verificar si el username ya existe en otro usuario
    $stmt = $db->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
    $stmt->execute(['username' => $username, 'id' => $user_id]);
    if ($stmt->fetch()) {
        json_error('El nombre de usuario ya existe');
    }
    
    // Actualizar usuario
    $stmt = $db->prepare("
        UPDATE users 
        SET username = :username,
            full_name = :full_name,
            email = :email,
            role = :role,
            is_active = :is_active
        WHERE id = :id
    ");
    
    $stmt->execute([
        'username' => $username,
        'full_name' => $full_name,
        'email' => $email ?: null,
        'role' => $role,
        'is_active' => $is_active,
        'id' => $user_id
    ]);
    
    log_message('INFO', "Usuario actualizado: {$username}", ['user_id' => $user_id, 'updated_by' => $_SESSION['user_id']]);
    
    json_success([
        'user_id' => $user_id,
        'username' => $username
    ], 'Usuario actualizado correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al actualizar usuario: ' . $e->getMessage());
    json_server_error('Error al actualizar usuario');
}
