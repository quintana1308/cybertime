<?php
/**
 * Generador de Hash de Contraseña
 * Ejecutar: http://localhost/cybertime/generate_password.php
 */

// Cambiar esta contraseña por la que quieras
$nueva_password = 'quingoz';

// Generar hash
$hash = password_hash($nueva_password, PASSWORD_BCRYPT);

echo "<h2>Generador de Hash de Contraseña</h2>";
echo "<p><strong>Contraseña:</strong> " . htmlspecialchars($nueva_password) . "</p>";
echo "<p><strong>Hash:</strong></p>";
echo "<textarea style='width:100%; height:100px; font-family:monospace;'>" . $hash . "</textarea>";
echo "<hr>";
echo "<h3>SQL para actualizar:</h3>";
echo "<textarea style='width:100%; height:150px; font-family:monospace;'>";
echo "UPDATE users SET password = '{$hash}' WHERE username = 'admin';";
echo "</textarea>";
echo "<hr>";
echo "<p><strong>Instrucciones:</strong></p>";
echo "<ol>";
echo "<li>Copia el SQL de arriba</li>";
echo "<li>Abre phpMyAdmin: http://localhost/phpmyadmin</li>";
echo "<li>Selecciona la base de datos 'cybertime'</li>";
echo "<li>Ve a la pestaña 'SQL'</li>";
echo "<li>Pega y ejecuta el SQL</li>";
echo "<li>Elimina este archivo (generate_password.php) por seguridad</li>";
echo "</ol>";
?>
