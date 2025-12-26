# STACK TECNOLÓGICO - CYBERTIME

## 1. RESUMEN EJECUTIVO

**CyberTime** es un sistema de control de tiempos para cyber cafés que opera en red local (LAN) con arquitectura cliente-servidor. El stack ha sido seleccionado priorizando **simplicidad, rendimiento y compatibilidad** con el entorno XAMPP existente.

---

## 2. TECNOLOGÍAS PRINCIPALES

### 2.1 Backend - Servidor (PC Principal)

#### **PHP 7.4+**
- **Propósito**: Lenguaje principal del servidor
- **Justificación**:
  - Nativo en XAMPP, sin instalación adicional
  - Excelente para APIs REST
  - Amplia documentación en español
  - Bajo consumo de recursos
  - Fácil mantenimiento
- **Uso**: Endpoints API, lógica de negocio, gestión de sesiones

#### **Apache 2.4+**
- **Propósito**: Servidor web HTTP
- **Justificación**:
  - Incluido en XAMPP
  - Configuración simple
  - Soporte para .htaccess
  - Estable y probado
- **Uso**: Servir aplicación web y APIs

#### **MariaDB 10.4+**
- **Propósito**: Sistema de gestión de base de datos
- **Justificación**:
  - Incluido en XAMPP
  - Compatible con MySQL
  - Excelente rendimiento
  - Transacciones ACID
  - Soporte para JSON
- **Uso**: Almacenamiento de PCs, sesiones, configuración, logs

---

### 2.2 Frontend - Cliente y Servidor

#### **HTML5**
- **Propósito**: Estructura de páginas
- **Características usadas**:
  - Semantic HTML
  - Local Storage API
  - Fullscreen API
  - Visibility API

#### **CSS3**
- **Propósito**: Estilos y diseño visual
- **Características usadas**:
  - Flexbox y Grid Layout
  - CSS Variables (Custom Properties)
  - Transitions y Animations
  - Media Queries (responsive)
- **Sin frameworks**: CSS puro para máximo control y rendimiento

#### **JavaScript ES6+ (Vanilla)**
- **Propósito**: Interactividad y comunicación con servidor
- **Justificación**:
  - Sin dependencias externas
  - Máximo rendimiento
  - Menor tamaño de carga
  - Control total del código
- **Características usadas**:
  - Fetch API (AJAX)
  - Async/Await
  - Arrow Functions
  - Template Literals
  - Modules (ES6)
  - setInterval/setTimeout
  - Event Listeners

---

## 3. ARQUITECTURA DEL SISTEMA

### 3.1 Diagrama de Componentes

