<?php
/**
 * CYBERTIME - Gestión de Tarifas
 */

session_start();
define('ADMIN_PAGE', true);

require_once __DIR__ . '/../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/auth.php';

require_auth();

$page_title = 'Gestión de Tarifas';
$current_page = 'pricing';

// Obtener todas las tarifas
try {
    $db = get_db_connection();
    
    $stmt = $db->query("
        SELECT * FROM pricing
        ORDER BY display_order, minutes
    ");
    
    $pricing = $stmt->fetchAll();
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener tarifas: ' . $e->getMessage());
    $pricing = [];
}

include 'includes/header.php';
?>

<div class="page-header">
    <h2>Gestión de Tarifas</h2>
    <button class="btn btn-primary" onclick="openAddPricingModal()">
        <i class="fas fa-plus"></i> Agregar Tarifa
    </button>
</div>

<!-- Grid de Tarifas -->
<div class="pricing-grid-view">
    <?php foreach ($pricing as $price): ?>
        <div class="pricing-card <?php echo $price['is_active'] ? '' : 'inactive'; ?>">
            <div class="pricing-header">
                <h3><?php echo escape_html($price['name']); ?></h3>
                <?php if (!$price['is_active']): ?>
                    <span class="badge badge-inactive">Inactiva</span>
                <?php endif; ?>
            </div>
            
            <div class="pricing-body">
                <div class="pricing-price">
                    <?php echo format_price($price['price']); ?>
                </div>
                <div class="pricing-time">
                    <i class="fas fa-clock"></i> <?php echo $price['minutes']; ?> minutos
                </div>
            </div>
            
            <div class="pricing-footer">
                <div class="pricing-stats">
                    <small>Orden: <?php echo $price['display_order']; ?></small>
                </div>
                <div class="pricing-actions">
                    <button class="btn btn-sm btn-primary" onclick="editPricing(<?php echo $price['id']; ?>)" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <?php if ($price['is_active']): ?>
                        <button class="btn btn-sm btn-warning" onclick="togglePricing(<?php echo $price['id']; ?>, 0)" title="Desactivar">
                            <i class="fas fa-ban"></i>
                        </button>
                    <?php else: ?>
                        <button class="btn btn-sm btn-success" onclick="togglePricing(<?php echo $price['id']; ?>, 1)" title="Activar">
                            <i class="fas fa-check"></i>
                        </button>
                    <?php endif; ?>
                    <?php if (is_admin()): ?>
                        <button class="btn btn-sm btn-danger" onclick="deletePricing(<?php echo $price['id']; ?>, '<?php echo escape_html($price['name']); ?>')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($pricing)): ?>
        <div class="empty-state">
            <i class="fas fa-dollar-sign fa-3x"></i>
            <p>No hay tarifas registradas</p>
            <button class="btn btn-primary" onclick="openAddPricingModal()">
                <i class="fas fa-plus"></i> Agregar Primera Tarifa
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Confirmar Activar/Desactivar -->
<div id="togglePricingModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2 id="togglePricingTitle">Confirmar Acción</h2>
            <button class="modal-close" onclick="closeModal('togglePricingModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p id="togglePricingMessage"></p>
            <input type="hidden" id="toggle_pricing_id">
            <input type="hidden" id="toggle_pricing_action">
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('togglePricingModal')">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmTogglePricingBtn" onclick="confirmTogglePricing()">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminar -->
<div id="deletePricingModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2>Confirmar Eliminación</h2>
            <button class="modal-close" onclick="closeModal('deletePricingModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <p id="deletePricingMessage"></p>
            <p class="warning-text">Esta acción no se puede deshacer.</p>
            <input type="hidden" id="delete_pricing_id">
            <input type="hidden" id="delete_pricing_name">
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deletePricingModal')">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeletePricing()">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar/Editar Tarifa -->
<div id="pricingModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="pricingModalTitle">Agregar Tarifa</h2>
            <button class="modal-close" onclick="closeModal('pricingModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="pricingForm">
                <input type="hidden" id="pricing_id" name="pricing_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="pricing_name">Nombre de la Tarifa *</label>
                        <input type="text" id="pricing_name" name="name" class="form-control" required placeholder="1 Hora">
                    </div>
                    
                    <div class="form-group">
                        <label for="pricing_minutes">Minutos *</label>
                        <input type="number" id="pricing_minutes" name="minutes" class="form-control" required min="1" placeholder="60">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="pricing_price">Precio *</label>
                        <input type="number" id="pricing_price" name="price" class="form-control" required min="0" step="0.01" placeholder="5.00">
                    </div>
                    
                    <div class="form-group">
                        <label for="pricing_order">Orden de Visualización</label>
                        <input type="number" id="pricing_order" name="display_order" class="form-control" min="0" value="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="pricing_is_active" name="is_active" checked>
                        <span>Tarifa Activa</span>
                    </label>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('pricingModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
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

.pricing-grid-view {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.pricing-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
    border: 2px solid transparent;
}

.pricing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    border-color: var(--primary-color);
}

.pricing-card.inactive {
    opacity: 0.6;
    background: var(--light-color);
}

.pricing-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border-color);
}

