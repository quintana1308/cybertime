-- ============================================================================
-- CYBERTIME - SISTEMA DE CONTROL DE TIEMPOS PARA CYBER CAFÉ
-- ============================================================================
-- Archivo: schema.sql
-- Descripción: Estructura completa de la base de datos
-- Versión: 1.0.0
-- Fecha: 2024-12-26
-- Motor: MariaDB 10.4+
-- Charset: utf8mb4_unicode_ci
-- ============================================================================

-- Eliminar base de datos si existe (solo para desarrollo)
-- DROP DATABASE IF EXISTS cybertime;

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS cybertime
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE cybertime;

-- ============================================================================
-- TABLA: users
-- Descripción: Usuarios administradores del sistema
-- ============================================================================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt',
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL,
    role ENUM('admin', 'operator') NOT NULL DEFAULT 'operator',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Usuarios administradores del sistema';

-- ============================================================================
-- TABLA: pcs
-- Descripción: Computadoras del cyber café
-- ============================================================================
CREATE TABLE IF NOT EXISTS pcs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE COMMENT 'Nombre descriptivo (PC-01, PC-02, etc)',
    ip_address VARCHAR(45) NULL COMMENT 'Dirección IP de la PC',
    mac_address VARCHAR(17) NULL COMMENT 'Dirección MAC',
    status ENUM('disponible', 'en_uso', 'pausada', 'mantenimiento') NOT NULL DEFAULT 'disponible',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'PC habilitada/deshabilitada',
    location VARCHAR(100) NULL COMMENT 'Ubicación física (Piso 1, Sala A, etc)',
    specifications TEXT NULL COMMENT 'Especificaciones del hardware (JSON)',
    notes TEXT NULL COMMENT 'Notas adicionales',
    last_heartbeat DATETIME NULL COMMENT 'Última señal de vida recibida',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_name (name),
    INDEX idx_status (status),
    INDEX idx_is_active (is_active),
    INDEX idx_ip_address (ip_address),
    INDEX idx_last_heartbeat (last_heartbeat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Computadoras del cyber café';

-- ============================================================================
-- TABLA: sessions
-- Descripción: Sesiones de uso de las PCs
-- ============================================================================
CREATE TABLE IF NOT EXISTS sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pc_id INT UNSIGNED NOT NULL,
    client_name VARCHAR(100) NULL COMMENT 'Nombre del cliente (opcional)',
    client_phone VARCHAR(20) NULL COMMENT 'Teléfono del cliente (opcional)',
    assigned_time INT UNSIGNED NOT NULL COMMENT 'Tiempo asignado en segundos',
    remaining_time INT UNSIGNED NOT NULL COMMENT 'Tiempo restante en segundos',
    start_time DATETIME NOT NULL,
    end_time DATETIME NULL,
    pause_time DATETIME NULL COMMENT 'Momento en que se pausó',
    paused_duration INT UNSIGNED DEFAULT 0 COMMENT 'Tiempo total pausado en segundos',
    status ENUM('activa', 'pausada', 'finalizada', 'cancelada') NOT NULL DEFAULT 'activa',
    price DECIMAL(10,2) NULL COMMENT 'Precio cobrado',
    notes TEXT NULL COMMENT 'Notas de la sesión',
    created_by INT UNSIGNED NULL COMMENT 'Usuario que creó la sesión',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pc_id) REFERENCES pcs(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_pc_id (pc_id),
    INDEX idx_status (status),
    INDEX idx_start_time (start_time),
    INDEX idx_end_time (end_time),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Sesiones de uso de las PCs';

-- ============================================================================
-- TABLA: time_logs
-- Descripción: Registro de cambios de tiempo (agregar, pausar, reanudar)
-- ============================================================================
CREATE TABLE IF NOT EXISTS time_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id INT UNSIGNED NOT NULL,
    action ENUM('asignar', 'agregar', 'pausar', 'reanudar', 'detener', 'finalizar') NOT NULL,
    time_before INT UNSIGNED NULL COMMENT 'Tiempo antes del cambio (segundos)',
    time_after INT UNSIGNED NULL COMMENT 'Tiempo después del cambio (segundos)',
    time_change INT SIGNED NULL COMMENT 'Cambio de tiempo (positivo o negativo)',
    reason VARCHAR(255) NULL COMMENT 'Razón del cambio',
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_session_id (session_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registro de cambios de tiempo en sesiones';

-- ============================================================================
-- TABLA: transactions
-- Descripción: Transacciones financieras (cobros)
-- ============================================================================
CREATE TABLE IF NOT EXISTS transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id INT UNSIGNED NULL,
    type ENUM('tiempo', 'producto', 'servicio', 'otro') NOT NULL DEFAULT 'tiempo',
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('efectivo', 'tarjeta', 'transferencia', 'otro') NOT NULL DEFAULT 'efectivo',
    status ENUM('pendiente', 'pagado', 'cancelado') NOT NULL DEFAULT 'pagado',
    notes TEXT NULL,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_session_id (session_id),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_payment_method (payment_method)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Transacciones financieras del sistema';

-- ============================================================================
-- TABLA: pricing
-- Descripción: Tarifas de precios por tiempo
-- ============================================================================
CREATE TABLE IF NOT EXISTS pricing (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Nombre de la tarifa (Hora, Media hora, etc)',
    minutes INT UNSIGNED NOT NULL COMMENT 'Minutos que incluye',
    price DECIMAL(10,2) NOT NULL COMMENT 'Precio de la tarifa',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    display_order INT UNSIGNED DEFAULT 0 COMMENT 'Orden de visualización',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_is_active (is_active),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tarifas de precios por tiempo';

-- ============================================================================
-- TABLA: settings
-- Descripción: Configuraciones del sistema
-- ============================================================================
CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_type ENUM('string', 'integer', 'boolean', 'json') NOT NULL DEFAULT 'string',
    description VARCHAR(255) NULL,
    is_editable TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Configuraciones generales del sistema';

-- ============================================================================
-- TABLA: logs
-- Descripción: Registro de eventos del sistema
-- ============================================================================
CREATE TABLE IF NOT EXISTS logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    level ENUM('DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL') NOT NULL DEFAULT 'INFO',
    category VARCHAR(50) NULL COMMENT 'Categoría del log (auth, session, system, etc)',
    message TEXT NOT NULL,
    context TEXT NULL COMMENT 'Contexto adicional en JSON',
    user_id INT UNSIGNED NULL,
    pc_id INT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (pc_id) REFERENCES pcs(id) ON DELETE SET NULL,
    
    INDEX idx_level (level),
    INDEX idx_category (category),
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id),
    INDEX idx_pc_id (pc_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registro de eventos del sistema';

-- ============================================================================
-- TABLA: alerts
-- Descripción: Alertas y notificaciones del sistema
-- ============================================================================
CREATE TABLE IF NOT EXISTS alerts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('info', 'warning', 'error', 'success') NOT NULL DEFAULT 'info',
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    pc_id INT UNSIGNED NULL,
    session_id INT UNSIGNED NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    is_resolved TINYINT(1) NOT NULL DEFAULT 0,
    resolved_at DATETIME NULL,
    resolved_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pc_id) REFERENCES pcs(id) ON DELETE CASCADE,
    FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_type (type),
    INDEX idx_is_read (is_read),
    INDEX idx_is_resolved (is_resolved),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Alertas y notificaciones del sistema';

-- ============================================================================
-- VISTAS ÚTILES
-- ============================================================================

-- Vista: Sesiones activas con información de PC
CREATE OR REPLACE VIEW v_active_sessions AS
SELECT 
    s.id AS session_id,
    s.pc_id,
    p.name AS pc_name,
    p.ip_address,
    s.client_name,
    s.assigned_time,
    s.remaining_time,
    s.start_time,
    s.status,
    TIMESTAMPDIFF(SECOND, s.start_time, NOW()) AS elapsed_seconds,
    u.username AS created_by_username
FROM sessions s
INNER JOIN pcs p ON s.pc_id = p.id
LEFT JOIN users u ON s.created_by = u.id
WHERE s.status IN ('activa', 'pausada')
ORDER BY s.start_time DESC;

-- Vista: Estadísticas de PCs
CREATE OR REPLACE VIEW v_pc_stats AS
SELECT 
    p.id,
    p.name,
    p.status,
    COUNT(DISTINCT s.id) AS total_sessions,
    SUM(CASE WHEN s.status = 'finalizada' THEN s.assigned_time ELSE 0 END) AS total_time_used,
    SUM(CASE WHEN s.status = 'finalizada' THEN t.amount ELSE 0 END) AS total_revenue,
    MAX(s.start_time) AS last_session_start
FROM pcs p
LEFT JOIN sessions s ON p.id = s.pc_id
LEFT JOIN transactions t ON s.id = t.session_id
GROUP BY p.id, p.name, p.status;

-- Vista: Resumen financiero diario
CREATE OR REPLACE VIEW v_daily_revenue AS
SELECT 
    DATE(created_at) AS date,
    COUNT(*) AS total_transactions,
    SUM(CASE WHEN type = 'tiempo' THEN amount ELSE 0 END) AS time_revenue,
    SUM(CASE WHEN type = 'producto' THEN amount ELSE 0 END) AS product_revenue,
    SUM(CASE WHEN type = 'servicio' THEN amount ELSE 0 END) AS service_revenue,
    SUM(amount) AS total_revenue,
    SUM(CASE WHEN payment_method = 'efectivo' THEN amount ELSE 0 END) AS cash_revenue,
    SUM(CASE WHEN payment_method = 'tarjeta' THEN amount ELSE 0 END) AS card_revenue
FROM transactions
WHERE status = 'pagado'
GROUP BY DATE(created_at)
ORDER BY date DESC;

-- ============================================================================
-- TRIGGERS
-- ============================================================================

-- Trigger: Actualizar estado de PC cuando cambia sesión
DELIMITER //
CREATE TRIGGER trg_session_update_pc_status
AFTER UPDATE ON sessions
FOR EACH ROW
BEGIN
    IF NEW.status = 'activa' AND OLD.status != 'activa' THEN
        UPDATE pcs SET status = 'en_uso' WHERE id = NEW.pc_id;
    ELSEIF NEW.status = 'pausada' AND OLD.status != 'pausada' THEN
        UPDATE pcs SET status = 'pausada' WHERE id = NEW.pc_id;
    ELSEIF NEW.status IN ('finalizada', 'cancelada') AND OLD.status NOT IN ('finalizada', 'cancelada') THEN
        UPDATE pcs SET status = 'disponible' WHERE id = NEW.pc_id;
    END IF;
END//
DELIMITER ;

-- Trigger: Registrar log cuando se crea sesión
DELIMITER //
CREATE TRIGGER trg_session_log_create
AFTER INSERT ON sessions
FOR EACH ROW
BEGIN
    INSERT INTO logs (level, category, message, user_id, pc_id)
    VALUES (
        'INFO',
        'session',
        CONCAT('Nueva sesión creada: ', NEW.assigned_time, ' segundos asignados'),
        NEW.created_by,
        NEW.pc_id
    );
END//
DELIMITER ;

-- Trigger: Registrar log cuando finaliza sesión
DELIMITER //
CREATE TRIGGER trg_session_log_finish
AFTER UPDATE ON sessions
FOR EACH ROW
BEGIN
    IF NEW.status IN ('finalizada', 'cancelada') AND OLD.status NOT IN ('finalizada', 'cancelada') THEN
        INSERT INTO logs (level, category, message, user_id, pc_id)
        VALUES (
            'INFO',
            'session',
            CONCAT('Sesión ', NEW.status, ': Tiempo usado: ', (NEW.assigned_time - NEW.remaining_time), ' segundos'),
            NEW.created_by,
            NEW.pc_id
        );
    END IF;
END//
DELIMITER ;

-- Trigger: Crear alerta cuando quedan 5 minutos
DELIMITER //
CREATE TRIGGER trg_session_low_time_alert
AFTER UPDATE ON sessions
FOR EACH ROW
BEGIN
    IF NEW.remaining_time <= 300 AND OLD.remaining_time > 300 AND NEW.status = 'activa' THEN
        INSERT INTO alerts (type, title, message, pc_id, session_id)
        VALUES (
            'warning',
            'Tiempo bajo',
            CONCAT('La PC ', (SELECT name FROM pcs WHERE id = NEW.pc_id), ' tiene menos de 5 minutos restantes'),
            NEW.pc_id,
            NEW.id
        );
    END IF;
END//
DELIMITER ;

-- ============================================================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================================================

-- Procedimiento: Limpiar sesiones antiguas
DELIMITER //
CREATE PROCEDURE sp_cleanup_old_sessions(IN days_old INT)
BEGIN
    DELETE FROM sessions 
    WHERE status IN ('finalizada', 'cancelada') 
    AND created_at < DATE_SUB(NOW(), INTERVAL days_old DAY);
    
    SELECT ROW_COUNT() AS deleted_sessions;
END//
DELIMITER ;

-- Procedimiento: Obtener estadísticas del día
DELIMITER //
CREATE PROCEDURE sp_get_daily_stats(IN target_date DATE)
BEGIN
    SELECT 
        COUNT(DISTINCT s.id) AS total_sessions,
        COUNT(DISTINCT s.pc_id) AS pcs_used,
        SUM(s.assigned_time) AS total_time_assigned,
        SUM(s.assigned_time - s.remaining_time) AS total_time_used,
        SUM(t.amount) AS total_revenue,
        AVG(s.assigned_time) AS avg_session_time
    FROM sessions s
    LEFT JOIN transactions t ON s.id = t.session_id AND t.status = 'pagado'
    WHERE DATE(s.created_at) = target_date;
END//
DELIMITER ;

-- Procedimiento: Finalizar sesiones expiradas
DELIMITER //
CREATE PROCEDURE sp_expire_sessions()
BEGIN
    UPDATE sessions 
    SET status = 'finalizada', 
        end_time = NOW(),
        remaining_time = 0
    WHERE status = 'activa' 
    AND remaining_time <= 0;
    
    SELECT ROW_COUNT() AS expired_sessions;
END//
DELIMITER ;

-- ============================================================================
-- EVENTOS PROGRAMADOS
-- ============================================================================

-- Habilitar el programador de eventos
SET GLOBAL event_scheduler = ON;

-- Evento: Limpiar logs antiguos cada día
CREATE EVENT IF NOT EXISTS evt_cleanup_old_logs
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
    DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Evento: Limpiar alertas resueltas antiguas
CREATE EVENT IF NOT EXISTS evt_cleanup_old_alerts
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
    DELETE FROM alerts 
    WHERE is_resolved = 1 
    AND resolved_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- ============================================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================================================

-- Índice compuesto para búsquedas frecuentes
CREATE INDEX idx_sessions_pc_status ON sessions(pc_id, status);
CREATE INDEX idx_sessions_status_time ON sessions(status, start_time);
CREATE INDEX idx_transactions_date ON transactions(created_at, status);

-- ============================================================================
-- COMENTARIOS FINALES
-- ============================================================================

-- Verificar que todas las tablas se crearon correctamente
SELECT 
    TABLE_NAME,
    ENGINE,
    TABLE_ROWS,
    CREATE_TIME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'cybertime'
ORDER BY TABLE_NAME;

-- Mostrar mensaje de éxito
SELECT 'Base de datos CyberTime creada exitosamente' AS status;
