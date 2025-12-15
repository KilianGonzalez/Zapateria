<?php 
$titulo = "Registro";
require_once 'views/layout/header.php'; 
?>

<div class="auth-page">
    <div class="auth-form">
        <h1>Crear Cuenta</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje error">
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="/auth/registroPost" method="POST">
            <div class="form-grupo">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-grupo">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido">
            </div>
            
            <div class="form-grupo">
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono">
            </div>
            
            <div class="form-grupo">
                <label for="correo">Correo Electrónico *</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            
            <div class="form-grupo">
                <label for="contrasena">Contraseña *</label>
                <input type="password" id="contrasena" name="contrasena" required minlength="6">
            </div>
            
            <div class="form-grupo">
                <label for="contrasena_confirm">Confirmar Contraseña *</label>
                <input type="password" id="contrasena_confirm" name="contrasena_confirm" required minlength="6">
            </div>
            
            <button type="submit" class="btn-primary btn-block">Registrarse</button>
        </form>
        
        <p class="auth-link">¿Ya tienes cuenta? <a href="/auth/login">Inicia sesión aquí</a></p>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>
