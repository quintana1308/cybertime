/**
 * CYBERTIME - JavaScript del Cliente
 */

// Estado global
let currentState = {
    pcId: PC_ID,
    pcName: '',
    status: 'bloqueada',
    remainingTime: 0,
    assignedTime: 0,
    sessionId: null,
    clientName: '',
    isConnected: true
};

let localRemainingTime = 0;
let lastSyncTime = Date.now();
let reconnectAttempts = 0;
const MAX_RECONNECT_ATTEMPTS = 10;

// Elementos del DOM
const lockScreen = document.getElementById('lockScreen');
const activeScreen = document.getElementById('activeScreen');
const disconnectScreen = document.getElementById('disconnectScreen');
const timeDisplay = document.getElementById('timeDisplay');
const timeProgress = document.getElementById('timeProgress');
const timePercentage = document.getElementById('timePercentage');
const lowTimeAlert = document.getElementById('lowTimeAlert');
const pcName = document.getElementById('pcName');
const pcNameActive = document.getElementById('pcNameActive');
const clientNameEl = document.getElementById('clientName');
const startTimeEl = document.getElementById('startTime');
const endTimeEl = document.getElementById('endTime');

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    console.log('CyberTime Client iniciado');
    
    // Intentar entrar en pantalla completa
    requestFullscreen();
    
    // Prevenir teclas especiales
    preventSpecialKeys();
    
    // Registrar PC si no tiene ID
    if (!currentState.pcId) {
        registerPC();
    } else {
        startPolling();
    }
    
    // Actualizar contador local cada segundo
    setInterval(updateLocalTime, 1000);
    
    // Enviar heartbeat cada 10 segundos
    setInterval(sendHeartbeat, 10000);
});

/**
 * Registrar PC en el sistema
 */
async function registerPC() {
    try {
        const response = await fetch(`${API_BASE}/client/register.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `ip_address=${encodeURIComponent(window.location.hostname)}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentState.pcId = data.data.pc_id;
            currentState.pcName = data.data.pc_name;
            PC_ID = data.data.pc_id;
            
            // Guardar en localStorage
            localStorage.setItem('cybertime_pc_id', data.data.pc_id);
            
            updatePCName();
            startPolling();
            
            console.log('PC registrada:', data.data.pc_name);
        }
    } catch (error) {
        console.error('Error al registrar PC:', error);
        setTimeout(registerPC, 5000);
    }
}

/**
 * Iniciar polling del estado
 */
function startPolling() {
    checkStatus();
    setInterval(checkStatus, POLLING_INTERVAL);
}

/**
 * Verificar estado de la PC
 */
async function checkStatus() {
    if (!currentState.pcId) return;
    
    try {
        const response = await fetch(`${API_BASE}/client/status.php?pc_id=${currentState.pcId}`);
        const data = await response.json();
        
        if (data.success) {
            handleStatusUpdate(data.data);
            
            // Marcar como conectado
            if (!currentState.isConnected) {
                currentState.isConnected = true;
                hideDisconnectScreen();
                reconnectAttempts = 0;
            }
        }
    } catch (error) {
        console.error('Error al verificar estado:', error);
        handleDisconnection();
    }
}

/**
 * Manejar actualización de estado
 */
function handleStatusUpdate(data) {
    currentState.status = data.status;
    currentState.pcName = data.pc_name;
    currentState.sessionId = data.session_id;
    currentState.clientName = data.client_name;
    
    // Actualizar nombre de PC
    updatePCName();
    
    if (data.is_locked) {
        // Mostrar pantalla de bloqueo
        showLockScreen();
    } else {
        // Mostrar pantalla activa
        currentState.remainingTime = data.remaining_time;
        currentState.assignedTime = data.assigned_time;
        
        // Sincronizar tiempo local
        localRemainingTime = data.remaining_time;
        lastSyncTime = Date.now();
        
        showActiveScreen();
        updateTimeDisplay();
        updateProgress();
        updateSessionInfo();
        checkLowTime();
    }
}

/**
 * Actualizar tiempo local (cada segundo)
 */
function updateLocalTime() {
    if (currentState.status === 'bloqueada' || currentState.status === 'pausada') {
        return;
    }
    
    if (localRemainingTime > 0) {
        localRemainingTime--;
        updateTimeDisplay();
        updateProgress();
        checkLowTime();
        
        // Si llega a 0, bloquear
        if (localRemainingTime <= 0) {
            showLockScreen();
        }
    }
}

/**
 * Actualizar visualización del tiempo
 */
function updateTimeDisplay() {
    const hours = Math.floor(localRemainingTime / 3600);
    const minutes = Math.floor((localRemainingTime % 3600) / 60);
    const seconds = localRemainingTime % 60;
    
    const timeString = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
    timeDisplay.textContent = timeString;
    
    // Cambiar color según tiempo restante
    timeDisplay.classList.remove('warning', 'danger');
    if (localRemainingTime <= 60) {
        timeDisplay.classList.add('danger');
    } else if (localRemainingTime <= WARNING_TIME) {
        timeDisplay.classList.add('warning');
    }
}

