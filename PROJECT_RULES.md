# REGLAS PRINCIPALES DEL PROYECTO - CYBERTIME

## 1. PRINCIPIOS FUNDAMENTALES

### 1.1 Arquitectura
- **Arquitectura Cliente-Servidor**: Sistema distribuido con un servidor central (PC Principal) y múltiples clientes (PCs del Cyber)
- **Comunicación en Tiempo Real**: Uso de polling HTTP cada 2 segundos para sincronización de estados
- **Red Local**: Todo el sistema opera en una red local (LAN) sin dependencias externas de internet
- **Servidor Único**: Solo una PC Principal puede actuar como servidor en la red

### 1.2 Seguridad y Control
- **Bloqueo Físico**: Las PCs clientes deben bloquearse completamente cuando no tienen tiempo asignado
- **Control Centralizado**: Solo la PC Principal puede asignar, modificar o detener tiempos
- **Persistencia de Datos**: Todos los estados deben guardarse en base de datos para recuperación ante fallos
- **Validación de Tiempo**: El servidor es la única fuente de verdad para el tiempo restante

### 1.3 Experiencia de Usuario
- **Interfaz Simple**: Diseño minimalista y funcional, fácil de usar sin capacitación
- **Feedback Visual**: Estados claros (disponible, en uso, bloqueada) con códigos de color
- **Alertas Tempranas**: Notificaciones cuando queden 5 minutos de tiempo
- **Sin Intervención del Cliente**: Las PCs clientes no pueden modificar su propio tiempo

## 2. REGLAS DE DESARROLLO

### 2.1 Código
- **Lenguaje**: PHP 7.4+ para backend, JavaScript vanilla para frontend
- **Sin Frameworks Pesados**: Código nativo para máximo rendimiento y mínimas dependencias
- **Comentarios en Español**: Todo el código debe estar comentado en español
- **Nombres Descriptivos**: Variables y funciones con nombres claros y autodocumentados
- **Manejo de Errores**: Todos los endpoints deben retornar JSON con estructura consistente

### 2.2 Base de Datos
- **Motor**: MariaDB 10.4+
- **Nomenclatura**: Tablas en minúsculas con guiones bajos (snake_case)
- **Timestamps**: Todas las tablas deben tener created_at y updated_at
- **IDs Autoincrement**: Usar enteros autoincrement para claves primarias
- **Sin Eliminación Física**: Usar soft deletes con campo deleted_at cuando sea necesario

### 2.3 API REST
- **Formato**: Todas las respuestas en JSON
- **Estructura Estándar**:
  ```json
  {
    "success": true/false,
    "data": {},
    "message": "Mensaje descriptivo",
    "timestamp": "2024-01-01 12:00:00"
  }
  ```
- **Códigos HTTP**: Usar códigos apropiados (200, 400, 500)
- **Endpoints Descriptivos**: URLs claras y RESTful

### 2.4 Frontend
- **Responsive**: Diseño adaptable a diferentes resoluciones
- **CSS Moderno**: Uso de Flexbox/Grid, variables CSS
- **JavaScript Modular**: Funciones pequeñas y reutilizables
- **Sin jQuery**: JavaScript vanilla para mejor rendimiento
- **Accesibilidad**: Contraste adecuado, tamaños de fuente legibles

## 3. REGLAS DE OPERACIÓN

### 3.1 Estados de PC
- **DISPONIBLE**: PC sin tiempo asignado, bloqueada
- **EN_USO**: PC con tiempo activo, desbloqueada
- **PAUSADA**: PC con tiempo pausado, bloqueada
- **MANTENIMIENTO**: PC fuera de servicio, bloqueada

### 3.2 Gestión de Tiempo
- **Unidad Mínima**: 1 minuto
- **Tiempo Máximo**: 24 horas por sesión
- **Actualización**: El tiempo se actualiza cada segundo en el cliente
- **Sincronización**: Verificación con servidor cada 2 segundos
- **Tolerancia**: 3 segundos de diferencia aceptable entre cliente y servidor

### 3.3 Bloqueo de PC
- **Método**: Overlay HTML fullscreen con z-index máximo
- **Prevención**: Deshabilitar F11, Alt+Tab, Ctrl+Alt+Del mediante JavaScript
- **Persistencia**: El bloqueo debe mantenerse incluso si se recarga la página
- **Desbloqueo**: Solo mediante asignación de tiempo desde PC Principal

### 3.4 Recuperación ante Fallos
- **Reconexión Automática**: Si se pierde conexión, reintentar cada 5 segundos
- **Estado Persistente**: El estado se guarda en DB cada cambio
- **Recuperación de Sesión**: Al reiniciar, recuperar último estado conocido
- **Logs**: Registrar todos los eventos importantes

## 4. REGLAS DE INSTALACIÓN

