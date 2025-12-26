<?php
/**
 * CYBERTIME - Funciones de Autenticación
 * Manejo de autenticación y autorización de usuarios
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

/**
 * Iniciar sesión de usuario
 * 
 * @param string $username Nombre de usuario
 * @param string $password Contraseña
 * @return array|false Datos del usuario o false si falla
 */
function login_user($username, $password) {
    try {
        $db = get_db_connection();
        
        $stmt = $db->prepare("
            SELECT id, username, password, full_name, email, role, is_active
            FROM users
            WHERE username = :username
        ");
        
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            log_message('WARNING', "Intento de login fallido: usuario no existe", ['username' => $username]);
            return false;
        }
        
        if (!$user['is_active']) {
            log_message('WARNING', "Intento de login con usuario inactivo", ['username' => $username]);
            return false;
        }
        
        if (!verify_password($password, $user['password'])) {
            log_message('WARNING', "Intento de login fallido: contraseña incorrecta", ['username' => $username]);
            return false;
        }
        
        // Actualizar último login
        $update_stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $update_stmt->execute(['id' => $user['id']]);
        
        // Crear sesión
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        log_message('INFO', "Usuario autenticado exitosamente", ['username' => $username]);
        log_to_database('INFO', 'auth', 'Login exitoso', $user['id']);
        
        return $user;
        
    } catch (Exception $e) {
        log_message('ERROR', "Error en login: " . $e->getMessage());
        return false;
    }
}

/**
 * Cerrar sesión de usuario
 */
function logout_user() {
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'unknown';
    
    session_destroy();
    
    log_message('INFO', "Usuario cerró sesión", ['username' => $username]);
    
    if ($user_id) {
        log_to_database('INFO', 'auth', 'Logout', $user_id);
    }
}

/**
 * Verificar si la sesión es válida
 * 
 * @return bool True si la sesión es válida
 */
function check_session() {
    if (!is_authenticated()) {
        return false;
    }
    
    // Verificar timeout de sesión
    if (isset($_SESSION['login_time'])) {
        $elapsed = time() - $_SESSION['login_time'];
        
        if ($elapsed > SESSION_TIMEOUT) {
            logout_user();
            return false;
        }
    }
    
    return true;
}

/**
 * Requerir autenticación (redirige si no está autenticado)
 * 
 * @param string $redirect_url URL de redirección (opcional)
 */
function require_auth($redirect_url = null) {
    if (!check_session()) {
        if ($redirect_url === null) {
            $redirect_url = BASE_URL . '/admin/login.php';
        }
        redirect($redirect_url);
    }
}

/**
 * Requerir rol de administrador
 * 
 * @param string $redirect_url URL de redirección (opcional)
 */
function require_admin($redirect_url = null) {
    require_auth();
    
    if (!is_admin()) {
        if ($redirect_url === null) {
            $redirect_url = BASE_URL . '/admin/';
        }
        redirect($redirect_url);
    }
}

/**
 * Obtener usuario autenticado actual
 * 
 * @return array|null Datos del usuario o null
 */
function get_authenticated_user() {
    if (!is_authenticated()) {
        return null;
    }
    
    try {
        $db = get_db_connection();
        
        $stmt = $db->prepare("
            SELECT id, username, full_name, email, role, is_active
            FROM users
            WHERE id = :id
        ");
        
        $stmt->execute(['id' => $_SESSION['user_id']]);
        return $stmt->fetch();
        
    } catch (Exception $e) {
        log_message('ERROR', "Error al obtener usuario actual: " . $e->getMessage());
        return null;
    }
}
