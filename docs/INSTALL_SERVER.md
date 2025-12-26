# INSTALACIÓN EN PC PRINCIPAL (SERVIDOR)

## 📋 TABLA DE CONTENIDOS
1. [Requisitos Previos](#requisitos-previos)
2. [Instalación de XAMPP](#instalación-de-xampp)
3. [Configuración de Red](#configuración-de-red)
4. [Instalación de CyberTime](#instalación-de-cybertime)
5. [Configuración de Base de Datos](#configuración-de-base-de-datos)
6. [Configuración del Sistema](#configuración-del-sistema)
7. [Verificación de Instalación](#verificación-de-instalación)
8. [Configuración de Firewall](#configuración-de-firewall)
9. [Configuración de Inicio Automático](#configuración-de-inicio-automático)
10. [Solución de Problemas](#solución-de-problemas)

---

## 1. REQUISITOS PREVIOS

### 1.1 Hardware Mínimo
- **Procesador**: Intel Core i3 o equivalente (2.0 GHz+)
- **RAM**: 4 GB mínimo, 8 GB recomendado
- **Disco Duro**: 10 GB de espacio libre
- **Tarjeta de Red**: Adaptador WiFi o Ethernet

### 1.2 Software
- **Sistema Operativo**: Windows 7, 8, 10 u 11 (32 o 64 bits)
- **Permisos**: Cuenta de administrador
- **Navegador**: Chrome, Firefox o Edge actualizado

### 1.3 Red
- **Router WiFi** con DHCP habilitado
- **Conexión a Internet** (solo para descarga inicial)
- **Puertos disponibles**: Puerto 80 (HTTP) y 3306 (MySQL)

---

## 2. INSTALACIÓN DE XAMPP

### 2.1 Descarga de XAMPP

1. Abrir navegador web
2. Ir a: **https://www.apachefriends.org/**
3. Descargar **XAMPP para Windows**
   - Versión recomendada: **8.0.x** o superior
   - Tamaño aproximado: 150 MB

### 2.2 Instalación Paso a Paso

#### Paso 1: Ejecutar Instalador
```
1. Hacer doble clic en: xampp-windows-x64-8.0.x-installer.exe
2. Si aparece advertencia de UAC, clic en "Sí"
3. Si aparece advertencia de antivirus, permitir ejecución
```

#### Paso 2: Seleccionar Componentes
```
✅ Apache
✅ MySQL (MariaDB)
✅ PHP
✅ phpMyAdmin
❌ FileZilla (no necesario)
❌ Mercury (no necesario)
❌ Tomcat (no necesario)
❌ Perl (no necesario)
```

#### Paso 3: Seleccionar Carpeta de Instalación
```
Ruta recomendada: C:\xampp
⚠️ IMPORTANTE: No instalar en "Archivos de programa" por permisos
```

#### Paso 4: Completar Instalación
```
1. Desmarcar "Learn more about Bitnami"
2. Clic en "Next" hasta finalizar
3. Marcar "Do you want to start the Control Panel now?"
4. Clic en "Finish"
```

### 2.3 Configuración Inicial de XAMPP

#### Paso 1: Abrir XAMPP Control Panel
```
Ubicación: C:\xampp\xampp-control.exe
Ejecutar como Administrador (clic derecho → Ejecutar como administrador)
```

#### Paso 2: Iniciar Servicios
```
1. Clic en botón "Start" junto a Apache
   - Debe aparecer en verde con texto "Running"
   
2. Clic en botón "Start" junto a MySQL
   - Debe aparecer en verde con texto "Running"
```

#### Paso 3: Verificar Puertos
```
Si Apache no inicia (puerto 80 ocupado):
1. Clic en "Config" (botón junto a Apache)
2. Seleccionar "Apache (httpd.conf)"
3. Buscar línea: Listen 80
4. Cambiar a: Listen 8080
5. Guardar y reiniciar Apache

Si MySQL no inicia (puerto 3306 ocupado):
1. Clic en "Config" (botón junto a MySQL)
2. Seleccionar "my.ini"
3. Buscar línea: port=3306
4. Cambiar a: port=3307
5. Guardar y reiniciar MySQL
```

#### Paso 4: Instalar Servicios (Opcional pero Recomendado)
```
1. Clic en botón "X" rojo junto a Apache → Instalar servicio
2. Clic en botón "X" rojo junto a MySQL → Instalar servicio
3. Esto hará que se inicien automáticamente con Windows
```

---

## 3. CONFIGURACIÓN DE RED

### 3.1 Asignar IP Estática a la PC Principal

#### Método 1: Configuración Manual (Recomendado)

**Paso 1: Abrir Configuración de Red**
```
Windows 10/11:
1. Clic derecho en icono de red (barra de tareas)
2. "Abrir configuración de red e Internet"
3. "Cambiar opciones del adaptador"

Windows 7/8:
1. Panel de Control
2. Redes e Internet
3. Centro de redes y recursos compartidos
4. Cambiar configuración del adaptador
```

**Paso 2: Configurar Adaptador**
```
1. Clic derecho en adaptador WiFi o Ethernet
2. Seleccionar "Propiedades"
3. Doble clic en "Protocolo de Internet versión 4 (TCP/IPv4)"
4. Seleccionar "Usar la siguiente dirección IP"
```

**Paso 3: Ingresar Configuración**
```
Ejemplo de configuración (ajustar según tu red):

Dirección IP:        192.168.1.100
Máscara de subred:   255.255.255.0
Puerta de enlace:    192.168.1.1
DNS preferido:       8.8.8.8
DNS alternativo:     8.8.4.4

⚠️ IMPORTANTE: 
- La IP debe estar en el rango de tu router
- No debe estar asignada a otro dispositivo
- Anota esta IP, la necesitarás después
```

**Paso 4: Guardar y Verificar**
```
1. Clic en "Aceptar" en todas las ventanas
2. Abrir CMD (Símbolo del sistema)
3. Ejecutar: ipconfig
4. Verificar que aparezca la IP configurada
```

#### Método 2: Reserva DHCP en Router

```
1. Acceder al panel del router (ej: 192.168.1.1)
2. Buscar sección "DHCP" o "Reservas"
3. Agregar reserva con la MAC de la PC Principal
4. Asignar IP fija (ej: 192.168.1.100)
5. Guardar y reiniciar router
```

### 3.2 Verificar Conectividad

```
1. Abrir CMD en otra PC de la red
2. Ejecutar: ping 192.168.1.100
3. Debe responder sin pérdida de paquetes
```

---

## 4. INSTALACIÓN DE CYBERTIME

### 4.1 Obtener Archivos del Sistema

#### Opción A: Descarga Directa
```
1. Descargar cybertime.zip desde el repositorio
2. Extraer contenido en: C:\xampp\htdocs\
3. Debe quedar: C:\xampp\htdocs\cybertime\
```

#### Opción B: Clonar Repositorio (si aplica)
```
1. Instalar Git para Windows
2. Abrir CMD en: C:\xampp\htdocs\
3. Ejecutar: git clone [URL_REPOSITORIO] cybertime
```

#### Opción C: Copiar Archivos Manualmente
```
1. Copiar carpeta completa "cybertime"
2. Pegar en: C:\xampp\htdocs\
3. Verificar que existan todos los archivos
```

### 4.2 Verificar Estructura de Archivos

```
C:\xampp\htdocs\cybertime\
├── admin/
├── api/
├── client/
├── database/
├── docs/
├── includes/
├── install/
├── logs/
├── backups/
├── config.php
├── index.php
└── README.md
```

### 4.3 Configurar Permisos de Carpetas

```
1. Clic derecho en carpeta: C:\xampp\htdocs\cybertime\logs
2. Propiedades → Seguridad → Editar
3. Dar permisos de "Escritura" a "Usuarios"
4. Aplicar y Aceptar

Repetir para carpeta: C:\xampp\htdocs\cybertime\backups
```

---

## 5. CONFIGURACIÓN DE BASE DE DATOS

### 5.1 Acceder a phpMyAdmin

```
1. Abrir navegador
2. Ir a: http://localhost/phpmyadmin
3. Usuario: root
4. Contraseña: (dejar en blanco)
5. Clic en "Continuar"
```

### 5.2 Crear Base de Datos

#### Método 1: Interfaz Gráfica

```
1. En phpMyAdmin, clic en pestaña "Bases de datos"
2. En "Crear base de datos":
   - Nombre: cybertime
   - Cotejamiento: utf8mb4_unicode_ci
3. Clic en "Crear"
```

#### Método 2: SQL Directo

```sql
1. Clic en pestaña "SQL"
2. Copiar y pegar:

CREATE DATABASE cybertime 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

3. Clic en "Continuar"
```

### 5.3 Importar Estructura de Base de Datos

```
1. En phpMyAdmin, seleccionar base de datos "cybertime" (panel izquierdo)
2. Clic en pestaña "Importar"
3. Clic en "Seleccionar archivo"
4. Navegar a: C:\xampp\htdocs\cybertime\database\schema.sql
5. Clic en "Continuar"
6. Esperar mensaje de éxito
```

### 5.4 Importar Datos Iniciales

```
1. En phpMyAdmin, con "cybertime" seleccionada
2. Clic en pestaña "Importar"
3. Seleccionar archivo: C:\xampp\htdocs\cybertime\database\seeds.sql
4. Clic en "Continuar"
5. Verificar que se crearon registros iniciales
```

### 5.5 Crear Usuario de Base de Datos (Recomendado)

```sql
1. En phpMyAdmin, clic en pestaña "SQL"
2. Ejecutar:

CREATE USER 'cybertime_user'@'localhost' 
IDENTIFIED BY 'Cyber2024!Secure';

GRANT ALL PRIVILEGES ON cybertime.* 
TO 'cybertime_user'@'localhost';

FLUSH PRIVILEGES;

3. Clic en "Continuar"
```

### 5.6 Verificar Tablas Creadas

```
1. En phpMyAdmin, seleccionar "cybertime"
2. Verificar que existan las siguientes tablas:
   ✅ pcs
   ✅ sessions
   ✅ users
   ✅ settings
   ✅ logs
```

---

## 6. CONFIGURACIÓN DEL SISTEMA

### 6.1 Configurar Archivo config.php

```
1. Abrir: C:\xampp\htdocs\cybertime\config.php
2. Usar editor de texto (Notepad++, VS Code, o Bloc de notas)
```

#### Configuración de Base de Datos

```php
// Buscar sección: DATABASE CONFIGURATION
define('DB_HOST', 'localhost');
define('DB_NAME', 'cybertime');
define('DB_USER', 'cybertime_user');  // o 'root' si no creaste usuario
define('DB_PASS', 'Cyber2024!Secure'); // o '' si usas root
define('DB_CHARSET', 'utf8mb4');
```

#### Configuración de Red

```php
// Buscar sección: NETWORK CONFIGURATION
define('SERVER_IP', '192.168.1.100'); // Tu IP estática
define('SERVER_PORT', '80');          // O 8080 si cambiaste el puerto
define('SERVER_URL', 'http://192.168.1.100'); // URL completa
```

#### Configuración de Sistema

```php
// Buscar sección: SYSTEM CONFIGURATION
define('TIMEZONE', 'America/Mexico_City'); // Ajustar a tu zona horaria
define('POLLING_INTERVAL', 2);             // Segundos entre actualizaciones
define('SESSION_TIMEOUT', 28800);          // 8 horas en segundos
define('MAX_CLIENTS', 50);                 // Máximo de PCs
```

#### Guardar Cambios

```
1. Archivo → Guardar
2. Cerrar editor
```

### 6.2 Ejecutar Instalador Web (Opcional)

```
1. Abrir navegador
2. Ir a: http://localhost/cybertime/install/
3. Seguir asistente de instalación:
   - Verificar requisitos
   - Configurar base de datos
   - Crear usuario administrador
   - Finalizar instalación
4. Eliminar carpeta install/ por seguridad
```

---

## 7. VERIFICACIÓN DE INSTALACIÓN

### 7.1 Verificar Acceso al Panel de Administración

```
1. Abrir navegador
2. Ir a: http://localhost/cybertime/admin/
3. Debe aparecer pantalla de login
4. Credenciales por defecto:
   Usuario: admin
   Contraseña: admin123
5. Clic en "Iniciar Sesión"
6. Debe aparecer el dashboard principal
```

### 7.2 Verificar APIs

#### Test API de Admin

```
1. Abrir navegador
2. Ir a: http://localhost/cybertime/api/admin/get_pcs.php
3. Debe retornar JSON:
{
  "success": true,
  "data": [],
  "message": "PCs obtenidas correctamente",
  "timestamp": "2024-12-26 12:00:00"
}
```

#### Test API de Cliente

```
1. Ir a: http://localhost/cybertime/api/client/status.php?pc_id=1
2. Debe retornar JSON con estado de la PC
```

### 7.3 Verificar Acceso desde Otra PC

```
1. En otra PC de la red, abrir navegador
2. Ir a: http://192.168.1.100/cybertime/admin/
3. Debe cargar la página de login
4. Si no carga, revisar firewall (ver sección 8)
```

---

## 8. CONFIGURACIÓN DE FIREWALL

### 8.1 Windows Firewall

#### Método 1: Permitir Apache Automáticamente

```
1. Al iniciar Apache por primera vez, aparecerá alerta de firewall
2. Marcar "Redes privadas" y "Redes públicas"
3. Clic en "Permitir acceso"
```

#### Método 2: Configuración Manual

**Windows 10/11:**
```
1. Buscar "Firewall de Windows Defender"
2. Clic en "Configuración avanzada"
3. Clic en "Reglas de entrada"
4. Clic en "Nueva regla..."
5. Tipo de regla: Puerto
6. Protocolo: TCP
7. Puerto: 80 (o el que configuraste)
8. Acción: Permitir la conexión
9. Perfil: Marcar todos
10. Nombre: Apache CyberTime
11. Clic en "Finalizar"
```

**Windows 7/8:**
```
1. Panel de Control → Sistema y seguridad
2. Firewall de Windows
3. Configuración avanzada
4. Seguir pasos similares a Windows 10/11
```

### 8.2 Antivirus de Terceros

Si usas antivirus como Avast, AVG, Norton, etc.:

```
1. Abrir configuración del antivirus
2. Buscar sección "Firewall" o "Excepciones"
3. Agregar excepción para:
   - C:\xampp\apache\bin\httpd.exe
   - Puerto 80 (TCP)
4. Guardar cambios
```

### 8.3 Verificar Puertos Abiertos

```
1. Abrir CMD como Administrador
2. Ejecutar: netstat -an | findstr :80
3. Debe aparecer: 0.0.0.0:80 LISTENING
```

---

## 9. CONFIGURACIÓN DE INICIO AUTOMÁTICO

### 9.1 Configurar XAMPP como Servicio

```
1. Abrir XAMPP Control Panel como Administrador
2. Clic en botón "X" rojo junto a Apache
3. Seleccionar "Install as service"
4. Confirmar instalación
5. Repetir para MySQL
```

### 9.2 Configurar Inicio Automático de Servicios

```
1. Presionar Win + R
2. Escribir: services.msc
3. Buscar "Apache2.4"
4. Clic derecho → Propiedades
5. Tipo de inicio: Automático
6. Clic en "Aplicar"
7. Repetir para "MySQL"
```

### 9.3 Crear Acceso Directo al Panel de Admin

```
1. Clic derecho en Escritorio → Nuevo → Acceso directo
2. Ubicación: http://localhost/cybertime/admin/
3. Nombre: CyberTime - Panel Admin
4. Clic en "Finalizar"
```

---

## 10. SOLUCIÓN DE PROBLEMAS

### 10.1 Apache no inicia

**Problema: Puerto 80 ocupado**
```
Solución:
1. Identificar qué usa el puerto 80:
   CMD: netstat -ano | findstr :80
2. Opciones:
   a) Detener el servicio que usa el puerto
   b) Cambiar puerto de Apache a 8080 (ver sección 2.3)
```

**Problema: Permisos insuficientes**
```
Solución:
1. Ejecutar XAMPP Control Panel como Administrador
2. Clic derecho en xampp-control.exe → Propiedades
3. Compatibilidad → Marcar "Ejecutar como administrador"
```

### 10.2 MySQL no inicia

**Problema: Puerto 3306 ocupado**
```
Solución:
1. Verificar si hay otro MySQL instalado
2. Detener servicio MySQL existente:
   services.msc → Buscar MySQL → Detener
3. O cambiar puerto en XAMPP (ver sección 2.3)
```

**Problema: Base de datos corrupta**
```
Solución:
1. Detener MySQL
2. Renombrar carpeta: C:\xampp\mysql\data
3. Copiar carpeta backup: C:\xampp\mysql\backup → data
4. Iniciar MySQL
```

### 10.3 No se puede acceder desde otras PCs

**Problema: Firewall bloqueando**
```
Solución:
1. Verificar regla de firewall (sección 8)
2. Desactivar temporalmente firewall para probar
3. Si funciona, crear regla correcta
```

**Problema: IP incorrecta**
```
Solución:
1. Verificar IP de la PC Principal:
   CMD: ipconfig
2. Actualizar config.php con IP correcta
3. Usar esa IP en las PCs clientes
```

**Problema: Apache escuchando solo en localhost**
```
Solución:
1. Editar: C:\xampp\apache\conf\httpd.conf
2. Buscar: Listen 127.0.0.1:80
3. Cambiar a: Listen 0.0.0.0:80
4. Guardar y reiniciar Apache
```

### 10.4 Error de conexión a base de datos

**Problema: Credenciales incorrectas**
```
Solución:
1. Verificar config.php
2. Probar conexión en phpMyAdmin
3. Recrear usuario si es necesario (sección 5.5)
```

**Problema: Base de datos no existe**
```
Solución:
1. Abrir phpMyAdmin
2. Verificar que exista "cybertime"
3. Si no existe, crearla (sección 5.2)
4. Importar schema.sql (sección 5.3)
```

### 10.5 Páginas en blanco o errores 500

**Problema: Errores de PHP**
```
Solución:
1. Habilitar display_errors:
   Editar: C:\xampp\php\php.ini
   Buscar: display_errors = Off
   Cambiar a: display_errors = On
2. Reiniciar Apache
3. Revisar errores en pantalla
4. Revisar logs: C:\xampp\apache\logs\error.log
```

### 10.6 Rendimiento lento

**Problema: Muchas PCs conectadas**
```
Solución:
1. Aumentar límites de Apache:
   Editar: C:\xampp\apache\conf\extra\httpd-mpm.conf
   Aumentar MaxRequestWorkers
2. Optimizar base de datos:
   phpMyAdmin → cybertime → Operaciones → Optimizar tabla
```

---

## 📝 CHECKLIST FINAL DE INSTALACIÓN

Antes de poner en producción, verificar:

- [ ] XAMPP instalado y funcionando
- [ ] Apache iniciado y accesible
- [ ] MySQL iniciado y accesible
- [ ] IP estática configurada en PC Principal
- [ ] Base de datos "cybertime" creada
- [ ] Tablas importadas correctamente
- [ ] config.php configurado correctamente
- [ ] Firewall configurado para permitir conexiones
- [ ] Acceso al panel admin desde localhost
- [ ] Acceso al panel admin desde otra PC
- [ ] APIs respondiendo correctamente
- [ ] Servicios configurados para inicio automático
- [ ] Carpetas logs/ y backups/ con permisos de escritura
- [ ] Contraseña de admin cambiada (seguridad)
- [ ] Backup inicial de base de datos realizado

---

## 📞 SOPORTE

Si después de seguir esta guía tienes problemas:

1. Revisar logs de error:
   - Apache: `C:\xampp\apache\logs\error.log`
   - PHP: `C:\xampp\php\logs\php_error_log`
   - CyberTime: `C:\xampp\htdocs\cybertime\logs\error.log`

2. Consultar documentación adicional:
   - `README.md`
   - `PROJECT_RULES.md`
   - `TECH_STACK.md`

3. Verificar requisitos mínimos cumplidos

---

**Versión**: 1.0.0  
**Fecha**: 2024-12-26  
**Tiempo estimado de instalación**: 45-60 minutos  
**Nivel de dificultad**: Intermedio
