<?php
/**
 * CYBERTIME - Configuración del Sistema
 */

session_start();
define('ADMIN_PAGE', true);

require_once __DIR__ . '/../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/auth.php';

require_auth();

$page_title = 'Configuración';
$current_page = 'settings';

$message = '';
$error = '';

// Obtener configuraciones actuales
try {
    $db = get_db_connection();
    
    $stmt = $db->query("SELECT * FROM settings ORDER BY setting_key");
    $settings_raw = $stmt->fetchAll();
    
    // Organizar por categoría
    $settings = [];
    foreach ($settings_raw as $setting) {
        $category = explode('_', $setting['setting_key'])[0];
        $settings[$category][] = $setting;
    }
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener configuraciones: ' . $e->getMessage());
    $settings = [];
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_admin()) {
    try {
        $db = get_db_connection();
        
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'setting_') === 0) {
                $setting_key = substr($key, 8); // Remover "setting_"
                
                $stmt = $db->prepare("
                    UPDATE settings 
                    SET setting_value = :value 
                    WHERE setting_key = :key
                ");
                
                $stmt->execute([
                    'value' => $value,
                    'key' => $setting_key
                ]);
            }
        }
        
        log_message('INFO', 'Configuraciones actualizadas', ['user_id' => $_SESSION['user_id']]);
        $message = 'Configuraciones guardadas correctamente';
        
        // Recargar configuraciones
        header('Location: settings.php?success=1');
        exit;
        
    } catch (Exception $e) {
        log_message('ERROR', 'Error al actualizar configuraciones: ' . $e->getMessage());
        $error = 'Error al guardar configuraciones';
    }
}

if (isset($_GET['success'])) {
    $message = 'Configuraciones guardadas correctamente';
}

include 'includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo escape_html($message); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo escape_html($error); ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <h2>Configuración del Sistema</h2>
</div>

<form method="POST" action="">
    <div class="settings-container">
        
        <!-- Información del Sistema -->
        <div class="settings-section">
            <div class="section-header">
                <h3><i class="fas fa-info-circle"></i> Información del Sistema</h3>
            </div>
            <div class="section-body">
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Versión:</strong>
                        <span><?php echo SYSTEM_VERSION; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Servidor:</strong>
                        <span><?php echo SERVER_IP; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Base de Datos:</strong>
                        <span><?php echo DB_NAME; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>PHP:</strong>
                        <span><?php echo phpversion(); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Configuraciones del Sistema -->
        <?php if (isset($settings['system'])): ?>
        <div class="settings-section">
            <div class="section-header">
                <h3><i class="fas fa-cog"></i> Configuración General</h3>
            </div>
            <div class="section-body">
                <?php foreach ($settings['system'] as $setting): ?>
                    <div class="form-group">
                        <label for="setting_<?php echo $setting['setting_key']; ?>">
                            <?php echo escape_html($setting['setting_key']); ?>
                        </label>
                        <input 
                            type="text" 
                            id="setting_<?php echo $setting['setting_key']; ?>" 
                            name="setting_<?php echo $setting['setting_key']; ?>" 
                            class="form-control" 
                            value="<?php echo escape_html($setting['setting_value']); ?>"
                            <?php echo !is_admin() ? 'disabled' : ''; ?>
                        >
                        <?php if ($setting['description']): ?>
                            <small class="form-text"><?php echo escape_html($setting['description']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Configuraciones de Negocio -->
        <?php if (isset($settings['business'])): ?>
        <div class="settings-section">
            <div class="section-header">
                <h3><i class="fas fa-building"></i> Información del Negocio</h3>
            </div>
            <div class="section-body">
                <?php foreach ($settings['business'] as $setting): ?>
                    <div class="form-group">
                        <label for="setting_<?php echo $setting['setting_key']; ?>">
                            <?php echo escape_html($setting['setting_key']); ?>
                        </label>
                        <input 
                            type="text" 
                            id="setting_<?php echo $setting['setting_key']; ?>" 
                            name="setting_<?php echo $setting['setting_key']; ?>" 
                            class="form-control" 
                            value="<?php echo escape_html($setting['setting_value']); ?>"
                            <?php echo !is_admin() ? 'disabled' : ''; ?>
                        >
                        <?php if ($setting['description']): ?>
                            <small class="form-text"><?php echo escape_html($setting['description']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Mantenimiento -->
        <div class="settings-section">
            <div class="section-header">
                <h3><i class="fas fa-tools"></i> Mantenimiento</h3>
            </div>
            <div class="section-body">
                <div class="maintenance-actions">
                    <button type="button" class="btn btn-warning" onclick="clearLogs()">
                        <i class="fas fa-trash"></i> Limpiar Logs Antiguos
                    </button>
                    <button type="button" class="btn btn-info" onclick="backupDatabase()">
                        <i class="fas fa-database"></i> Respaldar Base de Datos
                    </button>
                    <button type="button" class="btn btn-danger" onclick="clearSessions()">
                        <i class="fas fa-broom"></i> Limpiar Sesiones Antiguas
                    </button>
                </div>
            </div>
        </div>
        
    </div>
    
    <?php if (is_admin()): ?>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Guardar Configuraciones
            </button>
        </div>
    <?php endif; ?>
</form>

<style>
.page-header {
    margin-bottom: 30px;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.settings-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 30px;
}

.settings-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.section-header {
    padding: 20px;
    background: var(--light-color);
    border-bottom: 1px solid var(--border-color);
}

.section-header h3 {
    font-size: 18px;
    color: var(--dark-color);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.section-body {
    padding: 25px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-item strong {
    color: var(--gray-color);
    font-size: 14px;
}

.info-item span {
    color: var(--dark-color);
    font-size: 16px;
    font-weight: 600;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark-color);
    text-transform: capitalize;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-control:disabled {
    background: var(--light-color);
    cursor: not-allowed;
}

.form-text {
    display: block;
    margin-top: 5px;
    color: var(--gray-color);
    font-size: 13px;
}

.maintenance-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.form-actions {
    display: flex;
    justify-content: center;
    padding: 20px;
}

.btn-lg {
    padding: 15px 40px;
    font-size: 16px;
}
</style>

<script>
function clearLogs() {
    if (!confirm('¿Estás seguro de limpiar los logs antiguos?')) return;
    
    showNotification('Función en desarrollo', 'info');
    // Implementar limpieza de logs
}

function backupDatabase() {
    if (!confirm('¿Crear respaldo de la base de datos?')) return;
    
    showNotification('Función en desarrollo', 'info');
    // Implementar respaldo de BD
}

function clearSessions() {
    if (!confirm('¿Estás seguro de limpiar las sesiones antiguas?\n\nEsto eliminará sesiones finalizadas de hace más de 30 días.')) return;
    
    showNotification('Función en desarrollo', 'info');
    // Implementar limpieza de sesiones
}
</script>

<?php include 'includes/footer.php'; ?>
