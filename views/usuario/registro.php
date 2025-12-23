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
        
        <form id="formRegistro" action="<?php echo BASE_URL; ?>/auth/registroPost" method="POST">
            <div class="form-grupo">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required>
                <span class="error-mensaje" id="errorNombre"></span>
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
                <span class="error-mensaje" id="errorCorreo"></span>
                <span class="info-mensaje" id="infoCorreo"></span>
            </div>
            
            <div class="form-grupo">
                <label for="contrasena">Contraseña *</label>
                <input type="password" id="contrasena" name="contrasena" required minlength="6">
                <small>Mínimo 6 caracteres</small>
                <span class="error-mensaje" id="errorContrasena"></span>
            </div>
            
            <div class="form-grupo">
                <label for="contrasena_confirm">Confirmar Contraseña *</label>
                <input type="password" id="contrasena_confirm" name="contrasena_confirm" required minlength="6">
                <span class="error-mensaje" id="errorContrasenaConfirm"></span>
            </div>
            
            <div class="form-grupo">
                <label for="preguntaSeguridad">Pregunta de Seguridad *</label>
                <select id="preguntaSeguridad" name="preguntaSeguridad" required>
                    <option value="">Selecciona una pregunta</option>
                    <option value="¿Cuál es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                    <option value="¿En qué ciudad naciste?">¿En qué ciudad naciste?</option>
                    <option value="¿Cuál es tu comida favorita?">¿Cuál es tu comida favorita?</option>
                    <option value="¿Cuál es el nombre de tu mejor amigo de la infancia?">¿Cuál es el nombre de tu mejor amigo de la infancia?</option>
                    <option value="¿Cuál fue tu primer trabajo?">¿Cuál fue tu primer trabajo?</option>
                </select>
                <span class="error-mensaje" id="errorPregunta"></span>
            </div>
            
            <div class="form-grupo">
                <label for="respuestaSeguridad">Respuesta de Seguridad *</label>
                <input type="text" id="respuestaSeguridad" name="respuestaSeguridad" required>
                <small>Esta respuesta te ayudará a recuperar tu contraseña</small>
                <span class="error-mensaje" id="errorRespuesta"></span>
            </div>
            
            <button type="submit" class="btn-primary btn-block">Registrarse</button>
        </form>
        
        <p class="auth-link">¿Ya tienes cuenta? <a href="<?php echo BASE_URL; ?>/auth/login">Inicia sesión aquí</a></p>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>