### 4.1 Requisitos Mínimos
- **PC Principal**: Windows 7+, 4GB RAM, XAMPP 7.4+
- **PC Cliente**: Windows 7+, 2GB RAM, Navegador moderno
- **Red**: Router WiFi con DHCP, velocidad mínima 10Mbps

### 4.2 Configuración de Red
- **IP Estática**: La PC Principal debe tener IP fija
- **Puerto**: Apache en puerto 80 (o configurar otro)
- **Firewall**: Permitir conexiones entrantes en el puerto configurado
- **Nombre de Red**: Configurar nombre de host descriptivo

### 4.3 Despliegue
- **Ubicación**: c:\xampp\htdocs\cybertime
- **Base de Datos**: Crear DB antes de instalar
- **Configuración**: Archivo config.php con credenciales
- **Permisos**: Carpeta logs con permisos de escritura

## 5. REGLAS DE MANTENIMIENTO

### 5.1 Respaldos
- **Frecuencia**: Backup diario de base de datos
- **Ubicación**: Carpeta backups/ en el proyecto
- **Retención**: Mantener últimos 7 días
- **Automatización**: Script programado en Windows Task Scheduler

### 5.2 Logs
- **Nivel**: INFO para operaciones normales, ERROR para fallos
- **Rotación**: Archivo nuevo cada día
- **Tamaño Máximo**: 10MB por archivo
- **Limpieza**: Eliminar logs mayores a 30 días

### 5.3 Monitoreo
- **Estado de PCs**: Dashboard en tiempo real
- **Conexiones**: Verificar conectividad de cada cliente
- **Rendimiento**: Monitorear uso de CPU/RAM del servidor
- **Alertas**: Notificar si una PC no responde por 30 segundos

## 6. REGLAS DE ESCALABILIDAD

### 6.1 Límites
- **Máximo de PCs**: 50 clientes simultáneos
- **Conexiones Concurrentes**: Configurar Apache para soportar carga
- **Tamaño de DB**: Limpiar sesiones antiguas mensualmente
- **Caché**: Implementar caché de configuración en memoria

### 6.2 Optimización
- **Índices**: Crear índices en campos de búsqueda frecuente
- **Queries**: Optimizar consultas SQL, evitar SELECT *
- **Assets**: Minificar CSS/JS en producción
- **Imágenes**: Optimizar tamaño y formato

## 7. REGLAS DE TESTING

### 7.1 Pruebas Obligatorias
- **Asignación de Tiempo**: Verificar que se asigna correctamente
- **Bloqueo/Desbloqueo**: Probar transiciones de estado
- **Finalización de Tiempo**: Verificar bloqueo automático al llegar a 0
- **Reconexión**: Simular pérdida de red y verificar recuperación
- **Múltiples Clientes**: Probar con al menos 5 PCs simultáneas

### 7.2 Escenarios de Fallo
- **Servidor Caído**: Verificar comportamiento del cliente
- **Cliente Desconectado**: Verificar detección en servidor
- **Tiempo Negativo**: Validar que no ocurra
- **Doble Asignación**: Prevenir asignar tiempo a PC ya en uso

## 8. CONVENCIONES DE NOMBRES

### 8.1 Archivos
- **PHP**: snake_case.php (ej: assign_time.php)
- **CSS**: kebab-case.css (ej: admin-panel.css)
- **JavaScript**: camelCase.js (ej: timeManager.js)
- **SQL**: UPPERCASE para keywords, snake_case para nombres

### 8.2 Variables
- **PHP**: $snake_case
- **JavaScript**: camelCase
- **CSS**: kebab-case
- **SQL**: snake_case

### 8.3 Constantes
- **PHP**: UPPER_SNAKE_CASE
- **JavaScript**: UPPER_SNAKE_CASE
- **Base de Datos**: UPPER_SNAKE_CASE para enums

## 9. DOCUMENTACIÓN OBLIGATORIA

### 9.1 Código
- **Funciones**: Docblock con descripción, parámetros y retorno
- **Clases**: Descripción de propósito y uso
- **APIs**: Documentar endpoints con ejemplos
- **Configuración**: Comentar cada opción en config.php

### 9.2 Usuario
- **Manual de Instalación**: Paso a paso con capturas
- **Manual de Uso**: Guía para operador del cyber
- **Troubleshooting**: Problemas comunes y soluciones
- **FAQ**: Preguntas frecuentes

## 10. CONTROL DE VERSIONES

### 10.1 Versionado
- **Formato**: MAJOR.MINOR.PATCH (ej: 1.0.0)
- **MAJOR**: Cambios incompatibles
- **MINOR**: Nueva funcionalidad compatible
- **PATCH**: Corrección de bugs

### 10.2 Changelog
- **Formato**: Markdown con fecha y versión
- **Categorías**: Added, Changed, Fixed, Removed
- **Detalle**: Descripción clara de cada cambio

---

**Fecha de Creación**: 2024-12-26
**Versión del Documento**: 1.0.0
**Autor**: Sistema CyberTime
**Última Actualización**: 2024-12-26
