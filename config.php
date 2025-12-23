<?php
// Configuración de rutas base del proyecto
define('BASE_URL', 'http://localhost/Zapateria');
define('ASSETS_URL', BASE_URL . '/assets');

// Zona horaria
date_default_timezone_set('Europe/Madrid');

// Mostrar errores en desarrollo (cambiar a 0 en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
