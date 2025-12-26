<?php
/**
 * CYBERTIME - Interfaz de Cliente
 */

require_once __DIR__ . '/../config.php';

// Obtener o generar ID de PC
$pc_id = $_GET['pc_id'] ?? $_COOKIE['cybertime_pc_id'] ?? null;

// Si no tiene ID, intentar registrarse automáticamente
if (!$pc_id) {
    $client_ip = $_SERVER['REMOTE_ADDR'];
    // El registro se hará vía JavaScript
}

// Guardar ID en cookie
if ($pc_id) {
    setcookie('cybertime_pc_id', $pc_id, time() + (86400 * 365), '/');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo BUSINESS_NAME; ?> - PC Cliente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/client.css">
</head>
<body>
    <!-- Pantalla de Bloqueo -->
    <div id="lockScreen" class="lock-screen active">
        <div class="lock-content">
            <div class="lock-icon"><i class="fas fa-lock"></i></div>
            <h1>PC BLOQUEADA</h1>
            <p class="lock-message">Esperando asignación de tiempo</p>
            <div class="business-info">
                <h2><?php echo BUSINESS_NAME; ?></h2>
                <p>Solicita tiempo al encargado</p>
            </div>
            <div class="pc-info">
                <span id="pcName">Cargando...</span>
            </div>
        </div>
        <div class="lock-footer">
            <small>Powered by <?php echo SYSTEM_NAME; ?></small>
        </div>
    </div>
    
    <!-- Pantalla Activa -->
    <div id="activeScreen" class="active-screen">
        <div class="time-container">
            <div class="pc-header">
                <span id="pcNameActive">PC-01</span>
                <span id="clientName" class="client-name"></span>
            </div>
            
            <div class="time-display-wrapper">
                <h2>TIEMPO RESTANTE</h2>
                <div id="timeDisplay" class="time-display">00:00:00</div>
                <div class="time-progress-bar">
                    <div id="timeProgress" class="time-progress-fill"></div>
                </div>
                <div class="time-percentage" id="timePercentage">100%</div>
            </div>
            
            <div class="session-info">
                <div class="info-item">
                    <span class="info-label">Inicio:</span>
                    <span id="startTime" class="info-value">--:--:--</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fin estimado:</span>
                    <span id="endTime" class="info-value">--:--:--</span>
                </div>
            </div>
        </div>
        
        <!-- Alerta de tiempo bajo -->
        <div id="lowTimeAlert" class="low-time-alert">
            <div class="alert-content">
                <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <span class="alert-text">¡Quedan menos de 5 minutos!</span>
            </div>
        </div>
    </div>
    
    <!-- Pantalla de Desconexión -->
    <div id="disconnectScreen" class="disconnect-screen">
        <div class="disconnect-content">
            <div class="disconnect-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h1>CONEXIÓN PERDIDA</h1>
            <p>Intentando reconectar con el servidor...</p>
            <div class="spinner"></div>
        </div>
    </div>
    
    <script>
        // Configuración
        const API_BASE = '<?php echo BASE_URL; ?>/api';
        const POLLING_INTERVAL = <?php echo POLLING_INTERVAL * 1000; ?>;
        const WARNING_TIME = <?php echo WARNING_TIME; ?>;
        
        let PC_ID = <?php echo $pc_id ? $pc_id : 'null'; ?>;
    </script>
    <script src="assets/js/client.js"></script>
</body>
</html>
