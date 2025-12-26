<?php
/**
 * CYBERTIME - Header del Panel de Administración
 */

if (!defined('ADMIN_PAGE')) {
    die('Acceso directo no permitido');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? SYSTEM_NAME; ?> - <?php echo SYSTEM_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo SYSTEM_NAME; ?></h2>
                <p><?php echo BUSINESS_NAME; ?></p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item <?php echo ($current_page === 'dashboard') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                    <span class="nav-text">Dashboard</span>
                </a>
                
                <a href="pcs.php" class="nav-item <?php echo ($current_page === 'pcs') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-desktop"></i></span>
                    <span class="nav-text">Gestión de PCs</span>
                </a>
                
                <a href="pricing.php" class="nav-item <?php echo ($current_page === 'pricing') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-dollar-sign"></i></span>
                    <span class="nav-text">Tarifas</span>
                </a>
                
                <a href="reports.php" class="nav-item <?php echo ($current_page === 'reports') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                    <span class="nav-text">Reportes</span>
                </a>
                
                <a href="settings.php" class="nav-item <?php echo ($current_page === 'settings') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-cog"></i></span>
                    <span class="nav-text">Configuración</span>
                </a>
                
                <?php if (is_admin()): ?>
                <a href="users.php" class="nav-item <?php echo ($current_page === 'users') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    <span class="nav-text">Usuarios</span>
                </a>
                <?php endif; ?>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <strong><?php echo escape_html($_SESSION['full_name'] ?? 'Usuario'); ?></strong>
                    <small><?php echo escape_html($_SESSION['user_role'] ?? ''); ?></small>
                </div>
                <a href="logout.php" class="btn btn-logout">Cerrar Sesión</a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h1><?php echo $page_title ?? 'Dashboard'; ?></h1>
                <div class="header-actions">
                    <span class="server-ip">Servidor: <?php echo SERVER_IP; ?></span>
                    <span class="current-time" id="currentTime"></span>
                </div>
            </header>
            
            <div class="content-body">
