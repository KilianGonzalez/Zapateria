<?php 
$titulo = "Nueva Contraseña";
require_once 'views/layout/header.php'; 
?>

<div class="auth-page">
    <div class="auth-form">
        <h1>Nueva Contraseña</h1>
        <p>Ingresa tu nueva contraseña.</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje error">
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <form id="formNuevaContrasena" action="<?php echo BASE_URL; ?>/password/actualizar" method="POST">
            <div class="form-grupo">
                <label for="nueva_contrasena">Nueva Contraseña *</label>
                <input type="password" id="nueva_contrasena" name="nueva_contrasena" required minlength="6">
                <small>Mínimo 6 caracteres</small>
                <span class="error-mensaje" id="errorNuevaContrasena"></span>
            </div>
            
            <div class="form-grupo">
                <label for="confirmar_contrasena">Confirmar Contraseña *</label>
                <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required minlength="6">
                <span class="error-mensaje" id="errorConfirmarContrasena"></span>
            </div>
            
            <button type="submit" class="btn-primary btn-block">Actualizar Contraseña</button>
        </form>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>