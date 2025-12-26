-- ============================================================================
-- CYBERTIME - DATOS INICIALES (SEEDS)
-- ============================================================================
-- Archivo: seeds.sql
-- Descripción: Datos iniciales para el sistema
-- Versión: 1.0.0
-- Fecha: 2024-12-26
-- ============================================================================

USE cybertime;

-- ============================================================================
-- USUARIOS INICIALES
-- ============================================================================

-- Usuario administrador por defecto
-- Usuario: admin
-- Contraseña: admin123
-- Hash generado con: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO users (username, password, full_name, email, role, is_active) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin@cybertime.local', 'admin', 1);

-- Usuario operador de ejemplo
-- Usuario: operador
-- Contraseña: operador123
INSERT INTO users (username, password, full_name, email, role, is_active) VALUES
('operador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operador Principal', 'operador@cybertime.local', 'operator', 1);

-- ============================================================================
-- COMPUTADORAS INICIALES
-- ============================================================================

-- Crear 10 PCs de ejemplo
INSERT INTO pcs (name, location, status, is_active, specifications) VALUES
('PC-01', 'Sala Principal - Fila 1', 'disponible', 1, '{"cpu": "Intel i3", "ram": "8GB", "monitor": "19 pulgadas"}'),
('PC-02', 'Sala Principal - Fila 1', 'disponible', 1, '{"cpu": "Intel i3", "ram": "8GB", "monitor": "19 pulgadas"}'),
('PC-03', 'Sala Principal - Fila 1', 'disponible', 1, '{"cpu": "Intel i3", "ram": "8GB", "monitor": "19 pulgadas"}'),
('PC-04', 'Sala Principal - Fila 2', 'disponible', 1, '{"cpu": "Intel i5", "ram": "16GB", "monitor": "22 pulgadas"}'),
('PC-05', 'Sala Principal - Fila 2', 'disponible', 1, '{"cpu": "Intel i5", "ram": "16GB", "monitor": "22 pulgadas"}'),
('PC-06', 'Sala Principal - Fila 2', 'disponible', 1, '{"cpu": "Intel i5", "ram": "16GB", "monitor": "22 pulgadas"}'),
('PC-07', 'Sala VIP', 'disponible', 1, '{"cpu": "Intel i7", "ram": "32GB", "monitor": "27 pulgadas", "gpu": "GTX 1660"}'),
('PC-08', 'Sala VIP', 'disponible', 1, '{"cpu": "Intel i7", "ram": "32GB", "monitor": "27 pulgadas", "gpu": "GTX 1660"}'),
('PC-09', 'Sala Gaming', 'disponible', 1, '{"cpu": "AMD Ryzen 7", "ram": "32GB", "monitor": "27 pulgadas", "gpu": "RTX 3060"}'),
('PC-10', 'Sala Gaming', 'disponible', 1, '{"cpu": "AMD Ryzen 7", "ram": "32GB", "monitor": "27 pulgadas", "gpu": "RTX 3060"}');

-- ============================================================================
-- TARIFAS DE PRECIOS
-- ============================================================================

INSERT INTO pricing (name, minutes, price, is_active, display_order) VALUES
('15 minutos', 15, 5.00, 1, 1),
('30 minutos', 30, 10.00, 1, 2),
('1 hora', 60, 15.00, 1, 3),
('2 horas', 120, 25.00, 1, 4),
('3 horas', 180, 35.00, 1, 5),
('Noche completa (8 horas)', 480, 80.00, 1, 6),
('Día completo (12 horas)', 720, 100.00, 1, 7);

-- ============================================================================
-- CONFIGURACIONES DEL SISTEMA
-- ============================================================================

INSERT INTO settings (setting_key, setting_value, setting_type, description, is_editable) VALUES
-- Configuración general
('system_name', 'CyberTime', 'string', 'Nombre del sistema', 1),
('business_name', 'Mi Cyber Café', 'string', 'Nombre del negocio', 1),
('timezone', 'America/Mexico_City', 'string', 'Zona horaria del sistema', 1),
('currency', 'MXN', 'string', 'Moneda utilizada', 1),
('currency_symbol', '$', 'string', 'Símbolo de la moneda', 1),

-- Configuración de red
('server_ip', '192.168.1.100', 'string', 'IP del servidor', 1),
('server_port', '80', 'integer', 'Puerto del servidor', 1),
('polling_interval', '2', 'integer', 'Intervalo de actualización en segundos', 1),
('heartbeat_timeout', '30', 'integer', 'Timeout de heartbeat en segundos', 1),

-- Configuración de sesiones
('session_timeout', '28800', 'integer', 'Timeout de sesión de admin en segundos (8 horas)', 1),
('max_clients', '50', 'integer', 'Máximo de PCs clientes', 1),
('auto_lock_on_timeout', '1', 'boolean', 'Bloquear automáticamente al terminar tiempo', 0),
('warning_time', '300', 'integer', 'Tiempo de advertencia en segundos (5 minutos)', 1),

-- Configuración de alertas
('enable_alerts', '1', 'boolean', 'Habilitar alertas del sistema', 1),
('alert_low_time', '1', 'boolean', 'Alertar cuando quede poco tiempo', 1),
('alert_disconnection', '1', 'boolean', 'Alertar cuando se desconecte una PC', 1),
('alert_sound', '1', 'boolean', 'Reproducir sonido en alertas', 1),

