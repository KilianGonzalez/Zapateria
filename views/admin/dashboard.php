<?php 
$titulo = "Panel de Administración";
require_once 'views/layout/header.php'; 
?>

<div class="admin-page">
    <h1>Panel de Administración</h1>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="mensaje success">
            <?php 
            echo $_SESSION['mensaje']; 
            unset($_SESSION['mensaje']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="mensaje error">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="admin-acciones">
        <a href="<?php echo BASE_URL; ?>/admin/crearProducto" class="btn-primary">Añadir Nuevo Producto</a>
        <a href="<?php echo BASE_URL; ?>/admin/pedidos" class="btn-secondary">Ver Todos los Pedidos</a>
    </div>
    
    <h2>Gestión de Productos</h2>
    
    <?php if (empty($productos)): ?>
        <p>No hay productos en el sistema.</p>
    <?php else: ?>
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Marca</th>
                    <th>Color</th>
                    <th>Talla</th>
                    <th>Precio</th>
                    <th>Sexo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo $producto['id']; ?></td>
                        <td><?php echo htmlspecialchars($producto['tipo']); ?></td>
                        <td><?php echo htmlspecialchars($producto['marca']); ?></td>
                        <td><?php echo htmlspecialchars($producto['color']); ?></td>
                        <td><?php echo htmlspecialchars($producto['talla']); ?></td>
                        <td><?php echo number_format($producto['precio'], 2); ?> €</td>
                        <td><?php echo $producto['sexo'] == 'M' ? 'Hombre' : ($producto['sexo'] == 'F' ? 'Mujer' : 'Unisex'); ?></td>
                        <td class="admin-acciones-tabla">
                            <a href="<?php echo BASE_URL; ?>/admin/editarProducto/<?php echo $producto['id']; ?>" 
                               class="btn-editar">Editar</a>
                            <a href="<?php echo BASE_URL; ?>/admin/eliminarProducto/<?php echo $producto['id']; ?>" 
                               class="btn-eliminar"
                               onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'views/layout/footer.php'; ?>