# INSTALACIÓN EN PCs CLIENTES

## 📋 TABLA DE CONTENIDOS
1. [Requisitos Previos](#requisitos-previos)
2. [Preparación de la PC Cliente](#preparación-de-la-pc-cliente)
3. [Configuración de Red](#configuración-de-red)
4. [Configuración del Navegador](#configuración-del-navegador)
5. [Primer Acceso al Sistema](#primer-acceso-al-sistema)
6. [Configuración de Inicio Automático](#configuración-de-inicio-automático)
7. [Configuración de Seguridad](#configuración-de-seguridad)
8. [Verificación de Funcionamiento](#verificación-de-funcionamiento)
9. [Solución de Problemas](#solución-de-problemas)
10. [Mantenimiento](#mantenimiento)

---

## 1. REQUISITOS PREVIOS

### 1.1 Hardware Mínimo
- **Procesador**: Intel Pentium 4 o equivalente (1.5 GHz+)
- **RAM**: 2 GB mínimo, 4 GB recomendado
- **Disco Duro**: 500 MB de espacio libre
- **Tarjeta de Red**: Adaptador WiFi o Ethernet
- **Monitor**: Resolución mínima 1024x768

### 1.2 Software
- **Sistema Operativo**: Windows 7, 8, 10 u 11 (32 o 64 bits)
- **Navegador Web**: 
  - Google Chrome 90+ (Recomendado)
  - Mozilla Firefox 88+
  - Microsoft Edge 90+
- **NO se requiere**:
  - PHP
  - Apache
  - MySQL/MariaDB
  - Ningún servidor web

### 1.3 Información Necesaria
Antes de comenzar, necesitas conocer:
- **IP del Servidor**: Ejemplo: `192.168.1.100`
- **Puerto del Servidor**: Generalmente `80` (o el configurado)
- **Nombre/Número de la PC**: Ejemplo: `PC-01`, `PC-02`, etc.

---

## 2. PREPARACIÓN DE LA PC CLIENTE

### 2.1 Actualizar Sistema Operativo

**Windows 10/11:**
```
1. Configuración → Actualización y seguridad
2. Windows Update → Buscar actualizaciones
3. Instalar todas las actualizaciones disponibles
4. Reiniciar si es necesario
```

**Windows 7/8:**
```
1. Panel de Control → Sistema y seguridad
2. Windows Update
3. Buscar actualizaciones
4. Instalar actualizaciones importantes
```

### 2.2 Instalar/Actualizar Navegador

#### Instalar Google Chrome (Recomendado)

```
1. Descargar desde: https://www.google.com/chrome/
2. Ejecutar instalador
3. Seguir asistente de instalación
4. Establecer como navegador predeterminado
```

#### Actualizar Navegador Existente

**Chrome:**
```
1. Abrir Chrome
2. Menú (⋮) → Ayuda → Información de Google Chrome
3. Se actualizará automáticamente
4. Reiniciar navegador
```

**Firefox:**
```
1. Abrir Firefox
2. Menú (☰) → Ayuda → Acerca de Firefox
3. Se actualizará automáticamente
4. Reiniciar navegador
```

### 2.3 Limpiar PC (Opcional pero Recomendado)

```
1. Desinstalar programas innecesarios
2. Ejecutar Liberador de espacio en disco
3. Vaciar Papelera de reciclaje
4. Limpiar archivos temporales
```

---

## 3. CONFIGURACIÓN DE RED

### 3.1 Conectar a la Red del Cyber

#### Conexión WiFi

```
1. Clic en icono de red (barra de tareas)
2. Seleccionar red WiFi del cyber
3. Clic en "Conectar"
4. Ingresar contraseña WiFi
5. Esperar confirmación de conexión
```

#### Conexión por Cable (Ethernet)

```
1. Conectar cable Ethernet a la PC
2. Windows detectará automáticamente la conexión
3. Esperar a que obtenga IP automáticamente (DHCP)
```

### 3.2 Verificar Conectividad con el Servidor

**Método 1: Ping desde CMD**

```
1. Presionar Win + R
2. Escribir: cmd
3. Presionar Enter
4. En la ventana negra, escribir:
   ping 192.168.1.100
   (Reemplazar con la IP de tu servidor)
5. Debe responder:
   Respuesta desde 192.168.1.100: bytes=32 tiempo<1ms TTL=128
   
Si aparece "Tiempo de espera agotado":
- Verificar que el servidor esté encendido
- Verificar que estén en la misma red
- Verificar firewall del servidor
```

**Método 2: Acceso desde Navegador**

```
1. Abrir navegador
2. Escribir en barra de direcciones:
   http://192.168.1.100/cybertime/
3. Debe cargar la página del sistema
4. Si no carga, revisar configuración de red
```

### 3.3 Obtener IP de la PC Cliente (Opcional)

```
1. Abrir CMD (Win + R → cmd)
2. Escribir: ipconfig
3. Buscar "Dirección IPv4"
4. Anotar la IP (ejemplo: 192.168.1.105)
5. Esta IP puede usarse para identificar la PC
```

---

## 4. CONFIGURACIÓN DEL NAVEGADOR

### 4.1 Configuración de Google Chrome

#### Paso 1: Configurar Página de Inicio

```
1. Abrir Chrome
2. Menú (⋮) → Configuración
3. Sección "Al iniciar"
4. Seleccionar "Abrir una página o un conjunto de páginas específicas"
5. Clic en "Agregar una página nueva"
6. Ingresar: http://192.168.1.100/cybertime/client/
   (Reemplazar con IP de tu servidor)
7. Clic en "Agregar"
```

#### Paso 2: Deshabilitar Restauración de Pestañas

```
1. Configuración → Al iniciar
2. Asegurar que NO esté marcado:
   "Continuar donde lo dejaste"
```

#### Paso 3: Configurar Pantalla Completa Automática (Opcional)

```
1. Instalar extensión "Auto Fullscreen" (si está disponible)
2. O usar atajo: F11 al abrir el navegador
```

#### Paso 4: Deshabilitar Notificaciones

```
1. Configuración → Privacidad y seguridad
2. Configuración de sitios → Notificaciones
3. Seleccionar "No permitir que los sitios envíen notificaciones"
```

#### Paso 5: Deshabilitar Actualizaciones Automáticas (Opcional)

```
⚠️ Solo si causa problemas de rendimiento
1. Descargar herramienta: Chrome Update Disabler
2. Ejecutar y deshabilitar actualizaciones
```

### 4.2 Configuración de Mozilla Firefox

#### Paso 1: Configurar Página de Inicio

```
1. Abrir Firefox
2. Menú (☰) → Configuración
3. Sección "Inicio"
4. En "Página de inicio y ventanas nuevas"
5. Seleccionar "Direcciones web personalizadas"
6. Ingresar: http://192.168.1.100/cybertime/client/
```

#### Paso 2: Configuración de Privacidad

```
1. Configuración → Privacidad y seguridad
2. Cookies y datos del sitio
3. Marcar "Eliminar cookies y datos del sitio al cerrar Firefox"
4. Clic en "Excepciones"
5. Agregar: http://192.168.1.100
6. Clic en "Permitir"
```

### 4.3 Configuración de Microsoft Edge

#### Paso 1: Configurar Página de Inicio

```
1. Abrir Edge
2. Menú (⋯) → Configuración
3. Inicio, página principal y pestañas nuevas
4. Seleccionar "Abrir estas páginas"
5. Agregar: http://192.168.1.100/cybertime/client/
```

---

## 5. PRIMER ACCESO AL SISTEMA

### 5.1 Acceder a la Interfaz del Cliente

```
1. Abrir navegador configurado
2. Ir a: http://192.168.1.100/cybertime/client/
   (Usar la IP de tu servidor)
3. Debe aparecer pantalla de bloqueo con mensaje:
   "PC Bloqueada - Esperando asignación de tiempo"
```

### 5.2 Registrar la PC en el Sistema

**Método 1: Registro Automático**

```
1. Al acceder por primera vez, el sistema detecta la PC
2. Se genera un ID automático
3. La PC aparece en el panel de administración
4. El administrador puede asignarle un nombre descriptivo
```

**Método 2: Registro Manual**

```
1. En la pantalla del cliente, buscar "ID de PC"
2. Anotar el ID mostrado (ejemplo: PC-192-168-1-105)
3. Informar al administrador
4. El administrador registra la PC en el panel
```

### 5.3 Verificar Registro Exitoso

```
1. En el panel de administración (PC Principal)
2. Debe aparecer la nueva PC en la lista
3. Estado: "Disponible" o "Bloqueada"
4. Nombre: Asignar nombre descriptivo (ej: "PC-01")
```

---

## 6. CONFIGURACIÓN DE INICIO AUTOMÁTICO

### 6.1 Configurar Inicio Automático del Navegador

#### Método 1: Carpeta de Inicio de Windows

**Windows 10/11:**
```
1. Presionar Win + R
2. Escribir: shell:startup
3. Presionar Enter (se abre carpeta de inicio)
4. Clic derecho → Nuevo → Acceso directo
5. Ubicación del elemento:
   "C:\Program Files\Google\Chrome\Application\chrome.exe" --start-fullscreen --kiosk http://192.168.1.100/cybertime/client/
6. Nombre: CyberTime Cliente
7. Clic en "Finalizar"
```

**Explicación de parámetros:**
- `--start-fullscreen`: Inicia en pantalla completa
- `--kiosk`: Modo quiosco (oculta barra de direcciones)
- URL: Dirección del sistema

**Windows 7/8:**
```
1. Inicio → Todos los programas
2. Clic derecho en "Inicio" → Abrir
3. Seguir pasos 4-7 de arriba
```

#### Método 2: Registro de Windows (Avanzado)

```
1. Presionar Win + R
2. Escribir: regedit
3. Navegar a:
   HKEY_CURRENT_USER\Software\Microsoft\Windows\CurrentVersion\Run
4. Clic derecho → Nuevo → Valor de cadena
5. Nombre: CyberTimeClient
6. Valor: "C:\Program Files\Google\Chrome\Application\chrome.exe" --kiosk http://192.168.1.100/cybertime/client/
7. Cerrar regedit
```

### 6.2 Configurar Inicio de Sesión Automático (Opcional)

⚠️ **ADVERTENCIA**: Solo para PCs dedicadas del cyber, reduce seguridad

**Windows 10/11:**
```
1. Presionar Win + R
2. Escribir: netplwiz
3. Presionar Enter
4. Desmarcar: "Los usuarios deben escribir su nombre y contraseña"
5. Clic en "Aplicar"
6. Ingresar contraseña de la cuenta
7. Clic en "Aceptar"
8. Reiniciar para probar
```

### 6.3 Deshabilitar Protector de Pantalla

```
1. Clic derecho en Escritorio → Personalizar
2. Pantalla de bloqueo → Configuración del protector de pantalla
3. Protector de pantalla: (Ninguno)
4. Clic en "Aplicar"
```

### 6.4 Configurar Opciones de Energía

```
1. Panel de Control → Opciones de energía
2. Seleccionar plan: "Alto rendimiento"
3. Cambiar la configuración del plan
4. Apagar pantalla: Nunca
5. Suspender el equipo: Nunca
6. Guardar cambios
```

---

## 7. CONFIGURACIÓN DE SEGURIDAD

### 7.1 Bloquear Acceso a Configuración del Sistema

#### Método 1: Cuenta de Usuario Limitada

```
1. Panel de Control → Cuentas de usuario
2. Administrar otra cuenta → Agregar un nuevo usuario
3. Nombre: Cliente01 (o similar)
4. Tipo de cuenta: Usuario estándar
5. Sin contraseña (o contraseña conocida solo por admin)
6. Usar esta cuenta para el inicio automático
```

#### Método 2: Políticas de Grupo (Windows Pro)

```
1. Presionar Win + R
2. Escribir: gpedit.msc
3. Navegar a:
   Configuración de usuario → Plantillas administrativas → Panel de control
4. Doble clic en "Prohibir acceso al Panel de control"
5. Seleccionar "Habilitada"
6. Aplicar y Aceptar
```

### 7.2 Deshabilitar Administrador de Tareas

```
1. Presionar Win + R
2. Escribir: regedit
3. Navegar a:
   HKEY_CURRENT_USER\Software\Microsoft\Windows\CurrentVersion\Policies\System
4. Clic derecho → Nuevo → Valor DWORD (32 bits)
5. Nombre: DisableTaskMgr
6. Valor: 1
7. Cerrar regedit
8. Reiniciar PC
```

### 7.3 Ocultar Iconos del Escritorio

```
1. Clic derecho en Escritorio
2. Ver → Desmarcar "Mostrar iconos del escritorio"
```

### 7.4 Deshabilitar Menú Contextual del Escritorio

```
1. Descargar herramienta: Right Click Disabler
2. Ejecutar e instalar
3. Configurar para deshabilitar clic derecho
```

---

## 8. VERIFICACIÓN DE FUNCIONAMIENTO

### 8.1 Prueba de Conexión

```
1. Reiniciar la PC Cliente
2. Debe iniciar automáticamente el navegador
3. Debe cargar la interfaz de CyberTime
4. Debe mostrar pantalla de bloqueo
```

### 8.2 Prueba de Asignación de Tiempo

```
1. Desde el panel de administración (PC Principal)
2. Seleccionar la PC Cliente
3. Asignar tiempo (ejemplo: 30 minutos)
4. En la PC Cliente debe:
   - Desbloquearse automáticamente
   - Mostrar contador de tiempo
   - Permitir uso normal de la PC
```

### 8.3 Prueba de Finalización de Tiempo

```
1. Esperar a que el tiempo llegue a 0
2. O detener tiempo desde panel de administración
3. La PC Cliente debe:
   - Bloquearse automáticamente
   - Mostrar mensaje de tiempo agotado
   - No permitir uso hasta nueva asignación
```

### 8.4 Prueba de Reconexión

```
1. Desconectar cable de red (o WiFi)
2. La PC Cliente debe mostrar mensaje de desconexión
3. Reconectar red
4. La PC debe recuperar su estado automáticamente
```

---

## 9. SOLUCIÓN DE PROBLEMAS

### 9.1 No se puede acceder al servidor

**Síntoma**: Navegador no carga la página

**Soluciones**:
```
1. Verificar conexión de red:
   - Cable conectado correctamente
   - WiFi conectado a red correcta
   
2. Verificar IP del servidor:
   - Hacer ping: ping 192.168.1.100
   - Si no responde, verificar que servidor esté encendido
   
3. Verificar URL correcta:
   - http://192.168.1.100/cybertime/client/
   - No https://
   - No olvidar /cybertime/client/
   
4. Limpiar caché del navegador:
   - Chrome: Ctrl + Shift + Delete
   - Seleccionar "Todo el tiempo"
   - Marcar "Imágenes y archivos en caché"
   - Borrar datos
```

### 9.2 Pantalla de bloqueo no funciona

**Síntoma**: Se puede usar la PC aunque esté bloqueada

**Soluciones**:
```
1. Verificar JavaScript habilitado:
   - Chrome: Configuración → Privacidad → Configuración de sitios
   - JavaScript debe estar "Permitido"
   
2. Actualizar navegador a última versión

3. Probar con otro navegador (Chrome recomendado)

4. Verificar que no haya extensiones interfiriendo:
   - Abrir en modo incógnito: Ctrl + Shift + N
   - Si funciona, deshabilitar extensiones
```

### 9.3 No se actualiza el tiempo

**Síntoma**: Contador de tiempo no cambia

**Soluciones**:
```
1. Verificar conexión a internet/red

2. Abrir consola del navegador:
   - Presionar F12
   - Pestaña "Console"
   - Buscar errores en rojo
   
3. Recargar página: Ctrl + F5 (recarga forzada)

4. Verificar que el servidor esté respondiendo:
   - Abrir: http://192.168.1.100/cybertime/api/client/status.php?pc_id=1
   - Debe retornar JSON
```

### 9.4 Navegador no inicia automáticamente

**Síntoma**: Al encender PC, no se abre el navegador

**Soluciones**:
```
1. Verificar acceso directo en carpeta de inicio:
   - Win + R → shell:startup
   - Debe existir acceso directo a Chrome
   
2. Verificar ruta del acceso directo:
   - Clic derecho → Propiedades
   - Verificar que ruta de chrome.exe sea correcta
   
3. Probar manualmente el acceso directo:
   - Doble clic en el acceso directo
   - Si no funciona, recrearlo
   
4. Verificar que usuario tenga permisos:
   - Iniciar sesión con cuenta correcta
```

### 9.5 PC muy lenta

**Síntoma**: Sistema responde lento

**Soluciones**:
```
1. Cerrar programas innecesarios:
   - Ctrl + Shift + Esc (Administrador de tareas)
   - Finalizar procesos que consuman mucha CPU/RAM
   
2. Aumentar RAM si es posible (mínimo 4GB)

3. Desfragmentar disco (solo HDD, no SSD):
   - Buscar "Desfragmentar"
   - Optimizar unidad C:
   
4. Verificar virus/malware:
   - Ejecutar Windows Defender
   - Escaneo completo
```

### 9.6 Pantalla en blanco

**Síntoma**: Solo se ve pantalla blanca

**Soluciones**:
```
1. Esperar 30 segundos (puede estar cargando)

2. Verificar URL correcta en barra de direcciones

3. Abrir consola del navegador (F12):
   - Buscar errores
   - Tomar captura y reportar a soporte
   
4. Limpiar caché y cookies:
   - Ctrl + Shift + Delete
   - Borrar todo
   
5. Reinstalar navegador si persiste
```

---

## 10. MANTENIMIENTO

### 10.1 Mantenimiento Semanal

```
✅ Verificar conexión de red estable
✅ Limpiar archivos temporales
✅ Verificar espacio en disco (mínimo 1GB libre)
✅ Reiniciar PC al menos una vez
```

### 10.2 Mantenimiento Mensual

```
✅ Actualizar navegador web
✅ Ejecutar Windows Update
✅ Escanear con antivirus
✅ Limpiar polvo del hardware
✅ Verificar cables de red
```

### 10.3 Limpieza de Caché

```
Cada 15 días:
1. Abrir navegador
2. Ctrl + Shift + Delete
3. Seleccionar "Todo el tiempo"
4. Marcar:
   - Historial de navegación
   - Cookies y otros datos de sitios
   - Imágenes y archivos en caché
5. Borrar datos
6. Reiniciar navegador
```

### 10.4 Respaldo de Configuración

```
Anotar en documento:
- IP del servidor
- Nombre de la PC
- ID de la PC en el sistema
- Usuario de Windows utilizado
- Configuraciones especiales aplicadas

Guardar en lugar seguro para reinstalación rápida
```

---

## 📝 CHECKLIST DE INSTALACIÓN POR PC

Imprimir y completar para cada PC:

```
PC #: _______  Nombre: _____________  IP: _______________

□ Windows actualizado
□ Navegador instalado/actualizado
□ Conexión de red configurada
□ Ping al servidor exitoso
□ Acceso a interfaz cliente verificado
□ PC registrada en el sistema
□ Inicio automático configurado
□ Inicio de sesión automático (opcional)
□ Protector de pantalla deshabilitado
□ Opciones de energía configuradas
□ Seguridad configurada (usuario limitado)
□ Prueba de asignación de tiempo exitosa
□ Prueba de bloqueo exitosa
□ Prueba de reconexión exitosa
□ Documentación de configuración guardada

Instalado por: ________________  Fecha: __________
Verificado por: _______________  Fecha: __________
```

---

## 🎯 CONFIGURACIÓN RÁPIDA (RESUMEN)

Para instalación express (15 minutos por PC):

```
1. Conectar a red del cyber
2. Instalar/actualizar Chrome
3. Configurar página de inicio:
   http://[IP_SERVIDOR]/cybertime/client/
4. Crear acceso directo en inicio:
   chrome.exe --kiosk http://[IP_SERVIDOR]/cybertime/client/
5. Configurar inicio de sesión automático
6. Deshabilitar protector de pantalla
7. Opciones de energía: Nunca apagar/suspender
8. Reiniciar y verificar
```

---

## 📞 SOPORTE

Si tienes problemas después de seguir esta guía:

1. Verificar que el servidor esté funcionando
2. Consultar sección de solución de problemas
3. Revisar logs del navegador (F12 → Console)
4. Contactar al administrador del sistema

---

**Versión**: 1.0.0  
**Fecha**: 2024-12-26  
**Tiempo estimado de instalación**: 15-30 minutos por PC  
**Nivel de dificultad**: Básico-Intermedio
