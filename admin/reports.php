<?php
/**
 * CYBERTIME - Reportes
 */

session_start();
define('ADMIN_PAGE', true);

require_once __DIR__ . '/../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/auth.php';

require_auth();

$page_title = 'Reportes';
$current_page = 'reports';

// Obtener estadísticas generales
try {
    $db = get_db_connection();
    
    // Ingresos del día
    $stmt = $db->query("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM transactions 
        WHERE DATE(created_at) = CURDATE() AND status = 'pagado'
    ");
    $daily_revenue = $stmt->fetch()['total'];
    
    // Ingresos del mes
    $stmt = $db->query("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM transactions 
        WHERE MONTH(created_at) = MONTH(CURDATE()) 
        AND YEAR(created_at) = YEAR(CURDATE())
        AND status = 'pagado'
    ");
    $monthly_revenue = $stmt->fetch()['total'];
    
    // Total de sesiones del día
    $stmt = $db->query("
        SELECT COUNT(*) as total 
        FROM sessions 
        WHERE DATE(created_at) = CURDATE()
    ");
    $daily_sessions = $stmt->fetch()['total'];
    
    // Total de sesiones del mes
    $stmt = $db->query("
        SELECT COUNT(*) as total 
        FROM sessions 
        WHERE MONTH(created_at) = MONTH(CURDATE()) 
        AND YEAR(created_at) = YEAR(CURDATE())
    ");
    $monthly_sessions = $stmt->fetch()['total'];
    
    // Sesiones por PC (Top 5)
    $stmt = $db->query("
        SELECT 
            p.name,
            COUNT(s.id) as total_sessions,
            SUM(s.assigned_time) as total_time
        FROM pcs p
        LEFT JOIN sessions s ON p.id = s.pc_id
        WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY p.id
        ORDER BY total_sessions DESC
        LIMIT 5
    ");
    $top_pcs = $stmt->fetchAll();
    
    // Ingresos por día (últimos 7 días)
    $stmt = $db->query("
        SELECT 
            DATE(created_at) as date,
            COALESCE(SUM(amount), 0) as total
        FROM transactions
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        AND status = 'pagado'
        GROUP BY DATE(created_at)
        ORDER BY date DESC
    ");
    $daily_chart = $stmt->fetchAll();
    
    // Sesiones recientes
    $stmt = $db->query("
        SELECT 
            s.*,
            p.name as pc_name,
            u.username as created_by_username
        FROM sessions s
        INNER JOIN pcs p ON s.pc_id = p.id
        LEFT JOIN users u ON s.created_by = u.id
        ORDER BY s.created_at DESC
        LIMIT 10
    ");
    $recent_sessions = $stmt->fetchAll();
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener reportes: ' . $e->getMessage());
    $daily_revenue = $monthly_revenue = $daily_sessions = $monthly_sessions = 0;
    $top_pcs = $daily_chart = $recent_sessions = [];
}

include 'includes/header.php';
?>

<div class="page-header">
    <h2>Reportes y Estadísticas</h2>
    <div class="header-actions">
        <button class="btn btn-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <button class="btn btn-primary" onclick="exportReport()">
            <i class="fas fa-download"></i> Exportar
        </button>
    </div>
</div>

<!-- Estadísticas Principales -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-content">
            <h3><?php echo format_price($daily_revenue); ?></h3>
            <p>Ingresos Hoy</p>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-content">
            <h3><?php echo format_price($monthly_revenue); ?></h3>
            <p>Ingresos del Mes</p>
        </div>
    </div>
    
    <div class="stat-card stat-info">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-content">
            <h3><?php echo $daily_sessions; ?></h3>
            <p>Sesiones Hoy</p>
        </div>
    </div>
    
    <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-content">
            <h3><?php echo $monthly_sessions; ?></h3>
            <p>Sesiones del Mes</p>
        </div>
    </div>
</div>

<!-- Gráficos y Tablas -->
<div class="reports-grid">
    <!-- Ingresos por Día -->
    <div class="report-card">
        <div class="report-header">
            <h3><i class="fas fa-chart-bar"></i> Ingresos Últimos 7 Días</h3>
        </div>
        <div class="report-body">
            <div class="chart-container">
                <?php if (!empty($daily_chart)): ?>
                    <table class="simple-chart">
                        <?php foreach ($daily_chart as $day): ?>
                            <tr>
                                <td class="chart-label"><?php echo format_date($day['date']); ?></td>
                                <td class="chart-bar">
                                    <div class="bar" style="width: <?php echo min(100, ($day['total'] / max(1, $monthly_revenue / 30)) * 100); ?>%">
                                        <?php echo format_price($day['total']); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No hay datos disponibles</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Top PCs -->
    <div class="report-card">
        <div class="report-header">
            <h3><i class="fas fa-trophy"></i> PCs Más Utilizadas</h3>
        </div>
        <div class="report-body">
            <?php if (!empty($top_pcs)): ?>
                <table class="simple-table">
                    <thead>
                        <tr>
                            <th>PC</th>
                            <th>Sesiones</th>
                            <th>Tiempo Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_pcs as $pc): ?>
                            <tr>
                                <td><strong><?php echo escape_html($pc['name']); ?></strong></td>
                                <td><?php echo $pc['total_sessions']; ?></td>
                                <td><?php echo format_time($pc['total_time']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No hay datos disponibles</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Sesiones Recientes -->
<div class="report-card full-width">
    <div class="report-header">
        <h3><i class="fas fa-history"></i> Sesiones Recientes</h3>
    </div>
    <div class="report-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>PC</th>
                        <th>Cliente</th>
                        <th>Tiempo Asignado</th>
                        <th>Estado</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Creado Por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_sessions as $session): ?>
                        <tr>
                            <td><strong><?php echo escape_html($session['pc_name']); ?></strong></td>
                            <td><?php echo escape_html($session['client_name'] ?? '-'); ?></td>
                            <td><?php echo format_time($session['assigned_time']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $session['status']; ?>">
                                    <?php echo ucfirst($session['status']); ?>
                                </span>
                            </td>
                            <td><?php echo format_datetime($session['start_time']); ?></td>
                            <td><?php echo $session['end_time'] ? format_datetime($session['end_time']) : '-'; ?></td>
                            <td><?php echo escape_html($session['created_by_username'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($recent_sessions)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay sesiones registradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.report-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.report-card.full-width {
    grid-column: 1 / -1;
}

.report-header {
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
    background: var(--light-color);
}

.report-header h3 {
    font-size: 18px;
    color: var(--dark-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.report-body {
    padding: 20px;
}

.chart-container {
    min-height: 200px;
}

.simple-chart {
    width: 100%;
}

.simple-chart tr {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-label {
    min-width: 100px;
    font-size: 14px;
    color: var(--gray-color);
}

.chart-bar {
    flex: 1;
}

.bar {
    background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    font-weight: 600;
    font-size: 14px;
    text-align: right;
    min-width: 80px;
}

.simple-table {
    width: 100%;
    border-collapse: collapse;
}

.simple-table th {
    text-align: left;
    padding: 10px;
    background: var(--light-color);
    font-weight: 600;
    border-bottom: 2px solid var(--border-color);
}

.simple-table td {
    padding: 10px;
    border-bottom: 1px solid var(--border-color);
}

.table-responsive {
    overflow-x: auto;
}

.text-muted {
    color: var(--gray-color);
    text-align: center;
    padding: 40px;
}

.badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-activa {
    background: #dbeafe;
    color: #1e40af;
}

.badge-pausada {
    background: #fef3c7;
    color: #92400e;
}

.badge-finalizada {
    background: #d1fae5;
    color: #065f46;
}

.badge-cancelada {
    background: #fee2e2;
    color: #991b1b;
}

@media print {
    .sidebar, .header-actions, .btn {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
    }
}
</style>

<script>
function exportReport() {
    showNotification('Función de exportación en desarrollo', 'info');
    // Aquí se puede implementar exportación a PDF o Excel
}
</script>

<?php include 'includes/footer.php'; ?>
