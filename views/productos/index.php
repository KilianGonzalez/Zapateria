<?php 
$titulo = "Productos - Zapatería";
require_once 'views/layout/header.php'; 
?>

<div class="productos-page">
    <aside class="filtros">
        <h2>Filtros</h2>
        
        <div class="filtro-grupo">
            <h3>Tipo de Producto</h3>
            <select id="filtroTipo" name="idTipo">
                <option value="">Todos</option>
            </select>
        </div>
        
        <div class="filtro-grupo">
            <h3>Marca</h3>
            <select id="filtroMarca" name="idMarca">
                <option value="">Todas</option>
            </select>
        </div>
        
        <div class="filtro-grupo">
            <h3>Sexo</h3>
            <select id="filtroSexo" name="sexo">
                <option value="">Todos</option>
                <option value="M">Hombre</option>
                <option value="F">Mujer</option>
                <option value="U">Unisex</option>
            </select>
        </div>
        
        <div class="filtro-grupo">
            <h3>Precio</h3>
            <input type="number" id="precioMin" name="precioMin" placeholder="Mínimo" step="0.01">
            <input type="number" id="precioMax" name="precioMax" placeholder="Máximo" step="0.01">
        </div>
        
        <button id="btnFiltrar" class="btn-primary">Aplicar Filtros</button>
        <button id="btnLimpiar" class="btn-secondary">Limpiar</button>
    </aside>
    
    <section class="productos-lista">
        <h2>Nuestros Productos</h2>
        <div id="productosContainer" class="productos-grid">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="producto-card">
                        <a href="<?php echo BASE_URL; ?>/producto/detalle/<?php echo $producto['id']; ?>">
                            <div class="producto-imagen">
                                <?php $imagen = $producto['imagenPrincipal'] ?? 'default.jpg'; ?>
                                <img src="<?php echo ASSETS_URL; ?>/uploads/productos/<?php echo $imagen; ?>" 
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
            <?php else: ?>
                <p class="no-productos">No se encontraron productos.</p>
            <?php endif; ?>
        </div>
        <div id="loading" class="loading" style="display: none;">
            <p>Cargando productos...</p>
        </div>
    </section>
</div>

<?php require_once 'views/layout/footer.php'; ?>