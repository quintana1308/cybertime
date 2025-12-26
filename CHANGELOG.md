# Changelog - CyberTime

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/lang/es/).

---

## [1.0.0] - 2024-12-26

### 🎉 Lanzamiento Inicial

Primera versión estable del sistema CyberTime.

### ✨ Agregado

#### Core del Sistema
- Sistema completo de control de tiempos para cyber cafés
- Arquitectura cliente-servidor en red local (LAN)
- Comunicación mediante HTTP Polling cada 2 segundos
- Base de datos MariaDB con 9 tablas principales

#### Panel de Administración
- Dashboard con vista en tiempo real de todas las PCs
- Sistema de autenticación con roles (admin/operador)
- Asignación de tiempo a PCs
- Agregar tiempo adicional a sesiones activas
- Pausar/Reanudar sesiones
- Detener sesiones manualmente
- Vista de estadísticas (PCs en uso, disponibles, ingresos)
- Gestión de PCs (registrar, editar, activar/desactivar)
- Sistema de tarifas personalizables
- Reportes de ingresos y uso

#### Interfaz de Cliente
- Pantalla de bloqueo automática
- Contador de tiempo en tiempo real
- Barra de progreso visual
- Alertas cuando quedan 5 minutos
- Pantalla de desconexión con reconexión automática
- Registro automático de nuevas PCs
- Heartbeat cada 10 segundos
- Prevención de teclas especiales (F11, Alt+Tab, etc.)
- Modo pantalla completa automático

#### API REST
- `GET /api/admin/get_pcs.php` - Listar PCs
- `POST /api/admin/assign_time.php` - Asignar tiempo
- `POST /api/admin/add_time.php` - Agregar tiempo
- `POST /api/admin/pause_time.php` - Pausar sesión
- `POST /api/admin/stop_time.php` - Detener sesión
- `GET /api/admin/get_pricing.php` - Obtener tarifas
- `GET /api/client/status.php` - Estado de PC cliente
- `POST /api/client/heartbeat.php` - Señal de vida
- `POST /api/client/register.php` - Registrar PC

#### Base de Datos
- Tabla `users` - Usuarios administradores
- Tabla `pcs` - Computadoras del cyber
- Tabla `sessions` - Sesiones de uso
- Tabla `time_logs` - Historial de cambios de tiempo
- Tabla `transactions` - Transacciones financieras
- Tabla `pricing` - Tarifas de precios
- Tabla `settings` - Configuraciones del sistema
- Tabla `logs` - Registro de eventos
- Tabla `alerts` - Alertas y notificaciones
- 3 vistas optimizadas (active_sessions, pc_stats, daily_revenue)
- 4 triggers automáticos
- 3 procedimientos almacenados
- 2 eventos programados

#### Seguridad
- Contraseñas hasheadas con bcrypt
- Prepared statements (prevención SQL injection)
- Escape de HTML (prevención XSS)
- Sesiones con timeout de 8 horas
- Validación de todos los inputs
- Bloqueo de PC imposible de eludir desde cliente

#### Documentación
- README.md completo
- PROJECT_RULES.md con reglas del proyecto
- TECH_STACK.md con stack tecnológico detallado
- PROPUESTA_DESARROLLO.md con propuesta completa
- INSTALL_SERVER.md con instalación paso a paso del servidor
- INSTALL_CLIENT.md con instalación paso a paso de clientes
- INSTALLATION_GUIDE.md con guía rápida
- CHANGELOG.md (este archivo)

#### Instalador
- Instalador web paso a paso
- Verificación de requisitos
- Configuración automática de base de datos
- Importación automática de schema y seeds

#### Datos Iniciales
- Usuario admin (admin/admin123)
- Usuario operador (operador/operador123)
- 10 PCs de ejemplo
- 7 tarifas predefinidas
- 30+ configuraciones del sistema

### 🎨 Diseño

#### Estilos
- CSS puro sin frameworks
- Variables CSS para temas
- Diseño responsive
- Animaciones suaves
- Gradientes modernos
- Iconos emoji nativos

#### Interfaz Admin
- Sidebar con navegación
- Grid de PCs con tarjetas
- Modales para acciones
- Notificaciones toast
- Estadísticas visuales
- Colores por estado

#### Interfaz Cliente
- Pantalla de bloqueo fullscreen
- Contador grande y legible
- Barra de progreso animada
- Alertas visuales
- Diseño minimalista

### 🔧 Configuración

#### Archivos de Configuración
- `config.php` - Configuración global
- `.htaccess` - Configuración Apache
- `.gitignore` - Archivos a ignorar

#### Configuraciones del Sistema
- Zona horaria
- Intervalo de polling (2 segundos)
- Timeout de sesión (8 horas)
- Tiempo de advertencia (5 minutos)
- Máximo de clientes (50 PCs)
- Nivel de logging
- Retención de logs (30 días)
- Respaldos automáticos

### 📊 Características Técnicas

#### Performance
- Polling optimizado cada 2 segundos
- Actualización local del contador cada segundo
- Queries SQL optimizadas con índices
- Caché de configuración
- Compresión GZIP
- Assets minificables

#### Compatibilidad
- PHP 7.4+
- Apache 2.4+
- MariaDB 10.4+
- Chrome 90+
- Firefox 88+
- Edge 90+
- Windows 7, 8, 10, 11

#### Escalabilidad
- Hasta 50 PCs simultáneas
- ~25 requests/segundo con 50 PCs
- Uso de CPU: ~12.5%
- Base de datos optimizada

### 📝 Notas

- Sistema diseñado para operar 100% en red local
- No requiere internet para funcionar
- Basado en XAMPP para fácil instalación
- Sin dependencias de frameworks externos
- Código completamente en español
- Documentación completa incluida

---

## [Unreleased] - Futuras Versiones

### 🔮 Planeado para v2.0

#### Comunicación en Tiempo Real
- [ ] Implementar WebSockets
- [ ] Eliminar polling HTTP
- [ ] Notificaciones push

#### Progressive Web App
- [ ] Convertir a PWA
- [ ] Modo offline
- [ ] Instalable en dispositivos

#### App Móvil
- [ ] App para administración remota
- [ ] Notificaciones móviles
- [ ] Control desde smartphone

### 🔮 Planeado para v3.0

#### Funcionalidades Avanzadas
- [ ] Múltiples sucursales
- [ ] Venta de productos (snacks, bebidas)
- [ ] Sistema de membresías
- [ ] Integración con sistemas de pago
- [ ] Análisis avanzado con IA
- [ ] Reportes exportables (PDF, Excel)

#### Mejoras Técnicas
- [ ] API GraphQL
- [ ] Caché Redis
- [ ] Microservicios
- [ ] Docker containers
- [ ] CI/CD pipeline

---

## Tipos de Cambios

- **Added** (Agregado): Para nuevas funcionalidades
- **Changed** (Cambiado): Para cambios en funcionalidades existentes
- **Deprecated** (Obsoleto): Para funcionalidades que serán eliminadas
- **Removed** (Eliminado): Para funcionalidades eliminadas
- **Fixed** (Corregido): Para corrección de bugs
- **Security** (Seguridad): Para vulnerabilidades corregidas

---

**Versión Actual**: 1.0.0  
**Fecha de Lanzamiento**: 2024-12-26  
**Estado**: Estable