.pricing-header h3 {
    font-size: 20px;
    color: var(--dark-color);
    margin: 0;
}

.pricing-body {
    text-align: center;
    margin-bottom: 20px;
}

.pricing-price {
    font-size: 48px;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.pricing-time {
    font-size: 18px;
    color: var(--gray-color);
    margin-bottom: 15px;
}

.pricing-time i {
    color: var(--primary-color);
}

.pricing-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
}

.pricing-stats small {
    color: var(--gray-color);
}

.pricing-actions {
    display: flex;
    gap: 5px;
}

.badge-inactive {
    background: #fee2e2;
    color: #991b1b;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: var(--gray-color);
}

.empty-state i {
    margin-bottom: 20px;
    color: var(--primary-color);
}

.empty-state p {
    font-size: 18px;
    margin-bottom: 20px;
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

function openAddPricingModal() {
    document.getElementById('pricingModalTitle').textContent = 'Agregar Tarifa';
    document.getElementById('pricingForm').reset();
    document.getElementById('pricing_id').value = '';
    document.getElementById('pricing_is_active').checked = true;
    openModal('pricingModal');
}

function editPricing(pricingId) {
    fetch(`${API_BASE}/admin/get_pricing.php?id=${pricingId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const pricing = data.data;
                document.getElementById('pricingModalTitle').textContent = 'Editar Tarifa';
                document.getElementById('pricing_id').value = pricing.id;
                document.getElementById('pricing_name').value = pricing.name;
                document.getElementById('pricing_minutes').value = pricing.minutes;
                document.getElementById('pricing_price').value = pricing.price;
                document.getElementById('pricing_order').value = pricing.display_order;
                document.getElementById('pricing_is_active').checked = pricing.is_active == 1;
                openModal('pricingModal');
            }
        })
        .catch(err => {
            showNotification('Error al cargar tarifa', 'error');
        });
}

function togglePricing(pricingId, activate) {
    const action = activate ? 'activar' : 'desactivar';
    
    document.getElementById('togglePricingTitle').textContent = activate ? 'Activar Tarifa' : 'Desactivar Tarifa';
    document.getElementById('togglePricingMessage').textContent = `¿Estás seguro de ${action} esta tarifa?`;
    document.getElementById('toggle_pricing_id').value = pricingId;
    document.getElementById('toggle_pricing_action').value = activate;
    
    const confirmBtn = document.getElementById('confirmTogglePricingBtn');
    confirmBtn.className = activate ? 'btn btn-success' : 'btn btn-warning';
    
    openModal('togglePricingModal');
}

function confirmTogglePricing() {
    const pricingId = document.getElementById('toggle_pricing_id').value;
    const activate = document.getElementById('toggle_pricing_action').value;
    const action = activate == '1' ? 'activar' : 'desactivar';
    
    const formData = new FormData();
    formData.append('pricing_id', pricingId);
    formData.append('is_active', activate);
    
    fetch(`${API_BASE}/admin/toggle_pricing.php`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification(`Tarifa ${action}da correctamente`, 'success');
            closeModal('togglePricingModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    });
}

function deletePricing(pricingId, pricingName) {
    document.getElementById('deletePricingMessage').textContent = `¿Estás seguro de eliminar la tarifa "${pricingName}"?`;
    document.getElementById('delete_pricing_id').value = pricingId;
    document.getElementById('delete_pricing_name').value = pricingName;
    
    openModal('deletePricingModal');
}

function confirmDeletePricing() {
    const pricingId = document.getElementById('delete_pricing_id').value;
    
    const formData = new FormData();
    formData.append('pricing_id', pricingId);
    
    fetch(`${API_BASE}/admin/delete_pricing.php`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Tarifa eliminada correctamente', 'success');
            closeModal('deletePricingModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error al eliminar tarifa', 'error');
        }
    })
    .catch(err => {
        showNotification('Error de conexión', 'error');
    });
}

// Procesar formulario
document.getElementById('pricingForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const pricingId = document.getElementById('pricing_id').value;
    const url = pricingId ? `${API_BASE}/admin/update_pricing.php` : `${API_BASE}/admin/create_pricing.php`;
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(pricingId ? 'Tarifa actualizada' : 'Tarifa creada', 'success');
            closeModal('pricingModal');
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