-- Configuración de logs
('log_level', 'INFO', 'string', 'Nivel de logging (DEBUG, INFO, WARNING, ERROR)', 1),
('log_retention_days', '30', 'integer', 'Días de retención de logs', 1),
('enable_file_logging', '1', 'boolean', 'Habilitar logs en archivo', 1),

-- Configuración de respaldos
('enable_auto_backup', '1', 'boolean', 'Habilitar respaldo automático', 1),
('backup_time', '02:00', 'string', 'Hora de respaldo automático (HH:MM)', 1),
('backup_retention_days', '7', 'integer', 'Días de retención de respaldos', 1),

-- Configuración de interfaz
('theme', 'light', 'string', 'Tema de la interfaz (light, dark)', 1),
('language', 'es', 'string', 'Idioma del sistema', 1),
('date_format', 'Y-m-d', 'string', 'Formato de fecha', 1),
('time_format', 'H:i:s', 'string', 'Formato de hora', 1),

-- Configuración de seguridad
('require_client_name', '0', 'boolean', 'Requerir nombre de cliente al asignar tiempo', 1),
('enable_client_registration', '1', 'boolean', 'Permitir registro de clientes', 1),
('max_login_attempts', '5', 'integer', 'Máximo de intentos de login', 1),
('lockout_duration', '900', 'integer', 'Duración de bloqueo en segundos (15 minutos)', 1);

-- ============================================================================
-- SESIONES DE EJEMPLO (OPCIONAL - COMENTADAS)
-- ============================================================================

-- Descomentar para crear sesiones de prueba
/*
-- Sesión activa en PC-01
INSERT INTO sessions (pc_id, client_name, assigned_time, remaining_time, start_time, status, created_by) VALUES
(1, 'Juan Pérez', 3600, 2400, NOW(), 'activa', 1);

-- Sesión pausada en PC-02
INSERT INTO sessions (pc_id, client_name, assigned_time, remaining_time, start_time, pause_time, status, created_by) VALUES
(2, 'María García', 7200, 5400, DATE_SUB(NOW(), INTERVAL 30 MINUTE), NOW(), 'pausada', 1);

-- Sesión finalizada en PC-03
INSERT INTO sessions (pc_id, client_name, assigned_time, remaining_time, start_time, end_time, status, price, created_by) VALUES
(3, 'Carlos López', 3600, 0, DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_SUB(NOW(), INTERVAL 1 HOUR), 'finalizada', 15.00, 1);

-- Actualizar estado de las PCs
UPDATE pcs SET status = 'en_uso' WHERE id = 1;
UPDATE pcs SET status = 'pausada' WHERE id = 2;
*/

-- ============================================================================
-- TRANSACCIONES DE EJEMPLO (OPCIONAL - COMENTADAS)
-- ============================================================================

/*
-- Transacción de la sesión finalizada
INSERT INTO transactions (session_id, type, description, amount, payment_method, status, created_by) VALUES
(3, 'tiempo', '1 hora de uso', 15.00, 'efectivo', 'pagado', 1);
*/

-- ============================================================================
-- LOGS INICIALES
-- ============================================================================

INSERT INTO logs (level, category, message, user_id) VALUES
('INFO', 'system', 'Sistema CyberTime inicializado correctamente', 1),
('INFO', 'system', 'Base de datos creada y poblada con datos iniciales', 1),
('INFO', 'system', '10 PCs registradas en el sistema', 1);

-- ============================================================================
-- VERIFICACIÓN DE DATOS
-- ============================================================================

-- Mostrar resumen de datos insertados
SELECT 'Datos iniciales insertados correctamente' AS status;

SELECT 
    'Usuarios' AS tabla,
    COUNT(*) AS registros
FROM users
UNION ALL
SELECT 
    'PCs' AS tabla,
    COUNT(*) AS registros
FROM pcs
UNION ALL
SELECT 
    'Tarifas' AS tabla,
    COUNT(*) AS registros
FROM pricing
UNION ALL
SELECT 
    'Configuraciones' AS tabla,
    COUNT(*) AS registros
FROM settings
UNION ALL
SELECT 
    'Logs' AS tabla,
    COUNT(*) AS registros
FROM logs;

-- ============================================================================
-- NOTAS IMPORTANTES
-- ============================================================================

/*
CREDENCIALES POR DEFECTO:
========================

Administrador:
- Usuario: admin
- Contraseña: admin123

Operador:
- Usuario: operador
- Contraseña: operador123

⚠️ IMPORTANTE: Cambiar estas contraseñas después de la instalación

PRÓXIMOS PASOS:
===============

1. Cambiar contraseñas de usuarios por defecto
2. Configurar IP del servidor en tabla settings
3. Ajustar tarifas según tu negocio
4. Personalizar nombres y ubicaciones de PCs
5. Configurar zona horaria correcta
6. Ajustar configuraciones según necesidades

COMANDOS ÚTILES:
================

-- Ver todas las PCs:
SELECT * FROM pcs;

-- Ver sesiones activas:
SELECT * FROM v_active_sessions;

-- Ver estadísticas de PCs:
SELECT * FROM v_pc_stats;

-- Ver ingresos del día:
SELECT * FROM v_daily_revenue WHERE date = CURDATE();

-- Cambiar contraseña de admin:
UPDATE users 
SET password = '$2y$10$[NUEVO_HASH]' 
WHERE username = 'admin';

-- Limpiar sesiones antiguas (más de 30 días):
CALL sp_cleanup_old_sessions(30);

-- Obtener estadísticas del día:
CALL sp_get_daily_stats(CURDATE());

-- Finalizar sesiones expiradas:
CALL sp_expire_sessions();
*/
