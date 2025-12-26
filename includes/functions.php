<?php
/**
 * CYBERTIME - Funciones Auxiliares
 * Funciones comunes utilizadas en todo el sistema
 */

/**
 * Sanitizar entrada de usuario
 * 
 * @param string $data Datos a sanitizar
 * @return string Datos sanitizados
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validar email
 * 
 * @param string $email Email a validar
 * @return bool True si es válido
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar IP
 * 
 * @param string $ip IP a validar
 * @return bool True si es válida
 */
function validate_ip($ip) {
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

/**
 * Generar hash de contraseña
 * 
 * @param string $password Contraseña en texto plano
 * @return string Hash de la contraseña
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verificar contraseña
 * 
 * @param string $password Contraseña en texto plano
 * @param string $hash Hash almacenado
 * @return bool True si coincide
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Registrar log en archivo
 * 
 * @param string $level Nivel: DEBUG, INFO, WARNING, ERROR, CRITICAL
 * @param string $message Mensaje
 * @param array $context Contexto adicional
 */
function log_message($level, $message, $context = []) {
    if (!ENABLE_FILE_LOGGING) {
        return;
    }
    
    $levels = ['DEBUG' => 0, 'INFO' => 1, 'WARNING' => 2, 'ERROR' => 3, 'CRITICAL' => 4];
    $current_level = $levels[LOG_LEVEL] ?? 1;
    $message_level = $levels[$level] ?? 1;
    
    if ($message_level < $current_level) {
        return;
    }
    
    $timestamp = date(DATETIME_FORMAT);
    $context_str = !empty($context) ? ' | ' . json_encode($context) : '';
    $log_entry = "[{$timestamp}] [{$level}] {$message}{$context_str}\n";
    
    $log_file = ($level === 'ERROR' || $level === 'CRITICAL') ? ERROR_LOG_FILE : LOG_FILE;
    
    @file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * Registrar log en base de datos
 * 
 * @param string $level Nivel del log
 * @param string $category Categoría
 * @param string $message Mensaje
 * @param int $user_id ID del usuario (opcional)
 * @param int $pc_id ID de la PC (opcional)
 * @param array $context Contexto adicional (opcional)
 */
function log_to_database($level, $category, $message, $user_id = null, $pc_id = null, $context = null) {
    try {
        $db = get_db_connection();
        
        $stmt = $db->prepare("
            INSERT INTO logs (level, category, message, user_id, pc_id, context, ip_address, user_agent)
            VALUES (:level, :category, :message, :user_id, :pc_id, :context, :ip_address, :user_agent)
        ");
        
        $stmt->execute([
            'level' => $level,
            'category' => $category,
            'message' => $message,
            'user_id' => $user_id,
            'pc_id' => $pc_id,
            'context' => $context ? json_encode($context) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
    } catch (Exception $e) {
        log_message('ERROR', 'Error al guardar log en base de datos: ' . $e->getMessage());
    }
}

/**
 * Obtener IP del cliente
 * 
 * @return string IP del cliente
 */
function get_client_ip() {
    $ip = '';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    }
    
    return $ip;
}

/**
 * Generar token aleatorio
 * 
 * @param int $length Longitud del token
 * @return string Token generado
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Verificar si el usuario está autenticado
 * 
 * @return bool True si está autenticado
 */
function is_authenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verificar si el usuario es administrador
 * 
 * @return bool True si es admin
 */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === USER_ROLE_ADMIN;
}

/**
 * Redirigir a una URL
 * 
 * @param string $url URL de destino
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Escapar HTML
 * 
 * @param string $text Texto a escapar
 * @return string Texto escapado
 */
function escape_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Formatear fecha
 * 
 * @param string $date Fecha a formatear
 * @return string Fecha formateada
 */
function format_date($date) {
    if (empty($date)) {
        return '';
    }
    
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}

/**
 * Formatear fecha y hora
 * 
 * @param string $datetime Fecha y hora a formatear
 * @return string Fecha y hora formateada
 */
function format_datetime($datetime) {
    if (empty($datetime)) {
        return '';
    }
    
    $timestamp = strtotime($datetime);
    return date('d/m/Y H:i', $timestamp);
}

/**
 * Calcular diferencia de tiempo en segundos
 * 
 * @param string $start Fecha/hora de inicio
 * @param string $end Fecha/hora de fin (opcional, por defecto ahora)
 * @return int Diferencia en segundos
 */
function time_diff_seconds($start, $end = null) {
    $start_timestamp = strtotime($start);
    $end_timestamp = $end ? strtotime($end) : time();
    
    return $end_timestamp - $start_timestamp;
}
