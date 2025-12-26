/**
 * CYBERTIME - JavaScript del Panel de Administración
 */

// Estado global
let pricingOptions = [];
let currentPCs = [];

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    console.log('CyberTime Admin iniciado');
    
    // Actualizar reloj
    updateClock();
    setInterval(updateClock, 1000);
    
    // Cargar tarifas
    loadPricing();
});

/**
 * Actualizar reloj en header
 */
function updateClock() {
    const clockEl = document.getElementById('currentTime');
    if (!clockEl) return;
    
    const now = new Date();
    const hours = pad(now.getHours());
    const minutes = pad(now.getMinutes());
    const seconds = pad(now.getSeconds());
    
    clockEl.textContent = `${hours}:${minutes}:${seconds}`;
}

/**
 * Refrescar lista de PCs
 */
async function refreshPCs() {
    try {
        const response = await fetch(`${API_BASE}/admin/get_pcs.php`);
        const data = await response.json();
        
        if (data.success) {
            currentPCs = data.data;
            updatePCsGrid(data.data);
        } else {
            showNotification('Error al actualizar PCs', 'error');
        }
    } catch (error) {
        console.error('Error al refrescar PCs:', error);
        showNotification('Error de conexión', 'error');
    }
}

/**
 * Actualizar grid de PCs
 */
function updatePCsGrid(pcs) {
    const grid = document.getElementById('pcsGrid');
    if (!grid) return;
    
    // Actualizar cada tarjeta existente
    pcs.forEach(pc => {
        const card = grid.querySelector(`[data-pc-id="${pc.id}"]`);
        if (card) {
            updatePCCard(card, pc);
        }
    });
}

/**
 * Actualizar tarjeta de PC individual
 */
function updatePCCard(card, pc) {
    // Actualizar estado
    card.className = `pc-card pc-${pc.status}`;
    
    const statusBadge = card.querySelector('.pc-status-badge');
    if (statusBadge) {
        statusBadge.className = `pc-status-badge status-${pc.status}`;
        statusBadge.textContent = ucfirst(pc.status);
    }
    
    // Actualizar tiempo si está en uso
    if (pc.session_id && pc.remaining_time) {
        const timeDisplay = card.querySelector('.time-display');
        if (timeDisplay) {
            timeDisplay.textContent = formatTime(pc.remaining_time);
        }
        
        const progressBar = card.querySelector('.progress-bar');
        if (progressBar && pc.assigned_time) {
            const percentage = (pc.remaining_time / pc.assigned_time) * 100;
            progressBar.style.width = percentage + '%';
        }
    }
}

/**
 * Actualizar displays de tiempo en todas las tarjetas
 */
function updateTimeDisplays() {
    currentPCs.forEach(pc => {
        if (pc.session_id && pc.status === 'en_uso') {
            const card = document.querySelector(`[data-pc-id="${pc.id}"]`);
            if (!card) return;
            
            const timeDisplay = card.querySelector('.time-display');
            if (!timeDisplay) return;
            
            const currentTime = timeDisplay.dataset.remaining;
            if (currentTime && currentTime > 0) {
                const newTime = parseInt(currentTime) - 1;
                timeDisplay.dataset.remaining = newTime;
                timeDisplay.textContent = formatTime(newTime);
                
                // Actualizar barra de progreso
                const progressBar = card.querySelector('.progress-bar');
                if (progressBar && pc.assigned_time) {
                    const percentage = (newTime / pc.assigned_time) * 100;
                    progressBar.style.width = percentage + '%';
                }
            }
        }
    });
}

/**
 * Asignar tiempo a una PC
 */
async function assignTime(pcId) {
    const pc = currentPCs.find(p => p.id === pcId);
    if (!pc) return;
    
    // Abrir modal
    openModal('assignTimeModal');
    
    // Establecer PC ID
    document.getElementById('assign_pc_id').value = pcId;
    
    // Limpiar formulario
    document.getElementById('client_name').value = '';
    document.getElementById('custom_minutes').value = '';
    
    // Cargar opciones de tarifas
    loadPricingOptions();
}

/**
 * Cargar tarifas
 */
async function loadPricing() {
    try {
        const response = await fetch(`${API_BASE}/admin/get_pricing.php`);
        const data = await response.json();
        
        if (data.success) {
            pricingOptions = data.data;
        }
    } catch (error) {
        console.error('Error al cargar tarifas:', error);
    }
}

/**
 * Cargar opciones de tarifas en modal
 */