/**
 * Actualizar barra de progreso
 */
function updateProgress() {
    if (currentState.assignedTime === 0) return;
    
    const percentage = (localRemainingTime / currentState.assignedTime) * 100;
    timeProgress.style.width = percentage + '%';
    timePercentage.textContent = Math.round(percentage) + '%';
    
    // Cambiar color de la barra
    timeProgress.classList.remove('warning', 'danger');
    if (percentage <= 10) {
        timeProgress.classList.add('danger');
    } else if (percentage <= 25) {
        timeProgress.classList.add('warning');
    }
}

/**
 * Actualizar información de sesión
 */
function updateSessionInfo() {
    const now = new Date();
    const endTime = new Date(now.getTime() + (localRemainingTime * 1000));
    
    startTimeEl.textContent = formatTime(now);
    endTimeEl.textContent = formatTime(endTime);
    
    if (currentState.clientName) {
        clientNameEl.textContent = currentState.clientName;
        clientNameEl.style.display = 'block';
    } else {
        clientNameEl.style.display = 'none';
    }
}

/**
 * Verificar tiempo bajo y mostrar alerta
 */
function checkLowTime() {
    if (localRemainingTime <= WARNING_TIME && localRemainingTime > 0) {
        lowTimeAlert.classList.add('active');
    } else {
        lowTimeAlert.classList.remove('active');
    }
}

/**
 * Mostrar pantalla de bloqueo
 */
function showLockScreen() {
    lockScreen.classList.add('active');
    activeScreen.classList.remove('active');
    lowTimeAlert.classList.remove('active');
}

/**
 * Mostrar pantalla activa
 */
function showActiveScreen() {
    lockScreen.classList.remove('active');
    activeScreen.classList.add('active');
}

/**
 * Mostrar pantalla de desconexión
 */
function showDisconnectScreen() {
    disconnectScreen.classList.add('active');
}

/**
 * Ocultar pantalla de desconexión
 */
function hideDisconnectScreen() {
    disconnectScreen.classList.remove('active');
}

/**
 * Manejar desconexión
 */
function handleDisconnection() {
    if (currentState.isConnected) {
        currentState.isConnected = false;
        showDisconnectScreen();
    }
    
    reconnectAttempts++;
    
    if (reconnectAttempts >= MAX_RECONNECT_ATTEMPTS) {
        console.error('Máximo de intentos de reconexión alcanzado');
        // Podría mostrar un mensaje diferente o recargar la página
    }
}

/**
 * Enviar heartbeat al servidor
 */
async function sendHeartbeat() {
    if (!currentState.pcId || !currentState.isConnected) return;
    
    try {
        await fetch(`${API_BASE}/client/heartbeat.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `pc_id=${currentState.pcId}`
        });
    } catch (error) {
        console.error('Error al enviar heartbeat:', error);
    }
}

/**
 * Actualizar nombre de PC en UI
 */
function updatePCName() {
    if (currentState.pcName) {
        pcName.textContent = currentState.pcName;
        pcNameActive.textContent = currentState.pcName;
    }
}

/**
 * Solicitar pantalla completa
 */
function requestFullscreen() {
    const elem = document.documentElement;
    
    if (elem.requestFullscreen) {
        elem.requestFullscreen().catch(err => {
            console.log('No se pudo entrar en pantalla completa:', err);
        });
    } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
        elem.msRequestFullscreen();
    }
}

/**
 * Prevenir teclas especiales
 */
function preventSpecialKeys() {
    document.addEventListener('keydown', (e) => {
        // Prevenir F11 (pantalla completa)
        if (e.key === 'F11') {
            e.preventDefault();
        }
        
        // Prevenir Alt+Tab, Alt+F4, etc.
        if (e.altKey) {
            e.preventDefault();
        }
        
        // Prevenir Ctrl+W, Ctrl+T, etc.
        if (e.ctrlKey && (e.key === 'w' || e.key === 't' || e.key === 'n')) {
            e.preventDefault();
        }
        
        // Prevenir Windows key
        if (e.key === 'Meta') {
            e.preventDefault();
        }
    });
    
    // Prevenir clic derecho
    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
    });
}

/**
 * Formatear tiempo HH:MM:SS
 */
function formatTime(date) {
    const hours = pad(date.getHours());
    const minutes = pad(date.getMinutes());
    const seconds = pad(date.getSeconds());
    return `${hours}:${minutes}:${seconds}`;
}

/**
 * Agregar ceros a la izquierda
 */
function pad(num) {
    return num.toString().padStart(2, '0');
}

// Detectar si se sale de pantalla completa
document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement) {
        // Intentar volver a pantalla completa
        setTimeout(requestFullscreen, 1000);
    }
});

// Detectar cambio de visibilidad de la página
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        console.log('Página oculta');
    } else {
        console.log('Página visible');
        // Forzar actualización de estado
        checkStatus();
    }
});

console.log('CyberTime Client cargado correctamente');
