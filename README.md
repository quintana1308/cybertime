# 🖥️ CYBERTIME - Sistema de Control de Tiempos para Cyber Café

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)
![MariaDB](https://img.shields.io/badge/MariaDB-10.4+-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Sistema profesional de gestión y control de tiempos para cyber cafés, diseñado para operar en red local (LAN) con arquitectura cliente-servidor.

---

## 📋 DESCRIPCIÓN

**CyberTime** es un sistema completo que permite administrar el tiempo de uso de las computadoras en un cyber café desde una PC principal (servidor), mientras que las PCs clientes se bloquean/desbloquean automáticamente según el tiempo asignado.

### Características Principales

✅ **Control Centralizado**: Gestiona todas las PCs desde un panel de administración  
✅ **Bloqueo Automático**: Las PCs se bloquean cuando no tienen tiempo asignado  
✅ **Tiempo Real**: Actualización de estados cada 2 segundos  
✅ **Alertas Inteligentes**: Notificaciones cuando queda poco tiempo  
✅ **Gestión de Tarifas**: Sistema flexible de precios por tiempo  
✅ **Reportes Financieros**: Estadísticas de ingresos y uso  
✅ **Multi-usuario**: Soporte para administradores y operadores  
✅ **Sin Internet**: Funciona 100% en red local  

---

## 🏗️ ARQUITECTURA

```
┌─────────────────────────────────────────────────────────┐
│                    RED LOCAL (LAN)                       │
│                                                          │
│  ┌────────────────────────────────────────────────┐    │
│  │        PC PRINCIPAL (SERVIDOR)                  │    │
│  │  • Apache + PHP + MariaDB                       │    │
│  │  • Panel de Administración                      │    │
│  │  • API REST                                     │    │
│  │  • IP Estática: 192.168.1.100                   │    │
│  └──────────────────┬──────────────────────────────┘    │
│                     │                                    │
│                     │ HTTP/JSON                          │
│                     │                                    │
│  ┌──────────────────┴──────────────────────────────┐    │
│  │                                                  │    │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐     │    │
│  │  │ PC-01    │  │ PC-02    │  │ PC-03    │ ... │    │
│  │  │ Cliente  │  │ Cliente  │  │ Cliente  │     │    │
│  │  │ Navegador│  │ Navegador│  │ Navegador│     │    │
│  │  └──────────┘  └──────────┘  └──────────┘     │    │
│  └──────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────┘
```

---

## 🚀 INICIO RÁPIDO

### Requisitos Previos

**PC Principal (Servidor):**
- Windows 7+ (64 bits recomendado)
- 4 GB RAM mínimo
- XAMPP 7.4+ instalado
- IP estática configurada

**PCs Clientes:**
- Windows 7+
- 2 GB RAM mínimo
- Navegador moderno (Chrome recomendado)
- Conexión a la misma red local

### Instalación Rápida

1. **Clonar/Descargar el proyecto**
   ```bash
   cd c:\xampp\htdocs\
   # Copiar carpeta cybertime aquí
   ```

2. **Crear base de datos**
   - Abrir phpMyAdmin: http://localhost/phpmyadmin
   - Importar: `database/schema.sql`
   - Importar: `database/seeds.sql`

3. **Configurar sistema**
   - Editar `config.php`
   - Configurar IP del servidor
   - Configurar credenciales de base de datos

4. **Acceder al panel**
   - URL: http://localhost/cybertime/admin/
   - Usuario: `admin`
   - Contraseña: `admin123`

5. **Configurar clientes**
   - En cada PC cliente, abrir navegador
   - Ir a: http://[IP_SERVIDOR]/cybertime/client/
   - Configurar inicio automático

📖 **Documentación Completa**: Ver `docs/INSTALL_SERVER.md` y `docs/INSTALL_CLIENT.md`

---

## 📁 ESTRUCTURA DEL PROYECTO

```
cybertime/
│
├── 📄 config.php                 # Configuración global
├── 📄 index.php                  # Página de inicio
├── 📄 README.md                  # Este archivo
├── 📄 PROJECT_RULES.md           # Reglas del proyecto
├── 📄 TECH_STACK.md              # Stack tecnológico
│
├── 📁 admin/                     # Panel de administración
│   ├── index.php                # Dashboard principal
│   ├── login.php                # Autenticación
│   ├── assets/
│   │   ├── css/                 # Estilos del panel
│   │   └── js/                  # JavaScript del panel
│   └── includes/                # Componentes comunes
│
├── 📁 client/                    # Interfaz de clientes
│   ├── index.php                # Pantalla principal
│   ├── assets/
│   │   ├── css/                 # Estilos del cliente
│   │   └── js/                  # JavaScript del cliente
│   └── lock.php                 # Pantalla de bloqueo
│
├── 📁 api/                       # Endpoints REST
│   ├── admin/                   # APIs de administración
│   │   ├── get_pcs.php         # Listar PCs
│   │   ├── assign_time.php     # Asignar tiempo
│   │   ├── pause_time.php      # Pausar tiempo
│   │   ├── stop_time.php       # Detener tiempo
│   │   └── add_time.php        # Agregar tiempo
│   └── client/                  # APIs de cliente
│       ├── status.php          # Estado de la PC
│       ├── heartbeat.php       # Ping de conexión
│       └── register.php        # Registrar PC
│
├── 📁 database/                  # Base de datos
│   ├── schema.sql               # Estructura
│   ├── seeds.sql                # Datos iniciales
│   └── migrations/              # Migraciones
│
├── 📁 includes/                  # Librerías PHP
│   ├── db.php                   # Conexión a DB
│   ├── functions.php            # Funciones auxiliares
│   ├── auth.php                 # Autenticación
│   └── response.php             # Respuestas JSON
│
├── 📁 logs/                      # Logs del sistema
├── 📁 backups/                   # Respaldos de DB
├── 📁 docs/                      # Documentación
│   ├── INSTALL_SERVER.md        # Instalación servidor
│   ├── INSTALL_CLIENT.md        # Instalación clientes
│   ├── USER_MANUAL.md           # Manual de usuario
│   └── API_DOCS.md              # Documentación API
│
└── 📁 install/                   # Instalador web
    ├── index.php                # Asistente de instalación
    └── check_requirements.php   # Verificar requisitos
```

---

## 💻 TECNOLOGÍAS UTILIZADAS

### Backend
- **PHP 7.4+**: Lenguaje del servidor
- **Apache 2.4+**: Servidor web
- **MariaDB 10.4+**: Base de datos

### Frontend
- **HTML5**: Estructura
- **CSS3**: Estilos (Flexbox, Grid, Variables)
- **JavaScript ES6+**: Interactividad (Vanilla, sin frameworks)

### Comunicación
- **API REST**: Arquitectura de comunicación
- **JSON**: Formato de intercambio de datos
- **Polling HTTP**: Actualización cada 2 segundos

### Herramientas
- **XAMPP**: Entorno de desarrollo
- **phpMyAdmin**: Gestión de base de datos

---

## 🎯 FUNCIONALIDADES

### Panel de Administración (PC Principal)

#### Dashboard
- Vista en tiempo real de todas las PCs
- Estados: Disponible, En uso, Pausada, Mantenimiento
- Tiempo restante de cada PC
- Alertas y notificaciones

#### Gestión de Tiempo
- Asignar tiempo a una PC
- Agregar tiempo adicional
- Pausar/Reanudar tiempo
- Detener tiempo manualmente
- Historial de sesiones

#### Gestión de PCs
- Registrar nuevas PCs
- Editar información de PCs
- Activar/Desactivar PCs
- Ver estadísticas por PC

#### Tarifas y Precios
- Crear tarifas personalizadas
- Precios por tiempo (15min, 30min, 1h, etc.)
- Activar/Desactivar tarifas

#### Reportes
- Ingresos diarios/mensuales
- Tiempo total usado por PC
- Sesiones por día/mes
- Exportar reportes

#### Configuración
- Ajustes del sistema
- Gestión de usuarios
- Configuración de red
- Respaldos de base de datos

### Interfaz de Cliente (PCs del Cyber)

#### Pantalla de Bloqueo
- Mensaje de PC bloqueada
- Logo del cyber
- Información de contacto

#### Pantalla Activa
- Contador de tiempo en grande
- Barra de progreso visual
- Alertas cuando queden 5 minutos
- Información de la sesión

#### Funciones Automáticas
- Desbloqueo al asignar tiempo
- Bloqueo al terminar tiempo
- Reconexión automática
- Sincronización con servidor

---

## 🔒 SEGURIDAD

### Autenticación
- Login con usuario y contraseña
- Contraseñas hasheadas con bcrypt
- Sesiones con timeout de 8 horas
- Regeneración de session_id

### Validación
- Sanitización de inputs
- Prepared statements (prevenir SQL injection)
- Escape de HTML (prevenir XSS)
- Validación de tipos de datos

### Bloqueo de PC
- Overlay fullscreen con z-index máximo
- Prevención de teclas especiales
- Verificación constante del estado
- No se puede eludir desde el cliente

---

## 📊 BASE DE DATOS

### Tablas Principales

- **users**: Usuarios administradores
- **pcs**: Computadoras del cyber
- **sessions**: Sesiones de uso
- **time_logs**: Historial de cambios de tiempo
- **transactions**: Transacciones financieras
- **pricing**: Tarifas de precios
- **settings**: Configuraciones del sistema
- **logs**: Registro de eventos
- **alerts**: Alertas y notificaciones

### Vistas
- `v_active_sessions`: Sesiones activas con info de PC
- `v_pc_stats`: Estadísticas por PC
- `v_daily_revenue`: Ingresos diarios

### Procedimientos
- `sp_cleanup_old_sessions`: Limpiar sesiones antiguas
- `sp_get_daily_stats`: Estadísticas del día
- `sp_expire_sessions`: Finalizar sesiones expiradas

---

## 🔌 API REST

### Endpoints de Administración

```
GET  /api/admin/get_pcs.php
     Obtener lista de todas las PCs

POST /api/admin/assign_time.php
     Asignar tiempo a una PC
     Params: pc_id, time_seconds, client_name (opcional)

POST /api/admin/add_time.php
     Agregar tiempo adicional
     Params: session_id, time_seconds

POST /api/admin/pause_time.php
     Pausar tiempo de una sesión
     Params: session_id

POST /api/admin/stop_time.php
     Detener tiempo manualmente
     Params: session_id
```

### Endpoints de Cliente

```
GET  /api/client/status.php?pc_id=1
     Obtener estado de la PC

POST /api/client/heartbeat.php
     Enviar señal de vida
     Params: pc_id

POST /api/client/register.php
     Registrar nueva PC
     Params: name, ip_address, mac_address
```

### Formato de Respuesta

```json
{
  "success": true,
  "data": {
    "pc_id": 1,
    "status": "en_uso",
    "remaining_time": 1800
  },
  "message": "Estado obtenido correctamente",
  "timestamp": "2024-12-26 12:00:00"
}
```

---

## 🛠️ CONFIGURACIÓN

### config.php

```php
// Base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'cybertime');
define('DB_USER', 'cybertime_user');
define('DB_PASS', 'tu_contraseña_segura');

// Red
define('SERVER_IP', '192.168.1.100');
define('SERVER_PORT', '80');
define('SERVER_URL', 'http://192.168.1.100');

// Sistema
define('TIMEZONE', 'America/Mexico_City');
define('POLLING_INTERVAL', 2);
define('SESSION_TIMEOUT', 28800);
define('MAX_CLIENTS', 50);
```

---

## 📖 DOCUMENTACIÓN

### Guías de Instalación
- 📘 [Instalación en PC Principal](docs/INSTALL_SERVER.md)
- 📗 [Instalación en PCs Clientes](docs/INSTALL_CLIENT.md)

### Documentación Técnica
- 📙 [Reglas del Proyecto](PROJECT_RULES.md)
- 📕 [Stack Tecnológico](TECH_STACK.md)
- 📔 [Documentación de API](docs/API_DOCS.md)

### Manuales de Usuario
- 📓 [Manual de Usuario](docs/USER_MANUAL.md)

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### Problemas Comunes

**Apache no inicia**
```
Solución: Puerto 80 ocupado
1. Cambiar puerto en httpd.conf
2. O detener servicio que usa puerto 80
```

**No se conectan los clientes**
```
Solución: Firewall bloqueando
1. Permitir Apache en firewall
2. Verificar IP del servidor
3. Hacer ping desde cliente
```

**Base de datos no conecta**
```
Solución: Credenciales incorrectas
1. Verificar config.php
2. Probar login en phpMyAdmin
3. Recrear usuario de DB
```

Ver documentación completa en `docs/INSTALL_SERVER.md` sección 10.

---

## 🔄 MANTENIMIENTO

### Diario
- Verificar que todas las PCs estén conectadas
- Revisar alertas pendientes

### Semanal
- Limpiar archivos temporales
- Verificar espacio en disco
- Reiniciar servidor

### Mensual
- Respaldo de base de datos
- Limpiar logs antiguos
- Actualizar sistema operativo
- Revisar estadísticas de uso

### Comandos Útiles

```sql
-- Limpiar sesiones antiguas (30 días)
CALL sp_cleanup_old_sessions(30);

-- Estadísticas del día
CALL sp_get_daily_stats(CURDATE());

-- Finalizar sesiones expiradas
CALL sp_expire_sessions();

-- Ver ingresos del día
SELECT * FROM v_daily_revenue WHERE date = CURDATE();
```

---

## 📈 ROADMAP

### Versión 1.0 (Actual)
- ✅ Control básico de tiempos
- ✅ Panel de administración
- ✅ Bloqueo de PCs
- ✅ Reportes básicos

### Versión 2.0 (Futuro)
- 🔄 WebSockets para tiempo real
- 🔄 PWA (Progressive Web App)
- 🔄 Notificaciones push
- 🔄 Modo offline
- 🔄 App móvil para administración

### Versión 3.0 (Futuro)
- 🔄 Múltiples sucursales
- 🔄 Integración con sistemas de pago
- 🔄 Venta de productos
- 🔄 Sistema de membresías
- 🔄 Análisis avanzado con IA

---

## 🤝 CONTRIBUIR

Este es un proyecto privado para uso interno. Si encuentras bugs o tienes sugerencias:

1. Documenta el problema detalladamente
2. Incluye pasos para reproducir
3. Adjunta capturas de pantalla si aplica
4. Revisa logs de error

---

## 📄 LICENCIA

Copyright © 2024 CyberTime. Todos los derechos reservados.

Este software es propietario y está diseñado para uso exclusivo en cyber cafés.

---

## 📞 SOPORTE

### Logs de Error
- Apache: `C:\xampp\apache\logs\error.log`
- PHP: `C:\xampp\php\logs\php_error_log`
- CyberTime: `C:\xampp\htdocs\cybertime\logs\error.log`

### Información del Sistema
```bash
# Versión de PHP
php -v

# Versión de Apache
httpd -v

# Versión de MariaDB
mysql --version
```

---

## 👥 CRÉDITOS

**Desarrollado por**: Sistema CyberTime  
**Versión**: 1.0.0  
**Fecha**: 2024-12-26  
**Tecnologías**: PHP, Apache, MariaDB, JavaScript

---

## 📝 CHANGELOG

### [1.0.0] - 2024-12-26
#### Agregado
- Sistema completo de control de tiempos
- Panel de administración
- Interfaz de cliente con bloqueo
- API REST completa
- Base de datos con triggers y procedimientos
- Documentación completa
- Sistema de alertas
- Reportes financieros
- Gestión de usuarios
- Sistema de tarifas

---

**¡Gracias por usar CyberTime! 🚀**

Para comenzar, consulta la [Guía de Instalación del Servidor](docs/INSTALL_SERVER.md).
