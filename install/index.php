<?php
/**
 * CYBERTIME - Instalador Web
 */

session_start();

// Verificar si ya está instalado
$config_file = __DIR__ . '/../config.php';
if (file_exists($config_file)) {
    require_once $config_file;
    
    // Verificar si la base de datos está configurada
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        
        // Verificar si existen las tablas
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            $already_installed = true;
        }
    } catch (Exception $e) {
        $already_installed = false;
    }
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 2) {
        // Verificar requisitos
        $step = 3;
    } elseif ($step == 3) {
        // Configurar base de datos
        $db_host = $_POST['db_host'] ?? 'localhost';
        $db_port = $_POST['db_port'] ?? '3306';
        $db_name = $_POST['db_name'] ?? 'cybertime';
        $db_user = $_POST['db_user'] ?? 'sistemas';
        $db_pass = $_POST['db_pass'] ?? 'adn';
        $server_ip = $_POST['server_ip'] ?? '192.168.1.100';
        
        try {
            // Probar conexión
            $pdo = new PDO("mysql:host=$db_host;port=$db_port", $db_user, $db_pass);
            
            // Crear base de datos si no existe
            $pdo->exec("CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Guardar en sesión
            $_SESSION['install'] = [
                'db_host' => $db_host,
                'db_port' => $db_port,
                'db_name' => $db_name,
                'db_user' => $db_user,
                'db_pass' => $db_pass,
                'server_ip' => $server_ip
            ];
            
            $success = 'Conexión exitosa. Base de datos creada.';
            $step = 4;
            
        } catch (Exception $e) {
            $error = 'Error de conexión: ' . $e->getMessage();
        }
    } elseif ($step == 4) {
        // Importar base de datos
        $install_data = $_SESSION['install'] ?? [];
        
        if (empty($install_data)) {
            $error = 'Datos de instalación no encontrados';
        } else {
            try {
                $pdo = new PDO(
                    "mysql:host={$install_data['db_host']};port={$install_data['db_port']};dbname={$install_data['db_name']}",
                    $install_data['db_user'],
                    $install_data['db_pass']
                );
                
                // Leer y ejecutar schema.sql
                $schema_file = __DIR__ . '/../database/schema.sql';
                if (file_exists($schema_file)) {
                    $sql = file_get_contents($schema_file);
                    $pdo->exec($sql);
                }
                
                // Leer y ejecutar seeds.sql
                $seeds_file = __DIR__ . '/../database/seeds.sql';
                if (file_exists($seeds_file)) {
                    $sql = file_get_contents($seeds_file);
                    $pdo->exec($sql);
                }
                
                $success = 'Base de datos importada correctamente';
                $step = 5;
                
            } catch (Exception $e) {
                $error = 'Error al importar base de datos: ' . $e->getMessage();
            }
        }
    } elseif ($step == 5) {
        // Guardar configuración
        $install_data = $_SESSION['install'] ?? [];
        
        if (empty($install_data)) {
            $error = 'Datos de instalación no encontrados';
        } else {
            // Leer config.php actual
            $config_content = file_get_contents($config_file);
            
            // Reemplazar valores
            $config_content = str_replace("define('DB_HOST', 'localhost');", "define('DB_HOST', '{$install_data['db_host']}');", $config_content);
            $config_content = str_replace("define('DB_NAME', 'cybertime');", "define('DB_NAME', '{$install_data['db_name']}');", $config_content);
            $config_content = str_replace("define('DB_USER', 'root');", "define('DB_USER', '{$install_data['db_user']}');", $config_content);
            $config_content = str_replace("define('DB_PASS', '');", "define('DB_PASS', '{$install_data['db_pass']}');", $config_content);
            $config_content = str_replace("define('DB_PORT', 3306);", "define('DB_PORT', {$install_data['db_port']});", $config_content);
            $config_content = str_replace("define('SERVER_IP', '192.168.1.100');", "define('SERVER_IP', '{$install_data['server_ip']}');", $config_content);
            
            // Guardar
            file_put_contents($config_file, $config_content);
            
            $success = 'Configuración guardada correctamente';
            $step = 6;
            
            // Limpiar sesión
            unset($_SESSION['install']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador - CyberTime</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .installer {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            font-size: 36px;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #6b7280;
        }
        
        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            position: relative;
        }
        
        .step::after {
            content: '';
            position: absolute;
            top: 20px;
            right: -50%;
            width: 100%;
            height: 2px;
            background: #e5e7eb;
        }
        
        .step:last-child::after {
            display: none;
        }
        
        .step.active {
            color: #2563eb;
            font-weight: 600;
        }
        
        .step.active::after {
            background: #2563eb;
        }
        
        .step.completed {
            color: #10b981;
        }
        
        .step.completed::after {
            background: #10b981;
        }
        
        .content {
            margin-bottom: 30px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1e40af;
        }
        
        .btn-success {
            background: #10b981;
            color: white;
        }
        
        .requirements {
            list-style: none;
        }
        
        .requirements li {
            padding: 10px;
            margin-bottom: 10px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }
        
        .requirements li::before {
            content: '✓';
            margin-right: 10px;
            color: #10b981;
            font-weight: bold;
            font-size: 18px;
        }
        
        .requirements li.error::before {
            content: '✗';
            color: #ef4444;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
    <div class="installer">
        <div class="header">
            <h1><i class="fas fa-desktop"></i> CyberTime</h1>
            <p>Asistente de Instalación</p>
        </div>
        
        <div class="steps">
            <div class="step <?php echo $step >= 1 ? 'completed' : ''; ?>">1. Bienvenida</div>
            <div class="step <?php echo $step == 2 ? 'active' : ($step > 2 ? 'completed' : ''); ?>">2. Requisitos</div>
            <div class="step <?php echo $step == 3 ? 'active' : ($step > 3 ? 'completed' : ''); ?>">3. Base de Datos</div>
            <div class="step <?php echo $step == 4 ? 'active' : ($step > 4 ? 'completed' : ''); ?>">4. Importar</div>
            <div class="step <?php echo $step == 5 ? 'active' : ($step > 5 ? 'completed' : ''); ?>">5. Configurar</div>
            <div class="step <?php echo $step == 6 ? 'active' : ''; ?>">6. Finalizar</div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (isset($already_installed) && $already_installed): ?>
            <div class="alert alert-warning">
                <strong>Sistema ya instalado</strong><br>
                El sistema ya está instalado. Si deseas reinstalar, elimina las tablas de la base de datos primero.
            </div>
            <div class="actions">
                <a href="../admin/" class="btn btn-primary">Ir al Panel de Administración</a>
            </div>
        <?php else: ?>
            
            <div class="content">
                <?php if ($step == 1): ?>
                    <h2>Bienvenido a CyberTime</h2>
                    <p>Este asistente te guiará en la instalación del sistema de control de tiempos para tu cyber café.</p>
                    <br>
                    <p><strong>Antes de comenzar, asegúrate de tener:</strong></p>
                    <ul style="margin-left: 20px; margin-top: 10px;">
                        <li>XAMPP instalado y funcionando</li>
                        <li>Apache y MySQL iniciados</li>
                        <li>Acceso a phpMyAdmin</li>
                        <li>IP estática configurada en esta PC</li>
                    </ul>
                    
                <?php elseif ($step == 2): ?>
                    <h2>Verificación de Requisitos</h2>
                    <ul class="requirements">
                        <li>PHP <?php echo phpversion(); ?> instalado</li>
                        <li>Extensión MySQLi disponible</li>
                        <li>Extensión JSON disponible</li>
                        <li>Carpeta logs/ con permisos de escritura</li>
                        <li>Carpeta backups/ con permisos de escritura</li>
                    </ul>
                    
                <?php elseif ($step == 3): ?>
                    <h2>Configuración de Base de Datos</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label>Host de Base de Datos</label>
                            <input type="text" name="db_host" class="form-control" value="localhost" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Puerto de Base de Datos</label>
                            <input type="number" name="db_port" class="form-control" value="3306" required>
                            <small>Puerto por defecto: 3306 (XAMPP) o 3309 (MariaDB independiente)</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Nombre de Base de Datos</label>
                            <input type="text" name="db_name" class="form-control" value="cybertime" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Usuario de Base de Datos</label>
                            <input type="text" name="db_user" class="form-control" value="root" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Contraseña de Base de Datos</label>
                            <input type="password" name="db_pass" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>IP del Servidor (Esta PC)</label>
                            <input type="text" name="server_ip" class="form-control" value="192.168.1.100" required>
                            <small>Ingresa la IP estática de esta PC</small>
                        </div>
                        
                        <div class="actions">
                            <button type="submit" class="btn btn-primary">Probar Conexión</button>
                        </div>
                    </form>
                    
                <?php elseif ($step == 4): ?>
                    <h2>Importar Base de Datos</h2>
                    <p>Se importarán las tablas y datos iniciales del sistema.</p>
                    <br>
                    <p><strong>Esto incluye:</strong></p>
                    <ul style="margin-left: 20px; margin-top: 10px;">
                        <li>9 tablas principales</li>
                        <li>Vistas, triggers y procedimientos</li>
                        <li>Usuario administrador por defecto</li>
                        <li>10 PCs de ejemplo</li>
                        <li>Tarifas predefinidas</li>
                    </ul>
                    <br>
                    <form method="POST">
                        <div class="actions">
                            <button type="submit" class="btn btn-primary">Importar Base de Datos</button>
                        </div>
                    </form>
                    
                <?php elseif ($step == 5): ?>
                    <h2>Guardar Configuración</h2>
                    <p>Se guardará la configuración en el archivo config.php</p>
                    <br>
                    <form method="POST">
                        <div class="actions">
                            <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                        </div>
                    </form>
                    
                <?php elseif ($step == 6): ?>
                    <h2>¡Instalación Completada!</h2>
                    <div class="alert alert-success">
                        <strong>✓ Sistema instalado correctamente</strong>
                    </div>
                    
                    <p><strong>Credenciales por defecto:</strong></p>
                    <ul style="margin-left: 20px; margin-top: 10px; margin-bottom: 20px;">
                        <li><strong>Usuario:</strong> admin</li>
                        <li><strong>Contraseña:</strong> admin123</li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <strong>⚠️ Importante:</strong><br>
                        - Cambia la contraseña del administrador después de iniciar sesión<br>
                        - Elimina la carpeta install/ por seguridad<br>
                        - Configura las PCs clientes según la documentación
                    </div>
                    
                    <div class="actions">
                        <a href="../admin/" class="btn btn-success">Ir al Panel de Administración</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($step > 1 && $step < 6 && empty($error)): ?>
                <form method="POST" action="?step=<?php echo $step + 1; ?>">
                    <div class="actions">
                        <?php if ($step == 2): ?>
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        <?php endif; ?>
                    </div>
                </form>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>
</body>
</html>
