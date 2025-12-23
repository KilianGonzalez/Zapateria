<?php 
$titulo = "Iniciar Sesión";
require_once 'views/layout/header.php'; 
?>

<div class="auth-page">
    <div class="auth-form">
        <h1>Iniciar Sesión</h1>
        
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
        
        <form id="formLogin" action="<?php echo BASE_URL; ?>/auth/loginPost" method="POST">
            <div class="form-grupo">
                <label for="correo">Correo Electrónico *</label>
                <input type="email" id="correo" name="correo" required>
                <span class="error-mensaje" id="errorCorreo"></span>
            </div>
            
            <div class="form-grupo">
                <label for="contrasena">Contraseña *</label>
                <input type="password" id="contrasena" name="contrasena" required>
                <span class="error-mensaje" id="errorContrasena"></span>
            </div>
            
            <button type="submit" class="btn-primary btn-block">Iniciar Sesión</button>
        </form>
        
        <p class="auth-link">¿Olvidaste tu contraseña? <a href="<?php echo BASE_URL; ?>/password/solicitar">Recupérala aquí</a></p>
        <p class="auth-link">¿No tienes cuenta? <a href="<?php echo BASE_URL; ?>/auth/registro">Regístrate aquí</a></p>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>