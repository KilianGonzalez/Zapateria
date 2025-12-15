<?php
session_start();

// Obtener la URL solicitada
$url = isset($_GET['url']) ? $_GET['url'] : 'producto/index';
$url = rtrim($url, '/');
$url = explode('/', $url);

// Determinar controlador, método y parámetros
$controllerName = ucfirst($url[0]) . 'Controller';
$method = isset($url[1]) ? $url[1] : 'index';
$params = array_slice($url, 2);

// Ruta del controlador
$controllerFile = 'controllers/' . $controllerName . '.php';

// Verificar que existe el controlador
if (file_exists($controllerFile)) {
    // Cargar Database primero
    require_once 'controllers/Database.php';
    
    // Cargar el controlador
    require_once $controllerFile;
    
    // Verificar que la clase existe
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        
        // Verificar que existe el método
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
        } else {
            http_response_code(404);
            die("Método no encontrado: " . $method);
        }
    } else {
        http_response_code(404);
        die("Clase no encontrada: " . $controllerName);
    }
} else {
    http_response_code(404);
    die("Controlador no encontrado: " . $controllerName . " (Buscando en: " . $controllerFile . ")");
}
?>