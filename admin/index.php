<?php
/**
 * CYBERTIME - Dashboard Principal
 */

session_start();
define('ADMIN_PAGE', true);

require_once __DIR__ . '/../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/auth.php';

// Verificar autenticación
require_auth();

$page_title = 'Dashboard';
$current_page = 'dashboard';

// Obtener estadísticas
try {
    $db = get_db_connection();
    
    // Total de PCs
    $stmt = $db->query("SELECT COUNT(*) as total FROM pcs WHERE is_active = 1");
    $total_pcs = $stmt->fetch()['total'];
    
    // PCs en uso
    $stmt = $db->query("SELECT COUNT(*) as total FROM pcs WHERE status = 'en_uso'");
    $pcs_in_use = $stmt->fetch()['total'];
    
    // PCs disponibles
    $stmt = $db->query("SELECT COUNT(*) as total FROM pcs WHERE status = 'disponible'");
    $pcs_available = $stmt->fetch()['total'];
    
    // Ingresos del día
    $stmt = $db->query("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM transactions 
        WHERE DATE(created_at) = CURDATE() AND status = 'pagado'
    ");
    $daily_revenue = $stmt->fetch()['total'];
    
    // Sesiones activas
    $stmt = $db->query("SELECT COUNT(*) as total FROM sessions WHERE status IN ('activa', 'pausada')");
    $active_sessions = $stmt->fetch()['total'];
    
    // Obtener todas las PCs con su estado
    $stmt = $db->query("
        SELECT 
            p.id,
            p.name,
            p.status,
            p.location,
            s.id as session_id,
            s.client_name,
            s.remaining_time,
            s.assigned_time,
            s.start_time,
            s.status as session_status
        FROM pcs p
        LEFT JOIN sessions s ON p.id = s.pc_id AND s.status IN ('activa', 'pausada')
        WHERE p.is_active = 1
        ORDER BY p.name
    ");
    $pcs = $stmt->fetchAll();
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener datos del dashboard: ' . $e->getMessage());
    $total_pcs = $pcs_in_use = $pcs_available = $daily_revenue = $active_sessions = 0;
    $pcs = [];
}

include 'includes/header.php';
?>

<!-- Estadísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">💻</div>
        <div class="stat-content">
            <h3><?php echo $total_pcs; ?></h3>
            <p>Total PCs</p>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
            <h3><?php echo $pcs_in_use; ?></h3>
            <p>En Uso</p>
        </div>
    </div>
    
    <div class="stat-card stat-info">
        <div class="stat-icon">🟢</div>
        <div class="stat-content">
            <h3><?php echo $pcs_available; ?></h3>
            <p>Disponibles</p>
        </div>
    </div>
    
    <div class="stat-card stat-warning">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
            <h3><?php echo format_price($daily_revenue); ?></h3>
            <p>Ingresos Hoy</p>
        </div>
    </div>
</div>

<!-- Grid de PCs -->
<div class="section-header">
    <h2>Estado de las PCs</h2>
    <div class="section-actions">
        <button class="btn btn-primary" onclick="refreshPCs()">🔄 Actualizar</button>
    </div>
</div>

<div class="pcs-grid" id="pcsGrid">
    <?php foreach ($pcs as $pc): ?>
        <div class="pc-card pc-<?php echo $pc['status']; ?>" data-pc-id="<?php echo $pc['id']; ?>">
            <div class="pc-header">
                <h3><?php echo escape_html($pc['name']); ?></h3>
                <span class="pc-status-badge status-<?php echo $pc['status']; ?>">
                    <?php echo ucfirst($pc['status']); ?>
                </span>
            </div>
            
            <div class="pc-body">
                <?php if ($pc['status'] === 'en_uso' && $pc['session_id']): ?>
                    <div class="pc-time">
                        <div class="time-display" data-remaining="<?php echo $pc['remaining_time']; ?>">
                            <?php echo format_time($pc['remaining_time']); ?>
                        </div>
                        <div class="time-progress">
                            <?php 
                            $progress = ($pc['remaining_time'] / $pc['assigned_time']) * 100;
                            ?>
                            <div class="progress-bar" style="width: <?php echo $progress; ?>%"></div>
                        </div>
                    </div>
                    
                    <?php if ($pc['client_name']): ?>
                        <div class="pc-client">
                            <strong>Cliente:</strong> <?php echo escape_html($pc['client_name']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="pc-actions">
                        <button class="btn btn-sm btn-warning" onclick="pauseTime(<?php echo $pc['session_id']; ?>)">
                            ⏸️ Pausar
                        </button>
                        <button class="btn btn-sm btn-success" onclick="addTime(<?php echo $pc['session_id']; ?>)">
                            ➕ Agregar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="stopTime(<?php echo $pc['session_id']; ?>)">
                            ⏹️ Detener
                        </button>
                    </div>
                    
                <?php elseif ($pc['status'] === 'pausada' && $pc['session_id']): ?>
                    <div class="pc-time">
                        <div class="time-display paused">
                            ⏸️ PAUSADA
                        </div>
                        <div class="time-remaining">
                            Tiempo restante: <?php echo format_time($pc['remaining_time']); ?>
                        </div>
                    </div>
                    
                    <div class="pc-actions">
                        <button class="btn btn-sm btn-success" onclick="resumeTime(<?php echo $pc['session_id']; ?>)">
                            ▶️ Reanudar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="stopTime(<?php echo $pc['session_id']; ?>)">
                            ⏹️ Detener
                        </button>
                    </div>
                    
                <?php else: ?>
                    <div class="pc-available">
                        <p>PC disponible para asignar tiempo</p>
                    </div>
                    
                    <div class="pc-actions">
                        <button class="btn btn-sm btn-primary" onclick="assignTime(<?php echo $pc['id']; ?>)">
                            ▶️ Asignar Tiempo
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($pc['location']): ?>
                <div class="pc-footer">
                    <small>📍 <?php echo escape_html($pc['location']); ?></small>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($pcs)): ?>
        <div class="empty-state">
            <p>No hay PCs registradas en el sistema</p>
            <a href="pcs.php" class="btn btn-primary">Agregar PC</a>
        </div>
    <?php endif; ?>
</div>

<!-- Modal para asignar tiempo -->
<div id="assignTimeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Asignar Tiempo</h2>
            <button class="modal-close" onclick="closeModal('assignTimeModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="assignTimeForm">
                <input type="hidden" id="assign_pc_id" name="pc_id">
                
                <div class="form-group">
                    <label for="client_name">Nombre del Cliente (Opcional)</label>
                    <input type="text" id="client_name" name="client_name" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Seleccionar Tarifa</label>
                    <div id="pricingOptions" class="pricing-grid">
                        <!-- Se cargará dinámicamente -->
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="custom_minutes">O ingresar minutos personalizados</label>
                    <input type="number" id="custom_minutes" name="custom_minutes" class="form-control" min="1" placeholder="Minutos">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('assignTimeModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar Tiempo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar tiempo -->
<div id="addTimeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Agregar Tiempo Adicional</h2>
            <button class="modal-close" onclick="closeModal('addTimeModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addTimeForm">
                <input type="hidden" id="add_session_id" name="session_id">
                
                <div class="form-group">
                    <label for="add_minutes">Minutos a Agregar</label>
                    <input type="number" id="add_minutes" name="minutes" class="form-control" min="1" required>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addTimeModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar Tiempo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Configuración global
const API_BASE = '<?php echo BASE_URL; ?>/api';
const POLLING_INTERVAL = <?php echo POLLING_INTERVAL * 1000; ?>;

// Actualizar PCs automáticamente
setInterval(refreshPCs, POLLING_INTERVAL);

// Actualizar contadores de tiempo cada segundo
setInterval(updateTimeDisplays, 1000);
</script>

<?php include 'includes/footer.php'; ?>
