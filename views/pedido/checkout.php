<?php 
$titulo = "Finalizar Compra";
require_once 'views/layout/header.php'; 
?>

<div class="checkout-page">
    <h1>Finalizar Compra</h1>
    
    <form id="formCheckout" action="<?php echo BASE_URL; ?>/pedido/procesar" method="POST">
        <div class="form-grupo">
            <label for="direccion">Dirección de Envío *</label>
            <textarea id="direccion" name="direccion" required rows="4" 
                      placeholder="Calle, número, piso, código postal, ciudad"></textarea>
            <span class="error-mensaje" id="errorDireccion"></span>
        </div>
        
        <div class="form-grupo">
            <label for="cuentaBancaria">Número de Cuenta Bancaria *</label>
            <input type="text" id="cuentaBancaria" name="cuentaBancaria" required
                   placeholder="ES00 0000 0000 0000 0000 0000"
                   pattern="ES\d{22}">
            <small>Formato: ES seguido de 22 dígitos</small>
            <span class="error-mensaje" id="errorCuenta"></span>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje error">
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="checkout-acciones">
            <a href="<?php echo BASE_URL; ?>/pedido/carrito" class="btn-secondary">Volver al Carrito</a>
            <button type="submit" class="btn-primary">Confirmar Pedido</button>
        </div>
    </form>
</div>

<?php require_once 'views/layout/footer.php'; ?>