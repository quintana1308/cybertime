<?php
/**
 * CYBERTIME - Gestión de PCs
 */

session_start();
define('ADMIN_PAGE', true);

require_once __DIR__ . '/../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/auth.php';

// Verificar autenticación
require_auth();

$page_title = 'Gestión de PCs';
$current_page = 'pcs';

// Obtener todas las PCs
try {
    $db = get_db_connection();
    
    $stmt = $db->query("
        SELECT 
            p.id,
            p.name,
            p.ip_address,
            p.mac_address,
            p.status,
            p.location,
            p.specifications,
            p.notes,
            p.is_active,
            p.last_heartbeat,
            p.created_at,
            COUNT(s.id) as total_sessions,
            SUM(CASE WHEN s.status = 'finalizada' THEN 1 ELSE 0 END) as completed_sessions
        FROM pcs p
        LEFT JOIN sessions s ON p.id = s.pc_id
        GROUP BY p.id
        ORDER BY p.name
    ");
    
    $pcs = $stmt->fetchAll();
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener PCs: ' . $e->getMessage());
    $pcs = [];
}

include 'includes/header.php';
?>

<div class="page-header">
    <h2>Gestión de Computadoras</h2>
    <button class="btn btn-primary" onclick="openAddPCModal()">
        <i class="fas fa-plus"></i> Agregar PC
    </button>
</div>

<!-- Tabla de PCs -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th>IP</th>
                <th>Ubicación</th>
                <th>Último Heartbeat</th>
                <th>Sesiones</th>
                <th>Activa</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pcs as $pc): ?>
                <tr>
                    <td>
                        <strong><?php echo escape_html($pc['name']); ?></strong>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $pc['status']; ?>">
                            <?php echo ucfirst($pc['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php echo escape_html($pc['ip_address'] ?? 'N/A'); ?>
                    </td>
                    <td>
                        <?php echo escape_html($pc['location'] ?? '-'); ?>
                    </td>
                    <td>
                        <?php 
                        if ($pc['last_heartbeat']) {
                            $diff = time() - strtotime($pc['last_heartbeat']);
                            if ($diff < 60) {
                                echo '<span class="text-success">Hace ' . $diff . 's</span>';
                            } elseif ($diff < 3600) {
                                echo '<span class="text-warning">Hace ' . floor($diff/60) . 'm</span>';
                            } else {
                                echo '<span class="text-danger">' . format_datetime($pc['last_heartbeat']) . '</span>';
                            }
                        } else {
                            echo '<span class="text-muted">Nunca</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <span class="badge badge-info">
                            <?php echo $pc['completed_sessions']; ?> / <?php echo $pc['total_sessions']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($pc['is_active']): ?>
                            <span class="text-success"><i class="fas fa-check-circle"></i> Sí</span>
                        <?php else: ?>
                            <span class="text-danger"><i class="fas fa-times-circle"></i> No</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-info" onclick="viewPC(<?php echo $pc['id']; ?>)" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="editPC(<?php echo $pc['id']; ?>)" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if ($pc['is_active']): ?>
                                <button class="btn btn-sm btn-warning" onclick="togglePC(<?php echo $pc['id']; ?>, 0)" title="Desactivar">
                                    <i class="fas fa-ban"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-success" onclick="togglePC(<?php echo $pc['id']; ?>, 1)" title="Activar">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (is_admin()): ?>
                                <button class="btn btn-sm btn-danger" onclick="deletePC(<?php echo $pc['id']; ?>, '<?php echo escape_html($pc['name']); ?>')" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <?php if (empty($pcs)): ?>
                <tr>
                    <td colspan="8" class="text-center">
                        <p>No hay PCs registradas en el sistema</p>
                        <button class="btn btn-primary" onclick="openAddPCModal()">
                            <i class="fas fa-plus"></i> Agregar Primera PC
                        </button>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Agregar/Editar PC -->
<div id="pcModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="pcModalTitle">Agregar PC</h2>
            <button class="modal-close" onclick="closeModal('pcModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="pcForm">
                <input type="hidden" id="pc_id" name="pc_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="pc_name">Nombre de la PC *</label>
                        <input type="text" id="pc_name" name="name" class="form-control" required placeholder="PC-01">
                    </div>
                    
                    <div class="form-group">
                        <label for="pc_ip">Dirección IP</label>
                        <input type="text" id="pc_ip" name="ip_address" class="form-control" placeholder="192.168.1.101">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="pc_mac">Dirección MAC</label>
                        <input type="text" id="pc_mac" name="mac_address" class="form-control" placeholder="00:00:00:00:00:00">
                    </div>
                    
                    <div class="form-group">
                        <label for="pc_location">Ubicación</label>
                        <input type="text" id="pc_location" name="location" class="form-control" placeholder="Piso 1, Sala A">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="pc_specifications">Especificaciones</label>
                    <textarea id="pc_specifications" name="specifications" class="form-control" rows="3" placeholder="Intel i5, 8GB RAM, GTX 1650"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="pc_notes">Notas</label>
                    <textarea id="pc_notes" name="notes" class="form-control" rows="2" placeholder="Notas adicionales"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="pc_is_active" name="is_active" checked>
                        <span>PC Activa</span>
                    </label>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('pcModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles -->
<div id="viewPCModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Detalles de la PC</h2>
            <button class="modal-close" onclick="closeModal('viewPCModal')">&times;</button>
        </div>
        <div class="modal-body" id="pcDetailsContent">
            <!-- Se cargará dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal Confirmar Activar/Desactivar -->
<div id="togglePCModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2 id="togglePCTitle">Confirmar Acción</h2>
            <button class="modal-close" onclick="closeModal('togglePCModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p id="togglePCMessage"></p>
            <input type="hidden" id="toggle_pc_id">
            <input type="hidden" id="toggle_pc_action">
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('togglePCModal')">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmToggleBtn" onclick="confirmTogglePC()">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminar -->
<div id="deletePCModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2>Confirmar Eliminación</h2>
            <button class="modal-close" onclick="closeModal('deletePCModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <p id="deletePCMessage"></p>
            <p class="warning-text">Esta acción no se puede deshacer.</p>
            <input type="hidden" id="delete_pc_id">
            <input type="hidden" id="delete_pc_name">
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deletePCModal')">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeletePC()">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.pc-details {
    padding: 20px 0;
}

.detail-row {
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    gap: 15px;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row strong {
    min-width: 150px;
    color: var(--dark-color);
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.table-container {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: var(--light-color);
}

.data-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: var(--dark-color);
    border-bottom: 2px solid var(--border-color);
}

.data-table td {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
}

.data-table tbody tr:hover {
    background: var(--light-color);
}

.badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-disponible {
    background: #d1fae5;
    color: #065f46;
}

.badge-en_uso {
    background: #dbeafe;
    color: #1e40af;
}

.badge-pausada {
    background: #fef3c7;
    color: #92400e;
}

.badge-mantenimiento {
    background: #fee2e2;
    color: #991b1b;
}

.badge-info {
    background: #e0e7ff;
    color: #4f46e5;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.text-success {
    color: var(--success-color);
}

.text-warning {
    color: var(--warning-color);
}

.text-danger {
    color: var(--danger-color);
}

.text-muted {
    color: var(--gray-color);
}

.text-center {
    text-align: center;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.modal-small {
    max-width: 500px;
}

.warning-icon {
    text-align: center;
    font-size: 64px;
    color: var(--warning-color);
    margin-bottom: 20px;
}

.warning-text {
    text-align: center;
    color: var(--danger-color);
    font-weight: 600;
    margin-top: 15px;
}
</style>

<script>
const API_BASE = '<?php echo BASE_URL; ?>/api';
let currentPCId = null;

function openAddPCModal() {
    document.getElementById('pcModalTitle').textContent = 'Agregar PC';
    document.getElementById('pcForm').reset();
    document.getElementById('pc_id').value = '';
    document.getElementById('pc_is_active').checked = true;
    openModal('pcModal');
}

function editPC(pcId) {
    // Cargar datos de la PC
    fetch(`${API_BASE}/admin/get_pc.php?id=${pcId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const pc = data.data;
                document.getElementById('pcModalTitle').textContent = 'Editar PC';
                document.getElementById('pc_id').value = pc.id;
                document.getElementById('pc_name').value = pc.name;
                document.getElementById('pc_ip').value = pc.ip_address || '';
                document.getElementById('pc_mac').value = pc.mac_address || '';
                document.getElementById('pc_location').value = pc.location || '';
                document.getElementById('pc_specifications').value = pc.specifications || '';
                document.getElementById('pc_notes').value = pc.notes || '';
                document.getElementById('pc_is_active').checked = pc.is_active == 1;
                openModal('pcModal');
            }
        })
        .catch(err => {
            showNotification('Error al cargar PC', 'error');
        });
}

function viewPC(pcId) {
    fetch(`${API_BASE}/admin/get_pc.php?id=${pcId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const pc = data.data;
                const content = `
                    <div class="pc-details">
                        <div class="detail-row">
                            <strong>Nombre:</strong> ${pc.name}
                        </div>
                        <div class="detail-row">
                            <strong>Estado:</strong> <span class="badge badge-${pc.status}">${pc.status}</span>
                        </div>
                        <div class="detail-row">
                            <strong>IP:</strong> ${pc.ip_address || 'N/A'}
                        </div>
                        <div class="detail-row">
                            <strong>MAC:</strong> ${pc.mac_address || 'N/A'}
                        </div>
                        <div class="detail-row">
                            <strong>Ubicación:</strong> ${pc.location || '-'}
                        </div>
                        <div class="detail-row">
                            <strong>Especificaciones:</strong> ${pc.specifications || '-'}
                        </div>
                        <div class="detail-row">
                            <strong>Notas:</strong> ${pc.notes || '-'}
                        </div>
                        <div class="detail-row">
                            <strong>Activa:</strong> ${pc.is_active ? 'Sí' : 'No'}
                        </div>
                        <div class="detail-row">
                            <strong>Creada:</strong> ${pc.created_at}
                        </div>
                    </div>
                `;
                document.getElementById('pcDetailsContent').innerHTML = content;
                openModal('viewPCModal');
            }
        });
}

function togglePC(pcId, activate) {
    const action = activate ? 'activar' : 'desactivar';
    
    document.getElementById('togglePCTitle').textContent = activate ? 'Activar PC' : 'Desactivar PC';
    document.getElementById('togglePCMessage').textContent = `¿Estás seguro de ${action} esta PC?`;
    document.getElementById('toggle_pc_id').value = pcId;
    document.getElementById('toggle_pc_action').value = activate;
    
    const confirmBtn = document.getElementById('confirmToggleBtn');
    confirmBtn.className = activate ? 'btn btn-success' : 'btn btn-warning';
    
    openModal('togglePCModal');
}

function confirmTogglePC() {
    const pcId = document.getElementById('toggle_pc_id').value;
    const activate = document.getElementById('toggle_pc_action').value;
    const action = activate == '1' ? 'activar' : 'desactivar';
    
    const formData = new FormData();
    formData.append('pc_id', pcId);
    formData.append('is_active', activate);
    
    fetch(`${API_BASE}/admin/toggle_pc.php`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification(`PC ${action}da correctamente`, 'success');
            closeModal('togglePCModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    });
}

function deletePC(pcId, pcName) {
    document.getElementById('deletePCMessage').textContent = `¿Estás seguro de eliminar la PC "${pcName}"?`;
    document.getElementById('delete_pc_id').value = pcId;
    document.getElementById('delete_pc_name').value = pcName;
    
    openModal('deletePCModal');
}

function confirmDeletePC() {
    const pcId = document.getElementById('delete_pc_id').value;
    
    const formData = new FormData();
    formData.append('pc_id', pcId);
    
    fetch(`${API_BASE}/admin/delete_pc.php`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('PC eliminada correctamente', 'success');
            closeModal('deletePCModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error al eliminar PC', 'error');
        }
    })
    .catch(err => {
        showNotification('Error de conexión', 'error');
    });
}

// Procesar formulario
document.getElementById('pcForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const pcId = document.getElementById('pc_id').value;
    const url = pcId ? `${API_BASE}/admin/update_pc.php` : `${API_BASE}/admin/create_pc.php`;
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(pcId ? 'PC actualizada' : 'PC creada', 'success');
            closeModal('pcModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    } catch (error) {
        showNotification('Error de conexión', 'error');
    }
});
</script>

<?php include 'includes/footer.php'; ?>
