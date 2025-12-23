<?php 
$titulo = "Resultados de búsqueda";
require_once 'views/layout/header.php'; 
?>

<div class="busqueda-resultados">
    <h1>Resultados de búsqueda</h1>
    
    <?php if (!empty($termino)): ?>
        <p class="termino-busqueda">
            Mostrando resultados para: <strong>"<?php echo htmlspecialchars($termino); ?>"</strong>
            <?php if (!empty($productos)): ?>
                (<?php echo count($productos); ?> producto<?php echo count($productos) > 1 ? 's' : ''; ?> encontrado<?php echo count($productos) > 1 ? 's' : ''; ?>)
            <?php endif; ?>
        </p>
    <?php endif; ?>
    
    <?php if (empty($termino) || strlen($termino) < 2): ?>
        <div class="mensaje error">
            Por favor, ingresa al menos 2 caracteres para buscar.
        </div>
        <a href="<?php echo BASE_URL; ?>/producto/index" class="btn-primary">Ver todos los productos</a>
    <?php elseif (empty($productos)): ?>
        <div class="no-productos">
            <p>No se encontraron productos que coincidan con tu búsqueda.</p>
            <a href="<?php echo BASE_URL; ?>/producto/index" class="btn-primary">Ver todos los productos</a>
        </div>
    <?php else: ?>
        <div class="productos-grid">
            <?php foreach ($productos as $producto): ?>
                <div class="producto-card">
                    <a href="<?php echo BASE_URL; ?>/producto/detalle/<?php echo $producto['id']; ?>">
                        <div class="producto-imagen">
                            <?php $imagenPrincipal = $producto['imagenPrincipal'] ?? 'default.jpg'; ?>
                            <img src="<?php echo ASSETS_URL; ?>/uploads/productos/<?php echo $imagenPrincipal; ?>" 
                                 alt="<?php echo htmlspecialchars($producto['tipo']); ?>"
                                 onerror="this.src='<?php echo ASSETS_URL; ?>/uploads/productos/default.jpg'">
                        </div>
                        <div class="producto-info">
                            <h3><?php echo htmlspecialchars($producto['tipo']); ?></h3>
                            <p class="marca"><?php echo htmlspecialchars($producto['marca']); ?></p>
                            <p class="color">Color: <?php echo htmlspecialchars($producto['color']); ?></p>
                            <p class="precio"><?php echo number_format($producto['precio'], 2); ?> €</p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'views/layout/footer.php'; ?>