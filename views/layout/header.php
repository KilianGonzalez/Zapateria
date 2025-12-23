<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Zapatería Online'; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-top">
                <h1><a href="<?php echo BASE_URL; ?>/producto/index">SHOES & C.O.</a></h1>
                
                <!-- Buscador -->
                <div class="buscador">
                    <input type="text" 
                           id="busqueda" 
                           placeholder="Buscar productos, marcas, colores..." 
                           autocomplete="off">
                    <button id="btnBuscar" class="btn-buscar">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>
                    <div id="resultadosBusqueda" class="resultados-busqueda" style="display: none;"></div>
                </div>
                
                <nav>
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>/producto/index">Productos</a></li>
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <li><a href="<?php echo BASE_URL; ?>/pedido/carrito">Carrito 
                                <?php if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                                    <span class="carrito-badge"><?php echo array_sum($_SESSION['carrito']); ?></span>
                                <?php endif; ?>
                            </a></li>
                            <li><a href="<?php echo BASE_URL; ?>/usuario/perfil">Mi Perfil</a></li>
                            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 't'): ?>
                                <li><a href="<?php echo BASE_URL; ?>/admin/dashboard">Admin</a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo BASE_URL; ?>/auth/logout">Salir</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo BASE_URL; ?>/auth/login">Iniciar Sesión</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/auth/registro">Registro</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <main class="container">