<?php 
$titulo = "Mi Perfil";
require_once 'views/layout/header.php'; 
?>

<div class="perfil-page">
    <h1>Mi Perfil</h1>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="mensaje success">
            <?php 
            echo $_SESSION['mensaje']; 
            unset($_SESSION['mensaje']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="perfil-info">
        <h2>Información Personal</h2>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nom'] . ' ' . $usuario['cognom']); ?></p>
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['correo']); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono'] ?? 'No especificado'); ?></p>
    </div>
    
    <div class="pedidos-historial">
        <h2>Mis Pedidos</h2>
        
        <?php if (empty($pedidos)): ?>
            <p>No has realizado ningún pedido todavía.</p>
            <a href="<?php echo BASE_URL; ?>/producto/index" class="btn-primary">Ver Productos</a>
        <?php else: ?>
            <table class="pedidos-tabla">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Producto</th>
                        <th>Dirección</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?php echo $pedido['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($pedido['tipo']); ?> - 
                                <?php echo htmlspecialchars($pedido['marca']); ?>
                                <br>
                                <small>Color: <?php echo htmlspecialchars($pedido['color']); ?> | 
                                Talla: <?php echo htmlspecialchars($pedido['talla']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($pedido['direccion']); ?></td>
                            <td><?php echo number_format($pedido['precioTotal'], 2); ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>