<?php 
$titulo = "Carrito de Compras";
require_once 'views/layout/header.php'; 
?>

<div class="carrito-page">
    <h1>Mi Carrito</h1>
    
    <?php if (empty($productos)): ?>
        <div class="carrito-vacio">
            <p>Tu carrito está vacío</p>
            <a href="<?php echo BASE_URL; ?>/producto/index" class="btn-primary">Ver Productos</a>
        </div>
    <?php else: ?>
        <div class="carrito-contenido">
            <table class="carrito-tabla">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr data-producto-id="<?php echo $producto['id']; ?>">
                            <td>
                                <div class="producto-carrito">
                                    <img src="<?php echo ASSETS_URL; ?>/uploads/productos/<?php echo $producto['imagenPrincipal'] ?? 'default.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($producto['tipo']); ?>"
                                         onerror="this.src='<?php echo ASSETS_URL; ?>/uploads/productos/default.jpg'">
                                    <div>
                                        <strong><?php echo htmlspecialchars($producto['tipo']); ?></strong>
                                        <p><?php echo htmlspecialchars($producto['marca']); ?></p>
                                        <p>Color: <?php echo htmlspecialchars($producto['color']); ?> | Talla: <?php echo htmlspecialchars($producto['talla']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="precio"><?php echo number_format($producto['precio'], 2); ?> €</td>
                            <td>
                                <input type="number" 
                                       class="cantidad-input" 
                                       value="<?php echo $producto['cantidad']; ?>" 
                                       min="1" 
                                       max="10"
                                       data-producto-id="<?php echo $producto['id']; ?>">
                            </td>
                            <td class="subtotal"><?php echo number_format($producto['subtotal'], 2); ?> €</td>
                            <td>
                                <button class="btn-eliminar" data-producto-id="<?php echo $producto['id']; ?>">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total:</strong></td>
                        <td colspan="2" class="total"><strong><?php echo number_format($total, 2); ?> €</strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="carrito-acciones">
                <a href="<?php echo BASE_URL; ?>/producto/index" class="btn-secondary">Seguir Comprando</a>
                <a href="<?php echo BASE_URL; ?>/pedido/checkout" class="btn-primary">Finalizar Compra</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.cantidad-input').forEach(input => {
    input.addEventListener('change', function() {
        const idProducto = this.getAttribute('data-producto-id');
        const cantidad = this.value;
        
        ajax('/pedido/actualizarCantidad', 'POST', 
            { idProducto: idProducto, cantidad: cantidad }, 
            function(error, response) {
                if (!error && response.success) {
                    const row = document.querySelector(`tr[data-producto-id="${idProducto}"]`);
                    row.querySelector('.subtotal').textContent = response.subtotal + ' €';
                    document.querySelector('.total strong').textContent = response.total + ' €';
                }
            }
        );
    });
});

document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('¿Estás seguro de eliminar este producto?')) {
            const idProducto = this.getAttribute('data-producto-id');
            
            ajax('/pedido/eliminarDelCarrito', 'POST', 
                { idProducto: idProducto }, 
                function(error, response) {
                    if (!error && response.success) {
                        location.reload();
                    }
                }
            );
        }
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>