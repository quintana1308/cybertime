# 🚀 GUÍA RÁPIDA DE INSTALACIÓN - CYBERTIME

## ⚡ Instalación Express (30 minutos)

### 1. Requisitos Previos
- ✅ XAMPP 7.4+ instalado
- ✅ Apache y MySQL iniciados
- ✅ IP estática configurada (ej: 192.168.1.100)

### 2. Instalación del Sistema

#### Opción A: Instalador Web (Recomendado)

1. **Copiar archivos**
   ```
   Copiar carpeta cybertime a: C:\xampp\htdocs\
   ```

2. **Abrir instalador**
   ```
   http://localhost/cybertime/install/
   ```

3. **Seguir asistente**
   - Verificar requisitos
   - Configurar base de datos
   - Importar datos
   - Finalizar

4. **Eliminar instalador**
   ```
   Eliminar carpeta: C:\xampp\htdocs\cybertime\install\
   ```

#### Opción B: Instalación Manual

1. **Crear base de datos**
   - Abrir phpMyAdmin: `http://localhost/phpmyadmin`
   - Crear base de datos: `cybertime`
   - Importar: `database/schema.sql`
   - Importar: `database/seeds.sql`

2. **Configurar sistema**
   - Editar: `config.php`
   - Cambiar `SERVER_IP` a tu IP estática
   - Cambiar credenciales de DB si es necesario

3. **Verificar permisos**
   - Carpeta `logs/` debe tener permisos de escritura
   - Carpeta `backups/` debe tener permisos de escritura

### 3. Primer Acceso

1. **Abrir panel admin**
   ```
   http://localhost/cybertime/admin/
   ```

2. **Iniciar sesión**
   - Usuario: `admin`
   - Contraseña: `admin123`

3. **Cambiar contraseña** (Importante)

### 4. Configurar PCs Clientes

En cada PC cliente:

1. **Abrir navegador Chrome**

2. **Ir a URL del servidor**
   ```
   http://192.168.1.100/cybertime/client/
   ```

3. **Configurar inicio automático**
   - Presionar `Win + R`
   - Escribir: `shell:startup`
   - Crear acceso directo con:
   ```
   "C:\Program Files\Google\Chrome\Application\chrome.exe" --kiosk http://192.168.1.100/cybertime/client/
   ```

4. **Reiniciar PC** para verificar

---

## 📋 Checklist de Instalación

### PC Principal (Servidor)
- [ ] XAMPP instalado
- [ ] Apache iniciado (puerto 80)
- [ ] MySQL iniciado (puerto 3306)
- [ ] IP estática configurada
- [ ] Base de datos creada
- [ ] Sistema instalado en `C:\xampp\htdocs\cybertime\`
- [ ] Acceso al panel admin verificado
- [ ] Contraseña de admin cambiada
- [ ] Firewall configurado (permitir puerto 80)

### PCs Clientes
- [ ] Chrome instalado
- [ ] Conexión a red del cyber
- [ ] Acceso a URL del servidor verificado
- [ ] Inicio automático configurado
- [ ] Protector de pantalla deshabilitado
- [ ] Opciones de energía: Nunca suspender

---

## 🔧 Configuración de Firewall

### Windows Firewall

1. Buscar "Firewall de Windows Defender"
2. Clic en "Configuración avanzada"
3. Clic en "Reglas de entrada"
4. Clic en "Nueva regla..."
5. Tipo: Puerto
6. Protocolo: TCP
7. Puerto: 80
8. Acción: Permitir
9. Nombre: Apache CyberTime
10. Finalizar

---

## 🎯 URLs Importantes

- **Panel Admin**: `http://192.168.1.100/cybertime/admin/`
- **Cliente**: `http://192.168.1.100/cybertime/client/`
- **API**: `http://192.168.1.100/cybertime/api/`
- **phpMyAdmin**: `http://localhost/phpmyadmin`

---

## 🆘 Solución Rápida de Problemas

### Apache no inicia
```
Problema: Puerto 80 ocupado
Solución: Cambiar puerto en httpd.conf o detener servicio que usa puerto 80
```

### No se conectan los clientes
```
Problema: Firewall bloqueando
Solución: Permitir Apache en firewall (ver sección arriba)
```

### Error de base de datos
```
Problema: Credenciales incorrectas
Solución: Verificar config.php y probar en phpMyAdmin
```

---

## 📚 Documentación Completa

Para instrucciones detalladas, consulta:

- **Servidor**: `docs/INSTALL_SERVER.md` (45-60 minutos)
- **Clientes**: `docs/INSTALL_CLIENT.md` (15-30 minutos por PC)
- **Manual de Usuario**: `docs/USER_MANUAL.md`
- **API**: `docs/API_DOCS.md`

---

## 💡 Consejos

1. **Usa IP estática** en el servidor para evitar cambios
2. **Prueba con 1 cliente** antes de configurar todos
3. **Haz backup** de la base de datos regularmente
4. **Cambia las contraseñas** por defecto
5. **Documenta tu configuración** (IPs, nombres de PC, etc.)

---

## 📞 Soporte

Si tienes problemas:

1. Revisa los logs:
   - Apache: `C:\xampp\apache\logs\error.log`
   - PHP: `C:\xampp\php\logs\php_error_log`
   - CyberTime: `C:\xampp\htdocs\cybertime\logs\error.log`

2. Consulta la documentación completa

3. Verifica que todos los requisitos estén cumplidos

---

**¡Listo para comenzar! 🚀**

Tiempo estimado total: 30-45 minutos para servidor + 15 minutos por cliente
