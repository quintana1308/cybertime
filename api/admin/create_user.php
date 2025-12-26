<?php
/**
 * API: Crear nuevo usuario
 * POST /api/admin/create_user.php
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
    json_unauthorized('Solo administradores pueden crear usuarios');
}

validate_http_method('POST');

$username = sanitize_input($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$full_name = sanitize_input($_POST['full_name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$role = $_POST['role'] ?? 'operator';
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (empty($username) || empty($password) || empty($full_name)) {
    json_error('Usuario, contraseña y nombre completo son requeridos');
}

if (strlen($password) < 6) {
    json_error('La contraseña debe tener al menos 6 caracteres');
}

if (!in_array($role, ['admin', 'operator'])) {
    json_error('Rol inválido');
}

try {
    $db = get_db_connection();
    
    // Verificar si el username ya existe
    $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch()) {
        json_error('El nombre de usuario ya existe');
    }
    
    // Hash de la contraseña
    $password_hash = hash_password($password);
    
    // Crear usuario
    $stmt = $db->prepare("
        INSERT INTO users (username, password, full_name, email, role, is_active)
        VALUES (:username, :password, :full_name, :email, :role, :is_active)
    ");
    
    $stmt->execute([
        'username' => $username,
        'password' => $password_hash,
        'full_name' => $full_name,
        'email' => $email ?: null,
        'role' => $role,
        'is_active' => $is_active
    ]);
    
    $user_id = $db->lastInsertId();
    
    log_message('INFO', "Usuario creado: {$username}", ['user_id' => $user_id, 'created_by' => $_SESSION['user_id']]);
    
    json_success([
        'user_id' => $user_id,
        'username' => $username
    ], 'Usuario creado correctamente');
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al crear usuario: ' . $e->getMessage());
    json_server_error('Error al crear usuario');
}
