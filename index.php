<?php
session_start();
require_once 'config.php';

// Obtener la URL solicitada
$request = $_SERVER['REQUEST_URI'];
$request = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $request);
$request = rtrim($request, '/');
$request = strtok($request, '?');

// Si está vacío, redirigir a /producto/index
if (empty($request)) {
    $request = '/producto/index';
}

// Parsear la ruta
$parts = explode('/', trim($request, '/'));
$controller = $parts[0] ?? 'producto';
$action = $parts[1] ?? 'index';
$param = $parts[2] ?? null;

// Mapeo de controladores
$controllerMap = [
    'producto' => 'ProductoController',
    'auth' => 'AuthController',
    'usuario' => 'UsuarioController',
    'pedido' => 'PedidoController',
    'admin' => 'AdminController',
    'password' => 'PasswordController'
];

// Verificar si el controlador existe
if (!isset($controllerMap[$controller])) {
    http_response_code(404);
    echo "Controlador no encontrado: {$controller}";
    exit;
}

$controllerClass = $controllerMap[$controller];
$controllerFile = "controllers/{$controllerClass}.php";

// Cargar el controlador
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerInstance = new $controllerClass();
    
    // Verificar si el método existe
    if (method_exists($controllerInstance, $action)) {
        // Llamar al método con parámetro si existe
        if ($param !== null) {
            $controllerInstance->$action($param);
        } else {
            $controllerInstance->$action();
        }
    } else {
        http_response_code(404);
        echo "Acción no encontrada: {$action}";
        exit;
    }
} else {
    http_response_code(404);
    echo "Archivo del controlador no encontrado: {$controllerFile}";
    exit;
}
?>
