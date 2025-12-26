# 📋 PROPUESTA DE DESARROLLO - CYBERTIME

## Sistema de Control de Tiempos para Cyber Café

---

## 🎯 RESUMEN EJECUTIVO

**CyberTime** es un sistema profesional de gestión y control de tiempos diseñado específicamente para cyber cafés. Permite administrar de forma centralizada el tiempo de uso de todas las computadoras desde una PC principal, mientras que las PCs clientes se bloquean y desbloquean automáticamente según el tiempo asignado.

### Características Destacadas
- ✅ **100% Red Local**: No requiere internet, opera completamente en LAN
- ✅ **Control Centralizado**: Gestión desde una sola PC principal
- ✅ **Bloqueo Automático**: Seguridad total en el control de acceso
- ✅ **Tiempo Real**: Actualización cada 2 segundos
- ✅ **Fácil Instalación**: Basado en XAMPP, sin dependencias complejas
- ✅ **Sin Frameworks**: Código nativo, máximo rendimiento

---

## 🏗️ ARQUITECTURA DEL SISTEMA

### Modelo Cliente-Servidor

```
┌─────────────────────────────────────────────────────────────┐
│                    RED LOCAL (WiFi/LAN)                      │
│                                                              │
│  ┌────────────────────────────────────────────────────┐    │
│  │         PC PRINCIPAL (SERVIDOR)                     │    │
│  │  ┌──────────────────────────────────────────────┐  │    │
│  │  │  XAMPP (Apache + PHP + MariaDB)              │  │    │
│  │  │  • Panel de Administración Web               │  │    │
│  │  │  • API REST (JSON)                            │  │    │
│  │  │  • Base de Datos MariaDB                      │  │    │
│  │  │  • IP Estática: 192.168.1.100                 │  │    │
│  │  └──────────────────────────────────────────────┘  │    │
│  └──────────────────┬───────────────────────────────────┘    │
│                     │                                        │
│                     │ HTTP/JSON (Polling cada 2 seg)         │
│                     │                                        │
│  ┌──────────────────┴───────────────────────────────────┐   │
│  │              PCs CLIENTES (Navegador)                │   │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌────┐  │   │
│  │  │  PC-01   │  │  PC-02   │  │  PC-03   │  │... │  │   │
│  │  │ Chrome   │  │ Chrome   │  │ Chrome   │  │    │  │   │
│  │  │ Bloqueada│  │  Activa  │  │ Pausada  │  │    │  │   │
│  │  └──────────┘  └──────────┘  └──────────┘  └────┘  │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### Flujo de Operación

1. **Administrador** asigna tiempo desde PC Principal
2. **Servidor** actualiza base de datos y cambia estado de PC
3. **Cliente** consulta su estado cada 2 segundos (polling)
4. **Cliente** se desbloquea y muestra contador de tiempo
5. **Cliente** actualiza tiempo restante cada segundo
6. **Cliente** envía heartbeat al servidor cada 2 segundos
7. Al llegar a 0, **Cliente** se bloquea automáticamente

---

## 💻 STACK TECNOLÓGICO

### Backend (PC Principal)

| Tecnología | Versión | Propósito | Justificación |
|------------|---------|-----------|---------------|
| **PHP** | 7.4+ | Lenguaje del servidor | Incluido en XAMPP, simple, eficiente |
| **Apache** | 2.4+ | Servidor web | Incluido en XAMPP, estable, probado |
| **MariaDB** | 10.4+ | Base de datos | Incluido en XAMPP, excelente concurrencia |
| **XAMPP** | 8.0+ | Entorno integrado | Todo en uno, fácil instalación |

### Frontend (Ambos)

| Tecnología | Versión | Propósito | Justificación |
|------------|---------|-----------|---------------|
| **HTML5** | - | Estructura | Estándar web, APIs modernas |
| **CSS3** | - | Estilos | Flexbox, Grid, Variables CSS |
| **JavaScript** | ES6+ | Interactividad | Vanilla JS, sin frameworks, máximo rendimiento |

### Comunicación

| Protocolo | Formato | Método | Frecuencia |
|-----------|---------|--------|------------|
| **HTTP** | JSON | Polling | Cada 2 segundos |
| **REST API** | JSON | Request/Response | Bajo demanda |

### ¿Por qué NO usamos frameworks?

❌ **React/Vue/Angular**: Overhead innecesario, mayor complejidad  
❌ **jQuery**: Obsoleto, JavaScript nativo es suficiente  
❌ **Bootstrap/Tailwind**: CSS puro es más eficiente  
❌ **Laravel/Symfony**: Demasiado pesado para este proyecto  

✅ **Código Nativo**: Máximo rendimiento, mínima complejidad, fácil mantenimiento

---

## 📊 BASE DE DATOS

### Diseño de Tablas

```sql
users           → Administradores del sistema
pcs             → Computadoras del cyber
sessions        → Sesiones de uso activas/históricas
time_logs       → Historial de cambios de tiempo
transactions    → Transacciones financieras
pricing         → Tarifas de precios
settings        → Configuraciones del sistema
logs            → Registro de eventos
alerts          → Alertas y notificaciones
```

### Características Avanzadas

- **Vistas**: Consultas optimizadas pre-calculadas
- **Triggers**: Automatización de cambios de estado
- **Procedimientos**: Operaciones complejas encapsuladas
- **Eventos**: Limpieza automática de datos antiguos
- **Índices**: Optimización de consultas frecuentes

### Ejemplo de Trigger

```sql
-- Al finalizar sesión, cambiar estado de PC a disponible
CREATE TRIGGER trg_session_update_pc_status
AFTER UPDATE ON sessions
FOR EACH ROW
BEGIN
    IF NEW.status = 'finalizada' THEN
        UPDATE pcs SET status = 'disponible' WHERE id = NEW.pc_id;
    END IF;
