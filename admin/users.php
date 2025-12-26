<?php
/**
 * CYBERTIME - Gestión de Usuarios
 */

session_start();
define('ADMIN_PAGE', true);

require_once __DIR__ . '/../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/auth.php';

require_auth();
require_admin(); // Solo administradores

$page_title = 'Gestión de Usuarios';
$current_page = 'users';

// Obtener todos los usuarios
try {
    $db = get_db_connection();
    
    $stmt = $db->query("
        SELECT 
            u.*,
            COUNT(DISTINCT s.id) as total_sessions_created
        FROM users u
        LEFT JOIN sessions s ON u.id = s.created_by
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ");
    
    $users = $stmt->fetchAll();
    
} catch (Exception $e) {
    log_message('ERROR', 'Error al obtener usuarios: ' . $e->getMessage());
    $users = [];
}

include 'includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-users"></i> Gestión de Usuarios</h2>
    <button class="btn btn-primary" onclick="openAddUserModal()">
        <i class="fas fa-user-plus"></i> Agregar Usuario
    </button>
</div>

<!-- Grid de Usuarios -->
<div class="users-grid">
    <?php foreach ($users as $user): ?>
        <div class="user-card <?php echo $user['is_active'] ? '' : 'inactive'; ?>">
            <div class="user-header">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-info">
                    <h3><?php echo escape_html($user['full_name']); ?></h3>
                    <p class="username">@<?php echo escape_html($user['username']); ?></p>
                </div>
                <?php if (!$user['is_active']): ?>
                    <span class="badge badge-inactive">Inactivo</span>
                <?php endif; ?>
            </div>
            
            <div class="user-body">
                <div class="user-detail">
                    <i class="fas fa-envelope"></i>
                    <span><?php echo escape_html($user['email'] ?? 'Sin email'); ?></span>
                </div>
                
                <div class="user-detail">
                    <i class="fas fa-shield-alt"></i>
                    <span class="badge badge-<?php echo $user['role']; ?>">
                        <?php echo $user['role'] === 'admin' ? 'Administrador' : 'Operador'; ?>
                    </span>
                </div>
                
                <div class="user-detail">
                    <i class="fas fa-clock"></i>
                    <span>
                        <?php 
                        if ($user['last_login']) {
                            echo 'Último login: ' . format_datetime($user['last_login']);
                        } else {
                            echo 'Nunca ha iniciado sesión';
                        }
                        ?>
                    </span>
                </div>
                
                <div class="user-detail">
                    <i class="fas fa-chart-line"></i>
                    <span><?php echo $user['total_sessions_created']; ?> sesiones creadas</span>
                </div>
            </div>
            
            <div class="user-footer">
                <div class="user-actions">
                    <button class="btn btn-sm btn-info" onclick="viewUser(<?php echo $user['id']; ?>)" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" onclick="editUser(<?php echo $user['id']; ?>)" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="changePassword(<?php echo $user['id']; ?>)" title="Cambiar contraseña">
                        <i class="fas fa-key"></i>
                    </button>
                    <?php if ($user['is_active']): ?>
                        <button class="btn btn-sm btn-danger" onclick="toggleUser(<?php echo $user['id']; ?>, 0)" title="Desactivar">
                            <i class="fas fa-ban"></i>
                        </button>
                    <?php else: ?>
                        <button class="btn btn-sm btn-success" onclick="toggleUser(<?php echo $user['id']; ?>, 1)" title="Activar">
                            <i class="fas fa-check"></i>
                        </button>
                    <?php endif; ?>
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo escape_html($user['username']); ?>')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="fas fa-users fa-3x"></i>
            <p>No hay usuarios registrados</p>
            <button class="btn btn-primary" onclick="openAddUserModal()">
                <i class="fas fa-user-plus"></i> Agregar Primer Usuario
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Ver Usuario -->
<div id="viewUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Detalles del Usuario</h2>
            <button class="modal-close" onclick="closeModal('viewUserModal')">&times;</button>
        </div>
        <div class="modal-body" id="userDetailsContent">
            <!-- Se cargará dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal Agregar/Editar Usuario -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="userModalTitle">Agregar Usuario</h2>
            <button class="modal-close" onclick="closeModal('userModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="userForm">
                <input type="hidden" id="user_id" name="user_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="user_username">Usuario *</label>
                        <input type="text" id="user_username" name="username" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="user_full_name">Nombre Completo *</label>
                        <input type="text" id="user_full_name" name="full_name" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="user_email">Email</label>
                        <input type="email" id="user_email" name="email" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="user_role">Rol *</label>
                        <select id="user_role" name="role" class="form-control" required>
                            <option value="operator">Operador</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" id="passwordGroup">
                    <label for="user_password">Contraseña *</label>
                    <input type="password" id="user_password" name="password" class="form-control">
                    <small class="form-text">Mínimo 6 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="user_is_active" name="is_active" checked>
                        <span>Usuario Activo</span>
                    </label>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('userModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Activar/Desactivar -->
<div id="toggleUserModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2 id="toggleUserTitle">Confirmar Acción</h2>
            <button class="modal-close" onclick="closeModal('toggleUserModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p id="toggleUserMessage"></p>
            <input type="hidden" id="toggle_user_id">
            <input type="hidden" id="toggle_user_action">
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('toggleUserModal')">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmToggleUserBtn" onclick="confirmToggleUser()">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminar -->
<div id="deleteUserModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2>Confirmar Eliminación</h2>
            <button class="modal-close" onclick="closeModal('deleteUserModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <p id="deleteUserMessage"></p>
            <p class="warning-text">Esta acción no se puede deshacer.</p>
            <input type="hidden" id="delete_user_id">
            <input type="hidden" id="delete_user_name">
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteUserModal')">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteUser()">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Contraseña -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Cambiar Contraseña</h2>
            <button class="modal-close" onclick="closeModal('passwordModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="passwordForm">
                <input type="hidden" id="password_user_id" name="user_id">
                
                <div class="form-group">
                    <label for="new_password">Nueva Contraseña *</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
                    <small class="form-text">Mínimo 6 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="6">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('passwordModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Cambiar Contraseña
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

.page-header h2 {
    display: flex;
    align-items: center;
    gap: 10px;
}

.users-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.user-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.user-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    border-color: var(--primary-color);
}

.user-card.inactive {
    opacity: 0.6;
    background: var(--light-color);
}

.user-header {
    padding: 20px;
    background: linear-gradient(135deg, var(--primary-color), #667eea);
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
}

.user-avatar {
    font-size: 48px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
}

.user-info h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.username {
    margin: 5px 0 0 0;
    font-size: 14px;
    opacity: 0.9;
}

.user-body {
    padding: 20px;
}

.user-detail {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
    font-size: 14px;
}

.user-detail:last-child {
    border-bottom: none;
}

.user-detail i {
    width: 20px;
    color: var(--primary-color);
}

.user-footer {
    padding: 15px 20px;
    background: var(--light-color);
    border-top: 1px solid var(--border-color);
}

.user-actions {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
    justify-content: center;
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

.user-details {
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

.badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-admin {
    background: #fef3c7;
    color: #92400e;
}

.badge-operator {
    background: #dbeafe;
    color: #1e40af;
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

.form-text {
    display: block;
    margin-top: 5px;
    color: var(--gray-color);
    font-size: 13px;
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

function viewUser(userId) {
    fetch(`${API_BASE}/admin/get_user.php?id=${userId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const user = data.data;
                const content = `
                    <div class="user-details">
                        <div class="detail-row">
                            <strong>Usuario:</strong> ${user.username}
                        </div>
                        <div class="detail-row">
                            <strong>Nombre Completo:</strong> ${user.full_name}
                        </div>
                        <div class="detail-row">
                            <strong>Email:</strong> ${user.email || 'No registrado'}
                        </div>
                        <div class="detail-row">
                            <strong>Rol:</strong> <span class="badge badge-${user.role}">${user.role === 'admin' ? 'Administrador' : 'Operador'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Estado:</strong> ${user.is_active ? '<span class="text-success">Activo</span>' : '<span class="text-danger">Inactivo</span>'}
                        </div>
                        <div class="detail-row">
                            <strong>Último Login:</strong> ${user.last_login || 'Nunca'}
                        </div>
                        <div class="detail-row">
                            <strong>Creado:</strong> ${user.created_at}
                        </div>
                    </div>
                `;
                document.getElementById('userDetailsContent').innerHTML = content;
                openModal('viewUserModal');
            }
        })
        .catch(err => {
            showNotification('Error al cargar usuario', 'error');
        });
}

function openAddUserModal() {
    document.getElementById('userModalTitle').textContent = 'Agregar Usuario';
    document.getElementById('userForm').reset();
    document.getElementById('user_id').value = '';
    document.getElementById('user_is_active').checked = true;
    document.getElementById('user_password').required = true;
    document.getElementById('passwordGroup').style.display = 'block';
    openModal('userModal');
}

function editUser(userId) {
    fetch(`${API_BASE}/admin/get_user.php?id=${userId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const user = data.data;
                document.getElementById('userModalTitle').textContent = 'Editar Usuario';
                document.getElementById('user_id').value = user.id;
                document.getElementById('user_username').value = user.username;
                document.getElementById('user_full_name').value = user.full_name;
                document.getElementById('user_email').value = user.email || '';
                document.getElementById('user_role').value = user.role;
                document.getElementById('user_is_active').checked = user.is_active == 1;
                document.getElementById('user_password').required = false;
                document.getElementById('user_password').value = '';
                document.getElementById('passwordGroup').style.display = 'none';
                openModal('userModal');
            }
        })
        .catch(err => {
            showNotification('Error al cargar usuario', 'error');
        });
}

function changePassword(userId) {
    document.getElementById('password_user_id').value = userId;
    document.getElementById('passwordForm').reset();
    document.getElementById('password_user_id').value = userId;
    openModal('passwordModal');
}

function toggleUser(userId, activate) {
    const action = activate ? 'activar' : 'desactivar';
    
    document.getElementById('toggleUserTitle').textContent = activate ? 'Activar Usuario' : 'Desactivar Usuario';
    document.getElementById('toggleUserMessage').textContent = `¿Estás seguro de ${action} este usuario?`;
    document.getElementById('toggle_user_id').value = userId;
    document.getElementById('toggle_user_action').value = activate;
    
    const confirmBtn = document.getElementById('confirmToggleUserBtn');
    confirmBtn.className = activate ? 'btn btn-success' : 'btn btn-warning';
    
    openModal('toggleUserModal');
}

function confirmToggleUser() {
    const userId = document.getElementById('toggle_user_id').value;
    const activate = document.getElementById('toggle_user_action').value;
    const action = activate == '1' ? 'activar' : 'desactivar';
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('is_active', activate);
    
    fetch(`${API_BASE}/admin/toggle_user.php`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification(`Usuario ${action}do correctamente`, 'success');
            closeModal('toggleUserModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    });
}

function deleteUser(userId, username) {
    document.getElementById('deleteUserMessage').textContent = `¿Estás seguro de eliminar al usuario "${username}"?`;
    document.getElementById('delete_user_id').value = userId;
    document.getElementById('delete_user_name').value = username;
    
    openModal('deleteUserModal');
}

function confirmDeleteUser() {
    const userId = document.getElementById('delete_user_id').value;
    
    const formData = new FormData();
    formData.append('user_id', userId);
    
    fetch(`${API_BASE}/admin/delete_user.php`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Usuario eliminado correctamente', 'success');
            closeModal('deleteUserModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error al eliminar usuario', 'error');
        }
    })
    .catch(err => {
        showNotification('Error de conexión', 'error');
    });
}

// Procesar formulario de usuario
document.getElementById('userForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const userId = document.getElementById('user_id').value;
    const url = userId ? `${API_BASE}/admin/update_user.php` : `${API_BASE}/admin/create_user.php`;
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(userId ? 'Usuario actualizado' : 'Usuario creado', 'success');
            closeModal('userModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    } catch (error) {
        showNotification('Error de conexión', 'error');
    }
});

// Procesar formulario de cambio de contraseña
document.getElementById('passwordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        showNotification('Las contraseñas no coinciden', 'error');
        return;
    }
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch(`${API_BASE}/admin/change_password.php`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Contraseña cambiada correctamente', 'success');
            closeModal('passwordModal');
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    } catch (error) {
        showNotification('Error de conexión', 'error');
    }
});
</script>

<?php include 'includes/footer.php'; ?>
