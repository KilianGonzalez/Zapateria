<?php 
$titulo = "Recuperar Contraseña";
require_once 'views/layout/header.php'; 
?>

<div class="auth-page">
    <div class="auth-form">
        <h1>Recuperar Contraseña</h1>
        <p>Ingresa tu correo electrónico para recuperar tu contraseña.</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje error">
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo BASE_URL; ?>/password/verificar" method="POST">
            <div class="form-grupo">
                <label for="correo">Correo Electrónico *</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            
            <button type="submit" class="btn-primary btn-block">Continuar</button>
        </form>
        
        <p class="auth-link"><a href="<?php echo BASE_URL; ?>/auth/login">Volver al inicio de sesión</a></p>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>
