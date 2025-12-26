# ⚡ QUICK START - CYBERTIME

## 🚀 Inicio Rápido en 5 Pasos

### 1️⃣ Instalar XAMPP (5 minutos)
```
1. Descargar XAMPP: https://www.apachefriends.org/
2. Instalar en C:\xampp
3. Iniciar Apache y MySQL desde XAMPP Control Panel
```

### 2️⃣ Configurar IP Estática (5 minutos)
```
1. Panel de Control → Redes → Cambiar opciones del adaptador
2. Clic derecho en adaptador → Propiedades
3. IPv4 → Usar la siguiente dirección IP
4. IP: 192.168.1.100 (o la que prefieras)
5. Máscara: 255.255.255.0
6. Puerta de enlace: 192.168.1.1
```

### 3️⃣ Instalar CyberTime (10 minutos)
```
1. Copiar carpeta cybertime a C:\xampp\htdocs\
2. Abrir: http://localhost/cybertime/install/
3. Seguir asistente de instalación
4. Eliminar carpeta install/ al finalizar
```

### 4️⃣ Acceder al Panel Admin (2 minutos)
```
1. Abrir: http://localhost/cybertime/admin/
2. Usuario: admin
3. Contraseña: admin123
4. ¡Cambiar contraseña!
```

### 5️⃣ Configurar PCs Clientes (5 minutos por PC)
```
1. En cada PC, abrir Chrome
2. Ir a: http://192.168.1.100/cybertime/client/
3. Win + R → shell:startup
4. Crear acceso directo:
   "C:\Program Files\Google\Chrome\Application\chrome.exe" --kiosk http://192.168.1.100/cybertime/client/
5. Reiniciar PC
```

---

## ✅ Verificación Rápida

### En el Servidor
- [ ] Apache corriendo (verde en XAMPP)
- [ ] MySQL corriendo (verde en XAMPP)
- [ ] Panel admin accesible
- [ ] Puedes ver el dashboard

### En los Clientes
- [ ] Navegador abre automáticamente
- [ ] Muestra pantalla de bloqueo
- [ ] PC aparece en panel admin
- [ ] Puedes asignar tiempo desde admin

---

## 🎯 Primer Uso

### Asignar Tiempo a una PC

1. En panel admin, busca la PC
2. Clic en "Asignar Tiempo"
3. Selecciona tarifa (ej: 1 hora)
4. Clic en "Asignar Tiempo"
5. ¡La PC se desbloquea automáticamente!

### Agregar Tiempo

1. Busca PC en uso
2. Clic en "Agregar"
3. Ingresa minutos
4. Confirmar

### Detener Sesión

1. Busca PC en uso
2. Clic en "Detener"
3. Confirmar
4. La PC se bloquea

---

## 🔧 Configuración Básica

### Cambiar IP del Servidor

Editar `config.php`:
```php
define('SERVER_IP', '192.168.1.100'); // Tu IP
```

### Cambiar Tarifas

1. Panel admin → Tarifas
2. Agregar/Editar tarifas
3. Guardar

### Agregar PCs

Las PCs se registran automáticamente al acceder por primera vez.
Puedes editarlas desde: Panel admin → Gestión de PCs

---

## 📱 URLs Importantes

| Descripción | URL |
|-------------|-----|
| Panel Admin | `http://192.168.1.100/cybertime/admin/` |
| Cliente | `http://192.168.1.100/cybertime/client/` |
| phpMyAdmin | `http://localhost/phpmyadmin` |
| Instalador | `http://localhost/cybertime/install/` |

---

## 🆘 Problemas Comunes

### "No se puede conectar"
```
Solución: Verificar que Apache esté corriendo y firewall permita puerto 80
```

### "Error de base de datos"
```
Solución: Verificar que MySQL esté corriendo y credenciales en config.php
```

### "PC no se desbloquea"
```
Solución: Verificar que el cliente esté consultando el servidor (F12 → Console)
```

---

## 📚 Más Información

- **Instalación Detallada**: `docs/INSTALL_SERVER.md`
- **Manual Completo**: `README.md`
- **Documentación API**: `docs/API_DOCS.md`

---

## 💡 Tips

1. **Prueba primero con 1 cliente** antes de configurar todos
2. **Anota tu IP** para no olvidarla
3. **Haz backup** de la base de datos semanalmente
4. **Cambia las contraseñas** por defecto inmediatamente

---

**¡Listo! Tu sistema está funcionando en menos de 30 minutos 🎉**

¿Necesitas ayuda? Consulta `README.md` o `docs/INSTALL_SERVER.md`