```
┌─────────────────────────────────────────────────────────────┐
│                      RED LOCAL (LAN)                         │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │           PC PRINCIPAL (SERVIDOR)                     │  │
│  │                                                        │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌────────────┐ │  │
│  │  │   Apache     │  │     PHP      │  │  MariaDB   │ │  │
│  │  │   (Puerto    │◄─┤   Backend    │◄─┤  Database  │ │  │
│  │  │    80)       │  │   API REST   │  │            │ │  │
│  │  └──────┬───────┘  └──────────────┘  └────────────┘ │  │
│  │         │                                             │  │
│  │         │  ┌──────────────────────────────────────┐  │  │
│  │         └─►│  Panel Administración (Frontend)     │  │  │
│  │            │  HTML + CSS + JavaScript             │  │  │
│  │            └──────────────────────────────────────┘  │  │
│  └──────────────────────────┬───────────────────────────┘  │
│                              │                              │
│                              │ HTTP/JSON                    │
│                              │                              │
│  ┌───────────────────────────┼──────────────────────────┐  │
│  │                           ▼                          │  │
│  │  ┌─────────────┐    ┌─────────────┐    ┌─────────┐ │  │
│  │  │  PC Cliente │    │  PC Cliente │    │   ...   │ │  │
│  │  │     #1      │    │     #2      │    │         │ │  │
│  │  │             │    │             │    │         │ │  │
│  │  │  Navegador  │    │  Navegador  │    │         │ │  │
│  │  │  (Chrome/   │    │  (Chrome/   │    │         │ │  │
│  │  │   Firefox)  │    │   Firefox)  │    │         │ │  │
│  │  │             │    │             │    │         │ │  │
│  │  │  HTML+CSS+  │    │  HTML+CSS+  │    │         │ │  │
│  │  │     JS      │    │     JS      │    │         │ │  │
│  │  └─────────────┘    └─────────────┘    └─────────┘ │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 Flujo de Comunicación

```
1. PC Cliente solicita estado → GET /api/client/status.php?pc_id=1
2. Servidor consulta DB → SELECT * FROM pcs WHERE id = 1
3. Servidor responde JSON → {success: true, data: {...}}
4. Cliente actualiza UI → Muestra tiempo restante
5. Cada 2 segundos → Repetir proceso
```

---

## 4. ESTRUCTURA DE DIRECTORIOS

```
c:\xampp\htdocs\cybertime\
│
├── index.php                      # Redirección inicial
├── config.php                     # Configuración global
├── PROJECT_RULES.md              # Reglas del proyecto
├── TECH_STACK.md                 # Este documento
├── README.md                      # Documentación principal
│
├── admin/                         # Panel de administración (PC Principal)
│   ├── index.php                 # Dashboard principal
│   ├── login.php                 # Autenticación
│   ├── logout.php                # Cerrar sesión
│   ├── assets/
│   │   ├── css/
│   │   │   └── admin.css        # Estilos del panel
│   │   └── js/
│   │       └── admin.js         # Lógica del panel
│   └── includes/
│       ├── header.php           # Header común
│       └── footer.php           # Footer común
│
├── client/                        # Interfaz para PCs clientes
│   ├── index.php                 # Pantalla principal del cliente
│   ├── assets/
│   │   ├── css/
│   │   │   └── client.css       # Estilos del cliente
│   │   └── js/
│   │       └── client.js        # Lógica del cliente
│   └── lock.php                  # Pantalla de bloqueo
│
├── api/                           # Endpoints REST
│   ├── admin/
│   │   ├── get_pcs.php          # Listar todas las PCs
│   │   ├── assign_time.php      # Asignar tiempo a PC
│   │   ├── pause_time.php       # Pausar tiempo
│   │   ├── stop_time.php        # Detener tiempo
│   │   ├── add_time.php         # Agregar tiempo adicional
│   │   └── update_pc.php        # Actualizar configuración de PC
│   │
│   └── client/
│       ├── status.php           # Obtener estado de la PC
│       ├── heartbeat.php        # Ping de conexión
│       └── register.php         # Registrar nueva PC
│
├── database/
│   ├── schema.sql               # Estructura de la base de datos
│   ├── seeds.sql                # Datos iniciales
│   └── migrations/              # Migraciones futuras
│
├── includes/
│   ├── db.php                   # Conexión a base de datos
│   ├── functions.php            # Funciones auxiliares
│   ├── auth.php                 # Funciones de autenticación
│   └── response.php             # Funciones para respuestas JSON
│
├── logs/                          # Logs del sistema
│   ├── .htaccess                # Denegar acceso web
│   ├── app.log                  # Log de aplicación
│   └── error.log                # Log de errores
│
├── backups/                       # Respaldos de base de datos
│   └── .htaccess                # Denegar acceso web
│
├── docs/                          # Documentación
│   ├── INSTALL_SERVER.md        # Instalación PC Principal
│   ├── INSTALL_CLIENT.md        # Instalación PCs Clientes
│   ├── USER_MANUAL.md           # Manual de usuario
│   └── API_DOCS.md              # Documentación de API
│
└── install/                       # Scripts de instalación
    ├── index.php                # Instalador web
    ├── check_requirements.php   # Verificar requisitos
    └── setup_database.php       # Configurar base de datos
