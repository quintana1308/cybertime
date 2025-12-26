<?php
/**
 * CYBERTIME - Funciones de Respuesta JSON
 * Funciones para enviar respuestas JSON estandarizadas
 */

/**
 * Enviar respuesta JSON exitosa
 * 
 * @param mixed $data Datos a enviar
 * @param string $message Mensaje descriptivo
 * @param int $http_code Código HTTP (por defecto 200)
 */
function json_success($data = null, $message = 'Operación exitosa', $http_code = 200) {
    http_response_code($http_code);
    header('Content-Type: application/json; charset=utf-8');
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'message' => $message,
        'timestamp' => date(DATETIME_FORMAT)
    ], JSON_UNESCAPED_UNICODE);
    
    exit;
}

/**
 * Enviar respuesta JSON de error
 * 
 * @param string $message Mensaje de error
 * @param mixed $errors Errores específicos (opcional)
 * @param int $http_code Código HTTP (por defecto 400)
 */
function json_error($message = 'Error en la operación', $errors = null, $http_code = 400) {
    http_response_code($http_code);
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
        'success' => false,
        'message' => $message,
        'timestamp' => date(DATETIME_FORMAT)
    ];
    
    if ($errors !== null) {
        $response['errors'] = $errors;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
    exit;
}

/**
 * Enviar respuesta JSON no autorizado
 * 
 * @param string $message Mensaje de error
 */
function json_unauthorized($message = 'No autorizado') {
    json_error($message, null, 401);
}

/**
 * Enviar respuesta JSON no encontrado
 * 
 * @param string $message Mensaje de error
 */
function json_not_found($message = 'Recurso no encontrado') {
    json_error($message, null, 404);
}

/**
 * Enviar respuesta JSON error del servidor
 * 
 * @param string $message Mensaje de error
 */
function json_server_error($message = 'Error interno del servidor') {
    json_error($message, null, 500);
}

/**
 * Validar método HTTP
 * 
 * @param string|array $allowed_methods Método(s) permitido(s)
 * @return bool True si el método es válido
 */
function validate_http_method($allowed_methods) {
    if (!is_array($allowed_methods)) {
        $allowed_methods = [$allowed_methods];
    }
    
    $current_method = $_SERVER['REQUEST_METHOD'];
    
    if (!in_array($current_method, $allowed_methods)) {
        json_error('Método HTTP no permitido', null, 405);
        return false;
    }
    
    return true;
}

/**
 * Obtener datos JSON del body de la petición
 * 
 * @return array|null Datos parseados o null si hay error
 */
function get_json_input() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }
    
    return $data;
}

/**
 * Validar parámetros requeridos
 * 
 * @param array $params Parámetros a validar
 * @param array $required Campos requeridos
 * @return bool True si todos los campos están presentes
 */
function validate_required_params($params, $required) {
    $missing = [];
    
    foreach ($required as $field) {
        if (!isset($params[$field]) || $params[$field] === '') {
            $missing[] = $field;
        }
    }
    
    if (!empty($missing)) {
        json_error('Faltan parámetros requeridos', ['missing_fields' => $missing]);
        return false;
    }
    
    return true;
}