function loadPricingOptions() {
    const container = document.getElementById('pricingOptions');
    if (!container) return;
    
    container.innerHTML = '';
    
    pricingOptions.forEach(pricing => {
        const option = document.createElement('div');
        option.className = 'pricing-option';
        option.dataset.minutes = pricing.minutes;
        option.innerHTML = `
            <div class="price">$${pricing.price}</div>
            <div class="time">${pricing.name}</div>
        `;
        
        option.addEventListener('click', () => {
            // Deseleccionar otros
            container.querySelectorAll('.pricing-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Seleccionar este
            option.classList.add('selected');
            
            // Limpiar minutos personalizados
            document.getElementById('custom_minutes').value = '';
        });
        
        container.appendChild(option);
    });
}

/**
 * Procesar formulario de asignación de tiempo
 */
document.getElementById('assignTimeForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const pcId = document.getElementById('assign_pc_id').value;
    const clientName = document.getElementById('client_name').value;
    const customMinutes = document.getElementById('custom_minutes').value;
    
    // Obtener minutos seleccionados
    let minutes = customMinutes;
    if (!minutes) {
        const selectedOption = document.querySelector('.pricing-option.selected');
        if (selectedOption) {
            minutes = selectedOption.dataset.minutes;
        }
    }
    
    if (!minutes || minutes < 1) {
        showNotification('Selecciona una tarifa o ingresa minutos', 'error');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('pc_id', pcId);
        formData.append('time_minutes', minutes);
        if (clientName) {
            formData.append('client_name', clientName);
        }
        
        const response = await fetch(`${API_BASE}/admin/assign_time.php`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Tiempo asignado correctamente', 'success');
            closeModal('assignTimeModal');
            refreshPCs();
        } else {
            showNotification(data.message || 'Error al asignar tiempo', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    }
});

/**
 * Agregar tiempo adicional
 */
async function addTime(sessionId) {
    openModal('addTimeModal');
    document.getElementById('add_session_id').value = sessionId;
    document.getElementById('add_minutes').value = '';
}

/**
 * Procesar formulario de agregar tiempo
 */
document.getElementById('addTimeForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const sessionId = document.getElementById('add_session_id').value;
    const minutes = document.getElementById('add_minutes').value;
    
    if (!minutes || minutes < 1) {
        showNotification('Ingresa minutos válidos', 'error');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('session_id', sessionId);
        formData.append('time_minutes', minutes);
        
        const response = await fetch(`${API_BASE}/admin/add_time.php`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Tiempo agregado correctamente', 'success');
            closeModal('addTimeModal');
            refreshPCs();
        } else {
            showNotification(data.message || 'Error al agregar tiempo', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    }
});

/**
 * Pausar tiempo
 */
async function pauseTime(sessionId) {
    if (!confirm('¿Pausar esta sesión?')) return;
    
    try {
        const formData = new FormData();
        formData.append('session_id', sessionId);
        
        const response = await fetch(`${API_BASE}/admin/pause_time.php`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Sesión pausada', 'success');
            refreshPCs();
        } else {
            showNotification(data.message || 'Error al pausar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    }
}

/**
 * Reanudar tiempo (igual que pausar, pero desde estado pausado)
 */
async function resumeTime(sessionId) {
    // Por implementar en API
    showNotification('Función en desarrollo', 'warning');
}

/**
 * Detener tiempo
 */
async function stopTime(sessionId) {
    if (!confirm('¿Detener esta sesión? El tiempo restante se perderá.')) return;
    
    try {
        const formData = new FormData();
        formData.append('session_id', sessionId);
        
        const response = await fetch(`${API_BASE}/admin/stop_time.php`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Sesión detenida', 'success');
            refreshPCs();
        } else {
            showNotification(data.message || 'Error al detener', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    }
}

/**
 * Abrir modal
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

/**
 * Cerrar modal
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

/**
 * Cerrar modal al hacer clic fuera
 */
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});

/**
 * Mostrar notificación
 */
function showNotification(message, type = 'info') {
    // Crear notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    // Eliminar después de 3 segundos
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Formatear tiempo en HH:MM:SS
 */
function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    
    return `${pad(hours)}:${pad(minutes)}:${pad(secs)}`;
}

/**
 * Agregar ceros a la izquierda
 */
function pad(num) {
    return num.toString().padStart(2, '0');
}

/**
 * Primera letra en mayúscula
 */
function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Agregar estilos de animación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

console.log('CyberTime Admin JS cargado correctamente');