```

---

## 5. DEPENDENCIAS Y REQUISITOS

### 5.1 PC Principal (Servidor)

#### **Software Obligatorio**
- **XAMPP 7.4+** (incluye Apache, PHP, MariaDB)
  - Descarga: https://www.apachefriends.org/
  - Versión recomendada: 8.0.x o superior

#### **Extensiones PHP Requeridas** (incluidas en XAMPP)
- `mysqli` - Conexión a MariaDB
- `json` - Manejo de JSON
- `session` - Gestión de sesiones
- `pdo` - Alternativa para DB
- `mbstring` - Manejo de strings multibyte

#### **Configuración Apache**
- `mod_rewrite` - URLs amigables
- `mod_headers` - Headers CORS si es necesario

#### **Navegador Web**
- Google Chrome 90+ (recomendado)
- Mozilla Firefox 88+
- Microsoft Edge 90+

### 5.2 PCs Clientes

#### **Software Obligatorio**
- **Navegador Web Moderno**:
  - Google Chrome 90+ (recomendado)
  - Mozilla Firefox 88+
  - Microsoft Edge 90+

#### **Características del Navegador Requeridas**
- JavaScript habilitado
- Cookies habilitadas
- Local Storage habilitado
- Fullscreen API soportada

#### **NO se requiere**:
- PHP
- Apache
- Base de datos
- Instalación de software adicional

### 5.3 Infraestructura de Red

#### **Router WiFi**
- DHCP habilitado
- Soporte para 50+ dispositivos
- Velocidad mínima: 10 Mbps

#### **Configuración de Red**
- **PC Principal**: IP estática (ej: 192.168.1.100)
- **PCs Clientes**: IP dinámica (DHCP) o estática

---

## 6. LIBRERÍAS Y COMPONENTES

### 6.1 Backend (PHP)

**NO se utilizan frameworks ni librerías externas**. Todo el código es nativo PHP por las siguientes razones:
- Máximo rendimiento
- Sin dependencias de Composer
- Fácil mantenimiento
- Menor curva de aprendizaje

#### **Funciones PHP Nativas Utilizadas**
- `mysqli_*` - Conexión y queries a DB
- `json_encode/decode` - Manejo de JSON
- `session_*` - Gestión de sesiones
- `password_hash/verify` - Hash de contraseñas
- `file_*` - Manejo de archivos (logs)
- `date/time` - Manejo de fechas

### 6.2 Frontend (JavaScript)

**NO se utilizan frameworks (React, Vue, Angular) ni librerías (jQuery)**. Todo es JavaScript vanilla.

#### **APIs del Navegador Utilizadas**
- **Fetch API**: Peticiones AJAX
  ```javascript
  fetch('/api/client/status.php')
    .then(res => res.json())
    .then(data => console.log(data));
  ```

- **Local Storage**: Almacenamiento local
  ```javascript
  localStorage.setItem('pc_id', '1');
  ```

- **Fullscreen API**: Modo pantalla completa
  ```javascript
  document.documentElement.requestFullscreen();
  ```

- **Visibility API**: Detectar cambio de pestaña
  ```javascript
  document.addEventListener('visibilitychange', handler);
  ```

- **setInterval/setTimeout**: Polling y timers
  ```javascript
  setInterval(checkStatus, 2000);
  ```

### 6.3 Estilos (CSS)

**NO se utilizan frameworks (Bootstrap, Tailwind)**. CSS puro con metodología BEM.

#### **Características CSS3 Utilizadas**
- **Flexbox**: Layout flexible
- **Grid**: Layout en cuadrícula
- **Variables CSS**: Temas y colores
  ```css
  :root {
    --primary-color: #2563eb;
    --danger-color: #dc2626;
  }
  ```
- **Transitions**: Animaciones suaves
- **Media Queries**: Diseño responsive

---

## 7. PROTOCOLOS Y ESTÁNDARES

### 7.1 Comunicación

#### **HTTP/1.1**
- Protocolo de comunicación entre cliente y servidor
- Sin necesidad de HTTPS (red local confiable)
- Puerto: 80 (default) o configurado

#### **JSON (JavaScript Object Notation)**
- Formato de intercambio de datos
- Todas las respuestas API en JSON
- Estructura estándar definida en PROJECT_RULES.md

### 7.2 API REST

#### **Métodos HTTP**
- `GET` - Obtener información
- `POST` - Crear/modificar recursos
- `PUT` - Actualizar recursos (opcional)
- `DELETE` - Eliminar recursos (opcional)

#### **Estructura de URLs**
```
/api/{contexto}/{accion}.php?parametros
```

Ejemplos:
- `GET /api/admin/get_pcs.php`
- `POST /api/admin/assign_time.php`
- `GET /api/client/status.php?pc_id=1`

---

## 8. SEGURIDAD

### 8.1 Autenticación

#### **Sesiones PHP**
- Login con usuario y contraseña
- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Sesiones con timeout de 8 horas
- Regeneración de session_id al login

### 8.2 Validación

#### **Validación de Entrada**
- Sanitización de todos los inputs
- Prepared statements para SQL (prevenir SQL injection)
- Validación de tipos de datos
- Escape de HTML (prevenir XSS)

#### **Validación de Salida**
- Headers de seguridad
- Content-Type correcto
- CORS configurado si es necesario

### 8.3 Bloqueo de PC

#### **Método de Bloqueo**
- Overlay HTML fullscreen con `z-index: 999999`
- `pointer-events: none` en contenido bloqueado
- Prevención de teclas especiales vía JavaScript
- Verificación constante del estado de bloqueo

---

## 9. RENDIMIENTO Y OPTIMIZACIÓN

### 9.1 Backend

#### **Optimizaciones de Base de Datos**
- Índices en campos de búsqueda frecuente
- Queries optimizadas (evitar SELECT *)
- Conexiones persistentes
- Caché de configuración en memoria

#### **Optimizaciones de PHP**
- OPcache habilitado
- Sesiones en archivos (no DB)
- Compresión de salida (gzip)
- Lazy loading de recursos

### 9.2 Frontend

#### **Optimizaciones de Carga**
- CSS y JS minificados en producción
- Imágenes optimizadas (WebP, compresión)
- Lazy loading de imágenes
- Caché del navegador configurado

#### **Optimizaciones de Ejecución**
- Debouncing de eventos
- Throttling de polling
- Uso eficiente de DOM
- Event delegation

---

## 10. COMPATIBILIDAD

### 10.1 Sistemas Operativos

#### **PC Principal (Servidor)**
- Windows 7, 8, 10, 11 (32/64 bits)
- Windows Server 2012+

#### **PCs Clientes**
- Windows 7, 8, 10, 11
- Linux (Ubuntu, Debian, etc.)
- macOS 10.12+

### 10.2 Navegadores

#### **Soporte Completo**
- Chrome 90+
- Firefox 88+
- Edge 90+
- Opera 76+

#### **Soporte Parcial**
- Safari 14+ (macOS)
- Internet Explorer 11 (limitado)

---

## 11. ESCALABILIDAD

### 11.1 Límites Actuales
- **Máximo de PCs**: 50 clientes simultáneos
- **Polling**: 2 segundos por cliente
- **Requests/segundo**: ~25 (50 clientes / 2 segundos)

### 11.2 Mejoras Futuras

#### **WebSockets** (Fase 2)
- Comunicación bidireccional en tiempo real
- Eliminar polling, reducir carga
- Librería: Ratchet (PHP) o Socket.IO

#### **Caché Redis** (Fase 3)
- Caché de sesiones y configuración
- Reducir carga en MariaDB
- Mejora de rendimiento 10x

#### **Load Balancer** (Fase 4)
- Para más de 100 PCs
- Múltiples servidores backend
- Alta disponibilidad

---

## 12. JUSTIFICACIÓN DE DECISIONES TÉCNICAS

### 12.1 ¿Por qué PHP y no Node.js?

| Criterio | PHP | Node.js |
|----------|-----|---------|
| **Disponibilidad** | ✅ Incluido en XAMPP | ❌ Instalación adicional |
| **Curva de aprendizaje** | ✅ Más simple | ❌ Más complejo |
| **Documentación en español** | ✅ Abundante | ⚠️ Limitada |
| **Consumo de recursos** | ✅ Bajo | ⚠️ Medio |
| **Mantenimiento** | ✅ Fácil | ⚠️ Requiere npm |

### 12.2 ¿Por qué JavaScript Vanilla y no React/Vue?

| Criterio | Vanilla JS | React/Vue |
|----------|------------|-----------|
| **Tamaño** | ✅ ~10KB | ❌ ~100KB+ |
| **Dependencias** | ✅ Ninguna | ❌ npm, build tools |
| **Rendimiento** | ✅ Máximo | ⚠️ Overhead de framework |
| **Complejidad** | ✅ Simple | ❌ Complejo |
| **Tiempo de carga** | ✅ Instantáneo | ⚠️ Varios segundos |

### 12.3 ¿Por qué MariaDB y no SQLite?

| Criterio | MariaDB | SQLite |
|----------|---------|--------|
| **Concurrencia** | ✅ Excelente | ❌ Limitada |
| **Transacciones** | ✅ ACID completo | ⚠️ ACID básico |
| **Escalabilidad** | ✅ 50+ clientes | ❌ <10 clientes |
| **Disponibilidad** | ✅ En XAMPP | ⚠️ Instalación adicional |
| **Herramientas** | ✅ phpMyAdmin | ⚠️ Limitadas |

---

## 13. ROADMAP TECNOLÓGICO

### Versión 1.0 (Actual)
- ✅ PHP + Apache + MariaDB
- ✅ JavaScript Vanilla
- ✅ Polling HTTP cada 2 segundos
- ✅ Hasta 50 PCs

### Versión 2.0 (Futuro)
- 🔄 WebSockets para tiempo real
- 🔄 PWA (Progressive Web App)
- 🔄 Notificaciones push
- 🔄 Modo offline

### Versión 3.0 (Futuro)
- 🔄 API GraphQL
- 🔄 Caché Redis
- 🔄 Microservicios
- 🔄 Docker containers

---

## 14. RECURSOS Y REFERENCIAS

### 14.1 Documentación Oficial
- PHP: https://www.php.net/manual/es/
- MariaDB: https://mariadb.com/kb/es/
- JavaScript MDN: https://developer.mozilla.org/es/
- Apache: https://httpd.apache.org/docs/

### 14.2 Herramientas de Desarrollo
- **Editor de Código**: Visual Studio Code, Sublime Text, PHPStorm
- **Cliente MySQL**: phpMyAdmin (incluido en XAMPP), HeidiSQL
- **Testing API**: Postman, Insomnia
- **Debugging**: Xdebug (PHP), Chrome DevTools (JS)

### 14.3 Comunidades
- Stack Overflow en Español
- Foros de PHP en español
- Comunidad XAMPP

---

**Versión**: 1.0.0  
**Fecha**: 2024-12-26  
**Autor**: Sistema CyberTime  
**Última Actualización**: 2024-12-26