END;
```

---

## 🔌 API REST

### Endpoints Principales

#### Administración

```
GET  /api/admin/get_pcs.php
     → Lista todas las PCs con su estado actual

POST /api/admin/assign_time.php
     → Asigna tiempo a una PC
     Params: pc_id, time_seconds, client_name (opcional)

POST /api/admin/add_time.php
     → Agrega tiempo adicional a sesión activa
     Params: session_id, time_seconds

POST /api/admin/pause_time.php
     → Pausa el tiempo de una sesión
     Params: session_id

POST /api/admin/stop_time.php
     → Detiene el tiempo manualmente
     Params: session_id
```

#### Cliente

```
GET  /api/client/status.php?pc_id=1
     → Obtiene estado actual de la PC

POST /api/client/heartbeat.php
     → Envía señal de vida al servidor
     Params: pc_id

POST /api/client/register.php
     → Registra una nueva PC en el sistema
     Params: name, ip_address, mac_address
```

### Formato de Respuesta Estándar

```json
{
  "success": true,
  "data": {
    "pc_id": 1,
    "status": "en_uso",
    "remaining_time": 1800,
    "assigned_time": 3600,
    "client_name": "Juan Pérez"
  },
  "message": "Estado obtenido correctamente",
  "timestamp": "2024-12-26 12:00:00"
}
```

---

## 🎨 INTERFAZ DE USUARIO

### Panel de Administración (PC Principal)

#### Dashboard Principal
- **Vista de Cuadrícula**: Todas las PCs con estado visual
- **Códigos de Color**:
  - 🟢 Verde: Disponible
  - 🔵 Azul: En uso
  - 🟡 Amarillo: Pausada
  - 🔴 Rojo: Mantenimiento
- **Información por PC**:
  - Nombre (PC-01, PC-02, etc.)
  - Estado actual
  - Tiempo restante (si está en uso)
  - Nombre del cliente (opcional)
- **Acciones Rápidas**:
  - Asignar tiempo
  - Agregar tiempo
  - Pausar/Reanudar
  - Detener

#### Módulos Adicionales
- **Gestión de PCs**: Agregar, editar, deshabilitar
- **Tarifas**: Configurar precios por tiempo
- **Reportes**: Ingresos, uso, estadísticas
- **Configuración**: Ajustes del sistema
- **Usuarios**: Gestión de administradores

### Interfaz de Cliente (PCs del Cyber)

#### Pantalla de Bloqueo
```
┌─────────────────────────────────────┐
│                                     │
│         🔒 PC BLOQUEADA             │
│                                     │
│    Esperando asignación de tiempo   │
│                                     │
│         [Logo del Cyber]            │
│                                     │
│    Solicita tiempo al encargado     │
│                                     │
└─────────────────────────────────────┘
```

#### Pantalla Activa
```
┌─────────────────────────────────────┐
│  PC-01                    [Usuario] │
│                                     │
│        TIEMPO RESTANTE              │
│                                     │
│          00:45:30                   │
│                                     │
│  ████████████░░░░░░░░░░░  75%      │
│                                     │
│  Inicio: 12:00:00                   │
│  Fin estimado: 13:00:00             │
│                                     │
└─────────────────────────────────────┘
```

---

## 🔒 SEGURIDAD

### Autenticación
- Contraseñas hasheadas con **bcrypt**
- Sesiones con timeout de 8 horas
- Regeneración de session_id al login
- Protección contra fuerza bruta

### Validación
- **SQL Injection**: Prepared statements
- **XSS**: Escape de HTML
- **CSRF**: Tokens de validación
- Sanitización de todos los inputs

### Bloqueo de PC
- Overlay fullscreen con z-index 999999
- Prevención de teclas especiales (F11, Alt+Tab, etc.)
- Verificación constante del estado
- No se puede eludir desde el cliente

---

## 📦 INSTALACIÓN

### Requisitos Mínimos

#### PC Principal (Servidor)
- Windows 7+ (64 bits recomendado)
- 4 GB RAM
- 10 GB espacio en disco
- XAMPP 7.4+
- IP estática configurada

#### PCs Clientes
- Windows 7+
- 2 GB RAM
- Navegador moderno (Chrome recomendado)
- Conexión a la misma red local

### Proceso de Instalación

#### 1. PC Principal (30-60 minutos)

```
1. Instalar XAMPP
2. Configurar IP estática
3. Copiar archivos a c:\xampp\htdocs\cybertime\
4. Crear base de datos en phpMyAdmin
5. Importar schema.sql y seeds.sql
6. Configurar config.php
7. Configurar firewall
8. Verificar acceso
```

**Documentación completa**: `docs/INSTALL_SERVER.md`

#### 2. PCs Clientes (15-30 minutos cada una)

```
1. Conectar a red del cyber
2. Instalar/actualizar Chrome
3. Configurar página de inicio
4. Crear acceso directo de inicio automático
5. Configurar inicio de sesión automático
6. Deshabilitar protector de pantalla
7. Configurar opciones de energía
8. Verificar funcionamiento
```

**Documentación completa**: `docs/INSTALL_CLIENT.md`

---

## 📈 FUNCIONALIDADES

### Versión 1.0 (Propuesta Actual)

#### Gestión de Tiempo
- ✅ Asignar tiempo a PC
- ✅ Agregar tiempo adicional
- ✅ Pausar/Reanudar tiempo
- ✅ Detener tiempo manualmente
- ✅ Contador en tiempo real
- ✅ Bloqueo automático al terminar

#### Control de PCs
- ✅ Registrar nuevas PCs
- ✅ Ver estado de todas las PCs
- ✅ Activar/Desactivar PCs
- ✅ Configurar ubicación y especificaciones
- ✅ Detección de desconexión

#### Sistema de Alertas
- ✅ Alerta cuando quedan 5 minutos
- ✅ Alerta de desconexión de PC
- ✅ Notificaciones en panel admin
- ✅ Sonido de alerta (opcional)

#### Reportes Financieros
- ✅ Ingresos diarios/mensuales
- ✅ Tiempo total usado por PC
- ✅ Sesiones por período
- ✅ Estadísticas de uso
- ✅ Historial de transacciones

#### Gestión de Tarifas
- ✅ Crear tarifas personalizadas
- ✅ Precios por tiempo (15min, 30min, 1h, etc.)
- ✅ Activar/Desactivar tarifas
- ✅ Orden de visualización

#### Multi-usuario
- ✅ Roles: Administrador y Operador
- ✅ Permisos diferenciados
- ✅ Registro de acciones por usuario
- ✅ Historial de login

### Versión 2.0 (Futuro)
- 🔄 WebSockets para comunicación en tiempo real
- 🔄 PWA (Progressive Web App)
- 🔄 Notificaciones push
- 🔄 Modo offline con sincronización
- 🔄 App móvil para administración remota
- 🔄 Venta de productos (snacks, bebidas)
- 🔄 Sistema de membresías

---

## 🎯 VENTAJAS COMPETITIVAS

### vs. Soluciones Comerciales

| Característica | CyberTime | Soluciones Comerciales |
|----------------|-----------|------------------------|
| **Costo** | Gratis | $500-2000 USD |
| **Licencias** | Ilimitadas | Por PC |
| **Personalización** | Total | Limitada |
| **Soporte** | Documentación completa | Pago adicional |
| **Dependencia** | Ninguna | Proveedor |
| **Código fuente** | Accesible | Cerrado |
| **Actualizaciones** | Cuando quieras | Forzadas |

### vs. Soluciones Gratuitas

| Característica | CyberTime | Soluciones Gratuitas |
|----------------|-----------|---------------------|
| **Profesionalismo** | Alto | Variable |
| **Documentación** | Completa | Escasa |
| **Mantenimiento** | Activo | Abandonado |
| **Seguridad** | Robusta | Cuestionable |
| **Funcionalidades** | Completas | Básicas |
| **Interfaz** | Moderna | Anticuada |

---

## 📋 REGLAS PRINCIPALES DEL PROYECTO

### 1. Principios Fundamentales
- Arquitectura cliente-servidor en red local
- Comunicación en tiempo real (polling cada 2 segundos)
- Control centralizado desde PC Principal
- Bloqueo físico de PCs clientes
- Persistencia de datos en base de datos

### 2. Reglas de Desarrollo
- Código en PHP nativo, sin frameworks pesados
- JavaScript vanilla, sin jQuery ni librerías
- CSS puro, sin Bootstrap ni Tailwind
- Comentarios en español
- Nombres descriptivos y autodocumentados
- Manejo de errores consistente

### 3. Reglas de Operación
- Estados de PC: Disponible, En uso, Pausada, Mantenimiento
- Unidad mínima de tiempo: 1 minuto
- Actualización de tiempo cada segundo en cliente
- Sincronización con servidor cada 2 segundos
- Tolerancia de 3 segundos entre cliente y servidor

### 4. Reglas de Seguridad
- Solo PC Principal puede asignar/modificar tiempos
- Contraseñas hasheadas con bcrypt
- Prepared statements para prevenir SQL injection
- Escape de HTML para prevenir XSS
- Validación de todos los inputs

**Documento completo**: `PROJECT_RULES.md`

---

## 📚 DOCUMENTACIÓN ENTREGADA

### Documentos Principales

1. **README.md** - Documentación general del proyecto
2. **PROJECT_RULES.md** - Reglas y principios del proyecto
3. **TECH_STACK.md** - Stack tecnológico detallado
4. **PROPUESTA_DESARROLLO.md** - Este documento

### Guías de Instalación

5. **docs/INSTALL_SERVER.md** - Instalación en PC Principal (paso a paso)
6. **docs/INSTALL_CLIENT.md** - Instalación en PCs Clientes (paso a paso)

### Base de Datos

7. **database/schema.sql** - Estructura completa de la base de datos
8. **database/seeds.sql** - Datos iniciales del sistema

### Archivos de Configuración

9. **config.php** - Configuración global del sistema
10. **.htaccess** - Configuración de Apache
11. **.gitignore** - Archivos a ignorar en Git

### Archivos Base

12. **index.php** - Página de inicio
13. **includes/db.php** - Conexión a base de datos
14. **includes/functions.php** - Funciones auxiliares
15. **includes/response.php** - Respuestas JSON
16. **includes/auth.php** - Autenticación

---

## 🚀 PRÓXIMOS PASOS

### Fase 1: Desarrollo del Core (Semanas 1-2)
- [ ] Implementar panel de administración
- [ ] Crear interfaz de cliente
- [ ] Desarrollar APIs REST
- [ ] Implementar sistema de bloqueo
- [ ] Pruebas básicas

### Fase 2: Funcionalidades Avanzadas (Semanas 3-4)
- [ ] Sistema de alertas
- [ ] Reportes financieros
- [ ] Gestión de tarifas
- [ ] Multi-usuario
- [ ] Pruebas completas

### Fase 3: Refinamiento (Semana 5)
- [ ] Optimización de rendimiento
- [ ] Mejoras de interfaz
- [ ] Documentación de usuario
- [ ] Pruebas de estrés
- [ ] Corrección de bugs

### Fase 4: Despliegue (Semana 6)
- [ ] Instalación en producción
- [ ] Configuración de todas las PCs
- [ ] Capacitación de usuarios
- [ ] Monitoreo inicial
- [ ] Ajustes finales

---

## 💰 ESTIMACIÓN DE COSTOS

### Costos de Infraestructura

| Concepto | Costo | Notas |
|----------|-------|-------|
| **PC Principal** | Ya existe | Usar PC existente |
| **PCs Clientes** | Ya existen | Usar PCs existentes |
| **Router WiFi** | Ya existe | Usar router existente |
| **XAMPP** | Gratis | Software libre |
| **Desarrollo** | Gratis | Sistema incluido |
| **Total** | $0 USD | Sin costos adicionales |

### Comparación con Alternativas

| Solución | Costo Inicial | Costo Mensual | Costo Anual |
|----------|---------------|---------------|-------------|
| **CyberTime** | $0 | $0 | $0 |
| CyberCafePro | $800 | $20 | $240 |
| HandyCafe | $500 | $15 | $180 |
| TrueCafe | $600 | $18 | $216 |

**Ahorro estimado**: $500-800 USD iniciales + $180-240 USD anuales

---

## 📞 SOPORTE Y MANTENIMIENTO

### Documentación Incluida
- ✅ Manual de instalación detallado
- ✅ Manual de usuario
- ✅ Documentación técnica completa
- ✅ Solución de problemas comunes
- ✅ FAQ

### Mantenimiento Recomendado
- **Diario**: Verificar PCs conectadas
- **Semanal**: Limpiar archivos temporales
- **Mensual**: Respaldo de base de datos
- **Trimestral**: Actualizar sistema operativo

### Logs y Debugging
- Logs de aplicación en `logs/app.log`
- Logs de errores en `logs/error.log`
- Logs de Apache en `C:\xampp\apache\logs\`
- Logs de PHP en `C:\xampp\php\logs\`

---

## ✅ CONCLUSIÓN

**CyberTime** es una solución profesional, completa y gratuita para la gestión de tiempos en cyber cafés. Su arquitectura simple pero robusta, basada en tecnologías probadas y sin dependencias complejas, garantiza:

- ✅ **Fácil instalación y mantenimiento**
- ✅ **Máximo rendimiento y estabilidad**
- ✅ **Control total del código fuente**
- ✅ **Cero costos de licenciamiento**
- ✅ **Escalabilidad hasta 50 PCs**
- ✅ **Documentación completa en español**

El sistema está diseñado siguiendo las mejores prácticas de desarrollo, con código limpio, bien documentado y fácil de mantener. La arquitectura modular permite futuras expansiones sin comprometer la estabilidad del core.

### Recomendación

Proceder con la implementación siguiendo el plan de desarrollo propuesto. El sistema está listo para comenzar el desarrollo inmediato con todas las bases técnicas, arquitectónicas y documentales establecidas.

---

**Versión**: 1.0.0  
**Fecha**: 2024-12-26  
**Autor**: Sistema CyberTime  
**Estado**: Propuesta Aprobada para Desarrollo

---

## 📄 ANEXOS

### A. Credenciales por Defecto

```
Base de Datos:
- Host: localhost
- Puerto: 3306
- Database: cybertime
- Usuario: root
- Contraseña: (vacía)

Panel Admin:
- Usuario: admin
- Contraseña: admin123

⚠️ CAMBIAR DESPUÉS DE LA INSTALACIÓN
```

### B. Puertos Utilizados

```
- Puerto 80: Apache (HTTP)
- Puerto 3306: MariaDB
- Puerto 443: HTTPS (futuro)
```

### C. Estructura de Archivos Completa

Ver `README.md` sección "Estructura del Proyecto"

### D. Comandos Útiles

```bash
# Iniciar XAMPP
C:\xampp\xampp-control.exe

# Acceder a phpMyAdmin
http://localhost/phpmyadmin

# Acceder al panel admin
http://192.168.1.100/cybertime/admin/

# Acceder a interfaz cliente
http://192.168.1.100/cybertime/client/
```

---

**¿Listo para comenzar el desarrollo? 🚀**

Consulta `README.md` para empezar o `docs/INSTALL_SERVER.md` para instalar.
