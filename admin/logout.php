<?php
/**
 * CYBERTIME - Cerrar Sesión
 */

session_start();
require_once __DIR__ . '/../config.php';
require_once INCLUDES_DIR . '/auth.php';

logout_user();
redirect('login.php');
