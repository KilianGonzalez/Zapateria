<?php 
$titulo = "Gestión de Pedidos";
require_once 'views/layout/header.php'; 
?>

<div class="admin-page">
    <h1>Gestión de Pedidos</h1>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="mensaje success">
            <?php 
            echo $_SESSION['mensaje']; 
            unset($_SESSION['mensaje']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="admin-acciones">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn-secondary">Volver a Productos</a>
    </div>
    
    <h2>Todos los Pedidos</h2>
    
    <?php if (empty($pedidos)): ?>
        <p>No hay pedidos registrados.</p>
    <?php else: ?>
        <table class="admin-tabla pedidos-tabla-admin">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Dirección</th>
                    <th>Cuenta Bancaria</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?php echo $pedido['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($pedido['nom']); ?></strong><br>
                            <small><?php echo htmlspecialchars($pedido['correo']); ?></small>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($pedido['tipo']); ?> - 
                            <?php echo htmlspecialchars($pedido['marca']); ?><br>
                            <small>Color: <?php echo htmlspecialchars($pedido['color']); ?> | 
                            Talla: <?php echo htmlspecialchars($pedido['talla']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($pedido['direccion']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['cuentaBancaria']); ?></td>
                        <td><strong><?php echo number_format($pedido['precioTotal'], 2); ?> €</strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'views/layout/footer.php'; ?>